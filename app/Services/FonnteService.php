<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    /**
     * Kirim pesan WhatsApp via Fonnte API.
     *
     * @param  string  $nomor  Nomor tujuan (08xx / 62xx / +62xx)
     * @param  string  $pesan  Isi pesan yang dikirim
     * @return bool   true jika berhasil, false jika gagal
     */
    public function kirimWa(string $nomor, string $pesan): bool
    {
        $token = config('services.fonnte.token');

        if (empty($token)) {
            Log::warning('[Fonnte] FONNTE_TOKEN belum diisi di .env — pesan tidak dikirim.', [
                'nomor' => $nomor,
            ]);
            return false;
        }

        // Normalisasi nomor: 08xx → 628xx
        $nomor = preg_replace('/^\+/', '', $nomor);  // hapus +
        if (str_starts_with($nomor, '08')) {
            $nomor = '62' . substr($nomor, 1);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->timeout(15)->post('https://api.fonnte.com/send', [
                'target'  => $nomor,
                'message' => $pesan,
            ]);

            $body = $response->json();

            if ($response->successful() && ($body['status'] ?? false)) {
                Log::info('[Fonnte] Pesan WA berhasil dikirim.', ['nomor' => $nomor]);
                return true;
            }

            Log::error('[Fonnte] Gagal kirim WA.', [
                'nomor'   => $nomor,
                'status'  => $response->status(),
                'response' => $body,
            ]);
        } catch (\Throwable $e) {
            Log::error('[Fonnte] Exception saat kirim WA: ' . $e->getMessage(), [
                'nomor' => $nomor,
            ]);
        }

        return false;
    }
}

