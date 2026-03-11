<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: sans-serif;
            background: #f4f4f4;
            padding: 40px;
        }
        .container {
            background: #fff;
            max-width: 500px;
            margin: 0 auto;
            padding: 40px;
            border-radius: 8px;
        }
        .button {
            display: inline-block;
            background: #4a90e2;
            color: #fff;
            padding: 12px 32px;
            border-radius: 4px;
            text-decoration: none;
            margin-top: 24px;
        }
        .expire {
            color: #888;
            font-size: 12px;
            margin-top: 16px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>会員登録のご案内</h2>
        <p>以下のボタンから会員登録を完了してください。</p>
        <a href="{{ config('app.frontend_url') }}/register-form?token={{ $token }}&email={{ urlencode($email) }}" class="button">
            会員登録を完了する
        </a>
        <p class="expire">このリンクは30分間有効です。</p>
        <p class="expire">心当たりがない場合は無視してください。</p>
    </div>
</body>
</html>