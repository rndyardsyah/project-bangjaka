<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pembayaran_model extends CI_Model {

	/*
	 * @author     		RENDY
	 * @author_email    rendyardian199@gmail.com
	 *  KOTA TANGERANG 
	 */

	var $table = 't_pembayaran';
	var $table2 = 't_hasil_pekerjaan';
	var $table3 = 't_pencairan';
	var $column_order = array(null, 'nama_pekerjaan', 'tgl_permohonan_pembayaran'); //set column field database for datatable orderable
	var $column_search = array('no_permohonan_pembayaran', 'nama_pekerjaan', 'pekerjaan_termin', 'nama_kegiatan', 'nama_penyedia'); //set column field database for datatable searchable just firstname , lastname , address are searchable
	var $order = array('cdd' => 'desc'); // default order 

	public function __construct()
	{
		parent::__construct();
		// $this->load->database();		
	}

	private function _get_datatables_query()
	{
		if($this->session->userdata('id_penyedia')){
			$wheres = 'WHERE `t_pencairan`.`id_penyedia` = "'.$this->session->userdata('id_penyedia').'" AND t_pencairan.status ="1"';		
			$wheres2 = '';		
		}else{
			$wheres = 'WHERE t_pencairan.status ="1"';
			$wheres2 = 'AND (`t_pembayaran`.`cdb` = "'.$this->session->userdata('id_pegawai').'" or `t_pembayaran`.`mdb` = "'.$this->session->userdata('id_pegawai').'")';
		}
		
		$this->db_ba->from('(
				SELECT
				t_gab_pembayaran.*,
				m_spk.nama_pekerjaan,
				t_pencairan.pekerjaan_termin,
				m_spk.nama_kegiatan,
				t_pencairan.nama_penyedia
				FROM
				(
					SELECT 
					t_pembayaran.id_pembayaran,
					t_pembayaran.no_permohonan_pembayaran,
					t_pembayaran.tgl_permohonan_pembayaran,
					t_pembayaran.no_ba_pembayaran,
					t_pembayaran.tgl_ba_pembayaran,
					t_pembayaran.nota_dinas_pencairan,
					t_pembayaran.tgl_nota_dinas_pencairan,
					t_pembayaran.cdd,
					"" termin,
					t_hasil_pekerjaan.id_pencairan
					FROM `t_pembayaran`
					JOIN (SELECT * FROM t_hasil_pekerjaan GROUP BY t_hasil_pekerjaan.id_pembayaran ) as t_hasil_pekerjaan ON t_hasil_pekerjaan.id_pembayaran = t_pembayaran.id_pembayaran
					WHERE `t_pembayaran`.`status` = 1					
					'.$wheres2.'
					ORDER BY `t_pembayaran`.`cdd` DESC
				) as t_gab_pembayaran
				JOIN t_pencairan ON t_pencairan.id_pencairan = t_gab_pembayaran.id_pencairan
				JOIN m_spk ON m_spk.id_spk = t_pencairan.id_spk
				'.$wheres.'
			) as xz
		');

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
		// $where = array('id_pembayaran' => $id);
		// $this->db_ba
		// ->where($where)
		// ->from($this->table);
		// $exist = $this->db_ba->get()->row();	
				
		// $id_pembayaran = ($exist) ? $id :  getID('id_pembayaran', $this->table);
		
		// $prop = array('id_pembayaran'=> $id_pembayaran);				
		
		// $data = array_merge($prop,$data);	
		
		// //check for data set for escaped field
		// $data = $this->_check_escaped_data_set($data);	
		
		// check if dataexist already exist
		if($id)			
		{	
			$this->db_ba->where('id_pembayaran' , $id);
			$data = array_merge($data,array('mdd'=> date('Y-m-d H:i:s')),$created);
			$this->db_ba->update($this->table, $data);
			$result = $id;
		}
		else					
		{
			$data = array_merge($data,array('cdd'=> date('Y-m-d H:i:s')),$created);	
			// print_r($data);
			$this->db_ba->insert($this->table,$data);
			
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
		$this->db_ba->where('id_pembayaran',$id);
		$query = $this->db_ba->get();

		return $query->row();
	}
	
	public function update($where, $data)
	{
		$this->db_ba->update($this->table, $data, $where);
		return $this->db_ba->affected_rows();
	}

}
