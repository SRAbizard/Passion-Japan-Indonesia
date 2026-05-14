<?php

namespace Database\Seeders;

use App\Models\Chapter;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Material;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\User;
use Illuminate\Database\Seeder;

class LmsSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedCategories();
        $this->seedCourses();
    }

    private function seedCategories(): void
    {
        $cats = [
            ['jlpt',           ['id'=>'JLPT',                'en'=>'JLPT',                'ja'=>'JLPT'],                'heroicon-o-language',         '#b32510'],
            ['business-japanese',['id'=>'Bahasa Bisnis',     'en'=>'Business Japanese',   'ja'=>'ビジネス日本語'],       'heroicon-o-briefcase',        '#0ea5e9'],
            ['culture',        ['id'=>'Budaya Jepang',       'en'=>'Japanese Culture',    'ja'=>'日本文化'],            'heroicon-o-heart',            '#ec4899'],
            ['interview-prep', ['id'=>'Persiapan Interview', 'en'=>'Interview Prep',      'ja'=>'面接対策'],            'heroicon-o-academic-cap',     '#f59e0b'],
            ['ssw-prep',       ['id'=>'Persiapan SSW',       'en'=>'SSW Exam Prep',       'ja'=>'特定技能試験対策'],     'heroicon-o-document-text',    '#10b981'],
        ];
        foreach ($cats as $i => [$slug, $name, $icon, $color]) {
            CourseCategory::firstOrCreate(['slug' => $slug], [
                'name'       => $name,
                'icon'       => $icon,
                'color'      => $color,
                'sort_order' => $i + 1,
            ]);
        }
    }

    private function seedCourses(): void
    {
        $instructor = User::role('superadmin')->first() ?? User::role('admin')->first() ?? User::first();
        $jlpt = CourseCategory::where('slug', 'jlpt')->first();
        $biz  = CourseCategory::where('slug', 'business-japanese')->first();
        $intv = CourseCategory::where('slug', 'interview-prep')->first();
        $ssw  = CourseCategory::where('slug', 'ssw-prep')->first();

        // Course 1: JLPT N5 Foundations (free, with quiz)
        $c1 = Course::firstOrCreate(['slug' => 'jlpt-n5-foundations'], [
            'course_category_id' => $jlpt?->id,
            'instructor_id'      => $instructor?->id,
            'title' => [
                'id' => 'JLPT N5 — Fondasi Bahasa Jepang',
                'en' => 'JLPT N5 — Japanese Foundations',
                'ja' => 'JLPT N5 — 日本語の基礎',
            ],
            'subtitle' => [
                'id' => 'Mulai dari nol: hiragana, katakana, kosa kata dasar, dan tata bahasa N5.',
                'en' => 'Start from zero: hiragana, katakana, basic vocab, and N5 grammar.',
                'ja' => 'ゼロから始める：ひらがな、カタカナ、基本語彙、N5文法。',
            ],
            'description' => [
                'id' => '<p>Kursus pemula yang membawa Anda dari nol ke level JLPT N5 dalam 8 minggu. Kombinasi video, latihan, dan kuis interaktif.</p><p>Materi disusun oleh tim instruktur Passion Japan Indonesia dengan pengalaman mengajar di Jepang.</p>',
                'en' => '<p>A beginner-friendly course taking you from zero to JLPT N5 level in 8 weeks. Mix of video, drills, and interactive quizzes.</p><p>Curated by Passion Japan instructors with on-the-ground teaching experience in Japan.</p>',
                'ja' => '<p>ゼロから8週間でJLPT N5レベルに到達する初心者向けコース。動画、ドリル、インタラクティブクイズの組み合わせ。</p>',
            ],
            'what_you_learn' => [
                'id' => "Membaca & menulis hiragana dan katakana\n100+ kosa kata dasar\nPola kalimat sederhana (です・ます)\nAngka, waktu, dan tanggal\nPercakapan sehari-hari sederhana",
                'en' => "Read & write hiragana and katakana\n100+ essential vocabulary\nSimple sentence patterns (desu/masu)\nNumbers, time, and dates\nBasic everyday conversation",
                'ja' => "ひらがな・カタカナの読み書き\n基本語彙100語以上\n基本文型（です・ます）\n数字、時間、日付\n基本的な日常会話",
            ],
            'prerequisites' => [
                'id' => 'Tidak ada — kursus ini benar-benar untuk pemula.',
                'en' => 'None — this course truly starts from zero.',
                'ja' => 'なし — 完全な初心者向けです。',
            ],
            'level'         => 'beginner',
            'duration_days' => 90,
            'price'         => 0,
            'is_free'       => true,
            'is_featured'   => true,
            'published_at'  => now()->subDays(30),
        ]);
        $this->seedChaptersForN5($c1);
        $this->seedQuizForN5($c1);

        // Course 2: Business Japanese for Engineers
        $c2 = Course::firstOrCreate(['slug' => 'business-japanese-engineers'], [
            'course_category_id' => $biz?->id,
            'instructor_id'      => $instructor?->id,
            'title' => [
                'id' => 'Bahasa Jepang Bisnis untuk Engineer',
                'en' => 'Business Japanese for Engineers',
                'ja' => 'エンジニアのためのビジネス日本語',
            ],
            'subtitle' => [
                'id' => 'Komunikasi profesional di lingkungan kerja teknik Jepang.',
                'en' => 'Professional communication in Japanese tech workplaces.',
                'ja' => '日本のテック企業でのプロフェッショナルなコミュニケーション。',
            ],
            'description' => [
                'id' => '<p>Untuk lulusan engineering yang sudah menguasai N4–N3 dan ingin menyiapkan diri masuk perusahaan teknologi Jepang. Fokus: keigo, meeting, email kerja.</p>',
                'en' => '<p>For engineering grads with N4–N3 prep heading into Japanese tech companies. Focus: keigo, meetings, work email.</p>',
                'ja' => '<p>N4〜N3レベルの工学系卒業生が日本のテック企業に向けて準備するコース。敬語、会議、業務メールに焦点。</p>',
            ],
            'level'         => 'intermediate',
            'duration_days' => 60,
            'price'         => 750000,
            'currency'      => 'IDR',
            'is_free'       => false,
            'is_featured'   => true,
            'published_at'  => now()->subDays(20),
        ]);
        $this->seedChaptersGeneric($c2, [
            ['Pengenalan Keigo', 'Keigo Introduction', '敬語入門'],
            ['Komunikasi Email', 'Email Communication', 'メールコミュニケーション'],
            ['Meeting & Presentasi', 'Meetings & Presentations', '会議とプレゼン'],
        ]);

        // Course 3: SSW Caregiver Prep
        $c3 = Course::firstOrCreate(['slug' => 'ssw-caregiver-prep'], [
            'course_category_id' => $ssw?->id,
            'instructor_id'      => $instructor?->id,
            'title' => [
                'id' => 'Persiapan Ujian SSW Kaigo (Caregiver)',
                'en' => 'SSW Caregiver Exam Prep',
                'ja' => '特定技能 介護試験対策',
            ],
            'subtitle' => [
                'id' => 'Latihan soal lengkap untuk ujian skill caregiver SSW.',
                'en' => 'Complete drills for the SSW caregiver skills exam.',
                'ja' => '特定技能介護スキル試験のための演習。',
            ],
            'description' => [
                'id' => '<p>Modul lengkap dengan kosa kata medis, prosedur perawatan lansia, dan latihan soal ala ujian resmi.</p>',
                'en' => '<p>Comprehensive module with medical vocabulary, elder-care procedures, and exam-style drills.</p>',
                'ja' => '<p>医療語彙、高齢者ケア手順、模擬試験を含む総合モジュール。</p>',
            ],
            'level'         => 'elementary',
            'duration_days' => 45,
            'price'         => 500000,
            'currency'      => 'IDR',
            'is_free'       => false,
            'published_at'  => now()->subDays(10),
        ]);
        $this->seedChaptersGeneric($c3, [
            ['Kosa Kata Medis', 'Medical Vocabulary', '医療語彙'],
            ['Prosedur Caregiving', 'Caregiving Procedures', '介護手順'],
            ['Latihan Soal Ujian', 'Exam Practice', '試験演習'],
        ]);

        // Course 4: Interview Prep — Technical Intern
        $c4 = Course::firstOrCreate(['slug' => 'interview-prep-intern'], [
            'course_category_id' => $intv?->id,
            'instructor_id'      => $instructor?->id,
            'title' => [
                'id' => 'Persiapan Interview Magang Teknis',
                'en' => 'Technical Intern Interview Prep',
                'ja' => '技能実習面接対策',
            ],
            'subtitle' => [
                'id' => 'Tips, pertanyaan umum, dan simulasi interview.',
                'en' => 'Tips, common questions, and mock interviews.',
                'ja' => 'コツ、頻出質問、模擬面接。',
            ],
            'description' => [
                'id' => '<p>Persiapan menyeluruh sebelum interview — bahasa tubuh, jawaban kunci, dan simulasi langsung.</p>',
                'en' => '<p>Thorough prep before your interview — body language, key answers, and live simulations.</p>',
                'ja' => '<p>面接前の徹底準備 — ボディランゲージ、重要な回答、ライブシミュレーション。</p>',
            ],
            'level'         => 'beginner',
            'duration_days' => 30,
            'price'         => 0,
            'is_free'       => true,
            'published_at'  => now()->subDays(5),
        ]);
        $this->seedChaptersGeneric($c4, [
            ['Persiapan & Mindset', 'Preparation & Mindset', '準備とマインドセット'],
            ['Pertanyaan Umum', 'Common Questions', '頻出質問'],
            ['Simulasi Interview', 'Mock Interview', '模擬面接'],
        ]);
    }

    private function seedChaptersForN5(Course $course): void
    {
        $chapters = [
            [
                'title' => ['id' => 'Hiragana Lengkap', 'en' => 'Complete Hiragana', 'ja' => 'ひらがな完全マスター'],
                'desc'  => ['id' => 'Pelajari 46 karakter hiragana dasar.', 'en' => 'Master all 46 hiragana characters.', 'ja' => '46のひらがなを習得。'],
                'materials' => [
                    ['video', ['id' => 'Pengenalan Hiragana', 'en' => 'Introduction to Hiragana', 'ja' => 'ひらがな入門'], 'https://www.youtube.com/embed/_wzcEGI0kS8', null, 12, true],
                    ['text',  ['id' => 'Latihan Menulis Vokal', 'en' => 'Writing Vowels Drill', 'ja' => '母音書き取り練習'], null, ['id' => '<h2>Latihan menulis あ い う え お</h2><p>Tulis 10 kali per huruf. Perhatikan urutan stroke.</p>', 'en' => '<h2>Practice writing あ い う え お</h2><p>Write 10 times per character. Mind the stroke order.</p>', 'ja' => '<h2>あいうえお書き取り</h2><p>各文字10回書きましょう。書き順に注意。</p>'], 8, false],
                    ['video', ['id' => 'Konsonan K, S, T', 'en' => 'Consonants K, S, T', 'ja' => '子音 K・S・T'], 'https://www.youtube.com/embed/_wzcEGI0kS8', null, 15, false],
                ],
            ],
            [
                'title' => ['id' => 'Katakana & Kosa Kata Asing', 'en' => 'Katakana & Loanwords', 'ja' => 'カタカナと外来語'],
                'desc'  => ['id' => 'Karakter katakana dan penerapannya.', 'en' => 'Katakana characters and their use.', 'ja' => 'カタカナとその使い方。'],
                'materials' => [
                    ['video', ['id' => 'Pengenalan Katakana', 'en' => 'Introduction to Katakana', 'ja' => 'カタカナ入門'], 'https://www.youtube.com/embed/_wzcEGI0kS8', null, 10, false],
                    ['text',  ['id' => '50 Kata Loanwords Umum', 'en' => '50 Common Loanwords', 'ja' => '頻出外来語50'], null, ['id' => '<p>コーヒー (kopi), パン (roti), テレビ (TV), ...</p>', 'en' => '<p>コーヒー (coffee), パン (bread), テレビ (TV), ...</p>', 'ja' => '<p>コーヒー、パン、テレビ など。</p>'], 6, false],
                ],
            ],
            [
                'title' => ['id' => 'Tata Bahasa N5 Dasar', 'en' => 'Basic N5 Grammar', 'ja' => 'N5基本文法'],
                'desc'  => ['id' => 'Pola kalimat fundamental.', 'en' => 'Fundamental sentence patterns.', 'ja' => '基本文型。'],
                'materials' => [
                    ['video', ['id' => 'Pola です／ます', 'en' => 'desu/masu Patterns', 'ja' => 'です／ますの使い方'], 'https://www.youtube.com/embed/_wzcEGI0kS8', null, 18, false],
                    ['text',  ['id' => 'Partikel は・が・を', 'en' => 'Particles wa/ga/wo', 'ja' => '助詞は・が・を'], null, ['id' => '<p>は = topik, が = subjek, を = objek.</p>', 'en' => '<p>wa = topic, ga = subject, wo = object.</p>', 'ja' => '<p>は = 主題、が = 主語、を = 目的語。</p>'], 12, false],
                ],
            ],
        ];

        foreach ($chapters as $idx => $ch) {
            $chapter = Chapter::firstOrCreate(
                ['course_id' => $course->id, 'sort_order' => $idx + 1],
                ['title' => $ch['title'], 'description' => $ch['desc'], 'is_published' => true],
            );
            foreach ($ch['materials'] as $mIdx => [$type, $title, $videoUrl, $content, $duration, $preview]) {
                Material::firstOrCreate(
                    ['chapter_id' => $chapter->id, 'sort_order' => $mIdx + 1],
                    [
                        'title'             => $title,
                        'type'              => $type,
                        'video_url'         => $videoUrl,
                        'content'           => $content,
                        'duration_minutes'  => $duration,
                        'is_free_preview'   => $preview,
                    ],
                );
            }
        }
    }

    private function seedChaptersGeneric(Course $course, array $chapterTitles): void
    {
        foreach ($chapterTitles as $idx => [$id, $en, $ja]) {
            $chapter = Chapter::firstOrCreate(
                ['course_id' => $course->id, 'sort_order' => $idx + 1],
                [
                    'title' => ['id' => $id, 'en' => $en, 'ja' => $ja],
                    'is_published' => true,
                ],
            );
            // Each chapter gets 2 placeholder text materials
            for ($j = 1; $j <= 2; $j++) {
                Material::firstOrCreate(
                    ['chapter_id' => $chapter->id, 'sort_order' => $j],
                    [
                        'title' => [
                            'id' => "Materi {$j}: {$id}",
                            'en' => "Lesson {$j}: {$en}",
                            'ja' => "レッスン{$j}：{$ja}",
                        ],
                        'type'    => 'text',
                        'content' => [
                            'id' => "<p>Konten pembelajaran untuk materi {$j} di chapter {$id}. Akan diisi instruktur.</p>",
                            'en' => "<p>Learning content for lesson {$j} in chapter {$en}. To be filled by instructor.</p>",
                            'ja' => "<p>{$ja} チャプターのレッスン {$j} の内容。講師により記入予定。</p>",
                        ],
                        'duration_minutes' => 10,
                        'is_free_preview'  => $idx === 0 && $j === 1,
                    ],
                );
            }
        }
    }

    private function seedQuizForN5(Course $course): void
    {
        $quiz = Quiz::firstOrCreate(
            ['course_id' => $course->id],
            [
                'title' => [
                    'id' => 'Kuis Akhir JLPT N5',
                    'en' => 'JLPT N5 Final Quiz',
                    'ja' => 'JLPT N5 最終クイズ',
                ],
                'description' => [
                    'id' => 'Tes pemahaman dasar Anda. Lulus dengan minimal 70%.',
                    'en' => 'Test your foundational understanding. Pass with at least 70%.',
                    'ja' => '基礎理解を確認。70%以上で合格。',
                ],
                'passing_score'      => 70,
                'time_limit_minutes' => 15,
                'max_attempts'       => 0,
                'is_published'       => true,
            ],
        );

        $questions = [
            [
                'q' => ['id' => 'Karakter ＂あ＂ termasuk dalam huruf apa?', 'en' => 'Which writing system does ＂あ＂ belong to?', 'ja' => '「あ」はどの文字体系ですか？'],
                'choices' => [
                    ['key' => 'a', 'text' => ['id' => 'Hiragana', 'en' => 'Hiragana', 'ja' => 'ひらがな']],
                    ['key' => 'b', 'text' => ['id' => 'Katakana', 'en' => 'Katakana', 'ja' => 'カタカナ']],
                    ['key' => 'c', 'text' => ['id' => 'Kanji',    'en' => 'Kanji',    'ja' => '漢字']],
                    ['key' => 'd', 'text' => ['id' => 'Romaji',   'en' => 'Romaji',   'ja' => 'ローマ字']],
                ],
                'correct' => 'a',
            ],
            [
                'q' => ['id' => 'Apa arti ＂こんにちは＂?', 'en' => 'What does ＂こんにちは＂ mean?', 'ja' => '「こんにちは」の意味は？'],
                'choices' => [
                    ['key' => 'a', 'text' => ['id' => 'Selamat pagi', 'en' => 'Good morning', 'ja' => 'おはよう']],
                    ['key' => 'b', 'text' => ['id' => 'Halo / Selamat siang', 'en' => 'Hello / Good afternoon', 'ja' => 'こんにちは']],
                    ['key' => 'c', 'text' => ['id' => 'Selamat malam', 'en' => 'Good night', 'ja' => 'おやすみ']],
                    ['key' => 'd', 'text' => ['id' => 'Terima kasih', 'en' => 'Thank you', 'ja' => 'ありがとう']],
                ],
                'correct' => 'b',
            ],
            [
                'q' => ['id' => 'Partikel apa yang menandai topik kalimat?', 'en' => 'Which particle marks the topic of a sentence?', 'ja' => '主題を示す助詞は？'],
                'choices' => [
                    ['key' => 'a', 'text' => ['id' => 'を', 'en' => 'wo', 'ja' => 'を']],
                    ['key' => 'b', 'text' => ['id' => 'が', 'en' => 'ga', 'ja' => 'が']],
                    ['key' => 'c', 'text' => ['id' => 'は', 'en' => 'wa', 'ja' => 'は']],
                    ['key' => 'd', 'text' => ['id' => 'に', 'en' => 'ni', 'ja' => 'に']],
                ],
                'correct' => 'c',
            ],
            [
                'q' => ['id' => 'Bagaimana cara mengatakan ＂terima kasih＂ dalam bahasa Jepang?', 'en' => 'How do you say ＂thank you＂ in Japanese?', 'ja' => '「ありがとう」を選んでください。'],
                'choices' => [
                    ['key' => 'a', 'text' => ['id' => 'すみません', 'en' => 'sumimasen', 'ja' => 'すみません']],
                    ['key' => 'b', 'text' => ['id' => 'ありがとう', 'en' => 'arigatou',   'ja' => 'ありがとう']],
                    ['key' => 'c', 'text' => ['id' => 'おはよう',   'en' => 'ohayou',     'ja' => 'おはよう']],
                    ['key' => 'd', 'text' => ['id' => 'さようなら', 'en' => 'sayounara',  'ja' => 'さようなら']],
                ],
                'correct' => 'b',
            ],
            [
                'q' => ['id' => 'Berapa jumlah karakter hiragana dasar?', 'en' => 'How many basic hiragana characters are there?', 'ja' => '基本ひらがなはいくつありますか？'],
                'choices' => [
                    ['key' => 'a', 'text' => ['id' => '40', 'en' => '40', 'ja' => '40']],
                    ['key' => 'b', 'text' => ['id' => '46', 'en' => '46', 'ja' => '46']],
                    ['key' => 'c', 'text' => ['id' => '50', 'en' => '50', 'ja' => '50']],
                    ['key' => 'd', 'text' => ['id' => '52', 'en' => '52', 'ja' => '52']],
                ],
                'correct' => 'b',
            ],
        ];

        foreach ($questions as $idx => $q) {
            QuizQuestion::firstOrCreate(
                ['quiz_id' => $quiz->id, 'sort_order' => $idx + 1],
                [
                    'question'       => $q['q'],
                    'choices'        => $q['choices'],
                    'correct_answer' => $q['correct'],
                    'points'         => 1,
                ],
            );
        }
    }
}
