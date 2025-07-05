<?php $this->load->view('layouts/header'); ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4>Register</h4>
            </div>
            <div class="card-body">
                <?= form_open('auth/register') ?>
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="nama" class="form-control" required>
                        <?= form_error('nama', '<small class="text-danger">', '</small>') ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                        <?= form_error('email', '<small class="text-danger">', '</small>') ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                        <?= form_error('password', '<small class="text-danger">', '</small>') ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                        <?= form_error('confirm_password', '<small class="text-danger">', '</small>') ?>
                    </div>
                    <button type="submit" class="btn btn-primary">Register</button>
                    <a href="<?= base_url('auth/login') ?>" class="btn btn-link">Login</a>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>