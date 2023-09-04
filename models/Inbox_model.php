<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inbox_model extends CI_Model {

	/*
	 * @author     		RENDY
	 * @author_email    rendyardian199@gmail.com
	 *  KOTA TANGERANG 
	 */

	var $table = 't_inbox';
	var $column_order = array(null, 'nama_mailfrom','nama_pekerjaan',null); //set column field database for datatable orderable
	var $column_search = array('nama_mailfrom', 'nama_mailto', 'termin', 'nama_perusahaan', 'nama_penyedia', 'nama_pekerjaan', 'json_rup_penyedia'); //set column field database for datatable searchable just firstname , lastname , address are searchable
	var $order = array('tgl_surat' => 'desc'); // default order 

	public function __construct()
	{
		parent::__construct();
		// $this->load->database();		
	}

	private function _get_datatables_query()
	{
		// var_dump($_POST['length']);exit;
		$this->db_ba->from('
			(
					(
						SELECT 
						"" as json_rup_penyedia,
						"" as id_bentuk,
						z.id_inbox,
						z.id_hasil_pekerjaan,
						z.mailto,
						z.nip_mailto,
						z.nama_mailto,
						z.nomenklatur_jabatan_mailto,
						(SELECT x.mailfrom FROM t_inbox x WHERE x.id_hasil_pekerjaan = z.id_hasil_pekerjaan AND x.mailto = "'.$this->session->userdata('id_pegawai').'"  AND x.active = "1" AND x.type = "1" ORDER BY x.cdd DESC LIMIT 1) as mailfrom,
						(SELECT x.nip_mailfrom FROM t_inbox x WHERE x.id_hasil_pekerjaan = z.id_hasil_pekerjaan AND x.mailto = "'.$this->session->userdata('id_pegawai').'"  AND x.active = "1" AND x.type = "1" ORDER BY x.cdd DESC LIMIT 1) as nip_mailfrom,
						(SELECT x.nama_mailfrom FROM t_inbox x WHERE x.id_hasil_pekerjaan = z.id_hasil_pekerjaan AND x.mailto = "'.$this->session->userdata('id_pegawai').'"  AND x.active = "1" AND x.type = "1" ORDER BY x.cdd DESC LIMIT 1) as nama_mailfrom,
						(SELECT x.nomenklatur_jabatan_mailfrom FROM t_inbox x WHERE x.id_hasil_pekerjaan = z.id_hasil_pekerjaan AND x.mailto = "'.$this->session->userdata('id_pegawai').'"  AND x.active = "1" AND x.type = "1" ORDER BY x.cdd DESC LIMIT 1) as nomenklatur_jabatan_mailfrom,
						(SELECT x.status FROM t_inbox x WHERE x.id_hasil_pekerjaan = z.id_hasil_pekerjaan AND x.mailto = "'.$this->session->userdata('id_pegawai').'"  AND x.active = "1" AND x.type = "1" ORDER BY x.cdd DESC LIMIT 1) as status,
						(SELECT x.viewed FROM t_inbox x WHERE x.id_hasil_pekerjaan = z.id_hasil_pekerjaan AND x.mailto = "'.$this->session->userdata('id_pegawai').'"  AND x.active = "1" AND x.type = "1" ORDER BY x.cdd DESC LIMIT 1) as viewed,
						(SELECT x.catatan FROM t_inbox x WHERE x.id_hasil_pekerjaan = z.id_hasil_pekerjaan AND x.mailto = "'.$this->session->userdata('id_pegawai').'"  AND x.active = "1" AND x.type = "1" ORDER BY x.cdd DESC LIMIT 1) as catatan,
						z.notif_text,
						(SELECT x.cdd FROM t_inbox x WHERE x.id_hasil_pekerjaan = z.id_hasil_pekerjaan AND x.mailto = "'.$this->session->userdata('id_pegawai').'"  AND x.active = "1" AND x.type = "1" ORDER BY x.cdd DESC LIMIT 1) as cdd,
						z.cdb,
						z.cdi,
						z.mdd,
						z.mdb,
						z.mdi,
						(SELECT x.paraf FROM t_inbox x WHERE x.id_hasil_pekerjaan = z.id_hasil_pekerjaan AND x.mailto = "'.$this->session->userdata('id_pegawai').'"  AND x.active = "1" AND x.type = "1" ORDER BY x.cdd DESC LIMIT 1) as paraf,
						z.id_pembayaran,
						z.active,
						z.type,
						`a`.`id_pencairan`, 
						`a`.`no_srt_penyerahan`, 
						`a`.`tgl_srt_penyerahan`, 
						CONCAT("Termin ",`a`.`termin`) as termin,  
						`a`.`no_bas_penerimaan`, 
						`a`.`tanggal_bas`, 
						`a`.`nilai_pekerjaan`, 
						`a`.`nilai_pekerjaan_terbilang`, 
						`a`.`pjbtpenerima_nosk`, 
						`a`.`pjbtpenerima_tglsk`, 
						`a`.`nama_pegawai_pphp`, 
						`a`.`nomenklatur_jabatan_pphp`, 
						`a`.`nama_pegawai_ppk`, 
						`a`.`nomenklatur_jabatan_ppk`, 
						`a`.`nama_pegawai_pptk`, 
						`a`.`nomenklatur_jabatan_pptk`, 
						`a`.`nama_pegawai_bendahara`, 
						`a`.`nomenklatur_jabatan_bendahara`, 
						`a`.`nama_pegawai_pengguna_anggaran`, 
						`a`.`nomenklatur_jabatan_pengguna_anggaran`, 
						`a`.`file_pekerjaan`, 
						`d`.`no_spk`, 
						`d`.`nama_pekerjaan`, 
						`d`.`nama_kegiatan`, 
						`a`.tgl_srt_penyerahan as tgl_surat,
						`b`.pekerjaan_termin,
						`e`.kategori,
						`e`.nama_perusahaan,
						`b`.nama_penyedia,
						(SELECT ww.paraf FROM t_inbox ww WHERE ww.id_hasil_pekerjaan = z.id_hasil_pekerjaan AND ww.mailfrom = "'.$this->session->userdata('id_pegawai').'" AND ww.`active` = "1" AND ww.type = "1" ORDER BY ww.cdd DESC, ww.id_inbox DESC LIMIT 1) as paraf_next, 
						CONCAT("Termin ",`a`.`termin`) as termin1					
						FROM t_inbox z
						JOIN `t_hasil_pekerjaan` `a` ON `z`.`id_hasil_pekerjaan` = `a`.`id_hasil_pekerjaan`
						JOIN `t_pencairan` `b` ON `a`.`id_pencairan` = `b`.`id_pencairan`
						JOIN `m_spk` `d` ON `d`.`id_spk` = `b`.`id_spk`
						JOIN `m_penyedia` `e` ON `e`.`id_penyedia` = `b`.`id_penyedia`
						WHERE `z`.`active` = "1" AND `z`.mailto = "'.$this->session->userdata('id_pegawai').'"  AND z.type = "1" AND d.status = "1"
						GROUP BY `z`.`mailto`, `z`.`id_hasil_pekerjaan`
						ORDER BY `cdd` DESC
					)
					UNION ALL
					(						
						SELECT xy.*, GROUP_CONCAT(CONCAT("Termin ", xy.termin) ORDER by xy.termin ASC) termin1
						FROM (
							SELECT 
							"" as json_rup_penyedia,
							"" as id_bentuk,
							z.id_inbox,
							z.id_hasil_pekerjaan,
							z.mailto,
							z.nip_mailto,
							z.nama_mailto,
							z.nomenklatur_jabatan_mailto,
							z.mailfrom,
							z.nip_mailfrom,
							z.nama_mailfrom,
							z.nomenklatur_jabatan_mailfrom,
							z.status,
							z.viewed,
							z.catatan,
							z.notif_text,
							z.cdd,
							z.cdb,
							z.cdi,
							z.mdd,
							z.mdb,
							z.mdi,
							z.paraf,
							z.id_pembayaran,
							z.active,
							z.type,
							`a`.`id_pencairan`, 
							`a`.`no_srt_penyerahan`, 
							`a`.`tgl_srt_penyerahan`, 
							`a`.`termin` as `termin`, 
							`a`.`no_bas_penerimaan`, 
							`a`.`tanggal_bas`, 
							`a`.`nilai_pekerjaan`, 
							`a`.`nilai_pekerjaan_terbilang`, 
							`a`.`pjbtpenerima_nosk`, 
							`a`.`pjbtpenerima_tglsk`, 
							`a`.`nama_pegawai_pphp`, 
							`a`.`nomenklatur_jabatan_pphp`, 
							`a`.`nama_pegawai_ppk`, 
							`a`.`nomenklatur_jabatan_ppk`, 
							`a`.`nama_pegawai_pptk`, 
							`a`.`nomenklatur_jabatan_pptk`, 
							`a`.`nama_pegawai_bendahara`, 
							`a`.`nomenklatur_jabatan_bendahara`, 
							`a`.`nama_pegawai_pengguna_anggaran`, 
							`a`.`nomenklatur_jabatan_pengguna_anggaran`, 
							`a`.`file_pekerjaan`, 
							`d`.`no_spk`, 
							`d`.`nama_pekerjaan`, 
							`d`.`nama_kegiatan`, 							
							IF(c.tgl_permohonan_pembayaran is null, c.cdd, c.tgl_permohonan_pembayaran) as tgl_surat,
							`b`.pekerjaan_termin,	
							`e`.kategori,
							`e`.nama_perusahaan,					
							`b`.nama_penyedia,
							(SELECT ww.paraf FROM t_inbox ww WHERE ww.id_pembayaran = z.id_pembayaran AND ww.mailfrom = "'.$this->session->userdata('id_pegawai').'" AND ww.`active` = "1" ORDER BY ww.cdd DESC, ww.id_inbox DESC LIMIT 1) as paraf_next
							FROM (SELECT * FROM t_inbox WHERE mailto = "'.$this->session->userdata('id_pegawai').'" ORDER BY cdd DESC) as z
							JOIN `t_pembayaran` `c` ON `z`.`id_pembayaran` = `c`.`id_pembayaran`
							JOIN `t_hasil_pekerjaan` `a` ON `a`.`id_pembayaran` = `c`.`id_pembayaran`
							JOIN `t_pencairan` `b` ON `a`.`id_pencairan` = `b`.`id_pencairan`
							JOIN `m_spk` `d` ON `d`.`id_spk` = `b`.`id_spk`
							JOIN `m_penyedia` `e` ON `e`.`id_penyedia` = `b`.`id_penyedia`
							WHERE `z`.`active` = "1"  AND z.type = "1"
							GROUP BY `z`.`mailto`, `z`.`id_pembayaran`, `a`.`termin`
							ORDER BY `cdd` DESC
						) as xy
						GROUP BY xy.id_inbox
					)
					UNION ALL
					(
						SELECT 
							(SELECT json_rup_penyedia FROM t_surat WHERE id_surat = (SELECT id_parent FROM t_surat WHERE id_surat = z.id_hasil_pekerjaan)) as json_rup_penyedia,
							(SELECT id_bentuk FROM t_surat WHERE id_surat = z.id_hasil_pekerjaan) as id_bentuk,
							z.id_inbox,
							z.id_hasil_pekerjaan,
							z.mailto,
							z.nip_mailto,
							z.nama_mailto,
							z.nomenklatur_jabatan_mailto,
							z.mailfrom,
							z.nip_mailfrom,
							z.nama_mailfrom,
							z.nomenklatur_jabatan_mailfrom,
							z.status,
							(SELECT x.viewed FROM t_inbox x WHERE x.id_hasil_pekerjaan = z.id_hasil_pekerjaan AND x.mailto = "'.$this->session->userdata('id_pegawai').'"  AND x.active = "1" AND x.type = "2" ORDER BY x.viewed ASC LIMIT 1) as viewed,
							z.catatan,
							z.notif_text,
							"" as cdd,
							z.cdb,
							z.cdi,
							z.mdd,
							z.mdb,
							z.mdi,
							"" as paraf,
							z.id_pembayaran,
							z.active,
							z.type,
							"" as id_pencairan, 
							"" as no_srt_penyerahan, 
							"" as tgl_srt_penyerahan, 
							"" as termin,  
							"" as no_bas_penerimaan, 
							"" as tanggal_bas, 
							"" as nilai_pekerjaan, 
							"" as nilai_pekerjaan_terbilang, 
							"" as pjbtpenerima_nosk, 
							"" as pjbtpenerima_tglsk, 
							a.penandatangan as nama_pegawai_pphp, 
							a.paraf as nomenklatur_jabatan_pphp, 
							a.tembusan as nama_pegawai_ppk, 
							"" as nomenklatur_jabatan_ppk, 
							"" as nama_pegawai_pptk, 
							"" as nomenklatur_jabatan_pptk, 
							"" as nama_pegawai_bendahara, 
							"" as nomenklatur_jabatan_bendahara, 
							"" as nama_pegawai_pengguna_anggaran, 
							"" as nomenklatur_jabatan_pengguna_anggaran, 
							"" as file_pekerjaan, 
							"" as no_spk, 
							a.perihal as nama_pekerjaan, 
							a.bentuk_surat as nama_kegiatan, 
							a.tanggal_surat as tgl_surat,
							"" as pekerjaan_termin,
							"" as kategori,
							(SELECT IF(f.id_parent is null, f.perihal, (SELECT IF(c.id_parent is null, c.perihal, "") FROM t_surat c WHERE c.id_surat = f.id_parent)) FROM t_surat f WHERE f.id_surat = a.id_parent) as nama_perusahaan,
							a.isi_surat as nama_penyedia,
							"" as paraf_next,					
							"" as termin1					
						FROM t_inbox z
						JOIN (SELECT * FROM t_surat WHERE active = "1" ) a ON z.id_hasil_pekerjaan = a.id_surat
						WHERE z.active = "1" AND z.mailto = "'.$this->session->userdata('id_pegawai').'"  AND z.type = "2"
						GROUP BY z.id_hasil_pekerjaan
						ORDER BY a.cdd DESC
					)
			)  as xc
		');				
		
		$i = 0;
	
		if(@$_POST['status'] == 1){
			$this->db_ba->where("viewed", null);
		}
		
		if(@$_POST['status'] == 2){
			$this->db_ba->where("paraf_next", null);
			$this->db_ba->where("viewed is NOT NULL", NULL, FALSE);
		}
		
		if(@$_POST['status'] == 3){
			$this->db_ba->where("paraf_next", 1);
			// $this->db_ba->where("viewed is NOT NULL", NULL, FALSE);
		}
		
		if(@$_POST['status'] == 4){
			$this->db_ba->where("paraf_next", 0);
		}
		
		foreach ($this->column_search as $item) // loop column 
		{
			if(@$_POST['search']['value']) // if datatable send POST for search
			{
				
				if($i===0) // first loop
				{
					$this->db_ba->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					$this->db_ba->like($item, @$_POST['search']['value']);
				}
				else
				{
					$this->db_ba->or_like($item, @$_POST['search']['value']);
				}

				if(count($this->column_search) - 1 == $i) //last loop
					$this->db_ba->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db_ba->order_by($this->column_order[@$_POST['order']['0']['column']], @$_POST['order']['0']['dir']);
		} 
		else if(isset($this->order))
		{			
			$this->db_ba->order_by('viewed IS NULL DESC', NULL, FALSE);
			$this->db_ba->order_by('tgl_surat', 'DESC');
			$this->db_ba->order_by('cdd', 'DESC');
		}
	}
	
	private function _get_datatables_query_notif()
	{
		$this->db_ba->from('
			(
					(
						SELECT 
						z.id_inbox,
						z.id_hasil_pekerjaan,
						z.mailto,
						z.nip_mailto,
						z.nama_mailto,
						z.nomenklatur_jabatan_mailto,
						z.mailfrom,
						z.nip_mailfrom,
						z.nama_mailfrom,
						z.nomenklatur_jabatan_mailfrom,
						z.status,
						z.viewed,
						z.catatan,
						z.notif_text,
						z.cdd,
						z.cdb,
						z.cdi,
						z.mdd,
						z.mdb,
						z.mdi,
						z.paraf,
						z.id_pembayaran,
						z.active,
						`a`.`id_pencairan`, 
						`a`.`no_srt_penyerahan`, 
						`a`.`tgl_srt_penyerahan`, 
						CONCAT("Termin ",`a`.`termin`) as termin,  
						`a`.`no_bas_penerimaan`, 
						`a`.`tanggal_bas`, 
						`a`.`nilai_pekerjaan`, 
						`a`.`nilai_pekerjaan_terbilang`, 
						`a`.`pjbtpenerima_nosk`, 
						`a`.`pjbtpenerima_tglsk`, 
						`a`.`nama_pegawai_pphp`, 
						`a`.`nomenklatur_jabatan_pphp`, 
						`a`.`nama_pegawai_ppk`, 
						`a`.`nomenklatur_jabatan_ppk`, 
						`a`.`nama_pegawai_pptk`,
						`a`.`nomenklatur_jabatan_pptk`, 
						`a`.`nama_pegawai_bendahara`, 
						`a`.`nomenklatur_jabatan_bendahara`, 
						`a`.`nama_pegawai_pengguna_anggaran`, 
						`a`.`nomenklatur_jabatan_pengguna_anggaran`, 
						`d`.`no_spk`, 
						`d`.`nama_pekerjaan`, 
						`d`.`nama_kegiatan`, 
						`a`.cdd as tgl_surat,
						(SELECT ww.paraf FROM t_inbox ww WHERE ww.id_hasil_pekerjaan = z.id_hasil_pekerjaan AND ww.mailfrom = "'.$this->session->userdata('id_pegawai').'" AND ww.`active` = "1" ORDER BY ww.cdd DESC, ww.id_inbox DESC LIMIT 1) as paraf_next, 
						CONCAT("Termin ",`a`.`termin`) as termin1					
						FROM (SELECT * FROM t_inbox WHERE mailto = "'.$this->session->userdata('id_pegawai').'" ORDER BY cdd DESC ) as z
						JOIN `t_hasil_pekerjaan` `a` ON `z`.`id_hasil_pekerjaan` = `a`.`id_hasil_pekerjaan`
						JOIN `t_pencairan` `b` ON `a`.`id_pencairan` = `b`.`id_pencairan`
						JOIN `m_spk` `d` ON `d`.`id_spk` = `b`.`id_spk`
						WHERE `z`.`active` = "1"  AND z.type = "1"
						-- GROUP BY `z`.`mailto`, `z`.`id_hasil_pekerjaan`
						ORDER BY `cdd` DESC
					)
					UNION ALL
					(						
						SELECT xy.*, GROUP_CONCAT(CONCAT("Termin ", xy.termin) ORDER by xy.termin ASC) termin1
						FROM (
							SELECT 
							z.id_inbox,
							z.id_hasil_pekerjaan,
							z.mailto,
							z.nip_mailto,
							z.nama_mailto,
							z.nomenklatur_jabatan_mailto,
							z.mailfrom,
							z.nip_mailfrom,
							z.nama_mailfrom,
							z.nomenklatur_jabatan_mailfrom,
							z.status,
							z.viewed,
							z.catatan,
							z.notif_text,
							z.cdd,
							z.cdb,
							z.cdi,
							z.mdd,
							z.mdb,
							z.mdi,
							z.paraf,
							z.id_pembayaran,
							z.active,
							`a`.`id_pencairan`, 
							`a`.`no_srt_penyerahan`, 
							`a`.`tgl_srt_penyerahan`, 
							`a`.`termin` as `termin`, 
							`a`.`no_bas_penerimaan`, 
							`a`.`tanggal_bas`, 
							`a`.`nilai_pekerjaan`, 
							`a`.`nilai_pekerjaan_terbilang`, 
							`a`.`pjbtpenerima_nosk`, 
							`a`.`pjbtpenerima_tglsk`, 
							`a`.`nama_pegawai_pphp`, 
							`a`.`nomenklatur_jabatan_pphp`, 
							`a`.`nama_pegawai_ppk`, 
							`a`.`nomenklatur_jabatan_ppk`, 
							`a`.`nama_pegawai_pptk`, 
							`a`.`nomenklatur_jabatan_pptk`, 
							`a`.`nama_pegawai_bendahara`, 
							`a`.`nomenklatur_jabatan_bendahara`, 
							`a`.`nama_pegawai_pengguna_anggaran`, 
							`a`.`nomenklatur_jabatan_pengguna_anggaran`, 
							`d`.`no_spk`, 
							`d`.`nama_pekerjaan`, 
							`d`.`nama_kegiatan`, 
							IF(c.tgl_permohonan_pembayaran is null, c.cdd, c.tgl_permohonan_pembayaran) as tgl_surat,
							(SELECT ww.paraf FROM t_inbox ww WHERE ww.id_pembayaran = z.id_pembayaran AND ww.mailfrom = "'.$this->session->userdata('id_pegawai').'" AND ww.`active` = "1" ORDER BY ww.cdd DESC, ww.id_inbox DESC LIMIT 1) as paraf_next
							FROM (SELECT * FROM t_inbox WHERE mailto = "'.$this->session->userdata('id_pegawai').'" ORDER BY cdd DESC) as z
							JOIN `t_pembayaran` `c` ON `z`.`id_pembayaran` = `c`.`id_pembayaran`
							JOIN `t_hasil_pekerjaan` `a` ON `a`.`id_pembayaran` = `c`.`id_pembayaran`
							JOIN `t_pencairan` `b` ON `a`.`id_pencairan` = `b`.`id_pencairan`
							JOIN `m_spk` `d` ON `d`.`id_spk` = `b`.`id_spk`
							WHERE `z`.`active` = "1"  AND z.type = "1"
							ORDER BY `cdd` DESC
						) as xy
						GROUP BY xy.id_inbox
					)
			)  as xc
		');				
		
		$this->db_ba->where("cdd <", $_POST['current_date']);
		
		$i = 0;
	
		foreach ($this->column_search as $item) // loop column 
		{
			if(@$_POST['search']['value']) // if datatable send POST for search
			{
				
				if($i===0) // first loop
				{
					$this->db_ba->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					$this->db_ba->like($item, @$_POST['search']['value']);
				}
				else
				{
					$this->db_ba->or_like($item, @$_POST['search']['value']);
				}

				if(count($this->column_search) - 1 == $i) //last loop
					$this->db_ba->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db_ba->order_by($this->column_order[@$_POST['order']['0']['column']], @$_POST['order']['0']['dir']);
		} 
		else if(isset($this->order))
		{
			$order = $this->order;
			$this->db_ba->order_by(key($order), $order[key($order)]);
			$this->db_ba->order_by('cdd', 'DESC');
		}
	}

	function get_datatables()
	{
		$id_akses = $this->session->userdata('id_akses');
		
		if(!empty($_POST['current_date'])){
			$this->_get_datatables_query_notif();
		}else{
			$this->_get_datatables_query();
		}
		if(@$_POST['length'] != -1)
		$this->db_ba->limit(@$_POST['length'], @$_POST['start']);	
		$query = $this->db_ba->get();
		// var_dump($this->db_ba->last_query());
		// exit;
		return $query->result();
	}

	function count_filtered()
	{
		$id_akses = $this->session->userdata('id_akses');
		// if($id_akses >= 3 && $id_akses <= 6){ //jika ppk
			// $this->_get_datatables_query_ppk();
		// }else{
			$this->_get_datatables_query();
		// }
		$query = $this->db_ba->get();
		return $query->num_rows();
	}

	public function count_all()
	{
		$this->db_ba->select('viewed');
		$this->db_ba->from($this->table);
		$this->db_ba->where("mailto", $this->session->userdata('id_pegawai'));
		$this->db_ba->where("viewed", null);
		$this->db_ba->where("active", 1);
		return $this->db_ba->count_all_results();
	}
	
	public function count_all_hasil_pekerjaan()
	{
		$this->db_ba->select('viewed');
		$this->db_ba->from($this->table);
		$this->db_ba->where("mailto", $this->session->userdata('id_pegawai'));
		$this->db_ba->where("id_hasil_pekerjaan is NOT NULL", NULL, FALSE);
		$this->db_ba->where("viewed", null);
		$this->db_ba->where("active", 1);
		$this->db_ba->group_by('id_hasil_pekerjaan');		
		
		$query = $this->db_ba->get();		
		return $query->num_rows();
	}
	
	public function count_all_pembayaran()
	{
		$this->db_ba->select('viewed');
		$this->db_ba->from($this->table);
		$this->db_ba->where("mailto", $this->session->userdata('id_pegawai'));
		$this->db_ba->where("id_pembayaran is NOT NULL", NULL, FALSE);
		$this->db_ba->where("viewed", null);
		$this->db_ba->where("active", 1);
		$this->db_ba->group_by('id_pembayaran');		
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
		// $where = array('id_inbox' => $id);
		// $this->db_ba
		// ->where($where)
		// ->from($this->table);
		// $exist = $this->db_ba->get()->row();	
					
		
		// check if dataexist already exist
		if($id)			
		{	
			
			// $id_inbox = ($exist) ? $id :  getID('id_inbox', $this->table);
			$id_inbox = $id;
			
			$prop = array('id_inbox'=> $id_inbox);				
			
			$data = array_merge($prop,$data);	
			
			//check for data set for escaped field
			$data = $this->_check_escaped_data_set($data);
		
			$this->db_ba->where('id_inbox' , $id_inbox);
			$data = array_merge($data,array('mdd'=> date('Y-m-d H:i:s')),$created);
			$this->db_ba->update($this->table, $data);
			$result = $id_inbox;
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

	
	public function savev2($data, $table='m_armada')
	{
		$this->db->insert($table, $data);
		return $this->db->insert_id();
	}
	
	public function get_by_id($where, $field = '*')
	{
		$this->db_ba->select($field);
		$this->db_ba->from($this->table);
		$this->db_ba->where($where);
		$this->db_ba->order_by('cdd', 'DESC');
		$this->db_ba->order_by('id_inbox', 'DESC');
		$this->db_ba->limit(1);
		$query = $this->db_ba->get();

		return $query->row();
	}
	
	public function get_by_id_in($where_in='', $field_in='', $field = '*')
	{
		$this->db_ba->select($field);
		$this->db_ba->from($this->table);
		$this->db_ba->where_in($field_in, $where_in);
		$this->db_ba->order_by('cdd', 'DESC');
		$this->db_ba->order_by('id_inbox', 'DESC');
		$query = $this->db_ba->get();

		return $query->row();
	}
	
	public function get_by_id_count_all($where, $field = '*')
	{
		$this->db_ba->select($field);
		$this->db_ba->from($this->table);
		$this->db_ba->where($where);
		$this->db_ba->order_by('cdd', 'DESC');
		$query = $this->db_ba->count_all_results();

		return $query;
	}
	
	public function updatev2($where, $data, $nolimit=false)
	{
		$this->db_ba->where($where);
		$this->db_ba->order_by('cdd', 'DESC');
		$this->db_ba->order_by('id_inbox', 'DESC');

		if(!$nolimit){			
		$this->db_ba->limit(2);
		}
		$this->db_ba->update($this->table, $data);
		
		return $this->db_ba->affected_rows();
	}
	
	public function update($where, $data, $table='t_inbox')
	{
		$this->db_ba->update($table, $data, $where);
		return $this->db_ba->affected_rows();
	}
	
	function get_where($where='', $field='*', $order=false){
		$this->db_ba->select($field);
		
		if($order){
			$this->db_ba->order_by('cdd', 'ASC');
			$this->db_ba->order_by('id_inbox', 'ASC');
		}
		
		$query = $this->db_ba->get_where($this->table, $where);
		return $query->result_array(); 
	}
	
	
    public function get($start = null, $length = null, $sort = null, $order = null, $where = null, $like = null, $table='t_inbox', $field='*', $row_array=false, $join=false){
        $this->db->select($field);
        $this->db->from($table);
		
		if($join){
			foreach($join as $rs){
				$this->db->join($rs['nama_tabel'], $rs['kunci']);
			}
		}
		
        if($start !== null && $length !== null){
            $this->db->limit($length, $start);
        }
        if($sort !== null && $order !== null){
            $this->db->order_by($sort, $order);
        }
        if($where !== null){
            $this->db->where($where);
        }
        if($like !== null){						
			$this->db->like($like);
        }
        $query = $this->db->get();
		
		if($row_array){
			return $query->row_array();			
		}else{
			return $query->result_array();
		}
    }

}
