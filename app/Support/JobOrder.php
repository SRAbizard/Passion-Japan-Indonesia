<?php

namespace App\Support;

use App\Models\JobVacancy;

/**
 * The structured Job Order attached to a vacancy (job_vacancies.job_order).
 * One definition drives both the admin form and the generated
 * "Terjemahan Job Order" PDF, so the two never drift apart.
 *
 * Each field: [label (Indonesian, the document language), Japanese term,
 * input type, example placeholder].
 */
class JobOrder
{
    public const INSURANCE = [
        'kenko'  => 'Asuransi Kesehatan (Kenkou Hoken)',
        'kosei'  => 'Pensiun Kesejahteraan (Kousei Nenkin)',
        'koyo'   => 'Asuransi Ketenagakerjaan (Koyou Hoken)',
        'rosai'  => 'Asuransi Kecelakaan Kerja (Rousai Hoken)',
    ];

    public const EMPLOYMENT = [
        'seishain' => 'Karyawan Tetap (Seishain)',
        'keiyaku'  => 'Karyawan Kontrak (Keiyaku-shain)',
        'arubaito' => 'Paruh Waktu (Arubaito)',
        'haken'    => 'Karyawan Alih Daya (Haken)',
    ];

    /** @return array<string, array{title: string, jp: string, fields: array<string, array{0: string, 1: string, 2: string, 3?: string}>}> */
    public static function sections(): array
    {
        return [
            'company' => ['title' => 'Profil Perusahaan', 'jp' => '企業情報', 'fields' => [
                'name'           => ['Nama Perusahaan', '企業名', 'text', 'Nikko Shokuhin Co., Ltd. (日光食品株式会社)'],
                'representative' => ['Perwakilan / Direktur', '代表者', 'text', 'Koji Higashida (東田 晃次)'],
                'address'        => ['Alamat Perusahaan', '所在地', 'textarea', 'Beppu-shi, Ishigaki Higashi 5-Chome 1-Ban 5-Go'],
                'station'        => ['Stasiun Terdekat', '最寄り駅', 'text', 'JR Beppu Station (10 menit jalan kaki)'],
                'business'       => ['Deskripsi Bisnis', '事業内容', 'textarea', 'Produksi kue dan makanan manis khas Jepang'],
                'employees'      => ['Jumlah Karyawan', '従業員数', 'textarea', "- Total: 120 orang\n- Di cabang ini: 45 orang\n- Laki-laki 40% / Perempuan 60%"],
            ]],
            'job' => ['title' => 'Kondisi Pekerjaan & Kontrak', 'jp' => '労働条件・契約', 'fields' => [
                'job_type'      => ['Jenis Pekerjaan', '職種', 'textarea', 'Pembuatan kue/makanan manis, melipat kotak, pengepakan ke dalam kotak, persiapan pengiriman, dan pembersihan'],
                'employment'    => ['Status Kepegawaian', '雇用形態', 'select'],
                'contract'      => ['Masa Kontrak', '契約期間', 'text', '1 tahun, dapat diperpanjang'],
                'probation'     => ['Masa Percobaan', '試用期間', 'textarea', 'Ada, 3 bulan (tidak ada perubahan gaji)'],
                'work_location' => ['Lokasi Kerja', '勤務地', 'textarea', "Beppu-shi, Ishigaki Higashi\n- Gedung 1: 4-Chome 1-20\n- Gedung 2: 4-Chome 2-23"],
                'transfer'      => ['Kemungkinan Mutasi', '転勤', 'text', 'Tidak ada'],
            ]],
            'hours' => ['title' => 'Jam Kerja & Libur', 'jp' => '勤務時間・休日', 'fields' => [
                'work_hours'      => ['Jam Kerja', '勤務時間', 'text', '08.30 – 17.30 (Istirahat: 60 menit)'],
                'shift'           => ['Sistem Shift / Flex-time', 'シフト制', 'text', 'Tidak ada'],
                'monthly_average' => ['Rata-Rata Kerja', '月平均労働', 'text', '21 hari / 168 jam per bulan'],
                'overtime'        => ['Lembur', '時間外労働', 'textarea', 'Ada (Shokutei Jikan-gai Roudou: Ari), rata-rata 20 jam/bulan'],
                'holidays'        => ['Hari Libur', '休日', 'textarea', 'Sabtu dan Minggu, serta libur akhir/awal tahun (31 Desember – 3 Januari)'],
                'annual_holidays' => ['Total Libur Setahun', '年間休日', 'text', '108 hari'],
                'paid_leave'      => ['Cuti Berbayar (Yukyu)', '有給休暇', 'textarea', '10 hari setelah masa kerja terus-menerus mencapai 6 bulan (maksimal 20 hari setelah 6,5 tahun)'],
            ]],
            'pay' => ['title' => 'Informasi Gaji', 'jp' => '給与・手当', 'fields' => [
                'salary_system' => ['Sistem Gaji', '給与形態', 'text', 'Gaji bulanan'],
                'basic_salary'  => ['Gaji Pokok', '基本給', 'text', '¥180.000 / bulan'],
                'allowances'    => ['Tunjangan', '諸手当', 'textarea', "- Transportasi: ¥200 / hari (untuk jarak minimal 1 km)\n- Tempat tinggal: tidak ada\n- Keluarga: tidak ada"],
                'overtime_pay'  => ['Persentase Uang Lembur / Tambahan', '割増賃金', 'textarea', "- Lembur biasa (sampai 60 jam/bulan): 25%\n- Lembur (> 60 jam/bulan): 50%\n- Libur resmi (Houtei Kyujitsu): 35%\n- Kerja malam (Shinya): 35%"],
                'raise'         => ['Kenaikan Gaji', '昇給', 'text', 'Ada (tergantung kinerja perusahaan, tahun lalu sebesar 3%)'],
                'bonus'         => ['Bonus', '賞与', 'text', 'Ada (berlaku untuk masa kerja minimal 6 bulan, tahun lalu sebesar 1 bulan gaji)'],
            ]],
            'qualification' => ['title' => 'Kualifikasi & Persyaratan Pelamar', 'jp' => '応募資格', 'fields' => [
                'education' => ['Pendidikan & Pengalaman', '学歴・経験', 'textarea', 'Minimal lulusan SMA/SMK; pengalaman tidak wajib'],
                'language'  => ['Kemampuan Bahasa', '語学力', 'text', 'JLPT N4 atau JFT-Basic (A2)'],
                'licenses'  => ['Sertifikasi / Lisensi', '免許・資格', 'textarea', 'Lulus ujian keterampilan Tokutei Ginou bidang pengolahan makanan'],
                'others'    => ['Persyaratan Lain', 'その他条件', 'textarea', 'Usia 18–30 tahun, sehat jasmani dan rohani'],
            ]],
            'welfare' => ['title' => 'Fasilitas, Asuransi & Ketentuan Lainnya', 'jp' => '福利厚生・社会保険', 'fields' => [
                'insurance'      => ['Asuransi', '社会保険', 'checklist'],
                'accommodation'  => ['Akomodasi', '社員寮', 'textarea', 'Disediakan asrama karyawan (biaya tanggungan pribadi belum ditentukan)'],
                'meals'          => ['Subsidi Makan', '食事補助', 'text', 'Tidak ada'],
                'retirement_pay' => ['Uang Pensiun', '退職金', 'text', 'Tidak ada'],
                'retirement_age' => ['Usia Pensiun', '定年', 'text', '65 tahun (tersedia sistem kelanjutan kerja)'],
                'facilities'     => ['Fasilitas Lain', 'その他', 'textarea'],
            ]],
            'other' => ['title' => 'Informasi Lainnya', 'jp' => 'その他', 'fields' => [
                'introduced' => ['Jumlah Kandidat Yang Dikenalkan', '紹介人数', 'text', '4 Orang (Laki-laki)'],
                'accepted'   => ['Jumlah Kandidat Yang Diterima', '採用人数', 'text', '2 Orang (Laki-laki)'],
                'interview'  => ['Jadwal & Metode Interview', '面接', 'text', 'Online (Zoom), November 2026'],
                'notes'      => ['Catatan', '備考', 'textarea'],
            ]],
        ];
    }

