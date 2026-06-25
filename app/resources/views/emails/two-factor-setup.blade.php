<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; color: #333; line-height: 1.7; }
        .container { max-width: 600px; margin: 0 auto; padding: 24px; }
        .header { background: #d97706; color: #fff; padding: 16px 24px; border-radius: 4px 4px 0 0; }
        .body { background: #fafafa; padding: 24px; border: 1px solid #e5e7eb; }
        .info-table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        .info-table th { text-align: left; padding: 8px 12px; background: #f3f4f6; width: 40%; }
        .info-table td { padding: 8px 12px; }
        .warning { background: #fef3c7; border-left: 4px solid #d97706; padding: 12px 16px; margin-top: 16px; }
        .footer { color: #9ca3af; font-size: 12px; padding: 16px 0; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <strong>【セキュリティ通知】2段階認証が設定されました</strong>
    </div>
    <div class="body">
        <p>{{ $userName }} 様</p>
        <p>お使いのアカウントに2段階認証が設定されました。</p>

        <table class="info-table">
            <tr>
                <th>設定日時</th>
                <td>{{ $confirmedAt }}</td>
            </tr>
            <tr>
                <th>IPアドレス</th>
                <td>{{ $ipAddress }}</td>
            </tr>
        </table>

        <div class="warning">
            <strong>身に覚えのない場合は、直ちに管理者へご連絡ください。</strong><br>
            第三者によって設定された可能性があります。
        </div>
    </div>
    <div class="footer">
        このメールは自動送信されています。返信はできません。
    </div>
</div>
</body>
</html>
