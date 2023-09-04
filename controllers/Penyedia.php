<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Penyedia extends CI_Controller {
	
	/*
	 * @author     		RENDY
	 * @author_email    rendyardian199@gmail.com
	 *  KOTA TANGERANG 
	 */

	public function __construct()
	{
		parent::__construct();
		$this->load->model('penyedia_model', 'penyedia');		
		$this->load->model('user_model', 'user');		
		$this->db_ba = $this->load->database('db_pembayaran_ls', TRUE);
		if($this->session->userdata('login')==false){
			redirect(base_url('site/login'));
		}else{
			if($this->session->userdata('id_akses') == 1 || $this->session->userdata('id_akses') == 3 ||  $this->session->userdata('id_akses') == 4 ||  $this->session->userdata('id_akses') == 8 ||  $this->session->userdata('id_akses') == 10 ){
				
			}else{
				redirect('ba/inbox');
			}
		}
	}
	
	public function index()
	{
		$content['class_name'] = get_class($this);	
		$content['content'] = $this->load->view('penyedia/index',@$content,true);
		$this->load->view('ba/index', @$content,false,false);
	}
	
	public function ajax_list()
	{
		$this->load->helper('url');

		$list = $this->penyedia->get_datatables();
		// var_dump($list);exit;
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $person) {
			$no++;
			$row = array();			
			
			$data_penyedia = '<font style="font-weight: bold;">' . $person->nama_penyedia . '</font><br>'.
							 '<font style="font-size:8pt;">'.$person->alamat.'</font><br>'.
							 '<font style="font-size:8pt;">NPWP: '.$person->npwp.'</font><br>'.
							 '<font style="font-size:8pt;">BANK: '.$person->bank.' '.$person->no_rekening_penyedia.' a.n '.$person->atas_nama_rekening.'</font><br>
							 <font style="font-size:8pt;">BANK CABANG: '.$person->cabang_bank.' a.n '.$person->atas_nama_rekening.'</font>'
			;
			
			if($this->session->userdata('id_akses') == 1 || $this->session->userdata('id_akses') == 3 ||  $this->session->userdata('id_akses') == 4 ||  $this->session->userdata('id_akses') == 8){			
				$row[] = '
						<div class="btn-group">
								<button type="button" class="btn btn-primary dropdown-toggle btn-xs" data-toggle="dropdown"><i class="fa fa-gears fa-fw"></i> Aksi <span class="caret"></span></button>
								<ul class="dropdown-menu" role="menu">
								  <li><a href="javascript:void(0)" onclick="edit_penyedia('.$person->id_penyedia.')"><i class="glyphicon glyphicon-edit"></i> Edit </a></li>
								  <li><a href="javascript:void(0)" onclick="delete_penyedia('.$person->id_penyedia.')"><i class="glyphicon glyphicon-trash"></i> Hapus </a></li>
								</ul>
							  </div>';
			}else{	
				$row[] = '';
			}
			
			$row[] = $data_penyedia;
			$row[] = $person->nama_kategori;
			//add html for action
		
			$data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->penyedia->count_filtered(),
						"recordsFiltered" => $this->penyedia->count_filtered(),
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
		
		//ini apabila mau menampilkan data untuk diedit
		if(!empty($id_penyedia)){
			$data_penyedia = $this->penyedia->get_by_id($id_penyedia);
			$data_email = $this->user->get_by_id_penyedia($id_penyedia);
			$data_penyedia->email = $data_email->email;
			$content['data_penyedia'] = $data_penyedia;
			
		}else{
			if($this->session->userdata('id_akses') == 1 || $this->session->userdata('id_akses') == 3 ||  $this->session->userdata('id_akses') == 4 ||  $this->session->userdata('id_akses') == 8){
				
			}else{
				redirect('ba/penyedia');
			}
		}
		
		
		
		$content['content'] = $this->load->view('penyedia/form',@$content);
	}
	
	public function ajax_save()
	{
		$this->_validate();
		$post = $this->input->post();
		$data = $post;
		unset($data['id'], $data['email']); //hapus
		$id = (!empty($post['id'])) ? $post['id'] : '';
		
		if(!empty($id)){
			$created = array(
				'mdb'=> $this->session->userdata('id_pegawai'),
				'mdi' => 'web'
			);
		}else{
			$created = array(
				'cdb'=> $this->session->userdata('id_pegawai'),
				'kode_unor'=> substr($this->session->userdata('kode_unor'),0,5),
				'cdi' => 'web'
			);
		}
		
		// action to save surat
		$result = $this->penyedia->save($id, $data, $created);
		
		if($result){
			
			$result_user = $this->user->get_by_id_penyedia($result, 'id, username, password,email');
			
			
			// ganiprayoga11@gmail.com
			if(!$result_user){ //penyedia belum punya user
				$result_user = $this->user->get_by_email($post['email'], 'id, password, email');
				
				if(!$result_user){
					$password = $this->rand_code(10);
					$data_email = array(
						'id_penyedia' => $result,
						'email' => $post['email'],
						'password' => sha1($password),
						'id_akses' => 10,
					);
					$save_email = $this->user->save(false,$data_email,array('cdb'=> $this->session->userdata('id_pegawai'),'cdi' => 'web'));
					
					if($save_email){
						$data_surat = '
							<table width="100%">
								'.@$post['nama_perusahaan'].'
								<tr>
									<td>Nama Penyedia/Penanggung Jawab</td>
									<td>:</td>
									<td>'.$post['nama_penyedia'].'</td>
								</tr>
								<tr>
									<td>Alamat</td>
									<td>:</td>
									<td>'.$post['alamat'].'</td>
								</tr>
								<tr>
									<td>NPWP</td>
									<td>:</td>
									<td>'.$post['npwp'].'</td>
								</tr>
								<tr>
									<td>Akun</td>
									<td>:</td>
									<td>'.$post['email'].'</td>
								</tr>
								<tr>
									<td>Password</td>
									<td>:</td>
									<td>'.$password.'</td>
								</tr>
							</table>
						';
						$isi_surat = 'Terima kasih telah melakukan registrasi akun pada [E-Kontrak] https://e-kontrak.tangerangkota.go.id, Silahkan Login Dengan Akun Username & Password tersebut';
						
						$cekmail['email'] = $post['email'];						
						$result_email = sendtoEmail((object) $cekmail, false, '[E-Kontrak] Registrasi Akun Penyedia Sukses', $data_surat, $isi_surat);
					}
					
				}
			}else{ //jika penyedia punya user, maka update

				
				if(empty($result_user->email)){
					$password = $this->rand_code(10);
					$data = array(
						'id_penyedia' => $result,
						'email' => $post['email'],
						'password' => sha1($password),
						'id_akses' => 10,
					);
					$save_email = $this->user->update(array('id'=> $result_user->id), $data);
					
					if($save_email){
						$data_surat = '
							<table width="100%">
								'.@$post['nama_perusahaan'].'
								<tr>
									<td>Nama Penyedia/Penanggung Jawab</td>
									<td>:</td>
									<td>'.$post['nama_penyedia'].'</td>
								</tr>
								<tr>
									<td>Alamat</td>
									<td>:</td>
									<td>'.$post['alamat'].'</td>
								</tr>
								<tr>
									<td>NPWP</td>
									<td>:</td>
									<td>'.$post['npwp'].'</td>
								</tr>
								<tr>
									<td>Akun</td>
									<td>:</td>
									<td>'.$post['email'].'</td>
								</tr>
								<tr>
									<td>Password</td>
									<td>:</td>
									<td>'.$password.'</td>
								</tr>
							</table>
						';
						$isi_surat = 'Terima kasih telah melakukan registrasi akun pada [E-Kontrak] https://e-kontrak.tangerangkota.go.id, Silahkan Login Dengan Akun Username & Password tersebut';
						
						$cekmail['email'] = $post['email'];						
						$result_email = sendtoEmail((object) $cekmail, false, '[E-Kontrak] Registrasi Akun Penyedia Sukses', $data_surat, $isi_surat);
					}
					
				}
			}
			
			$content['status'] = true;
			$content['notif'] = '<div class="alert alert-success" role="alert"><i class="fa fa-thumbs-o-up fa-fw"></i> Data berhasil disimpan</div>';			

		}else{
		  $content['status'] = false;
		  $content['notif'] = '<div class="alert alert-danger" role="alert"><i class="fa fa-thumbs-o-down fa-fw"></i> Data gagal tersimpan!</div>';
		}
		
		echo json_encode($content);
	}
	
	function rand_code($len)
	{
		 $min_lenght= 0;
		 $max_lenght = 100;
		 $bigL = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		 $smallL = "abcdefghijklmnopqrstuvwxyz";
		 $number = "0123456789";
		 $bigB = str_shuffle($bigL);
		 $smallS = str_shuffle($smallL);
		 $numberS = str_shuffle($number);
		 $subA = substr($bigB,0,5);
		 $subB = substr($bigB,6,5);
		 $subC = substr($bigB,10,5);
		 $subD = substr($smallS,0,5);
		 $subE = substr($smallS,6,5);
		 $subF = substr($smallS,10,5);
		 $subG = substr($numberS,0,5);
		 $subH = substr($numberS,6,5);
		 $subI = substr($numberS,10,5);
		 $RandCode1 = str_shuffle($subA.$subD.$subB.$subF.$subC.$subE);
		 $RandCode2 = str_shuffle($RandCode1);
		 $RandCode = $RandCode1.$RandCode2;
		 if ($len>$min_lenght && $len<$max_lenght)
		 {
			$CodeEX = substr($RandCode,0,$len);
		 }
		 else
		 {
			$CodeEX = $RandCode;
		 }
		 return $CodeEX;
	}
	
	private function _validate()
	{
		$this->load->library('form_validation');
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if($this->input->post('nama_penyedia') == '')
		{
			$data['inputerror'][] = 'nama_penyedia';
			$data['error_string'][] = 'Nama Penyedia harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('alamat') == '')
		{
			$data['inputerror'][] = 'alamat';
			$data['error_string'][] = 'Alamat Lengkap harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('npwp') == '')
		{
			$data['inputerror'][] = 'npwp';
			$data['error_string'][] = 'NPWP harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('bank') == '')
		{
			$data['inputerror'][] = 'bank';
			$data['error_string'][] = 'BANK harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('no_rekening_penyedia') == '')
		{
			$data['inputerror'][] = 'no_rekening_penyedia';
			$data['error_string'][] = 'No Rekening Penyedia harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('atas_nama_rekening') == '')
		{
			$data['inputerror'][] = 'atas_nama_rekening';
			$data['error_string'][] = 'Atas Nama Rekening harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('cabang_bank') == '')
		{
			$data['inputerror'][] = 'cabang_bank';
			$data['error_string'][] = 'Cabang BANK harus diisi';
			$data['status'] = FALSE;
		}
		
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		if(!empty($_POST['email'])){
			if ($this->form_validation->run() == FALSE)
			{
				$data['inputerror'][] = 'email';
				$data['error_string'][] = 'Email harus diisi dan Valid';
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
		
		if($this->session->userdata('id_akses') == 1 || $this->session->userdata('id_akses') == 3 ||  $this->session->userdata('id_akses') == 4 ||  $this->session->userdata('id_akses') == 8){			
			$hasil = $this->penyedia->update(array('id_penyedia' => $id), $data);
			$this->user->delete_by_id($id);
		}else{
			$hasil = 0;
		}
		
		if($hasil > 0){
			$content['status'] = true;
			$content['notif'] = '<div class="alert alert-success" role="alert"><i class="fa fa-thumbs-o-up fa-fw"></i> Data berhasil di hapus</div>';			

		}else{
		  $content['status'] = false;
		  $content['notif'] = '<div class="alert-modal alert-danger" role="alert"><i class="fa fa-thumbs-o-down fa-fw"></i> Data gagal dihapus !</div>';
		}
		
		
		echo json_encode($content);
	}
	
	
}
