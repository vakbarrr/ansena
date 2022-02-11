<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class M_admin extends CI_Model {

    
    var $table = 'barang';
    var $column_order = array('nama', 'stok', 'harga', 'tanggal_masuk', null);
    var $column_search = array('nama', 'stok', 'harga', 'tanggal_masuk');
    var $order = array('id' => 'asc');

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_rows()
    {
        $this->db->from($this->table);
        $this->db->order_by('nama', 'ASC');
        $query = $this->db->get();

        return $query->result();
    }

    private function _get_datatables_query()
    {
        $this->db->from($this->table);
        $i = 0;

        foreach ($this->column_search as $item) {
            if ($_POST['search']['value']) {

                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }

                if (count($this->column_search) - 1 == $i)
                    $this->db->group_end();
            }
            $i++;
        }

        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_search[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables()
    {
        $this->_get_datatables_query();
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all()
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    public function get_by_id($id)
    {
        $this->db->from($this->table);
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    public function save($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($where, $data)
    {
        $this->db->update($this->table, $data, $where);
        return $this->db->affected_rows();
    }

    public function delete_by_id($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    public function total_users()
    {
        $this->db->select('COUNT(id) AS tot_users');
        $this->db->from('user');
        $query = $this->db->get();
        $data = $query->row();
        return $data;
    }

    public function total_barang()
    {
        $this->db->select('COUNT(id) AS tot_barang');
        $this->db->from('barang');
        $query = $this->db->get();
        $data = $query->row();
        return $data;
    }

}

/* End of file M_admin.php */
