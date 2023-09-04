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
		$this->load->model('uraian_model', 'uraian');		
		$this->load->model('paraf_pphp_model', 'paraf_pphp');		
		$this->load->model('user_model', 'user');			
		$this->load->model('adendum_model', 'adendum');		
		$this->load->model('berkas_model', 'berkas');		
		$this->load->model('pembayaran_rincian_model', 'pembayaran_rincian');		
		$this->load->model('rincian_model', 'rincian');		 
		$this->load->model('san/tim_pokja_model', 'person');		 
		$this->db_ba = $this->load->database('db_pembayaran_ls', TRUE);
		$this->db_eoffice = $this->load->database('db_eoffice', TRUE);
		// var_dump($this->session->userdata());exit;
		if($this->session->userdata('login')==false){
			redirect(base_url('site/login'));
		}
		
	}
	
	public function index()
	{
		
		getHakAkses();				
		$content['class_name'] = get_class($this);	
		$post = $this->input->post();

		$content['id_inbox'] = @$post['id_inbox'];
		$content['id_read'] = @$post['id_read'];
		$content['keterangan'] = @$post['keterangan'];
		$content['kk'] = @$post['kk'];
		
		$content['content'] = $this->load->view('inbox/index',@$content,true);
		$this->load->view('ba/index', @$content,false,false);
	}
	
	public function ajax_list()
	{
		$this->load->helper('url');
		$list = $this->inbox->get_datatables();
		// var_dump($this->db_ba->last_query());exit;
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $person) {
			$no++;
			$row = array();			
			
			
			if($person->type == '1')
			{				
				if($person->id_hasil_pekerjaan)
				{
					if(@$person->paraf == '1'){
						if($person->paraf == '1'){
							if(!empty($person->viewed)){
								$bold = 'style="cursor:pointer;"';
								$text_status = '<div><i class="glyphicon glyphicon-eye-open"></i> <span class="label label-default">Diacc</span></div>';
							}else{
								$bold = 'style="cursor:pointer; font-weight: bold;"';
								$text_status = '<div><i class="glyphicon glyphicon-info-sign"></i> <span class="label label-danger">Terbaru</span></div>';
							}					
						}else{
							if(!empty($person->viewed)){
								$bold = 'style="cursor:pointer;"';
								$text_status = '<div><i class="glyphicon glyphicon-eye-open"></i> <span class="label label-default">Ditolak</span></div>';
							}else{
								$bold = 'style="cursor:pointer; font-weight: bold;"';
								$text_status = '<div><i class="glyphicon glyphicon-info-sign"></i> <span class="label label-danger">Ditolak</span></div>';
							}	
						}

					}else if(!empty($person->paraf) && $person->paraf == '0'){
						if(!empty($person->viewed)){
							$bold = 'style="cursor:pointer;"';
							$text_status = '<div><i class="glyphicon glyphicon-eye-open"></i> <span class="label label-default">Ditolak</span></div>';
						}else{
							$bold = 'style="cursor:pointer; font-weight: bold;"';
							$text_status = '<div><i class="glyphicon glyphicon-info-sign"></i> <span class="label label-danger">Ditolak</span></div>';
						}	
					}else{
						if(!empty($person->viewed)){
							if($person->paraf_next == '0'){						
								$bold = 'style="cursor:pointer;"';
								$text_status = '<div><i class="glyphicon glyphicon-eye-open"></i> <span class="label label-default">Ditolak</span></div>';
							}
							
							if($person->paraf_next == '1'){		
									
									$cek_ppk = $this->hasil_pekerjaan->get_where_criteria(array('id_hasil_pekerjaan'=> $person->id_hasil_pekerjaan, 'id_pegawai_ppk'=> $this->session->userdata('id_pegawai')), 'id_pegawai_ppk');
									if($cek_ppk){									
										$cekGanda = $this->inbox->get_by_id_count_all(array('id_hasil_pekerjaan'=> $person->id_hasil_pekerjaan,'mailfrom'=> $this->session->userdata('id_pegawai'), 'active' => 1), 'id_pegawai');	
										
										$cekGanda2 = $this->inbox->get_by_id_count_all(array('id_hasil_pekerjaan'=> $person->id_hasil_pekerjaan,'mailto'=> $this->session->userdata('id_pegawai'), 'active' => 1), 'id_pegawai');
										
										if($cekGanda != $cekGanda2 ){		
											$bold = 'style="cursor:pointer;"';
											$text_status = '<div><i class="glyphicon glyphicon-eye-open"></i> <span class="label label-default">Belum Diacc</span></div>';
										}else{
											$bold = 'style="cursor:pointer;"';
											$text_status = '<div><i class="glyphicon glyphicon-eye-open"></i> <span class="label label-default">Diacc</span></div>';
										}
									}else{
										$bold = 'style="cursor:pointer;"';
										$text_status = '<div><i class="glyphicon glyphicon-eye-open"></i> <span class="label label-default">Diacc</span></div>';
									}								
							}
							
							if($person->paraf == null && $person->paraf_next == null){
								$bold = 'style="cursor:pointer;"';
								$text_status = '<div><i class="glyphicon glyphicon-eye-open"></i> <span class="label label-default">Belum diacc</span></div>';
							}
							
							if($person->paraf == '0' && $person->paraf_next == null){
								$bold = 'style="cursor:pointer;"';
								$text_status = '<div><i class="glyphicon glyphicon-eye-open"></i> <span class="label label-default">Ditolak</span></div>';
							}
							
						}else{
							$bold = 'style="cursor:pointer; font-weight: bold;"';
							$text_status = '<div><i class="glyphicon glyphicon-info-sign"></i> <span class="label label-danger">Terbaru</span></div>';
							
							if($person->paraf_next == '0'){						
								$bold = 'style="cursor:pointer; font-weight: bold;"';
								$text_status = '<div><i class="glyphicon glyphicon-info-sign"></i> <span class="label label-danger">Ditolak</span></div>';
							}
							
							if($person->paraf_next == '1'){			
									$cekGanda = $this->inbox->get_by_id_count_all(array('id_hasil_pekerjaan'=> $person->id_hasil_pekerjaan,'mailto'=> $this->session->userdata('id_pegawai'), 'active' => 1), 'id_pegawai');
									
									if($cekGanda == 2){									
										$bold = 'style="cursor:pointer; font-weight: bold;"';
										$text_status = '<div><i class="glyphicon glyphicon-info-sign"></i> <span class="label label-danger">Terbaru</span></div>';
									}else{
										$bold = 'style="cursor:pointer; font-weight: bold;"';
										$text_status = '<div><i class="glyphicon glyphicon-info-sign"></i> <span class="label label-danger">Diacc</span></div>';
									}
							}
							
							if($person->paraf == null && $person->paraf_next == null){
								$bold = 'style="cursor:pointer; font-weight: bold;"';
								$text_status = '<div><i class="glyphicon glyphicon-info-sign"></i> <span class="label label-danger">Terbaru</span></div>';
							}
							
							if($person->paraf == '0' && $person->paraf_next == null){
								$bold = 'style="cursor:pointer; font-weight: bold;"';
								$text_status = '<div><i class="glyphicon glyphicon-info-sign"></i> <span class="label label-danger">Ditolak</span></div>';
							}
						}
					}
				}else{
					if(@$person->paraf == '1'){
						if($person->paraf == '1'){
							if($person->status != '2'){
								if(!empty($person->viewed)){
									$bold = 'style="cursor:pointer;"';
									$text_status = '<div><i class="glyphicon glyphicon-eye-open"></i> <span class="label label-default">Diacc</span></div>';
								}else{
									$bold = 'style="cursor:pointer; font-weight: bold;"';
									$text_status = '<div><i class="glyphicon glyphicon-info-sign"></i> <span class="label label-danger">Terbaru</span></div>';
								}	
							}else{
								if(!empty($person->viewed)){
									if($person->paraf_next == '0'){						
										$bold = 'style="cursor:pointer;"';
										$text_status = '<div><i class="glyphicon glyphicon-eye-open"></i> <span class="label label-default">Ditolak</span></div>';
									}
									
									if($person->paraf_next == '1'){
										$bold = 'style="cursor:pointer;"';
										$text_status = '<div><i class="glyphicon glyphicon-eye-open"></i> <span class="label label-default">Diacc</span></div>';
									}
									
									if($person->paraf == null && $person->paraf_next == null){
										$bold = 'style="cursor:pointer;"';
										$text_status = '<div><i class="glyphicon glyphicon-eye-open"></i> <span class="label label-default">Belum diacc</span></div>';
									}								
									
									if($person->paraf == '1' && $person->paraf_next == null){
										$bold = 'style="cursor:pointer;"';
										$text_status = '<div><i class="glyphicon glyphicon-eye-open"></i> <span class="label label-default">Belum diacc</span></div>';
									}
								}else{	
									
									/* if($person->paraf == '1'){									
										
										//cek pptk atau bukan
										if(){ //jika akses == 3
											$bold = 'style="cursor:pointer; font-weight: bold;"';
											$text_status = '<div><i class="glyphicon glyphicon-info-sign"></i> <span class="label label-danger">Terbaru</span></div>';
										}else{
											$bold = 'style="cursor:pointer; font-weight: bold;"';
											$text_status = '<div><i class="glyphicon glyphicon-info-sign"></i> <span class="label label-danger">Diacc</span></div>';
										}
										var_dump();
										//cek pptk atau bukan
									} */
									
									if($person->paraf_next == '0'){						
										$bold = 'style="cursor:pointer; font-weight: bold;"';
										$text_status = '<div><i class="glyphicon glyphicon-info-sign"></i> <span class="label label-danger">Ditolak</span></div>';
									}
									
									if($person->paraf_next == '1'){
										$bold = 'style="cursor:pointer; font-weight: bold;"';
										$text_status = '<div><i class="glyphicon glyphicon-info-sign"></i> <span class="label label-danger">Terbaru</span></div>';
									}
									
									if($person->paraf == null && $person->paraf_next == null){
										$bold = 'style="cursor:pointer; font-weight: bold;"';
										$text_status = '<div><i class="glyphicon glyphicon-info-sign"></i> <span class="label label-danger">Belum diacc</span></div>';
									}								
									
									if($person->paraf == '1' && $person->paraf_next == null){
										$bold = 'style="cursor:pointer; font-weight: bold;"';
										$text_status = '<div><i class="glyphicon glyphicon-info-sign"></i> <span class="label label-danger">Terbaru</span></div>';
									}
									
									if($person->paraf == '0'){
										$bold = 'style="cursor:pointer; font-weight: bold;"';
										$text_status = '<div><i class="glyphicon glyphicon-info-sign"></i> <span class="label label-danger">Ditolak</span></div>';
									}
								}	
							}				
						}else{
							if(!empty($person->viewed)){
								$bold = 'style="cursor:pointer;"';
								$text_status = '<div><i class="glyphicon glyphicon-eye-open"></i> <span class="label label-default">Ditolak</span></div>';
							}else{
								$bold = 'style="cursor:pointer; font-weight: bold;"';
								$text_status = '<div><i class="glyphicon glyphicon-info-sign"></i> <span class="label label-danger">Ditolak</span></div>';
							}	
						}

					}else if(!empty($person->paraf) && $person->paraf == '0'){
						if(!empty($person->viewed)){
							$bold = 'style="cursor:pointer;"';
							$text_status = '<div><i class="glyphicon glyphicon-eye-open"></i> <span class="label label-default">Ditolak</span></div>';
						}else{
							$bold = 'style="cursor:pointer; font-weight: bold;"';
							$text_status = '<div><i class="glyphicon glyphicon-info-sign"></i> <span class="label label-danger">Ditolak</span></div>';
						}	
					}else{
						if(!empty($person->viewed)){
							if($person->paraf_next == '0'){						
								$bold = 'style="cursor:pointer;"';
								$text_status = '<div><i class="glyphicon glyphicon-eye-open"></i> <span class="label label-default">Ditolak</span></div>';
							}
							
							if($person->paraf_next == '1'){
								$bold = 'style="cursor:pointer;"';
								$text_status = '<div><i class="glyphicon glyphicon-eye-open"></i> <span class="label label-default">Diacc</span></div>';
							}
							
							if($person->paraf == null && $person->paraf_next == null){
								$bold = 'style="cursor:pointer;"';
								$text_status = '<div><i class="glyphicon glyphicon-eye-open"></i> <span class="label label-default">Belum diacc</span></div>';
							}
							
							if($person->paraf == '0' && $person->paraf_next == null){
								$bold = 'style="cursor:pointer;"';
								$text_status = '<div><i class="glyphicon glyphicon-eye-open"></i> <span class="label label-default">Ditolak</span></div>';
							}
							
						}else{
							$bold = 'style="cursor:pointer; font-weight: bold;"';
							$text_status = '<div><i class="glyphicon glyphicon-info-sign"></i> <span class="label label-danger">Terbaru</span></div>';
						}
					}
				}

				if($person->pekerjaan_termin == 1){
					$termins = '';
				}else{
					if(isset($person->id_hasil_pekerjaan)){
						$termins = $person->termin;
					}else{
						$termins = $person->termin1;
					}
				}
				
				if($person->kategori == 1){
					$nama_penyedia = $person->nama_perusahaan;
				}else{
					$nama_penyedia = $person->nama_penyedia;
				}
				
				$file = (!empty($person->file_pekerjaan)) ?  '<a href="'.base_url().'assets/file/dokumen_pekerjaan/'.$person->file_pekerjaan.'" target="_blank"><img class="imageThumb" src="'.base_url().'assets/file/kontrak/icon_pdf.png" title="Lampiran Hasil Pekerjaan"/></a>' : '';
				if(isset($person->id_hasil_pekerjaan)){
					$data_perihal = '<font style="font-size:8pt;">Surat Penyerahan Hasil Pekerjaan '.$termins.'<br>'.$nama_penyedia.'</font><br>';
				}else{
					$data_perihal = '<font style="font-size:8pt;">Permohonan Pembayaran '.$termins.'<br>'.$nama_penyedia.'</font><br>';
				}
				
				$keterangan = (isset($person->id_hasil_pekerjaan)) ? 0 : 1; //jika pembayaran maka 1, jika hasil pekerjaan maka 0				
				$data_dari = '<font>' . $person->nama_mailfrom . '</font><br>'.
							'<font style="font-size:8pt;">'.$person->nomenklatur_jabatan_mailfrom.'</font>'
				;
			}else
			{	
				
				// $getverif = $this->inbox->get($start = null, $length = null, $sort = null, $order = null, $where = array(''), $like = null, $table='t_inbox', $field='*', $row_array=false, $join=false);

				
				$bold = 'style="cursor:pointer; font-weight: bold;"';

				if(empty($person->viewed)){
					$text_status = '<div><i class="glyphicon glyphicon-info-sign"></i> <span class="label label-danger">Terbaru</span></div>';
				}else{
					$text_status = '<div><i class="glyphicon glyphicon-eye-open"></i> <span class="label label-default">Terbaca</span></div>';
				}

				$tombolcreate = '';

				if($person->nama_kegiatan == 'Surat Pengantar'){


					$get_parent = $this->inbox->get($start = null, $length = null, $sort = null, $order = null, $wherex = array('a.id_surat'=> $person->id_hasil_pekerjaan, 'a.active'=> 1), $like = null, $table='t_surat a', $field='a.kepada', $row_array=true, $join=false);

					$tujuanjson_decode = json_decode($get_parent['kepada'],true);
					if($tujuanjson_decode[0]['id_pegawai'] == $this->session->userdata('id_pegawai')){
						$tombolcreate = '<a href="javascript:void(0)" onclick="createspk_prakontrak('.$person->id_hasil_pekerjaan.')"><span class="label label-default btn btn-warning">Buat SPK</span></a>';
					}
					
				}

				$data_perihal = '<small>'.$person->nama_kegiatan.'</small><br>'.$person->nama_pekerjaan . ' '. $tombolcreate;
				if($person->id_bentuk == '4'){	
					
					$json_rup_penyedia = json_decode($person->json_rup_penyedia,true);				
					$data_perihal = '<small>'.$person->nama_kegiatan.'</small><br>'.$json_rup_penyedia['PAKET'] . ' '. $tombolcreate;
				}
				$keterangan = $person->type; //jika pembayaran maka 1, jika hasil pekerjaan maka 0
				$file = '';

				$data_perihal .= '<br><small stye="font-size: 55%!important;">'.$person->nama_perusahaan.'</small>';

				
				// a.penandatangan as nama_pegawai_pphp, 
				// a.paraf as nomenklatur_jabatan_pphp, 
				// a.tembusan as nama_pegawai_ppk, 
				$jabatan = $person->nomenklatur_jabatan_mailfrom;

				// $penandatangan = (!empty($person->nama_pegawai_pphp)) ? json_decode($person->nama_pegawai_pphp, true) : array();
				// $paraf = (!empty($person->nama_pegawai_ppnomenklatur_jabatan_pphpk)) ? json_decode($person->nomenklatur_jabatan_pphp, true) : array();
				// $tembusan = (!empty($person->nama_pegawai_ppk)) ? json_decode($person->nama_pegawai_ppk, true) : array();

				// $data_people = array_merge($penandatangan, $paraf, $tembusan);
				
				// if($data_people){
				// 	$cekdata = array_search($person->mailfrom, array_column($data_people, 'id_pegawai'));

				// 	if($cekdata !== FALSE)
				// 	{
				// 		$jabatan = $data_people[$cekdata]['data_jabatan'];
				// 	}
				// }	

				$data_dari = '<font>' . $person->nama_mailfrom . '</font><br>'.
							'<font style="font-size:8pt;">'.$jabatan.'</font>'
				;

			}
			
			
			$data_tanggal = (!empty(@$person->tgl_surat)) ? GetOnlyDate(@$person->tgl_surat) : '';		
			
			$data_status = $text_status;
			
			$id_read = (isset($person->id_hasil_pekerjaan)) ? $person->id_hasil_pekerjaan : $person->id_pembayaran;
			
			$kk = 0;
			$row[] = '<div '.$bold.' href="javascript:void(0)" onclick="read_view_all('.$person->id_inbox.', '.$id_read.', '.$keterangan.', '.$kk.')">'.$data_dari.'</div>';
			$row[] = '<div '.$bold.' href="javascript:void(0)" onclick="read_view_all('.$person->id_inbox.', '.$id_read.', '.$keterangan.', '.$kk.')">'.$data_perihal.'</div>'. $file;
			$row[] = '<div '.$bold.' href="javascript:void(0)" onclick="read_view_all('.$person->id_inbox.', '.$id_read.', '.$keterangan.', '.$kk.')">'.$data_tanggal.'</div>';
			$row[] = '<div '.$bold.' href="javascript:void(0)" onclick="read_view_all('.$person->id_inbox.', '.$id_read.', '.$keterangan.', '.$kk.')">'.$data_status.'</div>';
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
			$data['error_string'][] = 'Nama Penyedia harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('id_spk') == '')
		{
			$data['inputerror'][] = 'id_spk';
			$data['error_string'][] = 'Nama Pekerjaan harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('pekerjaan_termin') == '')
		{
			$data['inputerror'][] = 'pekerjaan_termin';
			$data['error_string'][] = 'Pekerjaan Termin harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('nominal_bayar') == '')
		{
			$data['inputerror'][] = 'nominal_bayar';
			$data['error_string'][] = 'Nominal Biaya harus diisi';
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
			
			if($keterangan == '0'){
				$where = array('id_hasil_pekerjaan' =>  $id_read, 'mailto' => $this->session->userdata('id_pegawai'), 'viewed' =>  null, 'type'=> '1');
			}
			
			if($keterangan == '1'){
				$where = array('id_pembayaran' =>  $id_read, 'mailto' => $this->session->userdata('id_pegawai'), 'viewed' =>  null, 'type'=> '1');
			}
			
			if($keterangan == '2'){
				$where = array('id_hasil_pekerjaan' =>  $id_read, 'mailto' => $this->session->userdata('id_pegawai'), 'viewed' =>  null, 'type'=> '2');
			}
			$data_update = array('viewed' =>  date('Y-m-d H:i:s'));
			$this->inbox->update($where, $data_update);
		}

		
		// jika ada id inbox maka viewed inbox di update
		if($keterangan == '0'){ // jika hasil pekerjaan maka 0
			if(!empty($id_read)){	
				$data = $this->hasil_pekerjaan->get_by_id_detail_hasil_pekerjaan($id_read, '
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
				// echo '<pre>';
				// var_dump($data, $this->db_ba->last_query());
				// exit;
				$data_uraian_pekerjaan = $this->uraian->get_where(array('id_hasil_pekerjaan'=> $id_read, 'status'=> 1), 'uraian, volume, satuan, keterangan, harga_satuan, jumlah');
				$data_pejabat_pphp = $this->paraf_pphp->get_where_criteria(array('id_hasil_pekerjaan'=> $id_read, 'status'=> 1), 'id_pegawai_pphp, nip_pegawai_pphp, nama_pegawai_pphp, nomenklatur_jabatan_pphp', true);
				$data->data_uraian_pekerjaan = $data_uraian_pekerjaan;
				
				$ttd_pphp = '';
				$ttd_pptk = '';
				$ttd_ppk = '';
				$ttd_bendahara = '';
				$ttd_pengguna_anggaran = '';
				
				//cek apakah pphp
				$nomor = 1;
				$pejabat_pphp = array();
				$unikCode = '';
				if($data_pejabat_pphp){
					foreach($data_pejabat_pphp as $rss){		
						if($rss['id_pegawai_pphp'] == $this->session->userdata('id_pegawai'))
						{
							$unikCode = 'ttd_pphp'.$nomor++;
							$content['name_id'] = $unikCode;
							$rs['name_id'] = $unikCode;
							$rss['buttonTtd'] = $this->cek_ttd($id_inbox, $id_read, $this->session->userdata('id_pegawai'), $unikCode);
						}else{
							$rss['buttonTtd'] = $this->cek_ttd($id_inbox, $id_read, $rss['id_pegawai_pphp'], 'ttd_pphp'.$nomor++, true);
						}	
						$pejabat_pphp[] = $rss;		
					}
				}
				//cek apakah pphp
				
				
				//cek apakah pptk
				if($data->id_pegawai_pptk == $this->session->userdata('id_pegawai'))
				{
					$ttd_pptk = $this->cek_ttd($id_inbox, $id_read, $this->session->userdata('id_pegawai'), 'ttd_pptk');
				}else{				
					$ttd_pptk = $this->cek_ttd($id_inbox, $id_read, $data->id_pegawai_ppk, 'ttd_pptk', true);
				}
				//cek apakah pptk
				
				//cek apakah ppk_bapenerimaan					
				if($data->id_pegawai_ppk == $this->session->userdata('id_pegawai'))
				{
					$ttd_ppk_bapenerimaan = $this->cek_ttd($id_inbox, $id_read, $this->session->userdata('id_pegawai'), 'ttd_ppk_bapenerimaan'); //1 itu true, disini menandakan kalao ppk di hasil pekerjaan bukan di kwitansi
					$totalppk = $this->inbox->get_by_id_count_all(array('mailto'=> $this->session->userdata('id_pegawai'), 'active'=> 1, 'id_hasil_pekerjaan'=> $id_read, 'type'=> '1'), 'id_pegawai');
					
					if($totalppk < 2){
						$ttd_ppk_bapenerimaan = '';
					}
				}else{
					$ttd_ppk_bapenerimaan = $this->cek_ttd($id_inbox, $id_read, $data->id_pegawai_ppk, 'ttd_ppk_bapenerimaan', true);
				}
				//cek apakah ppk
				
				//cek apakah ppk					
				if($data->id_pegawai_ppk == $this->session->userdata('id_pegawai'))
				{
					$ttd_ppk = $this->cek_ttd($id_inbox, $id_read, $this->session->userdata('id_pegawai'), 'ttd_ppk'); //1 itu true, disini menandakan kalao ppk di hasil pekerjaan bukan di kwitansi
				}else{
					$ttd_ppk = $this->cek_ttd($id_inbox, $id_read, $data->id_pegawai_ppk, 'ttd_ppk', true);
				}
				//cek apakah ppk
				
				//cek apakah ppk bast
				if($data->id_pegawai_ppk == $this->session->userdata('id_pegawai'))
				{
					$ttd_ppk_bast = $this->cek_ttd($id_inbox, $id_read, $this->session->userdata('id_pegawai'), 'ttd_ppk_bast', false, 1); //1 itu true, disini menandakan kalao ppk di hasil pekerjaan bukan di kwitansi
				}else{
					$ttd_ppk_bast = $this->cek_ttd($id_inbox, $id_read, $data->id_pegawai_ppk, 'ttd_ppk_bast', true);
				}
				//cek apakah ppk
				
				//data adendum
				$get_id_pembayaran = $this->hasil_pekerjaan->get_by_id($id_read, 'id_pembayaran');
				if($get_id_pembayaran){
					if(@$get_id_pembayaran->id_pembayaran){
						$data_adendum = $this->adendum->get_where(array('id_pembayaran'=> $get_id_pembayaran->id_pembayaran, 'status'=>1), 'no_adendum, tgl_adendum, biaya_adendum, waktu_pelaksanaan_adendum', true);
						$rs['data_adendum'] = $data_adendum;
					}
				}
				
				
				$rs['data_berkas'] = $this->berkas->get_where_criteria(array('active'=> 1), 'nama_berkas, id_berkas', true);
				$rs['data'] = $data;
				$rs['data_pejabat_pphp'] = $pejabat_pphp;
				// $rs['ttd_pphp'] = $ttd_pphp;
				$rs['ttd_pptk'] = $ttd_pptk;
				$rs['ttd_ppk_bapenerimaan'] = $ttd_ppk_bapenerimaan;
				$rs['ttd_ppk'] = $ttd_ppk;
				$rs['ttd_ppk_bast'] = $ttd_ppk_bast;
				
				$isi_template_surat_permohonan = $this->load->view('hasil_pekerjaan/template_surat_penyerahan_hasil_pekerjaan', @$rs, true);
				
				if(date('Y', strtotime($data->tgl_pekerjaan)) >= '2021')
				{
					$get_prokeg = $this->inbox->get($start = null, $length = null, $sort = null, $order = null, $wherex = array('id_spk'=> $data->id_spk), $like = null, $table='rincian_detail_spk', $field='*', $row_array=true, $join=false);
					
					$data_prokeg = json_decode($get_prokeg['data_prokeg_json'], true);
					
					$rs['data_prokeg'] = $data_prokeg;
					
					$isi_template_bast = $this->load->view('hasil_pekerjaan/template_bast_2021', @$rs, true);
					
					$isi_template_bas = $this->load->view('hasil_pekerjaan/template_basv2_2021', @$rs, true);

				}else{
					$isi_template_bast = $this->load->view('hasil_pekerjaan/template_bast', @$rs, true);
					
					$isi_template_bas = $this->load->view('hasil_pekerjaan/template_basv2', @$rs, true);
				}
				
				if(!empty($pejabat_pphp)){
					$hasil .= '<page size="A4">'.$isi_template_bas.'</page>';
				}
				
				$hasil .= '
				<page size="F4">'.$isi_template_bast.'</page>
				<page size="A4">'.$isi_template_surat_permohonan.'</page>
				<page size="batas" layout="portrait"></page>';
				
			}
		}
		
		if($keterangan == '1'){ //jika id pembayaran maka 1
			if(!empty($id_read)){	
				$data_all = $this->hasil_pekerjaan->get_by_id_detail($id_read, 'a.*, 
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
				');
				
				
				$data_pejabat_pphp = $this->paraf_pphp->get_where_criteria(array('id_hasil_pekerjaan'=> $data_all[0]->id_hasil_pekerjaan, 'status'=> 1), 'id_pegawai_pphp, nip_pegawai_pphp, nama_pegawai_pphp, nomenklatur_jabatan_pphp', true);
				
				// if($id_read == '1169')
				// {
					// echo '<pre>';
					// var_dump($data_pejabat_pphp);
					// exit;
				// }
				
				
				$reversed = array_reverse($data_all);
				$data_prokeg = '';
				foreach($reversed as $data)
				{
					$data_uraian_pekerjaan = $this->uraian->get_where(array('id_hasil_pekerjaan'=> $data->id_hasil_pekerjaan, 'status'=> 1), 'uraian, volume, satuan, keterangan, harga_satuan, jumlah');
					$data->data_uraian_pekerjaan = $data_uraian_pekerjaan;
					
					$ttd_pphp = '';
					$ttd_pptk = '';
					$ttd_ppk = '';
					$ttd_bendahara = '';
					$ttd_pengguna_anggaran = '';
					
					
					//cek apakah pphp
					$nomor = 1;
					$pejabat_pphp = array();
					if(!empty($data_pejabat_pphp)){
						foreach($data_pejabat_pphp as $rss){		
							if($rss['id_pegawai_pphp'] == $this->session->userdata('id_pegawai'))
							{
								$rss['buttonTtd'] = $this->cek_ttd($id_inbox, $data->id_hasil_pekerjaan, $this->session->userdata('id_pegawai'), 'ttd_pphp'.$nomor++);
							}else{
								$rss['buttonTtd'] = $this->cek_ttd($id_inbox, $data->id_hasil_pekerjaan, $rss['id_pegawai_pphp'], 'ttd_pphp'.$nomor++, true);
							}	
							$pejabat_pphp[] = $rss;		
						}
					}
					//cek apakah pphp
					
					//cek apakah pptk
					if($data->id_pegawai_pptk == $this->session->userdata('id_pegawai'))
					{
						$ttd_pptk = $this->cek_ttd($id_inbox, $data->id_hasil_pekerjaan, $this->session->userdata('id_pegawai'), 'ttd_pptk');
					}else{				
						$ttd_pptk = $this->cek_ttd($id_inbox, $data->id_hasil_pekerjaan, $data->id_pegawai_ppk, 'ttd_pptk', true);
					}
					//cek apakah pptk
					
					//cek apakah ppk
					if($data->id_pegawai_ppk == $this->session->userdata('id_pegawai'))
					{
						$ttd_ppk = $this->cek_ttd($id_inbox, $data->id_hasil_pekerjaan, $this->session->userdata('id_pegawai'), 'ttd_ppk');						
					}else{
						$ttd_ppk = $this->cek_ttd($id_inbox, $data->id_hasil_pekerjaan, $data->id_pegawai_ppk, 'ttd_ppk', true);
					}
					//cek apakah ppk
					
					//cek apakah ppk 
					if($data->id_pegawai_ppk == $this->session->userdata('id_pegawai'))
					{
						$ttd_ppk_bast = $this->cek_ttd($id_inbox, $data->id_hasil_pekerjaan, $this->session->userdata('id_pegawai'), 'ttd_ppk_bast', false, 1);						
					}else{
						$ttd_ppk_bast = $this->cek_ttd($id_inbox, $data->id_hasil_pekerjaan, $data->id_pegawai_ppk, 'ttd_ppk_bast', true);
					}
					//cek apakah ppk
					
					
					//cek apakah ppk_bapenerimaan					
					if($data->id_pegawai_ppk == $this->session->userdata('id_pegawai'))
					{
						$ttd_ppk_bapenerimaan = $this->cek_ttd($id_inbox, $data->id_hasil_pekerjaan, $this->session->userdata('id_pegawai'), 'ttd_ppk_bapenerimaan'); //1 itu true, disini menandakan kalao ppk di hasil pekerjaan bukan di kwitansi
						$totalppk = $this->inbox->get_by_id_count_all(array('mailto'=> $this->session->userdata('id_pegawai'), 'active'=> 1, 'id_hasil_pekerjaan'=> $id_read, 'type'=> '1'), 'id_pegawai');
												
					}else{						
						$ttd_ppk_bapenerimaan = $this->cek_ttd($id_inbox, $data->id_hasil_pekerjaan, $data->id_pegawai_ppk, 'ttd_ppk_bapenerimaan', true);
					}
					//cek apakah ppk					
						
					//cek apakah bendahara
					if($data->id_pegawai_bendahara == $this->session->userdata('id_pegawai'))
					{
						$ttd_bendahara = $this->cek_ttd($id_inbox, $data->id_pembayaran, $this->session->userdata('id_pegawai'), 'ttd_bendahara');						
					}else{
						$ttd_bendahara = $this->cek_ttd($id_inbox, $data->id_pembayaran, $data->id_pegawai_bendahara, 'ttd_bendahara', true);
					}
					//cek apakah bendahara
					
					//cek apakah pengguna anggaran
					if($data->id_pegawai_pengguna_anggaran == $this->session->userdata('id_pegawai'))
					{
						$ttd_pengguna_anggaran = $this->cek_ttd($id_inbox, $data->id_pembayaran, $this->session->userdata('id_pegawai'), 'ttd_pengguna_anggaran');						
					}else{
						$ttd_pengguna_anggaran = $this->cek_ttd($id_inbox, $data->id_pembayaran, $data->id_pegawai_pengguna_anggaran, 'ttd_pengguna_anggaran', true);
					}
					//cek apakah bendahara
					
					
					$rs['data_berkas'] = $this->berkas->get_where_criteria(array('active'=> 1), 'nama_berkas, id_berkas', true);
					$rs['data'] = $data;
					$rs['data_pejabat_pphp'] = $pejabat_pphp;
					$rs['ttd_pptk'] = '<div id="logo_ttd_pptk"></div><button type="button" class="btn btn-success btn-circle btn-lg"><i class="glyphicon glyphicon-ok"></i></button>';
					$rs['ttd_ppk'] = $ttd_ppk;
					$rs['ttd_ppk_bast'] = $ttd_ppk_bast;
					$rs['ttd_bendahara'] = $ttd_bendahara;
					$rs['ttd_pengguna_anggaran'] = $ttd_pengguna_anggaran;
					$rs['ttd_ppk_bapenerimaan'] = $ttd_ppk_bapenerimaan;
					
					
					$isi_template_surat_permohonan = $this->load->view('hasil_pekerjaan/template_surat_penyerahan_hasil_pekerjaan', @$rs, true);
					if(date('Y', strtotime($data->tgl_pekerjaan)) >= '2021')
					{
						$get_prokeg = $this->inbox->get($start = null, $length = null, $sort = null, $order = null, $wherex = array('id_spk'=> $data->id_spk), $like = null, $table='rincian_detail_spk', $field='*', $row_array=true, $join=false);
						
						$data_prokeg = json_decode($get_prokeg['data_prokeg_json'], true);
						
						$rs['data_prokeg'] = $data_prokeg;
						
						
						$isi_template_bas = $this->load->view('hasil_pekerjaan/template_basv2_2021', @$rs, true);
						$isi_template_bast = $this->load->view('hasil_pekerjaan/template_bast_2021', @$rs, true);
					}else{				
						
						$isi_template_bas = $this->load->view('hasil_pekerjaan/template_basv2', @$rs, true);
						$isi_template_bast = $this->load->view('hasil_pekerjaan/template_bast', @$rs, true);
					}
					
					if(!empty($pejabat_pphp)){
						$hasil_2 .= '
						<page size="A4">'.$isi_template_bas.'</page>';
					}
					$hasil_2 .= '
					<page size="F4">'.$isi_template_bast.'</page>
					<page size="A4">'.$isi_template_surat_permohonan.'</page>
					<page size="batas" layout="portrait"></page>';
					
				}
				
				$data_rincian = $this->rincian->get_by_id_multi($data_all[0]->id_spk, 'id');
			
				$where_rincian = array();
				if(!empty($data_rincian)){
					foreach($data_rincian as $rst){
						$where_rincian[] = $rst['id'];
					}
				}
				$data_pembayaran_rincian = $this->pembayaran_rincian->get_by_id($id_read, '
					uraian_dpa,
					volume,
					satuan,
					harga_satuan,
					jumlah_harga_satuan,
				', $where_rincian);
				
				// var_dump($where_rincian, $data_rincian, $this->db_ba->last_query());exit;
				$data_adendum = $this->adendum->get_where(array('id_pembayaran'=> $id_read, 'status'=>1), 'no_adendum, tgl_adendum, biaya_adendum, waktu_pelaksanaan_adendum', true);
				$rss['data_adendum'] = $data_adendum;
				$rss['data_pembayaran_rincian'] = $data_pembayaran_rincian;
				$rss['data_pembayaran'] = $reversed;
				
				$rss['viewer'] = true;
				
				
				$isi_template_surat_permohonan_pembayaran = $this->load->view('pembayaran/template_surat_permohonan', @$rss, true);
				
				if(date('Y', strtotime($data_all[0]->tgl_pekerjaan)) >= '2021')
				{
					$rs['data_prokeg'] = $data_prokeg;
					
					$template_lampiran_bapembayaran_v2 = $this->load->view('pembayaran/template_lampiran_bapembayaran_v2_2021', @$rss, true);
					$isi_template_pembayaran = $this->load->view('pembayaran/template_bapembayaran_v2_2021', @$rss, true);
					$isi_kwitansi_pembayaran = $this->load->view('pembayaran/template_kwitansi_pembayaran_2021', @$rss, true);

				}else{
					$template_lampiran_bapembayaran_v2 = $this->load->view('pembayaran/template_lampiran_bapembayaran_v2', @$rss, true);
					$isi_template_pembayaran = $this->load->view('pembayaran/template_bapembayaran_v2', @$rss, true);
					$isi_kwitansi_pembayaran = $this->load->view('pembayaran/template_kwitansi_pembayaran', @$rss, true);
				}
				
				$hasil_1 = '
				<page size="kwitansi">'.$isi_kwitansi_pembayaran.'</page>
				<page size="F4">'.$isi_template_pembayaran.'</page>
				<page size="F4">'.$template_lampiran_bapembayaran_v2.'</page>
				<page size="A4">'.$isi_template_surat_permohonan_pembayaran.'</page>
				';
				
				$hasil .= $hasil_1;
				$hasil .= $hasil_2;
				
			}
		}
		
		$content['id_read'] = $id_read;
		$content['keterangan'] = $keterangan;
		$content['id_inbox'] = $id_inbox;

		if($keterangan == '2'){

			$wherezs['id_hasil_pekerjaan'] = $id_read;
			$wherezs['type'] = 2;
			$wherezs['mailto'] = $this->session->userdata('id_pegawai');
			$data_update = array('viewed' =>  date('Y-m-d H:i:s'));
			$this->inbox->update($wherezs, $data_update);

			$get_data_surat = $this->person->get($start = null, $length = null, $sort = null, $order = null, $where = array('a.id_surat'=> $id_read, 'a.active'=> 1), $like = null, $table='t_surat a', $field='a.*', $row_array=true, $join=false);
			
			$get_data_surat['id_inbox'] = $id_inbox;

			if($get_data_surat['id_bentuk'] == '7' ){
				$hasil = surat_biasa_nontender($get_data_surat);
			}
			if($get_data_surat['id_bentuk'] == '11'){
				$hasil = surat_undangan_nontender($get_data_surat);
			}
			if($get_data_surat['id_bentuk'] == '12'){
				$hasil = surat_berita_acara_nontender($get_data_surat);
			}
			if($get_data_surat['id_bentuk'] == '13'){
				$hasil = surat_notadinas_nontender($get_data_surat);
			}
			

			if($get_data_surat['id_bentuk'] == '6' ){
				$hasil = surat_biasa($get_data_surat);
			}

			
			if($get_data_surat['id_bentuk'] == '4'){
				$hasil = surat_spt($get_data_surat);
			}

			if($get_data_surat['id_bentuk'] == '1'){
				$hasil = surat_undangan($get_data_surat);
			}

			if($get_data_surat['id_bentuk'] == '34'){
				$hasil = surat_berita_acara($get_data_surat);
			}

			if($get_data_surat['id_bentuk'] == '2'){
				$hasil = surat_notadinas($get_data_surat);
			}

			if($get_data_surat['id_bentuk'] == '9'){
				$hasil = surat_pengantar($get_data_surat);
			}
		}
		
		$content['hasil'] = $hasil;
		echo $this->load->view('inbox/view_template',@$content);
		
	}
	
	
	public function cek_ttd($id_inbox = '', $id = '', $id_pegawai = '', $type = '', $kondisi = false, $hasil_pekerjaan = 0){
		$ttd = '';
		
		//id ini bisa id_hasil_pekerjaan ataupun id_pembayaran
		//$hasil_pekerjaan jika true maka itu ppk tanda tangan di hasil pekerjaan
		$where = (($type == 'ttd_ppk' && $hasil_pekerjaan == 0) || ($type == 'ttd_bendahara' || $type == 'ttd_pengguna_anggaran')) ? array('id_pembayaran'=> $id, 'mailto'=> $id_pegawai, "active" => 1) : array('id_hasil_pekerjaan'=> $id, 'mailto'=> $id_pegawai, "active" => 1);
		$status_ttd = $this->inbox->get_by_id($where, 'paraf');
		
		
		
		if(@$status_ttd){
			$where = (($type == 'ttd_ppk' && $hasil_pekerjaan == 0) || ($type == 'ttd_bendahara' || $type == 'ttd_pengguna_anggaran')) ? array('id_pembayaran'=> $id, 'mailfrom'=> $id_pegawai, "active" => 1) : array('id_hasil_pekerjaan'=> $id, 'mailfrom'=> $id_pegawai, "active" => 1);
			$status_ttd = $this->inbox->get_by_id($where, 'paraf, status');			
			
			
			if($type == 'ttd_ppk_bapenerimaan'){
				// $where = array('mailfrom'=> $id_pegawai,);
				/* $total_from = $this->inbox->get_by_id_count_all($where, 'paraf, status'); //apabila hanya satu maka benar ini ttd_ppk_bapenerimaan
				$total_to = $this->inbox->get_by_id_count_all(array('id_hasil_pekerjaan'=> $id, 'mailto'=> $id_pegawai, "active" => 1), 'paraf, status'); //apabila hanya satu maka benar ini ttd_ppk_bapenerimaan
				
				//tambahan 29-10-2019
				if($total_from == 1){
					$status_ttd->paraf = null;
				}else{
					if($total_from == $total_to){						
						$status_ttd->paraf = null;
					}else{
						$kondisi = true;
					}
				} */
				$totalnya = $this->inbox->get_by_id_count_all($where, 'paraf, status', true); //apabila hanya satu maka benar ini ttd_ppk_bapenerimaan
			
				if($totalnya == 1){
					$status_ttd->paraf = null;
				}else{
					$kondisi = true;
				}
			}
			
			if(@$status_ttd->paraf == '1')
			{
				$ttd = '<div class="logo_'.$type.'"></div><button type="button" class="btn btn-success btn-circle btn-lg"><i class="glyphicon glyphicon-ok"></i></button>';
			}else if(@$status_ttd->paraf == '0'){
				$ttd = '<div class="logo_'.$type.'"></div><button type="button" class="btn btn-danger btn-circle btn-lg"><i class="glyphicon glyphicon-remove-sign"></i></button>';
			}else{
				
				if($kondisi){
					$ttd = '';
					// $ttd = '<div class="logo_'.$type.'"></div><div class="tombol_'.$type.'"><input disabled id="'.$type.'" type="checkbox"></div>';
				}else{					
					$ttd = '<div class="logo_'.$type.'"></div><div class="tombol_'.$type.'"><input id="'.$type.'" onchange="doAcc(\''.$type.'\', '.$id.', '.$hasil_pekerjaan.','.$id_inbox.');" type="checkbox"></div>';
				}
			}
		}
		
		
		return $ttd;
	}
	
	public function next_paraf($id = '', $name_id='', $hasilpekerjaan = '', $statusParaf){
		//cek daftar tujuan
		$before_next_callback = array();
		
		if(($name_id == 'ttd_ppk' && $hasilpekerjaan == 0) || ($name_id == 'ttd_bendahara' || $name_id == 'ttd_pengguna_anggaran')){ //ini jika ba pembayaran
			$result_daftar_ttd = $this->hasil_pekerjaan->get_by_id_detail($id, 'id_pegawai_pptk, nip_pegawai_pptk, nama_pegawai_pptk, nomenklatur_jabatan_pptk, id_pegawai_ppk, nip_pegawai_ppk, nama_pegawai_ppk, nomenklatur_jabatan_ppk, id_pegawai_bendahara, nip_pegawai_bendahara, nama_pegawai_bendahara, nomenklatur_jabatan_bendahara, id_pegawai_pengguna_anggaran, nip_pegawai_pengguna_anggaran, nama_pegawai_pengguna_anggaran, nomenklatur_jabatan_pengguna_anggaran');
			$daftar_ttd = $result_daftar_ttd[0];
			
			$tujuan_next[] = array('mailto'=> $daftar_ttd->id_pegawai_pptk, 'nip_mailto'=> $daftar_ttd->nip_pegawai_pptk, 'nama_mailto'=> $daftar_ttd->nama_pegawai_pptk, 'nomenklatur_jabatan_mailto'=> $daftar_ttd->nomenklatur_jabatan_pptk);
			$tujuan_next[] = array('mailto'=> $daftar_ttd->id_pegawai_ppk, 'nip_mailto'=> $daftar_ttd->nip_pegawai_ppk, 'nama_mailto'=> $daftar_ttd->nama_pegawai_ppk, 'nomenklatur_jabatan_mailto'=> $daftar_ttd->nomenklatur_jabatan_ppk);
			$tujuan_next[] = array('mailto'=> $daftar_ttd->id_pegawai_bendahara, 'nip_mailto'=> $daftar_ttd->nip_pegawai_bendahara, 'nama_mailto'=> $daftar_ttd->nama_pegawai_bendahara, 'nomenklatur_jabatan_mailto'=> $daftar_ttd->nomenklatur_jabatan_bendahara);
			$tujuan_next[] = array('mailto'=> $daftar_ttd->id_pegawai_pengguna_anggaran, 'nip_mailto'=> $daftar_ttd->nip_pegawai_pengguna_anggaran, 'nama_mailto'=> $daftar_ttd->nama_pegawai_pengguna_anggaran, 'nomenklatur_jabatan_mailto'=> $daftar_ttd->nomenklatur_jabatan_pengguna_anggaran);
			
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
						$next_inbox = $tujuan_next[0];
					}else{
						$next_inbox = $tujuan_next[$i+1];
					}
					$before_next = $tujuan_before[0];
					break;
				}
			}
			//cek daftar tujuan
			$next = array_merge($next_inbox, $before_next);
			
			//cek daftar tujuan
			for($i=0; $i<count($tujuan_next); $i++)
			{
				if($this->session->userdata('id_pegawai') == $tujuan_next[$i]['mailto'])
				{				
					$next_inbox_callback = $tujuan_next[0];
					$before_next_callback = $tujuan_before[$i];
					break;
				}
			}
			
			
			//cek daftar tujuan
			$next_callback = array_merge($next_inbox_callback, $before_next_callback);
			
			if($next['mailfrom'] == $next['mailto']){
				return array($next_callback);
			}else{
				if($statusParaf == 0){
					return array($next_callback);
				}else{
					return array($next_callback, $next);
				}
			}
			
		}else{//ini jika ba hasil pekerjaan saja
			
			$daftar_ttd = $this->hasil_pekerjaan->get_by_id($id, 'id_pegawai_pptk, nip_pegawai_pptk, nama_pegawai_pptk, nomenklatur_jabatan_pptk, id_pegawai_ppk, nip_pegawai_ppk, nama_pegawai_ppk, nomenklatur_jabatan_ppk');
			
			//ambil daftar data pphp
			$daftar_pphp = $this->paraf_pphp->get_where_criteria(array('id_hasil_pekerjaan'=> $id, 'status'=> 1), 'id_pegawai_pphp, nip_pegawai_pphp, nama_pegawai_pphp, nomenklatur_jabatan_pphp', true);
			
			if(!empty($daftar_pphp)){
			
				for($i=0; $i<count($daftar_pphp); $i++)
				{
					if($this->session->userdata('id_pegawai') == $daftar_pphp[$i]['id_pegawai_pphp'])
					{
						$before_next_callback[] = array(
							'mailto'=> $daftar_ttd->id_pegawai_pptk, 
							'nip_mailto'=> $daftar_ttd->nip_pegawai_pptk, 
							'nama_mailto'=> $daftar_ttd->nama_pegawai_pptk, 
							'nomenklatur_jabatan_mailto'=>  $daftar_ttd->nomenklatur_jabatan_pptk,
							'mailfrom'=> $daftar_pphp[$i]['id_pegawai_pphp'], 
							'nip_mailfrom'=> $daftar_pphp[$i]['nip_pegawai_pphp'], 
							'nama_mailfrom'=> $daftar_pphp[$i]['nama_pegawai_pphp'], 
							'nomenklatur_jabatan_mailfrom'=> $daftar_pphp[$i]['nomenklatur_jabatan_pphp']
						);
						
						if(@$daftar_pphp[$i+1]['id_pegawai_pphp']){
							//kirim ke selanjutnya
							$before_next_callback[] = array(
								'mailto'=> $daftar_pphp[$i+1]['id_pegawai_pphp'], 
								'nip_mailto'=> $daftar_pphp[$i+1]['nip_pegawai_pphp'], 
								'nama_mailto'=> $daftar_pphp[$i+1]['nama_pegawai_pphp'], 
								'nomenklatur_jabatan_mailto'=> $daftar_pphp[$i+1]['nomenklatur_jabatan_pphp'],
								'mailfrom'=> $daftar_ttd->id_pegawai_pptk, 
								'nip_mailfrom'=> $daftar_ttd->nip_pegawai_pptk, 
								'nama_mailfrom'=> $daftar_ttd->nama_pegawai_pptk, 
								'nomenklatur_jabatan_mailfrom'=> $daftar_ttd->nomenklatur_jabatan_pptk
							);						
						}
						
						break;
					}
				}
			}
			
			
			if($statusParaf == "0" && count($daftar_pphp) > 1 || $statusParaf == "1" && count($daftar_pphp) >= 1){
				if(empty($before_next_callback) || count($before_next_callback) == 1){
					if($this->session->userdata('id_pegawai') != $daftar_ttd->id_pegawai_ppk)
					{
						$before_next_callback[] = array(
							'mailto'=> $daftar_ttd->id_pegawai_ppk, 
							'nip_mailto'=> $daftar_ttd->nip_pegawai_ppk, 
							'nama_mailto'=> $daftar_ttd->nama_pegawai_ppk, 
							'nomenklatur_jabatan_mailto'=> $daftar_ttd->nomenklatur_jabatan_ppk,
							'mailfrom'=> $daftar_ttd->id_pegawai_pptk, 
							'nip_mailfrom'=> $daftar_ttd->nip_pegawai_pptk, 
							'nama_mailfrom'=> $daftar_ttd->nama_pegawai_pptk, 
							'nomenklatur_jabatan_mailfrom'=> $daftar_ttd->nomenklatur_jabatan_pptk
						);	
					}else{
						$before_next_callback[] = array(
							'mailto'=> $daftar_ttd->id_pegawai_pptk, 
							'nip_mailto'=> $daftar_ttd->nip_pegawai_pptk, 
							'nama_mailto'=> $daftar_ttd->nama_pegawai_pptk, 
							'nomenklatur_jabatan_mailto'=> $daftar_ttd->nomenklatur_jabatan_pptk,
							'mailfrom'=> $daftar_ttd->id_pegawai_ppk, 
							'nip_mailfrom'=> $daftar_ttd->nip_pegawai_ppk, 
							'nama_mailfrom'=> $daftar_ttd->nama_pegawai_ppk, 
							'nomenklatur_jabatan_mailfrom'=> $daftar_ttd->nomenklatur_jabatan_ppk
						);
					}
				}
			}else if($statusParaf == 0 && (count($daftar_pphp) == 1 && $daftar_pphp[0]['id_pegawai_pphp'] == $this->session->userdata('id_pegawai'))){
				//update status 2 untuk perbaikan
				$this->hasil_pekerjaan->update(array("id_hasil_pekerjaan"=> $id), array("draft"=> 2));
			}
			
			// var_dump($statusParaf, count($daftar_pphp));
			// exit;
			if($name_id == 'ttd_ppk_bast' && $statusParaf == 1){				
				//cek dulu, apabila ini tanda tangan ppk bast 
				//maka ada tambahan pptk ke pphp
				if(!empty($daftar_pphp)){
					$before_next_callback[] = array(
						'mailfrom'=> $daftar_ttd->id_pegawai_pptk, 
						'nip_mailfrom'=> $daftar_ttd->nip_pegawai_pptk, 
						'nama_mailfrom'=> $daftar_ttd->nama_pegawai_pptk, 
						'nomenklatur_jabatan_mailfrom'=>  $daftar_ttd->nomenklatur_jabatan_pptk,
						'mailto'=> $daftar_pphp[0]['id_pegawai_pphp'], 
						'nip_mailto'=> $daftar_pphp[0]['nip_pegawai_pphp'], 
						'nama_mailto'=> $daftar_pphp[0]['nama_pegawai_pphp'], 
						'nomenklatur_jabatan_mailto'=> $daftar_pphp[0]['nomenklatur_jabatan_pphp']
					);		
				}else{
					$before_next_callback[] = array(
						'mailfrom'=> $daftar_ttd->id_pegawai_ppk, 
						'nip_mailfrom'=> $daftar_ttd->nip_pegawai_ppk, 
						'nama_mailfrom'=> $daftar_ttd->nama_pegawai_ppk, 
						'nomenklatur_jabatan_mailfrom'=>  $daftar_ttd->nomenklatur_jabatan_ppk,
						'mailto'=> $daftar_ttd->id_pegawai_pptk, 
						'nip_mailto'=> $daftar_ttd->nip_pegawai_pptk, 
						'nama_mailto'=> $daftar_ttd->nama_pegawai_pptk, 
						'nomenklatur_jabatan_mailto'=> $daftar_ttd->nomenklatur_jabatan_pptk
					);		
				}
			}
			
			if($name_id == 'ttd_ppk_bast' && $statusParaf == 0){				
				//cek dulu, apabila ini tanda tangan ppk bast 
				//maka ada tambahan pptk ke pphp
				$this->hasil_pekerjaan->update(array("id_hasil_pekerjaan"=> $id), array("draft"=> 2));

				$before_next_callback[] = array(
					'mailto'=> $daftar_ttd->id_pegawai_pptk, 
					'nip_mailto'=> $daftar_ttd->nip_pegawai_pptk, 
					'nama_mailto'=> $daftar_ttd->nama_pegawai_pptk, 
					'nomenklatur_jabatan_mailto'=> $daftar_ttd->nomenklatur_jabatan_pptk,
					'mailfrom'=> $daftar_ttd->id_pegawai_ppk, 
					'nip_mailfrom'=> $daftar_ttd->nip_pegawai_ppk, 
					'nama_mailfrom'=> $daftar_ttd->nama_pegawai_ppk, 
					'nomenklatur_jabatan_mailfrom'=> $daftar_ttd->nomenklatur_jabatan_ppk
				);		
			}
			
			
			return $before_next_callback;
		}
	}
	
	private function next_parafbukanBA(){
		$post = $this->input->post();

		$id_pegawai_paraf = $post['id'];
		$id_hasil_pekerjaan = $post['hasilpekerjaan'];
		$statusparaf = $post['statusParaf'];

		$get_surat = $this->inbox->get($start = null, $length = null, $sort = null, $order = null, $wherex = array('a.id_surat'=> $id_hasil_pekerjaan, 'a.active'=> 1), $like = null, $table='t_surat a', $field='a.id_surat, a.penandatangan, a.paraf, a.tembusan, a.rekap_peg_created, a.id_bentuk, a.sp_kepada_pns, a.nomor, a.id_bentuk, a.kepada', $row_array=true, $join=false);


		$kepada = (!empty($get_surat['kepada'])) ? json_decode($get_surat['kepada'], true) : array();
		$penandatangan = (!empty($get_surat['penandatangan'])) ? json_decode($get_surat['penandatangan'], true) : array();
		$paraf = (!empty($get_surat['paraf'])) ? json_decode($get_surat['paraf'], true) : array();
		$tembusan = (!empty($get_surat['tembusan'])) ? json_decode($get_surat['tembusan'], true) : array();
		$pembuat = (!empty($get_surat['rekap_peg_created'])) ? json_decode($get_surat['rekap_peg_created'], true) : array();

		//kirim setuju
		if($get_surat['id_bentuk'] == '4'){
			$sp_kepada_pns = (!empty($get_surat['sp_kepada_pns'])) ? json_decode($get_surat['sp_kepada_pns'], true) : array();
			//kirim dari penandatangan ke pembuat
			$row = array();
			$row['id_hasil_pekerjaan'] = $id_hasil_pekerjaan;
			$row['type'] = 2;
			$row['mailto'] = $pembuat[0]['id_pegawai'];
			$row['mailto_json'] = json_encode($pembuat);
			$row['nip_mailto'] = $pembuat[0]['nip_baru'];
			$row['nama_mailto'] = ini_pegawai_nama_arr($pembuat[0]);
			$row['nomenklatur_jabatan_mailto'] = $pembuat[0]['nomenklatur_jabatan'];
			$row['mailfrom'] = $penandatangan[0]['id_pegawai'];
			$row['mailfrom_json'] = json_encode(array($penandatangan[0]));
			$row['nip_mailfrom'] = $penandatangan[0]['nip_baru'];
			$row['nama_mailfrom'] = ini_pegawai_nama_arr($penandatangan[0]);
			$row['nomenklatur_jabatan_mailfrom'] = $penandatangan[0]['nomenklatur_jabatan'];
			$row['status'] = 2;
			$row['paraf'] = 1;
			$row['notif_text'] = 'Surat Masuk';
			$row['cdd'] = date('Y-m-d H:i:s');
			$insert = $this->inbox->savev2($row, $table='t_inbox');
			

			//kirim dari pembuat ke sp pegawai pns
			$insert_inbox = array();

			foreach($sp_kepada_pns as $rss)
			{
				$row = array();
				$row['id_hasil_pekerjaan'] = $id_hasil_pekerjaan;
				$row['type'] = 2;
				$row['mailfrom'] = $penandatangan[0]['id_pegawai'];
				$row['mailfrom_json'] = json_encode($penandatangan);
				$row['nip_mailfrom'] = $penandatangan[0]['nip_baru'];
				$row['nama_mailfrom'] = ini_pegawai_nama_arr($penandatangan[0]);
				$row['nomenklatur_jabatan_mailfrom'] = $penandatangan[0]['nomenklatur_jabatan'];
				
				$row['mailto'] = $rss['id_pegawai'];
				$row['mailto_json'] = json_encode(array($rss));
				$row['nip_mailto'] = $rss['nip_baru'];
				$row['nama_mailto'] = ini_pegawai_nama_arr($rss);
				$row['nomenklatur_jabatan_mailto'] = $rss['nomenklatur_jabatan'];
				$row['status'] = 6;
				$row['paraf'] = 0;
				$row['notif_text'] = 'Surat Masuk';
				$row['cdd'] = date('Y-m-d H:i:s');
				$insert_inbox[] = $row;

				$rps = array();
				$result_hasil['data'] = array();
				$rps['id_surat'] = $id_hasil_pekerjaan;
				$rps['title'] = 'BANGJAKA';
				$rps['longmessage'] = 'Anda menerima Surat Perintah Tugas Nomor : '.$get_surat['nomor'].' dari '. ini_pegawai_nama_arr($penandatangan[0]) . ' ('.$penandatangan[0]['nomenklatur_jabatan'].')';
				$rps['id_bentuk'] = $get_surat['id_bentuk'];
				$result_hasil['data'] = $rps;
				send_wa($result_hasil, $rss['id_pegawai']);
			}
			$this->db->insert_batch('t_inbox',$insert_inbox);
			$update = $this->inbox->update(array('id_surat'=> $id_hasil_pekerjaan, 'active'=> 1), array('acc'=> 1, 'acc_date'=> date('Y-m-d H:i:s')), $table='t_surat');
			//update acc jadi 1 pada surat
			
		}else if($get_surat['id_bentuk'] == '34'){
			$selanjutnya = array_merge($penandatangan, $paraf);
			$inbox_next = array_reverse($selanjutnya);

			$setuju_arr = array();
			$next_arr = array(); 
			$posisi_akhir = count($inbox_next) - 1;
			$insert_inbox = array();
			for($i=0; $i<count($inbox_next); $i++)
			{
				if($inbox_next[$i]['id_pegawai'] == $id_pegawai_paraf)
				{
					$setuju_arr = $inbox_next[$i];

					$row = array();
					$row['id_hasil_pekerjaan'] = $id_hasil_pekerjaan;
					$row['type'] = 2;
					$row['mailto'] = $pembuat[0]['id_pegawai'];
					$row['mailto_json'] = json_encode($pembuat);
					$row['nip_mailto'] = $pembuat[0]['nip_baru'];
					$row['nama_mailto'] = ini_pegawai_nama_arr($pembuat[0]);
					$row['nomenklatur_jabatan_mailto'] = $pembuat[0]['nomenklatur_jabatan'];
					$row['mailfrom'] = $setuju_arr['id_pegawai'];
					$row['mailfrom_json'] = json_encode(array($setuju_arr));
					$row['nip_mailfrom'] = $setuju_arr['nip_baru'];
					$row['nama_mailfrom'] = ini_pegawai_nama_arr($setuju_arr);
					$row['nomenklatur_jabatan_mailfrom'] = $setuju_arr['nomenklatur_jabatan'];
					$row['status'] = 2;
					$row['paraf'] = 1;
					$row['notif_text'] = 'Surat Masuk';
					$row['cdd'] = date('Y-m-d H:i:s');

					$insert_inbox[] = $row;


					$cekpegawai_timpokja = array_search($id_pegawai_paraf, array_column($penandatangan, 'id_pegawai'));
					
					if($cekpegawai_timpokja === false)
					{
						foreach($penandatangan as $next_arr){
							$row = array();
							$row['id_hasil_pekerjaan'] = $id_hasil_pekerjaan;
							$row['type'] = 2;
							$row['mailto'] = $next_arr['id_pegawai'];
							$row['mailto_json'] = json_encode(array($next_arr));
							$row['nip_mailto'] = $next_arr['nip_baru'];
							$row['nama_mailto'] = ini_pegawai_nama_arr($next_arr);
							$row['nomenklatur_jabatan_mailto'] = $next_arr['nomenklatur_jabatan'];
							$row['mailfrom'] = $pembuat[0]['id_pegawai'];
							$row['mailfrom_json'] = json_encode($pembuat);
							$row['nip_mailfrom'] = $pembuat[0]['nip_baru'];
							$row['nama_mailfrom'] = ini_pegawai_nama_arr($pembuat[0]);
							$row['nomenklatur_jabatan_mailfrom'] = $pembuat[0]['nomenklatur_jabatan'];
							$row['status'] = 0;
							$row['paraf'] = 0;
							$row['notif_text'] = 'Surat Masuk';
							$row['cdd'] = date('Y-m-d H:i:s');

							$insert_inbox[] = $row;
							
				
							$rps = array();
							$result_hasil['data'] = array();
							$rps['id_surat'] = $id_hasil_pekerjaan;
							$rps['title'] = 'Bangjaka';
							$rps['longmessage'] = 'Anda menerima Surat Berita Acara Kaji Ulang Nomor : '.$get_surat['nomor'].' dari '. ini_pegawai_nama_arr($pembuat[0]);
							$rps['id_bentuk'] = $get_surat['id_bentuk'];
							$result_hasil['data'] = $rps;
							send_wa($result_hasil, $next_arr['id_pegawai']);
						}
					}

					$totalttd = 1;

					foreach($penandatangan as $rss)
					{
						$sdhacc = $this->person->get($start = null, $length = null, $sort = null, $order = null, $where = array('a.mailfrom'=> $rss['id_pegawai'], 'a.id_hasil_pekerjaan'=> $get_surat['id_surat'], 'type'=> '2', 'active'=> 1, 'a.paraf'=> 1), $like = null, $table='t_inbox a', $field='a.paraf', $row_array=true, $join=false);
						
						if(!empty($sdhacc)){
							$totalttd += 1;
						}
					}

					if(count($penandatangan) == $totalttd){
						$update = $this->inbox->update(array('id_surat'=> $get_surat['id_surat'], 'active'=> 1), array('acc'=> 1, 'acc_date'=> date('Y-m-d H:i:s')), $table='t_surat');
					}
					


					$this->db->insert_batch('t_inbox',$insert_inbox);
					break;
				}
			}
		}else if($get_surat['id_bentuk'] == '2'){
			$selanjutnya = array_merge($kepada, $penandatangan, $paraf);
			$inbox_next = array_reverse($selanjutnya);

			$setuju_arr = array();
			$next_arr = array(); 
			$posisi_akhir = count($inbox_next) - 1;
			$insert_inbox = array();
			for($i=0; $i<count($inbox_next); $i++)
			{
				if($inbox_next[$i]['id_pegawai'] == $id_pegawai_paraf)
				{
					$setuju_arr = $inbox_next[$i];

					$row = array();
					$row['id_hasil_pekerjaan'] = $id_hasil_pekerjaan;
					$row['type'] = 2;
					$row['mailto'] = $pembuat[0]['id_pegawai'];
					$row['mailto_json'] = json_encode($pembuat);
					$row['nip_mailto'] = $pembuat[0]['nip_baru'];
					$row['nama_mailto'] = ini_pegawai_nama_arr($pembuat[0]);
					$row['nomenklatur_jabatan_mailto'] = $pembuat[0]['nomenklatur_jabatan'];
					$row['mailfrom'] = $setuju_arr['id_pegawai'];
					$row['mailfrom_json'] = json_encode(array($setuju_arr));
					$row['nip_mailfrom'] = $setuju_arr['nip_baru'];
					$row['nama_mailfrom'] = ini_pegawai_nama_arr($setuju_arr);
					$row['nomenklatur_jabatan_mailfrom'] = $setuju_arr['nomenklatur_jabatan'];
					$row['status'] = 2;
					$row['paraf'] = 1;
					$row['notif_text'] = 'Surat Masuk';
					$row['cdd'] = date('Y-m-d H:i:s');

					$insert_inbox[] = $row;


					$cekpegawai_timpokja = array_search($id_pegawai_paraf, array_column($penandatangan, 'id_pegawai'));
					
					if($cekpegawai_timpokja !== false)
					{
						$update = $this->inbox->update(array('id_surat'=> $get_surat['id_surat'], 'active'=> 1), array('acc'=> 1, 'acc_date'=> date('Y-m-d H:i:s')), $table='t_surat');

						$row = array();
						$row['id_hasil_pekerjaan'] = $id_hasil_pekerjaan;
						$row['type'] = 2;
						$row['mailto'] = $kepada[0]['id_pegawai'];
						$row['mailto_json'] = json_encode($kepada);
						$row['nip_mailto'] = $kepada[0]['nip_baru'];
						$row['nama_mailto'] = ini_pegawai_nama_arr($kepada[0]);
						$row['nomenklatur_jabatan_mailto'] = $kepada[0]['nomenklatur_jabatan'];
						$row['mailfrom'] = $setuju_arr['id_pegawai'];
						$row['mailfrom_json'] = json_encode(array($setuju_arr));
						$row['nip_mailfrom'] = $setuju_arr['nip_baru'];
						$row['nama_mailfrom'] = ini_pegawai_nama_arr($setuju_arr);
						$row['nomenklatur_jabatan_mailfrom'] = $setuju_arr['nomenklatur_jabatan'];
						$row['status'] = 2;
						$row['paraf'] = 1;
						$row['notif_text'] = 'Surat Masuk';
						$row['cdd'] = date('Y-m-d H:i:s');

						$insert_inbox[] = $row;
					}

					// $totalttd = 1;

					// foreach($penandatangan as $rss)
					// {
					// 	$sdhacc = $this->person->get($start = null, $length = null, $sort = null, $order = null, $where = array('a.mailfrom'=> $rss['id_pegawai'], 'a.id_hasil_pekerjaan'=> $get_surat['id_surat'], 'type'=> '2', 'active'=> 1, 'a.paraf'=> 1), $like = null, $table='t_inbox a', $field='a.paraf', $row_array=true, $join=false);
						
					// 	if(!empty($sdhacc)){
					// 		$totalttd += 1;
					// 	}
					// }

					// if(count($penandatangan) == $totalttd){
					// 	$update = $this->inbox->update(array('id_surat'=> $get_surat['id_surat']), array('acc'=> 1, 'acc_date'=> date('Y-m-d H:i:s')), $table='t_surat');
					// }
					


					$this->db->insert_batch('t_inbox',$insert_inbox);
					break;
				}
			}
		}else{
			$selanjutnya = array_merge($penandatangan, $paraf);
			$inbox_next = array_reverse($selanjutnya);

			$setuju_arr = array();
			$next_arr = array(); 
			$posisi_akhir = count($inbox_next) - 1;
			$insert_inbox = array();
			for($i=0; $i<count($inbox_next); $i++)
			{
				if($inbox_next[$i]['id_pegawai'] == $id_pegawai_paraf)
				{
					$setuju_arr = $inbox_next[$i];

					$row = array();
					$row['id_hasil_pekerjaan'] = $id_hasil_pekerjaan;
					$row['type'] = 2;
					$row['mailto'] = $pembuat[0]['id_pegawai'];
					$row['mailto_json'] = json_encode($pembuat);
					$row['nip_mailto'] = $pembuat[0]['nip_baru'];
					$row['nama_mailto'] = ini_pegawai_nama_arr($pembuat[0]);
					$row['nomenklatur_jabatan_mailto'] = $pembuat[0]['nomenklatur_jabatan'];
					$row['mailfrom'] = $setuju_arr['id_pegawai'];
					$row['mailfrom_json'] = json_encode(array($setuju_arr));
					$row['nip_mailfrom'] = $setuju_arr['nip_baru'];
					$row['nama_mailfrom'] = ini_pegawai_nama_arr($setuju_arr);
					$row['nomenklatur_jabatan_mailfrom'] = $setuju_arr['nomenklatur_jabatan'];
					$row['status'] = 2;
					$row['paraf'] = 1;
					$row['notif_text'] = 'Surat Masuk';
					$row['cdd'] = date('Y-m-d H:i:s');

					$insert_inbox[] = $row;

					if($i!=$posisi_akhir)
					{
						$next_arr = $inbox_next[$i+1];
						
						$row = array();
						$row['id_hasil_pekerjaan'] = $id_hasil_pekerjaan;
						$row['type'] = 2;
						$row['mailto'] = $next_arr['id_pegawai'];
						$row['mailto_json'] = json_encode(array($next_arr));
						$row['nip_mailto'] = $next_arr['nip_baru'];
						$row['nama_mailto'] = ini_pegawai_nama_arr($next_arr);
						$row['nomenklatur_jabatan_mailto'] = $next_arr['nomenklatur_jabatan'];
						$row['mailfrom'] = $pembuat[0]['id_pegawai'];
						$row['mailfrom_json'] = json_encode($pembuat);
						$row['nip_mailfrom'] = $pembuat[0]['nip_baru'];
						$row['nama_mailfrom'] = ini_pegawai_nama_arr($pembuat[0]);
						$row['nomenklatur_jabatan_mailfrom'] = $pembuat[0]['nomenklatur_jabatan'];
						$row['status'] = 0;
						$row['paraf'] = 0;
						$row['notif_text'] = 'Surat Masuk';
						$row['cdd'] = date('Y-m-d H:i:s');

						$insert_inbox[] = $row;
					}else{
						$update = $this->inbox->update(array('id_surat'=> $id_hasil_pekerjaan, 'active'=> 1), array('acc'=> 1, 'acc_date'=> date('Y-m-d H:i:s')), $table='t_surat');
					}

					$this->db->insert_batch('t_inbox',$insert_inbox);
					break;
				}
			}
		}
		

		

		$content['status'] = true;
		$content['notif'] = '<div class="alert alert-success" role="alert"><i class="fa fa-thumbs-o-up fa-fw"></i> Data berhasil disimpan</div>';
			// exit;
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_insert(){
		
		$post = $this->input->post();	
		
		//cek dahulu apakah termasuk yg sudah surat diedit atau belum
		$data_active = $this->inbox->get_by_id(array('id_inbox'=>@$post['id_inbox']), 'active');
		if(@$data_active->active === '0'){
			echo json_encode(array("status" => FALSE));
			exit;
		}
		
		$id = $post['id'];
		$statusParaf = $post['statusParaf'];
		$catatan = (!empty($post['catatan'])) ? $post['catatan'] : null;
		$hasilpekerjaan = (!empty($post['hasilpekerjaan'])) ? $post['hasilpekerjaan'] : 0;
		$name_id = $post['name_id'];
		// exit;

		if($name_id == 'bukanBA')
		{
			$this->next_parafbukanBA();
			exit;
		}

		$hasil_next_paraf = $this->next_paraf($id, $name_id, $hasilpekerjaan, $statusParaf);
		$data_mailto = '';
		$data_mailfrom = '';
		$status_next = 0;
		
		foreach($hasil_next_paraf as $rss)
		{
			
			$created = array(
				'cdb'=> $this->session->userdata('id_pegawai'),
				'cdi' => 'web'
			);
			
			//insert data t_inbox
			$id_inbox = '';
			
			
			//cek mailfrom dan mailto sebagai apa
			$this->db_ba->select('*');
			$this->db_ba->from('t_hasil_pekerjaan');
			$this->db_ba->where('id_pembayaran',$id);
			$this->db_ba->order_by('cdd', 'DESC');
			$this->db_ba->limit(1);
			$query = $this->db_ba->get();
			$restult = $query->row_array();	
			//cek mailfrom dan mailto sebagai apa
			
			if($rss["mailfrom"] == $restult['id_pegawai_ppk'] && $rss["mailto"] == $restult['id_pegawai_pptk'])
			{ 
				//jika ppk to pptk
				$status = 2;
			}elseif($rss["mailfrom"] == $restult['id_pegawai_pptk'] && $rss["mailto"] == $restult['id_pegawai_bendahara'])
			{
				//jika pptk to bendahara
				$status = 3;
				$statusParaf = null;
			}elseif($rss["mailfrom"] == $restult['id_pegawai_bendahara'] && $rss["mailto"] == $restult['id_pegawai_pptk'])
			{
				//jika bendahara to pptk
				$status = 3;
			}elseif($rss["mailfrom"] == $restult['id_pegawai_pptk'] && $rss["mailto"] == $restult['id_pegawai_pengguna_anggaran'])
			{
				//jika pptk to pengguna anggaran
				$status = 4;
				$statusParaf = null;
			}elseif($rss["mailfrom"] == $restult['id_pegawai_pengguna_anggaran'] && $rss["mailto"] == $restult['id_pegawai_pptk'])
			{
				//jika pengguna anggaran to pptk
				$status = 4;
			}
			
			// var_dump($name_id, $status);
			// exit;
			$cekemail = $this->user->get_by_id_pegawai($rss["mailto"], 'email');
			
			if(($name_id == 'ttd_ppk' && $hasilpekerjaan == 0) || ($name_id == 'ttd_bendahara' || $name_id == 'ttd_pengguna_anggaran')){					
				$data_inbox = array(
					'id_pembayaran'=> $id,
					'status'=> $status,
					'paraf'=> $statusParaf,
					'catatan'=> $catatan
				);
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
				
				$nilaiTermin .= '
					<tr>
						<td>Nilai Termin '.$texttermin.'</td>
						<td>:</td>
						<td>'.number_format($totalsTermins, 0).' ('.$viewBiaya.')</td>
					</tr>
				';
				
				$nomspk = $this->hasil_pekerjaan->get_by_id_detail_hasil_pekerjaan($data_pekerjaan[0]['id_hasil_pekerjaan'], 
				'c.no_spk, c.nama_kegiatan, c.nama_pekerjaan, d.nama_penyedia, d.nama_perusahaan, d.kategori, b.nominal_bayar, a.nilai_pekerjaan, a.termin');
				
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
			}else{
				$status = 1;
				
				if($status_next == 1){
					$status = 0;
					$statusParaf = null;
				}
				
				$data_inbox = array(
					'id_hasil_pekerjaan'=> $id,
					'status'=> $status,
					'paraf'=> $statusParaf,
					'catatan'=> $catatan
				);
				$data_pekerjaan = $this->hasil_pekerjaan->get_by_id_detail_hasil_pekerjaan($id, 'c.`no_spk`, c.`nama_kegiatan`, c.`nama_pekerjaan`, d.nama_penyedia, d.nama_perusahaan, d.kategori, b.nominal_bayar, a.nilai_pekerjaan, a.termin');					
				
				if($name_id == 'ttd_ppk_bast'){
					$perihal = 'Berita Acara Serah Terima Pekerjaan Termin '.$data_pekerjaan->termin.' ('.$data_pekerjaan->no_spk.')';
				}else{
					$perihal = 'Penyerahan Hasil Pekerjaan Termin '.$data_pekerjaan->termin.' ('.$data_pekerjaan->no_spk.')';
				}
				
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
			}
			$status_next = $status;
			$data_insert = array_merge($data_inbox, $rss);
			$result_inbox = $this->inbox->save($id_inbox, $data_insert, $created);
			//insert data t_inbox

			//send notif email
			$result_email = sendtoEmail($cekemail, $statusParaf, $perihal, $data_surat);
			//send notif email
			
			//send notif
			$data_riwayat = $data_insert;
			$data_riwayat['notif_text'] = '';
			$data_riwayat['mdd'] = date('Y-m-d H:i:s');
			$data_riwayat['mdb'] = $this->session->userdata('id_pegawai');
			$data_riwayat['mdi'] = 'web';
			
			// send_pls_notification($data_riwayat, $result_inbox);
		}		
		
		
		
		$content['status'] = true;
		$content['notif'] = '<div class="alert alert-success" role="alert"><i class="fa fa-thumbs-o-up fa-fw"></i> Data berhasil disimpan</div>';
			// exit;
		echo json_encode(array("status" => TRUE));
	}
	
}
