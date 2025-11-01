<?php

namespace App\Livewire;

use App\Models\Transaction; // <--- PASTIKAN MENGGUNAKAN MODEL TRANSACTION
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class FinanceHomeLivewire extends Component
{
    public $auth;

    public function mount()
    {
        $this->auth = Auth::user();
    }

    public function render()
    {
        // Mengambil semua transaksi milik user yang sedang login
        $transactions = Transaction::where('user_id', $this->auth->id)
            ->orderBy('date', 'desc')
            ->get();

        $data = [
            'transactions' => $transactions,
        ];

        return view('livewire.finance-home-livewire', $data);
    }

    //
    // Kebutuhan Utama 2: Tambah Data
    // 
    public $addTransactionType = '';
    public $addTransactionAmount = '';
    public $addTransactionDate = '';
    public $addTransactionDescription = '';

    public function addTransaction()
    {
        $this->validate([
            'addTransactionType' => 'required|in:income,expense',
            'addTransactionAmount' => 'required|numeric|min:1',
            'addTransactionDate' => 'required|date',
            'addTransactionDescription' => 'required|string',
        ]);

        Transaction::create([
            'user_id' => $this->auth->id,
            'type' => $this->addTransactionType,
            'amount' => $this->addTransactionAmount,
            'date' => $this->addTransactionDate,
            'description' => $this->addTransactionDescription,
        ]);

        // Reset form
        $this->reset(['addTransactionType', 'addTransactionAmount', 'addTransactionDate', 'addTransactionDescription']);
        // Tutup modal
        $this->dispatch('closeModal', id: 'addTransactionModal');
    }

    //
    // Kebutuhan Utama 3: Ubah Data
    // 
    public $editTransactionId = '';
    public $editTransactionType = '';
    public $editTransactionAmount = '';
    public $editTransactionDate = '';
    public $editTransactionDescription = '';

    public function prepareEditTransaction($id)
    {
        $transaction = Transaction::where('id', $id)->where('user_id', $this->auth->id)->first();
        if (!$transaction) {
            return;
        }

        $this->editTransactionId = $transaction->id;
        $this->editTransactionType = $transaction->type;
        $this->editTransactionAmount = $transaction->amount;
        // Format tanggal agar sesuai dengan input type="date"
        $this->editTransactionDate = $transaction->date->format('Y-m-d');
        $this->editTransactionDescription = $transaction->description;

        $this->dispatch('showModal', id: 'editTransactionModal');
    }

    public function editTransaction()
    {
        $this->validate([
            'editTransactionType' => 'required|in:income,expense',
            'editTransactionAmount' => 'required|numeric|min:1',
            'editTransactionDate' => 'required|date',
            'editTransactionDescription' => 'required|string',
        ]);

        $transaction = Transaction::where('id', $this->editTransactionId)->where('user_id', $this->auth->id)->first();
        if (!$transaction) {
            return;
        }

        $transaction->type = $this->editTransactionType;
        $transaction->amount = $this->editTransactionAmount;
        $transaction->date = $this->editTransactionDate;
        $transaction->description = $this->editTransactionDescription;
        $transaction->save();

        $this->reset(['editTransactionId', 'editTransactionType', 'editTransactionAmount', 'editTransactionDate', 'editTransactionDescription']);
        $this->dispatch('closeModal', id: 'editTransactionModal');
    }


    //
    // Kebutuhan Utama 4: Hapus Data
    // 
    public $deleteTransactionId = '';
    public $deleteTransactionAmount = '';
    public $deleteTransactionDescription = '';

    public function prepareDeleteTransaction($id)
    {
        $transaction = Transaction::where('id', $id)->where('user_id', $this->auth->id)->first();
        if (!$transaction) {
            return;
        }

        $this->deleteTransactionId = $transaction->id;
        $this->deleteTransactionAmount = $transaction->amount;
        $this->deleteTransactionDescription = $transaction->description;
        $this->dispatch('showModal', id: 'deleteTransactionModal');
    }

    public function deleteTransaction()
    {
        Transaction::destroy($this->deleteTransactionId);

        $this->reset(['deleteTransactionId', 'deleteTransactionAmount', 'deleteTransactionDescription']);
        $this->dispatch('closeModal', id: 'deleteTransactionModal');
    }
}