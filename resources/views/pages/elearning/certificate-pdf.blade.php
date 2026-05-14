<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>Certificate {{ $certificate->certificate_number }}</title>
    <style>
        @page { margin: 0; }
        body {
            font-family: 'Helvetica', sans-serif;
            margin: 0;
            padding: 0;
            color: #1a1a1a;
            background: #fff;
        }
        .frame {
            position: relative;
            width: 100%;
            height: 100vh;
            padding: 60px 80px;
            box-sizing: border-box;
            border: 12px solid #b32510;
            background: linear-gradient(135deg, #fff 0%, #fdf4f3 100%);
        }
        .inner {
            border: 2px solid #b32510;
            padding: 40px 60px;
            height: calc(100% - 120px);
            position: relative;
            text-align: center;
        }
        .corner {
            position: absolute;
            color: #b32510;
            font-size: 28px;
            font-weight: bold;
        }
        .corner.tl { top: 18px; left: 24px; }
        .corner.tr { top: 18px; right: 24px; }
        .corner.bl { bottom: 18px; left: 24px; }
        .corner.br { bottom: 18px; right: 24px; }
        .brand-logo {
            font-family: 'Helvetica', sans-serif;
            font-size: 11px;
            color: #b32510;
            letter-spacing: 4px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        h1 {
            font-size: 42px;
            color: #b32510;
            margin: 10px 0 5px;
            font-weight: bold;
            letter-spacing: 2px;
        }
        .subtitle {
            font-size: 13px;
            color: #555;
            letter-spacing: 6px;
            text-transform: uppercase;
            margin-bottom: 35px;
        }
        .label {
            font-size: 11px;
            color: #777;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin: 18px 0 8px;
        }
        .name {
            font-size: 36px;
            font-weight: bold;
            color: #1a1a1a;
            margin: 5px 0 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 15px;
            display: inline-block;
            min-width: 60%;
        }
        .course-title {
            font-size: 22px;
            color: #b32510;
            font-weight: bold;
            margin: 12px 0;
        }
        .footer {
            position: absolute;
            bottom: 30px;
            left: 60px;
            right: 60px;
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #555;
        }
        .footer-block { text-align: left; }
        .footer-block.right { text-align: right; }
        .footer-block strong { display: block; color: #1a1a1a; font-size: 13px; margin-top: 2px; }
        .footer-block .num { font-family: 'Courier New', monospace; }
        .score {
            font-size: 14px;
            color: #1a7f37;
            margin-top: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="frame">
        <div class="inner">
            <span class="corner tl">★</span>
            <span class="corner tr">★</span>
            <span class="corner bl">★</span>
            <span class="corner br">★</span>

            <div class="brand-logo">PASSION JAPAN INDONESIA</div>
            <h1>CERTIFICATE</h1>
            <div class="subtitle">OF COMPLETION</div>

            <div class="label">This is to certify that</div>
            <div class="name">{{ $certificate->user->name }}</div>

            <div class="label">has successfully completed the course</div>
            <div class="course-title">{{ $certificate->course->t('title') }}</div>

            @if($certificate->final_score)
                <div class="score">Final score: {{ $certificate->final_score }}%</div>
            @endif

            <div class="footer">
                <div class="footer-block">
                    Issued on
                    <strong>{{ $certificate->issued_at->format('d F Y') }}</strong>
                </div>
                <div class="footer-block right">
                    Certificate number
                    <strong class="num">{{ $certificate->certificate_number }}</strong>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
