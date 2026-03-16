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
        h2 {
            font-size: 20px;
            color: #333;
            margin-bottom: 16px;
        }
        p {
            font-size: 14px;
            color: #555;
            line-height: 1.7;
            margin-bottom: 12px;
        }
        .button {
            display: inline-block;
            background: #4a90e2;
            color: #fff;
            padding: 12px 32px;
            border-radius: 4px;
            text-decoration: none;
            margin-top: 24px;
            font-size: 14px;
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
        {!! $body !!}
    </div>
</body>
</html>