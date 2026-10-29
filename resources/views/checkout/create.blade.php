@extends('layouts.app')

@section('title', 'Checkout — ' . $store->nama_toko)

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        @include('partials.alert')

        <nav class="text-xs text-gray-500 mb-5">
            <a href="{{ route('carts.index') }}" class="hover:text-primary">Keranjang</a>
            <span class="mx-1.5">/</span>
            <span class="text-gray-800 font-medium">Checkout</span>
        </nav>

        <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Checkout ke {{ $store->nama_toko }}</h1>
        <p class="mt-1 text-sm text-gray-600">
            Pesanan akan dicatat, lalu pesan WhatsApp dibuat otomatis untuk penjual.
        </p>

        @php
            $tersediaKirim = $store->metode_serah === 'keduanya' || $store->metode_serah === 'kirim';
            $tersediaAmbil = $store->metode_serah === 'keduanya' || $store->metode_serah === 'ambil';
            $defaultMetode = old('metode_serah', $store->metode_serah === 'ambil' ? 'ambil' : 'kirim');
            $isAmbilAwal = $defaultMetode === 'ambil';
        @endphp

        {{-- ALUR CHECKOUT STEPPER --}}
        <div class="mt-6 p-4 rounded-xl bg-white border border-gray-100 shadow-xs">
            <div class="grid grid-cols-3 gap-2 text-center text-xs">
                <div class="flex items-center gap-2 justify-center text-emerald-700 font-semibold">
                    <span class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center text-xs">✓</span>
                    <span>1. Keranjang</span>
                </div>
                <div class="flex items-center gap-2 justify-center text-primary font-bold">
                    <span class="w-6 h-6 rounded-full bg-primary text-white flex items-center justify-center text-xs">2</span>
                    <span id="stepper-step2-text">{{ $isAmbilAwal ? '2. Pengambilan di Toko' : '2. Data Pengiriman' }}</span>
                </div>
                <div class="flex items-center gap-2 justify-center text-gray-400 font-medium">
                    <span class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center text-xs">3</span>
                    <span>3. Chat WhatsApp</span>
                </div>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 lg:grid-cols-5 gap-6">
            {{-- FORM --}}
            <div class="lg:col-span-3">
                <form method="POST" action="{{ route('checkout.store', $store) }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
                    @csrf

                    {{-- PILIHAN METODE PENYERAHAN --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="block text-xs font-bold uppercase tracking-wider text-gray-700">Pilih Metode Penyerahan</span>
                            <span class="text-[11px] text-gray-500">Toko melayani: {{ $store->metode_serah_label }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            {{-- Opsi Kirim --}}
                            <label class="cursor-pointer {{ $tersediaKirim ? '' : 'opacity-40 pointer-events-none' }}">
                                <input type="radio" name="metode_serah" value="kirim" class="peer sr-only"
                                       @checked($defaultMetode === 'kirim')
                                       @disabled(! $tersediaKirim)>
                                <div class="rounded-xl border-2 border-gray-200 p-3.5 text-center transition-all peer-checked:border-primary peer-checked:bg-emerald-50/50">
                                    <div class="w-8 h-8 mx-auto rounded-full bg-gray-100 peer-checked:bg-primary-light flex items-center justify-center text-gray-700 mb-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                                        </svg>
                                    </div>
                                    <span class="block text-sm font-bold text-gray-800">Dikirim</span>
                                    <span class="block text-[11px] text-gray-500 mt-0.5">Kirim ke alamat tujuan</span>
                                </div>
                            </label>

                            {{-- Opsi Ambil di Toko --}}
                            <label class="cursor-pointer {{ $tersediaAmbil ? '' : 'opacity-40 pointer-events-none' }}">
                                <input type="radio" name="metode_serah" value="ambil" class="peer sr-only"
                                       @checked($defaultMetode === 'ambil')
                                       @disabled(! $tersediaAmbil)>
                                <div class="rounded-xl border-2 border-gray-200 p-3.5 text-center transition-all peer-checked:border-primary peer-checked:bg-emerald-50/50">
                                    <div class="w-8 h-8 mx-auto rounded-full bg-gray-100 peer-checked:bg-primary-light flex items-center justify-center text-gray-700 mb-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    <span class="block text-sm font-bold text-gray-800">Ambil di toko</span>
                                    <span class="block text-[11px] text-gray-500 mt-0.5">Ambil langsung ke toko</span>
                                </div>
                            </label>
                        </div>
                        @error('metode_serah')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    {{-- BOX INFORMASI LOKASI TOKO (HANYA MUNCUL SAAT "AMBIL DI TOKO") --}}
                    <div id="box-alamat-toko" class="{{ $isAmbilAwal ? '' : 'hidden' }} p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-gray-800 transition-all">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-lg bg-emerald-600 text-white flex items-center justify-center shrink-0 mt-0.5 shadow-xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-emerald-800">Lokasi Pengambilan (Alamat Toko)</span>
                                    <span class="text-[10px] font-bold bg-emerald-200 text-emerald-900 px-2 py-0.5 rounded-full">Bebas Ongkir</span>
                                </div>
                                <h3 class="text-base font-bold text-gray-900 mt-1">{{ $store->nama_toko }}</h3>
                                <p class="text-xs text-gray-700 mt-1 leading-relaxed">{{ $store->alamat }}</p>
                                <p class="text-xs font-semibold text-emerald-900 mt-1">Kota: {{ $store->kota }}</p>

                                <div class="mt-3 pt-2.5 border-t border-emerald-200/70 flex flex-wrap items-center gap-3 text-xs text-gray-800">
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.9 9.9 0 004.79 1.22h.01c5.46 0 9.91-4.45 9.91-9.91C21.96 6.45 17.5 2 12.04 2z"/>
                                        </svg>
                                        No. WhatsApp Toko: <strong class="text-emerald-900">{{ $store->no_wa }}</strong>
                                    </span>
                                </div>
                                <div class="mt-2.5 p-2 rounded-lg bg-white/80 border border-emerald-100 text-[11px] text-emerald-900 leading-snug">
                                    📍 <strong>Panduan:</strong> Anda tidak perlu mengisi alamat rumah. Cukup konfirmasi pesanan lewat WhatsApp dan sepakati jadwal pengambilan dengan penjual.
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- NAMA --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="nama_penerima" id="label-nama-penerima" class="block text-xs font-semibold text-gray-700">
                                {{ $isAmbilAwal ? 'Nama Pengambil' : 'Nama Penerima' }}
                            </label>
                            <span id="hint-nama-penerima" class="text-[11px] text-gray-400">
                                {{ $isAmbilAwal ? 'Orang yang akan mengambil tanaman di toko' : 'Nama penerima paket pesanan' }}
                            </span>
                        </div>
                        <input type="text" id="nama_penerima" name="nama_penerima" required maxlength="100"
                               value="{{ old('nama_penerima', $user->name) }}"
                               class="w-full px-3 py-2.5 rounded-lg border text-sm outline-none transition
                                      @error('nama_penerima') border-red-400 @else border-gray-300 focus:border-primary focus:ring-primary @enderror">
                        @error('nama_penerima')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    {{-- NOMOR HP / WHATSAPP --}}
                    <div>
                        <label for="no_hp" class="block text-xs font-semibold text-gray-700 mb-1.5">Nomor HP / WhatsApp Aktif</label>
                        <input type="text" id="no_hp" name="no_hp" required maxlength="20" placeholder="08xxxxxxxxxx"
                               value="{{ old('no_hp', $user->no_hp) }}"
                               class="w-full px-3 py-2.5 rounded-lg border text-sm outline-none transition
                                      @error('no_hp') border-red-400 @else border-gray-300 focus:border-primary focus:ring-primary @enderror">
                        @error('no_hp')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    {{-- ALAMAT PENGIRIMAN (HANYA DITAMPILKAN SAAT METODE "DIKIRIM") --}}
                    <div id="field-alamat-pengiriman" class="{{ $isAmbilAwal ? 'hidden' : '' }}">
                        <label for="alamat" class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Alamat Lengkap Pengiriman <span class="text-red-500">*</span>
                        </label>
                        <textarea id="alamat" name="alamat" rows="3" maxlength="500"
                                  placeholder="Jalan, nomor rumah, RT/RW, kelurahan, kecamatan, kota"
                                  {{ $isAmbilAwal ? '' : 'required' }}
                                  class="w-full px-3 py-2.5 rounded-lg border text-sm outline-none transition resize-none
                                         @error('alamat') border-red-400 @else border-gray-300 focus:border-primary focus:ring-primary @enderror">{{ old('alamat', $user->alamat) }}</textarea>
                        @error('alamat')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    {{-- CATATAN --}}
                    <div>
                        <label for="catatan" class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Catatan <span class="font-normal text-gray-400">(opsional)</span>
                        </label>
                        <textarea id="catatan" name="catatan" rows="2" maxlength="500"
                                  placeholder="{{ $isAmbilAwal ? 'Contoh: perkiraan saya ambil hari Sabtu sekitar jam 14:00' : 'Contoh: mohon dipacking kardus tebal, kirim sore hari' }}"
                                  class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary resize-none">{{ old('catatan') }}</textarea>
                    </div>

                    <button type="submit" id="btn-submit-checkout"
                            class="w-full py-3.5 rounded-lg bg-emerald-600 text-white text-sm font-bold shadow-sm hover:bg-emerald-700 transition-colors flex items-center justify-center gap-2">
                        <span>Buat Pesanan &amp; Lanjut ke WhatsApp</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </form>
            </div>

            {{-- RINGKASAN --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 sticky top-6">
                    <h2 class="text-sm font-bold text-gray-900">Ringkasan Pesanan</h2>

                    <div class="mt-4 space-y-3">
                        @foreach ($items as $item)
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $item->plant->nama }}</p>
                                    <p class="text-xs text-gray-500">{{ $item->jumlah }} × {{ $item->plant->harga_rupiah }}</p>
                                </div>
                                <p class="text-sm font-semibold text-gray-900 whitespace-nowrap">{{ $item->subtotal_rupiah }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-sm text-gray-600">Total</span>
                        <span class="text-lg font-extrabold text-primary">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>

                    {{-- KOTAK INFORMASI STATUS METODE DI RINGKASAN --}}
                    <div id="ringkasan-metode-info" class="mt-5 p-3.5 rounded-lg bg-sky-50 border border-sky-200 text-[11px] text-sky-900 leading-relaxed">
                        @if ($isAmbilAwal)
                            Pesanan akan <strong>diambil langsung</strong> di toko <strong>{{ $store->nama_toko }}</strong>. Koordinasikan jam pengambilan dengan penjual melalui WhatsApp.
                        @else
                            Pesanan dikirim ke WhatsApp <strong>{{ $store->nama_toko }}</strong>. Pembayaran dan pengiriman diatur langsung antara Anda dan penjual — TanamKu tidak memproses pembayaran.
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const radios = document.querySelectorAll('input[name="metode_serah"]');
        const boxToko = document.getElementById('box-alamat-toko');
        const fieldAlamat = document.getElementById('field-alamat-pengiriman');
        const inputAlamat = document.getElementById('alamat');
        const labelNama = document.getElementById('label-nama-penerima');
        const hintNama = document.getElementById('hint-nama-penerima');
        const stepperText = document.getElementById('stepper-step2-text');
        const inputCatatan = document.getElementById('catatan');
        const ringkasanInfo = document.getElementById('ringkasan-metode-info');
        const namaToko = @json($store->nama_toko);

        function toggleMetode(metode) {
            if (metode === 'ambil') {
                if (boxToko) boxToko.classList.remove('hidden');
                if (fieldAlamat) fieldAlamat.classList.add('hidden');
                if (inputAlamat) inputAlamat.removeAttribute('required');
                if (labelNama) labelNama.textContent = 'Nama Pengambil';
                if (hintNama) hintNama.textContent = 'Orang yang akan mengambil tanaman di toko';
                if (stepperText) stepperText.textContent = '2. Pengambilan di Toko';
                if (inputCatatan && !inputCatatan.value) {
                    inputCatatan.placeholder = 'Contoh: perkiraan saya ambil hari Sabtu sekitar jam 14:00';
                }
                if (ringkasanInfo) {
                    ringkasanInfo.className = 'mt-5 p-3.5 rounded-lg bg-emerald-50 border border-emerald-200 text-[11px] text-emerald-950 leading-relaxed';
                    ringkasanInfo.innerHTML = 'Pesanan akan <strong>diambil langsung</strong> di toko <strong>' + namaToko + '</strong> (bebas ongkir). Koordinasikan jadwal pengambilan dengan penjual via WhatsApp.';
                }
            } else {
                if (boxToko) boxToko.classList.add('hidden');
                if (fieldAlamat) fieldAlamat.classList.remove('hidden');
                if (inputAlamat) inputAlamat.setAttribute('required', 'required');
                if (labelNama) labelNama.textContent = 'Nama Penerima';
                if (hintNama) hintNama.textContent = 'Nama penerima paket pesanan';
                if (stepperText) stepperText.textContent = '2. Data Pengiriman';
                if (inputCatatan && !inputCatatan.value) {
                    inputCatatan.placeholder = 'Contoh: mohon dipacking kardus tebal, kirim sore hari';
                }
                if (ringkasanInfo) {
                    ringkasanInfo.className = 'mt-5 p-3.5 rounded-lg bg-sky-50 border border-sky-200 text-[11px] text-sky-900 leading-relaxed';
                    ringkasanInfo.innerHTML = 'Pesanan dikirim ke WhatsApp <strong>' + namaToko + '</strong>. Pembayaran dan pengiriman diatur langsung antara Anda dan penjual — TanamKu tidak memproses pembayaran.';
                }
            }
        }

        radios.forEach(function (radio) {
            radio.addEventListener('change', function () {
                toggleMetode(this.value);
            });
        });

        const activeRadio = document.querySelector('input[name="metode_serah"]:checked');
        if (activeRadio) {
            toggleMetode(activeRadio.value);
        }
    });
</script>
@endpush
