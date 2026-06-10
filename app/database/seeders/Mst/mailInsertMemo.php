INSERT INTO `mail_templates` (`template_key`, `template_name`, `subject`, `body`) VALUES
('chat_invited', 'チャット招待通知', '【チャット招待】{{dealer_name}}からチャットへの招待が届いています',
'<p>{{user_name}} 様</p>
<p>{{dealer_name}}からチャットへの招待が届いています。</p>
<p>以下のURLから参加・拒否の選択をお願いします。</p>
<p><a href="{{url}}">招待を確認する</a></p>
<p>※この招待は72時間有効です。</p>
<p>心当たりがない場合は無視してください。</p>'),

('chat_invite_rejected', 'チャット招待拒否通知（ディーラー向け）', '【チャット】招待が拒否されました',
'<p>{{dealer_name}} 担当者様</p>
<p>チャットへの招待が拒否されました。</p>
<p>■ ルーム名：{{room_name}}</p>
<p>■ 拒否日時：{{rejected_at}}</p>
<p>直接お客様にご連絡ください。</p>'),

('chat_invite_expired', 'チャット招待期限切れ通知（ディーラー向け）', '【チャット】招待の有効期限が切れました',
'<p>{{dealer_name}} 担当者様</p>
<p>チャットへの招待が期限切れになりました。</p>
<p>■ ルーム名：{{room_name}}</p>
<p>■ 期限切れ日時：{{expired_at}}</p>
<p>必要な場合は再度招待してください。</p>');