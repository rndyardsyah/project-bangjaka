<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pembayaran extends CI_Controller {
	
	/*
	 * @author     		RENDY
	 * @author_email    rendyardian199@gmail.com
	 *  KOTA TANGERANG 
	 */

	public function __construct()
	{
		parent::__construct();
		$this->load->model('pembayaran_model', 'pembayaran');		
		$this->load->model('pencairan_model', 'pencairan');		
		$this->load->model('penyedia_model', 'penyedia');		
		$this->load->model('hasil_pekerjaan_model', 'hasil_pekerjaan');		
		$this->load->model('spk_model', 'spk');		
		$this->load->model('inbox_model', 'inbox');		
		$this->load->model('uraian_model', 'uraian');		
		$this->load->model('paraf_pphp_model', 'paraf_pphp');		
		$this->load->model('adendum_model', 'adendum');		
		$this->load->model('rincian_model', 'rincian');				
		$this->load->model('user_model', 'user');		
		$this->load->model('pembayaran_rincian_model', 'pembayaran_rincian');		
		$this->load->model('berkas_model', 'berkas');	
		$this->db_ba = $this->load->database('db_pembayaran_ls', TRUE);
		$this->db_eoffice = $this->load->database('db_eoffice', TRUE);
		if($this->session->userdata('login')==false){
			redirect(base_url('site/login'));
		}
	}
	
	public function index()
	{
		$content['class_name'] = get_class($this);	
		$content['content'] = $this->load->view('pembayaran/index',@$content,true);
		$this->load->view('ba/index', @$content,false,false);
	}
	
	public function ajax_list()
	{
		$this->load->helper('url');

		$list = $this->pembayaran->get_datatables();
		// var_dump($this->db_ba->last_query());
		// exit;
		$data = array();
		$no = $_POST['start'];
		$view_termin = '';
		$printPdf = '';
		foreach ($list as $person) {
			$no++;
			$row = array();			
			$printPdf = '';
			$termins = $this->hasil_pekerjaan->get_where_criteria(array('id_pembayaran'=> $person->id_pembayaran), 'termin');
			
			if($termins){
				foreach($termins as $rts){
					$view_termin .= $rts['termin'] . ',';
				}
			}
			
			$data_penyedia = '<font>Nota Dinas Pencairan : ' . $person->nota_dinas_pencairan . '<br>
			Tanggal : '.GetFullDateFull($person->tgl_nota_dinas_pencairan).'<br>
			Penyedia : ' . $person->nama_penyedia . '<br>
			Pekerjaan : ' . $person->nama_pekerjaan . '<br>
			Kegiatan : ' . $person->nama_kegiatan . '<br>
			Termin : ' . $view_termin . '
			</font>'
			;
			
			/* $row[] = '<div class="btn-group">
								<button type="button" class="btn btn-primary dropdown-toggle btn-xs" data-toggle="dropdown"><i class="fa fa-gears fa-fw"></i> Aksi <span class="caret"></span></button>
								
								<ul class="dropdown-menu" role="menu">
								  <li><a href="javascript:void(0)" onclick="edit_penyedia('.$person->id_pembayaran.')"><i class="glyphicon glyphicon-edit"></i> Edit </a></li>
								  <li><a href="javascript:void(0)" onclick="delete_penyedia('.$person->id_pembayaran.')"><i class="glyphicon glyphicon-trash"></i> Hapus </a></li>
								  <li><a href="javascript:void(0)" onclick="cetak('.$person->id_pembayaran.')"><i class="glyphicon glyphicon-file"></i> Print PDF </a></li>
								</ul>
							  </div>
										
					<button style="margin-top: 5px;" type="button" onclick="set_date('.$person->id_pembayaran.')" class="btn btn-warning dropdown-toggle btn-xs" data-toggle="dropdown"><i class="fa fa-gears fa-fw"></i> Setup Tanggal </button>  
					'; */
			
			//cek data print pdf
			$dataInbox = $this->inbox->get_by_id_count_all(array('id_pembayaran'=> $person->id_pembayaran, 'active'=> 1), 'id_pembayaran');
			if($dataInbox >= 1){
				$printPdf = '<li><a href="javascript:void(0)" onclick="cetak('.$person->id_pembayaran.')"><i class="glyphicon glyphicon-file"></i> Print PDF </a></li>';
			}
			
			if($this->session->userdata('id_akses') == 1 || $this->session->userdata('id_akses') == 3 ||  $this->session->userdata('id_akses') == 4 ||  $this->session->userdata('id_akses') == 8){		
				$row[] = '<div class="btn-group">
									<button type="button" class="btn btn-primary dropdown-toggle btn-xs" data-toggle="dropdown"><i class="fa fa-gears fa-fw"></i> Aksi <span class="caret"></span></button>
									
									<ul class="dropdown-menu" role="menu">
									  <li><a href="javascript:void(0)" onclick="edit_penyedia('.$person->id_pembayaran.')"><i class="glyphicon glyphicon-edit"></i> Edit </a></li>
									  '.$printPdf.'
									</ul>
								  </div>
											
						<button style="margin-top: 5px;" type="button" onclick="set_date('.$person->id_pembayaran.')" class="btn btn-warning dropdown-toggle btn-xs" data-toggle="dropdown"><i class="fa fa-gears fa-fw"></i> Setup Tanggal </button>  
						';
			}else{		
				$row[] = '<div class="btn-group">
									<button type="button" class="btn btn-primary dropdown-toggle btn-xs" data-toggle="dropdown"><i class="fa fa-gears fa-fw"></i> Aksi <span class="caret"></span></button>
									
									<ul class="dropdown-menu" role="menu">
									  '.$printPdf.'
									</ul>
								  </div>
											
						<button style="margin-top: 5px;" type="button" onclick="set_date('.$person->id_pembayaran.')" class="btn btn-warning dropdown-toggle btn-xs" data-toggle="dropdown"><i class="fa fa-gears fa-fw"></i> Setup Tanggal </button>  
						';
			}
			
			$row[] = $data_penyedia;
			$row[] = GetFullDateFull($person->tgl_permohonan_pembayaran);
			//add html for action
			
			$view_termin = '';
			$data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->pembayaran->count_filtered(),
						"recordsFiltered" => $this->pembayaran->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}
	
	public function ajax_set_date(){
		$post = $this->input->post();
		
		$id_pembayaran = $post['id'];
		$tgl_permohonan_pembayaran = date('Y-m-d', strtotime($post['tgl_permohonan_pembayaran']));
		$result = $this->pembayaran->update(array('id_pembayaran'=>$id_pembayaran), array('tgl_permohonan_pembayaran'=>$tgl_permohonan_pembayaran, 'tgl_ba_pembayaran'=>$tgl_permohonan_pembayaran, 'tgl_nota_dinas_pencairan'=>$tgl_permohonan_pembayaran));
		
		$content['status'] = true;
		$content['notif'] = '<div class="alert alert-success" role="alert"><i class="fa fa-thumbs-o-up fa-fw"></i> Data Berhasil tersimpan!</div>';
		
		echo json_encode($content);
	}
	
	public function getDataListTermin(){
		$post = $this->input->post();
		$hasil = '';
		$id_pencairan = (!empty($post['id_pencairan'])) ? $post['id_pencairan'] : '';
		$id_pembayaran = (!empty($post['id_pembayaran'])) ? $post['id_pembayaran'] : '';
		// var_dump($post);exit;
		if(!empty($id_pencairan)){
			$data_select = getformselect_pembayaranv2('t_hasil_pekerjaan','id_hasil_pekerjaan','termin','status = "1" AND id_pencairan="'.$id_pencairan.'"', true, $id_pembayaran);
			
			$hasil = '<label class="control-label col-md-3">Pilih Termin</label><div class="col-md-9">'. $data_select .'<span class="help-block">*Bisa Lebih dari satu termin</span></div>';
		}
		
		echo $hasil;
	}
	
	public function getDataListPasti(){
		$post = $this->input->post();
		$hasil = '';
		$id_pencairan = (!empty($post['id_pencairan'])) ? $post['id_pencairan'] : '';
		$id_pembayaran = (!empty($post['id_pembayaran'])) ? $post['id_pembayaran'] : '';
		
		if(!empty($id_pencairan)){
			$data_id_spk = $this->pencairan->get_by_id($id_pencairan, 'id_spk');
			$data_cara_pembayaran = $this->spk->get_by_id($data_id_spk->id_spk, 'cara_pembayaran');
			
			if($data_cara_pembayaran->cara_pembayaran != '3'){
				$hasil = '
				<label class="control-label col-md-3">Nilai Pembayaran</label>
				<div class="col-md-9">
					<select class="form-control selectpicker show-tick" name="pasti" id="pasti" data-live-search="true" required="required" onchange="getPasti(this)">
						<option value="">Silahkan Pilih</option>
						<option value="1">Sudah ditentukan</option>
						<option value="2">Belum ditentukan</option>
					</select>
					<span class="help-block"></span>
				</div>
				';
			}
			
			
		}
		
		echo $hasil;
	}
	
	public function getDataListBelumPasti(){
		$post = $this->input->post();
		
		$content['class_name'] = get_class($this);	
		$content['content'] = $this->load->view('pembayaran/form_belum_pasti',@$content);
	}
	
	public function ajax_form()
	{		
		$content['class_name'] = get_class($this);	
		
		$post = $this->input->post();
		$id_pembayaran = (!empty($post['id_pembayaran'])) ? $post['id_pembayaran'] : '';
		
		
		//ini apabila mau menampilkan data untuk diedit
		if(!empty($id_pembayaran)){
			$data = $this->pembayaran->get_by_id($id_pembayaran);
			$data_id_pencairan = $this->hasil_pekerjaan->get_by_id_pembayaran($id_pembayaran, 'id_pencairan');
		
			$content['data_kontrak'] = getformselect_onclickv3('t_pencairan','id_pencairan','no_spk','nama_penyedia','a.status = 1');
			$content['id_pencairan'] = $data_id_pencairan['id_pencairan'];
			$content['data'] = $data;
		}else{
			$content['data_kontrak'] = getformselect_onclickv3('t_pencairan','id_pencairan','no_spk','nama_penyedia','a.status = 1', false, '', 'b.cdd DESC, b.id_spk DESC');
			//getformselect_onclickv3($table='',$field_id='',$field_name='',$field_name2='',$cond='', $multiple=false, $field_slc='',$order_by='')
		}
		
		$content['content'] = $this->load->view('pembayaran/form',@$content);
	}
	
	public function ajax_form_last_year()
	{		
		$content['class_name'] = get_class($this);	
		
		$post = $this->input->post();
		$id_pembayaran = (!empty($post['id_pembayaran'])) ? $post['id_pembayaran'] : '';
		
		$content['id_pembayaran'] = $id_pembayaran;
		$content['content'] = $this->load->view('pembayaran/form_last_year',@$content);
	}
		
	public function ajax_save()
	{
		$this->_validate();
		$post = $this->input->post();		
		$data = $post;
		
		unset($data['id'], $data['id_pencairan_teks'], $data['id_pencairan'], $data['id_hasil_pekerjaan'], $data['save_method']); //hapus
		$id = (!empty($post['id'])) ? $post['id'] : '';
		$id_pencairan = (!empty($post['id_pencairan_teks'])) ? $post['id_pencairan_teks'] : '';
		$id_hasil_pekerjaan = (!empty($post['id_hasil_pekerjaan'])) ? $post['id_hasil_pekerjaan'] : '';
		$data['harga_satuan'] = (!empty($post['harga_satuan'])) ? str_replace(',','',$post['harga_satuan']) : '';
		$data['jumlah_harga_satuan'] = (!empty($post['jumlah_harga_satuan'])) ? str_replace(',','',$post['jumlah_harga_satuan']) : '';
		$data['volume'] = (!empty($post['volume'])) ? str_replace(',','',$post['volume']) : '';
		
		if(!empty($id)){
			$created = array(
				'mdi' => 'web'
			);
		}else{
			$created = array(
				'cdb'=> $this->session->userdata('id_pegawai'),
				'cdi' => 'web'
			);
		}
		
		//adendum		
		$data['bayar_persen'] = (!empty($post['bayar_persen'])) ? str_replace(',','',$post['bayar_persen']) : '';
		$data['uang_muka'] = (!empty($post['uang_muka'])) ? str_replace(',','',$post['uang_muka']) : '';
		$data['retensi'] = (!empty($post['retensi'])) ? str_replace(',','',$post['retensi']) : '';
		$data['lain_lain'] = (!empty($post['lain_lain'])) ? str_replace(',','',$post['lain_lain']) : '';
		
		// action to save surat
		if($id && @$post['save_method'] == 'proses_adendum'){
			$this->inbox->update(array('id_pembayaran'=> $id), array('active'=> 0)); //menonaktifkan data sebelumnya, karena revisi
		}
		$result = $this->pembayaran->save($id, $data, $created);		
		
		if($result){
			$id_pembayaran = $result;
			
			if(!empty($post['id']))
			{
				//ambil data id_hasil_pekerjaan dari id_pencairan
				$get_id_hasil_pekerjaan = $this->pencairan->get_by_id_t_hasil_pekerjaan_pembayaran($post['id_pencairan'],$id_pembayaran, 'id_hasil_pekerjaan');
				
				if($get_id_hasil_pekerjaan){					
					foreach($get_id_hasil_pekerjaan as $rt){ //database
						$data_r = array('id_pembayaran' => null);
						$id_hasil = $rt['id_hasil_pekerjaan'];
						$this->hasil_pekerjaan->save($id_hasil, $data_r, $created);
						
					}
					
					foreach($id_hasil_pekerjaan as $rt){ //database
						$data_r = array('id_pembayaran' => $id_pembayaran);
						$id_hasil = $rt;
						$this->hasil_pekerjaan->save($id_hasil, $data_r, $created);						
					}
					
					$id_hasil_pekerjaan = $id_hasil;
				}			
			}else{
				if(!empty($id_hasil_pekerjaan)){
					$data_r = array('id_pembayaran' => $id_pembayaran);
					foreach($id_hasil_pekerjaan as $row)
					{
						//update data hasil pekerjaan		
						$id_hasil_pekerjaan = $row;
						$this->hasil_pekerjaan->save($row, $data_r, $created);
					}
				}		
			}
			if(@$post['save_method'] == 'proses_adendum'){
				$cek = $this->ajax_send_save($id_pembayaran);
				
				if($cek){				
					
					$daftar_ttd = $this->hasil_pekerjaan->get_by_id($id_hasil_pekerjaan, 'id_pegawai_pptk, nip_pegawai_pptk, nama_pegawai_pptk, nomenklatur_jabatan_pptk, id_pegawai_ppk, nip_pegawai_ppk, nama_pegawai_ppk, nomenklatur_jabatan_ppk');
					
					//insert data t_inbox
					$id_inbox = '';
					$data_inbox = array(
						'id_pembayaran'=> $id_pembayaran,
						'mailto'=> $daftar_ttd->id_pegawai_ppk,
						'nip_mailto'=> $daftar_ttd->nip_pegawai_ppk,
						'nama_mailto'=> $daftar_ttd->nama_pegawai_ppk,
						'nomenklatur_jabatan_mailto'=> $daftar_ttd->nomenklatur_jabatan_ppk,
						'mailfrom'=> $daftar_ttd->id_pegawai_pptk,
						'nip_mailfrom'=> $daftar_ttd->nip_pegawai_pptk,
						'nama_mailfrom'=> $daftar_ttd->nama_pegawai_pptk,
						'nomenklatur_jabatan_mailfrom'=> $daftar_ttd->nomenklatur_jabatan_pptk,
						'status'=> 2,
						'paraf'=> 1
					);
					$result_inbox = $this->inbox->save($id_inbox, $data_inbox, $created);
					//insert data t_inbox
					
					//notif email
					$cekemail = $this->user->get_by_id_pegawai($daftar_ttd->id_pegawai_ppk, 'email');
					
					$nomspk = $this->hasil_pekerjaan->get_by_id_detail_hasil_pekerjaan($id_hasil_pekerjaan, 'c.no_spk, c.nama_kegiatan, c.nama_pekerjaan, d.nama_penyedia, d.nama_perusahaan, d.kategori, b.nominal_bayar, a.nilai_pekerjaan, a.termin');
					
					$data_pekerjaan = $this->hasil_pekerjaan->get_by_id_pembayaranv2($id);
					$texttermin  = '';
					$nilaiTermin  = '';
					$viewBiaya  = '';
					$nomrs = 1;
					$totalsTermins = 0;
					foreach($data_pekerjaan as $rows){
						
						if(count($data_pekerjaan) == $nomrs++){
							$texttermin .= $rows['termin'];
							$viewBiaya .= number_format($rows['nilai_pekerjaan'], 0);
						}else{
							$texttermin .= $rows['termin'].', ';
							$viewBiaya .= number_format($rows['nilai_pekerjaan'],0).' + ';
						}
						$totalsTermins += $rows['nilai_pekerjaan'];
					}
					
					
					$nama_perush = '';
					if($nomspk->kategori == '1'){
						$nama_perush = '
							<tr>
								<td>Nama Perusahaan</td>
								<td>:</td>
								<td>'.$nomspk->nama_perusahaan.'</td>
							</tr>
						';
					}
					
					$nilaiTermin .= '
						<tr>
							<td>Nilai Termin '.$texttermin.'</td>
							<td>:</td>
							<td>'.number_format($totalsTermins, 0).' ('.$viewBiaya.')</td>
						</tr>
					';
					
					$perihal = 'Permohonan Pembayaran Termin '.$texttermin.' ('.$nomspk->no_spk.')';
					$data_surat = '
						<table width="100%">
							'.$nama_perush.'
							<tr>
								<td>Nama Penyedia/Penanggung Jawab</td>
								<td>:</td>
								<td>'.$nomspk->nama_penyedia.'</td>
							</tr>
							<tr>
								<td>Nama Kegiatan</td>
								<td>:</td>
								<td>'.$nomspk->nama_kegiatan.'</td>
							</tr>
							<tr>
								<td>Nama Pekerjaan</td>
								<td>:</td>
								<td>'.$nomspk->nama_pekerjaan.'</td>
							</tr>
							<tr>
								<td>Nilai Kontrak</td>
								<td>:</td>
								<td>'.number_format($nomspk->nominal_bayar, 0).'</td>
							</tr>
							'.
							$nilaiTermin
							.'
						</table>
					';
					
					$result_email = sendtoEmail($cekemail, null, $perihal, $data_surat);
					//notif email
					
					//send notif
					$data_riwayat = $data_inbox;
					$data_riwayat['notif_text'] = '';
					$data_riwayat['mdd'] = date('Y-m-d H:i:s');
					$data_riwayat['mdb'] = $this->session->userdata('id_pegawai');
					$data_riwayat['mdi'] = 'web';
					
					// send_pls_notification($data_riwayat, $result_inbox);
					//send notif
				}
			}
			
			$content['status'] = true;
			$content['id_pembayaran'] = $id_pembayaran;
			$content['notif'] = '<div class="alert alert-success" role="alert"><i class="fa fa-thumbs-o-up fa-fw"></i> Data berhasil disimpan</div>';			

		}else{
		  $content['status'] = false;
		  $content['notif'] = '<div class="alert alert-danger" role="alert"><i class="fa fa-thumbs-o-down fa-fw"></i> Data gagal tersimpan!</div>';
		}
		
		echo json_encode($content);
	}
		
	public function ajax_send_save($id_pembayaran)
	{		
		//kondisi cek dlu di inbox, apabila sudah ada statusnya 1 (aktif) maka tidak kirim inbox, hanya simpan data updatenya saja
		$result = $this->inbox->get_by_id(array('id_pembayaran'=> $id_pembayaran, 'active'=> 1), 'id_inbox');
		
		$count = is_array($result) ? count($result) : 0;
		if($count == 0){
			return true;
		}else{
			return false;
		}
		
	}
	
	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;
		
		
		if($this->input->post('id_pencairan') == '')
		{
			$data['inputerror'][] = 'id_pencairan';
			$data['error_string'][] = 'Data Kontrak harus diisi';
			$data['status'] = FALSE;
		}
		
		if($this->input->post('id_hasil_pekerjaan') == '' || empty($this->input->post('id_hasil_pekerjaan')))
		{
			$data['inputerror'][] = 'id_hasil_pekerjaan[]';
			$data['error_string'][] = 'Data Termin harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('no_permohonan_pembayaran') == '')
		{
			$data['inputerror'][] = 'no_permohonan_pembayaran';
			$data['error_string'][] = 'No Permohonan Pembayaran harus diisi';
			$data['status'] = FALSE;
		}
		
		if($this->input->post('no_ba_pembayaran') == '')
		{
			$data['inputerror'][] = 'no_ba_pembayaran';
			$data['error_string'][] = 'No Berita Acara Pembayaran harus diisi';
			$data['status'] = FALSE;
		}
		
		if($this->input->post('nota_dinas_pencairan') == '')
		{
			$data['inputerror'][] = 'nota_dinas_pencairan';
			$data['error_string'][] = 'No Nota Dinas Pencairan harus diisi';
			$data['status'] = FALSE;
		}
		
		/* if($this->input->post('satuan') == '')
		{
			$data['inputerror'][] = 'satuan';
			$data['error_string'][] = 'Satuan harus diisi';
			$data['status'] = FALSE;
		}
		
		if($this->input->post('uraian_dpa') == '')
		{
			$data['inputerror'][] = 'uraian_dpa';
			$data['error_string'][] = 'Jenis Pekerjaan harus diisi';
			$data['status'] = FALSE;
		}
		
		if($this->input->post('harga_satuan') == '')
		{
			$data['inputerror'][] = 'harga_satuan';
			$data['error_string'][] = 'Harga Satuan harus diisi';
			$data['status'] = FALSE;
		}
		if($this->input->post('jumlah_harga_satuan') == '')
		{
			$data['inputerror'][] = 'jumlah_harga_satuan';
			$data['error_string'][] = 'Jumlah Harga Satuan harus diisi';
			$data['status'] = FALSE;
		}
		
		if($this->input->post('volume') == '')
		{
			$data['inputerror'][] = 'volume';
			$data['error_string'][] = 'Volume harus diisi';
			$data['status'] = FALSE;
		} */
		
		if($this->input->post('pasti') !== null)
		{
			if($this->input->post('pasti') == '')
			{
				$data['inputerror'][] = 'pasti';
				$data['error_string'][] = 'Nilai Pembayaran harus diisi';
				$data['status'] = FALSE;
			}
		}
		
		/* if($this->input->post('bayar_persen') == '' || $this->input->post('bayar_persen') == 0)
		{
			$data['inputerror'][] = 'bayar_persen';
			$data['error_string'][] = 'Persentase Pembayaran harus diisi';
			$data['status'] = FALSE;
		} */
		
		

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
		$data_r = array("active" => 0);
		
		$hasil = $this->pembayaran->update(array('id_pembayaran' => $id), $data);
		$hasil = $this->inbox->update(array('id_pembayaran' => $id), $data_r);
		if($hasil > 0){
			$content['status'] = true;
			$content['notif'] = '<div class="alert alert-success" role="alert"><i class="fa fa-thumbs-o-up fa-fw"></i> Data berhasil di hapus</div>';			

		}else{
		  $content['status'] = false;
		  $content['notif'] = '<div class="alert-modal alert-danger" role="alert"><i class="fa fa-thumbs-o-down fa-fw"></i> Data gagal dihapus !</div>';
		}
		
		
		echo json_encode($content);
	}
	
	
	public function getDataRincianDPA(){
		$post = $this->input->post();
		
		$content['class_name'] = get_class($this);	
		
		$id_pencairan = (!empty($post['id_pencairan'])) ? $post['id_pencairan'] : '';
		$data_id_spk = $this->pencairan->get_by_id($id_pencairan, 'id_spk');
		
		$data_rincian = $this->rincian->get_by_id_multi($data_id_spk->id_spk, 'id, uraian');
		
		// if($this->session->userdata('id_pegawai') == '1690'){
			// var_dump($data_id_spk->id_spk, $this->db_ba->last_query());
			// exit;
		// }
		
		$content['data_rincian'] = $data_rincian;
		$content['content'] = $this->load->view('pembayaran/form_lampiran_ba',@$content);
	}
	
	public function download_pdf($id_pembayaran=''){
		
		$post = $this->input->post();
		$id_pembayaran = (!empty(@$post['id_pembayaran'])) ? @$post['id_pembayaran'] : @$post['id'];
		
		if(!empty($id_pembayaran)){	
			$data_all = $this->hasil_pekerjaan->get_by_id_detail($id_pembayaran,
			'
				a.id_hasil_pekerjaan,
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
				a.kuasa_anggaran,
				a.cdd,
				a.cdi,
				a.cdb,
				a.mdd,
				a.mdb,
				a.mdi,
				a.no_bast,
				a.tgl_bast,
				a.status,
				a.id_pembayaran,
				a.draft,
				b.pekerjaan_termin,
				b.id_pencairan,
				b.pekerjaan_termin,
				b.id_spk,
				b.nominal_bayar,
				b.nominal_bayar_terbilang,
				b.id_penyedia,
				b.nama_penyedia,
				b.alamat,
				b.bank,
				b.no_rekening_penyedia,
				b.cabang_bank,
				b.atas_nama_rekening,
				b.npwp,
				b.id_prokeg_aktif,
				c.id_pembayaran,
				c.no_permohonan_pembayaran,
				c.tgl_permohonan_pembayaran,
				c.no_ba_pembayaran,
				c.tgl_ba_pembayaran,
				c.nota_dinas_pencairan,
				c.tgl_nota_dinas_pencairan,
				c.lokasi,
				c.bayar_persen,
				c.uang_muka,
				c.retensi,
				c.lain_lain,
				c.pasti,
				c.uraian_dpa,
				c.volume,
				c.satuan,
				c.harga_satuan,
				c.jumlah_harga_satuan,
				c.nomor_kosong,
				d.no_spk,
				d.nama_pekerjaan,
				d.nama_kegiatan,
				d.tgl_pekerjaan,
				d.cara_pembayaran,
				d.dpa_skpd,
				d.kode_rek,
				d.pagu,
				d.file,
				d.tahap_kegiatan,
				d.waktu_pelaksanaan,
				d.type_kontrak,
				d.kode_unor,
				e.kategori,			
				e.nama_perusahaan,			
				e.jabatan,			
			'			
			);
			
			$data_pejabat_pphp = $this->paraf_pphp->get_where_criteria(array('id_hasil_pekerjaan'=> $data_all[0]->id_hasil_pekerjaan, 'status'=> 1), 'id_pegawai_pphp, nip_pegawai_pphp, nama_pegawai_pphp, nomenklatur_jabatan_pphp', true);
			$data_adendum = $this->adendum->get_wherev2(array('id_pembayaran'=> $id_pembayaran, 'status'=>1), 'no_adendum, tgl_adendum, biaya_adendum, waktu_pelaksanaan_adendum', true);
			
			
			$data_rincian = $this->rincian->get_by_id_multi($data_all[0]->id_spk, 'id');
			
			$where_rincian = array();
			if(!empty($data_rincian)){
				foreach($data_rincian as $rst){
					$where_rincian[] = $rst['id'];
				}
			}
			// var_dump($where_rincian, $data_rincian);exit;
			$data_pembayaran_rincian = $this->pembayaran_rincian->get_by_id($id_pembayaran, '
				uraian_dpa,
				volume,
				satuan,
				harga_satuan,
				jumlah_harga_satuan,
			',$where_rincian);
			$content['data_pembayaran'] = $data_all;
			$content['data_adendum'] = $data_adendum;
			$content['data_pembayaran_rincian'] = $data_pembayaran_rincian;
			// require_once("application/libraries/dompdf111/dompdf/autoload.inc.php");
            require_once("application/libraries/dompdf-master/vendor/autoload.php");
			// var_dump($data_pembayaran_rincian);
			// exit;
			$content['data_berkas'] = $this->berkas->get_where_criteria(array('active'=> 1), 'nama_berkas, id_berkas', true);
			$data_prokeg = '';
			foreach($data_all as $data)
			{	
				$data_uraian_pekerjaan = $this->uraian->get_where(array('id_hasil_pekerjaan'=> $data->id_hasil_pekerjaan, 'status'=> 1), 'uraian, volume, satuan, keterangan, harga_satuan, jumlah');
				
				$data->data_uraian_pekerjaan = $data_uraian_pekerjaan;	
				$content['data'] = $data;
				$content['data_pejabat_pphp'] = $data_pejabat_pphp;
				$content['pdf'] = true;
				
				
				$html1 = $this->load->view('hasil_pekerjaan/template_surat_penyerahan_hasil_pekerjaan', @$content, true);
				
				if(date('Y', strtotime($data->tgl_pekerjaan)) >= '2021')
				{
					$get_prokeg = $this->inbox->get($start = null, $length = null, $sort = null, $order = null, $wherex = array('id_spk'=> $data->id_spk), $like = null, $table='rincian_detail_spk', $field='*', $row_array=true, $join=false);
					
					$data_prokeg = json_decode($get_prokeg['data_prokeg_json'], true);
					
					$content['data_prokeg'] = $data_prokeg;
					
					$html2 = $this->load->view('hasil_pekerjaan/template_basv2_2021', @$content, true);
					$html3 = $this->load->view('hasil_pekerjaan/template_bast_2021', @$content, true);

				}else{
					$html2 = $this->load->view('hasil_pekerjaan/template_basv2', @$content, true);
					$html3 = $this->load->view('hasil_pekerjaan/template_bast', @$content, true);
				}
				
				
				
				//create pdf
				$post['paper'] = 'A4';			
				$dompdf = new Dompdf\Dompdf();
				$dompdf->load_html($html1);
				//$dompdf->set_paper('legal', 'portrait');
				if($post['paper'] == 'f4'){
					$paper_size = array(0,0,612.00,936.00); //opick
					$dompdf->set_paper($paper_size);
				}else{
					$dompdf->set_paper($post['paper'], 'portrait');
				}
				$dompdf->render();
				$output = $dompdf->output();				
                // var_dump('huya');die;
				
				$filename1 = 'surat_penyerahan_hasil_pekerjaan_termin_'.$data->termin.'_'.$data->id_hasil_pekerjaan.'.pdf';
				$file_to_save = 'assets/file/surat/'.$filename1;
				file_put_contents($file_to_save, $output);	
				unset($dompdf);
				
				
				if($data_pejabat_pphp)
				{
					$dompdf = new Dompdf\Dompdf();
					$dompdf->load_html($html2);		 
					
					if($post['paper'] == 'f4'){
						$paper_size = array(0,0,612.00,936.00); //opick
						$dompdf->set_paper($paper_size);
					}else{
						$dompdf->set_paper($post['paper'], 'portrait');
					}
					$dompdf->render();
					$output = $dompdf->output();				
					
					$filename2 = 'surat_berita_acara_penerimaan_hasil_pekerjaan_termin_'.$data->termin.'_'.$data->id_hasil_pekerjaan.'.pdf';
					$file_to_save = 'assets/file/surat/'.$filename2;
					file_put_contents($file_to_save, $output);			
					unset($dompdf);
				}				
				
				$dompdf = new Dompdf\Dompdf();
				$dompdf->load_html($html3);		 
				
				if($post['paper'] == 'f4'){
					$paper_size = array(0,0,612.00,936.00); //opick
					$dompdf->set_paper($paper_size);
				}else{
					$dompdf->set_paper($post['paper'], 'portrait');
				}
				$dompdf->render();
				$output = $dompdf->output();				
				
				$filename3 = 'surat_berita_acara_serah_terima_pekerjaan_termin_'.$data->termin.'_'.$data->id_hasil_pekerjaan.'.pdf';
				$file_to_save = 'assets/file/surat/'.$filename3;
				file_put_contents($file_to_save, $output);	
				unset($dompdf);
						
				$save_name_file[] = $filename1;					
				$save_name_file[] = $filename3;	
				if($data_pejabat_pphp)
				{
					$save_name_file[] = $filename2;	
				}			
			}
			$total = count($save_name_file);
			$all = $total - 1;
			
			
			$html4 = $this->load->view('pembayaran/template_surat_permohonan', @$content, true);	
			if(date('Y', strtotime($data_all[0]->tgl_pekerjaan)) >= '2021')
			{
				$content['data_prokeg'] = $data_prokeg;	
				$html5 = $this->load->view('pembayaran/template_lampiran_bapembayaran_v2_2021', @$content, true);
				$html6 = $this->load->view('pembayaran/template_bapembayaran_v2_2021', @$content, true);
				$html7 = $this->load->view('pembayaran/template_kwitansi_pembayaran_2021', @$content, true);

			}else{		
				$html5 = $this->load->view('pembayaran/template_lampiran_bapembayaran_v2', @$content, true);
				$html6 = $this->load->view('pembayaran/template_bapembayaran_v2', @$content, true);
				$html7 = $this->load->view('pembayaran/template_kwitansi_pembayaran', @$content, true);
			}
			
			
			//create pdf
			$post['paper'] = 'A4';			
			// require_once("application/libraries/dompdf083/dompdf/autoload.inc.php");
			$dompdf = new Dompdf\Dompdf();
			$dompdf->load_html($html4);		 
			//$dompdf->set_paper('legal', 'portrait');
			if($post['paper'] == 'f4'){
				$paper_size = array(0,0,612.00,936.00); //opick
				$dompdf->set_paper($paper_size);
			}else{
				$dompdf->set_paper($post['paper'], 'portrait');
			}
			$dompdf->render();
			$output = $dompdf->output();				
			
			$filename4 = 'surat_permohonan_pembayaran_'.$id_pembayaran.'.pdf';
			$file_to_save = 'assets/file/surat/'.$filename4;
			file_put_contents($file_to_save, $output);	
			unset($dompdf);
						
			
			//create pdf
			$post['paper'] = 'A4';			
			// require_once("application/libraries/dompdf083/dompdf/autoload.inc.php");
			$dompdf = new Dompdf\Dompdf();
			$dompdf->load_html($html5);		 
			if($post['paper'] == 'f4'){
				$paper_size = array(0,0,612.00,936.00); //opick
				$dompdf->set_paper($paper_size);
			}else{
				$dompdf->set_paper($post['paper'], 'portrait');
			}
			$dompdf->render();
			$output = $dompdf->output();				
			
			$filename5 = 'surat_lampiran_ba_pembayaran_'.$id_pembayaran.'.pdf';
			$file_to_save = 'assets/file/surat/'.$filename5;
			file_put_contents($file_to_save, $output);	
			unset($dompdf);
			
			
			//create pdf
			$post['paper'] = 'A4';			
			// require_once("application/libraries/dompdf083/dompdf/autoload.inc.php");
			$dompdf = new Dompdf\Dompdf();
			$dompdf->load_html($html6);		 
			if($post['paper'] == 'f4'){
				$paper_size = array(0,0,612.00,936.00); //opick
				$dompdf->set_paper($paper_size);
			}else{
				$dompdf->set_paper($post['paper'], 'portrait');
			}
			$dompdf->render();
			$output = $dompdf->output();				
			
			$filename6 = 'surat_ba_pembayaran_'.$id_pembayaran.'.pdf';
			$file_to_save = 'assets/file/surat/'.$filename6;
			file_put_contents($file_to_save, $output);	
			unset($dompdf);
						
			
			//create pdf
			$post['paper'] = 'A4';			
			// require_once("application/libraries/dompdf083/dompdf/autoload.inc.php");
			$dompdf = new Dompdf\Dompdf();
			$dompdf->load_html($html7);		 
			//$dompdf->set_paper('legal', 'portrait');
			if($post['paper'] == 'f4'){
				$paper_size = array(0,0,612.00,936.00); //opick
				$dompdf->set_paper($paper_size);
			}else{
				$dompdf->set_paper($post['paper'], 'portrait');
			}
			$dompdf->render();
			$output = $dompdf->output();				
			
			$filename7 = 'kwitansi_pembayaran_'.$id_pembayaran.'.pdf';
			$file_to_save = 'assets/file/surat/'.$filename7;
			file_put_contents($file_to_save, $output);	
			unset($dompdf);
						
			$filename_marge = 'ba_pencairan_'.$id_pembayaran.'.pdf';
			$kumpulanfile1 = array($filename7, $filename6, $filename5, $filename4);
			$save_name_file = array_reverse($save_name_file);
			$kumpulanfile2 = array_merge($kumpulanfile1, $save_name_file);
			$kumpulanfile = $kumpulanfile2;
			
			//marge pdf
			$margeFile = margeFilePDF($kumpulanfile, $filename_marge);
			//marge pdf
			
			echo json_encode(array("status"=> true, "url_file" => 'assets/file/surat/'.$filename_marge));
		}
	}
	
	
	public function download_pdf_permohonan(){
		
		$post = $this->input->post();
		// $id_hasil_pekerjaan = (!empty($post['id_hasil_pekerjaan'])) ? $post['id_hasil_pekerjaan'] : '';
		$id_pembayaran = 1;
		if(!empty($id_pembayaran)){	
			
			$content['data_pembayaran'] = $this->hasil_pekerjaan->get_by_id_detail($id_pembayaran);
			$html4 = $this->load->view('pembayaran/template_surat_permohonan', @$content, true);
			
			//create pdf
			$post['paper'] = 'A4';			
			require_once("application/libraries/dompdf083/dompdf/autoload.inc.php");
			$dompdf = new Dompdf\Dompdf();
			$dompdf->load_html($html4);		 
			//$dompdf->set_paper('legal', 'portrait');
			if($post['paper'] == 'f4'){
				$paper_size = array(0,0,612.00,936.00); //opick
				$dompdf->set_paper($paper_size);
			}else{
				$dompdf->set_paper($post['paper'], 'portrait');
			}
			$dompdf->render();
			$output = $dompdf->output();				
			
			$filename1 = 'surat_permohonan_pembayaran_'.$id_pembayaran.'.pdf';
			$file_to_save = 'assets/file/surat/'.$filename1;
			file_put_contents($file_to_save, $output);	
			
			//marge pdf
			// file_put_contents($file_to_save, $output);	
			// $dompdf->stream($filename1, array("Attachment" => false));
			// $pdf->Output('F',$filename_marge); 
			// redirect('assets/file/surat/'.$filename_marge); 
			//create pdf
		}
	}
	
	
	public function download_pdf_bapembayaran(){
		
		$post = $this->input->post();
		// $id_hasil_pekerjaan = (!empty($post['id_hasil_pekerjaan'])) ? $post['id_hasil_pekerjaan'] : '';
		$id_pembayaran = 1;
		if(!empty($id_pembayaran)){	
			
			$content['data_pembayaran'] = $this->hasil_pekerjaan->get_by_id_detail($id_pembayaran);
			$html5 = $this->load->view('pembayaran/template_bapembayaran', @$content, true);
			
			//create pdf
			$post['paper'] = 'A4';			
			require_once("application/libraries/dompdf083/dompdf/autoload.inc.php");
			$dompdf = new Dompdf\Dompdf();
			$dompdf->load_html($html5);		 
			if($post['paper'] == 'f4'){
				$paper_size = array(0,0,612.00,936.00); //opick
				$dompdf->set_paper($paper_size);
			}else{
				$dompdf->set_paper($post['paper'], 'portrait');
			}
			$dompdf->render();
			$output = $dompdf->output();				
			
			$filename1 = 'surat_ba_pembayaran_'.$id_pembayaran.'.pdf';
			$file_to_save = 'assets/file/surat/'.$filename1;
			file_put_contents($file_to_save, $output);	
			
			//marge pdf
			// file_put_contents($file_to_save, $output);	
			// $dompdf->stream($filename1, array("Attachment" => false));
			// $pdf->Output('F',$filename_marge); 
			// redirect('assets/file/surat/'.$filename_marge); 
			//create pdf
		}
	}
	
	public function download_pdf_kwitansi(){
		
		$post = $this->input->post();
		// $id_hasil_pekerjaan = (!empty($post['id_hasil_pekerjaan'])) ? $post['id_hasil_pekerjaan'] : '';
		$id_pembayaran = 1;
		if(!empty($id_pembayaran)){	
			
			$content['data_pembayaran'] = $this->hasil_pekerjaan->get_by_id_detail($id_pembayaran);
			$html6 = $this->load->view('pembayaran/template_kwitansi_pembayaran', @$content, true);
			
			//create pdf
			$post['paper'] = 'A4';	
			require_once("application/libraries/dompdf/dompdf_config.inc.php");
		
			$dompdf = new DOMPDF();		
			// $dompdf->load_html($html);		
			
			// require_once("application/libraries/dompdf083/dompdf/autoload.inc.php");
			// $dompdf = new Dompdf\Dompdf();
			$dompdf->load_html($html6);		 
			//$dompdf->set_paper('legal', 'portrait');
			if($post['paper'] == 'f4'){
				$paper_size = array(0,0,612.00,936.00); //opick
				$dompdf->set_paper($paper_size);
			}else{
				$dompdf->set_paper($post['paper'], 'portrait');
			}
			$dompdf->render();
			$output = $dompdf->output();				
			
			$filename1 = 'kwitansi_pembayaran_'.$id_pembayaran.'.pdf';
			$file_to_save = 'assets/file/surat/'.$filename1;
			file_put_contents($file_to_save, $output);	
			
			//marge pdf
			// file_put_contents($file_to_save, $output);	
			$dompdf->stream($filename1, array("Attachment" => false));
			// $pdf->Output('F',$filename_marge); 
			// redirect('assets/file/surat/'.$filename_marge); 
			//create pdf
		}
	}
	
	
	
}
