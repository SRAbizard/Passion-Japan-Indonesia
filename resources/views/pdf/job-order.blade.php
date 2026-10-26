{{-- "Terjemahan Job Order" PDF (mPDF). Layout follows the agency's
     Word template: logo top-left, centred title, then one two-column
     table per section with green labels. Data: App\Support\JobOrder. --}}
<html>
<head>
<style>
    body { font-family: freeserif; font-size: 11pt; color: #111; }
    h1 { font-size: 12.5pt; font-weight: bold; text-align: center; margin: 0 0 14pt; }
    h2 { font-size: 11.5pt; font-weight: bold; margin: 14pt 0 6pt; }
    table.jo { width: 100%; border-collapse: collapse; page-break-inside: auto; }
    table.jo td { border: 0.6pt solid #777; padding: 6pt 7pt; vertical-align: middle; line-height: 1.35; }
    table.jo td.label { width: 24%; color: #538135; font-weight: bold; }
    ul { margin: 0; padding-left: 16pt; }
    li { margin: 0; }
    p.line { margin: 0; }
</style>
</head>
<body>
    <h1>{{ $title }}</h1>

    @foreach($sections as $section)
        <h2>{{ $section['title'] }} <span style="font-family: bizud; font-size: 8pt; font-weight: normal; color: #8a8a8a;">{{ $section['jp'] }}</span></h2>
        <table class="jo">
            @foreach($section['rows'] as $row)
                <tr>
                    <td class="label">{{ $row['label'] }}<br><span style="font-family: bizud; font-size: 7pt; font-weight: normal; color: #8a8a8a;">{{ $row['jp'] }}</span></td>
                    <td>
                        @if(is_array($row['value']))
                            <ul>@foreach($row['value'] as $item)<li>{{ $item }}</li>@endforeach</ul>
                        @else
                            @php($l = \App\Support\JobOrder::lines($row['value']))
                            @foreach($l['text'] as $line)<p class="line">{{ $line }}</p>@endforeach
                            @if($l['bullets'])
                                <ul>@foreach($l['bullets'] as $item)<li>{{ $item }}</li>@endforeach</ul>
                            @endif
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    @endforeach
</body>
</html>
