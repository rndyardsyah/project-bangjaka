<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

	/*
	 * @author     		RENDY
	 * @author_email    rendyardian199@gmail.com
	 *  KOTA TANGERANG 
	 */

	var $table = 'user_akses';
	var $table2 = 'm_akses';
	var $column_order = array(null, 'nip_baru','nama_user','nama_akses', 'status'); //set column field database for datatable orderable
	var $column_search = array('a.nip_baru','a.nama_user','b.nama_akses'); //set column field database for datatable searchable just firstname , lastname , address are searchable
	var $order = array('b.cdd' => 'desc'); // default order 

	public function __construct()
	{
		parent::__construct();
		// $this->load->database();		
	}

	private function _get_datatables_query()
	{
		
		$this->db_ba->select('a.id, a.nip_baru, IF(a.nama_user is null, a.username, a.nama_user) as nama_user, b.nama_akses, IF(a.status = "1", "AKTIF", "NON AKTIF") as status');
		$this->db_ba->from($this->table.' a');
		$this->db_ba->join($this->table2.' b', 'a.id_akses = b.id_akses');
		// $this->db_ba->where("a.status", 1);
		
		if(!empty($this->session->userdata('kode_unor'))){
			$this->db_ba->like('a.kode_unor', $this->session->userdata('kode_unor'), 'after');
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
		$where = array('id' => $id);
		$this->db_ba
		->where($where)
		->from($this->table);
		$exist = $this->db_ba->get()->row();	
				
		$id_user_akses = ($exist) ? $id :  getID('id', $this->table);
		
		$prop = array('id'=> $id_user_akses);				
		
		$data = array_merge($prop,$data);	
		
		//check for data set for escaped field
		$data = $this->_check_escaped_data_set($data);	
		
		// check if dataexist already exist
		if($exist)			
		{	
			$this->db_ba->where('id' , $id_user_akses);
			$data = array_merge($data,array('mdd'=> date('Y-m-d H:i:s')),$created);
			$this->db_ba->update($this->table, $data);
			$result = $id_user_akses;
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
		$this->db_ba->where('id',$id);
		$query = $this->db_ba->get();

		return $query->row();
	}
	
	public function get_by_id_pegawai($id, $field = '*')
	{
		$this->db_ba->select($field);
		$this->db_ba->from($this->table);
		$this->db_ba->where('id_pegawai',$id);
		$this->db_ba->where('status',1);
		$query = $this->db_ba->get();

		return $query->row();
	}
	
	public function get_by_id_penyedia($id, $field = '*')
	{
		$this->db_ba->select($field);
		$this->db_ba->from($this->table);
		$this->db_ba->where('id_penyedia',$id);
		$this->db_ba->where('status',1);
		$query = $this->db_ba->get();

		return $query->row();
	}
	
	public function get_by_email($id, $field = '*')
	{
		$this->db_ba->select($field);
		$this->db_ba->from($this->table);
		$this->db_ba->where('email',$id);
		$this->db_ba->where('status',1);
		$query = $this->db_ba->get();

		return $query->row();
	}
	
	public function update($where, $data)
	{
		$this->db_ba->update($this->table, $data, $where);
		return $this->db_ba->affected_rows();
	}
	
	public function delete_by_id($id)
	{
		$this->db_ba->where('id_penyedia', $id);
		$this->db_ba->delete('user_akses');
	}

}
