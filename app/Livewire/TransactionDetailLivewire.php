<?php

namespace App\Livewire;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class TransactionDetailLivewire extends Component
{
    use WithFileUploads;

    public $transaction;
    public $auth;

    // Properti untuk Ubah Cover (Nama disamakan dengan Blade)
    public $editCoverFile;

    public function mount()
    {
        $this->auth = Auth::user();
        $transaction_id = request()->route('transaction_id');
        
        $targetTransaction = Transaction::where('id', $transaction_id)->where('user_id', $this->auth->id)->first();
        if (!$targetTransaction) {
            return redirect()->route('app.home');
        }

        $this->transaction = $targetTransaction;
    }

    public function render()
    {
        return view('livewire.transaction-detail-livewire');
    }

    /**
     * Logika untuk Simpan Cover Transaksi
     */
    public function saveCover()
    {
        $this->validate([
            'editCoverFile' => 'required|image|max:2048', // Max 2MB
        ]);

        // Menggunakan try-catch untuk penanganan error yang lebih baik
        try {
            if ($this->editCoverFile) {
                // Hapus cover lama jika ada
                if ($this->transaction->cover && Storage::disk('public')->exists($this->transaction->cover)) {
                    Storage::disk('public')->delete($this->transaction->cover);
                }

                // Buat nama file baru dan simpan
                $userId = $this->auth->id;
                $dateNumber = now()->format('YmdHis');
                $extension = $this->editCoverFile->getClientOriginalExtension();
                $filename = $userId . '-' . $dateNumber . '.' . $extension;
                
                // Simpan file ke storage/app/public/covers
                $path = $this->editCoverFile->storeAs('covers', $filename, 'public');

                // Simpan path baru ke database
                $this->transaction->cover = $path;
                $this->transaction->save();
            }

            $this->reset(['editCoverFile']);
            $this->dispatch('closeModal', id: 'editCoverTransactionModal');
            
            // NOTIFIKASI SUKSES (SweetAlert2 Toast)
            $this->dispatch('showAlert', ['icon' => 'success', 'message' => 'Bukti transaksi berhasil diubah.']);

        } catch (\Exception $e) {
            // NOTIFIKASI GAGAL jika ada masalah database atau file system
            $this->dispatch('showAlert', ['icon' => 'error', 'message' => 'Gagal mengubah bukti transaksi: ' . $e->getMessage()]);
            // Catatan: Di produksi, jangan tampilkan $e->getMessage()
        }
    }
}
