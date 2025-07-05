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