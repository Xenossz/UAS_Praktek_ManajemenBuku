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