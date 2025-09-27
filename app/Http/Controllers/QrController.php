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

        // Data encoded: pretty route for customer order by table code
        $data = route('customer.table', ['code' => $code]);

        $format = in_array(strtolower((string) $format), ['png', 'svg', 'eps']) ? strtolower((string) $format) : 'png';

        $qr = QrCode::format($format)
            ->margin($margin)
            ->size($size)
            ->generate($data);

        $mime = match ($format) {
            'svg' => 'image/svg+xml',
            'eps' => 'application/postscript',
            default => 'image/png',
        };

        return response($qr, 200)->header('Content-Type', $mime);
    }
}
