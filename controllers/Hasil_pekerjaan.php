<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hasil_pekerjaan extends CI_Controller {
	
	/*
	 * @author     		RENDY
	 * @author_email    rendyardian199@gmail.com
	 *  KOTA TANGERANG 
	 */

	public function __construct()
	{
		parent::__construct();
		$this->load->model('pencairan_model', 'pencairan');		
		$this->load->model('hasil_pekerjaan_model', 'hasil_pekerjaan');		
		$this->load->model('penyedia_model', 'penyedia');		
		$this->load->model('pembayaran_termin_model', 'pembayaran_termin');		
		$this->load->model('spk_model', 'spk');		
		$this->load->model('inbox_model', 'inbox');		
		$this->load->model('uraian_model', 'uraian');		
		$this->load->model('paraf_pphp_model', 'paraf_pphp');		
		$this->load->model('user_model', 'user');		
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
		$content['content'] = $this->load->view('hasil_pekerjaan/index',@$content,true);
		$this->load->view('ba/index', @$content,false,false);
	}
	
	public function ajax_hitungberkas()
	{
		$post = $this->input->post();
		$id_hasil_pekerjaan = $post['id_hasil_pekerjaan'];
		$id_pegawai = $this->session->userdata('id_pegawai');
		
		$OpenBerkas = getDataOpenBerkas();
		$ParafCeklis = getDataHasilBerkas(false, $id_hasil_pekerjaan, $id_pegawai);
		$tolakCeklis = getDataBerkasTidakLengkap($id_hasil_pekerjaan, $id_pegawai);
		
		$totalOpenBerkas = is_array($OpenBerkas) ? count($OpenBerkas): 0;
		$totalParafCeklis = is_array($ParafCeklis) ? count($ParafCeklis): 0;
		$totalTolakCeklis = is_array($tolakCeklis) ? count($tolakCeklis): 0;
		
		if($totalParafCeklis == $totalOpenBerkas && $totalTolakCeklis == 0){
			$return = array('status'=> true, 'teks'=> 'Lengkap', 'kondisi'=>1);
		}else if($totalParafCeklis == $totalOpenBerkas && $totalTolakCeklis >= 1){
			$return = array('status'=> true, 'teks'=> 'Tidak Lengkap', 'kondisi'=>2);
		}else{
			$return = array('status'=> false, 'teks'=> '', 'kondisi'=>3);
		}
		
		echo json_encode($return);
		
	}
	
	public function ajax_getberkas()
	{
		$post = $this->input->post();
		
		$id_berkas = @$post['name_id'];
		$id_hasil_pekerjaan = @$post['id'];
		$statusParaf = @$post['statusParaf'];
		$catatan = @$post['catatan'];
		
		$created = array(
			'cdb'=> $this->session->userdata('id_pegawai'),
			'cdi' => 'web'
		);
		
		$data = array(
			'id_berkas' => $id_berkas,
			'id_hasil_pekerjaan' => $id_hasil_pekerjaan,
			'status' => $statusParaf,
			'catatan' => $catatan
		);
		
		$result = $this->berkas->save_t_berkas($id_berkas,$data,$created);		
		
		echo json_encode(array('status'=> true));
	}
	
	public function ajax_list()
	{
		$this->load->helper('url');

		$list = $this->hasil_pekerjaan->get_datatables();
		// var_dump($this->db_ba->last_query());exit;
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $person) {
			$no++;
			$row = array();		
			
			if($person->kategori == 1){
				$data_penyedia = '<font style="font-weight: bold;">' . $person->nama_perusahaan . '</font><br>'.
								 '<font style="font-size:8pt;">' . $person->nama_penyedia . ' ('.$person->jabatan.')</font><br>
								 <font style="font-size:8pt;">'.$person->alamat.'</font><br>'.
								 '<font style="font-size:8pt;">NPWP: '.$person->npwp.'</font><br>'.
								 '<font style="font-size:8pt;">BANK: '.$person->bank.' '.$person->no_rekening_penyedia.' a.n '.$person->atas_nama_rekening.'</font>'
				;
			}else{
				$data_penyedia = '<font style="font-weight: bold;">' . $person->nama_penyedia . '</font><br>'.
								 '<font style="font-size:8pt;">'.$person->alamat.'</font><br>'.
								 '<font style="font-size:8pt;">NPWP: '.$person->npwp.'</font><br>'.
								 '<font style="font-size:8pt;">BANK: '.$person->bank.' '.$person->no_rekening_penyedia.' a.n '.$person->atas_nama_rekening.'</font>'
				;
			}
			
			$file = (!empty($person->file_pekerjaan)) ?  '<a href="'.base_url().'assets/file/dokumen_pekerjaan/'.$person->file_pekerjaan.'" target="_blank"><img class="imageThumb" src="'.base_url().'assets/file/kontrak/icon_pdf.png" title="Lampiran Hasil Pekerjaan"/></a>' : '';
			$data_kontrak = '<font style="font-size:8pt;">' . $person->no_spk . '</font><br>'.
							 '<font style="font-size:8pt;">'.$person->nama_pekerjaan.'</font><br>'.
							 '<font style="font-size:8pt;">'.$person->nama_kegiatan.'</font><br>'.
							 '<font style="font-weight: bold; font-size:8pt;">Pengajuan Termin	: '.$person->termin.'</font><br>'
			;

			if($person->draft == '0'){
				if($this->session->userdata('id_penyedia') && $person->pengajuan_penyedia == '1'){
					$status = '<div><i class="glyphicon glyphicon-pencil"></i> <span class="label label-warning">Diajukan</span></div>';		
				}else{
					if($this->session->userdata('id_penyedia')){
						$status = '<div><i class="glyphicon glyphicon-pencil"></i> <span class="label label-warning">Lengkapi</span></div>';		
					}else{
						$status = '<div><i class="glyphicon glyphicon-pencil"></i> <span class="label label-warning">Draft</span></div>';
					}
				}
			}else if($person->draft == '2'){
				$status = '<div><i class="glyphicon glyphicon-pencil"></i> <span class="label label-primary">Perbaikan</span></div>';
			}else{
				if($person->paraf == null){
					$status = '<div><i class="glyphicon glyphicon-eye-open"></i> <span class="label label-default">Belum diacc</span></div>';
				}else if($person->paraf == '0'){
					$status = '<div><i class="glyphicon glyphicon-eye-open"></i> <span class="label label-default">Ditolak</span></div>';
				}else if($person->paraf == '1'){
					$status = '<div><i class="glyphicon glyphicon-eye-open"></i> <span class="label label-default">Diacc</span></div>';
				}
			}
			
			
			$id_read = $person->id_hasil_pekerjaan;
			$keterangan = 0; //jika pembayaran maka 1, jika hasil pekerjaan maka 0
			$kk = 0;
			
			
			/* $row[] = '
						<div class="btn-group">
								<button type="button" class="btn btn-primary dropdown-toggle btn-xs" data-toggle="dropdown"><i class="fa fa-gears fa-fw"></i> Aksi <span class="caret"></span></button>
								
								<ul class="dropdown-menu" role="menu">
								  <li><a href="javascript:void(0)" onclick="edit_penyedia('.$person->id_hasil_pekerjaan.')"><i class="glyphicon glyphicon-edit"></i> Edit </a></li>
								  <li><a href="javascript:void(0)" onclick="delete_penyedia('.$person->id_hasil_pekerjaan.')"><i class="glyphicon glyphicon-trash"></i> Hapus </a></li>
								  <li><a href="javascript:void(0)" onclick="read('.@$keterangan.', '.@$id_read.', '.@$kk.')"><i class="glyphicon glyphicon-file"></i> Read </a></li>
								</ul> 
							  </div>'; */
							  
			$row[] = '<div style="cursor:pointer;" href="javascript:void(0)" onclick="read('.@$keterangan.', '.@$id_read.', '.@$kk.')">'.$data_penyedia.'</div>';
			$row[] = '<div style="cursor:pointer;" href="javascript:void(0)" onclick="read('.@$keterangan.', '.@$id_read.', '.@$kk.')">'.$data_kontrak.'</div>'.$file;
			// $row[] = '<div style="cursor:pointer;" href="javascript:void(0)" onclick="read('.@$keterangan.', '.@$id_read.', '.@$kk.')">'.$status.'</div>';			
			$row[] = '<div style="cursor:pointer;" href="javascript:void(0)" onclick="cek_status_draft('.$person->id_hasil_pekerjaan.', '.@$keterangan.', '.@$id_read.', '.@$kk.')">'.$status.'</div>';
			//add html for action
		
			$data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->hasil_pekerjaan->count_filtered(),
						"recordsFiltered" => $this->hasil_pekerjaan->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}
	
	
	public function ajax_bulk_delete()
	{
		$list_id = $this->input->post('id');
		// var_dump($list_id);exit;
		// foreach ($list_id as $id) {
			// $this->person->delete_by_id($id);
		// }
		echo json_encode(array("status" => TRUE));
	}
	
	public function cek_draft(){
		
		$id = $_GET['id'];
		
		$data = $this->hasil_pekerjaan->get_by_id($id, 'draft, pengajuan_penyedia');
		
		if($this->session->userdata('id_penyedia')){
			if($data->draft == '0'){
				if($data->pengajuan_penyedia == '1'){
					$data->draft = 1;	
				}
			}			
		}
		
		echo json_encode(array("status" => TRUE, 'draft' => @$data->draft));
		
	}
	
	
	public function ajax_form()
	{	
		$content['class_name'] = get_class($this);	
		
		$post = $this->input->post();
		
		$id_hasil_pekerjaan = (!empty($post['id_hasil_pekerjaan'])) ? $post['id_hasil_pekerjaan'] : '';
		$id_pencairan = (!empty($post['id_pencairan'])) ? $post['id_pencairan'] : '';

		$data_pegawai = get_pegawai(substr($this->session->userdata('kode_unor'), 0, 5));
		$data = '';
		$id_pegawai_pphp = '';
		$id_pegawai_ppk = '';
		$id_pegawai_pptk = '';
		$id_pegawai_bendahara = '';
		$id_pegawai_pengguna_anggaran = '';
		$data_pphp = array();
		$id_pejabat_pphp = '';
		
		//ini apabila mau menampilkan data untuk diedit
		if(!empty($id_hasil_pekerjaan)){
			$data = $this->hasil_pekerjaan->get_by_id($id_hasil_pekerjaan);
			$data_pphp = $this->paraf_pphp->get_by_idv2($id_hasil_pekerjaan, 'id_pegawai_pphp');
			
			$id_pencairan = $data->id_pencairan;
			$id_pegawai_ppk = (!empty($data->id_pegawai_ppk)) ? $data->id_pegawai_ppk : '';
			$id_pegawai_pptk = (!empty($data->id_pegawai_pptk)) ? $data->id_pegawai_pptk : '';
			$id_pegawai_bendahara = (!empty($data->id_pegawai_bendahara)) ? $data->id_pegawai_bendahara : '';
			$id_pegawai_pengguna_anggaran = (!empty($data->id_pegawai_pengguna_anggaran)) ? $data->id_pegawai_pengguna_anggaran : '';
			$content['data'] = $data;
			$content['data_termin'] = getformselect_terminv2('t_pencairan', 't_hasil_pekerjaan', 'pekerjaan_termin', 'id_pencairan', 'termin',  'id_hasil_pekerjaan', $id_pencairan, $id_hasil_pekerjaan, true);	
		}else{			
			$content['data_termin'] = getformselect_terminv2('t_pencairan', 't_hasil_pekerjaan', 'pekerjaan_termin', 'id_pencairan', 'termin',  'id_hasil_pekerjaan', $id_pencairan, $id_hasil_pekerjaan);	
			
			$data_pencairan = $this->inbox->get($start = null, $length = null, $sort = null, $order = null, $where = array('id_pencairan'=> $id_pencairan), $like = null, $table='t_pencairan', $field='*', $row_array=true, $join=false);
			
			$get_pejabat = $this->inbox->get($start = null, $length = null, $sort = null, $order = null, $where = array('id_prokeg_skpd'=> $data_pencairan['id_prokeg_aktif']), $like = null, $table='t_setting_pejabat', $field='*', $row_array=true, $join=false);
			
			if(!empty($get_pejabat['id_setting']))
			{
				$id_pegawai_pptk = (!empty($get_pejabat['id_pegawai_pptk'])) ? $get_pejabat['id_pegawai_pptk'] : false;
				$id_pegawai_ppk = (!empty($get_pejabat['id_pegawai_ppk'])) ? $get_pejabat['id_pegawai_ppk'] : false;
				
			}
		}
		
		$pphp = getformselect_service_multi($data_pegawai, 'id_pegawai_pphp', 'id_pegawai', 'nama_pegawai', true, @$data_pphp);
		$ppk = getformselect_servicev2($data_pegawai, 'id_pegawai_ppk', 'id_pegawai', 'nama_pegawai', $id_pegawai_ppk);
		$pptk = getformselect_servicev2($data_pegawai, 'id_pegawai_pptk', 'id_pegawai', 'nama_pegawai', $id_pegawai_pptk);
		$bendahara = getformselect_servicev2($data_pegawai, 'id_pegawai_bendahara', 'id_pegawai', 'nama_pegawai', $id_pegawai_bendahara);
		$pengguna_anggaran = getformselect_servicev2($data_pegawai, 'id_pegawai_pengguna_anggaran', 'id_pegawai', 'nama_pegawai', $id_pegawai_pengguna_anggaran);
		
		$content['data_pegawai'] = json_encode($data_pegawai); //ini digunakan untuk membantu pengambilan data pphp karena inputan tersebut bukan json array, jadi biar tidak mengecek berulang ke server	
		$content['id_pejabat_pphp'] = $id_pejabat_pphp;	
		$content['pphp'] = $pphp;	
		$content['ppk'] = $ppk;	
		$content['pptk'] = $pptk;	
		$content['bendahara'] = $bendahara;	
		$content['pengguna_anggaran'] = $pengguna_anggaran;	
		
		if($this->session->userdata('id_penyedia')){
			$content['content'] = $this->load->view('hasil_pekerjaan/form_penyedia',@$content);			
		}else{
			$content['content'] = $this->load->view('hasil_pekerjaan/form',@$content);
		}
	}
	
	function cek_pencairan_biaya_kontrak($id_pencairan = '', $nilai_pekerjaan = '', $id = ''){
		$result = $this->pencairan->get_by_id($id_pencairan, 'nominal_bayar');
		
		$array_criteria = (!empty($id)) ? array('id_pencairan'=> $id_pencairan, 'status'=> 1, 'id_hasil_pekerjaan !=' => $id) : array('id_pencairan'=> $id_pencairan, 'status'=> 1);
		$result_biaya_pencairan = $this->hasil_pekerjaan->get_where_criteria($array_criteria, 'SUM(nilai_pekerjaan) as nilai_pekerjaan');
		$data_pencairan = (!empty($result_biaya_pencairan['nilai_pekerjaan'])) ? (int) $result_biaya_pencairan['nilai_pekerjaan'] : 0 ;
		$total_pencairan = $nilai_pekerjaan + $data_pencairan;
		
		if($total_pencairan > $result->nominal_bayar){
			return false;
		}else{
			return true;
		}
		
	}
	
	public function ajax_prosesv1() //ini versi 1
	{
		$post = $this->input->post();
		$data = $post;
		
		unset($data['id']); //hapus
		$id = (!empty($post['id'])) ? $post['id'] : '';
		
		if(!empty($post['id'])){
			$data_uraian = $this->uraian->count_all($id); //cek data uraian
			if($data_uraian > 0){
				//ambil data hasil pekerjaan
				$data_pptk = $this->hasil_pekerjaan->get_by_id($id, 'id_pegawai_pptk, nip_pegawai_pptk, nama_pegawai_pptk, nomenklatur_jabatan_pptk');
				$data_pphp = $this->paraf_pphp->get_where_criteria(array('id_hasil_pekerjaan'=> $id, "status"=> 1), 'id_pegawai_pphp, nip_pegawai_pphp, nama_pegawai_pphp, nomenklatur_jabatan_pphp', 1);
				
				if(!empty($data_pphp)){
					//insert data t_inbox
					$created = array(
						'cdb'=> $this->session->userdata('id_pegawai'),
						'cdi' => 'web'
					);
					
					//cek status perbaikan atau bukan hasil pekerjaannya
					$status_perbaikan = $this->hasil_pekerjaan->get_by_id($id, 'draft');
					if($status_perbaikan->draft == 2){ //apabila perbaikan maka status pemaraf terakhir di nonaktifkan diinbox						
						$this->inbox->updatev2(array("id_hasil_pekerjaan"=> $id), array("active"=> 0));
					}
					//cek status perbaikan atau bukan hasil pekerjaannya
					
					
					// $data = array_merge( (array) $data_pptk, $data_pphp);
					$data = (array) $data_pptk;
					
					$id_inbox = '';
					$data_inbox = array(
						'id_hasil_pekerjaan'=> $id,
						'mailto'=> $data_pphp[0]['id_pegawai_pphp'],
						'nip_mailto'=> stripslashes(preg_replace("/[^A-Za-z0-9.]/", "" , $data_pphp[0]['nip_pegawai_pphp'])),
						'nama_mailto'=> $data_pphp[0]['nama_pegawai_pphp'],
						'nomenklatur_jabatan_mailto'=> $data_pphp[0]['nomenklatur_jabatan_pphp'],
						'mailfrom'=> $data['id_pegawai_pptk'],
						'nip_mailfrom'=> stripslashes(preg_replace("/[^A-Za-z0-9.]/", "" , $data['nip_pegawai_pptk'])),
						'nama_mailfrom'=> $data['nama_pegawai_pptk'],
						'nomenklatur_jabatan_mailfrom'=> $data['nomenklatur_jabatan_pptk'],
						'status'=> 0,
						'paraf'=> null
					);
					$result_inbox = $this->inbox->save($id_inbox, $data_inbox, $created);
					//insert data t_inbox
					
					//notif email
					$cekemail = $this->user->get_by_id_pegawai($data_pphp[0]['id_pegawai_pphp'], 'email');
					
					$data_pekerjaan = $this->hasil_pekerjaan->get_by_id_detail_hasil_pekerjaan($id);
				
					$perihal = 'Penyerahan Hasil Pekerjaan Termin '.$data_pekerjaan->termin.' ('.$data_pekerjaan->no_spk.')';
					$data_surat = '
						<table width="100%">
							<tr>
								<td>Nama Kegiatan</td>
								<td>:</td>
								<td>'.$data_pekerjaan->nama_kegiatan.'</td>
							</tr>
							<tr>
								<td>Nama Pekerjaan</td>
								<td>:</td>
								<td>'.$data_pekerjaan->nama_pekerjaan.'</td>
							</tr>
						</table>
					';
					$result_email = sendtoEmail($cekemail, null, $perihal, $data_surat);
					//notif email
					
					//update status draft 0 menjadi terkirim 1
					$this->hasil_pekerjaan->update(array('id_hasil_pekerjaan'=> $id), array('draft'=> 1));
					
					//send notif
					$data_riwayat = $data_inbox;
					$data_riwayat['notif_text'] = '';
					$data_riwayat['mdd'] = date('Y-m-d H:i:s');
					$data_riwayat['mdb'] = $this->session->userdata('id_pegawai');
					$data_riwayat['mdi'] = 'web';
					
					// send_pls_notification($data_riwayat, $result_inbox);
					//send notif
					$content['status'] = true;
					$content['notif'] = '<div class="alert alert-success" role="alert"><i class="fa fa-thumbs-o-up fa-fw"></i> Data berhasil terkirim</div>';
				}else{
					$content['status'] = false;
					$content['notif'] = '<div class="alert alert-danger" role="alert"><i class="fa fa-thumbs-o-down fa-fw"></i> Data PPHP Tidak Tersedia!</div>';
				}
			}else{
					$content['status'] = false;
					$content['notif'] = '<div class="alert alert-danger" role="alert"><i class="fa fa-thumbs-o-down fa-fw"></i> Silahkan Isi Data Uraian!</div>';
			}			
		}else{
			$content['status'] = false;
			$content['notif'] = '<div class="alert alert-danger" role="alert"><i class="fa fa-thumbs-o-down fa-fw"></i> Data gagal terkirim!</div>';
		}
		
		echo json_encode($content);
		
	}
	
	public function ajax_proses()  //ini versi 2
	{
		$post = $this->input->post();
		$data = $post;
		
		unset($data['id']); //hapus
		$id = (!empty($post['id'])) ? $post['id'] : '';
		
		if(!empty($post['id'])){
			$data_uraian = $this->uraian->count_all($id); //cek data uraian
			if($data_uraian > 0){
				//ambil data hasil pekerjaan
				$data_pptk = $this->hasil_pekerjaan->get_by_id($id, 'id_pegawai_pptk, nip_pegawai_pptk, nama_pegawai_pptk, nomenklatur_jabatan_pptk, id_pegawai_ppk, nip_pegawai_ppk, nama_pegawai_ppk, nomenklatur_jabatan_ppk, no_srt_penyerahan, tgl_srt_penyerahan, file_pekerjaan');
				
				//cek jika belum lengkap maka tidak bisa
				if(empty($data_pptk->no_srt_penyerahan)){
					$content['status'] = false;
					$content['notif'] = '<div class="alert alert-danger" role="alert"><i class="fa fa-thumbs-o-down fa-fw"></i> Silahkan Lengkapi Nomor Surat Penyerahan Hasil Pekerjaan!</div>';
					
					echo json_encode($content);
					exit;
				}
				
				if(empty($data_pptk->tgl_srt_penyerahan)){
					$content['status'] = false;
					$content['notif'] = '<div class="alert alert-danger" role="alert"><i class="fa fa-thumbs-o-down fa-fw"></i> Silahkan Lengkapi Tanggal Surat Penyerahan Hasil Pekerjaan!</div>';
					
					echo json_encode($content);
					exit;
				}
				
				if(empty($data_pptk->file_pekerjaan)){
					$content['status'] = false;
					$content['notif'] = '<div class="alert alert-danger" role="alert"><i class="fa fa-thumbs-o-down fa-fw"></i> Silahkan Lengkapi File Hasil Pekerjaan!</div>';
					
					echo json_encode($content);
					exit;
				}
				
				//insert data t_inbox
				$created = array(
					'cdb'=> (!empty($this->session->userdata('id_pegawai'))) ? $this->session->userdata('id_pegawai') : '',
					'cdi' => 'web'
				);
				
				$id_inbox = '';
				$data = (array) $data_pptk;
				
				//cek status perbaikan atau bukan hasil pekerjaannya
				$status_perbaikan = $this->hasil_pekerjaan->get_by_id($id, 'draft');
				if($status_perbaikan->draft == 2){ //apabila perbaikan maka status pemaraf terakhir di nonaktifkan diinbox						
					
					$data_pejabat_pphp = $this->paraf_pphp->get_by_idv2($id);
					
					$data_mailfrom_last = $this->inbox->get_by_id(array("id_hasil_pekerjaan"=> $id), 'mailfrom');
					
					// $cek_ready_pphp = $this->paraf_pphp->get_where_criteria(array("id_hasil_pekerjaan"=> $id, "status"=>1, "id_pegawai_pphp" => $data_mailfrom_last->mailfrom), 'id_pegawai_pphp');
					
					$cek = $this->inbox->updatev2(array("id_hasil_pekerjaan"=> $id, 'type'=> '1'), array("active"=> 0), true);	
					
					//dan berkas yg diperiksa seluruhnya menjadi nonaktif
					$this->berkas->update(array("id_hasil_pekerjaan"=> $id, 'type'=> '1'),array("active"=> 0), 't_berkas');
					
					//get data terakhir

					$data_inbox = array(
						'id_hasil_pekerjaan'=> $id,
						'mailto'=> $data['id_pegawai_ppk'],
						'nip_mailto'=> stripslashes(preg_replace("/[^A-Za-z0-9.]/", "" , $data['nip_pegawai_ppk'])),
						'nama_mailto'=> $data['nama_pegawai_ppk'],
						'nomenklatur_jabatan_mailto'=> $data['nomenklatur_jabatan_ppk'],
						'mailfrom'=> $data['id_pegawai_pptk'],
						'nip_mailfrom'=> stripslashes(preg_replace("/[^A-Za-z0-9.]/", "" , $data['nip_pegawai_pptk'])),
						'nama_mailfrom'=> $data['nama_pegawai_pptk'],
						'nomenklatur_jabatan_mailfrom'=> $data['nomenklatur_jabatan_pptk'],
						'status'=> 0,
						'paraf'=> null
					);

					
				}else{
					$data_inbox = array(
						'id_hasil_pekerjaan'=> $id,
						'mailto'=> $data['id_pegawai_ppk'],
						'nip_mailto'=> stripslashes(preg_replace("/[^A-Za-z0-9.]/", "" , $data['nip_pegawai_ppk'])),
						'nama_mailto'=> $data['nama_pegawai_ppk'],
						'nomenklatur_jabatan_mailto'=> $data['nomenklatur_jabatan_ppk'],
						'mailfrom'=> $data['id_pegawai_pptk'],
						'nip_mailfrom'=> stripslashes(preg_replace("/[^A-Za-z0-9.]/", "" , $data['nip_pegawai_pptk'])),
						'nama_mailfrom'=> $data['nama_pegawai_pptk'],
						'nomenklatur_jabatan_mailfrom'=> $data['nomenklatur_jabatan_pptk'],
						'status'=> 0,
						'paraf'=> null
					);
				}
				//cek status perbaikan atau bukan hasil pekerjaannya
				
				$result_inbox = $this->inbox->save($id_inbox, $data_inbox, $created);
				//insert data t_inbox
				
				
				//notif email
				$cekemail = $this->user->get_by_id_pegawai($data['id_pegawai_ppk'], 'email');
				
				$data_pekerjaan = $this->hasil_pekerjaan->get_by_id_detail_hasil_pekerjaan($id, 'c.`no_spk`, c.`nama_kegiatan`, c.`nama_pekerjaan`, d.nama_penyedia, d.nama_perusahaan, d.kategori, b.nominal_bayar, a.nilai_pekerjaan, a.termin
				');
				$nama_perush = '';
				if($data_pekerjaan->kategori == '1'){
					$nama_perush = '
						<tr>
							<td>Nama Perusahaan</td>
							<td>:</td>
							<td>'.$data_pekerjaan->nama_perusahaan.'</td>
						</tr>
					';
				}
				
				$perihal = 'Penyerahan Hasil Pekerjaan Termin '.$data_pekerjaan->termin.' ('.$data_pekerjaan->no_spk.')';
				$data_surat = '
					<table width="100%">
						'.$nama_perush.'
						<tr>
							<td>Nama Penyedia/Penanggung Jawab</td>
							<td>:</td>
							<td>'.$data_pekerjaan->nama_penyedia.'</td>
						</tr>
						<tr>
							<td>Nama Kegiatan</td>
							<td>:</td>
							<td>'.$data_pekerjaan->nama_kegiatan.'</td>
						</tr>
						<tr>
							<td>Nama Pekerjaan</td>
							<td>:</td>
							<td>'.$data_pekerjaan->nama_pekerjaan.'</td>
						</tr>
						<tr>
							<td>Nilai Kontrak</td>
							<td>:</td>
							<td>'.number_format($data_pekerjaan->nominal_bayar, 0).'</td>
						</tr>
						<tr>
							<td>Nilai Termin</td>
							<td>:</td>
							<td>'.number_format($data_pekerjaan->nilai_pekerjaan,0).'</td>
						</tr>
					</table>
				';
				$result_email = sendtoEmail($cekemail, null, $perihal, $data_surat);
				//notif email
				
				//update status draft 0 menjadi terkirim 1
				$this->hasil_pekerjaan->update(array('id_hasil_pekerjaan'=> $id), array('draft'=> 1));
				
				//send notif
				$data_riwayat = $data_inbox;
				$data_riwayat['notif_text'] = '';
				$data_riwayat['mdd'] = date('Y-m-d H:i:s');
				$data_riwayat['mdb'] = $this->session->userdata('id_pegawai');
				$data_riwayat['mdi'] = 'web';
				
				// send_pls_notification($data_riwayat, $result_inbox);
				//send notif
				$content['status'] = true;
				$content['notif'] = '<div class="alert alert-success" role="alert"><i class="fa fa-thumbs-o-up fa-fw"></i> Data berhasil terkirim</div>';
			}else{
					$content['status'] = false;
					$content['notif'] = '<div class="alert alert-danger" role="alert"><i class="fa fa-thumbs-o-down fa-fw"></i> Silahkan Isi Data Uraian!</div>';
			}			
		}else{
			$content['status'] = false;
			$content['notif'] = '<div class="alert alert-danger" role="alert"><i class="fa fa-thumbs-o-down fa-fw"></i> Data gagal terkirim!</div>';
		}
		
		echo json_encode($content);
		
	}
	
	public function ajax_save_penyedia()
	{
		$this->_validate();
		$post = $this->input->post();
		$data = $post;
		
		unset($data['id'], $data['id_pegawai_pphp'], $data['data_pegawai']); //hapus
		$id = (!empty($post['id'])) ? $post['id'] : '';	
		
		$tgl_srt_penyerahan = date('Y-m-d', strtotime($post['tgl_srt_penyerahan']));
		$data['tgl_srt_penyerahan'] = $tgl_srt_penyerahan;
		$data['no_srt_penyerahan'] = $post['no_srt_penyerahan'];
		$data['pengajuan_penyedia'] = 1;
		$nilai_pekerjaan =  str_replace(',','',$post['nilai_pekerjaan']);
		$nilai_pekerjaan_terbilang = terbilang($nilai_pekerjaan);
		$data_biaya = array('nilai_pekerjaan' => $nilai_pekerjaan, 'nilai_pekerjaan_terbilang' => $nilai_pekerjaan_terbilang);
		$data = array_merge($data, $data_biaya);
		
		if(!empty($_FILES['file_pekerjaan']['name']))
		{
			$upload = $this->_do_upload('file_pekerjaan');
			$data['file_pekerjaan'] = $upload;
		}
		
		// action to save surat
		$result = $this->hasil_pekerjaan->save($id, $data);
		
		if($result){			
			$content['id_hasil_pekerjaan'] = $result;
			$content['status'] = true;
			$content['notif'] = '<div class="alert alert-success" role="alert"><i class="fa fa-thumbs-o-up fa-fw"></i> Data berhasil disimpan</div>';			

		}else{
		  $content['status'] = false;
		  $content['notif'] = '<div class="alert alert-danger" role="alert"><i class="fa fa-thumbs-o-down fa-fw"></i> Data gagal tersimpan!</div>';
		}
		
		echo json_encode($content);
		
	}
	
	public function ajax_save()
	{
		// var_dump($this->input->post());exit;
		$this->_validate();
		$post = $this->input->post();
		$data = $post;
		
		unset($data['id'], $data['id_pegawai_pphp'], $data['data_pegawai']); //hapus
		$id = (!empty($post['id'])) ? $post['id'] : '';	
		
		//mencari biaya
		$id_pencairan = (!empty($post['id_pencairan'])) ? $post['id_pencairan'] : '';
		$data_pencairan = $this->pencairan->get_by_id($id_pencairan, 'pekerjaan_termin, nominal_bayar');
		$nilai_pekerjaan =  str_replace(',','',$post['nilai_pekerjaan']);
		$nilai_pekerjaan_terbilang = terbilang($nilai_pekerjaan);
		$data_biaya = array('nilai_pekerjaan' => $nilai_pekerjaan, 'nilai_pekerjaan_terbilang' => $nilai_pekerjaan_terbilang);
		//mencari biaya
		
		if($nilai_pekerjaan == 0)
		{			
		  $content['status'] = false;
		  $content['notif'] = '<div class="alert alert-danger" role="alert"><i class="fa fa-thumbs-o-down fa-fw"></i> Nominal Bayar tidak boleh nol!</div>';
		  echo json_encode($content);
		  exit;
		}
		
		$biaya = $this->cek_pencairan_biaya_kontrak($id_pencairan, $nilai_pekerjaan, $id);
		if($biaya == false){			
			$content['status'] = false;
			$content['notif'] = '<div class="alert alert-danger" role="alert"><i class="fa fa-thumbs-o-down fa-fw"></i> Biaya Pencairan Lebih Besar Dari dari Biaya Kontrak!</div>';
			echo json_encode($content);
			return false;
		}
		
				
		//olahan untuk di marge kembali
		$pjbtpenerima_tglsk = date('Y-m-d', strtotime($post['pjbtpenerima_tglsk']));
		$tanggal_bas = date('Y-m-d', strtotime($post['tanggal_bas']));
		$tgl_srt_penyerahan = date('Y-m-d', strtotime($post['tgl_srt_penyerahan']));
		$tgl_bast = date('Y-m-d', strtotime($post['tgl_bast']));
		$tanggal_convert = array('pjbtpenerima_tglsk' => $pjbtpenerima_tglsk, 'tanggal_bas' => $tanggal_bas, 'tgl_srt_penyerahan' => $tgl_srt_penyerahan, 'tgl_bast' => $tgl_bast);
		
		$data_pegawai_pphp = (!empty($post['id_pegawai_pphp'])) ? marge_data_pegawaiv3($post['id_pegawai_pphp'], json_decode($post['data_pegawai'], true), 'pphp', true) : '';
		
		
		
		// if($id == '2334'){
		// 	var_dump($data_pegawai_pphp, $post['id_pegawai_pphp'], $post['data_pegawai']);
		// 	exit;
		// }
		
		$data_pegawai_ppk = (!empty($post['id_pegawai_ppk'])) ? marge_data_pegawaiv2($post['id_pegawai_ppk'], 'ppk', false) : '';	
		$data_pegawai_pptk = (!empty($post['id_pegawai_pptk'])) ? marge_data_pegawaiv2($post['id_pegawai_pptk'], 'pptk', false) : '';	
		$data_pegawai_bendahara = (!empty($post['id_pegawai_bendahara'])) ? marge_data_pegawaiv2($post['id_pegawai_bendahara'], 'bendahara', false) : '';	
		$data_pegawai_pengguna_anggaran = (!empty($post['id_pegawai_pengguna_anggaran'])) ? marge_data_pegawaiv2($post['id_pegawai_pengguna_anggaran'], 'pengguna_anggaran', false) : '';	
		
		$marge_data = array_merge($data, $data_pegawai_pptk, $data_pegawai_ppk, $data_pegawai_bendahara, $data_pegawai_pengguna_anggaran, $tanggal_convert, $data_biaya);
		
		//olahan untuk di marge kembali
		$data = $marge_data;		
		
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
		
		
		if(!empty($_FILES['file_pekerjaan']['name']))
		{
			$upload = $this->_do_upload('file_pekerjaan');
			$data['file_pekerjaan'] = $upload;
		}
		
		// action to save surat
		$result = $this->hasil_pekerjaan->save($id, $data, $created);
		
		if($result){
			//insert paraf pphp			
			$this->paraf_pphp('', $data_pegawai_pphp, $result);
			//insert paraf pphp
			
			$content['id_hasil_pekerjaan'] = $result;
			$content['status'] = true;
			$content['notif'] = '<div class="alert alert-success" role="alert"><i class="fa fa-thumbs-o-up fa-fw"></i> Data berhasil disimpan</div>';
			
			$data_pekerjaan = $this->hasil_pekerjaan->get_by_id_detail_hasil_pekerjaan($result);
			
			
			
			if($data_pekerjaan->sendemail == '0') //0 belum pernah dikirimin email utk pencairan termin ini
			{
				
				//notif email
				$join[] = array('nama_tabel'=> 'user_akses b', 'kunci'=> 'a.id_penyedia = b.id_penyedia');
				
				$data_penyedia = $this->inbox->get($start = null, $length = null, $sort = null, $order = null, $where = array('a.id_penyedia'=> $data_pekerjaan->id_penyedia), $like = null, $table='m_penyedia a', $field='a.*, b.email', $row_array=true, $join);
			
				$cekemail = (object) array('email'=> @$data_penyedia['email']);
			
				$perihal = '[E-Kontrak] Hasil Pekerjaan Termin '.$data_pekerjaan->termin.' ('.$data_pekerjaan->no_spk.')';
				
				
				$data_prokeg = $this->inbox->get($start = null, $length = null, $sort = null, $order = null, $wherex = array('a.id_spk'=> $data_pekerjaan->id_spk, 'id_prokeg_aktif'=> $data_pekerjaan->id_prokeg_aktif), $like = null, $table='rincian_detail_spk a', $field='a.data_prokeg_json', $row_array=true, $join=false);
				
				$json_decode = json_decode($data_prokeg['data_prokeg_json'], true);
				
				$data_surat = '
					Silahkan melengkapi laporan hasil pekerjaan <a href="https://e-kontrak.tangerangkota.go.id/"> di aplikasi e-kontrak </a> dengan data kontrak sebagai berikut :
					<table width="100%">
						<tr>
							<td>Nama Penyedia</td>
							<td>:</td>
							<td>'.@$data_penyedia['nama_penyedia'].'</td>
						</tr>
						<tr>
							<td>Nama Kegiatan</td>
							<td>:</td>
							<td>'.@$json_decode[1]['uraian_prokeg'].'</td>
						</tr>
						<tr>
							<td>Nama Sub Kegiatan</td>
							<td>:</td>
							<td>'.@$data_pekerjaan->nama_kegiatan.'</td>
						</tr>
						<tr>
							<td>Nama Pekerjaan</td>
							<td>:</td>
							<td>'.@$data_pekerjaan->nama_pekerjaan.'</td>
						</tr>
					</table>
				';
				$result_email = sendtoEmail($cekemail, null, $perihal, $data_surat);
				// notif email
				
				if($result_email)
				{
					$this->inbox->update($where=array('id_hasil_pekerjaan'=> $data_pekerjaan->id_hasil_pekerjaan), $data=array('sendemail'=> 1), $table='t_hasil_pekerjaan');
				}
			}

		}else{
		  $content['status'] = false;
		  $content['notif'] = '<div class="alert alert-danger" role="alert"><i class="fa fa-thumbs-o-down fa-fw"></i> Data gagal tersimpan!</div>';
		}
		
		echo json_encode($content);
	}
	
	public function paraf_pphp($id = '', $data = array(), $id_hasil_pekerjaan = '', $created=''){
			
			//ambil data yg sudah ada, siapa saja pphpnya			
			$data_pphp = $this->paraf_pphp->get_by_idv2($id_hasil_pekerjaan, 'id_paraf_pphp, id_pegawai_pphp');
			
			if(!empty($data_pphp)){
				//hapus semua, data yg ada
				$this->paraf_pphp->delete_by_id($id_hasil_pekerjaan);
				
				//ini pasti bukan edit, tapi data baru input
				foreach($data as $row)
				{				
					$row['id_hasil_pekerjaan'] = $id_hasil_pekerjaan;
					$this->paraf_pphp->save($id, $row, $created);			
				}
			}else{
				//ini pasti bukan edit, tapi data baru input
				if($data){
					foreach($data as $row)
					{				
						$row['id_hasil_pekerjaan'] = $id_hasil_pekerjaan;
						$this->paraf_pphp->save($id, $row, $created);			
					}
				}
			}
	}
	
	public function ajax_cari_data()
	{
		$result = $this->hasil_pekerjaan->get_data_search($_POST['query']); 
		echo '<pre>';
		var_dump($result);
		echo '</pre>';
		exit;
		echo json_encode($result);
	}
	
	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;
		
		
		if($this->session->userdata('id_penyedia')){
			if($this->input->post('no_srt_penyerahan') == '')
			{
				$data['inputerror'][] = 'no_srt_penyerahan';
				$data['error_string'][] = 'Nomor Surat Penyerahan harus diisi';
				$data['status'] = FALSE;
			}

			if($this->input->post('tgl_srt_penyerahan') == '')
			{
				$data['inputerror'][] = 'tgl_srt_penyerahan';
				$data['error_string'][] = 'Tanggal Surat Penyerahan Lengkap harus diisi';
				$data['status'] = FALSE;
			}

			if($this->input->post('termin') == '')
			{
				$data['inputerror'][] = 'termin';
				$data['error_string'][] = 'Termin Pengajuan harus diisi';
				$data['status'] = FALSE;
			}
			
			if(empty($_FILES['file_pekerjaan']['name']))
			{
				$data['inputerror'][] = 'file_pekerjaan';
				$data['error_string'][] = 'File Pekerjaan harus diisi';
				$data['status'] = FALSE;
			}
		}else{
			/* if($this->input->post('no_srt_penyerahan') == '')
			{
				$data['inputerror'][] = 'no_srt_penyerahan';
				$data['error_string'][] = 'Nomor Surat Penyerahan harus diisi';
				$data['status'] = FALSE;
			}

			if($this->input->post('tgl_srt_penyerahan') == '')
			{
				$data['inputerror'][] = 'tgl_srt_penyerahan';
				$data['error_string'][] = 'Tanggal Surat Penyerahan Lengkap harus diisi';
				$data['status'] = FALSE;
			} */

			if($this->input->post('termin') == '')
			{
				$data['inputerror'][] = 'termin';
				$data['error_string'][] = 'Termin Pengajuan harus diisi';
				$data['status'] = FALSE;
			}

			// if($this->input->post('pjbtpenerima_nosk') == '')
			// {
			// 	$data['inputerror'][] = 'pjbtpenerima_nosk';
			// 	$data['error_string'][] = 'No SK PPHP harus diisi';
			// 	$data['status'] = FALSE;
			// }

			// if($this->input->post('pjbtpenerima_tglsk') == '')
			// {
			// 	$data['inputerror'][] = 'pjbtpenerima_tglsk';
			// 	$data['error_string'][] = 'Tanggal SK PPHP harus diisi';
			// 	$data['status'] = FALSE;
			// }

			// if($this->input->post('id_pegawai_pphp') == '')
			// {
			// 	$data['inputerror'][] = 'id_pegawai_pphp';
			// 	$data['error_string'][] = 'Nama Pegawai PPHP harus diisi';
			// 	$data['status'] = FALSE;
			// }

			if($this->input->post('id_pegawai_ppk') == '')
			{
				$data['inputerror'][] = 'id_pegawai_ppk';
				$data['error_string'][] = 'Nama Pegawai PPK harus diisi';
				$data['status'] = FALSE;
			}
			
			if($this->input->post('nilai_pekerjaan') == '')
			{
				$data['inputerror'][] = 'nilai_pekerjaan';
				$data['error_string'][] = 'Biaya Pencairan harus diisi';
				$data['status'] = FALSE;
			}

			if($this->input->post('id_pegawai_pptk') == '')
			{
				$data['inputerror'][] = 'id_pegawai_pptk';
				$data['error_string'][] = 'Nama Pegawai PPTK harus diisi';
				$data['status'] = FALSE;
			}

			if($this->input->post('id_pegawai_bendahara') == '')
			{
				$data['inputerror'][] = 'id_pegawai_bendahara';
				$data['error_string'][] = 'Nama Pegawai Bendahara harus diisi';
				$data['status'] = FALSE;
			}
			
			if($this->input->post('id_pegawai_pengguna_anggaran') == '')
			{
				$data['inputerror'][] = 'id_pegawai_pengguna_anggaran';
				$data['error_string'][] = 'Nama Pegawai Pengguna Anggaran harus diisi';
				$data['status'] = FALSE;
			}
			
			if($this->input->post('no_bas_penerimaan') == '')
			{
				$data['inputerror'][] = 'no_bas_penerimaan';
				$data['error_string'][] = 'Nomor Berita Acara Penerimaan harus diisi';
				$data['status'] = FALSE;
			}
			
			if($this->input->post('tanggal_bas') == '')
			{
				$data['inputerror'][] = 'tanggal_bas';
				$data['error_string'][] = 'tanggal Berita Acara Penerimaan harus diisi';
				$data['status'] = FALSE;
			}
			
			if($this->input->post('no_bast') == '')
			{
				$data['inputerror'][] = 'no_bast';
				$data['error_string'][] = 'Nomor Berita Acara Serah Terima Pekerjaan (BAST) harus diisi';
				$data['status'] = FALSE;
			}
			
			if($this->input->post('tgl_bast') == '')
			{
				$data['inputerror'][] = 'tgl_bast';
				$data['error_string'][] = 'Tanggal Berita Acara Serah Terima Pekerjaan (BAST) harus diisi';
				$data['status'] = FALSE;
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
		
		$hasil = $this->pencairan->update(array('id_penyedia' => $id), $data);
		if($hasil > 0){
			$content['status'] = true;
			$content['notif'] = '<div class="alert alert-success" role="alert"><i class="fa fa-thumbs-o-up fa-fw"></i> Data berhasil di hapus</div>';			

		}else{
		  $content['status'] = false;
		  $content['notif'] = '<div class="alert-modal alert-danger" role="alert"><i class="fa fa-thumbs-o-down fa-fw"></i> Data gagal dihapus !</div>';
		}
		
		
		echo json_encode($content);
	}
	
	public function download_pdf(){
		
		$post = $this->input->post();
		
		$id_hasil_pekerjaan = (!empty($post['id_hasil_pekerjaan'])) ? $post['id_hasil_pekerjaan'] : $post['id'];
		
		// $id_hasil_pekerjaan = 1;
		if(!empty($id_hasil_pekerjaan)){	
			
			$data = $this->hasil_pekerjaan->get_by_id_detail_hasil_pekerjaan($id_hasil_pekerjaan, '
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
						a.no_bast,
						a.tgl_bast,
						a.status,
						a.id_pembayaran,
						a.draft,
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
						c.id_spk,
						c.no_spk,
						c.nama_pekerjaan,
						c.id_paket,
						c.id_prokeg_aktif,
						c.nama_kegiatan,
						c.tgl_pekerjaan,
						c.kode_unor,
						c.dpa_skpd,
						c.kode_rek,
						c.pagu,
						c.type_kontrak,
						d.kategori,
						d.nama_perusahaan,
						d.jabatan,
						a.cdd
						');
			$data_pejabat_pphp = $this->paraf_pphp->get_where_criteria(array('id_hasil_pekerjaan'=> $id_hasil_pekerjaan, 'status'=> 1), 'id_pegawai_pphp, nip_pegawai_pphp, nama_pegawai_pphp, nomenklatur_jabatan_pphp', true);
			$data->data_uraian_pekerjaan = $this->uraian->get_where(array('id_hasil_pekerjaan'=> $id_hasil_pekerjaan, 'status'=> 1), 'uraian, volume, satuan, keterangan');
			
			$content['data'] = $data;
			$content['data_pejabat_pphp'] = $data_pejabat_pphp;
			$content['pdf'] = true;
			$content['data_berkas'] = $this->berkas->get_where_criteria(array('active'=> 1), 'nama_berkas, id_berkas', true);
			$html1 = $this->load->view('hasil_pekerjaan/template_surat_penyerahan_hasil_pekerjaan', @$content, true);
			//disini kasih kondisi apabila cdd bulan juli atau lebih maka pakai format baru lembar suratnya
			// $html2 = $this->load->view('hasil_pekerjaan/template_bas', @$content, true);
			
			if(date('Y', strtotime($data->tgl_pekerjaan)) >= '2021'){
				
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
			
			require_once("application/libraries/dompdf083/dompdf/autoload.inc.php");
			$dompdf = new Dompdf\Dompdf();
			
			// require_once("application/libraries/dompdf/dompdf_config.inc.php");			
			// $dompdf = new DOMPDF();	
			
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
			
			$filename1 = 'surat_penyerahan_hasil_pekerjaan_'.$id_hasil_pekerjaan .'.pdf';
			$file_to_save = 'assets/file/surat/'.$filename1;
			file_put_contents($file_to_save, $output);	
			unset($dompdf);
			
			
			$dompdf = new Dompdf\Dompdf();
			// $dompdf = new DOMPDF();		
			$dompdf->load_html($html3);		 
			
			if($post['paper'] == 'f4'){
				$paper_size = array(0,0,612.00,936.00); //opick
				$dompdf->set_paper($paper_size);
			}else{
				$dompdf->set_paper($post['paper'], 'portrait');
			}
			$dompdf->render();
			$output = $dompdf->output();				
			
			$filename2 = 'surat_berita_acara_penerimaan_hasil_pekerjaan_'.$id_hasil_pekerjaan .'.pdf';
			$file_to_save = 'assets/file/surat/'.$filename2;
			file_put_contents($file_to_save, $output);			
			unset($dompdf);
			
			if(!empty($data_pejabat_pphp)){
				$dompdf = new Dompdf\Dompdf();
				// $dompdf = new DOMPDF();		
				$dompdf->load_html($html2);		 
				
				if($post['paper'] == 'f4'){
					$paper_size = array(0,0,612.00,936.00); //opick
					$dompdf->set_paper($paper_size);
				}else{
					$dompdf->set_paper($post['paper'], 'portrait');
				}
				$dompdf->render();
				$output = $dompdf->output();				
				
				$filename3 = 'surat_berita_acara_serah_terima_pekerjaan_'.$id_hasil_pekerjaan.'.pdf';
				$file_to_save = 'assets/file/surat/'.$filename3;
				file_put_contents($file_to_save, $output);		
				$kumpulanfile = array($filename3, $filename2, $filename1);
			}else{				
				$kumpulanfile = array($filename2, $filename1);
			}	
			
			$filename_marge = 'surat_hasil_pekerjaan_'.$id_hasil_pekerjaan.'.pdf';
			
			//marge pdf
			$margeFile = margeFilePDF($kumpulanfile, $filename_marge);
			//marge pdf
			echo json_encode(array("status"=> true, "url_file" => 'assets/file/surat/'.$filename_marge));
			//create pdf
		}
	}
	
	private function _do_upload($nameFile='')
	{
		$config['upload_path']          = 'assets/file/dokumen_pekerjaan';
        $config['allowed_types']        = 'pdf';
        $config['max_size']             = 200000; //set max size allowed in Kilobyte
        $config['file_name']            = round(microtime(true) * 1000); //just milisecond timestamp fot unique name

        $this->load->library('upload', $config);

        if(!$this->upload->do_upload($nameFile)) //upload and validate
        {
            $data['inputerror'][] = $nameFile;
			$data['error_string'][] = 'Upload error: '.$this->upload->display_errors('',''); //show ajax error
			$data['status'] = FALSE;
			echo json_encode($data);
			exit();
		}
		return $this->upload->data('file_name');
	}
	
	
}
