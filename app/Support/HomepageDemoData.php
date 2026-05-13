<?php

namespace App\Support;

/**
 * Phase 2 demo data for the homepage. Each translatable field is stored as
 * ['id' => ..., 'en' => ..., 'ja' => ...] — the exact same shape Spatie
 * Translatable will write to JSON columns in Phase 3, so migration is a
 * one-liner: replace `static::benefits()` with `Benefit::published()->get()`
 * and Spatie's `HasTranslations` trait will hydrate the same array shape.
 */
final class HomepageDemoData
{
    public static function pick(array $field, ?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        return $field[$locale] ?? $field['id'] ?? reset($field) ?: '';
    }

    public static function benefits(): array
    {
        return [
            [
                'icon'  => 'M11.49 3.17c.39-.78 1.5-.78 1.9 0l1.84 3.67 4.06.59c.86.12 1.2 1.18.58 1.78l-2.94 2.86.69 4.03c.15.86-.75 1.51-1.51 1.1l-3.62-1.9-3.63 1.9c-.77.4-1.66-.25-1.51-1.1l.69-4.03L4.1 9.21c-.62-.6-.28-1.66.58-1.78l4.06-.59 1.84-3.67z',
                'title' => [
                    'id' => 'Proses Mudah dan Fleksibel',
                    'en' => 'Easy and Flexible Process',
                    'ja' => 'シンプルで柔軟なプロセス',
                ],
                'desc' => [
                    'id' => 'Administrasi online, sesi interview virtual, dan pertemuan langsung dengan perusahaan Jepang tanpa ribet.',
                    'en' => 'Online administration, virtual interview sessions, and direct meetings with Japanese companies — without the hassle.',
                    'ja' => 'オンライン手続き、バーチャル面接、日本企業との直接面談 — すべて簡単に。',
                ],
            ],
            [
                'icon'  => 'M12 8c-1.66 0-3 .9-3 2s1.34 2 3 2 3 .9 3 2-1.34 2-3 2m0-8v1m0 14v1m0-16a8 8 0 100 16 8 8 0 000-16z',
                'title' => [
                    'id' => 'Kualitas Terbaik dengan Harga Bersahabat',
                    'en' => 'Top Quality at a Friendly Price',
                    'ja' => '最高品質を手の届く価格で',
                ],
                'desc' => [
                    'id' => 'Biaya transparan dengan opsi pembiayaan fleksibel — mentor profesional yang menemani Anda hingga penempatan.',
                    'en' => 'Transparent pricing with flexible financing — professional mentors guide you all the way to placement.',
                    'ja' => '透明な料金体系と柔軟な支払いオプション — 配属までプロのメンターが伴走します。',
                ],
            ],
            [
                'icon'  => 'M9 13l-3-3m0 0l3-3m-3 3h12a4 4 0 014 4v2',
                'title' => [
                    'id' => 'Pilihan Karir yang Sesuai untuk Kamu',
                    'en' => 'Career Paths That Match You',
                    'ja' => 'あなたに合ったキャリアの選択肢',
                ],
                'desc' => [
                    'id' => 'Hospitality, Manufacturing, IT, Office, dan Senior Care — pilih jalur karier yang cocok dengan keahlianmu.',
                    'en' => 'Hospitality, Manufacturing, IT, Office, and Senior Care — pick the career path that fits your skills.',
                    'ja' => 'ホスピタリティ、製造、IT、オフィス、介護 — スキルに合ったキャリアパスを選択。',
                ],
            ],
        ];
    }

