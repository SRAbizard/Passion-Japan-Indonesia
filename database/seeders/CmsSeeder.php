<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Faq;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\User;
use App\Support\HomepageDemoData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CmsSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSettings();
        $this->seedFaqs();
        $this->seedTestimonials();
        $this->seedBlog();
        $this->seedEvents();
    }

    private function seedSettings(): void
    {
        $defaults = [
            'contact.email'    => 'info@passionjapan.id',
            'contact.phone'    => '+62 882-0078-85021',
            'contact.whatsapp' => '62882007885021',
            'social.instagram' => 'https://instagram.com/passionjapan',
            'social.facebook'  => 'https://facebook.com/passionjapan',
            'social.tiktok'    => 'https://tiktok.com/@passionjapan',
            'stats.students'   => '3,300+',
            'stats.workers'    => '1,200+',
            'stats.companies'  => '100+',
        ];
        foreach ($defaults as $k => $v) {
            $group = explode('.', $k)[0];
            Setting::set($k, $v, $group);
        }
    }

    private function seedFaqs(): void
    {
        foreach (HomepageDemoData::faqs() as $i => $f) {
            Faq::firstOrCreate(
                ['question->id' => $f['q']['id']],
                [
                    'question'     => $f['q'],
                    'answer'       => $f['a'],
                    'sort_order'   => $i + 1,
                    'is_published' => true,
                ],
            );
        }
    }

    private function seedTestimonials(): void
    {
        foreach (HomepageDemoData::testimonials()['company'] as $i => $t) {
            Testimonial::firstOrCreate(
                ['name' => $t['name'], 'kind' => 'company'],
                ['role' => $t['role'], 'quote' => $t['quote'], 'kind' => 'company', 'sort_order' => $i + 1, 'is_published' => true],
            );
        }
        foreach (HomepageDemoData::testimonials()['student'] as $i => $t) {
            Testimonial::firstOrCreate(
                ['name' => $t['name'], 'kind' => 'student'],
                ['role' => $t['role'], 'quote' => $t['quote'], 'kind' => 'student', 'sort_order' => $i + 1, 'is_published' => true],
            );
        }
    }

    private function seedBlog(): void
    {
        $cats = [
            ['slug' => 'jepang', 'name' => ['id' => 'Tentang Jepang', 'en' => 'About Japan', 'ja' => '日本について'], 'color' => '#b32510'],
            ['slug' => 'tips',   'name' => ['id' => 'Tips & Trik',     'en' => 'Tips & Tricks', 'ja' => 'ヒントとコツ'], 'color' => '#0ea5e9'],
            ['slug' => 'alumni', 'name' => ['id' => 'Kisah Alumni',    'en' => 'Alumni Stories', 'ja' => '卒業生ストーリー'], 'color' => '#22c55e'],
        ];
        foreach ($cats as $i => $c) {
            PostCategory::firstOrCreate(['slug' => $c['slug']], ['name' => $c['name'], 'color' => $c['color'], 'sort_order' => $i + 1]);
        }

        $author = User::role('superadmin')->first();
        $posts = [
            [
                'slug' => 'persiapan-jlpt-n5-untuk-pemula',
                'category' => 'jepang',
                'title' => ['id' => 'Persiapan JLPT N5 untuk Pemula', 'en' => 'JLPT N5 Prep for Beginners', 'ja' => '初心者のための JLPT N5 対策'],
                'excerpt' => ['id' => 'Strategi belajar 90 hari untuk lulus ujian JLPT N5 dengan percaya diri.', 'en' => '90-day study plan to pass JLPT N5 with confidence.', 'ja' => '自信を持って JLPT N5 に合格するための90日学習計画。'],
                'body' => [
                    'id' => '<p>JLPT N5 adalah level dasar — fokus pada hiragana, katakana, dan 100 kanji pertama. Berikut roadmap 90 hari:</p><ul><li>Minggu 1–2: Hiragana + Katakana harian</li><li>Minggu 3–6: 100 kanji + tata bahasa dasar</li><li>Minggu 7–10: Latihan soal + kosa kata</li><li>Minggu 11–12: Simulasi ujian</li></ul>',
                    'en' => '<p>JLPT N5 is the entry level — focus on hiragana, katakana, and the first 100 kanji. A 90-day roadmap:</p><ul><li>Weeks 1–2: Hiragana + Katakana daily</li><li>Weeks 3–6: 100 kanji + basic grammar</li><li>Weeks 7–10: Practice problems + vocabulary</li><li>Weeks 11–12: Mock exams</li></ul>',
                    'ja' => '<p>JLPT N5 は入門レベルです。ひらがな、カタカナ、最初の100個の漢字に集中しましょう。90日ロードマップ：</p><ul><li>1〜2週目：ひらがな・カタカナ</li><li>3〜6週目：漢字100＋基本文法</li><li>7〜10週目：練習問題＋語彙</li><li>11〜12週目：模擬試験</li></ul>',
                ],
                'tags' => ['jlpt', 'n5', 'pemula'],
                'is_featured' => true,
            ],
            [
                'slug' => '10-frasa-wajib-untuk-interview-perusahaan-jepang',
                'category' => 'tips',
                'title' => ['id' => '10 Frasa Wajib untuk Interview Perusahaan Jepang', 'en' => '10 Must-Know Phrases for Japanese Interviews', 'ja' => '日本企業の面接で必須の10フレーズ'],
                'excerpt' => ['id' => 'Frasa interview yang akan membuat pewawancara Jepang Anda terkesan.', 'en' => 'Phrases that impress Japanese interviewers.', 'ja' => '日本人面接官を感心させるフレーズ。'],
                'body' => [
                    'id' => '<p>Berikut 10 frasa interview yang wajib Anda kuasai sebelum wawancara dengan perusahaan Jepang:</p><ol><li>はじめまして — Senang berkenalan</li><li>よろしくお願いします — Mohon kerja samanya</li><li>頑張ります — Saya akan berusaha keras</li></ol>',
                    'en' => '<p>Ten must-know interview phrases before meeting a Japanese hiring panel:</p><ol><li>はじめまして — Nice to meet you</li><li>よろしくお願いします — Looking forward to working with you</li><li>頑張ります — I will do my best</li></ol>',
                    'ja' => '<p>日本企業との面接前にマスターすべき10のフレーズ：</p><ol><li>はじめまして</li><li>よろしくお願いします</li><li>頑張ります</li></ol>',
                ],
                'tags' => ['interview', 'tips', 'bahasa'],
            ],
            [
                'slug' => 'kisah-akira-dari-bandung-ke-tokyo',
                'category' => 'alumni',
                'title' => ['id' => 'Kisah Akira: Dari Bandung ke Tokyo', 'en' => 'Akira\'s Story: From Bandung to Tokyo', 'ja' => 'アキラさんの物語：バンドンから東京へ'],
                'excerpt' => ['id' => 'Bagaimana Akira, lulusan teknik dari Bandung, akhirnya bekerja sebagai software engineer di Tokyo.', 'en' => 'How Akira, an engineering graduate from Bandung, landed a software engineering role in Tokyo.', 'ja' => 'バンドン出身の工学部卒業生アキラさんが、東京でソフトウェアエンジニアになるまで。'],
                'body' => [
                    'id' => '<p>Akira lulus dari ITB tahun 2024, lalu mengikuti program persiapan kerja Jepang Passion Japan selama 9 bulan. Hari ini, dia bekerja di startup AI di Shibuya.</p><blockquote>"Yang paling berat adalah JLPT N3. Tapi mentor di Passion Japan sabar membimbing dari N5 sampai N3 hanya dalam 8 bulan."</blockquote>',
                    'en' => '<p>Akira graduated from ITB in 2024 and joined the Passion Japan Japan-prep program for 9 months. Today he works at an AI startup in Shibuya.</p><blockquote>"JLPT N3 was the hardest. The mentors at Passion Japan guided me from N5 to N3 in just 8 months."</blockquote>',
                    'ja' => '<p>アキラさんは2024年に ITB を卒業し、Passion Japan の日本就職準備プログラムに9ヶ月間参加しました。現在は渋谷の AI スタートアップで働いています。</p>',
                ],
                'tags' => ['alumni', 'tokyo', 'engineer'],
                'is_featured' => true,
            ],
        ];

        foreach ($posts as $i => $p) {
            Post::firstOrCreate(['slug' => $p['slug']], [
                'post_category_id' => PostCategory::where('slug', $p['category'])->value('id'),
                'author_id'        => $author?->id,
                'title'            => $p['title'],
                'excerpt'          => $p['excerpt'],
                'body'             => $p['body'],
                'tags'             => $p['tags'] ?? [],
                'published_at'     => now()->subDays(($i + 1) * 3),
                'is_featured'      => $p['is_featured'] ?? false,
            ]);
        }
    }

    private function seedEvents(): void
    {
        $cats = [
            ['slug' => 'seminar',    'name' => ['id' => 'Seminar', 'en' => 'Seminar', 'ja' => 'セミナー'], 'color' => '#b32510'],
            ['slug' => 'job-fair',   'name' => ['id' => 'Job Fair', 'en' => 'Job Fair', 'ja' => 'ジョブフェア'], 'color' => '#22c55e'],
            ['slug' => 'interview',  'name' => ['id' => 'Interview Session', 'en' => 'Interview Session', 'ja' => '面接セッション'], 'color' => '#0ea5e9'],
        ];
        foreach ($cats as $i => $c) {
            EventCategory::firstOrCreate(['slug' => $c['slug']], ['name' => $c['name'], 'color' => $c['color'], 'sort_order' => $i + 1]);
        }

        $events = [
            [
                'slug' => 'job-fair-jepang-jakarta-2026',
                'category' => 'job-fair',
                'title' => ['id' => 'Job Fair Jepang Jakarta 2026', 'en' => 'Japan Job Fair Jakarta 2026', 'ja' => 'ジャパン ジョブフェア ジャカルタ 2026'],
                'description' => [
                    'id' => '<p>50+ perusahaan Jepang merekrut langsung di Jakarta. Sektor: IT, manufaktur, kaigo, hospitality.</p>',
                    'en' => '<p>50+ Japanese companies recruiting on-site in Jakarta. Sectors: IT, manufacturing, kaigo, hospitality.</p>',
                    'ja' => '<p>50社以上の日本企業がジャカルタで現地採用。分野：IT、製造、介護、ホスピタリティ。</p>',
                ],
                'organizer' => ['id' => 'Passion Japan Indonesia', 'en' => 'Passion Japan Indonesia', 'ja' => 'Passion Japan Indonesia'],
                'location'  => ['id' => 'JCC Senayan, Jakarta',    'en' => 'JCC Senayan, Jakarta',    'ja' => 'JCC センヤン、ジャカルタ'],
                'starts_at' => now()->addDays(45),
                'ends_at'   => now()->addDays(46),
                'featured'  => true,
            ],
            [
                'slug' => 'seminar-tokutei-ginou-yogyakarta',
                'category' => 'seminar',
                'title' => ['id' => 'Seminar: Tokutei Ginou di Yogyakarta', 'en' => 'Seminar: Tokutei Ginou in Yogyakarta', 'ja' => 'セミナー：ジョクジャカルタでの特定技能'],
                'description' => [
                    'id' => '<p>Pelajari semua tentang visa Tokutei Ginou langsung dari konsultan Passion Japan.</p>',
                    'en' => '<p>Learn everything about the Tokutei Ginou visa directly from Passion Japan consultants.</p>',
                    'ja' => '<p>Passion Japan のコンサルタントから特定技能ビザのすべてを学ぶ。</p>',
                ],
                'organizer' => ['id' => 'Passion Japan Indonesia', 'en' => 'Passion Japan Indonesia', 'ja' => 'Passion Japan Indonesia'],
                'location'  => ['id' => 'UGM Yogyakarta',          'en' => 'UGM Yogyakarta',          'ja' => 'UGM ジョクジャカルタ'],
                'starts_at' => now()->addDays(14)->setTime(14, 0),
                'ends_at'   => now()->addDays(14)->setTime(17, 0),
                'featured'  => false,
            ],
            [
                'slug' => 'sesi-interview-tahap-1-mei-2026',
                'category' => 'interview',
                'title' => ['id' => 'Sesi Interview Tahap 1 — Mei 2026', 'en' => 'Stage-1 Interview Session — May 2026', 'ja' => '一次面接セッション 2026年5月'],
                'description' => [
                    'id' => '<p>Sesi wawancara tahap pertama untuk lowongan Driver dan Kaigo. Khusus peserta yang sudah lolos seleksi administrasi.</p>',
                    'en' => '<p>First-stage interviews for Driver and Kaigo openings. Invitation only.</p>',
                    'ja' => '<p>ドライバーと介護職の一次面接。書類選考通過者のみ招待。</p>',
                ],
                'organizer' => ['id' => 'Passion Japan + Mitra', 'en' => 'Passion Japan + Partner', 'ja' => 'Passion Japan＋パートナー'],
                'location'  => ['id' => 'Online (Zoom)',         'en' => 'Online (Zoom)',          'ja' => 'オンライン (Zoom)'],
                'starts_at' => now()->subDays(7),
                'ends_at'   => now()->subDays(7)->addHours(4),
                'featured'  => false,
            ],
        ];

        foreach ($events as $i => $e) {
            Event::firstOrCreate(['slug' => $e['slug']], [
                'event_category_id' => EventCategory::where('slug', $e['category'])->value('id'),
                'title'             => $e['title'],
                'description'       => $e['description'],
                'organizer'         => $e['organizer'],
                'location'          => $e['location'],
                'starts_at'         => $e['starts_at'],
                'ends_at'           => $e['ends_at'],
                'published_at'      => now()->subDays($i + 1),
                'is_featured'       => $e['featured'],
            ]);
        }
    }
}
