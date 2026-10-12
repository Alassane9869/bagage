{{-- Logo Diarra Transport
     Priorité : 1) image uploadée en base 2) SVG par défaut
     Usage: @include('partials.logo-svg', ['size'=>36, 'class'=>'topbar-logo']) --}}
@php
  $s       = $size  ?? 40;
  $cls     = $class ?? '';
  $societe = app(\App\Models\Societe::class)::first();
  $logoUrl = ($societe && $societe->logo_path)
             ? asset('storage/' . $societe->logo_path)
             : null;
@endphp

@if($logoUrl)
  {{-- Logo uploadé --}}
  <img src="{{ $logoUrl }}"
       width="{{ $s }}" height="{{ $s }}"
       class="{{ $cls }}"
       style="flex-shrink:0;object-fit:contain;border-radius:50%;"
       alt="Logo Diarra Transport">
@else
  {{-- Logo SVG par défaut (tricolore Mali + cheval) --}}
  <svg xmlns="http://www.w3.org/2000/svg" width="{{ $s }}" height="{{ $s }}"
       viewBox="0 0 100 100" class="{{ $cls }}" style="flex-shrink:0;">
    <defs>
      <clipPath id="circleClip{{ $s }}x">
        <circle cx="50" cy="50" r="46"/>
      </clipPath>
    </defs>
    <circle cx="50" cy="50" r="49" fill="#0d5c2e"/>
    <circle cx="50" cy="50" r="46" fill="#ffffff"/>
    <rect x="4"  y="4" width="30" height="92" clip-path="url(#circleClip{{ $s }}x)" fill="#169B62"/>
    <rect x="34" y="4" width="32" height="92" clip-path="url(#circleClip{{ $s }}x)" fill="#FCD116"/>
    <rect x="66" y="4" width="30" height="92" clip-path="url(#circleClip{{ $s }}x)" fill="#CE1126"/>
    <g clip-path="url(#circleClip{{ $s }}x)">
      <ellipse cx="50" cy="56" rx="22" ry="14" fill="white" transform="rotate(-10,50,56)"/>
      <ellipse cx="72" cy="40" rx="9"  ry="7"  fill="white" transform="rotate(-20,72,40)"/>
      <polygon points="64,44 72,33 78,46 68,50" fill="white"/>
      <ellipse cx="68" cy="34" rx="5"  ry="8"  fill="white" transform="rotate(-30,68,34)"/>
      <polygon points="70,30 73,24 76,30"       fill="white"/>
      <rect x="62" y="62" width="5" height="16" rx="2" fill="white" transform="rotate(20,64,62)"/>
      <rect x="70" y="60" width="5" height="16" rx="2" fill="white" transform="rotate(35,72,60)"/>
      <rect x="30" y="62" width="5" height="14" rx="2" fill="white" transform="rotate(-20,32,62)"/>
      <rect x="38" y="64" width="5" height="14" rx="2" fill="white" transform="rotate(-10,40,64)"/>
      <path d="M29,54 Q18,48 16,56 Q18,65 26,62" fill="white"/>
      <circle cx="75" cy="38" r="1.5" fill="#169B62"/>
    </g>
    <path id="arcTop{{ $s }}x" d="M 15,50 A 35,35 0 0,1 85,50" fill="none"/>
    <text font-family="Arial Black,Arial,sans-serif" font-size="11" font-weight="900"
          fill="white" stroke="#0d5c2e" stroke-width=".5" letter-spacing="2">
      <textPath href="#arcTop{{ $s }}x" startOffset="12%">DIARRA</textPath>
    </text>
    <path id="arcBot{{ $s }}x" d="M 12,54 A 38,38 0 0,0 88,54" fill="none"/>
    <text font-family="Arial Black,Arial,sans-serif" font-size="9" font-weight="900"
          fill="white" stroke="#0d5c2e" stroke-width=".4" letter-spacing="1">
      <textPath href="#arcBot{{ $s }}x" startOffset="8%">TRANSPORT</textPath>
    </text>
  </svg>
@endif
