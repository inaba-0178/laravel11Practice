<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; color: #333; line-height: 1.7; }
        .container { max-width: 600px; margin: 0 auto; padding: 24px; }
        .header { background: #2563eb; color: #fff; padding: 16px 24px; border-radius: 4px 4px 0 0; }
        .body { background: #fafafa; padding: 24px; border: 1px solid #e5e7eb; }
        .info-table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        .info-table th { text-align: left; padding: 8px 12px; background: #f3f4f6; width: 40%; }
        .info-table td { padding: 8px 12px; word-break: break-all; }
        .warning { background: #fee2e2; border-left: 4px solid #dc2626; padding: 12px 16px; margin-top: 16px; }
        .footer { color: #9ca3af; font-size: 12px; padding: 16px 0; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <strong>【セキュリティ通知】管理画面へのログインがありました</strong>
    </div>
    <div class="body">
        <p>{{ $userName }} 様</p>
        <p>管理画面へのログインが確認されました。</p>

        <table class="info-table">
            <tr>
                <th>ログイン日時</th>
                <td>{{ $loginAt }}</td>
            </tr>
            <tr>
                <th>IPアドレス</th>
                <td>{{ $ipAddress }}</td>
            </tr>
            <tr>
                <th>ブラウザ情報</th>
                <td>{{ $userAgent }}</td>
            </tr>
        </table>

        <div class="warning">
            <strong>身に覚えのない場合は、直ちにパスワードを変更し、管理者へご連絡ください。</strong>
        </div>
    </div>
    <div class="footer">
        このメールは自動送信されています。返信はできません。
    </div>
</div>
</body>
</html>
