<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inbox extends CI_Controller {
	
	/*
	 * @author     		RENDY
	 * @author_email    rendyardian199@gmail.com
	 *  KOTA TANGERANG 
	 */

	public function __construct()
	{
		parent::__construct();
		$this->load->model('hasil_pekerjaan_model', 'hasil_pekerjaan');						
		$this->load->model('pencairan_model', 'pencairan');		
		$this->load->model('penyedia_model', 'penyedia');		
		$this->load->model('spk_model', 'spk');		
		$this->load->model('inbox_model', 'inbox');		
		$this->db_ba = $this->load->database('db_pembayaran_ls', TRUE);
		if($this->session->userdata('login')==false){
			redirect(base_url('site/login'));
		}
		
	}
	
	public function index()
	{
		getHakAkses();		
		$content['class_name'] = get_class($this);	
		$content['content'] = $this->load->view('inbox/index',@$content,true);
		$this->load->view('ba/index', @$content,false,false);
	}
	
	public function ajax_list()
	{
		$this->load->helper('url');
		$list = $this->inbox->get_datatables();		
		
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $person) {
			$no++;
			$row = array();			
			
			if(!empty($person->viewed)){
				$bold = 'style="cursor:pointer;"';
				$text_status = '<div style="text-align:center"><i class="glyphicon glyphicon-eye-open"></i> <span class="label label-default">Terbaca</span></div>';
			}else{
				$bold = 'style="cursor:pointer; font-weight: bold;"';
				$text_status = '<div style="text-align:center"><i class="glyphicon glyphicon-info-sign"></i> <span class="label label-danger">Terbaru</span></div>';
			}
			
			$data_dari = '<font>' . $person->nama_mailfrom . '</font><br>'.
						 '<font style="font-size:8pt;">'.$person->nomenklatur_jabatan_mailfrom.'</font>'
			;
			
			if(isset($person->id_hasil_pekerjaan)){
				$data_perihal = '<font style="font-size:8pt;">Surat Penyerahan Hasil Pekerjaan '.$person->termin.'</font><br>';
			}else{
				$data_perihal = '<font style="font-size:8pt;">Permohonan Pembayaran '.$person->termin.'</font><br>';
			}
			
			$data_tanggal = '';			
			$data_status = $text_status;
			
			$id_read = (isset($person->id_hasil_pekerjaan)) ? $person->id_hasil_pekerjaan : $person->id_pembayaran;
			$keterangan = (isset($person->id_hasil_pekerjaan)) ? 0 : 1; //jika pembayaran maka 1, jika hasil pekerjaan maka 0
			
			$row[] = '<div '.$bold.' href="javascript:void(0)" onclick="read('.$person->id_inbox.', '.$id_read.', '.$keterangan.')">'.$data_dari.'</div>';
			$row[] = '<div '.$bold.' href="javascript:void(0)" onclick="read('.$person->id_inbox.', '.$id_read.', '.$keterangan.')">'.$data_perihal.'</div>';
			$row[] = '<div '.$bold.' href="javascript:void(0)" onclick="read('.$person->id_inbox.', '.$id_read.', '.$keterangan.')">'.$data_tanggal.'</div>';
			$row[] = '<div '.$bold.' href="javascript:void(0)" onclick="read('.$person->id_inbox.', '.$id_read.', '.$keterangan.')">'.$data_status.'</div>';
			//add html for action
		
			$data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->inbox->count_filtered(),
						"recordsFiltered" => $this->inbox->count_filtered(),
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
		
	public function ajax_save()
	{
		$this->_validate();
		$post = $this->input->post();
		$data = $post;
		$rs = 0;
		
		unset($data['id']); //hapus
		$id = (!empty($post['id'])) ? $post['id'] : '';
		$id_penyedia = (!empty($post['id_penyedia'])) ? $post['id_penyedia'] : '';
		$id_spk = (!empty($post['id_spk'])) ? $post['id_spk'] : '';
		$nominal_bayar = (!empty($post['nominal_bayar'])) ? $post['nominal_bayar'] : '';
		$nominal_bayar_terbilang = terbilang_rupiah($nominal_bayar);
		$data_nominal_terbilang = array('nominal_bayar_terbilang'=> $nominal_bayar_terbilang);
		
		// Data Penyedia
		$data_penyedia = $this->penyedia->get_by_id($id_penyedia, 'id_penyedia,nama_penyedia,alamat,bank,no_rekening_penyedia,atas_nama_rekening,npwp,cabang_bank'); //get data penyedia
		$data_penyedia_convert = (array)$data_penyedia; // conver array stdClass to Array
		
		// Data SPK
		$data_spk = $this->spk->get_by_id($id_spk, 'id_spk,no_spk,tgl_pekerjaan as tgl_spk, nama_pekerjaan,id_kegiatan,nama_kegiatan as kegiatan'); //get data SPK
		$data_spk_convert = (array)$data_spk; // conver array stdClass to Array		
		// var_dump($data_spk_convert);exit;
		
		//marge array
		$gabung = array_merge($data, $data_penyedia_convert, $data_spk_convert, $data_nominal_terbilang);
		$data = $gabung;
		
		if(!empty($id)){
			$created = array(
				'mdb'=> '',
				'mdi' => 'web'
			);
		}else{
			$created = array(
				'cdb'=> '',
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
		
		$hasil = $this->pencairan->update(array('id_pencairan' => $id), $data);
		if($hasil > 0){
			$content['status'] = true;
			$content['notif'] = '<div class="alert alert-success" role="alert"><i class="fa fa-thumbs-o-up fa-fw"></i> Data berhasil di hapus</div>';			

		}else{
		  $content['status'] = false;
		  $content['notif'] = '<div class="alert-modal alert-danger" role="alert"><i class="fa fa-thumbs-o-down fa-fw"></i> Data gagal dihapus !</div>';
		}
		
		
		echo json_encode($content);
	}
	
	
	public function ajax_read(){		
		$post = $this->input->post();
		$hasil = '';
		$hasil_1 = '';
		$hasil_2 = '';
		$id_read = (!empty($post['id_read'])) ? $post['id_read'] : ''; //id read ini bisa jadi id_hasil_pekerjaan, bisa jadi id_pembayaran tergantung dari nilai keterangan 1 atau 0, jika pembayaran maka 1, jika hasil pekerjaan maka 0
		$id_inbox = (!empty($post['id_inbox'])) ? $post['id_inbox'] : '';
		$keterangan = $post['keterangan'];  //jika pembayaran maka 1, jika hasil pekerjaan maka 0
		
		// jika ada id inbox maka viewed inbox di update
		if(!empty($id_inbox)){
			
			if($keterangan == 0){
				$where = array('id_hasil_pekerjaan' =>  $id_read, 'mailto' => $this->session->userdata('id_pegawai'), 'viewed' =>  null);
			}else{
				$where = array('id_pembayaran' =>  $id_read, 'mailto' => $this->session->userdata('id_pegawai'), 'viewed' =>  null);
			}
			$data_update = array('viewed' =>  date('Y-m-d H:i:s'));
			$this->inbox->update($where, $data_update);
		}
		// jika ada id inbox maka viewed inbox di update
		
		if($keterangan == 0){ // jika hasil pekerjaan maka 0
			if(!empty($id_read)){	
				$data = $this->hasil_pekerjaan->get_by_id_detail_hasil_pekerjaan($id_read);
				
				$ttd_pphp = '';
				$ttd_pptk = '';
				$ttd_ppk = '';
				$ttd_bendahara = '';
				$ttd_pengguna_anggaran = '';
				
				//cek apakah pphp
				if($data->id_pegawai_pphp == $this->session->userdata('id_pegawai'))
				{
					$ttd_pphp = $this->cek_ttd($id_read, $this->session->userdata('id_pegawai'), 'ttd_pphp');
				}else{
					$ttd_pphp = $this->cek_ttd($id_read, $data->id_pegawai_pphp, 'ttd_pphp', true);
				}
				//cek apakah pphp
				
				//cek apakah pptk
				if($data->id_pegawai_pptk == $this->session->userdata('id_pegawai'))
				{
					$ttd_pptk = $this->cek_ttd($id_read, $this->session->userdata('id_pegawai'), 'ttd_pptk');
				}else{				
					$ttd_pptk = $this->cek_ttd($id_read, $data->id_pegawai_ppk, 'ttd_pptk', true);
				}
				//cek apakah pptk
				
				//cek apakah ppk
				if($data->id_pegawai_ppk == $this->session->userdata('id_pegawai'))
				{
					$ttd_ppk = $this->cek_ttd($id_read, $this->session->userdata('id_pegawai'), 'ttd_ppk');
				}else{
					$ttd_ppk = $this->cek_ttd($id_read, $data->id_pegawai_ppk, 'ttd_ppk', true);
				}
				//cek apakah ppk
				
				
				$rs['data'] = $data;
				$rs['ttd_pphp'] = $ttd_pphp;
				$rs['ttd_pptk'] = $ttd_pptk;
				$rs['ttd_ppk'] = $ttd_ppk;
				$isi_template_surat_permohonan = $this->load->view('hasil_pekerjaan/template_surat_penyerahan_hasil_pekerjaan', @$rs, true);
				$isi_template_bas = $this->load->view('hasil_pekerjaan/template_bas', @$rs, true);
				$isi_template_bast = $this->load->view('hasil_pekerjaan/template_bast', @$rs, true);
				
				$hasil = '
				<page size="F4">'.$isi_template_bast.'</page>
				<page size="A4">'.$isi_template_bas.'</page>
				<page size="A4">'.$isi_template_surat_permohonan.'</page>
				<page size="batas" layout="portrait"></page>';
				
			}
		}else{ //jika id pembayaran maka 1
			if(!empty($id_read)){	
				$data_all = $this->hasil_pekerjaan->get_by_id_detail($id_read);
				$reversed = array_reverse($data_all);
				
				
				
				foreach($reversed as $data)
				{
					$ttd_pphp = '';
					$ttd_pptk = '';
					$ttd_ppk = '';
					$ttd_bendahara = '';
					$ttd_pengguna_anggaran = '';
					
					//cek apakah pphp
					if($data->id_pegawai_pphp == $this->session->userdata('id_pegawai'))
					{
						$ttd_pphp = $this->cek_ttd($data->id_hasil_pekerjaan, $this->session->userdata('id_pegawai'), 'ttd_pphp');
					}else{
						$ttd_pphp = $this->cek_ttd($data->id_hasil_pekerjaan, $data->id_pegawai_pphp, 'ttd_pphp', true);
					}
					//cek apakah pphp
					
					//cek apakah pptk
					if($data->id_pegawai_pptk == $this->session->userdata('id_pegawai'))
					{
						$ttd_pptk = $this->cek_ttd($data->id_hasil_pekerjaan, $this->session->userdata('id_pegawai'), 'ttd_pptk');
					}else{				
						$ttd_pptk = $this->cek_ttd($data->id_hasil_pekerjaan, $data->id_pegawai_ppk, 'ttd_pptk', true);
					}
					//cek apakah pptk
					
					//cek apakah ppk
					if($data->id_pegawai_ppk == $this->session->userdata('id_pegawai'))
					{
						$ttd_ppk = $this->cek_ttd($data->id_pembayaran, $this->session->userdata('id_pegawai'), 'ttd_ppk');						
					}else{
						$ttd_ppk = $this->cek_ttd($data->id_pembayaran, $data->id_pegawai_ppk, 'ttd_ppk', true);
					}
					//cek apakah ppk
					
					//cek apakah bendahara
					if($data->id_pegawai_bendahara == $this->session->userdata('id_pegawai'))
					{
						$ttd_bendahara = $this->cek_ttd($data->id_pembayaran, $this->session->userdata('id_pegawai'), 'ttd_bendahara');						
					}else{
						$ttd_bendahara = $this->cek_ttd($data->id_pembayaran, $data->id_pegawai_bendahara, 'ttd_bendahara', true);
					}
					//cek apakah bendahara
					
					//cek apakah pengguna anggaran
					if($data->id_pegawai_pengguna_anggaran == $this->session->userdata('id_pegawai'))
					{
						$ttd_pengguna_anggaran = $this->cek_ttd($data->id_pembayaran, $this->session->userdata('id_pegawai'), 'ttd_pengguna_anggaran');						
					}else{
						$ttd_pengguna_anggaran = $this->cek_ttd($data->id_pembayaran, $data->id_pegawai_pengguna_anggaran, 'ttd_pengguna_anggaran', true);
					}
					//cek apakah bendahara
					
					
					$rs['data'] = $data;
					$rs['ttd_pphp'] = $ttd_pphp;
					$rs['ttd_pptk'] = '<div id="logo_ttd_pptk"></div><button type="button" class="btn btn-success btn-circle btn-lg"><i class="glyphicon glyphicon-ok"></i></button>';
					$rs['ttd_ppk'] = $ttd_ppk;
					$rs['ttd_bendahara'] = $ttd_bendahara;
					$rs['ttd_pengguna_anggaran'] = $ttd_pengguna_anggaran;
					$isi_template_surat_permohonan = $this->load->view('hasil_pekerjaan/template_surat_penyerahan_hasil_pekerjaan', @$rs, true);
					$isi_template_bas = $this->load->view('hasil_pekerjaan/template_bas', @$rs, true);
					$isi_template_bast = $this->load->view('hasil_pekerjaan/template_bast', @$rs, true);
					
					$hasil_2 .= '
					<page size="F4">'.$isi_template_bast.'</page>
					<page size="A4">'.$isi_template_bas.'</page>
					<page size="A4">'.$isi_template_surat_permohonan.'</page>
					<page size="batas" layout="portrait"></page>';
					
				}
				
				$rss['data_pembayaran'] = $reversed;
				$rss['viewer'] = true;
				$isi_template_surat_permohonan_pembayaran = $this->load->view('pembayaran/template_surat_permohonan', @$rss, true);
				$isi_template_pembayaran = $this->load->view('pembayaran/template_bapembayaran', @$rss, true);
				$isi_kwitansi_pembayaran = $this->load->view('pembayaran/template_kwitansi_pembayaran', @$rss, true);
				
				$hasil_1 = '
				<page size="kwitansi">'.$isi_kwitansi_pembayaran.'</page>
				<page size="F4">'.$isi_template_pembayaran.'</page>
				<page size="A4">'.$isi_template_surat_permohonan_pembayaran.'</page>
				';
				
				$hasil .= $hasil_1;
				$hasil .= $hasil_2;
				
			}
		}
		$content['hasil'] = $hasil;
		$content['content'] = $this->load->view('inbox/view_template',@$content);
		
	}
	
	public function cek_ttd($id = '', $id_pegawai = '', $type = '', $kondisi = false){
		$ttd = '';
		
		//id ini bisa id_hasil_pekerjaan ataupun id_pembayaran
		
		$where = ($type == 'ttd_ppk' || $type == 'ttd_bendahara' || $type == 'ttd_pengguna_anggaran') ? array('id_pembayaran'=> $id, 'mailto'=> $id_pegawai) : array('id_hasil_pekerjaan'=> $id, 'mailto'=> $id_pegawai);
		$status_ttd = $this->inbox->get_by_id($where, 'paraf');
				
		// if($type == 'ttd_ppk'){
			// echo '<pre>';
			// var_dump($id, $type, $status_ttd, $this->db_ba->last_query());
			// echo '</pre>';
		// }
		
		if(@$status_ttd){
			$where = ($type == 'ttd_ppk' || $type == 'ttd_bendahara' || $type == 'ttd_pengguna_anggaran') ? array('id_pembayaran'=> $id, 'mailfrom'=> $id_pegawai) : array('id_hasil_pekerjaan'=> $id, 'mailfrom'=> $id_pegawai);
			$status_ttd = $this->inbox->get_by_id($where, 'paraf');
			
			if(@$status_ttd->paraf == 1)
			{
				$ttd = '<div id="logo_'.$type.'"></div><button type="button" class="btn btn-success btn-circle btn-lg"><i class="glyphicon glyphicon-ok"></i></button>';
			}else{
				if($kondisi){
					$ttd = '<div id="logo_'.$type.'"></div><div id="tombol_'.$type.'"><input disabled id="'.$type.'" type="checkbox"></div>';
				}else{
					$ttd = '<div id="logo_'.$type.'"></div><div id="tombol_'.$type.'"><input id="'.$type.'" onchange="doAcc(\''.$type.'\', '.$id.');" type="checkbox"></div>';
				}
			}
		}
		
		
		return $ttd;
	}
	
	public function next_paraf($id = '', $name_id=''){
		//cek daftar tujuan
		if($name_id == 'ttd_ppk' || $name_id == 'ttd_bendahara' || $name_id == 'ttd_pengguna_anggaran'){
			$result_daftar_ttd = $this->hasil_pekerjaan->get_by_id_detail($id, 'id_pegawai_pphp, nip_pegawai_pphp, nama_pegawai_pphp, nomenklatur_jabatan_pphp, id_pegawai_pptk, nip_pegawai_pptk, nama_pegawai_pptk, nomenklatur_jabatan_pptk, id_pegawai_ppk, nip_pegawai_ppk, nama_pegawai_ppk, nomenklatur_jabatan_ppk, id_pegawai_bendahara, nip_pegawai_bendahara, nama_pegawai_bendahara, nomenklatur_jabatan_bendahara, id_pegawai_pengguna_anggaran, nip_pegawai_pengguna_anggaran, nama_pegawai_pengguna_anggaran, nomenklatur_jabatan_pengguna_anggaran');
			$daftar_ttd = $result_daftar_ttd[0];
		}else{
			$daftar_ttd = $this->hasil_pekerjaan->get_by_id($id, 'id_pegawai_pphp, nip_pegawai_pphp, nama_pegawai_pphp, nomenklatur_jabatan_pphp, id_pegawai_pptk, nip_pegawai_pptk, nama_pegawai_pptk, nomenklatur_jabatan_pptk, id_pegawai_ppk, nip_pegawai_ppk, nama_pegawai_ppk, nomenklatur_jabatan_ppk, id_pegawai_bendahara, nip_pegawai_bendahara, nama_pegawai_bendahara, nomenklatur_jabatan_bendahara, id_pegawai_pengguna_anggaran, nip_pegawai_pengguna_anggaran, nama_pegawai_pengguna_anggaran, nomenklatur_jabatan_pengguna_anggaran');
		}
		
		$tujuan_next[] = array('mailto'=> $daftar_ttd->id_pegawai_pphp, 'nip_mailto'=> $daftar_ttd->nip_pegawai_pphp, 'nama_mailto'=> $daftar_ttd->nama_pegawai_pphp, 'nomenklatur_jabatan_mailto'=> $daftar_ttd->nomenklatur_jabatan_pphp);
		$tujuan_next[] = array('mailto'=> $daftar_ttd->id_pegawai_pptk, 'nip_mailto'=> $daftar_ttd->nip_pegawai_pptk, 'nama_mailto'=> $daftar_ttd->nama_pegawai_pptk, 'nomenklatur_jabatan_mailto'=> $daftar_ttd->nomenklatur_jabatan_pptk);
		$tujuan_next[] = array('mailto'=> $daftar_ttd->id_pegawai_ppk, 'nip_mailto'=> $daftar_ttd->nip_pegawai_ppk, 'nama_mailto'=> $daftar_ttd->nama_pegawai_ppk, 'nomenklatur_jabatan_mailto'=> $daftar_ttd->nomenklatur_jabatan_ppk);
		$tujuan_next[] = array('mailto'=> $daftar_ttd->id_pegawai_bendahara, 'nip_mailto'=> $daftar_ttd->nip_pegawai_bendahara, 'nama_mailto'=> $daftar_ttd->nama_pegawai_bendahara, 'nomenklatur_jabatan_mailto'=> $daftar_ttd->nomenklatur_jabatan_bendahara);
		$tujuan_next[] = array('mailto'=> $daftar_ttd->id_pegawai_pengguna_anggaran, 'nip_mailto'=> $daftar_ttd->nip_pegawai_pengguna_anggaran, 'nama_mailto'=> $daftar_ttd->nama_pegawai_pengguna_anggaran, 'nomenklatur_jabatan_mailto'=> $daftar_ttd->nomenklatur_jabatan_pengguna_anggaran);
		
		$tujuan_before[] = array('mailfrom'=> $daftar_ttd->id_pegawai_pphp, 'nip_mailfrom'=> $daftar_ttd->nip_pegawai_pphp, 'nama_mailfrom'=> $daftar_ttd->nama_pegawai_pphp, 'nomenklatur_jabatan_mailfrom'=> $daftar_ttd->nomenklatur_jabatan_pphp);
		$tujuan_before[] = array('mailfrom'=> $daftar_ttd->id_pegawai_pptk, 'nip_mailfrom'=> $daftar_ttd->nip_pegawai_pptk, 'nama_mailfrom'=> $daftar_ttd->nama_pegawai_pptk, 'nomenklatur_jabatan_mailfrom'=> $daftar_ttd->nomenklatur_jabatan_pptk);
		$tujuan_before[] = array('mailfrom'=> $daftar_ttd->id_pegawai_ppk, 'nip_mailfrom'=> $daftar_ttd->nip_pegawai_ppk, 'nama_mailfrom'=> $daftar_ttd->nama_pegawai_ppk, 'nomenklatur_jabatan_mailfrom'=> $daftar_ttd->nomenklatur_jabatan_ppk);
		$tujuan_before[] = array('mailfrom'=> $daftar_ttd->id_pegawai_bendahara, 'nip_mailfrom'=> $daftar_ttd->nip_pegawai_bendahara, 'nama_mailfrom'=> $daftar_ttd->nama_pegawai_bendahara, 'nomenklatur_jabatan_mailfrom'=> $daftar_ttd->nomenklatur_jabatan_bendahara);
		$tujuan_before[] = array('mailfrom'=> $daftar_ttd->id_pegawai_pengguna_anggaran, 'nip_mailfrom'=> $daftar_ttd->nip_pegawai_pengguna_anggaran, 'nama_mailfrom'=> $daftar_ttd->nama_pegawai_pengguna_anggaran, 'nomenklatur_jabatan_mailfrom'=> $daftar_ttd->nomenklatur_jabatan_pengguna_anggaran);
		
		//cek daftar tujuan
		for($i=0; $i<count($tujuan_next); $i++)
		{
			if($this->session->userdata('id_pegawai') == $tujuan_next[$i]['mailto'])
			{
				if($i+1 == count($tujuan_next)){ 
					// jika sudah terakhir pengguna anggaran makan balik ke pptk lagi mailto-Nya
					$next_inbox = $tujuan_next[1];
				}else{
					$next_inbox = $tujuan_next[$i+1];
				}
				$before_next = $tujuan_before[$i];
				break;
			}
		}
		//cek daftar tujuan
		$hasil = array_merge($next_inbox, $before_next);
		
		return $hasil;
	}
	
	public function ajax_insert(){
		
		$post = $this->input->post();
		
		$id = $post['id'];
		$statusParaf = $post['statusParaf'];
		$name_id = $post['name_id'];
		
		$hasil_next_paraf = $this->next_paraf($id, $name_id);
		
		
		$created = array(
			'cdb'=> $this->session->userdata('id_pegawai'),
			'cdi' => 'web'
		);
		
		//insert data t_inbox
		$id_inbox = '';
		
		if($name_id == 'ttd_ppk'){
			$status = 3;
		}else if($name_id == 'ttd_bendahara'){
			$status = 4;
		}else if($name_id == 'ttd_pengguna_anggaran'){
			$status = 5;
		}
		
		if($name_id == 'ttd_ppk' || $name_id == 'ttd_bendahara' || $name_id == 'ttd_pengguna_anggaran'){
			$data_inbox = array(
				'id_pembayaran'=> $id,
				'status'=> $status,
				'paraf'=> $statusParaf
			);
		}else{
			$data_inbox = array(
				'id_hasil_pekerjaan'=> $id,
				'status'=> 1,
				'paraf'=> $statusParaf
			);
		}
		$data_insert = array_merge($data_inbox, $hasil_next_paraf);
		
		$result_inbox = $this->inbox->save($id_inbox, $data_insert, $created);
		//insert data t_inbox
		
		$content['status'] = true;
		$content['notif'] = '<div class="alert alert-success" role="alert"><i class="fa fa-thumbs-o-up fa-fw"></i> Data berhasil disimpan</div>';
			
		echo json_encode(array("status" => TRUE));
	}
	
}
