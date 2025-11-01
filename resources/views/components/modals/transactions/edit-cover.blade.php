<form wire:submit.prevent="saveCover">
    <div class="modal fade" tabindex="-1" id="editCoverTransactionModal" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ubah Bukti (Cover) Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pilih Gambar Bukti</label>
                        {{-- Nama properti ini sudah benar, kita akan ikuti nama ini --}}
                        <input type="file" class="form-control" wire:model="editCoverFile">
                        @error('editCoverFile')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" @if (!$editCoverFile) disabled @endif>
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>