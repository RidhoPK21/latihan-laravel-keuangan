<!doctype html>
<html lang="id">

<head>
    {{-- Meta --}}
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Icon --}}
    <link rel="icon" href="/logo.png" type="image/x-icon" />

    {{-- Judul --}}
    <title>Laravel Todolist</title>

    {{-- Styles --}}
    @livewireStyles
    <link rel="stylesheet" href="/assets/vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    
    {{-- SweetAlert2 CDN (Disarankan ditaruh di <head>) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-light">
    <div class="container-fluid">
        @yield('content')
    </div>

    {{-- Scripts --}}
    <script src="/assets/vendor/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // ======================================================
        // LIVEWIRE LISTENERS UNTUK MODAL BOOTSTRAP (LAMA)
        // ======================================================
        document.addEventListener("livewire:initialized", () => {
            // Listener untuk menutup modal Bootstrap
            Livewire.on("closeModal", (data) => {
                const modal = bootstrap.Modal.getInstance(
                    document.getElementById(data.id)
                );
                if (modal) {
                    modal.hide();
                }
            });

            // Listener untuk menampilkan modal Bootstrap
            Livewire.on("showModal", (data) => {
                const modal = bootstrap.Modal.getOrCreateInstance(
                    document.getElementById(data.id)
                );
                if (modal) {
                    modal.show();
                }
            });
        });

        // ======================================================
        // SWEETALERT2 LISTENERS (BARU)
        // ======================================================
        document.addEventListener('DOMContentLoaded', function () {
            
            // 1. Alert (Toast) Biasa untuk Sukses/Gagal (Dipicu oleh $this->dispatch('showAlert', [...]))
            Livewire.on('showAlert', (data) => {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                Toast.fire({
                    icon: data[0].icon,
                    title: data[0].message
                });
            });

            // 2. Alert Konfirmasi untuk Hapus (Dipicu oleh $this->dispatch('showConfirm', [...]))
            Livewire.on('showConfirm', (data) => {
                Swal.fire({
                    title: data[0].title,
                    text: data[0].text,
                    icon: data[0].icon,
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: data[0].confirmButtonText || 'Ya, Lanjutkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Mengirim event kembali ke PHP untuk menjalankan method delete
                        Livewire.dispatch(data[0].method); 
                    }
                });
            });
            
            // 3. Menangani Validasi Livewire sebagai Toast SweetAlert2
            // Livewire.hook digunakan saat Livewire melakukan inisialisasi
            if (typeof Livewire !== 'undefined') {
                Livewire.hook('message.failed', ({ component, updateQueue, errors }) => {
                    if (errors) {
                        const errorMessages = Object.values(errors).flat();
                        const firstError = errorMessages[0];
                        
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 5000,
                            timerProgressBar: true,
                        });

                        Toast.fire({
                            icon: 'error',
                            title: firstError
                        });
                        
                        // Mencegah error validasi ditampilkan dua kali
                        return false; 
                    }
                });
            }
        });
    </script>
    
    {{-- @livewireScripts HARUS ADA DI AKHIR BODY --}}
    @livewireScripts
</body>

</html>
