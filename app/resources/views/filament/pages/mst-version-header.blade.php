<div style="background: #1a2e1a; border: 1px solid #22c55e; border-radius: 12px; padding: 24px 28px; margin-bottom: 16px; display: flex; align-items: center; gap: 20px;">
    <div style="background: #22c55e; border-radius: 8px; padding: 8px 16px;">
        <span style="font-size: 13px; font-weight: 600; color: white;">適用中</span>
    </div>
    @if($activeVersion)
    <div>
        <p style="font-size: 28px; font-weight: 700; color: #22c55e; margin: 0;">
            バージョン {{ $activeVersion->version }}
        </p>
        <p style="font-size: 13px; color: #ffffff; margin: 6px 0 0;">
            有効化日時：{{ $activeVersion->activated_at?->format('Y/m/d H:i') ?? $activeVersion->uploaded_at?->format('Y/m/d H:i') }}
        </p>
    </div>
    @else
    <p style="color: #6b7280; margin: 0; font-size: 16px;">適用中のバージョンはありません</p>
    @endif
</div>