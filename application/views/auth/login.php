<?php $this->load->view('layouts/header'); ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4>Login</h4>
            </div>
            <div class="card-body">
                <?= form_open('auth/login') ?>
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
                    <button type="submit" class="btn btn-primary">Login</button>
                    <a href="<?= base_url('auth/register') ?>" class="btn btn-link">Register</a>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>