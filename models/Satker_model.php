<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Satker_model extends CI_Model {

	var $table = 'satker';
	var $column_order = array('id_satuan_kerja', 'nama', 'created_at', 'updated_at');
	var $column_search = array('id_satuan_kerja', 'nama', 'created_at', 'updated_at');
	var $order = array('id_satuan_kerja' => 'asc');

	public function __construct()
	{
		parent::__construct();
	}

	private function _get_datatables_query()
	{		
		$this->db_ba->select('a.id_satuan_kerja, a.nama, a.created_at, a.updated_at');
		$this->db_ba->from('satker AS a');

		$i = 0;
	
		foreach ($this->column_search as $item)
		{
			if($_POST['search']['value'])
			{
				
				if($i===0)
				{
					$this->db_ba->group_start();
					$this->db_ba->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->db_ba->or_like($item, $_POST['search']['value']);
				}

				if(count($this->column_search) - 1 == $i)
					$this->db_ba->group_end();
			}
			$i++;
		}
		
		if(isset($_POST['order']))
		{
			$this->db_ba->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order))
		{
			$order = $this->order;
			$this->db_ba->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables()
	{
		$this->_get_datatables_query();
		if($_POST['length'] != -1)
		$this->db_ba->limit($_POST['length'], $_POST['start']);
		$query = $this->db_ba->get();
		return $query->result();
	}

	function count_filtered()
	{
		$this->_get_datatables_query();
		$query = $this->db_ba->get();
		return $query->num_rows();
	}

	public function count_all()
	{
		$this->db_ba->from($this->table);
		return $this->db_ba->count_all_results();
	}

	public function detail($v, $t, $i)
	{
		$this->db_ba->select('*');
		$this->db_ba->from($t);
		$this->db_ba->where($i, $v);
		$query = $this->db_ba->get();
		return $query;
	}

}
