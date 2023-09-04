<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pencairan extends CI_Controller {
	
	/*
	 * @author     		RENDY
	 * @author_email    rendyardian199@gmail.com
	 *  KOTA TANGERANG 
	 */

	public function __construct()
	{
		parent::__construct();
		$this->load->model('pencairan_model', 'pencairan');		
		$this->load->model('penyedia_model', 'penyedia');		
		$this->load->model('hasil_pekerjaan_model', 'hasil_pekerjaan');		
		$this->load->model('pembayaran_termin_model', 'pembayaran_termin');		
		$this->load->model('spk_model', 'spk');		
		$this->load->model('Setting_model', 'setting');
		$this->db_ba = $this->load->database('db_pembayaran_ls', TRUE);
		if($this->session->userdata('login')==false){
			redirect(base_url('site/login'));
		}
	}
	
	public function index()
	{
		$content['class_name'] = get_class($this);	
		$content['content'] = $this->load->view('pencairan/index',@$content,true);
		$this->load->view('ba/index', @$content,false,false);
	}
	
	public function ajax_list()
	{
		$this->load->helper('url');

		$wherexz['(a.id_pegawai_pptk = "'.$this->session->userdata('id_pegawai').'" or a.id_pegawai_ppk = "'.$this->session->userdata('id_pegawai').'")'] = NULL;
		$get_pptk_ppk =  $this->setting->get($start = null, $length = null, $sort = null, $order = null, $wherexz, $like = null, $table='t_setting_pejabat a', $field='a.id_prokeg_skpd', $row_array=false, $join=false, $group_by=false);

		if(!empty($get_pptk_ppk))
		{
			$data_arrx = array();
			foreach($get_pptk_ppk as $rsz)
			{
				$data_arrx[] = $rsz['id_prokeg_skpd'];
			}
			$_POST['data_id'] = $data_arrx;
		}

		$list = $this->pencairan->get_datatables();
		// var_dump($list);exit;
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $person) {
			$no++;
			$row = array();			
			
			if($person->kategori == 1){			
				$data_penyedia = '<font style="font-weight: bold;">' . $person->nama_perusahaan . '</font><br>'.
								 '<font style="font-size:8pt">' . $person->nama_penyedia . ' ('.$person->jabatan.')</font><br>
								  <font style="font-size:8pt;">'.$person->alamat.'</font><br>'.
								 '<font style="font-size:8pt;">NPWP: '.$person->npwp.'</font><br>'.
								 '<font style="font-size:8pt;">BANK: '.$person->no_rekening_penyedia.' a.n '.$person->atas_nama_rekening.'</font>'
				;
			}else{
				$data_penyedia = '<font style="font-weight: bold;">' . $person->nama_penyedia . '</font><br>'.
								 '<font style="font-size:8pt;">'.$person->alamat.'</font><br>'.
								 '<font style="font-size:8pt;">NPWP: '.$person->npwp.'</font><br>'.
								 '<font style="font-size:8pt;">BANK: '.$person->no_rekening_penyedia.' a.n '.$person->atas_nama_rekening.'</font>'
				;
			}
			
			$data_kontrak = '<font style="font-size:8pt;">' . $person->no_spk . '</font><br>'.
							 '<font style="font-size:8pt;">'.$person->nama_pekerjaan.'</font><br>'.
							 '<font style="font-size:8pt;">'.$person->nama_kegiatan.'</font><br>'
			;
			
			if(@$person->cdb == @$person->mdb || @$person->mdb == null){		
					$row[] = '
								<div class="btn-group">
										<button type="button" class="btn btn-primary" href="javascript:void(0)" onclick="add_hasil_pekerjaan('.$person->id_pencairan.')">
										<i class="fa fa-file fa-fw"></i> Proses</button>
										
										
									  </div>';

			}else{				
				if($person->mdb == $this->session->userdata('id_pegawai')){
					$row[] = '
								<div class="btn-group">
										<button type="button" class="btn btn-primary" href="javascript:void(0)" onclick="add_hasil_pekerjaan('.$person->id_pencairan.')">
										<i class="fa fa-file fa-fw"></i> Proses</button>
										
										
									  </div>';
				}else{
					$row[] = '';
				}
			}
			
			$row[] = $data_penyedia;
			$row[] = $data_kontrak;
			//add html for action
		
			$data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->pencairan->count_filtered(),
						"recordsFiltered" => $this->pencairan->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}
	
	public function ajax_form()
	{		
		$content['class_name'] = get_class($this);			
		$post = $this->input->post();
		$id_penyedia = (!empty($post['id_penyedia'])) ? $post['id_penyedia'] : '';	
		$content['nama_penyedia'] = getformselect('m_penyedia','id_penyedia','nama_penyedia','status = 1');
		$content['nama_pekerjaan'] = getformselect('m_spk','id_spk','nama_pekerjaan','status = 1');	
		
		//ini apabila mau menampilkan data untuk diedit
		if(!empty($id_penyedia)){
			$data_penyedia = $this->pencairan->get_by_id($id_penyedia);
			
			$content['nama_penyedia'] = getformselect('m_penyedia','id_penyedia','nama_penyedia','status = 1', false, $data_penyedia->id_penyedia);
			$content['nama_pekerjaan'] = getformselect('m_spk','id_spk','nama_pekerjaan','status = 1', false, $data_penyedia->id_spk);
			$content['data_penyedia'] = $data_penyedia;
		}
		
		$content['content'] = $this->load->view('pencairan/form',@$content);
	}
	
	
	public function ajax_save_pembayaran_termin($id = '', $id_pencairan = '')
	{
		$post = $this->input->post();
		$data = $post;
		
		if(!empty($id)){
			$created = array(
				'mdb'=> $this->session->userdata('id_pegawai'),
				'mdi' => 'web'
			);
		}else{
			$created = array(
				'cdb'=> $this->session->userdata('id_pegawai'),
				'cdi' => 'web'
			);
		}
		
		$success = 0; //0 false, 1 true
		
		if(!empty($data['pembayaran_termin'])){
			$no = 1;
			foreach($data['pembayaran_termin'] as $row){		
				$toData = array("biaya"=> str_replace(',','',$row), "termin"=> $no++, "id_pencairan" => $id_pencairan);
				$success = $this->pembayaran_termin->save($id, $toData, $created);
			}
		}
		
		return $success;
	}
		
	public function ajax_save()
	{
		$this->_validate();
		$post = $this->input->post();
		$data = $post;
		$rs = 0;		
		
		unset($data['id'], $data['pagu'], $data['pembayaran_termin']); //hapus
		$id = (!empty($post['id'])) ? $post['id'] : '';
		$id_penyedia = (!empty($post['id_penyedia'])) ? $post['id_penyedia'] : '';
		$id_spk = (!empty($post['id_spk'])) ? $post['id_spk'] : '';
		$nominal_bayar = (!empty($post['nominal_bayar'])) ? str_replace(',','',$post['nominal_bayar']) : '';
		$nominal_bayar_terbilang = terbilang_rupiah($nominal_bayar);
		
		// Data Penyedia
		$data_penyedia = $this->penyedia->get_by_id($id_penyedia, 'id_penyedia,nama_penyedia,alamat,bank,no_rekening_penyedia,atas_nama_rekening,npwp,cabang_bank'); //get data penyedia
		$data_penyedia_convert = (array)$data_penyedia; // conver array stdClass to Array
		
		// Data SPK
		$data_spk = $this->spk->get_by_id($id_spk, 'id_spk,no_spk,tgl_pekerjaan as tgl_spk, nama_pekerjaan,id_kegiatan,id_prokeg_aktif,nama_kegiatan as kegiatan'); //get data SPK
		$data_spk_convert = (array)$data_spk; // conver array stdClass to Array		
		// var_dump($data_spk_convert);exit;
		
		//marge array
		$data['nominal_bayar_terbilang'] = $nominal_bayar_terbilang;
		$data['nominal_bayar'] = str_replace(',','',$post['nominal_bayar']);
		$gabung = array_merge($data, $data_penyedia_convert, $data_spk_convert);
		$data = $gabung;
		
		
		if($data['nominal_bayar'] > $post['pagu']){
			$content['status'] = false;
			$content['notif'] = '<div class="alert alert-danger" role="alert"><i class="fa fa-thumbs-o-down fa-fw"></i> Data Nominal Biaya Lebih dari Pagu!</div>';
			echo json_encode($content);
			return false;
		}
		
		if(!empty($id)){
			$created = array(
				'mdb'=> $this->session->userdata('id_pegawai'),
				'mdi' => 'web'
			);
		}else{
			$created = array(
				'cdb'=> $this->session->userdata('id_pegawai'),
				'cdi' => 'web'
			);
		}
		
		//cek data pengajuan apakah sudah ada		
		if(empty($id)){
			$rs = $this->pencairan->cek_data_ready($id_penyedia, $id_spk);
		}
		
		if($rs == 1){
			$content['error_string'] = array();
			$content['inputerror'] = array();
			$content['status'] = false;
			$content['notif'] = '<div class="alert alert-danger" role="alert"><i class="fa fa-thumbs-o-down fa-fw"></i> Data Sudah Ada!</div>';
		}else{
			// action to save surat
			$result = $this->pencairan->save($id, $data, $created);
			if($result){
				$this->ajax_save_pembayaran_termin($id, $result);				
				$content['status'] = true;
				$content['notif'] = '<div class="alert alert-success" role="alert"><i class="fa fa-thumbs-o-up fa-fw"></i> Data berhasil disimpan</div>';			

			}else{
			  $content['status'] = false;
			  $content['notif'] = '<div class="alert alert-danger" role="alert"><i class="fa fa-thumbs-o-down fa-fw"></i> Data gagal tersimpan!</div>';
			}
		}
				
		echo json_encode($content);
	}
	
	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		// var_dump($this->input->post());
		// exit;
		
		if($this->input->post('id_penyedia') == '')
		{
			$data['inputerror'][] = 'id_penyedia';
			$data['error_string'][] = 'Nama Penyedia is required';
			$data['status'] = FALSE;
		}

		if($this->input->post('id_spk') == '')
		{
			$data['inputerror'][] = 'id_spk';
			$data['error_string'][] = 'Nama Pekerjaan is required';
			$data['status'] = FALSE;
		}

		if($this->input->post('pekerjaan_termin') == '')
		{
			$data['inputerror'][] = 'pekerjaan_termin';
			$data['error_string'][] = 'Pekerjaan Termin is required';
			$data['status'] = FALSE;
		}

		if($this->input->post('nominal_bayar') == '')
		{
			$data['inputerror'][] = 'nominal_bayar';
			$data['error_string'][] = 'Nominal Biaya is required';
			$data['status'] = FALSE;
		}
		
		if(!empty($this->input->post('pembayaran_termin')))
		{
			foreach($this->input->post('pembayaran_termin') as $key=> $row){
				$jmlah = (int) $key + 1;
				$convert = stripslashes(preg_replace("/[^A-Za-z0-9.]/", "" , $row));
				if(empty($convert)){
					$data['inputerror'][] = 'pembayaran_termin_'.(int)$key;
					$data['error_string'][] = 'Biaya Pembayaran Termin '.$jmlah.' is required';
					$data['status'] = FALSE;					
				}
			}
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}
	
	public function ajax_delete()
	{
		$post = $this->input->post();
		$id = (!empty($post['id'])) ? $post['id'] : '';
		
		//delete file
		$field = 'status';		
		$data = array("status" => 0);
		
		$hasil = $this->pencairan->update(array('id_pencairan' => $id, 'status'=> 1), $data);
		if($hasil > 0){
			$content['status'] = true;
			$content['notif'] = '<div class="alert alert-success" role="alert"><i class="fa fa-thumbs-o-up fa-fw"></i> Data berhasil di hapus</div>';			

		}else{
		  $content['status'] = false;
		  $content['notif'] = '<div class="alert-modal alert-danger" role="alert"><i class="fa fa-thumbs-o-down fa-fw"></i> Data gagal dihapus !</div>';
		}
		
		
		echo json_encode($content);
	}
	
	public function ajax_cek_pagu()
	{
		$post = $this->input->post();
		$id = (!empty($post['id'])) ? $post['id'] : '';
		
		$hasil = $this->spk->get_by_id($id, 'pagu');
		if(!empty($hasil)){
			$content['status'] = true;
			$content['hasil'] = $hasil->pagu;			

		}else{
		  $content['status'] = false;
		  $content['hasil'] = 0;
		}
		
		
		echo json_encode($content);
	}
	
	
	function cek_data_proses(){
		$post = $this->input->post();
		$id = $post['id'];
		
		$get_data = $this->pencairan->get_by_id_v2($id, 'id_pencairan');
		$get_cek = $this->hasil_pekerjaan->cek_data_ready(@$get_data->id_pencairan);
		// var_dump($this->db_ba->last_query());
		// exit;
		// if($get_cek > 0){
			// echo json_encode(array("status"=> false));
		// }else{			
			echo json_encode(array("status"=> true));
		// }
		
	}
	
}
