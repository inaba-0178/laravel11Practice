<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; color: #333; line-height: 1.7; }
        .container { max-width: 600px; margin: 0 auto; padding: 24px; }
        .header { background: #dc2626; color: #fff; padding: 16px 24px; border-radius: 4px 4px 0 0; }
        .body { background: #fafafa; padding: 24px; border: 1px solid #e5e7eb; }
        .steps { background: #f0fdf4; border-left: 4px solid #16a34a; padding: 12px 16px; margin: 16px 0; }
        .steps ol { margin: 8px 0; padding-left: 20px; }
        .btn { display: inline-block; background: #2563eb; color: #fff; padding: 10px 24px; border-radius: 4px; text-decoration: none; margin-top: 16px; }
        .footer { color: #9ca3af; font-size: 12px; padding: 16px 0; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <strong>【重要】2段階認証の設定をお願いします</strong>
    </div>
    <div class="body">
        <p>{{ $userName }} 様</p>
        <p>管理画面のセキュリティ強化のため、<strong>2段階認証（2FA）</strong>の設定をお願いしています。<br>
        まだ設定が完了していないアカウントには、引き続きご対応をお願いします。</p>

        <div class="steps">
            <strong>設定手順</strong>
            <ol>
                <li>管理画面にログインする</li>
                <li>画面の案内に従い、認証アプリ（Google Authenticator など）でQRコードを読み取る</li>
                <li>表示された6桁のコードを入力して設定を完了する</li>
            </ol>
        </div>

        <p>設定が完了するまで、ログイン後に毎回設定画面が表示されます。</p>

        <a href="{{ $adminUrl }}" class="btn">管理画面へ</a>
    </div>
    <div class="footer">
        このメールは自動送信されています。返信はできません。
    </div>
</div>
</body>
</html>
