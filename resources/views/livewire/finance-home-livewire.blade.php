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
            <div class="d-flex mb-2">
                <div class="flex-fill">
                    <h3>Daftar Transaksi</h3>
                </div>
                <div>
                    <button class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#addTransactionModal">
                        Tambah Transaksi
                    </button>
                </div>
            </div>

            <table class="table table-striped">
                <tr class="table-light">
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Tipe</th>
                    <th>Jumlah</th>
                    <th>Deskripsi</th>
                    <th>Tindakan</th>
                </tr>
                @foreach ($transactions as $key => $transaction)
                    <tr>
                        <td>{{ $key + 1 }}</td>
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
                @if (sizeof($transactions) == 0)
                    <tr>
                        <td colspan="6" class="text-center">Belum ada data transaksi yang
                            tersedia.</td>
                    </tr>
                @endif
            </table>
        </div>
    </div>

    {{-- Modals --}}
    @include('components.modals.transactions.add')
    @include('components.modals.transactions.edit')
    @include('components.modals.transactions.delete')
</div>