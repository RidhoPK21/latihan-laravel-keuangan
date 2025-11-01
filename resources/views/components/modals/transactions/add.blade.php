<form wire:submit.prevent="addTransaction">
    <div class="modal fade" tabindex="-1" id="addTransactionModal" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tipe Transaksi</label>
                        {{-- PERBAIKAN: "class_exists" diubah menjadi "class" --}}
                        <select class="form-select" wire:model="addTransactionType">
                            <option value="">Pilih Tipe</option>
                            <option value="income">Pemasukan (Income)</option>
                            <option value="expense">Pengeluaran (Expense)</option>
                        </select>
                        @error('addTransactionType')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah (Rp)</label>
                        <input type="number" class="form-control" wire:model="addTransactionAmount"
                            placeholder="Contoh: 50000">
                        @error('addTransactionAmount')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- BARU: Input untuk 'addTransactionTitle' ditambahkan di sini --}}
                    <div class="mb-3">
                        <label class="form-label">Judul</label>
                        <input type="text" class="form-control" wire:model="addTransactionTitle"
                            placeholder="Contoh: Gaji Bulanan">
                        @error('addTransactionTitle')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" class="form-control" wire:model="addTransactionDate">
                        @error('addTransactionDate')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" rows="4" wire:model="addTransactionDescription"></textarea>
                        @error('addTransactionDescription')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</form>