    public static function programs(): array
    {
        return [
            ['name' => 'Tokutei Ginou', 'tag' => ['id' => 'Visa', 'en' => 'Visa', 'ja' => 'ビザ'], 'desc' => [
                'id' => 'Specified Skilled Worker untuk 14 sektor industri Jepang.',
                'en' => 'Specified Skilled Worker visa for 14 Japanese industry sectors.',
                'ja' => '日本の14産業分野向け特定技能ビザ。',
            ]],
            ['name' => 'Engineering / Gijinkoku', 'tag' => ['id' => 'Visa', 'en' => 'Visa', 'ja' => 'ビザ'], 'desc' => [
                'id' => 'Visa engineer untuk lulusan teknik / IT.',
                'en' => 'Engineer visa for engineering / IT graduates.',
                'ja' => '工学・IT卒業者向けの技術ビザ。',
            ]],
            ['name' => 'Internship', 'tag' => ['id' => 'Magang', 'en' => 'Internship', 'ja' => '実習'], 'desc' => [
                'id' => 'Program magang teknis 1–3 tahun di perusahaan Jepang.',
                'en' => '1–3 year technical internship at Japanese companies.',
                'ja' => '日本企業での1〜3年の技能実習プログラム。',
            ]],
            ['name' => 'JLPT N5 & N4', 'tag' => ['id' => 'Bahasa', 'en' => 'Language', 'ja' => '語学'], 'desc' => [
                'id' => 'Kelas bahasa Jepang persiapan ujian JLPT.',
                'en' => 'Japanese language classes preparing for JLPT exam.',
                'ja' => 'JLPT試験対策の日本語クラス。',
            ]],
            ['name' => 'Driver Jepang', 'tag' => ['id' => 'Karier', 'en' => 'Career', 'ja' => 'キャリア'], 'desc' => [
                'id' => 'Jalur karier driver komersial di Jepang.',
                'en' => 'Career path for commercial drivers in Japan.',
                'ja' => '日本での商業ドライバーキャリアパス。',
            ]],
            ['name' => 'Kaigo (Caregiver)', 'tag' => ['id' => 'Karier', 'en' => 'Career', 'ja' => 'キャリア'], 'desc' => [
                'id' => 'Pelatihan dan penempatan tenaga perawat lansia.',
                'en' => 'Training and placement for elderly caregivers.',
                'ja' => '介護人材の研修と配属。',
            ]],
        ];
    }

    public static function jobs(): array
    {
        return [
            [
                'category' => 'Gijinkoku / Engineering',
                'items' => [
                    ['title' => ['id' => 'Civil Engineer', 'en' => 'Civil Engineer', 'ja' => '土木技師'], 'company' => 'PT Sakura Indah', 'location' => 'Tokyo, Japan', 'salary' => '¥220K–280K', 'visa' => 'Engineering', 'tag' => ['id' => 'Konstruksi', 'en' => 'Construction', 'ja' => '建設']],
                    ['title' => ['id' => 'IT Software Engineer', 'en' => 'IT Software Engineer', 'ja' => 'ITソフトウェアエンジニア'], 'company' => 'Nippon Tech Co.', 'location' => 'Osaka, Japan', 'salary' => '¥260K–350K', 'visa' => 'Engineering', 'tag' => ['id' => 'IT', 'en' => 'IT', 'ja' => 'IT']],
                    ['title' => ['id' => 'Office Administrator', 'en' => 'Office Administrator', 'ja' => 'オフィス管理者'], 'company' => 'Yamato Trading', 'location' => 'Yokohama, Japan', 'salary' => '¥190K–230K', 'visa' => 'Engineering', 'tag' => ['id' => 'Administrasi', 'en' => 'Administration', 'ja' => '事務']],
                ],
            ],
            [
                'category' => 'Tokutei Ginou',
                'items' => [
                    ['title' => ['id' => 'Caregiver (Kaigo)', 'en' => 'Caregiver (Kaigo)', 'ja' => '介護士'], 'company' => 'Sakura Care Home', 'location' => 'Nagoya, Japan', 'salary' => '¥180K–210K', 'visa' => 'Tokutei Ginou', 'tag' => ['id' => 'Kaigo', 'en' => 'Caregiving', 'ja' => '介護']],
                    ['title' => ['id' => 'Hotel Service Staff', 'en' => 'Hotel Service Staff', 'ja' => 'ホテルサービススタッフ'], 'company' => 'Hotel Kyoto Royal', 'location' => 'Kyoto, Japan', 'salary' => '¥170K–200K', 'visa' => 'Tokutei Ginou', 'tag' => ['id' => 'Perhotelan', 'en' => 'Hospitality', 'ja' => 'ホスピタリティ']],
                    ['title' => ['id' => 'Food Production', 'en' => 'Food Production', 'ja' => '食品製造'], 'company' => 'Asahi Foods', 'location' => 'Sapporo, Japan', 'salary' => '¥175K–195K', 'visa' => 'Tokutei Ginou', 'tag' => ['id' => 'Restoran', 'en' => 'Food Service', 'ja' => '飲食']],
                ],
            ],
        ];
    }

