<?php

namespace App\Services;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\Result\ResultInterface;
use Illuminate\Support\Facades\File;

class QrCodeService
{
    public const PUBLIC_PATH = 'marketing/website-qr.png';

    public function __construct(private SettingService $settings) {}

    public function scanUrl(): string
    {
        $base = rtrim($this->settings->qrScanBaseUrl(), '/');

        return $base.'/visit';
    }

    public function publicFilePath(): string
    {
        return public_path(self::PUBLIC_PATH);
    }

    public function stableFileUrl(): string
    {
        return rtrim($this->settings->qrScanBaseUrl(), '/').'/'.self::PUBLIC_PATH;
    }

    public function publicUrl(): string
    {
        return $this->stableFileUrl().'?v='.$this->settings->get('qr_generated_at', '1');
    }

    public function generateAndStore(): string
    {
        File::ensureDirectoryExists(dirname($this->publicFilePath()));

        $result = $this->buildQr($this->scanUrl());
        $result->saveToFile($this->publicFilePath());

        $this->settings->set('qr_generated_at', (string) time());

        return $this->publicFilePath();
    }

    public function ensureExists(): void
    {
        if (! is_file($this->publicFilePath())) {
            $this->generateAndStore();
        }
    }

    protected function buildQr(string $url): ResultInterface
    {
        $qr = new QrCode(
            data: $url,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 900,
            margin: 20,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(53, 94, 59),
            backgroundColor: new Color(255, 255, 255),
        );

        $writer = new PngWriter();
        $logoPath = $this->resolveLogoPath();

        if ($logoPath) {
            $logo = Logo::create($logoPath)
                ->setResizeToWidth(160)
                ->setResizeToHeight(160)
                ->setPunchoutBackground(true);

            return $writer->write($qr, $logo);
        }

        return $writer->write($qr);
    }

    protected function resolveLogoPath(): ?string
    {
        $candidates = [
            $this->settings->get('logo_path'),
            $this->settings->get('favicon_path'),
            'images/logo.png',
        ];

        foreach ($candidates as $path) {
            if (! $path) {
                continue;
            }

            $full = public_path(ltrim($path, '/'));

            if (is_file($full)) {
                return $full;
            }
        }

        return null;
    }
}