    /** True when at least one Job Order field has been filled in. */
    public static function filled(JobVacancy $vacancy): bool
    {
        return collect($vacancy->job_order ?? [])->flatten()->filter(fn ($v) => filled($v))->isNotEmpty();
    }

    /**
     * The sections ready for the PDF: only filled rows, with select and
     * checklist values turned into text. Company name falls back to the
     * vacancy's company.
     *
     * @return array<int, array{title: string, jp: string, rows: array<int, array{label: string, jp: string, value: string|array}>}>
     */
    public static function forDocument(JobVacancy $vacancy): array
    {
        $data = $vacancy->job_order ?? [];
        data_fill($data, 'company.name', $vacancy->company?->name);

        $out = [];
        foreach (self::sections() as $key => $section) {
            $rows = [];
            foreach ($section['fields'] as $field => [$label, $jp, $type]) {
                $value = $data[$key][$field] ?? null;
                $value = match ($type) {
                    'select'    => self::EMPLOYMENT[$value] ?? $value,
                    'checklist' => collect((array) $value)->map(fn ($k) => self::INSURANCE[$k] ?? $k)->values()->all(),
                    default     => is_string($value) ? trim($value) : $value,
                };
                if ($type === 'checklist' && filled($data[$key]['insurance_note'] ?? null)) {
                    $value[] = trim($data[$key]['insurance_note']);
                }
                if (filled($value)) {
                    $rows[] = ['label' => $label, 'jp' => $jp, 'value' => $value];
                }
            }
            if ($rows) {
                $out[] = ['title' => $section['title'], 'jp' => $section['jp'], 'rows' => $rows];
            }
        }

        return $out;
    }

    /** "Terjemahan Job Order – Company [CODE]" */
    public static function documentTitle(JobVacancy $vacancy): string
    {
        $company = data_get($vacancy->job_order, 'company.name') ?: ($vacancy->company?->name ?? $vacancy->t('title'));
        $code    = trim((string) $vacancy->job_order_code);

        return 'Terjemahan Job Order – '.$company.($code !== '' ? " [{$code}]" : '');
    }

    /**
     * Split a textarea value into an intro paragraph and bullet points:
     * lines starting with "-" or "•" become bullets.
     *
     * @return array{text: array<int, string>, bullets: array<int, string>}
     */
    public static function lines(string $value): array
    {
        $text = $bullets = [];
        foreach (preg_split('/\R/u', $value) as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            if (preg_match('/^[-•*]\s*(.+)$/u', $line, $m)) {
                $bullets[] = $m[1];
            } else {
                $text[] = $line;
            }
        }

        return ['text' => $text, 'bullets' => $bullets];
    }
}