    public static function courses(): array
    {
        return [
            [
                'name' => 'Japan N5',
                'duration' => ['id' => '365 hari', 'en' => '365 days', 'ja' => '365日'],
                'chapters' => ['id' => '29 bab', 'en' => '29 chapters', 'ja' => '29章'],
                'desc' => [
                    'id' => 'Bahasa Jepang dasar untuk pemula — huruf hiragana, katakana, kosakata sehari-hari.',
                    'en' => 'Beginner Japanese — hiragana, katakana, and everyday vocabulary.',
                    'ja' => '初心者向け日本語 — ひらがな、カタカナ、日常語彙。',
                ],
                'color' => 'from-brand-700 to-brand-900',
            ],
            [
                'name' => 'Japan N4',
                'duration' => ['id' => '365 hari', 'en' => '365 days', 'ja' => '365日'],
                'chapters' => ['id' => '11 bab', 'en' => '11 chapters', 'ja' => '11章'],
                'desc' => [
                    'id' => 'Kelas lanjutan menuju level menengah — tata bahasa dan percakapan sehari-hari.',
                    'en' => 'Intermediate-track class — grammar and everyday conversation.',
                    'ja' => '中級レベルへのステップアップクラス — 文法と日常会話。',
                ],
                'color' => 'from-surface-700 to-surface-900',
            ],
        ];
    }

    public static function faqs(): array
    {
        return [
            ['q' => [
                'id' => 'Apakah jadwal belajar bisa fleksibel?',
                'en' => 'Are class schedules flexible?',
                'ja' => '学習スケジュールは柔軟ですか？',
            ], 'a' => [
                'id' => 'Ya, semua kelas e-learning bisa diakses kapan saja selama masa aktif kursus. Untuk kelas live, kami sediakan beberapa slot waktu setiap minggu.',
                'en' => 'Yes. All e-learning classes can be accessed at any time during the active course period. For live classes, we offer multiple time slots each week.',
                'ja' => 'はい。すべてのEラーニングはコース有効期間内にいつでも受講可能です。ライブクラスは毎週複数の時間帯をご用意しています。',
            ]],
            ['q' => [
                'id' => 'Apakah saya dapat sertifikat setelah menyelesaikan program?',
                'en' => 'Do I get a certificate after completing the program?',
                'ja' => 'プログラム修了後に修了証は発行されますか？',
            ], 'a' => [
                'id' => 'Semua peserta yang menyelesaikan program kami menerima sertifikat resmi yang diakui oleh mitra perusahaan kami di Jepang.',
                'en' => 'Every participant who completes our program receives an official certificate recognized by our Japanese partner companies.',
                'ja' => '修了者全員に、日本のパートナー企業に認められた公式修了証を発行します。',
            ]],
            ['q' => [
                'id' => 'Bagaimana cara mengikuti ujian kompetensi?',
                'en' => 'How do I take the competency exam?',
                'ja' => '技能試験はどのように受験しますか？',
            ], 'a' => [
                'id' => 'Ujian kompetensi diadakan langsung oleh lembaga resmi Jepang. Kami membantu pendaftaran, pelatihan, dan simulasi ujian.',
                'en' => 'Competency exams are administered directly by official Japanese authorities. We assist with registration, training, and mock exams.',
                'ja' => '技能試験は日本の公式機関が直接実施します。登録、研修、模擬試験までサポートいたします。',
            ]],
            ['q' => [
                'id' => 'Berapa lama saya bisa mengakses materi?',
                'en' => 'How long can I access the course materials?',
                'ja' => '教材へのアクセス期間はどれくらいですか？',
            ], 'a' => [
                'id' => 'Materi kursus dapat diakses selama 365 hari sejak tanggal pembelian, dengan opsi perpanjangan.',
                'en' => 'Course materials are accessible for 365 days from the date of purchase, with extension options.',
                'ja' => '教材はご購入日から365日間アクセス可能で、延長オプションもございます。',
            ]],
            ['q' => [
                'id' => 'Bagaimana cara mendaftar ke program ini?',
                'en' => 'How do I register for the program?',
                'ja' => 'プログラムへの申し込み方法は？',
            ], 'a' => [
                'id' => 'Klik tombol "Get Started" di atas, isi formulir registrasi, dan tim kami akan menghubungi Anda untuk konsultasi gratis.',
                'en' => 'Click the "Get Started" button above, fill out the registration form, and our team will contact you for a free consultation.',
                'ja' => '上記の「はじめる」ボタンから登録フォームをご記入ください。担当者より無料相談のご連絡を差し上げます。',
            ]],
            ['q' => [
                'id' => 'Apakah ada opsi pembiayaan?',
                'en' => 'Are there financing options?',
                'ja' => '支払いオプションはありますか？',
            ], 'a' => [
                'id' => 'Ya, kami menyediakan cicilan dan opsi pembiayaan dengan partner lembaga keuangan terpercaya.',
                'en' => 'Yes, we offer instalments and financing through trusted partner institutions.',
                'ja' => '信頼できる金融機関パートナーを通じた分割払いや融資のオプションをご用意しています。',
            ]],
        ];
    }

