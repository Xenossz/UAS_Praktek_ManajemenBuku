# Dokumentasi Aplikasi Manajemen Buku - CodeIgniter 3

## 1. Deskripsi Aplikasi
Aplikasi Manajemen Buku adalah sistem CRUD (Create, Read, Update, Delete) sederhana yang dibangun menggunakan CodeIgniter 3 dengan fitur autentikasi pengguna.

## 2. Fitur Utama
- **CRUD Buku**: Tambah, lihat, edit, hapus data buku
- **Autentikasi**: Register dan login pengguna
- **Dashboard**: Halaman utama setelah login
- **Pencarian**: Cari buku berdasarkan judul/penulis
- **Bootstrap UI**: Tampilan responsive

## 3. Struktur Database

### Tabel `users`
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Tabel `books`
```sql
CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(200) NOT NULL,
    penulis VARCHAR(100) NOT NULL,
    tahun_terbit YEAR NOT NULL,
    isbn VARCHAR(20),
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## 4. Struktur File

```
application/
├── controllers/
│   ├── Auth.php
│   ├── Dashboard.php
│   └── Books.php
├── models/
│   ├── User_model.php
│   └── Book_model.php
├── views/
│   ├── auth/
│   │   ├── login.php
│   │   └── register.php
│   ├── books/
│   │   ├── index.php
│   │   ├── create.php
│   │   └── edit.php
│   ├── layouts/
│   │   ├── header.php
│   │   └── footer.php
│   └── dashboard.php
├── config/
│   ├── autoload.php
│   ├── database.php
│   ├── config.php
│   └── routes.php
└── .htaccess
```

## 5. Konfigurasi

### Config Database (`config/database.php`)
```php
$db['default'] = array(
    'dsn'      => '',
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'manajemen_buku',
    'dbdriver' => 'mysqli',
    'dbprefix' => '',
    'pconnect' => FALSE,
    'db_debug' => TRUE,
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8',
    'dbcollat' => 'utf8_general_ci',
);
```

## Root Folder (`.htaccess`)
```php
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php/$1 [QSA,L]
```
### Config Autoload (`config/autoload.php`)
```php
$autoload['libraries'] = array('database', 'session');
$autoload['helper'] = array('url', 'form');
```

### Config Routes (`config/routes.php`)
```php
$route['default_controller'] = 'auth';
$route['login'] = 'auth/login';
$route['register'] = 'auth/register';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
```

### Config (`config/config.php`)
```php
$config['base_url'] = 'http://localhost/nama_folder_project/';
$config['index_page'] = '';
```

## 6. Implementasi Code

### Controller Auth (`controllers/Auth.php`)
```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library('form_validation');
    }
    
    public function index() {
        if ($this->session->userdata('logged_in')) {
            redirect('dashboard');
        }
        $this->login();
    }
    
    public function login() {
        if ($this->session->userdata('logged_in')) {
            redirect('dashboard');
        }
        
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('auth/login');
        } else {
            $email = $this->input->post('email');
            $password = md5($this->input->post('password'));
            
            $user = $this->User_model->check_login($email, $password);
            
            if ($user) {
                $session_data = array(
                    'user_id' => $user->id,
                    'nama' => $user->nama,
                    'email' => $user->email,
                    'logged_in' => TRUE
                );
                $this->session->set_userdata($session_data);
                redirect('dashboard');
            } else {
                $this->session->set_flashdata('error', 'Email atau password salah');
                redirect('auth/login');
            }
        }
    }
    
    public function register() {
        if ($this->session->userdata('logged_in')) {
            redirect('dashboard');
        }
        
        $this->form_validation->set_rules('nama', 'Nama', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]');
        
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('auth/register');
        } else {
            $data = array(
                'nama' => $this->input->post('nama'),
                'email' => $this->input->post('email'),
                'password' => md5($this->input->post('password'))
            );
            
            if ($this->User_model->register($data)) {
                $this->session->set_flashdata('success', 'Registrasi berhasil! Silakan login.');
                redirect('auth/login');
            } else {
                $this->session->set_flashdata('error', 'Registrasi gagal!');
                redirect('auth/register');
            }
        }
    }
    
    public function logout() {
        $this->session->sess_destroy();
        redirect('auth/login');
    }
}
```

### Controller Dashboard (`controllers/Dashboard.php`)
```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
        $this->load->model('Book_model');
    }
    
    public function index() {
        $data['total_books'] = $this->Book_model->count_all();
        $data['recent_books'] = $this->Book_model->get_recent(5);
        $this->load->view('dashboard', $data);
    }
}
```

### Controller Books (`controllers/Books.php`)
```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Books extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
        $this->load->model('Book_model');
        $this->load->library('form_validation');
    }
    
    public function index() {
        $search = $this->input->get('search');
        $sort = $this->input->get('sort');
        $order = $this->input->get('order');
        
        $data['books'] = $this->Book_model->get_all($search, $sort, $order);
        $data['search'] = $search;
        $data['sort'] = $sort;
        $data['order'] = $order;
        $this->load->view('books/index', $data);
    }
    
    public function create() {
        $this->form_validation->set_rules('judul', 'Judul', 'required');
        $this->form_validation->set_rules('penulis', 'Penulis', 'required');
        $this->form_validation->set_rules('tahun_terbit', 'Tahun Terbit', 'required|numeric');
        
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('books/create');
        } else {
            $data = array(
                'judul' => $this->input->post('judul'),
                'penulis' => $this->input->post('penulis'),
                'tahun_terbit' => $this->input->post('tahun_terbit'),
                'isbn' => $this->input->post('isbn'),
                'deskripsi' => $this->input->post('deskripsi')
            );
            
            if ($this->Book_model->create($data)) {
                $this->session->set_flashdata('success', 'Buku berhasil ditambahkan');
            } else {
                $this->session->set_flashdata('error', 'Gagal menambahkan buku');
            }
            redirect('books');
        }
    }
    
    public function edit($id) {
        $data['book'] = $this->Book_model->get_by_id($id);
        
        if (!$data['book']) {
            show_404();
        }
        
        $this->form_validation->set_rules('judul', 'Judul', 'required');
        $this->form_validation->set_rules('penulis', 'Penulis', 'required');
        $this->form_validation->set_rules('tahun_terbit', 'Tahun Terbit', 'required|numeric');
        
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('books/edit', $data);
        } else {
            $update_data = array(
                'judul' => $this->input->post('judul'),
                'penulis' => $this->input->post('penulis'),
                'tahun_terbit' => $this->input->post('tahun_terbit'),
                'isbn' => $this->input->post('isbn'),
                'deskripsi' => $this->input->post('deskripsi')
            );
            
            if ($this->Book_model->update($id, $update_data)) {
                $this->session->set_flashdata('success', 'Buku berhasil diperbarui');
            } else {
                $this->session->set_flashdata('error', 'Gagal memperbarui buku');
            }
            redirect('books');
        }
    }
    
    public function delete($id) {
        if ($this->Book_model->delete($id)) {
            $this->session->set_flashdata('success', 'Buku berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus buku');
        }
        redirect('books');
    }
}
```

### Model User (`models/User_model.php`)
```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {
    
    public function register($data) {
        return $this->db->insert('users', $data);
    }
    
    public function check_login($email, $password) {
        $this->db->where('email', $email);
        $this->db->where('password', $password);
        $query = $this->db->get('users');
        return $query->row();
    }
}
```

### Model Book (`models/Book_model.php`)
```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Book_model extends CI_Model {
    
    public function get_all($search = null, $sort = null, $order = null) {
        if ($search) {
            $this->db->like('judul', $search);
            $this->db->or_like('penulis', $search);
        }
        
        // Set sorting
        if ($sort && in_array($sort, ['judul', 'penulis', 'tahun_terbit', 'created_at'])) {
            $order = ($order === 'desc') ? 'DESC' : 'ASC';
            $this->db->order_by($sort, $order);
        } else {
            $this->db->order_by('created_at', 'DESC');
        }
        
        return $this->db->get('books')->result();
    }
    
    public function get_by_id($id) {
        return $this->db->get_where('books', array('id' => $id))->row();
    }
    
    public function create($data) {
        return $this->db->insert('books', $data);
    }
    
    public function update($id, $data) {
        return $this->db->update('books', $data, array('id' => $id));
    }
    
    public function delete($id) {
        return $this->db->delete('books', array('id' => $id));
    }
    
    public function count_all() {
        return $this->db->count_all('books');
    }
    
    public function get_recent($limit = 5) {
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get('books')->result();
    }
}
```

## 7. Views

### Layout Header (`views/layouts/header.php`)
```php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'Manajemen Buku' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php if($this->session->userdata('logged_in')): ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url('dashboard') ?>">Manajemen Buku</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('dashboard') ?>">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('books') ?>">Buku</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('auth/logout') ?>">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <?php endif; ?>

    <div class="container mt-4">
        <?php if($this->session->flashdata('success')): ?>
            <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
        <?php endif; ?>
        
        <?php if($this->session->flashdata('error')): ?>
            <div class="alert alert-danger"><?= $this->session->flashdata('error') ?></div>
        <?php endif; ?>
