<x-filament-panels::page>
    @php
        $visas = $this->getVisas();
        $types = $this->getDocumentTypes();
    @endphp

    {{-- Help / instructions card --}}
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-5 mb-6">
        <div class="flex items-start gap-3">
            <div class="shrink-0 inline-flex h-9 w-9 items-center justify-center rounded-lg bg-primary-50 text-primary-600 dark:bg-primary-500/10 dark:text-primary-400">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-sm text-gray-900 dark:text-gray-100 font-medium">
                    {{ __('Tick a checkbox to mark a document as required for that visa.') }}
                </p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('Use the Select all / Clear shortcuts at the top of each column. Manage the document types themselves under Students → Document Types.') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Matrix table --}}
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden">
        @if ($types->isEmpty())
            <div class="p-12 text-center">
                <div class="mx-auto h-12 w-12 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-3">
                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <p class="text-sm text-gray-500">{{ __('No document types yet. Add some under Students → Document Types.') }}</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full border-collapse" style="border-spacing: 0;">
                    <thead>
                        <tr style="background: rgb(249 250 251);" class="dark:!bg-gray-800/60 border-b border-gray-200 dark:border-white/10">
                            <th style="padding: 16px 20px; min-width: 240px; text-align: left;"
                                class="font-semibold text-gray-700 dark:text-gray-200 sticky left-0 bg-gray-50 dark:!bg-gray-800 z-10">
                                <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Document type') }}</p>
                            </th>
                            @foreach ($visas as $visa)
                                @php $count = count($matrix[$visa->slug] ?? []); @endphp
                                <th style="padding: 16px 20px; min-width: 200px;" class="text-center border-l border-gray-200 dark:border-white/10">
                                    <p class="font-bold text-gray-950 dark:text-white text-base">{{ $visa->t('name') }}</p>
                                    <div class="mt-2 inline-flex items-center gap-1 text-[10px] uppercase tracking-wider rounded-md bg-gray-100 dark:bg-gray-800 px-2 py-1">
                                        <button type="button" wire:click="selectAllForVisa('{{ $visa->slug }}')"
                                                class="font-semibold text-primary-600 dark:text-primary-400 hover:text-primary-700">
                                            {{ __('All') }}
                                        </button>
                                        <span class="text-gray-300 dark:text-gray-600 mx-0.5">·</span>
                                        <button type="button" wire:click="clearVisa('{{ $visa->slug }}')"
                                                class="font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-700">
                                            {{ __('Clear') }}
                                        </button>
                                    </div>
                                    <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">
                                        <span class="font-bold text-gray-950 dark:text-white">{{ $count }}</span>
                                        / {{ $types->count() }} {{ __('selected') }}
                                    </p>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($types as $rowIdx => $type)
                            <tr class="{{ $rowIdx % 2 === 0 ? 'bg-white dark:bg-gray-900' : 'bg-gray-50/40 dark:bg-white/[0.02]' }} hover:bg-primary-50/30 dark:hover:bg-primary-500/5 transition border-b border-gray-100 dark:border-white/5 last:border-0">
                                <td style="padding: 14px 20px;" class="sticky left-0 z-10 {{ $rowIdx % 2 === 0 ? 'bg-white dark:bg-gray-900' : 'bg-gray-50/40 dark:bg-gray-900/95' }}">
                                    <div class="flex items-center gap-3">
                                        @if($type->icon)
                                            <x-filament::icon :icon="$type->icon" class="h-5 w-5 text-gray-400 dark:text-gray-500 shrink-0" />
                                        @endif
                                        <div class="min-w-0">
                                            <p class="font-semibold text-gray-950 dark:text-white">{{ $type->t('label') }}</p>
                                            <p class="text-[11px] text-gray-400 dark:text-gray-500" style="font-family: ui-monospace, SFMono-Regular, monospace;">{{ $type->key }}</p>
                                        </div>
                                    </div>
                                </td>
                                @foreach ($visas as $visa)
                                    @php $checked = in_array($type->key, $matrix[$visa->slug] ?? [], true); @endphp
                                    <td style="padding: 14px 20px;" class="text-center border-l border-gray-200/60 dark:border-white/5">
                                        <label class="inline-flex items-center justify-center cursor-pointer p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/5 transition">
                                            <input type="checkbox"
                                                   wire:click="toggle('{{ $visa->slug }}', '{{ $type->key }}')"
                                                   {{ $checked ? 'checked' : '' }}
                                                   style="height: 20px; width: 20px; cursor: pointer; accent-color: #b32510;"
                                                   class="rounded border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-primary-500 focus:ring-offset-0">
                                        </label>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Action buttons (with proper spacing) --}}
    <div class="mt-6 flex items-center justify-between gap-3 flex-wrap">
        <p class="text-xs text-gray-500 dark:text-gray-400">
            {{ __('Changes affect new uploads and the student progress widget.') }}
        </p>
        <div class="flex items-center gap-3">
            <x-filament::button color="gray" wire:click="loadMatrix" type="button" icon="heroicon-o-arrow-path">
                {{ __('Reset changes') }}
            </x-filament::button>
            <x-filament::button wire:click="save" type="button" icon="heroicon-o-check">
                {{ __('Save changes') }}
            </x-filament::button>
        </div>
    </div>
</x-filament-panels::page>
