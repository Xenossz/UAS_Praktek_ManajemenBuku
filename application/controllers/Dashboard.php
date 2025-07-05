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