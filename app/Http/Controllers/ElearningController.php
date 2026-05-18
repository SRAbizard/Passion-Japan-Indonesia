<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Chapter;
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
                'chapters' => fn ($q) => $q->where('is_published', true)
                    ->with([
                        'materials' => fn ($qq) => $qq->orderBy('sort_order'),
                        'quizzes'   => fn ($qq) => $qq->where('is_published', true)
                            ->withCount('questions')
                            ->orderBy('sort_order'),
                    ]),
                'finalExam.questions',
            ])
            ->withCount('enrollments', 'chapters', 'materials')
            ->firstOrFail();

        $enrollment = null;
        $hasCertificate = null;
        if ($user = $request->user()) {
            $enrollment = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)->first();
            $hasCertificate = Certificate::where('user_id', $user->id)
                ->where('course_id', $course->id)->first();
        }

        $firstItem = $this->firstItemFor($course);

        $related = Course::published()
            ->where('id', '!=', $course->id)
            ->when($course->course_category_id, fn ($q, $cid) => $q->where('course_category_id', $cid))
            ->limit(3)->get();

        return view('pages.elearning.show', compact(
            'course', 'enrollment', 'hasCertificate', 'related', 'firstItem'
        ));
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

        $first = $this->firstItemFor($course->load([
            'chapters' => fn ($q) => $q->where('is_published', true)->with([
                'materials' => fn ($qq) => $qq->orderBy('sort_order'),
                'quizzes'   => fn ($qq) => $qq->where('is_published', true)->orderBy('sort_order'),
            ]),
        ]));

        if ($first) {
            return redirect($this->urlForItem($course, $first))->with('status', $msg);
        }

        return redirect()->route('elearning.show', $slug)->with('status', $msg);
    }

    public function material(string $slug, int $materialId, Request $request): View|RedirectResponse
    {
        $course = $this->loadCourseWithCurriculum($slug);

        $material = Material::with('chapter')->where('id', $materialId)->firstOrFail();
        if ($material->chapter->course_id !== $course->id) abort(404);

        $user = $request->user();
        $enrollment = $user
            ? Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->first()
            : null;

        // Access guards: enrolled OR free preview, plus sequential lock check
        if (! $material->is_free_preview && ! $enrollment) {
            return redirect()->route('elearning.show', $slug)
                ->with('status', __('Please enroll in this course to access this lesson.'));
        }

        if ($this->isItemLockedForUser($material->chapter, 'material', $material->id, $user)) {
            return redirect()->route('elearning.show', $slug)
                ->with('status', __('Finish the previous lesson before opening this one.'));
        }

        $nav = $this->itemNavigation($course, 'material', $material->id, $user);

        if ($enrollment) {
            $enrollment->update(['last_activity_at' => now()]);
        }

        $isCompleted = $user
            ? LessonProgress::where('user_id', $user->id)
                ->where('material_id', $material->id)
                ->whereNotNull('completed_at')->exists()
            : false;

        return view('pages.elearning.material', [
            'course'      => $course,
            'material'    => $material,
            'enrollment'  => $enrollment,
            'isCompleted' => $isCompleted,
            'prev'        => $nav['prev'],
            'next'        => $nav['next'],
            'currentKind' => 'material',
            'currentId'   => $material->id,
        ]);
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

        $this->maybeIssueCertificate($enrollment->fresh(), $course);

        $course = $this->loadCourseWithCurriculum($slug);
        $next = $this->itemNavigation($course, 'material', $material->id, $user)['next'];

        if ($next) {
            return redirect($this->urlForItem($course, $next))
                ->with('status', __('Lesson marked complete!'));
        }

        return redirect()->route('elearning.show', $slug)
            ->with('status', __('Course complete!'));
    }

    /**
     * Quiz intro page (Dicoding-style): shows total questions, passing
     * score, time limit, then a big "Mulai Quiz" button.
     */
    public function quizIntro(string $slug, int $quizId, Request $request): View|RedirectResponse
    {
        $course = $this->loadCourseWithCurriculum($slug);
        $quiz = Quiz::with('questions')->where('id', $quizId)
            ->where('course_id', $course->id)
            ->where('is_published', true)
            ->firstOrFail();

        $user = $request->user();
        if (! $user) {
            return redirect()->route('filament.student.auth.login');
        }

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)->firstOrFail();

        // Sequential-lock check for chapter quizzes
        if ($quiz->isChapterQuiz() && $this->isItemLockedForUser($quiz->chapter, 'quiz', $quiz->id, $user)) {
            return redirect()->route('elearning.show', $slug)
                ->with('status', __('Finish the previous lesson before opening this quiz.'));
        }

        $attempts = QuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->orderByDesc('finished_at')->get();
        $bestAttempt = $attempts->where('passed', true)->first() ?? $attempts->sortByDesc('score')->first();
        $reachedMax = $quiz->max_attempts > 0 && $attempts->count() >= $quiz->max_attempts;

        $certificate = $quiz->isFinalExam()
            ? Certificate::where('user_id', $user->id)->where('course_id', $course->id)->first()
            : null;

        $nav = $this->itemNavigation($course, 'quiz', $quiz->id, $user);

        return view('pages.elearning.quiz-intro', [
            'course'      => $course,
            'quiz'        => $quiz,
            'enrollment'  => $enrollment,
            'attempts'    => $attempts,
            'bestAttempt' => $bestAttempt,
            'reachedMax'  => $reachedMax,
            'certificate' => $certificate,
            'prev'        => $nav['prev'],
            'next'        => $nav['next'],
            'currentKind' => 'quiz',
            'currentId'   => $quiz->id,
        ]);
    }

    /**
     * Actual quiz form (after clicking "Mulai Quiz" on the intro).
     */
    public function quizTake(string $slug, int $quizId, Request $request): View|RedirectResponse
    {
        $course = $this->loadCourseWithCurriculum($slug);
        $quiz = Quiz::with('questions')->where('id', $quizId)
            ->where('course_id', $course->id)
            ->where('is_published', true)
            ->firstOrFail();

        $user = $request->user();
        if (! $user) return redirect()->route('filament.student.auth.login');

        Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->firstOrFail();

        if ($quiz->isChapterQuiz() && $this->isItemLockedForUser($quiz->chapter, 'quiz', $quiz->id, $user)) {
            return redirect()->route('elearning.show', $slug);
        }

        $attemptCount = QuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)->count();
        if ($quiz->max_attempts > 0 && $attemptCount >= $quiz->max_attempts) {
            return redirect()->route('elearning.quiz', [$slug, $quiz->id])
                ->with('status', __('You have reached the maximum number of attempts.'));
        }

        $nav = $this->itemNavigation($course, 'quiz', $quiz->id, $user);

        return view('pages.elearning.quiz-take', [
            'course'      => $course,
            'quiz'        => $quiz,
            'prev'        => $nav['prev'],
            'next'        => $nav['next'],
            'currentKind' => 'quiz',
            'currentId'   => $quiz->id,
        ]);
    }

    public function quizSubmit(string $slug, int $quizId, Request $request): RedirectResponse
    {
        $course = Course::published()->where('slug', $slug)->firstOrFail();
        $quiz = Quiz::with('questions')->where('id', $quizId)
            ->where('course_id', $course->id)
            ->where('is_published', true)
            ->firstOrFail();

        $user = $request->user() ?? abort(401);
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)->firstOrFail();

        $attemptCount = QuizAttempt::where('user_id', $user->id)->where('quiz_id', $quiz->id)->count();
        if ($quiz->max_attempts > 0 && $attemptCount >= $quiz->max_attempts) {
            return redirect()->route('elearning.quiz', [$slug, $quiz->id])
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

        DB::transaction(function () use ($user, $quiz, $score, $passed, $answers, $enrollment) {
            QuizAttempt::create([
                'user_id'     => $user->id,
                'quiz_id'     => $quiz->id,
                'score'       => $score,
                'passed'      => $passed,
                'answers'     => $answers,
                'started_at'  => now(),
                'finished_at' => now(),
            ]);
            // Chapter quizzes count toward course progress
            if ($quiz->isChapterQuiz() && $passed) {
                $enrollment->recomputeProgress();
            }
        });

        if ($passed) {
            $this->maybeIssueCertificate($enrollment->fresh(), $course, $quiz->isFinalExam() ? $score : null);
            return redirect()->route('elearning.quiz', [$slug, $quiz->id])
                ->with('status', __('You passed with :score%! 🎉', ['score' => $score]));
        }

        return redirect()->route('elearning.quiz', [$slug, $quiz->id])
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

    // =================================================================
    // Helpers
    // =================================================================

    /**
     * Eager-load a course with full curriculum (chapters → materials + quizzes).
     * Used by every learn/quiz route so the sidebar has data without N+1.
     */
    protected function loadCourseWithCurriculum(string $slug): Course
    {
        return Course::published()
            ->where('slug', $slug)
            ->with([
                'chapters' => fn ($q) => $q->where('is_published', true)
                    ->with([
                        'materials' => fn ($qq) => $qq->orderBy('sort_order'),
                        'quizzes'   => fn ($qq) => $qq->where('is_published', true)
                            ->withCount('questions')
                            ->orderBy('sort_order'),
                    ]),
                'finalExam' => fn ($q) => $q->withCount('questions'),
            ])
            ->firstOrFail();
    }

    /**
     * First curriculum item across the whole course (used by "Start learning"
     * button on the course detail page and by /enroll redirect).
     */
    protected function firstItemFor(Course $course): ?object
    {
        foreach ($course->chapters as $chapter) {
            $items = $chapter->items();
            if ($items->isNotEmpty()) return $items->first();
        }
        return null;
    }

    /**
     * Build the URL for any curriculum item (material or quiz).
     */
    protected function urlForItem(Course $course, object $item): string
    {
        return $item->kind === 'quiz'
            ? route('elearning.quiz',     [$course->slug, $item->id])
            : route('elearning.material', [$course->slug, $item->id]);
    }

    /**
     * Walk every item across all chapters in a course (flat ordered list)
     * to find prev/next neighbours for the given current item.
     */
    protected function itemNavigation(Course $course, string $kind, int $id, $user): array
    {
        $flat = collect();
        foreach ($course->chapters as $chapter) {
            $flat = $flat->concat($chapter->items());
        }
        if ($course->finalExam) {
            $flat->push((object) [
                'kind'  => 'quiz',
                'id'    => $course->finalExam->id,
                'model' => $course->finalExam,
                'title' => $course->finalExam->t('title'),
                'badge' => __('Final exam'),
                'code'  => $course->finalExam->code,
                'sort_order' => 99999,
            ]);
        }
        $flat = $flat->values();

        $idx = $flat->search(fn ($it) => $it->kind === $kind && $it->id === $id);
        if ($idx === false) return ['prev' => null, 'next' => null];

        return [
            'prev' => $idx > 0 ? $flat[$idx - 1] : null,
            'next' => $idx < $flat->count() - 1 ? $flat[$idx + 1] : null,
        ];
    }

    protected function isItemLockedForUser(Chapter $chapter, string $kind, int $id, $user): bool
    {
        if (! $chapter->isSequential()) return false;
        $items = $chapter->itemsFor($user);
        $target = $items->first(fn ($it) => $it->kind === $kind && $it->id === $id);
        return $target ? (bool) $target->locked : false;
    }

    /**
     * Issue certificate when:
     *  - Progress 100% (all materials done + all chapter quizzes passed)
     *  - AND final exam passed (if course has one)
     */
    protected function maybeIssueCertificate(Enrollment $enrollment, Course $course, ?int $finalScore = null): ?Certificate
    {
        $course->loadMissing('finalExam');

        if ($enrollment->progress_pct < 100) return null;

        if ($course->finalExam) {
            $passed = QuizAttempt::where('user_id', $enrollment->user_id)
                ->where('quiz_id', $course->finalExam->id)
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
