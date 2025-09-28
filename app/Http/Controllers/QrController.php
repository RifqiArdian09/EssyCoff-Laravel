<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrController extends Controller
{
    public function table(Request $request, string $code, ?string $format = 'png')
    {
        $size = (int) $request->query('size', 160);
        $margin = (int) $request->query('margin', 1);
        $forceDownload = (bool) $request->boolean('download', false);

        // Data encoded: pretty route for customer order by table code
        $data = route('customer.table', ['code' => $code]);

        $format = in_array(strtolower((string) $format), ['png', 'svg', 'eps']) ? strtolower((string) $format) : 'png';

        // Generate QR with graceful fallback if PNG renderer unavailable
        try {
            $qr = QrCode::format($format)
                ->margin($margin)
                ->size($size)
                ->generate($data);
        } catch (\Throwable $e) {
            // Fallback to SVG if PNG/EPS generation fails (e.g., missing GD/Imagick)
            $format = 'svg';
            $qr = QrCode::format($format)
                ->margin($margin)
                ->size($size)
                ->generate($data);
        }

        $mime = match ($format) {
            'svg' => 'image/svg+xml',
            'eps' => 'application/postscript',
            default => 'image/png',
        };

        $response = response($qr, 200)->header('Content-Type', $mime);
        if ($forceDownload) {
            $ext = $format === 'svg' ? 'svg' : ($format === 'eps' ? 'eps' : 'png');
            $filename = "QR-{$code}.{$ext}";
            $response->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        }
        return $response;
    }
}
