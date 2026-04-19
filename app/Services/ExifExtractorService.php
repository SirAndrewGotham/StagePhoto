<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ExifExtractorService
{
    public function extract($file): array
    {
        // Get the real path
        if ($file instanceof TemporaryUploadedFile || $file instanceof UploadedFile) {
            $filePath = $file->getRealPath();
        } elseif (is_string($file)) {
            $filePath = $file;
        } else {
            return $this->getEmptyExifData();
        }

        $result = $this->getEmptyExifData();

        if (! function_exists('exif_read_data')) {
            return $result;
        }

        try {
            $exif = @exif_read_data($filePath, 'ANY_TAG', true);

            if ($exif === false) {
                return $result;
            }

            $result['raw'] = $exif;

            // Camera Make
            if (isset($exif['IFD0']['Make'])) {
                $result['camera_make'] = trim($exif['IFD0']['Make']);
            }

            // Camera Model
            if (isset($exif['IFD0']['Model'])) {
                $result['camera_model'] = trim($exif['IFD0']['Model']);
            }

            // Lens Model
            if (isset($exif['EXIF']['LensModel'])) {
                $result['lens_model'] = trim($exif['EXIF']['LensModel']);
            } elseif (isset($exif['EXIF']['UndefinedTag:0xA434'])) {
                $result['lens_model'] = trim($exif['EXIF']['UndefinedTag:0xA434']);
            }

            // Focal Length
            if (isset($exif['EXIF']['FocalLength'])) {
                $focal = $exif['EXIF']['FocalLength'];
                $result['focal_length'] = is_array($focal) ? $focal[0].'mm' : $focal.'mm';
            }

            // Aperture
            if (isset($exif['EXIF']['FNumber'])) {
                $aperture = $exif['EXIF']['FNumber'];
                $result['aperture'] = is_array($aperture) ? 'f/'.($aperture[0] / $aperture[1]) : 'f/'.$aperture;
            }

            // Shutter Speed
            if (isset($exif['EXIF']['ExposureTime'])) {
                $shutter = $exif['EXIF']['ExposureTime'];
                $result['shutter_speed'] = is_array($shutter) ? $shutter[0].'/'.$shutter[1] : $shutter;
            }

            // ISO
            if (isset($exif['EXIF']['ISOSpeedRatings'])) {
                $result['iso'] = 'ISO '.$exif['EXIF']['ISOSpeedRatings'];
            }

            // Captured Date/Time
            if (isset($exif['EXIF']['DateTimeOriginal'])) {
                $result['captured_at'] = $this->parseDateTime($exif['EXIF']['DateTimeOriginal']);
            } elseif (isset($exif['IFD0']['DateTime'])) {
                $result['captured_at'] = $this->parseDateTime($exif['IFD0']['DateTime']);
            }

            // GPS Coordinates
            if (isset($exif['GPS'])) {
                $result['gps_latitude'] = $this->convertGpsToDecimal(
                    $exif['GPS']['GPSLatitude'] ?? null,
                    $exif['GPS']['GPSLatitudeRef'] ?? 'N'
                );
                $result['gps_longitude'] = $this->convertGpsToDecimal(
                    $exif['GPS']['GPSLongitude'] ?? null,
                    $exif['GPS']['GPSLongitudeRef'] ?? 'E'
                );
            }

        } catch (\Exception $e) {
            Log::warning('EXIF extraction failed: '.$e->getMessage());
        }

        return $result;
    }

    private function getEmptyExifData(): array
    {
        return [
            'raw' => [],
            'camera_make' => null,
            'camera_model' => null,
            'lens_model' => null,
            'focal_length' => null,
            'aperture' => null,
            'shutter_speed' => null,
            'iso' => null,
            'captured_at' => null,
            'gps_latitude' => null,
            'gps_longitude' => null,
        ];
    }

    private function parseDateTime(?string $dateTime): ?string
    {
        if (! $dateTime) {
            return null;
        }

        try {
            $timestamp = \DateTime::createFromFormat('Y:m:d H:i:s', $dateTime);

            return $timestamp ? $timestamp->format('Y-m-d H:i:s') : null;
        } catch (\Exception) {
            return null;
        }
    }

    private function convertGpsToDecimal($coordinate, $reference): ?string
    {
        if (! $coordinate || ! is_array($coordinate) || count($coordinate) !== 3) {
            return null;
        }

        $degrees = count($coordinate[0]) === 2 ? $coordinate[0][0] / $coordinate[0][1] : $coordinate[0];
        $minutes = count($coordinate[1]) === 2 ? $coordinate[1][0] / $coordinate[1][1] : $coordinate[1];
        $seconds = count($coordinate[2]) === 2 ? $coordinate[2][0] / $coordinate[2][1] : $coordinate[2];

        $decimal = $degrees + ($minutes / 60) + ($seconds / 3600);

        if ($reference === 'S' || $reference === 'W') {
            $decimal = -$decimal;
        }

        return (string) round($decimal, 6);
    }
}
