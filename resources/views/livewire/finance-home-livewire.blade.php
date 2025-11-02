<div class="mt-3">
    <div class="card">
        <div class="card-header d-flex">
            <div class="flex-fill">
                <h3>Hay, {{ $auth->name }}</h3>
            </div>
            <div>
                <a href="{{ route('auth.logout') }}" class="btn btn-warning">Keluar</a>
            </div>
        </div>
        <div class="card-body">
            
            {{-- BAGIAN BARU: PENCARIAN DAN FILTER --}}
            <div class="row mb-4">
                {{-- Kolom Pencarian --}}
                <div class="col-md-6">
                    <input type="text" class="form-control" placeholder="Cari Judul atau Deskripsi Transaksi..." 
                           wire:model.live.debounce.300ms="search">
                </div>
                {{-- Kolom Filter Tipe --}}
                <div class="col-md-3">
                    <select class="form-select" wire:model.live="filterType">
                        <option value="all">Semua Tipe</option>
                        <option value="income">Pemasukan</option>
                        <option value="expense">Pengeluaran</option>
                    </select>
                </div>
                {{-- Tombol Tambah Transaksi --}}
                <div class="col-md-3 d-flex justify-content-end">
                    <button class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#addTransactionModal">
                        Tambah Transaksi
                    </button>
                </div>
            </div>
            {{-- AKHIR BAGIAN BARU: PENCARIAN DAN FILTER --}}

            <div class="d-flex mb-2">
                <div class="flex-fill">
                    <h3>Daftar Transaksi</h3>
                </div>
                {{-- Tombol Tambah dihapus dari sini dan dipindahkan ke row pencarian di atas --}}
            </div>

            {{-- Mengubah logika tampilan tabel berdasarkan ada tidaknya data --}}
            @if ($transactions->isEmpty())
                <div class="alert alert-info text-center">
                    Tidak ada transaksi yang ditemukan untuk kriteria pencarian atau filter saat ini.
                </div>
            @else
                <table class="table table-striped">
                    <tr class="table-light">
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Tipe</th>
                        <th>Jumlah</th>
                        <th>Deskripsi</th>
                        <th>Tindakan</th>
                    </tr>
                    {{-- Pagination dimulai dari halaman mana pun, bukan selalu 1 --}}
                    @foreach ($transactions as $key => $transaction)
                        <tr>
                            <td>{{ $transactions->firstItem() + $loop->index }}</td>
                            <td>{{ $transaction->date->format('d F Y') }}</td>
                            <td>
                                @if ($transaction->type == 'income')
                                    <span class="badge bg-success">Pemasukan</span>
                                @else
                                    <span class="badge bg-danger">Pengeluaran</span>
                                @endif
                            </td>
                            <td>Rp {{ number_format($transaction->amount) }}</td>
                            <td>{{ $transaction->description }}</td>
                            <td>
                                <a href="{{ route('app.transactions.detail', ['transaction_id' => $transaction->id]) }}"
                                    class="btn btn-sm btn-info">
                                    Detail
                                </a>
                                <button wire:click="prepareEditTransaction({{ $transaction->id }})"
                                    class="btn btn-sm btn-warning">
                                    Edit
                                </button>
                                <button wire:click="prepareDeleteTransaction({{ $transaction->id }})"
                                    class="btn btn-sm btn-danger">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </table>

                {{-- BAGIAN BARU: PAGINATION --}}
                <div class="mt-3">
                    {{ $transactions->links() }}
                </div>
                {{-- AKHIR PAGIAN BARU --}}

            @endif
        </div>
    </div>

    {{-- Modals --}}
    @include('components.modals.transactions.add')
    @include('components.modals.transactions.edit')
    @include('components.modals.transactions.delete')
</div>