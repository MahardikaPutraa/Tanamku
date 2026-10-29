<?php

namespace App\Services;

use App\Models\Order;

class WhatsAppMessage
{
    /**
     * Susun pesan pemesanan yang dikirim pembeli ke WhatsApp penjual.
     */
    public static function untukOrder(Order $order): string
    {
        $baris = [];
        $baris[] = 'Halo ' . $order->store->nama_toko . ', saya ingin memesan dari TanamKu.';
        $baris[] = '';
        $baris[] = 'Kode pesanan: ' . $order->kode_pesanan;
        $baris[] = '--------------------------------';

        foreach ($order->items as $i => $item) {
            $baris[] = ($i + 1) . '. ' . $item->nama_produk;
            $baris[] = '    ' . $item->jumlah . ' x ' . $item->harga_satuan_rupiah
                . ' = ' . $item->subtotal_rupiah;
        }

        $baris[] = '--------------------------------';
        $baris[] = 'TOTAL: ' . $order->total_harga_rupiah;
        $baris[] = '';
        $baris[] = 'Nama     : ' . $order->nama_penerima;
        $baris[] = 'No HP    : ' . $order->no_hp;
        $baris[] = 'Metode   : ' . $order->metode_serah_label;
        if ($order->metode_serah === 'ambil') {
            $baris[] = 'Lokasi   : ' . $order->store->nama_toko . ' (' . $order->store->alamat . ', ' . $order->store->kota . ')';
        } else {
            $baris[] = 'Alamat   : ' . $order->alamat;
        }

        if ($order->catatan) {
            $baris[] = 'Catatan  : ' . $order->catatan;
        }

        $baris[] = '';
        $baris[] = 'Mohon konfirmasi ketersediaan stok dan total akhirnya. Terima kasih.';

        return implode("\n", $baris);
    }

    /**
     * Tautan wa.me siap buka, dengan pesan sudah terisi.
     */
    public static function url(Order $order): string
    {
        return 'https://wa.me/' . $order->store->wa_number
            . '?text=' . rawurlencode(self::untukOrder($order));
    }
}
