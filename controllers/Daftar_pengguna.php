<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Daftar_pengguna extends CI_Controller {
	
	/*
	 * @author     		RENDY
	 * @author_email    rendyardian199@gmail.com
	 *  KOTA TANGERANG 
	 */

	public function __construct()
	{
		parent::__construct();
		$this->load->model('User_model', 'user');	
		$this->db_ba = $this->load->database('db_pembayaran_ls', TRUE);
		if($this->session->userdata('login')==false){
			redirect(base_url('site/login'));
		}
	}
	
	public function index()
	{
		$content['class_name'] = get_class($this);	
		$content['content'] = $this->load->view('daftar_pengguna/index',@$content,true);
		$this->load->view('ba/index', @$content,false,false);
	}
	
	public function ajax_list()
	{
		$this->load->helper('url');

		$list = $this->user->get_datatables();
		
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $person) {
			$no++;
			$row = array();			
			$tomobol_edit = (!empty($person->nip_baru)) ? "edit_sso('".$person->id."')": "edit_no_sso('".$person->id."')";
			$row[] = '<div class="btn-group">
								<button type="button" class="btn btn-primary dropdown-toggle btn-xs" data-toggle="dropdown"><i class="fa fa-gears fa-fw"></i> Aksi <span class="caret"></span></button>
								
								<ul class="dropdown-menu" role="menu">
								  <li><a href="javascript:void(0)" onclick="'.$tomobol_edit.'"><i class="glyphicon glyphicon-pencil"></i> Edit </a></li>
								  <li><a href="javascript:void(0)" onclick="delete_pengguna('.$person->id.')"><i class="glyphicon glyphicon-trash"></i> Hapus </a></li>
								</ul>
							  </div>';
			$row[] = @$person->nip_baru;
			$row[] = @$person->nama_user;
			$row[] = @$person->nama_akses;
			$row[] = @$person->status;
			//add html for action
		
			$data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->user->count_filtered(),
						"recordsFiltered" => $this->user->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}
	
	
	public function ajax_form()
	{		
		$content['class_name'] = get_class($this);	
		
		$post = $this->input->post();
		$id_user = (!empty($post['id_spk'])) ? $post['id_spk'] : '';
		
		//ini apabila mau menampilkan data untuk diedit
		if(!empty($id_user)){
			
		}else{
			$dinas = getSkpd();
			
			$content['dinas'] = formselect_skpd($dinas, 'kode_unor', 'nama_unor');
			$content['hak_akses'] = formselect('m_akses','id_akses','nama_akses', 'WHERE id_akses = "1" OR id_akses = "8"');
		}		
		
		$content['content'] = $this->load->view('daftar_pengguna/form',@$content);
	}
	
	public function ajax_form_sso()
	{		
		$content['class_name'] = get_class($this);	
		
		$post = $this->input->post();
		$id_user = (!empty($post['id_user'])) ? $post['id_user'] : '';
		
		//ini apabila mau menampilkan data untuk diedit
		if(!empty($id_user)){
			$data_user = $this->user->get_by_id($id_user);
			$data = cek_nip($data_user->nip_baru);
			if($data){
				$datas = json_decode($data, true);
				$data_user->nama_pegawai = $datas['nama_pegawai'];
				$data_user->kode_unor = $datas['kode_unor'];
			}
			
			$content['data_user'] = $data_user;
			$content['hak_akses'] = formselect('m_akses','id_akses','nama_akses', 'WHERE id_akses != "1" AND id_akses != "8"', $data_user->id_akses);
		}else{
			$content['hak_akses'] = formselect('m_akses','id_akses','nama_akses', 'WHERE id_akses != "1" AND id_akses != "8"');
		}		
		
		$content['content'] = $this->load->view('daftar_pengguna/form_sso',@$content);
	}
	
	public function ajax_form_sso_dinas()
	{		
		$content['class_name'] = get_class($this);	
		
		$post = $this->input->post();
		$id_user = (!empty($post['id_spk'])) ? $post['id_spk'] : '';
		
		//ini apabila mau menampilkan data untuk diedit
		if(!empty($id_user)){
			
		}else{
			$data_pegawai = get_pegawai(substr($this->session->userdata('kode_unor'), 0, 5));
			
			$content['dinas'] = getformselect_servicev2($data_pegawai, 'id_pegawai', 'id_pegawai', 'nama_pegawai');
			$content['hak_akses'] = formselect('m_akses','id_akses','nama_akses', 'WHERE id_akses != "1" AND id_akses != "8"');
		}		
		
		$content['content'] = $this->load->view('daftar_pengguna/form_sso_dinas',@$content);
	}
	
	
	
	public function ajax_save()
	{
		$this->_validate();
		$post = $this->input->post();
				
		$data = $post;
		unset($data['id'], $data['unor'], $data['nip']); //hapus
		$id = (!empty($post['id'])) ? $post['id'] : '';
		
		if(!empty($post['password'])){
			$data['password'] = sha1($post['password']);
		}

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
		//cek data
		$cekganda = $this->user->get_by_id_pegawai($post['id_pegawai']);
		// if(!empty($cekganda)){
			// $content['status'] = false;
			// $content['notif'] = '<div class="alert alert-danger" role="alert"><i class="fa fa-thumbs-o-down fa-fw"></i> Data gagal tersimpan, Data Sudah Ada!</div>';
			// echo json_encode($content);
			// exit;
		// }
		
		// action to save surat
		$result = $this->user->save($id, $data, $created);
		
		if($result){			
			$content['status'] = true;
			$content['notif'] = '<div class="alert alert-success" role="alert"><i class="fa fa-thumbs-o-up fa-fw"></i> Data berhasil disimpan</div>';			

		}else{
		  $content['status'] = false;
		  $content['notif'] = '<div class="alert alert-danger" role="alert"><i class="fa fa-thumbs-o-down fa-fw"></i> Data gagal tersimpan!</div>';
		}
		
		echo json_encode($content);
	}
	
	
	public function ajax_save_sso_dinas()
	{
		$this->_validate();
		$post = $this->input->post();
				
		$data = $post;
		unset($data['id'], $data['unor'], $data['nip']); //hapus
		$id = (!empty($post['id'])) ? $post['id'] : '';
		
		if(!empty($post['id_pegawai'])){
			$id_pegawai_json = json_decode($post['id_pegawai'], true);
			$data['kode_unor'] = $id_pegawai_json[0]['kode_unor'];
			$data['id_pegawai'] = $id_pegawai_json[0]['id_pegawai'];
			$data['nip_baru'] = $id_pegawai_json[0]['nip_baru'];
			$data['nama_user'] = $id_pegawai_json[0]['nama_pegawai'];
			// nipnya gak ada nih
		}
		
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
		
		// action to save surat
		$result = $this->user->save($id, $data, $created);
		
		if($result){			
			$content['status'] = true;
			$content['notif'] = '<div class="alert alert-success" role="alert"><i class="fa fa-thumbs-o-up fa-fw"></i> Data berhasil disimpan</div>';			

		}else{
		  $content['status'] = false;
		  $content['notif'] = '<div class="alert alert-danger" role="alert"><i class="fa fa-thumbs-o-down fa-fw"></i> Data gagal tersimpan!</div>';
		}
		
		echo json_encode($content);
	}
	
	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		$nip = isset($_POST['nip']) ? true : false;
		if($nip){
			if($_POST['nip_baru']){
				$nip = false;
			}
		}
		
		if($nip)
		{
			if($this->input->post('nip') == '')
			{
				$data['inputerror'][] = 'nip';
				$data['error_string'][] = 'Nip harus diisi';
				$data['status'] = FALSE;
			}
		}
		
		if($this->input->post('kode_unor'))
		{
			if($this->input->post('kode_unor') == '')
			{
				$data['inputerror'][] = 'kode_unor';
				$data['error_string'][] = 'Kode Unor harus diisi';
				$data['status'] = FALSE;
			}
		}
		
		if($this->input->post('nama_user'))
		{
			if($this->input->post('nama_user') == '')
			{
				$data['inputerror'][] = 'nama_user';
				$data['error_string'][] = 'Nama Pegawai harus diisi';
				$data['status'] = FALSE;
			}
		}
		
		if($this->input->post('unor'))
		{
			if($this->input->post('unor') == '')
			{
				$data['inputerror'][] = 'unor';
				$data['error_string'][] = 'Nama Unor harus diisi';
				$data['status'] = FALSE;
			}
		}
		
		if($this->input->post('username')){		
			if($this->input->post('username') == '')
			{
				$data['inputerror'][] = 'username';
				$data['error_string'][] = 'Username harus diisi';
				$data['status'] = FALSE;
			}
			
		}
		
		if($this->input->post('password'))
		{
			if($this->input->post('password') == '')
			{
				$data['inputerror'][] = 'password';
				$data['error_string'][] = 'Password harus diisi';
				$data['status'] = FALSE;
			}
		}
		
		if($this->input->post('status') == '')
		{
			$data['inputerror'][] = 'status';
			$data['error_string'][] = 'Status harus diisi';
			$data['status'] = FALSE;
		}
		
		if($this->input->post('id_akses') == '')
		{
			$data['inputerror'][] = 'id_akses';
			$data['error_string'][] = 'Hak Akses harus diisi';
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
		
		$hasil = $this->user->update(array('id' => $id), $data);
		
		if($hasil > 0){
			$content['status'] = true;
			$content['notif'] = '<div class="alert alert-success" role="alert"><i class="fa fa-thumbs-o-up fa-fw"></i> Data berhasil di hapus</div>';			

		}else{
		  $content['status'] = false;
		  $content['notif'] = '<div class="alert-modal alert-danger" role="alert"><i class="fa fa-thumbs-o-down fa-fw"></i> Data gagal dihapus !</div>';
		}
		
		
		echo json_encode($content);
	}	
	
	function cek_nip(){
		
		$nip = $_POST['nip'];
		
		$data = cek_nip($nip);
		echo $data;		
	
	}
}
