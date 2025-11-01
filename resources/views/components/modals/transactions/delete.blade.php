<form wire:submit.prevent="deleteTransaction">
    <div class="modal fade" tabindex="-1" id="deleteTransactionModal" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hapus Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        Apakah kamu yakin ingin menghapus transaksi ini?
                        <br>
                        <strong>Deskripsi: "{{ $deleteTransactionDescription }}"</strong>
                        <br>
                        <strong>Jumlah: Rp {{ number_format($deleteTransactionAmount) }}</strong>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Lanjutkan, Hapus</button>
                </div>
            </div>
        </div>
    </div>
</form>