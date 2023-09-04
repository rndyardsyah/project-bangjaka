<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Berkas_model extends CI_Model {

	/*
	 * @author     		RENDY
	 * @author_email    rendyardian199@gmail.com
	 *  KOTA TANGERANG 
	 */

	var $table = 'm_berkas';	
	
	var $column_order = array(null,'nama_berkas'); //set column field database for datatable orderable
	var $column_search = array('nama_berkas'); //set column field database for datatable searchable just firstname , lastname , address are searchable
	var $order = array('sort' => 'asc'); // default order 
	
	public function __construct()
	{
		parent::__construct();	
	}
	
	private function _get_datatables_query()
	{
		
		$this->db_ba->select('id_berkas, nama_berkas');
		$this->db_ba->from($this->table);

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
		return $this->db_ba->count_all_results();
	}

	public function get_by_id($id)
	{
		$this->db_ba->from($this->table);
		$this->db_ba->where('id_berkas',$id);
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
	
	
	public function save_t_berkas($id=false,$data=array(),$created=array()) 
	{    
		$where = array('id_berkas' => $id, 'id_hasil_pekerjaan'=> $data['id_hasil_pekerjaan'], 'active'=> 1, 'cdb'=> $this->session->userdata('id_pegawai'));
		$this->db_ba
		->where($where)
		->from('t_berkas');
		$exist = $this->db_ba->get()->row();	
		
		$id_t_berkas = ($exist) ? $exist->id_t_berkas :  getID('id_t_berkas', 't_berkas');
		
		$prop = array('id_t_berkas'=> $id_t_berkas);				
		
		$data = array_merge($prop,$data);	
		
		//check for data set for escaped field
		$data = $this->_check_escaped_data_set($data);	
		
		// check if dataexist already exist
		if($exist)			
		{	
			$this->db_ba->where('id_t_berkas' , $id_t_berkas);
			$data = array_merge($data,array('mdd'=> date('Y-m-d H:i:s')),$created);
			$this->db_ba->update('t_berkas', $data);
			$result = $id_t_berkas;
		}
		else					
		{
			$data = array_merge($data,array('cdd'=> date('Y-m-d H:i:s')),$created);		
			$this->db_ba->insert('t_berkas', $data);
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
	
	public function update($where, $data, $table='m_berkas')
	{
		$this->db_ba->update($table, $data, $where);
		return $this->db_ba->affected_rows();
	}

}
