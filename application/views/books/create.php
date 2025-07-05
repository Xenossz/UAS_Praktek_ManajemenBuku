<?php $this->load->view('layouts/header'); ?>

<div class="row">
    <div class="col-md-8">
        <h2>Tambah Buku</h2>
        
        <?= form_open('books/create') ?>
            <div class="mb-3">
                <label class="form-label">Judul</label>
                <input type="text" name="judul" class="form-control" required>
                <?= form_error('judul', '<small class="text-danger">', '</small>') ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Penulis</label>
                <input type="text" name="penulis" class="form-control" required>
                <?= form_error('penulis', '<small class="text-danger">', '</small>') ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Tahun Terbit</label>
                <input type="number" name="tahun_terbit" class="form-control" required>
                <?= form_error('tahun_terbit', '<small class="text-danger">', '</small>') ?>
            </div>
            <div class="mb-3">
                <label class="form-label">ISBN</label>
                <input type="text" name="isbn" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?= base_url('books') ?>" class="btn btn-secondary">Batal</a>
        <?= form_close() ?>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>