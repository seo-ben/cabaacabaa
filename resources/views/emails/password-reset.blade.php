<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de votre mot de passe — {{ config('app.name') }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f4f4f5; padding: 40px 16px; }
        .wrapper { max-width: 600px; margin: 0 auto; }
        .card { background: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #ff4d00, #ea580c); padding: 40px 40px 50px; text-align: center; position: relative; }
        .header::after { content: ''; position: absolute; bottom: -24px; left: 0; right: 0; height: 48px; background: #ffffff; border-radius: 50% 50% 0 0 / 100% 100% 0 0; }
        .logo-icon { display: inline-flex; width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 16px; align-items: center; justify-content: center; margin-bottom: 16px; }
        .brand { font-size: 24px; font-weight: 900; color: #ffffff; letter-spacing: -0.5px; }
        .tagline { font-size: 13px; color: rgba(255,255,255,0.7); margin-top: 4px; }
        .body { padding: 48px 40px 40px; }
        .greeting { font-size: 22px; font-weight: 800; color: #111827; margin-bottom: 12px; }
        .text { font-size: 15px; color: #6b7280; line-height: 1.7; margin-bottom: 20px; }
        .btn-wrap { text-align: center; margin: 32px 0; }
        .btn { display: inline-block; background: linear-gradient(135deg, #ff4d00, #ea580c); color: #ffffff; text-decoration: none; font-weight: 800; font-size: 14px; padding: 16px 40px; border-radius: 16px; letter-spacing: 0.03em; box-shadow: 0 8px 24px rgba(255,77,0,0.3); }
        .expiry { background: #fff7ed; border: 1px solid #fed7aa; border-radius: 12px; padding: 12px 16px; font-size: 13px; color: #92400e; font-weight: 600; margin: 20px 0; }
        .link-fallback { background: #f9fafb; border-radius: 12px; padding: 16px; margin-top: 20px; }
        .link-fallback p { font-size: 12px; color: #9ca3af; font-weight: 600; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em; }
        .link-fallback a { font-size: 12px; color: #6b7280; word-break: break-all; }
        .divider { height: 1px; background: #f3f4f6; margin: 32px 0; }
        .footer { padding: 24px 40px 32px; text-align: center; }
        .footer p { font-size: 12px; color: #9ca3af; line-height: 1.6; }
        .footer a { color: #6b7280; text-decoration: none; }
        .security-note { margin-top: 24px; padding: 16px; background: #fef3c7; border-radius: 12px; }
        .security-note p { font-size: 12px; color: #92400e; font-weight: 600; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="header">
                <div class="logo-icon">
                    <svg width="28" height="28" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div class="brand">{{ config('app.name') }}</div>
                <div class="tagline">Plateforme de commande en ligne</div>
            </div>

            <div class="body">
                <h1 class="greeting">Bonjour {{ $user->name }} 👋</h1>
                <p class="text">Vous avez demandé la réinitialisation de votre mot de passe pour votre compte <strong>{{ config('app.name') }}</strong>. Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe :</p>

                <div class="btn-wrap">
                    <a href="{{ $resetUrl }}" class="btn">🔐 Réinitialiser mon mot de passe</a>
                </div>

                <div class="expiry">
                    ⏱️ Ce lien est valide pendant <strong>60 minutes</strong> seulement. Après ce délai, vous devrez faire une nouvelle demande.
                </div>

                <div class="security-note">
                    <p>🛡️ Si vous n'êtes pas à l'origine de cette demande, ignorez simplement cet email. Votre mot de passe ne sera pas modifié.</p>
                </div>

                <div class="link-fallback">
                    <p>Ou copiez ce lien dans votre navigateur :</p>
                    <a href="{{ $resetUrl }}">{{ $resetUrl }}</a>
                </div>
            </div>

            <div class="divider"></div>

            <div class="footer">
                <p>
                    Cet email a été envoyé automatiquement par <strong>{{ config('app.name') }}</strong>.<br>
                    Ne répondez pas à cet email, il n'est pas surveillé.<br><br>
                    © {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