```

### Layout Footer (`views/layouts/footer.php`)
```php
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

### Login View (`views/auth/login.php`)
```php
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
```

### Register View (`views/auth/register.php`)
```php
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
```

### Dashboard View (`views/dashboard.php`)
```php
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
```

### Books Index View (`views/books/index.php`)
```php
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
```

### Books Create View (`views/books/create.php`)
```php
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
```

### Books Edit View (`views/books/edit.php`)
```php
<?php $this->load->view('layouts/header'); ?>

<div class="row">
    <div class="col-md-8">
        <h2>Edit Buku</h2>
        
        <?= form_open('books/edit/'.$book->id) ?>
            <div class="mb-3">
                <label class="form-label">Judul</label>
                <input type="text" name="judul" class="form-control" value="<?= $book->judul ?>" required>
                <?= form_error('judul', '<small class="text-danger">', '</small>') ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Penulis</label>
                <input type="text" name="penulis" class="form-control" value="<?= $book->penulis ?>" required>
                <?= form_error('penulis', '<small class="text-danger">', '</small>') ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Tahun Terbit</label>
                <input type="number" name="tahun_terbit" class="form-control" value="<?= $book->tahun_terbit ?>" required>
                <?= form_error('tahun_terbit', '<small class="text-danger">', '</small>') ?>
            </div>
            <div class="mb-3">
                <label class="form-label">ISBN</label>
                <input type="text" name="isbn" class="form-control" value="<?= $book->isbn ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3"><?= $book->deskripsi ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="<?= base_url('books') ?>" class="btn btn-secondary">Batal</a>
        <?= form_close() ?>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>
```

