<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notifikasi extends CI_Controller {
	
	/*
	 * @author     		RENDY
	 * @author_email    rendyardian199@gmail.com
	 *  KOTA TANGERANG 
	 */

	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('inbox_model', 'inbox');		
		$this->load->model('pembayaran_model', 'pembayaran');		
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
		
	public function getcountinbox(){
		$hasil_pekerjaan = $this->inbox->count_all_hasil_pekerjaan(true);
		$pembayaran = $this->inbox->count_all_pembayaran();
		$hasil = $hasil_pekerjaan + $pembayaran;
		if($hasil > 0){
			$result = $hasil;
		}else{
			$result = '';
		}		
		echo $result;		
	}
	
	public function getcountnotif(){
		$hasil = $this->inbox->count_all();
		if($hasil > 0){
			$result = $hasil;
		}else{
			$result = '';
		}		
		echo $result;		
	}
	
	public function getnotifikasi(){
		
		$skrg = date("Y-m-d H:i:s");
		$_POST['length'] = 5;
		$_POST['current_date'] = ($_POST['current_date'] == 0) ? $skrg : $_POST['current_date'];
		$data_end_date = $_POST['current_date'];
		$list = $this->inbox->get_datatables();
		
		$result = '';
		$total = count($list);
		$no = 1;
		if($list){
			foreach($list as $person)
			{
				
				if($person->id_hasil_pekerjaan){
					$jenis_surat = 'Hasil Pekerjaan';
				}else{
					$jenis_surat = 'Permohonan Pembayaran';
				}
				
				if($person->id_hasil_pekerjaan)
				{
					if(@$person->paraf == '1'){
						if($person->paraf == '1'){
							if(!empty($person->viewed)){
								$bold = 'style="cursor:pointer;"';
								$text_status = '<div>telah menandatangani surat yang anda buat<i class="glyphicon glyphicon-leaf"></i></div>';
							}else{
								$bold = 'style="cursor:pointer; font-weight: bold;"';
								$text_status = '<div>telah menandatangani surat yang anda buat<i class="glyphicon glyphicon-leaf"></i></div>';
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
								$text_status = '<div>Surat Masuk Permohonan Hasil Pekerjaan</div>';
							}
							
							if($person->paraf_next == '1'){
								$bold = 'style="cursor:pointer;"';
								$text_status = '<div>Surat Masuk Permohonan Hasil Pekerjaan</div>';
							}
							
							if($person->paraf == null && $person->paraf_next == null){
								$bold = 'style="cursor:pointer;"';
								$text_status = '<div>Surat Masuk Permohonan Hasil Pekerjaan</div>';
							}
							
							if($person->paraf == '0' && $person->paraf_next == null){
								$bold = 'style="cursor:pointer;"';
								$text_status = '<div>telah menolak surat yang anda buat<i class="glyphicon glyphicon-leaf"></i></div>';
							}
							
						}else{
							$bold = 'style="cursor:pointer; font-weight: bold;"';
							$text_status = '<div><i class="glyphicon glyphicon-info-sign"></i> <span class="label label-danger">Terbaru</span></div>';
							
							if($person->paraf_next == '0'){						
								$bold = 'style="cursor:pointer; font-weight: bold;"';
								$text_status = '<div><i class="glyphicon glyphicon-info-sign"></i> <span class="label label-danger">Ditolak</span></div>';
							}
							
							if($person->paraf_next == '1'){					
								$bold = 'style="cursor:pointer; font-weight: bold;"';
								$text_status = '<div><i class="glyphicon glyphicon-info-sign"></i> <span class="label label-danger">Diacc</span></div>';
							}
							
							if($person->paraf == null && $person->paraf_next == null){
								$bold = 'style="cursor:pointer; font-weight: bold;"';
								$text_status = '<div>Surat Masuk Permohonan Hasil Pekerjaan</div>';
							}
							
							if($person->paraf == '0' && $person->paraf_next == null){
								$bold = 'style="cursor:pointer; font-weight: bold;"';
								$text_status = '<div>telah menolak surat yang anda buat<i class="glyphicon glyphicon-leaf"></i></div>';
							}
						}
					}
				}else{
					if(@$person->paraf == '1'){
						if($person->paraf == '1'){
							if($person->status != '2'){
								if(!empty($person->viewed)){
									$bold = 'style="cursor:pointer;"';
									$text_status = '<div>telah menandatangani surat yang anda buat<i class="glyphicon glyphicon-leaf"></i></div>';
								}else{
									$bold = 'style="cursor:pointer; font-weight: bold;"';
									$text_status = '<div>telah menandatangani surat yang anda buat<i class="glyphicon glyphicon-leaf"></i></div>';
								}	
							}else{
								if(!empty($person->viewed)){
									if($person->paraf_next == '0'){						
										$bold = 'style="cursor:pointer;"';
										$text_status = '<div><i class="glyphicon glyphicon-eye-open"></i> <span class="label label-default">Ditolak9</span></div>';
									}
									
									if($person->paraf_next == '1'){
										$bold = 'style="cursor:pointer;"';
										$pembuat = $this->pembayaran->get_by_id($person->id_pembayaran, 'cdb');
										
										$text_status = ($person->mailfrom == $pembuat->cdb) ? '<div>Surat Masuk Permohonan Pembayaran</div>' : 'telah menandatangani surat yang anda buat<i class="glyphicon glyphicon-leaf"></i>';
									}
									
									if($person->paraf == null && $person->paraf_next == null){
										$bold = 'style="cursor:pointer;"';
										$text_status = '<div><i class="glyphicon glyphicon-eye-open"></i> <span class="label label-default">Belum diacc3</span></div>';
									}								
									
									if($person->paraf == '1' && $person->paraf_next == null){
										$bold = 'style="cursor:pointer;"';
										$text_status = '<div>Surat Masuk Permohonan Pembayaran</div>';
									}
								}else{										
									
									if($person->paraf_next == '0'){						
										$bold = 'style="cursor:pointer; font-weight: bold;"';
										$text_status = '<div><i class="glyphicon glyphicon-info-sign"></i> <span class="label label-danger">Ditolak10</span></div>';
									}
									
									if($person->paraf_next == '1'){
										$bold = 'style="cursor:pointer; font-weight: bold;"';
										$text_status = '<div>telah menandatangani surat yang anda buat<i class="glyphicon glyphicon-leaf"></i></div>';
									}
									
									if($person->paraf == null && $person->paraf_next == null){
										$bold = 'style="cursor:pointer; font-weight: bold;"';
										$text_status = '<div><i class="glyphicon glyphicon-info-sign"></i> <span class="label label-danger">Belum diacc5</span></div>';
									}								
									
									if($person->paraf == '1' && $person->paraf_next == null){
										$bold = 'style="cursor:pointer; font-weight: bold;"';
										$text_status = '<div>Surat Masuk Permohonan Pembayaran</div>';
									}
									
									if($person->paraf == '0'){
										$bold = 'style="cursor:pointer; font-weight: bold;"';
										$text_status = '<div><i class="glyphicon glyphicon-info-sign"></i> <span class="label label-danger">Ditolak11</span></div>';
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
								$text_status = '<div>Surat Masuk Permohonan Pembayaran</div>';
							}
							
							if($person->paraf == null && $person->paraf_next == null){
								$bold = 'style="cursor:pointer;"';
								$text_status = '<div>Surat Masuk Permohonan Pembayaran</div>';
							}
							
							if($person->paraf == '0' && $person->paraf_next == null){
								$bold = 'style="cursor:pointer;"';
								$text_status = '<div><i class="glyphicon glyphicon-eye-open"></i> <span class="label label-default">Ditolak</span></div>';
							}
							
						}else{
							$bold = 'style="cursor:pointer; font-weight: bold;"';
							$text_status = '<div>Surat Masuk Permohonan Pembayaran</div>';
						}
					}
				}
				
				$pesan = $text_status;
				/* if($person->status == 0 && $person->paraf == 0 || $person->status == 2 && $person->paraf == 1){
					$pesan = 'Surat Masuk '. $jenis_surat;
				}else if($person->status == 1 && $person->paraf == 1){						
					$pesan = 'telah menandatangani surat yang anda buat<i class="glyphicon glyphicon-leaf"></i>';
				}else{
					$pesan = 'Tess';
				} */
												
				$id_read = (isset($person->id_hasil_pekerjaan)) ? $person->id_hasil_pekerjaan : $person->id_pembayaran;
				$keterangan = (isset($person->id_hasil_pekerjaan)) ? 0 : 1; //jika pembayaran maka 1, jika hasil pekerjaan maka 0
				
				if($person->viewed == null){
					$warna = 'background-color: #f8f8f8;';
				}else{
					$warna = '';
				}
				
				$no++;
				$result .= '
					<li id="li_'.$person->id_inbox.'" style="font-size: 10pt; '.$warna.'">
						<a href="javascript:void(0)" onclick="read_view('.$person->id_inbox.', '.$id_read.', '.$keterangan.','.$person->id_inbox.')">
							<div>
								<span class="pull-right text-muted" style="font-size: 8pt;">
									<em>'.$person->cdd.'</em>
								</span>
								<strong>'.$person->nama_mailfrom.'</strong>
							</div>
							<div style="font-size: 8.5pt;">
							'.$pesan.'
							</div>
						</a>
					</li>
					<li class="divider"></li>
				';
				
				$data_end_date = $person->cdd;
				// if($no == $total){
					// $result .= '
						// <li class="divider"></li>		
					// ';
				// }
			}
			
			/* 
			$result .= '
				<li>
					<a class="text-center" href="<?php echo base_url(); ?>assets/themes/sb-admin/#">
						<strong>Read All Messages</strong>
						<i class="fa fa-angle-right"></i>
					</a>
				</li>
			'; */
			
		}
		if($total == 0){
			$result .= '
				<li><a><i class="glyphicon glyphicon-ban-circle"></i> Tidak ada Pemberitahuan</a></li>
			';
		}
		$content['data_end_date'] = $data_end_date;
		$content['status'] = $total;
		$content['data'] = $result;
		
		echo json_encode($content);	
	}
	
	public function getnotifikasi_new(){
		$content['content'] = $this->load->view('penyedia/tester',@$content);
	}
	
}
