<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\Material;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ElearningController extends Controller
{
    public function index(Request $request): View
    {
        $q = Course::query()
            ->published()
            ->with('category', 'instructor')
            ->withCount('chapters', 'enrollments')
            ->when($request->string('category')->isNotEmpty(),
                fn ($q) => $q->whereHas('category', fn ($qq) => $qq->where('slug', $request->string('category'))))
            ->when($request->string('level')->isNotEmpty(),
                fn ($q) => $q->where('level', $request->string('level')))
            ->when($request->boolean('free'),
                fn ($q) => $q->where('is_free', true))
            ->when($request->string('q')->isNotEmpty(), function ($q) use ($request) {
                $term = '%'.$request->string('q').'%';
                $q->where(function ($q) use ($term) {
                    $q->where('title->id', 'like', $term)
                      ->orWhere('title->en', 'like', $term)
                      ->orWhere('title->ja', 'like', $term);
                });
            });

        return view('pages.elearning.index', [
            'courses'    => $q->orderByDesc('is_featured')->orderByDesc('published_at')->paginate(9)->withQueryString(),
            'categories' => CourseCategory::orderBy('sort_order')->withCount('courses')->get(),
            'levels'     => ['beginner', 'elementary', 'intermediate', 'advanced'],
        ]);
    }

    public function show(string $slug, Request $request): View
    {
        $course = Course::published()
            ->where('slug', $slug)
            ->with([
                'category',
                'instructor',
                'chapters' => fn ($q) => $q->where('is_published', true)->with(['materials' => fn ($qq) => $qq->orderBy('sort_order')]),
                'quiz.questions',
            ])
            ->withCount('enrollments', 'chapters', 'materials')
            ->firstOrFail();

        $enrollment = null;
        $completedIds = [];
        $hasCertificate = null;
        if ($user = $request->user()) {
            $enrollment = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)->first();
            if ($enrollment) {
                $completedIds = LessonProgress::where('user_id', $user->id)
                    ->whereIn('material_id', $course->materials->pluck('id'))
                    ->pluck('material_id')->all();
            }
            $hasCertificate = Certificate::where('user_id', $user->id)
                ->where('course_id', $course->id)->first();
        }

        $related = Course::published()
            ->where('id', '!=', $course->id)
            ->when($course->course_category_id, fn ($q, $cid) => $q->where('course_category_id', $cid))
            ->limit(3)->get();

        return view('pages.elearning.show', compact('course', 'enrollment', 'completedIds', 'hasCertificate', 'related'));
    }

    public function enroll(string $slug, Request $request): RedirectResponse
    {
        $course = Course::published()->where('slug', $slug)->firstOrFail();

        if (! $request->user()) {
            return redirect()->route('filament.student.auth.login')
                ->with('status', __('Please log in first to enroll in this course.'));
        }

        if (! $request->user()->hasRole('student')) {
            return redirect()->route('elearning.show', $slug)
                ->with('status', __('Only student accounts can enroll. You are signed in as an admin.'));
        }

        $enrollment = Enrollment::firstOrCreate(
            ['user_id' => $request->user()->id, 'course_id' => $course->id],
            [
                'status'      => 'enrolled',
                'started_at'  => now(),
                'last_activity_at' => now(),
                'expires_at'  => $course->duration_days ? now()->addDays($course->duration_days) : null,
            ],
        );

        $msg = $enrollment->wasRecentlyCreated
            ? __('You are now enrolled. Happy learning!')
            : __('You are already enrolled in this course.');

        // Redirect to first material (if any)
        $first = $course->chapters()->where('is_published', true)
            ->with(['materials' => fn ($q) => $q->orderBy('sort_order')])
            ->orderBy('sort_order')->get()
            ->flatMap->materials->first();

        if ($first) {
            return redirect()->route('elearning.material', [$slug, $first->id])->with('status', $msg);
        }

        return redirect()->route('elearning.show', $slug)->with('status', $msg);
    }

    public function material(string $slug, int $materialId, Request $request): View|RedirectResponse
    {
        $course = Course::published()->where('slug', $slug)
            ->with(['chapters' => fn ($q) => $q->where('is_published', true)
                ->with(['materials' => fn ($qq) => $qq->orderBy('sort_order')])])
            ->firstOrFail();

        $material = Material::with('chapter')->where('id', $materialId)->firstOrFail();
        if ($material->chapter->course_id !== $course->id) abort(404);

        $user = $request->user();
        $enrollment = $user
            ? Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->first()
            : null;

        // Access guard: must be enrolled OR free preview
        if (! $material->is_free_preview && ! $enrollment) {
            return redirect()->route('elearning.show', $slug)
                ->with('status', __('Please enroll in this course to access this lesson.'));
        }

        // Build flat ordered material list across all chapters for prev/next nav
        $allMaterials = $course->chapters->flatMap->materials->values();
        $currentIndex = $allMaterials->search(fn ($m) => $m->id === $material->id);
        $prev = $currentIndex > 0 ? $allMaterials[$currentIndex - 1] : null;
        $next = $currentIndex < $allMaterials->count() - 1 ? $allMaterials[$currentIndex + 1] : null;

        $completedIds = $user
            ? LessonProgress::where('user_id', $user->id)
                ->whereIn('material_id', $allMaterials->pluck('id'))->pluck('material_id')->all()
            : [];

        $isCompleted = in_array($material->id, $completedIds);

        // Touch last_activity_at
        if ($enrollment) {
            $enrollment->update(['last_activity_at' => now()]);
        }

        return view('pages.elearning.material', compact(
            'course', 'material', 'enrollment', 'allMaterials',
            'completedIds', 'isCompleted', 'prev', 'next'
        ));
    }

    public function complete(string $slug, int $materialId, Request $request): RedirectResponse
    {
        $course = Course::published()->where('slug', $slug)->firstOrFail();
        $material = Material::with('chapter')->findOrFail($materialId);
        if ($material->chapter->course_id !== $course->id) abort(404);

        if (! $user = $request->user()) {
            return redirect()->route('filament.student.auth.login');
        }

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)->firstOrFail();

        DB::transaction(function () use ($user, $material, $enrollment) {
            LessonProgress::firstOrCreate(
                ['user_id' => $user->id, 'material_id' => $material->id],
                ['completed_at' => now()],
            );
            $enrollment->recomputeProgress();
        });

        // Auto-issue certificate if course is now complete AND no quiz required
        $this->maybeIssueCertificate($enrollment->fresh(), $course->load('quiz'));

        // Find next material; if none, redirect to course show or quiz
        $allMaterials = $course->chapters()->where('is_published', true)
            ->with(['materials' => fn ($q) => $q->orderBy('sort_order')])
            ->orderBy('sort_order')->get()
            ->flatMap->materials->values();

        $currentIndex = $allMaterials->search(fn ($m) => $m->id === $material->id);
        $next = $currentIndex < $allMaterials->count() - 1 ? $allMaterials[$currentIndex + 1] : null;

        if ($next) {
            return redirect()->route('elearning.material', [$slug, $next->id])
                ->with('status', __('Lesson marked complete!'));
        }

        // Last lesson done
        if ($course->hasQuiz()) {
            return redirect()->route('elearning.quiz', $slug)
                ->with('status', __('All lessons done! Take the final quiz now.'));
        }

        return redirect()->route('elearning.show', $slug)
            ->with('status', __('Congratulations! You completed this course.'));
    }

    public function quiz(string $slug, Request $request): View|RedirectResponse
    {
        $course = Course::published()->where('slug', $slug)
            ->with(['quiz.questions'])
            ->firstOrFail();

        if (! $course->quiz) abort(404);
        if (! $user = $request->user()) {
            return redirect()->route('filament.student.auth.login');
        }

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)->firstOrFail();

        $attempts = QuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $course->quiz->id)
            ->orderByDesc('finished_at')->get();

        $bestAttempt = $attempts->where('passed', true)->first() ?? $attempts->sortByDesc('score')->first();

        return view('pages.elearning.quiz', compact('course', 'enrollment', 'attempts', 'bestAttempt'));
    }

    public function quizSubmit(string $slug, Request $request): RedirectResponse
    {
        $course = Course::published()->where('slug', $slug)
            ->with(['quiz.questions'])->firstOrFail();
        if (! $course->quiz) abort(404);

        $user = $request->user() ?? abort(401);
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)->firstOrFail();

        $quiz = $course->quiz;
        $attemptCount = QuizAttempt::where('user_id', $user->id)->where('quiz_id', $quiz->id)->count();
        if ($quiz->max_attempts > 0 && $attemptCount >= $quiz->max_attempts) {
            return redirect()->route('elearning.quiz', $slug)
                ->with('status', __('You have reached the maximum number of attempts.'));
        }

        $answers = $request->input('answers', []);
        $totalPoints = 0;
        $earnedPoints = 0;
        foreach ($quiz->questions as $q) {
            $totalPoints += $q->points;
            if (($answers[$q->id] ?? null) === $q->correct_answer) {
                $earnedPoints += $q->points;
            }
        }
        $score = $totalPoints > 0 ? (int) round(($earnedPoints / $totalPoints) * 100) : 0;
        $passed = $score >= $quiz->passing_score;

        QuizAttempt::create([
            'user_id'     => $user->id,
            'quiz_id'     => $quiz->id,
            'score'       => $score,
            'passed'      => $passed,
            'answers'     => $answers,
            'started_at'  => now(),
            'finished_at' => now(),
        ]);

        if ($passed) {
            $this->maybeIssueCertificate($enrollment->fresh(), $course, $score);
            return redirect()->route('elearning.quiz', $slug)
                ->with('status', __('You passed with :score%! 🎉', ['score' => $score]));
        }

        return redirect()->route('elearning.quiz', $slug)
            ->with('status', __('You scored :score%. Passing score is :pass%. Try again!', [
                'score' => $score, 'pass' => $quiz->passing_score,
            ]));
    }

    public function certificate(string $number): View
    {
        $certificate = Certificate::where('certificate_number', $number)
            ->with('user', 'course')->firstOrFail();

        return view('pages.elearning.certificate', compact('certificate'));
    }

    public function certificateDownload(string $number): Response
    {
        $certificate = Certificate::where('certificate_number', $number)
            ->with('user', 'course')->firstOrFail();

        $pdf = Pdf::loadView('pages.elearning.certificate-pdf', compact('certificate'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('certificate-'.$certificate->certificate_number.'.pdf');
    }

    /**
     * Issue certificate when:
     *  - All materials are completed (progress 100%)
     *  - AND quiz passed (if quiz exists)
     */
    protected function maybeIssueCertificate(Enrollment $enrollment, Course $course, ?int $finalScore = null): ?Certificate
    {
        $course->loadMissing('quiz');

        if ($enrollment->progress_pct < 100) return null;

        if ($course->quiz) {
            $passed = QuizAttempt::where('user_id', $enrollment->user_id)
                ->where('quiz_id', $course->quiz->id)
                ->where('passed', true)
                ->exists();
            if (! $passed) return null;
        }

        return Certificate::firstOrCreate(
            ['user_id' => $enrollment->user_id, 'course_id' => $course->id],
            ['final_score' => $finalScore],
        );
    }
}
