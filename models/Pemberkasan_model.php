<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pemberkasan_model extends CI_Model {

	/*
	 * @author     		RENDY
	 * @author_email    rendyardian199@gmail.com
	 *  KOTA TANGERANG 
	 */

	var $table = 't_history_berkas';	
	
	var $column_order = array(null,'nama_berkas'); //set column field database for datatable orderable
	var $column_search = array('nama_berkas'); //set column field database for datatable searchable just firstname , lastname , address are searchable
	var $order = array('mdd' => 'DESC', 'cdd' => 'DESC'); // default order 
	
	public function __construct()
	{
		parent::__construct();	
	}
	
	private function _get_datatables_query()
	{
		
		$this->db_ba->select('id_history_berkas, id_berkas, nama_berkas, keterangan, file');
		$this->db_ba->from($this->table);
		$this->db_ba->where('id_spk',@$_POST['id_spk']);
		$this->db_ba->where('active',1);
		

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
		$this->db_ba->where('id_spk',@$_POST['id_spk']);
		$this->db_ba->where('active',1);
		return $this->db_ba->count_all_results();
	}

	public function get_by_id($id)
	{
		$this->db_ba->from($this->table);
		$this->db_ba->where('id_history_berkas',$id);
		$query = $this->db_ba->get();

		return $query->row();
	}

	public function get_where_criteria($where='', $field='', $orderby='', $limit=''){
		$this->db_ba->select($field);
		
		if($limit){
			$this->db_ba->limit($limit);
		}
		
		if($orderby){
			$this->db_ba->order_by('sort', 'ASC');
		}
		
		$query = $this->db_ba->get_where($this->table, $where);	
	
		// return $query->row_array(); 
		return $query->result_array(); 
		
	}
	
	public function get_where_criteriav2($table = '', $where='', $field='', $orderby='', $limit=''){
		$this->db_ba->select($field);
		
		if($limit){
			$this->db_ba->limit($limit);
		}
		
		if($orderby){
			$this->db_ba->order_by('sort', 'ASC');
		}
		
		$query = $this->db_ba->get_where($this->table, $where);	
	
		// return $query->row_array(); 
		return $query->result_array(); 
		
	}
	
	
	public function save($id=false,$data=array(),$created=array()) 
	{    
		$where = array('id_history_berkas' => $id, 'active'=> 1);
		
		$this->db_ba
		->where($where)
		->from('t_history_berkas');
		$exist = $this->db_ba->get()->row();	
		
		$id_history_berkas = ($exist) ? $exist->id_history_berkas :  getID('id_history_berkas', 't_history_berkas');
		
		$prop = array('id_history_berkas'=> $id_history_berkas);				
		
		$data = array_merge($prop,$data);	
		
		//check for data set for escaped field
		$data = $this->_check_escaped_data_set($data);	
		
		// check if dataexist already exist
		
		if($exist)			
		{	
			$created = array(
				'mdb'=> $this->session->userdata('id_pegawai'),
				'mdi' => 'web'
			);	
			
			$this->db_ba->where('id_history_berkas' , $id_history_berkas);
			$data = array_merge($data,array('mdd'=> date('Y-m-d H:i:s')),$created);
			$this->db_ba->update('t_history_berkas', $data);
			$result = $id_history_berkas;
		}
		else					
		{
			$data = array_merge($data,array('cdd'=> date('Y-m-d H:i:s')),$created);		
			$this->db_ba->insert('t_history_berkas', $data);
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
	
	public function update($where, $data)
	{
		$this->db_ba->update($this->table, $data, $where);
		return $this->db_ba->affected_rows();
	}
	

}
