<?php

namespace App\Services;

class VideoCompressionService
{
    private string $ffmpegPath;

public function __construct()
{
    // Auto detect: Linux pakai which, Windows pakai path hardcode
    $this->ffmpegPath = PHP_OS_FAMILY === 'Windows'
        ? 'C:\\ffmpeg\\bin\\ffmpeg.exe'
        : trim(shell_exec('which ffmpeg') ?? '/usr/bin/ffmpeg');
}

    /**
     * Compress video pakai shell command FFmpeg langsung
     */
    public function compress(string $inputPath): string
    {
        $outputPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('compressed_') . '.mp4';

        // Command FFmpeg: compress ke 720p, CRF 28, audio 128kbps
        $command = sprintf(
            '"%s" -i "%s" -vcodec libx264 -crf 28 -preset fast -vf "scale=trunc(iw/2)*2:trunc(ih/2)*2" -acodec aac -b:a 128k -y "%s" 2>&1',
            $this->ffmpegPath,
            $inputPath,
            $outputPath
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0 || !file_exists($outputPath)) {
            // Kalau compress gagal, return path original — tetap upload tanpa compress
            return $inputPath;
        }

        return $outputPath;
    }

    public function isVideo(string $mimeType): bool
    {
        return str_starts_with($mimeType, 'video/');
    }
}