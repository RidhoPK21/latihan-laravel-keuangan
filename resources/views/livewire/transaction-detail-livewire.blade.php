<div class="mt-3">
    <div class="card">
        <div class="card-header d-flex">
            <div class="flex-fill">
                <a href="{{ route('app.home') }}" class="text-decoration-none">
                    <small class="text-muted">
                        &lt; Kembali ke Home
                    </small>
                </a>
                <h3>
                    {{ $transaction->description }}
                    @if ($transaction->type == 'income')
                        <small class="badge bg-success">Pemasukan</small>
                    @else
                        <small class="badge bg-danger">Pengeluaran</small>
                    @endif
                </h3>
                <small>Rp {{ number_format($transaction->amount) }} | {{ $transaction->date->format('d F Y') }}</small>
            </div>
            <div>
                <button class="btn btn-warning" data-bs-target="#editCoverTransactionModal" data-bs-toggle="modal">
                    Ubah Bukti (Cover)
                </button>
            </div>
        </div>
        <div class="card-body">
            @if ($transaction->cover)
                <img src="{{ asset('storage/' . $transaction->cover) }}" alt="Cover" style="max-width: 100%;">
                <hr>
            @else
                <div class="alert alert-info">Belum ada bukti (cover) yang di-upload.</div>
            @endif
        </div>
    </div>

    {{-- Modals --}}
    @include('components.modals.transactions.edit-cover')
</div>