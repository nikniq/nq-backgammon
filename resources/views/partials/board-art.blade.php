{{-- Decorative static board for marketing sections --}}
<svg viewBox="0 0 540 400" class="board-art" role="img" aria-label="Backgammon board">
    <defs>
        <linearGradient id="ba-wood" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#7a4a2a"/><stop offset="1" stop-color="#4a2a16"/></linearGradient>
        <radialGradient id="ba-felt" cx=".5" cy=".5" r=".75"><stop offset="0" stop-color="#2f6b45"/><stop offset="1" stop-color="#173a26"/></radialGradient>
        <radialGradient id="ba-w" cx=".35" cy=".3" r=".8"><stop offset="0" stop-color="#fff"/><stop offset=".6" stop-color="#efe6d4"/><stop offset="1" stop-color="#c9bba0"/></radialGradient>
        <radialGradient id="ba-b" cx=".35" cy=".3" r=".8"><stop offset="0" stop-color="#7a2a20"/><stop offset=".6" stop-color="#4a1410"/><stop offset="1" stop-color="#220806"/></radialGradient>
    </defs>
    <rect width="540" height="400" rx="18" fill="url(#ba-wood)"/>
    <rect x="18" y="18" width="232" height="364" rx="4" fill="url(#ba-felt)"/>
    <rect x="290" y="18" width="232" height="364" rx="4" fill="url(#ba-felt)"/>
    @for ($i = 0; $i < 12; $i++)
        @php($x = ($i < 6 ? 18 : 290) + ($i % 6) * 38.67)
        <polygon points="{{ $x+2 }},18 {{ $x+36.67 }},18 {{ $x+19.3 }},170" fill="{{ $i % 2 ? '#e9d3a6' : '#a83a26' }}"/>
        <polygon points="{{ $x+2 }},382 {{ $x+36.67 }},382 {{ $x+19.3 }},230" fill="{{ $i % 2 ? '#a83a26' : '#e9d3a6' }}"/>
    @endfor
    @php($stacks = [[0,'t',5,'b'],[4,'t',3,'w'],[6,'t',5,'w'],[11,'t',2,'b'],[0,'b',5,'w'],[4,'b',3,'b'],[6,'b',5,'b'],[11,'b',2,'w']])
    @foreach ($stacks as [$col, $side, $n, $c])
        @php($cx = ($col < 6 ? 18 : 290) + ($col % 6) * 38.67 + 19.3)
        @for ($k = 0; $k < $n; $k++)
            @php($cy = $side === 't' ? 36 + $k * 34 : 364 - $k * 34)
            <circle cx="{{ $cx }}" cy="{{ $cy }}" r="16.5" fill="url(#ba-{{ $c }})" stroke="rgba(0,0,0,.35)"/>
            <circle cx="{{ $cx }}" cy="{{ $cy }}" r="10" fill="none" stroke="{{ $c === 'w' ? 'rgba(0,0,0,.12)' : 'rgba(255,255,255,.15)' }}"/>
        @endfor
    @endforeach
    <g transform="translate(372 182) rotate(-12)"><rect width="36" height="36" rx="7" fill="#fbf7ef" stroke="#bfb39c"/><circle cx="10" cy="10" r="3.4" fill="#222"/><circle cx="18" cy="18" r="3.4" fill="#222"/><circle cx="26" cy="26" r="3.4" fill="#222"/></g>
    <g transform="translate(420 186) rotate(9)"><rect width="36" height="36" rx="7" fill="#fbf7ef" stroke="#bfb39c"/><circle cx="10" cy="10" r="3.4" fill="#222"/><circle cx="26" cy="10" r="3.4" fill="#222"/><circle cx="10" cy="26" r="3.4" fill="#222"/><circle cx="26" cy="26" r="3.4" fill="#222"/></g>
</svg>
