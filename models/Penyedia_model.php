<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Penyedia_model extends CI_Model {

	/*
	 * @author     		RENDY
	 * @author_email    rendyardian199@gmail.com
	 *  KOTA TANGERANG 
	 */

	var $table = 'm_penyedia';
	var $column_order = array(null, 'nama_penyedia', 'nama_kategori'); //set column field database for datatable orderable
	var $column_search = array('nama_penyedia','alamat','npwp','bank','no_rekening_penyedia', 'atas_nama_rekening','cabang_bank','kode_unor', 'nama_kategori'); //set column field database for datatable searchable just firstname , lastname , address are searchable
	var $order = array('cdd' => 'desc'); // default order 

	public function __construct()
	{
		parent::__construct();
		// $this->load->database();		
	}

	private function _get_datatables_query()
	{		
		// $this->db_ba->from('(SELECT a.*, IF(kategori = 1, "Perusahaan", "Perorangan") as nama_kategori FROM m_penyedia a WHERE a.status = "1" AND a.kode_unor = "'.substr($this->session->userdata('kode_unor'),0,5).'") z');
		$this->db_ba->select('z.*');
		$this->db_ba->from('(SELECT a.*, IF(kategori = 1, "Perusahaan", "Perorangan") as nama_kategori FROM m_penyedia a ) z');
		$this->db_ba->where("z.status", 1);
		if($this->session->userdata('id_penyedia')){
			$this->db_ba->where("z.id_penyedia", $this->session->userdata('id_penyedia'));			
		}else{
			$this->db_ba->where("z.kode_unor", substr($this->session->userdata('kode_unor'),0,5));
		}

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
		$where = array('id_penyedia' => $id);
		$this->db_ba
		->where($where)
		->from($this->table);
		$exist = $this->db_ba->get()->row();	
				
		$id_penyedia = ($exist) ? $id :  getID('id_penyedia', $this->table);
		
		$prop = array('id_penyedia'=> $id_penyedia);				
		
		$data = array_merge($prop,$data);	
		
		//check for data set for escaped field
		$data = $this->_check_escaped_data_set($data);	
		
		// check if dataexist already exist
		
		if($exist)			
		{	
			$this->db_ba->where('id_penyedia' , $id_penyedia);
			$data = array_merge($data,array('mdd'=> date('Y-m-d H:i:s')),$created);
			$this->db_ba->update($this->table, $data);
			$result = $id_penyedia;
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
		$this->db_ba->where('id_penyedia',$id);
		$query = $this->db_ba->get();

		return $query->row();
	}
	
	public function update($where, $data)
	{
		$this->db_ba->update($this->table, $data, $where);
		return $this->db_ba->affected_rows();
	}

}
