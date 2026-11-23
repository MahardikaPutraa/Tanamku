@if (session('success'))
    <div class="mb-6 flex items-start gap-3 p-4 bg-emerald-50/90 border border-emerald-200/90 text-emerald-900 rounded-2xl shadow-xs animate-alert-in" role="alert">
        <div class="w-7 h-7 rounded-lg bg-emerald-500/10 flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <div class="text-sm font-semibold pt-0.5">
            {{ session('success') }}
        </div>
    </div>
@endif

@if (session('error'))
    <div class="mb-6 flex items-start gap-3 p-4 bg-red-50/90 border border-red-200/90 text-red-900 rounded-2xl shadow-xs animate-alert-in" role="alert">
        <div class="w-7 h-7 rounded-lg bg-red-500/10 flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </div>
        <div class="text-sm font-semibold pt-0.5">
            {{ session('error') }}
        </div>
    </div>
@endif

@if (session('warning'))
    <div class="mb-6 flex items-start gap-3 p-4 bg-amber-50/90 border border-amber-200/90 text-amber-900 rounded-2xl shadow-xs animate-alert-in" role="alert">
        <div class="w-7 h-7 rounded-lg bg-amber-500/10 flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>
        <div class="text-sm font-semibold pt-0.5">
            {{ session('warning') }}
        </div>
    </div>
@endif

@if (isset($errors) && $errors->any())
    <div class="mb-6 p-4 bg-red-50/90 border border-red-200/90 text-red-900 rounded-2xl shadow-xs animate-alert-in" role="alert">
        <div class="flex items-center gap-2.5 font-bold text-sm mb-2 text-red-800">
            <div class="w-6 h-6 rounded-md bg-red-500/10 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <span>Mohon periksa kesalahan input berikut:</span>
        </div>
        <ul class="list-disc list-inside text-xs text-red-700 space-y-1 ml-3 font-medium">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
