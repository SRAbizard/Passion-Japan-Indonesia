<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['ktp',                ['id'=>'KTP',                 'en'=>'ID Card (KTP)',         'ja'=>'身分証（KTP）'],         'heroicon-o-identification'],
            ['kk',                 ['id'=>'Kartu Keluarga',      'en'=>'Family Card',           'ja'=>'家族カード'],            'heroicon-o-users'],
            ['passport',           ['id'=>'Paspor',              'en'=>'Passport',              'ja'=>'パスポート'],            'heroicon-o-globe-alt'],
            ['ijazah',             ['id'=>'Ijazah',              'en'=>'Diploma',               'ja'=>'卒業証書'],              'heroicon-o-academic-cap'],
            ['transcript',         ['id'=>'Transkrip Nilai',     'en'=>'Academic Transcript',   'ja'=>'成績証明'],              'heroicon-o-document-text'],
            ['cv',                 ['id'=>'CV / Resume',         'en'=>'CV / Resume',           'ja'=>'履歴書'],                'heroicon-o-briefcase'],
            ['photo',              ['id'=>'Pas Foto',            'en'=>'Photograph',            'ja'=>'写真'],                  'heroicon-o-camera'],
            ['medical_check',      ['id'=>'Medical Check-up',    'en'=>'Medical Check-up',      'ja'=>'健康診断書'],            'heroicon-o-heart'],
            ['sktm',               ['id'=>'SKTM (Tidak Mampu)',  'en'=>'Letter of Indigence',   'ja'=>'貧困証明書'],            'heroicon-o-document'],
            ['sktm_polri',         ['id'=>'SKCK Polri',          'en'=>'Police Clearance',      'ja'=>'警察証明書'],            'heroicon-o-shield-check'],
            ['jlpt_certificate',   ['id'=>'Sertifikat JLPT',     'en'=>'JLPT Certificate',      'ja'=>'JLPT証書'],              'heroicon-o-language'],
            ['skill_certificate',  ['id'=>'Sertifikat Keahlian', 'en'=>'Skill Certificate',     'ja'=>'スキル証書'],            'heroicon-o-trophy'],
            ['other',              ['id'=>'Lainnya',             'en'=>'Other',                 'ja'=>'その他'],                'heroicon-o-document-duplicate'],
        ];

        foreach ($types as $i => [$key, $label, $icon]) {
            DocumentType::updateOrCreate(
                ['key' => $key],
                [
                    'label'      => $label,
                    'icon'       => $icon,
                    'sort_order' => $i + 1,
                    'is_active'  => true,
                ],
            );
        }
    }
}
