{{-- Logo Diarra Transport
     Priorité : 1) image uploadée en base 2) SVG par défaut
     Usage: @include('partials.logo-svg', ['size'=>36, 'class'=>'topbar-logo']) --}}
@php
  $s       = $size  ?? 40;
  $cls     = $class ?? '';
  $societe = app(\App\Models\Societe::class)::first();
  $path    = ($societe && $societe->logo_path) ? $societe->logo_path : 'logos/logo_societe_1782763691.png';
  $logoUrl = asset('storage/' . $path);
@endphp

<img src="{{ $logoUrl }}"
     width="{{ $s }}" height="{{ $s }}"
     class="{{ $cls }}"
     style="flex-shrink:0;object-fit:cover;border-radius:50%;"
     alt="Logo Diarra Transport">
