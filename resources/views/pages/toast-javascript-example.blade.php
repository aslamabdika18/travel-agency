@extends('layouts.app')

@section('title', 'Contoh Notifikasi Toast dengan JavaScript')

@section('content')
<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            <div class="p-6 sm:p-8">
                <h1 class="text-2xl font-bold text-primary mb-6">Contoh Notifikasi Toast dengan JavaScript</h1>
                
                <p class="mb-6 text-secondary-dark">
                    Halaman ini menunjukkan cara menggunakan notifikasi toast langsung dari JavaScript.
                </p>
                
                <div class="space-y-6">
                    <div class="space-y-4">
                        <h2 class="text-xl font-semibold text-secondary-dark">Notifikasi Dasar</h2>
                        <div class="flex flex-wrap gap-3">
                            <button onclick="window.toast.success('Operasi berhasil dilakukan! Lihat progress bar di bawah.')" class="btn btn-primary">
                                Sukses
                            </button>
                            <button onclick="window.toast.error('Terjadi kesalahan saat memproses permintaan!')" class="btn bg-red-500 hover:bg-red-600 text-white">
                                Error
                            </button>
                            <button onclick="window.toast.warning('Perhatian! Ini adalah pesan peringatan.')" class="btn bg-yellow-500 hover:bg-yellow-600 text-white">
                                Peringatan
                            </button>
                            <button onclick="window.toast.info('Informasi: Ini adalah pesan informasi.')" class="btn bg-blue-500 hover:bg-blue-600 text-white">
                                Informasi
                            </button>
                        </div>
                    </div>
                    
                    <div class="space-y-4 pt-6 border-t border-gray-200">
                        <h2 class="text-xl font-semibold text-secondary-dark">Notifikasi dengan Opsi Kustom</h2>
                        <div class="flex flex-wrap gap-3">
                            <button onclick="window.toast.success('Progress bar 10 detik - lihat animasi timer!', {autoClose: 10000})" class="btn btn-primary">
                                Durasi 10 Detik
                            </button>
                            <button onclick="window.toast.info('Notifikasi ini tidak akan hilang otomatis', {autoClose: false})" class="btn bg-blue-500 hover:bg-blue-600 text-white">
                                Tanpa Auto Close
                            </button>
                            <button onclick="window.toast.warning('Progress bar disembunyikan', {hideProgressBar: true})" class="btn bg-purple-500 hover:bg-purple-600 text-white">
                                Tanpa Progress Bar
                            </button>
                            <button onclick="window.toast.info('Hover mouse ke toast ini untuk pause timer dan progress bar!', {pauseOnHover: true, autoClose: 8000})" class="btn bg-teal-500 hover:bg-teal-600 text-white">
                                Pause on Hover
                            </button>
                        </div>
                    </div>
                    
                    <div class="space-y-4 pt-6 border-t border-gray-200">
                        <h2 class="text-xl font-semibold text-secondary-dark">Notifikasi dalam Form</h2>
                        
                        <form id="demoForm" class="space-y-4" onsubmit="handleFormSubmit(event)">
                            <div>
                                <label for="name" class="block text-sm font-medium text-secondary-dark mb-1">Nama</label>
                                <input type="text" id="name" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" required>
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium text-secondary-dark mb-1">Email</label>
                                <input type="email" id="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" required>
                            </div>
                            
                            <div>
                                <button type="submit" class="btn btn-primary w-full">
                                    Kirim Form
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function handleFormSubmit(event) {
        event.preventDefault();
        
        const form = event.target;
        const name = form.elements.name.value;
        const email = form.elements.email.value;
        
        // Simulasi validasi
        if (name.length < 3) {
            window.toast.error('Nama harus minimal 3 karakter');
            return;
        }
        
        if (!email.includes('@')) {
            window.toast.error('Email tidak valid');
            return;
        }
        
        // Simulasi pengiriman form berhasil
        window.toast.success(`Form berhasil dikirim! Terima kasih, ${name}. Progress bar menunjukkan waktu tersisa.`);
        form.reset();
    }
    
    // Semua fungsi toast sekarang menggunakan inline onclick handlers di atas
    // Progress bar akan muncul secara default untuk semua toast dengan autoClose
</script>
@endpush