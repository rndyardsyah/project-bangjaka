<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rincian_model extends CI_Model {

	/*
	 * @author     		RENDY
	 * @author_email    rendyardian199@gmail.com
	 *  KOTA TANGERANG 
	 */

	var $table = 'rincian_detail_spk';
	var $column_order = array(null, 'no_spk','tgl_pekerjaan','kode_unor',null); //set column field database for datatable orderable
	var $column_search = array('no_spk','nama_pekerjaan','nama_kegiatan'); //set column field database for datatable searchable just firstname , lastname , address are searchable
	var $order = array('cdd' => 'desc'); // default order 

	public function __construct()
	{
		parent::__construct();
		// $this->load->database();		
	}

	private function _get_datatables_query()
	{
		
		$this->db_ba->from($this->table);
		$this->db_ba->where("status", 1);

		$i = 0;
	
		foreach ($this->column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{
				
				if($i===0) // first loop
				{
					$this->db_ba->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					$this->db_ba->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->db_ba->or_like($item, $_POST['search']['value']);
				}

				if(count($this->column_search) - 1 == $i) //last loop
					$this->db_ba->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
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
		$this->db_ba->where("status", 1);
		return $this->db_ba->count_all_results();
	}
	
	public function save($id=false,$data=array(),$created=array()) 
	{    
		$where = array('id' => $id);
		$this->db_ba
		->where($where)
		->from($this->table);
		$exist = $this->db_ba->get()->row();	
				
		$id = ($exist) ? $id :  getID('id', $this->table);
		
		$prop = array('id'=> $id);				
		
		$data = array_merge($prop,$data);	
		
		//check for data set for escaped field
		$data = $this->_check_escaped_data_set($data);	
		
		// check if dataexist already exist
		if($exist)			
		{	
			$this->db_ba->where('id', $id);
			$this->db_ba->where('id_prokeg_sub_hir' , $data['id_prokeg_sub_hir']);
			$data = array_merge($data,array('mdd'=> date('Y-m-d H:i:s')),$created);
			$this->db_ba->update($this->table, $data);
			$result = $id;
		}
		else					
		{
			$data = array_merge($data,array('cdd'=> date('Y-m-d H:i:s')),$created);		
			$this->db_ba->insert($this->table, $data);
			$result = $this->db_ba->insert_id();
		}
		
		return $result;
	}
	
	public function _check_escaped_data_set($data=array()) 
	{
		foreach($data as $key => $value)
		{
			if(is_array($value))
			{
				$escaped = $value;
				unset($data[$key]);
				
				foreach($escaped as $field => $val)
				{
					if($key == 'date')
					{
						$this->db_ba->set($field,"STR_TO_DATE('".$val."','%d-%m-%Y')",false);
					}else
					{
						$this->db_ba->set($field,$val,false);
					}
				}
			}
		}
		return $data;
	}	
	
	public function get_by_id($id, $field = '*')
	{
		$this->db_ba->select($field);
		$this->db_ba->from($this->table);
		$this->db_ba->where('id_spk',$id);
		$this->db_ba->where('status',1);
		$query = $this->db_ba->get();

		return $query->row();
	}
	
	public function get_cek_history($id_prokeg_aktif='', $id_prokeg_sub_hir = '')
	{
		$this->db_ba->select('id_prokeg_sub_hir');
		$this->db_ba->from($this->table);
		$this->db_ba->where('id_prokeg_aktif',$id_prokeg_aktif);
		$this->db_ba->where('id_prokeg_sub_hir',$id_prokeg_sub_hir);
		$this->db_ba->where('status',1);
		return $this->db_ba->count_all_results();
	}
	
	public function get_cek_history_by_id($id_prokeg_aktif='', $id_prokeg_sub_hir = '', $id_spk='')
	{
		$this->db_ba->select('id_prokeg_sub_hir');
		$this->db_ba->from($this->table);
		$this->db_ba->where('id_prokeg_aktif',$id_prokeg_aktif);
		$this->db_ba->where('id_prokeg_sub_hir',$id_prokeg_sub_hir);
		$this->db_ba->where('id_spk',$id_spk);
		$this->db_ba->where('status',1);
		return $this->db_ba->count_all_results();
	}
	
	public function get_by_id_multi($id, $field = '*')
	{
		$this->db_ba->select($field);
		$this->db_ba->from($this->table);
		$this->db_ba->where('id_spk',$id);
		$this->db_ba->where('status',1);
		if(date('Y') >= '2021')
		{
			$this->db_ba->where('YEAR(cdd)', date('Y'));
		}
		$query = $this->db_ba->get();

		return $query->result_array();
	}
	
	public function update($where, $data)
	{
		$this->db_ba->update($this->table, $data, $where);
		return $this->db_ba->affected_rows();
	}
		
	function get_where($where='', $field='*'){
		$this->db_ba->select($field);
		
		$query = $this->db_ba->get_where($this->table, $where);
		return $query->result_array(); 
	}
	
	public function delete_by_id($id)
	{
		$this->db_ba->where('id_spk', $id);
		$this->db_ba->delete($this->table);
	}
}
