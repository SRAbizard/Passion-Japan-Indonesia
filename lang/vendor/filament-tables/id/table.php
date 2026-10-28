<?php

// Strings Filament ships in English only; merged over its own id translations.
return [
    'column_manager' => [
        'position' => 'Posisi :position dari :count.',
        'position_in_group' => 'Posisi :position dari :count di :group.',
        'reordered' => ':column dipindah ke posisi :position dari :count.',
        'reordered_deferred' => ':column dipindah ke posisi :position dari :count. Terapkan kolom untuk menyimpan.',
        'reordered_in_group' => ':column dipindah ke posisi :position dari :count di :group.',
        'reordered_in_group_deferred' => ':column dipindah ke posisi :position dari :count di :group. Terapkan kolom untuk menyimpan.',
        'actions' => [
            'move_up' => [
                'label' => 'Pindahkan :column ke atas',
            ],
            'move_down' => [
                'label' => 'Pindahkan :column ke bawah',
            ],
            'reorder' => [
                'label' => 'Urutkan kolom',
            ],
        ],
    ],
    'columns' => [
        'editable' => [
            'saving' => 'Menyimpan perubahan...',
            'saved' => 'Perubahan tersimpan.',
            'failure' => 'Penyimpanan belum terkonfirmasi. Muat ulang halaman sebelum mencoba lagi.',
            'unavailable' => 'Item ini tidak bisa diubah lagi.',
        ],
        'icon' => [
            'boolean' => [
                'true' => 'Ya',
                'false' => 'Tidak',
            ],
        ],
    ],
    'actions' => [
        'reorder_record' => [
            'label' => 'Urutkan item :key',
        ],
        'move_record_up' => [
            'label' => 'Pindahkan :label ke atas',
        ],
        'move_record_down' => [
            'label' => 'Pindahkan :label ke bawah',
        ],
        'toggle_record_content' => [
            'label' => 'Bentangkan/ciutkan item :key',
        ],
    ],
    'loading' => 'Memuat...',
    'reordering' => [
        'position' => ':label, posisi :position dari :count.',
        'moved' => ':label dipindah ke posisi :position dari :count.',
    ],
    'result_count' => '{0} Tidak ada hasil|[1,*] :count hasil',
];
