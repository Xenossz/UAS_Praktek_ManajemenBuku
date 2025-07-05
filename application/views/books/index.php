<?php $this->load->view('layouts/header'); ?>

<div class="row">
    <div class="col-md-12">
        <h2>Daftar Buku</h2>
        
        <div class="row mb-3">
            <div class="col-md-6">
                <a href="<?= base_url('books/create') ?>" class="btn btn-primary">Tambah Buku</a>
            </div>
            <div class="col-md-6">
                <form method="get" class="d-flex">
                    <input type="text" name="search" class="form-control me-2" placeholder="Cari buku..." value="<?= $search ?>">
                    <input type="hidden" name="sort" value="<?= $sort ?>">
                    <input type="hidden" name="order" value="<?= $order ?>">
                    <button class="btn btn-outline-secondary" type="submit">Cari</button>
                </form>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>
                            <a href="<?= base_url('books?sort=judul&order=' . ($sort == 'judul' && $order == 'asc' ? 'desc' : 'asc') . ($search ? '&search=' . $search : '')) ?>" 
                               class="text-decoration-none text-dark">
                                Judul 
                                <?php if ($sort == 'judul'): ?>
                                    <?= $order == 'asc' ? '↑' : '↓' ?>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th>
                            <a href="<?= base_url('books?sort=penulis&order=' . ($sort == 'penulis' && $order == 'asc' ? 'desc' : 'asc') . ($search ? '&search=' . $search : '')) ?>" 
                               class="text-decoration-none text-dark">
                                Penulis 
                                <?php if ($sort == 'penulis'): ?>
                                    <?= $order == 'asc' ? '↑' : '↓' ?>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th>
                            <a href="<?= base_url('books?sort=tahun_terbit&order=' . ($sort == 'tahun_terbit' && $order == 'asc' ? 'desc' : 'asc') . ($search ? '&search=' . $search : '')) ?>" 
                               class="text-decoration-none text-dark">
                                Tahun Terbit 
                                <?php if ($sort == 'tahun_terbit'): ?>
                                    <?= $order == 'asc' ? '↑' : '↓' ?>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th>ISBN</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($books)): ?>
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data buku.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($books as $key => $book): ?>
                            <tr>
                                <td><?= $key + 1 ?></td>
                                <td><?= $book->judul ?></td>
                                <td><?= $book->penulis ?></td>
                                <td><?= $book->tahun_terbit ?></td>
                                <td><?= $book->isbn ?></td>
                                <td>
                                    <a href="<?= base_url('books/edit/'.$book->id) ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="<?= base_url('books/delete/'.$book->id) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>