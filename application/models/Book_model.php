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