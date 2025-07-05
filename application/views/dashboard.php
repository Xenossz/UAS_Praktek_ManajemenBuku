<?php $this->load->view('layouts/header'); ?>

<div class="row">
    <div class="col-md-12">
        <h2>Dashboard</h2>
        <p>Selamat datang, <?= $this->session->userdata('nama') ?>!</p>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Total Buku</h5>
                <h3 class="text-primary"><?= $total_books ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Buku Terbaru</h5>
            </div>
            <div class="card-body">
                <?php if(empty($recent_books)): ?>
                    <p>Belum ada buku.</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach($recent_books as $book): ?>
                            <li class="list-group-item">
                                <strong><?= $book->judul ?></strong> - <?= $book->penulis ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <a href="<?= base_url('books') ?>" class="btn btn-primary btn-sm mt-3">Lihat Semua</a>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>