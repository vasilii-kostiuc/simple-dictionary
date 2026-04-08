<?php

namespace App\Domain\Match\Services;

class MatchInviteQrCodeGenerator
{
    public function generate(string $url): string
    {
        $imageUrl = 'https://api.qrserver.com/v1/create-qr-code/?format=svg&size=280x280&data='.rawurlencode($url);
        $escapedUrl = htmlspecialchars($url, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 340" role="img" aria-label="Match invite QR code">
    <rect width="320" height="340" fill="#ffffff"/>
    <rect x="19" y="19" width="282" height="282" rx="16" fill="#f8fafc" stroke="#cbd5e1" stroke-width="2"/>
    <image href="{$imageUrl}" x="20" y="20" width="280" height="280" preserveAspectRatio="xMidYMid meet"/>
    <text x="160" y="318" text-anchor="middle" font-size="10" font-family="Arial, sans-serif" fill="#334155">Scan or open the invite link</text>
    <desc>{$escapedUrl}</desc>
</svg>
SVG;
    }
}
