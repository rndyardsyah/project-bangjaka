<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Paraf_pphp_model extends CI_Model {

	/*
	 * @author     		RENDY
	 * @author_email    rendyardian199@gmail.com
	 *  KOTA TANGERANG 
	 */

	var $table = 't_paraf_pphp';
	var $table2 = 't_pencairan';
	var $table3 = 't_pembayaran';
	var $table4 = 'm_spk';
	var $column_order = array(null, 'nama_penyedia','nama_pekerjaan',null); //set column field database for datatable orderable
	var $column_search = array('nama_penyedia'); //set column field database for datatable searchable just firstname , lastname , address are searchable
	var $order = array('cdd' => 'desc'); // default order 

	public function __construct()
	{
		parent::__construct();
		// $this->load->database();		
	}

	private function _get_datatables_query()
	{
		
		$this->db_ba->select('
			a.id_paraf_pphp,
			a.id_pencairan,
			a.no_srt_penyerahan,
			a.tgl_srt_penyerahan,
			a.termin,
			a.no_bas_penerimaan,
			a.tanggal_bas,
			a.nilai_pekerjaan,
			a.nilai_pekerjaan_terbilang,
			a.pjbtpenerima_nosk,
			a.pjbtpenerima_tglsk,
			a.id_pegawai_pphp,
			a.nip_pegawai_pphp,
			a.nama_pegawai_pphp,
			a.nomenklatur_jabatan_pphp,
			a.id_pegawai_ppk,
			a.nip_pegawai_ppk,
			a.nama_pegawai_ppk,
			a.nomenklatur_jabatan_ppk,
			a.id_pegawai_pptk,
			a.nip_pegawai_pptk,
			a.nama_pegawai_pptk,
			a.nomenklatur_jabatan_pptk,
			a.id_pegawai_bendahara,
			a.nip_pegawai_bendahara,
			a.nama_pegawai_bendahara,
			a.nomenklatur_jabatan_bendahara,
			a.id_pegawai_pengguna_anggaran,
			a.nip_pegawai_pengguna_anggaran,
			a.nama_pegawai_pengguna_anggaran,
			a.nomenklatur_jabatan_pengguna_anggaran,
			a.no_bast,
			a.tgl_bast,
			a.cdd,
			a.id_pembayaran,
			c.nama_pekerjaan,
			b.pekerjaan_termin,
			c.nama_kegiatan,
			c.no_spk,
			c.tgl_pekerjaan,
			b.id_penyedia,
			b.nama_penyedia,
			b.alamat,
			b.nominal_bayar,
			b.nominal_bayar_terbilang,
			b.bank,
			b.no_rekening_penyedia,
			b.atas_nama_rekening,
			b.npwp,
			c.id_spk,
			b.id_prokeg_aktif,
			b.cabang_bank,
			(SELECT t_inbox.paraf FROM t_inbox WHERE t_inbox.id_paraf_pphp = a.id_paraf_pphp ORDER BY t_inbox.cdd DESC LIMIT 1) as paraf,
			a.draft
		');
		$this->db_ba->from($this->table.' a');
		$this->db_ba->join($this->table2.' b', 'a.id_pencairan = b.id_pencairan');
		$this->db_ba->join($this->table4.' c', 'c.id_spk = b.id_spk');
		$this->db_ba->where("a.cdb", $this->session->userdata('id_pegawai'));
		$this->db_ba->where("a.status", 1);

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
		$where = array('id_paraf_pphp' => $id);
		$this->db_ba
		->where($where)
		->from($this->table);
		$exist = $this->db_ba->get()->row();	
				
		$id_paraf_pphp = ($exist) ? $id :  getID('id_paraf_pphp', $this->table);
		
		$prop = array('id_paraf_pphp'=> $id_paraf_pphp);				
		
		$data = array_merge($prop,$data);	
		
		//check for data set for escaped field
		$data = $this->_check_escaped_data_set($data);	
		
		// check if dataexist already exist
		if($exist)			
		{	
			$this->db_ba->where('id_paraf_pphp' , $id_paraf_pphp);
			$data = array_merge($data,array('mdd'=> date('Y-m-d H:i:s')));
			$this->db_ba->update($this->table, $data);
			$result = $id_paraf_pphp;
		}
		else					
		{
			$data = array_merge($data,array('cdd'=> date('Y-m-d H:i:s')));	
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
		$this->db_ba->where('id_hasil_pekerjaan',$id);
		$this->db_ba->where('status',1);
		$query = $this->db_ba->get();

		return $query->result();
	}
	
	public function get_by_idv2($id, $field = '*')
	{
		$this->db_ba->select($field);
		$this->db_ba->from($this->table);
		$this->db_ba->where('id_hasil_pekerjaan',$id);
		$this->db_ba->where('status',1);
		$query = $this->db_ba->get();

		return $query->result_array();
	}
	
	public function get_by_id_detail_old($id, $field = '*')
	{
		$this->db_ba->select($field);
		$this->db_ba->from($this->table);
		$this->db_ba->join($this->table2, $this->table.'.id_pencairan = ' .$this->table2.'.id_pencairan');
		$this->db_ba->join($this->table3, $this->table.'.id_pembayaran = ' .$this->table3.'.id_pembayaran');
		$this->db_ba->where($this->table.'.id_paraf_pphp',$id);
		$query = $this->db_ba->get();
		// var_dump($this->db_ba->last_query());exit;
		return $query->row();
	}
	
	public function get_by_id_detail($id, $field = '*')
	{
		$this->db_ba->select($field);
		$this->db_ba->from($this->table);
		$this->db_ba->join($this->table2, $this->table.'.id_pencairan = ' .$this->table2.'.id_pencairan');
		$this->db_ba->join($this->table3, $this->table.'.id_pembayaran = ' .$this->table3.'.id_pembayaran');
		$this->db_ba->join($this->table4, $this->table2.'.id_spk = ' .$this->table4.'.id_spk');
		$this->db_ba->where($this->table.'.id_pembayaran',$id);
		$query = $this->db_ba->get();
		// var_dump($this->db_ba->last_query());exit;
		return $query->result();
	}
	
	public function get_by_id_detail_hasil_pekerjaan($id, $field = '*')
	{
		$this->db_ba->select($field);
		$this->db_ba->from($this->table);
		$this->db_ba->join($this->table2, $this->table.'.id_pencairan = ' .$this->table2.'.id_pencairan');
		$this->db_ba->join($this->table4, $this->table2.'.id_spk = ' .$this->table4.'.id_spk');
		$this->db_ba->where($this->table.'.id_paraf_pphp',$id);
		$query = $this->db_ba->get();
		
		return $query->row();
	}
	
	public function update($where, $data)
	{
		$this->db_ba->update($this->table, $data, $where);
		return $this->db_ba->affected_rows();
	}
	
	
	public function cek_data_ready($id='')
	{
		$this->db_ba->from($this->table);
		$this->db_ba->where("id_pencairan", $id);
		$this->db_ba->where("status", 1);
		$query = $this->db_ba->get();
		return $query->num_rows();
	}
	
	public function get_data_search($search = '')
	{
		$this->db_ba->select('id_pegawai_pphp, DATE_FORMAT(pjbtpenerima_tglsk, "%m-%d-%Y") as pjbtpenerima_tglsk,, pjbtpenerima_nosk as name');
		$this->db_ba->from($this->table);
		$this->db_ba->like('pjbtpenerima_nosk', $search);
		$query = $this->db_ba->get();
		return $query->result_array();
	}
		
	public function get_where_criteria($where='', $field='', $rest=false){
		$this->db_ba->select($field);
		$query = $this->db_ba->get_where($this->table, $where);
		
		if($rest){
			return $query->result_array(); 
		}else{
			return $query->row_array(); 
		}
	}
	
	public function delete_by_id($id)
	{
		$this->db_ba->where('id_hasil_pekerjaan', $id);
		$this->db_ba->delete($this->table);
	}
	

}