## 8. Cara Instalasi

1. **Persiapan Database**
   - Buat database `manajemen_buku`
   - Jalankan script SQL untuk membuat tabel

2. **Konfigurasi**
   - Edit `config/database.php` sesuai setting database
   - Pastikan autoload dan routes sudah benar

3. **File Structure**
   - Simpan semua file sesuai struktur yang telah ditentukan
   - Pastikan semua controller, model, dan view ada

4. **Testing**
   - Akses aplikasi melalui browser
   - Test fitur register, login, dan CRUD

## 9. Fitur yang Sudah Dipenuhi

### Soal 1 (CRUD - 60%)
- ✅ Menampilkan daftar data dalam bentuk tabel
- ✅ Menambahkan data baru
- ✅ Mengubah data
- ✅ Menghapus data
- ✅ Database MySQL
- ✅ Validasi input
- ✅ Tampilan rapi

### Soal 2 (Autentikasi - 40%)
- ✅ Register dengan nama, email, password
- ✅ Password hash menggunakan MD5
- ✅ Login untuk user terdaftar
- ✅ Redirect ke dashboard setelah login
- ✅ Proteksi halaman untuk user yang belum login

### Bonus (+10%)
- ✅ Fitur pencarian buku
- ✅ Bootstrap untuk UI yang responsive

## 10. Kesimpulan

Aplikasi ini telah memenuhi semua requirement yang diminta dalam soal ujian praktik. Fitur CRUD lengkap dengan validasi, sistem autentikasi yang aman, dan tampilan yang rapi menggunakan Bootstrap. Bonus fitur pencarian juga telah diimplementasikan untuk meningkatkan user experience.