    public static function testimonials(): array
    {
        return [
            'company' => [
                [
                    'name'  => 'PT Sakura Indah',
                    'role'  => ['id' => 'Mitra Perusahaan', 'en' => 'Partner Company', 'ja' => 'パートナー企業'],
                    'quote' => [
                        'id' => 'Kandidat dari Passion Japan datang dengan bahasa Jepang yang siap pakai dan etos kerja yang baik. Sangat terbantu.',
                        'en' => 'Candidates from Passion Japan arrive with work-ready Japanese and excellent work ethic. Hugely helpful.',
                        'ja' => 'Passion Japan からの候補者は実務レベルの日本語と素晴らしい労働倫理を備えて来てくれます。大変助かっています。',
                    ],
                ],
                [
                    'name'  => 'PT Nippon Jaya',
                    'role'  => ['id' => 'Mitra Perusahaan', 'en' => 'Partner Company', 'ja' => 'パートナー企業'],
                    'quote' => [
                        'id' => 'Proses rekrutmen yang transparan dan cepat. Tim Passion Japan profesional dari awal sampai penempatan.',
                        'en' => 'Transparent and fast recruitment process. The Passion Japan team is professional from start to placement.',
                        'ja' => '透明で迅速な採用プロセス。Passion Japan のチームは最初から配属まで一貫してプロフェッショナルです。',
                    ],
                ],
            ],
            'student' => [
                [
                    'name'  => 'Akira Tanaka',
                    'role'  => ['id' => 'Alumni — Engineering, Tokyo', 'en' => 'Alumnus — Engineering, Tokyo', 'ja' => '卒業生 — 技術職、東京'],
                    'quote' => [
                        'id' => 'Berkat persiapan bahasa dan interview di Passion Japan, saya berhasil bekerja di perusahaan teknik di Tokyo.',
                        'en' => 'Thanks to Passion Japan\'s language and interview prep, I landed a role at an engineering firm in Tokyo.',
                        'ja' => 'Passion Japan の語学・面接対策のおかげで、東京のエンジニアリング企業に就職できました。',
                    ],
                ],
                [
                    'name'  => 'Rina Sato',
                    'role'  => ['id' => 'Alumni — Kaigo, Nagoya', 'en' => 'Alumna — Caregiving, Nagoya', 'ja' => '卒業生 — 介護、名古屋'],
                    'quote' => [
                        'id' => 'Programnya jelas, mentornya sabar. Sekarang saya sudah bekerja sebagai caregiver di Nagoya.',
                        'en' => 'The program is clear and the mentors are patient. I now work as a caregiver in Nagoya.',
                        'ja' => 'プログラムは明確でメンターも親切でした。今は名古屋で介護士として働いています。',
                    ],
                ],
            ],
        ];
    }

