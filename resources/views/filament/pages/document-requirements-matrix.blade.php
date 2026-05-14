<x-filament-panels::page>
    @php
        $visas = $this->getVisas();
        $types = $this->getDocumentTypes();
    @endphp

    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-5 mb-4">
        <p class="text-sm text-gray-600 dark:text-gray-300">
            {{ __('Tick a checkbox to mark a document as required for that visa. Use the Select all / Clear shortcuts at the top of each column. Click Save when done.') }}
        </p>
        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
            {{ __('Manage the document types themselves under Students → Document Types.') }}
        </p>
    </div>

    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-white/10">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold text-gray-700 dark:text-gray-200 sticky left-0 bg-gray-50 dark:bg-gray-800 z-10 min-w-[220px]">
                            {{ __('Document type') }}
                        </th>
                        @foreach ($visas as $visa)
                            <th class="px-4 py-3 text-center min-w-[160px]">
                                <p class="font-bold text-gray-950 dark:text-white">{{ $visa->t('name') }}</p>
                                <div class="mt-1.5 flex items-center justify-center gap-1.5 text-[10px] uppercase tracking-wider">
                                    <button type="button" wire:click="selectAllForVisa('{{ $visa->slug }}')"
                                            class="px-1.5 py-0.5 rounded text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-500/10">
                                        {{ __('All') }}
                                    </button>
                                    <span class="text-gray-300 dark:text-gray-700">·</span>
                                    <button type="button" wire:click="clearVisa('{{ $visa->slug }}')"
                                            class="px-1.5 py-0.5 rounded text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5">
                                        {{ __('Clear') }}
                                    </button>
                                </div>
                                <p class="mt-1 text-[10px] text-gray-400 dark:text-gray-500">
                                    {{ count($matrix[$visa->slug] ?? []) }} {{ __('selected') }}
                                </p>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($types as $type)
                        <tr class="border-b border-gray-100 dark:border-white/5 last:border-0 hover:bg-gray-50 dark:hover:bg-white/5">
                            <td class="px-4 py-3 sticky left-0 bg-white dark:bg-gray-900 group-hover:bg-gray-50 dark:group-hover:bg-white/5 z-10">
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $type->t('label') }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-mono">{{ $type->key }}</p>
                            </td>
                            @foreach ($visas as $visa)
                                @php $checked = in_array($type->key, $matrix[$visa->slug] ?? [], true); @endphp
                                <td class="px-4 py-3 text-center">
                                    <button type="button"
                                            wire:click="toggle('{{ $visa->slug }}', '{{ $type->key }}')"
                                            class="inline-flex items-center justify-center h-6 w-6 rounded-md border-2 transition
                                                {{ $checked
                                                    ? 'bg-primary-600 border-primary-600 text-white hover:bg-primary-500'
                                                    : 'border-gray-300 dark:border-gray-600 hover:border-primary-400' }}">
                                        @if ($checked)
                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        @endif
                                    </button>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($types->isEmpty())
            <div class="p-10 text-center text-sm text-gray-500">
                {{ __('No document types yet. Add some under Students → Document Types.') }}
            </div>
        @endif
    </div>

    <div class="mt-5 flex items-center justify-end gap-2">
        <x-filament::button color="gray" wire:click="loadMatrix" type="button">
            {{ __('Reset changes') }}
        </x-filament::button>
        <x-filament::button wire:click="save" type="button">
            {{ __('Save changes') }}
        </x-filament::button>
    </div>
</x-filament-panels::page>
