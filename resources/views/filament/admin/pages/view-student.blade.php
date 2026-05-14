<x-filament-panels::page>
    @php
        $progress       = $this->getProgress();
        $profilePct     = $this->getProfileCompletion();
        $student        = $this->record;
    @endphp

    {{-- Top: stats --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-5">
            <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Documents') }}</p>
            <p class="mt-1 text-2xl font-bold text-gray-950 dark:text-white">
                {{ $progress['verified_count'] }} <span class="text-base text-gray-500 font-normal">/ {{ $progress['required_count'] }}</span>
            </p>
            <div class="mt-3 h-2 w-full rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden">
                <div class="h-full bg-gradient-to-r from-primary-500 to-primary-700" style="width: {{ $progress['pct'] }}%"></div>
            </div>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                {{ $progress['pct'] }}% {{ __('verified') }}{{ $progress['using_default'] ? ' · '.__('default set') : '' }}
            </p>
        </div>

        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-5">
            <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Profile completion') }}</p>
            <p class="mt-1 text-2xl font-bold text-gray-950 dark:text-white">{{ $profilePct }}%</p>
            <div class="mt-3 h-2 w-full rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden">
                <div class="h-full bg-emerald-500" style="width: {{ $profilePct }}%"></div>
            </div>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Biodata fields filled') }}</p>
        </div>

        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-5">
            <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Applications') }}</p>
            <p class="mt-1 text-2xl font-bold text-gray-950 dark:text-white">{{ $student->applications->count() }}</p>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Total submitted') }}</p>
        </div>

        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-5">
            <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Courses') }}</p>
            <p class="mt-1 text-2xl font-bold text-gray-950 dark:text-white">{{ $student->enrollments->count() }}</p>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                {{ $student->certificates->count() }} {{ __('certificates earned') }}
            </p>
        </div>
    </div>

    {{-- Required documents checklist --}}
    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-950 dark:text-white">{{ __('Required documents checklist') }}</h3>
            @if($progress['using_default'])
                <span class="text-xs px-2 py-1 rounded-md bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400">
                    {{ __('Student has not applied yet — using default set') }}
                </span>
            @endif
        </div>

        @if($progress['required_count'] === 0)
            <p class="text-sm text-gray-500">{{ __('No required documents configured for this student\'s visa target.') }}</p>
        @else
            <ul class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($progress['required'] as $type)
                    @php
                        $verified = in_array($type, $progress['verified_types']);
                        $uploaded = in_array($type, $progress['uploaded_types']);
                    @endphp
                    <li class="flex items-center gap-2 text-sm">
                        @if($verified)
                            <svg class="h-4 w-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span class="text-gray-900 dark:text-gray-100">{{ __('document.type.'.$type) }}</span>
                            <span class="text-xs text-emerald-600 dark:text-emerald-400">— {{ __('verified') }}</span>
                        @elseif($uploaded)
                            <svg class="h-4 w-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-gray-900 dark:text-gray-100">{{ __('document.type.'.$type) }}</span>
                            <span class="text-xs text-amber-600 dark:text-amber-400">— {{ __('pending review') }}</span>
                        @else
                            <svg class="h-4 w-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            <span class="text-gray-500 dark:text-gray-400">{{ __('document.type.'.$type) }}</span>
                            <span class="text-xs text-gray-400">— {{ __('missing') }}</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    {{-- All uploaded documents --}}
    @if($student->studentDocuments->isNotEmpty())
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6">
            <h3 class="font-semibold text-gray-950 dark:text-white mb-4">{{ __('All uploaded documents') }} ({{ $student->studentDocuments->count() }})</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        <tr class="border-b border-gray-200 dark:border-white/10">
                            <th class="text-left py-2 pr-4">{{ __('Type') }}</th>
                            <th class="text-left py-2 pr-4">{{ __('Status') }}</th>
                            <th class="text-left py-2 pr-4">{{ __('Uploaded') }}</th>
                            <th class="text-left py-2 pr-4">{{ __('Verified by') }}</th>
                            <th class="text-left py-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($student->studentDocuments as $doc)
                            <tr class="border-b border-gray-100 dark:border-white/5 last:border-0">
                                <td class="py-2 pr-4 text-gray-900 dark:text-gray-100">{{ __('document.type.'.$doc->type) }}</td>
                                <td class="py-2 pr-4">
                                    @php $colors = ['pending'=>'amber','verified'=>'emerald','rejected'=>'rose']; $c = $colors[$doc->status] ?? 'gray'; @endphp
                                    <span class="text-xs uppercase tracking-wider font-bold px-2 py-1 rounded-md bg-{{ $c }}-50 text-{{ $c }}-700 dark:bg-{{ $c }}-500/10 dark:text-{{ $c }}-400">
                                        {{ __('document.status.'.$doc->status) }}
                                    </span>
                                </td>
                                <td class="py-2 pr-4 text-gray-500 dark:text-gray-400">{{ $doc->created_at->format('d M Y') }}</td>
                                <td class="py-2 pr-4 text-gray-500 dark:text-gray-400">{{ $doc->verifier?->name ?? '—' }}</td>
                                <td class="py-2">
                                    <a href="{{ $doc->file_url }}" target="_blank" class="text-primary-600 hover:underline text-xs">{{ __('View') }} →</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Applications --}}
    @if($student->applications->isNotEmpty())
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6">
            <h3 class="font-semibold text-gray-950 dark:text-white mb-4">{{ __('Applications') }} ({{ $student->applications->count() }})</h3>
            <ul class="divide-y divide-gray-100 dark:divide-white/5">
                @foreach($student->applications as $app)
                    <li class="py-3 flex items-center justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-950 dark:text-white truncate">{{ $app->vacancy?->t('title') ?? '—' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $app->vacancy?->company?->name }}
                                @if($app->vacancy?->visaCategory) · <span class="text-primary-600 dark:text-primary-400">{{ $app->vacancy->visaCategory->t('name') }}</span> @endif
                            </p>
                        </div>
                        <div class="text-xs uppercase tracking-wider font-bold px-2 py-1 rounded-md bg-gray-100 dark:bg-gray-800">
                            {{ __('application.status.'.$app->status) }}
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Enrollments --}}
    @if($student->enrollments->isNotEmpty())
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6">
            <h3 class="font-semibold text-gray-950 dark:text-white mb-4">{{ __('Enrollments') }} ({{ $student->enrollments->count() }})</h3>
            <ul class="divide-y divide-gray-100 dark:divide-white/5">
                @foreach($student->enrollments as $enr)
                    <li class="py-3 flex items-center justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-950 dark:text-white truncate">{{ $enr->course?->t('title') ?? '—' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ __('enrollment.status.'.$enr->status) }} · {{ $enr->progress_pct }}%
                            </p>
                        </div>
                        <div class="w-32 h-2 rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden">
                            <div class="h-full bg-primary-500" style="width: {{ $enr->progress_pct }}%"></div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</x-filament-panels::page>
