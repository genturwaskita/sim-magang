<?php
defined('BASEPATH') or exit('No direct script access allowed');

class JabatanModel extends CI_Model
{
    var $table = 'ref_jabatan';
    var $column_order = array('ref_jabatan.id', 'ref_jabatan.nama');
    var $column_search = array('ref_jabatan.id', 'ref_jabatan.nama');
    var $order = array('ref_jabatan.id' => 'DESC');

    private function _get_datatables_query()
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('aktif', '1');
        $this->db->where('dihapus_pada is NULL');

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
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    public function get_datatables()
    {
        $this->_get_datatables_query();
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all()
    {
        $this->db->from($this->table);
        $this->db->where('aktif', '1');
        $this->db->where('dihapus_pada is NULL');
        return $this->db->count_all_results();
    }

    public function daftar_semua($table)
    {
        $query = $this->db->get_where($table, ['aktif' => '1', 'dihapus_pada is NULL']);
        return $query->result_array();
    }

    public function daftar_sebagian($where, $table)
    {
        $query = $this->db->get_where($table, $where);
        return $query->result_array();
    }

    public function detail($where, $table)
    {
        $query = $this->db->get_where($table, $where);
        return $query->row_array();
    }

    public function tambah($data, $table)
    {
        $this->db->insert($table, $data);
    }

    public function ubah($data, $where, $table)
    {
        $this->db->where($where);
        $this->db->update($table, $data);
    }

    public function hapus($where, $table)
    {
        $this->db->where($where);
        $this->db->delete($table);
    }
}
