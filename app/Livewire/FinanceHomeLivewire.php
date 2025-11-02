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
    public $search = '';
    // 'all', 'income', atau 'expense'
    public $filterType = 'all';

    // Properti untuk Tambah Transaksi
    public $addTransactionTitle;
    public $addTransactionDescription;
    public $addTransactionAmount;
    public $addTransactionType = 'income';
    public $addTransactionDate;

    // Properti untuk Edit Transaksi
    public $editTransactionId;
    public $editTransactionTitle;
    public $editTransactionDescription;
    public $editTransactionAmount;
    public $editTransactionType;
    public $editTransactionDate;

    // Properti untuk Hapus Transaksi (Disederhanakan)
    public $deleteTransactionId;
    public $deleteTransactionTitle;
    public $deleteTransactionDescription; // Hanya untuk tampilan
    public $deleteTransactionAmount; // Hanya untuk tampilan
    
    // Properti Konfirmasi Dihapus, digantikan oleh SweetAlert2 Konfirmasi

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterType()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->auth = Auth::user();
    }

    public function render()
    {
        $userId = auth()->id() ?? 0;
        
        $query = Transaction::where('user_id', $userId);
        
        // 1. Logika Pencarian (Case-Insensitive)
        if (!empty($this->search)) {
            $searchTerm = strtolower($this->search); 
            
            $query->where(function ($q) use ($searchTerm) {
                // Menggunakan LOWER() untuk memaksa kolom database menjadi huruf kecil 
                // sebelum membandingkannya dengan searchTerm (Case-Insensitive).
                $q->whereRaw('LOWER(title) LIKE ?', ['%' . $searchTerm . '%'])
                  ->orWhereRaw('LOWER(description) LIKE ?', ['%' . $searchTerm . '%']);
            });
        }

        // 2. Logika Filter Tipe Transaksi
        if ($this->filterType !== 'all') {
            $query->where('type', $this->filterType);
        }

        // 3. Ambil data dengan Pagination (20 data per halaman)
        $transactions = $query
            ->orderBy('date', 'desc') 
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
            'addTransactionDescription' => 'required|string', 
            'addTransactionAmount' => 'required|numeric',
            'addTransactionType' => 'required|in:income,expense',
            'addTransactionDate' => 'required|date',
        ]);

        try {
            Transaction::create([
                'user_id' => auth()->id(), 
                'title' => $this->addTransactionTitle,
                'description' => $this->addTransactionDescription,
                'amount' => $this->addTransactionAmount,
                'type' => $this->addTransactionType,
                'date' => $this->addTransactionDate, 
            ]);

            $this->reset(['addTransactionTitle', 'addTransactionDescription', 'addTransactionAmount', 'addTransactionType', 'addTransactionDate']);
            $this->dispatch('closeModal', id: 'addTransactionModal');
            
            // NOTIFIKASI SUKSES (SweetAlert2 Toast)
            $this->dispatch('showAlert', ['icon' => 'success', 'message' => 'Transaksi berhasil ditambahkan.']);

        } catch (\Exception $e) {
            // NOTIFIKASI GAGAL
            $this->dispatch('showAlert', ['icon' => 'error', 'message' => 'Gagal menambahkan transaksi.']);
        }
    }

    /**
     * Logika untuk Persiapan Edit Transaksi
     */
    public function prepareEditTransaction($id)
    {
        $transaction = Transaction::where('id', $id)->where('user_id', auth()->id())->first();
        
        if (!$transaction) {
            $this->dispatch('showAlert', ['icon' => 'error', 'message' => 'Data tidak ditemukan untuk diubah.']);
            return;
        }

        $this->editTransactionId = $transaction->id;
        $this->editTransactionTitle = $transaction->title;
        $this->editTransactionDescription = $transaction->description;
        $this->editTransactionAmount = $transaction->amount;
        $this->editTransactionType = $transaction->type;
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
            'editTransactionDate' => 'required|date',
        ]);

        $transaction = Transaction::where('id', $this->editTransactionId)->where('user_id', auth()->id())->first();
        
        if (!$transaction) {
            $this->dispatch('showAlert', ['icon' => 'error', 'message' => 'Data transaksi tidak tersedia.']);
            return;
        }

        try {
            $transaction->title = $this->editTransactionTitle;
            $transaction->description = $this->editTransactionDescription;
            $transaction->amount = $this->editTransactionAmount;
            $transaction->type = $this->editTransactionType;
            $transaction->date = $this->editTransactionDate;
            $transaction->save();

            $this->reset(['editTransactionId', 'editTransactionTitle', 'editTransactionDescription', 'editTransactionAmount', 'editTransactionType', 'editTransactionDate']);
            $this->dispatch('closeModal', id: 'editTransactionModal');
            
            // NOTIFIKASI SUKSES
            $this->dispatch('showAlert', ['icon' => 'success', 'message' => 'Transaksi berhasil diubah.']);

        } catch (\Exception $e) {
            $this->dispatch('showAlert', ['icon' => 'error', 'message' => 'Gagal mengubah transaksi.']);
        }
    }

    // ===============================================
    // LOGIKA DELETE DENGAN SWEETALERT2
    // ===============================================

    /**
     * Logika untuk Persiapan Hapus Transaksi (Memicu SweetAlert Konfirmasi)
     */
    public function prepareDeleteTransaction($id)
    {
        $transaction = Transaction::where('id', $id)->where('user_id', auth()->id())->first();
        
        if (!$transaction) {
            $this->dispatch('showAlert', ['icon' => 'error', 'message' => 'Data tidak ditemukan untuk dihapus.']);
            return;
        }

        $this->deleteTransactionId = $transaction->id;
        $this->deleteTransactionTitle = $transaction->title;

        // TAMPILKAN KONFIRMASI SweetAlert2
        $this->dispatch('showConfirm', [
            'title' => 'Yakin Hapus?',
            'text' => "Anda akan menghapus transaksi: " . $this->deleteTransactionTitle . ". Tindakan ini tidak dapat dibatalkan.",
            'icon' => 'warning',
            'confirmButtonText' => 'Ya, Hapus Saja!',
            // Method yang akan dipicu jika pengguna mengklik "Ya, Hapus Saja!"
            'method' => 'executeDeleteTransaction' 
        ]);
    }

    /**
     * Logika Hapus yang sebenarnya (Dipicu oleh SweetAlert Konfirmasi)
     */
    public function executeDeleteTransaction()
    {
        try {
            $transaction = Transaction::where('id', $this->deleteTransactionId)->where('user_id', auth()->id())->first();

            if ($transaction) {
                // Hapus cover jika ada
                if ($transaction->cover && \Illuminate\Support\Facades\Storage::disk('public')->exists($transaction->cover)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($transaction->cover);
                }
                
                $transaction->delete();
                $this->dispatch('showAlert', ['icon' => 'success', 'message' => 'Transaksi berhasil dihapus.']);
            } else {
                $this->dispatch('showAlert', ['icon' => 'error', 'message' => 'Gagal menghapus. Transaksi tidak ditemukan.']);
            }

            $this->reset(['deleteTransactionId', 'deleteTransactionTitle', 'deleteTransactionDescription', 'deleteTransactionAmount']);

        } catch (\Exception $e) {
            $this->dispatch('showAlert', ['icon' => 'error', 'message' => 'Terjadi kesalahan saat menghapus data.']);
        }
    }
}
