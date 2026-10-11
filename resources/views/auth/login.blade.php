<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Connexion – Diarra Transport</title>
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">
<link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
<link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{--dark:#0e1621;--dark2:#162032;--dark3:#1e2d44;--gold:#c9a84c;--gold2:#e8c97a;--text:#eef2f7;--text2:#7a90a8;--border:rgba(201,168,76,.15);}
*{box-sizing:border-box;margin:0;padding:0;}
body{
  font-family:'Outfit',sans-serif;background:var(--dark);color:var(--text);
  min-height:100vh;display:flex;align-items:center;justify-content:center;
  background-image:radial-gradient(ellipse at 20% 50%, rgba(201,168,76,.06) 0%, transparent 60%),
                   radial-gradient(ellipse at 80% 20%, rgba(43,122,68,.06) 0%, transparent 50%);
}
.login-card{
  width:100%;max-width:400px;padding:40px 36px;
  background:var(--dark2);border:1px solid var(--border);
  border-radius:16px;box-shadow:0 24px 80px rgba(0,0,0,.5);
}
.login-logo{
  display:flex;flex-direction:column;align-items:center;gap:12px;
  margin-bottom:28px;
}
.logo-glow{
  filter:drop-shadow(0 0 16px rgba(201,168,76,.4));
}
.login-brand h1{
  font-size:1.3rem;font-weight:700;color:var(--gold);
  letter-spacing:.12em;text-align:center;text-transform:uppercase;
}
.login-brand p{
  font-size:.78rem;color:var(--text2);text-align:center;
  margin-top:4px;letter-spacing:.04em;
}
.login-title{
  font-size:1rem;font-weight:500;color:var(--text);
  margin-bottom:20px;text-align:center;
}
.field-group{margin-bottom:16px;position:relative;}
.field-group label{display:block;font-size:.78rem;font-weight:500;color:var(--text2);margin-bottom:6px;}
.field-group input{
  width:100%;padding:11px 42px 11px 14px;
  background:var(--dark3);border:1px solid var(--border);
  border-radius:8px;color:var(--text);font-family:inherit;font-size:.88rem;
  outline:none;transition:.2s;
}
.field-group input:focus{border-color:var(--gold);box-shadow:0 0 0 3px rgba(201,168,76,.12);}
.field-group input::placeholder{color:var(--text2);}
.field-icon{
  position:absolute;right:12px;bottom:11px;
  font-size:1rem;color:var(--text2);pointer-events:none;
}
.forgot-link{
  text-align:right;margin-bottom:22px;
  font-size:.78rem;color:var(--text2);
}
.forgot-link a{color:var(--gold);transition:.15s;}
.forgot-link a:hover{color:var(--gold2);}
.btn-valider{
  width:100%;padding:13px;
  background:linear-gradient(135deg,var(--gold),var(--gold2));
  border:none;border-radius:8px;color:#0e1621;
  font-family:inherit;font-size:.95rem;font-weight:700;
  letter-spacing:.06em;cursor:pointer;transition:.2s;
}
.btn-valider:hover{filter:brightness(1.08);transform:translateY(-1px);}
.error-msg{
  background:rgba(192,57,43,.15);border:1px solid rgba(192,57,43,.3);
  border-radius:8px;padding:10px 14px;
  color:#f87171;font-size:.8rem;margin-bottom:16px;
}
</style>
</head>
<body>
<div class="login-card">
  <div class="login-logo">
    <div class="logo-glow">
      @php
        $societe = \App\Models\Societe::first();
        $logoUrl = ($societe && $societe->logo_path)
                   ? asset('storage/' . $societe->logo_path)
                   : null;
      @endphp
      @if($logoUrl)
        <img src="{{ $logoUrl }}" width="80" height="80"
             style="border-radius:50%;object-fit:contain;"
             alt="Logo Diarra Transport">
      @else
        @include('partials.logo-svg', ['size'=>80])
      @endif
    </div>
    <div class="login-brand">
      <h1>Diarra Transport</h1>
      <p>Gestion des Bagages Accompagnés</p>
    </div>
  </div>

  <p class="login-title">Authentification</p>

  @if($errors->any())
  <div class="error-msg">{{ $errors->first() }}</div>
  @endif

  <form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="field-group">
      <label for="email">Identifiant ou Email</label>
      <input type="text" id="email" name="email" value="{{ old('email') }}"
             placeholder="Ex: admin, yacou.diakite ou email…" required autocomplete="username">
      <span class="field-icon">🔑</span>
    </div>
    <div class="field-group">
      <label for="password">Mot de Passe</label>
      <input type="password" id="password" name="password"
             placeholder="••••••••" required autocomplete="current-password">
      <span class="field-icon">🗝</span>
    </div>
    <div class="forgot-link">
      <a href="#" onclick="alert('Veuillez contacter l\'administrateur pour réinitialiser votre mot de passe.'); return false;">Mot de Passe Oublié ?</a>
    </div>
    <button type="submit" class="btn-valider">VALIDER</button>
  </form>
</div>
</body>
</html>
