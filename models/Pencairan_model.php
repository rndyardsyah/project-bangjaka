<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pencairan_model extends CI_Model {

	/*
	 * @author     		RENDY
	 * @author_email    rendyardian199@gmail.com
	 *  KOTA TANGERANG 
	 */

	var $table = 't_pencairan';
	var $table2 = 't_hasil_pekerjaan';
	var $table3 = 'm_spk';
	var $table4 = 'm_penyedia';
	var $column_order = array(null, 'nama_penyedia','nama_pekerjaan',null); //set column field database for datatable orderable
	var $column_search = array('nama_penyedia','nama_kegiatan','nama_pekerjaan','nama_perusahaan'); //set column field database for datatable searchable just firstname , lastname , address are searchable
	var $order = array('cdd' => 'desc'); // default order 

	public function __construct()
	{
		parent::__construct();
		// $this->load->database();		
	}

	private function _get_datatables_query()
	{
		$or_where_in = '';
		
		if(!empty($_POST['data_id']))
		{
			$txtnew = '';
			$nox = 1;
			foreach($_POST['data_id'] as $rsx)
			{
				$txtnew .= $rsx;
				if($nox++ != count($_POST['data_id'])){
					$txtnew .= ',';
				}
			}
			$or_where_in = 'OR ( `b`.`id_prokeg_aktif` IN ('.$txtnew.') AND a.status = "1")';
		}

		$wherex = '';
		if($this->session->userdata('id_penyedia')){
			$wherex = 'c.id_penyedia = "'.$this->session->userdata('id_penyedia').'"';	
		}else{
			$wherex = "(b.cdb = '".$this->session->userdata('id_pegawai')."' or b.mdb = '".$this->session->userdata('id_pegawai')."')";
		}

		$this->db_ba->select('*');
		$this->db_ba->from('
			(
				SELECT
					`a`.`id_pencairan`,
					`a`.`pekerjaan_termin`,
					`a`.`nominal_bayar`,
					`a`.`nominal_bayar_terbilang`,
					`a`.`id_penyedia`,
					`a`.`nama_penyedia`,
					`a`.`alamat`,
					`a`.`bank`,
					`a`.`no_rekening_penyedia`,
					`a`.`cabang_bank`,
					`a`.`atas_nama_rekening`,
					`a`.`npwp`,
					`b`.`no_spk`,
					`b`.`nama_pekerjaan`,
					`b`.`nama_kegiatan`,
					`c`.`kategori`,
					`c`.`nama_perusahaan`,
					`c`.`jabatan`,
					`b`.`cdb`,
					`b`.`mdb`,
					`a`.`cdd`
				FROM
					'.$this->table.' a
				JOIN (
					SELECT
						*
					FROM
						'.$this->table3.'
					WHERE
						status = "1"
				) b ON `a`.`id_spk` = `b`.`id_spk`
				JOIN '.$this->table4.' c ON `a`.`id_penyedia` = `c`.`id_penyedia`
				WHERE '.$wherex.'
				AND `a`.`status` = 1
				'.$or_where_in.'
			) as z
		');
		// $this->db_ba->from($this->table.' a');
		// $this->db_ba->join('(SELECT * FROM '.$this->table3.' WHERE status = "1") b', 'a.id_spk = b.id_spk');
		// $this->db_ba->join($this->table4.' c', 'a.id_penyedia = c.id_penyedia');
		
		// if($this->session->userdata('id_penyedia')){
		// 	$this->db_ba->where("c.id_penyedia", $this->session->userdata('id_penyedia'));			
		// }else{
		// 	$this->db_ba->where("(b.cdb = '".$this->session->userdata('id_pegawai')."' or b.mdb = '".$this->session->userdata('id_pegawai')."')");
		// }
		
		// $this->db_ba->where("a.status", 1);
		
		// if(!empty($_POST['data_id']))
		// {
		// 	$this->db_ba->or_where_in('b.id_prokeg_aktif', $_POST['data_id']);
		// }

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
		// var_dump($this->db_ba->last_query());exit;
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
	
	public function cek_data_ready($id_penyedia='', $id_spk='')
	{
		$this->db_ba->from($this->table);
		$this->db_ba->where("id_penyedia", $id_penyedia);
		$this->db_ba->where("id_spk", $id_spk);
		$this->db_ba->where("status", 1);
		$query = $this->db_ba->get();
		return $query->num_rows();
	}
	
	public function save($id=false,$data=array(),$created=array()) 
	{    
		// $where = array('id_pencairan' => $id);
		// $this->db_ba
		// ->where($where)
		// ->from($this->table);
		// $exist = $this->db_ba->get()->row();	
				
		// $id_pencairan = ($exist) ? $id :  getID('id_pencairan', $this->table);
		
		// $prop = array('id_pencairan'=> $id_pencairan);				
		
		// $data = array_merge($prop,$data);	
		
		// //check for data set for escaped field
		// $data = $this->_check_escaped_data_set($data);	
		
		// check if dataexist already exist
		if($id)			
		{	
			$this->db_ba->where('id_pencairan' , $id);
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
		$this->db_ba->where('id_pencairan',$id);
		$this->db_ba->where('status',1);
		$query = $this->db_ba->get();

		return $query->row();
	}
	
	public function get_by_id_spk($id, $field = '*')
	{
		$this->db_ba->select($field);
		$this->db_ba->from($this->table);
		$this->db_ba->where('id_spk',$id);
		$this->db_ba->where('status',1);
		$query = $this->db_ba->get();

		return $query->row();
	}
	
	public function get_by_id_v2($id, $field = '*')
	{
		$this->db_ba->select($field);
		$this->db_ba->from($this->table);
		$this->db_ba->where('id_spk',$id);
		$this->db_ba->where('status',1);
		$this->db_ba->order_by('cdd', 'DESC');
		$this->db_ba->limit(1);
		$query = $this->db_ba->get();

		return $query->row();
	}
	
	public function get_by_id_t_hasil_pekerjaan($id, $field = '*')
	{
		$this->db_ba->select($field);
		$this->db_ba->from($this->table2);
		$this->db_ba->where('id_pencairan',$id);
		$query = $this->db_ba->get();

		return $query->result_array();
	}
	
	public function get_by_id_t_hasil_pekerjaan_pembayaran($id, $id_pembayaran, $field = '*')
	{
		$this->db_ba->select($field);
		$this->db_ba->from($this->table2);
		$this->db_ba->where('id_pencairan',$id);
		$this->db_ba->where('id_pembayaran',$id_pembayaran);
		$query = $this->db_ba->get();

		return $query->result_array();
	}
	
	public function update($where, $data)
	{
		$this->db_ba->update($this->table, $data, $where);
		return $this->db_ba->affected_rows();
	}
	
	public function updatev2($where, $data)
	{
		$this->db_ba->update($this->table, $data, $where);
		$this->db_ba->order_by('cdd', 'DESC');
		$this->db_ba->limit(1);
		return $this->db_ba->affected_rows();
	}

}