    public static function workflow(): array
    {
        $steps = [
            [1,  'Konsultasi Pekerjaan',                       'Job Consultation',                       '就職相談',
                 'Sesi tanya-jawab dengan konsultan karir untuk menentukan jalur yang cocok.',
                 'Q&A session with a career consultant to find the right path.',
                 'キャリアコンサルタントと相談し、最適なパスを決定します。'],
            [2,  'Memilih Lowongan Tersedia',                  'Pick from Available Openings',           '掲載求人を選ぶ',
                 'Pilih posisi dari katalog lowongan terverifikasi kami.',
                 'Choose a role from our verified job catalogue.',
                 '当社の確認済み求人カタログから職種を選択。'],
            [3,  'Pengumpulan Berkas',                         'Document Collection',                    '書類の収集',
                 'Upload dokumen — KTP, ijazah, CV — melalui dashboard siswa.',
                 'Upload documents — ID, diploma, CV — via the student dashboard.',
                 '生徒ダッシュボード経由で身分証、卒業証明書、履歴書をアップロード。'],
            [4,  'Seleksi Administrasi',                       'Administrative Screening',               '書類選考',
                 'Tim kami memverifikasi dokumen sebelum diteruskan ke perusahaan.',
                 'Our team verifies documents before forwarding them to the company.',
                 '社内チームが書類を確認した上で企業へ送付。'],
            [5,  'Interview Tahap Pertama',                    'First-Stage Interview',                  '一次面接',
                 'Wawancara awal dengan perwakilan perusahaan via online.',
                 'Initial online interview with the company representative.',
                 '企業担当者とオンラインで一次面接。'],
            [6,  'Latihan Interview & Pemantapan Kaiwa',       'Interview Practice & Conversation Drill','面接練習・会話強化',
                 'Sesi role-play bersama mentor untuk membangun kepercayaan diri.',
                 'Role-play sessions with mentors to build confidence.',
                 'メンターとのロールプレイで自信を構築。'],
            [7,  'Interview Tahap Kedua',                      'Second-Stage Interview',                 '二次面接',
                 'Final interview langsung dengan tim HR perusahaan Jepang.',
                 'Final interview directly with the Japanese company\'s HR team.',
                 '日本企業の人事チームと最終面接。'],
            [8,  'Pengurusan Administrasi Keberangkatan',      'Departure Administration',               '出国手続き',
                 'Visa, COE, tiket, dan asuransi diurus oleh tim kami.',
                 'Visa, COE, tickets, and insurance handled by our team.',
                 'ビザ、COE、航空券、保険を当社チームで手配。'],
            [9,  'Berangkat Ke Jepang',                         'Departure to Japan',                    '日本へ出発',
                 'Penerbangan ke Jepang dengan pendampingan dokumen lengkap.',
                 'Flight to Japan with complete document support.',
                 '必要書類完備のもと日本へフライト。'],
            [10, 'Tiba di Jepang dan Penjemputan',             'Arrival and Pickup in Japan',            '日本到着・送迎',
                 'Pickup di bandara, orientasi tempat tinggal dan pengenalan perusahaan.',
                 'Airport pickup, housing orientation, and company onboarding.',
                 '空港送迎、住居案内、企業オリエンテーション。'],
        ];

        return array_map(fn ($s) => [
            'step' => $s[0],
            'title' => ['id' => $s[1], 'en' => $s[2], 'ja' => $s[3]],
            'desc'  => ['id' => $s[4], 'en' => $s[5], 'ja' => $s[6]],
        ], $steps);
    }
}
