<?php

namespace App\Livewire;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class FinanceHomeLivewire extends Component
{
    use WithPagination;

    // Kita tidak akan terlalu bergantung pada $auth ini lagi
    public $auth;

    // Properti untuk Tambah Transaksi
    public $addTransactionTitle;
    public $addTransactionDescription;
    public $addTransactionAmount;
    public $addTransactionType = 'income';
    public $addTransactionDate; // <--- 1. TAMBAHKAN INI

    // Properti untuk Edit Transaksi
    public $editTransactionId;
    public $editTransactionTitle;
    public $editTransactionDescription;
    public $editTransactionAmount;
    public $editTransactionType;
    public $editTransactionDate; // <--- TAMBAHKAN INI

    // Properti untuk Hapus Transaksi
    public $deleteTransactionId;
    public $deleteTransactionTitle;
    public $deleteTransactionDescription;
    public $deleteTransactionAmount;
    public $deleteTransactionConfirmTitle;

    public function mount()
    {
        $this->auth = Auth::user();
    }

    public function render()
    {
        // Ambil ID user yang aman, 0 jika belum login
        $userId = auth()->id() ?? 0;

        $transactions = Transaction::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $data = [
            'transactions' => $transactions
        ];

        return view('livewire.finance-home-livewire', $data);
    }

    /**
     * Logika untuk Tambah Transaksi
     */
    public function addTransaction()
    {
        $this->validate([
            'addTransactionTitle' => 'required|string|max:255',
            'addTransactionDescription' => 'required|string', // Dibuat required
            'addTransactionAmount' => 'required|numeric',
            'addTransactionType' => 'required|in:income,expense',
            'addTransactionDate' => 'required|date', // <--- 2. TAMBAHKAN INI
        ]);

        // Ambil ID user langsung dari helper auth()
        Transaction::create([
            'user_id' => auth()->id(), // <-- LEBIH AMAN
            'title' => $this->addTransactionTitle,
            'description' => $this->addTransactionDescription,
            'amount' => $this->addTransactionAmount,
            'type' => $this->addTransactionType,
            'date' => $this->addTransactionDate, // <--- 3. TAMBAHKAN INI (Asumsi Anda ingin menyimpan tanggal ini)
        ]);

        $this->reset(['addTransactionTitle', 'addTransactionDescription', 'addTransactionAmount', 'addTransactionType', 'addTransactionDate']);
        $this->dispatch('closeModal', id: 'addTransactionModal');
    }

    /**
     * Logika untuk Persiapan Edit Transaksi
     */
    public function prepareEditTransaction($id)
    {
        $transaction = Transaction::where('id', $id)->where('user_id', auth()->id())->first();
        
        if (!$transaction) {
            return;
        }

        $this->editTransactionId = $transaction->id;
        $this->editTransactionTitle = $transaction->title;
        $this->editTransactionDescription = $transaction->description;
        $this->editTransactionAmount = $transaction->amount;
        $this->editTransactionType = $transaction->type;
        
        // TAMBAHKAN INI (Gunakan format Y-m-d untuk input HTML)
        $this->editTransactionDate = $transaction->date->format('Y-m-d'); 

        $this->dispatch('showModal', id: 'editTransactionModal');
    }

    /**
     * Logika untuk Simpan Edit Transaksi
     */
 public function editTransaction()
    {
        $this->validate([
            'editTransactionTitle' => 'required|string|max:255',
            'editTransactionDescription' => 'required|string',
            'editTransactionAmount' => 'required|numeric',
            'editTransactionType' => 'required|in:income,expense',
            'editTransactionDate' => 'required|date', // <--- TAMBAHKAN VALIDASI INI
        ]);

        $transaction = Transaction::where('id', $this->editTransactionId)->where('user_id', auth()->id())->first();
        
        if (!$transaction) {
            $this->addError('editTransactionTitle', 'Data transaksi tidak tersedia.');
            return;
        }

        $transaction->title = $this->editTransactionTitle;
        $transaction->description = $this->editTransactionDescription;
        $transaction->amount = $this->editTransactionAmount;
        $transaction->type = $this->editTransactionType;
        $transaction->date = $this->editTransactionDate; // <--- TAMBAHKAN LOGIKA SIMPAN INI
        $transaction->save();

        // TAMBAHKAN 'editTransactionDate' DI DALAM RESET
        $this->reset(['editTransactionId', 'editTransactionTitle', 'editTransactionDescription', 'editTransactionAmount', 'editTransactionType', 'editTransactionDate']);
        $this->dispatch('closeModal', id: 'editTransactionModal');
    }

    /**
     * Logika untuk Persiapan Hapus Transaksi
     */
    public function prepareDeleteTransaction($id)
    {
        // Ambil ID user langsung dan tambahkan pengecekan
        $transaction = Transaction::where('id', $id)->where('user_id', auth()->id())->first();
        
        // INI PENGECEKAN PENTING YANG MEMPERBAIKI ERROR
        if (!$transaction) {
            return; // <-- Ini akan mencegah error "read property id on null"
        }

        $this->deleteTransactionId = $transaction->id;
        $this->deleteTransactionTitle = $transaction->title;
        $this->deleteTransactionDescription = $transaction->description;
        $this->deleteTransactionAmount = $transaction->amount;

        $this->dispatch('showModal', id: 'deleteTransactionModal');
    }

    /**
     * Logika untuk Hapus Transaksi
     */
    public function deleteTransaction()
    {
        if ($this->deleteTransactionConfirmTitle != $this->deleteTransactionTitle) {
            $this->addError('deleteTransactionConfirmTitle', 'Judul konfirmasi tidak sesuai.');
            return;
        }

        // Ambil ID user langsung dan tambahkan pengecekan
        $transaction = Transaction::where('id', $this->deleteTransactionId)->where('user_id', auth()->id())->first();

        if ($transaction && $transaction->cover && \Illuminate\Support\Facades\Storage::disk('public')->exists($transaction->cover)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($transaction->cover);
        }

        if ($transaction) {
            $transaction->delete(); // Lebih aman daripada destroy()
        }

        $this->reset(['deleteTransactionId', 'deleteTransactionTitle', 'deleteTransactionDescription', 'deleteTransactionAmount', 'deleteTransactionConfirmTitle']);
        $this->dispatch('closeModal', id: 'deleteTransactionModal');
    }
}