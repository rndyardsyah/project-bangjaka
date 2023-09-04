<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Spk_model extends CI_Model {

	/*
	 * @author     		RENDY
	 * @author_email    rendyardian199@gmail.com
	 *  KOTA TANGERANG 
	 */

	var $table = 'm_spk';
	var $table2 = 't_pencairan';
	var $table3 = 'm_penyedia';
	var $column_order = array(null, 'a.no_spk','a.tgl_pekerjaan','a.kode_unor',null); //set column field database for datatable orderable
	var $column_search = array('a.no_spk','a.nama_pekerjaan','a.nama_kegiatan', 'c.nama_penyedia', 'c.nama_perusahaan'); //set column field database for datatable searchable just firstname , lastname , address are searchable
	var $order = array('cdd' => 'desc'); // default order 

	public function __construct()
	{
		parent::__construct();
		// $this->load->database();		
	}

	private function _get_datatables_query()
	{
		
		$this->db_ba->select('
			a.id_spk,
			a.no_spk,
			a.nama_pekerjaan,
			a.id_paket,
			a.id_prokeg_aktif,
			a.nama_kegiatan,
			a.tgl_pekerjaan,
			a.kode_unor,
			a.dpa_skpd,
			a.kode_rek,
			a.pagu,
			a.cdd,
			a.cdb,
			a.mdb,
			c.kategori,
			c.nama_perusahaan,
			c.nama_penyedia,
			a.file,
			c.id_penyedia
		');
		$this->db_ba->from($this->table.' a');
		$this->db_ba->join($this->table2.' b', 'a.id_spk = b.id_spk', 'left');
		$this->db_ba->join($this->table3.' c', 'c.id_penyedia = b.id_penyedia', 'left');
		$this->db_ba->where("a.status", 1);

		if($this->session->userdata('id_akses') == '1')
		{
			$this->db_ba->where(array("a.kode_unor LIKE '".$this->session->userdata('kode_unor')."%'"=> NULL));
		}else{			
			if($this->session->userdata('id_penyedia')){
				$this->db_ba->where("c.id_penyedia", $this->session->userdata('id_penyedia'));			
			}else{
				$this->db_ba->where("(a.cdb ='".$this->session->userdata('id_pegawai')."' or a.mdb ='".$this->session->userdata('id_pegawai')."')");
			}
		}
		
		if(!empty($_POST['data_id']))
		{
			$this->db_ba->or_where_in('a.id_prokeg_aktif', $_POST['data_id']);
		}

		if(!empty($this->column_search)){
			
			// $this->db_ba->or_where('a.mdb', $this->session->userdata('id_pegawai')); 
			

			$i = 0;
			$this->db_ba->group_by('a.id_spk'); 
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
	
		// var_dump($this->db_ba->last_query());
		// exit;
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
	
	public function save_v2($data, $table='m_spk')
	{
		$this->db->insert($table, $data);
		return $this->db->insert_id();
	}
	
	public function save($id=false,$data=array(),$created=array()) 
	{    
		// $where = array('id_spk' => $id);
		// $this->db_ba
		// ->where($where)
		// ->from($this->table);
		// $exist = $this->db_ba->get()->row();	
				
		// $id_spk = ($exist) ? $id :  getID('id_spk', $this->table);
		
		// $prop = array('id_spk'=> $id_spk);				
		
		// $data = array_merge($prop,$data);	
		
		// //check for data set for escaped field
		// $data = $this->_check_escaped_data_set($data);	
		
		// check if dataexist already exist
		if($id)			
		{	
			$this->db_ba->where('id_spk' , $id);
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
		$query = $this->db_ba->get();

		return $query->row();
	}
	
	public function update($where, $data, $table='m_spk')
	{
		$this->db_ba->update($table, $data, $where);
		return $this->db_ba->affected_rows();
	}

}