<?php

namespace App\Livewire;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads; // Penting untuk upload file

class TransactionDetailLivewire extends Component
{
    use WithFileUploads; // Penting untuk upload file

    public $transaction;
    public $auth;

    public function mount()
    {
        $this->auth = Auth::user();

        // Ambil transaction_id dari URL
        $transaction_id = request()->route('transaction_id');
        $targetTransaction = Transaction::where('id', $transaction_id)
            ->where('user_id', $this->auth->id) // Pastikan milik user
            ->first();

        // Jika tidak ditemukan, lempar ke home
        if (!$targetTransaction) {
            return redirect()->route('app.home');
        }

        $this->transaction = $targetTransaction;
    }

    public function render()
    {
        return view('livewire.transaction-detail-livewire');
    }

    //
    // Kebutuhan Utama 5: Mengolah Data Gambar
    //
    public $editCoverFile;

    public function editCoverTransaction()
    {
        $this->validate([
            'editCoverFile' => 'required|image|max:2048', // Maks 2MB
        ]);

        if ($this->editCoverFile) {
            // Hapus cover lama jika ada
            if ($this->transaction->cover && Storage::disk('public')->exists($this->transaction->cover)) {
                Storage::disk('public')->delete($this->transaction->cover);
            }

            // Buat nama file baru yang unik
            $userId = $this->auth->id;
            $dateNumber = now()->format('YmdHis');
            $extension = $this->editCoverFile->getClientOriginalExtension();
            $filename = $userId . '-' . $dateNumber . '.' . $extension;

            // Simpan file baru ke 'storage/app/public/covers'
            $path = $this->editCoverFile->storeAs('covers', $filename, 'public');

            // Simpan path ke database
            $this->transaction->cover = $path;
            $this->transaction->save();
        }

        $this->reset(['editCoverFile']);
        $this->dispatch('closeModal', id: 'editCoverTransactionModal');
    }
}