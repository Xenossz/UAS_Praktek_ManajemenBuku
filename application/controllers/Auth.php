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