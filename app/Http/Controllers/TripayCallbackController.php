<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\Tagihan;

class TripayCallbackController extends Controller
{
    // Isi dengan private key anda
    protected $privateKey = 'bIUJE-9OykS-d0L1c-cmIiW-axufz';

    public function handle(Request $request)
    {
        $callbackSignature = $request->server('HTTP_X_CALLBACK_SIGNATURE');
        $json = $request->getContent();
        $signature = hash_hmac('sha256', $json, $this->privateKey);

        if ($signature !== (string) $callbackSignature) {
            return Response::json([
                'success' => false,
                'message' => 'Invalid signature',
            ]);
        }

        if ('payment_status' !== (string) $request->server('HTTP_X_CALLBACK_EVENT')) {
            return Response::json([
                'success' => false,
                'message' => 'Unrecognized callback event, no action was taken',
            ]);
        }

        $data = json_decode($json);
        if (JSON_ERROR_NONE !== json_last_error()) {
            return Response::json([
                'success' => false,
                'message' => 'Invalid data sent by Tripay',
            ]);
        }

        $reference = $data->reference;
        $status = strtoupper((string) $data->status);

        // Konversi status PAID dan UNPAID ke LS dan BL
        $databaseStatus = null;
        switch ($status) {
            case 'PAID':
                $databaseStatus = 'LS';
                break;
            case 'UNPAID':
                $databaseStatus = 'BL';
                break;
            default:
                return Response::json([
                    'success' => false,
                    'message' => 'Unrecognized payment status',
                ]);
        }

        if ($data->is_closed_payment === 1) {
            $tagihan = Tagihan::where('reference', $reference)
                ->where('status', '=', 'BL') // Menggunakan status 'BL' untuk UNPAID di database
                ->first();

            if (!$tagihan) {
                return Response::json([
                    'success' => false,
                    'message' => 'No invoice found or already paid: ' . $reference,
                ]);
            }

            // Update status di database sesuai dengan status yang telah dikonversi
            $tagihan->update([
                'status' => $databaseStatus,
                'tgl_bayar' => now(),
                'pembayaran_via' => 'online',
            ]);

            return Response::json(['success' => true]);
        }
    }
}

