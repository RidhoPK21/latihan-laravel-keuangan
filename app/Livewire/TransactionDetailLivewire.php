<?php

namespace App\Livewire;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // <-- DITAMBAHKAN
use Livewire\Component;
use Livewire\WithFileUploads; // <-- DITAMBAHKAN

class TransactionDetailLivewire extends Component
{
    use WithFileUploads; // <-- DITAMBAHKAN

    public $transaction;
    public $auth;

    // Properti untuk Ubah Cover (dari edit-cover.blade.php)
    public $editCoverTransactionFile; // <-- DITAMBAHKAN

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

    // ===================================
    // LOGIKA BARU DITAMBAHKAN DI BAWAH INI
    // ===================================

    /**
     * Logika untuk Ubah Cover Transaksi
     */
    public function editCoverTransaction()
    {
        $this->validate([
            'editCoverTransactionFile' => 'required|image|max:2048', // 2MB Max
        ]);

        if ($this->editCoverTransactionFile) {
            // Hapus cover lama jika ada
            if ($this->transaction->cover && Storage::disk('public')->exists($this->transaction->cover)) {
                Storage::disk('public')->delete($this->transaction->cover);
            }

            // Buat nama file baru dan simpan
            $userId = $this->auth->id;
            $dateNumber = now()->format('YmdHis');
            $extension = $this->editCoverTransactionFile->getClientOriginalExtension();
            $filename = $userId . '-' . $dateNumber . '.' . $extension;
            
            // Simpan file ke storage/app/public/covers
            $path = $this->editCoverTransactionFile->storeAs('covers', $filename, 'public');

            // Simpan path baru ke database
            $this->transaction->cover = $path;
            $this->transaction->save();
        }

        $this->reset(['editCoverTransactionFile']);
        $this->dispatch('closeModal', id: 'editCoverTransactionModal');
    }
}