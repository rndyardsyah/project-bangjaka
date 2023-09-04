<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kegiatan extends CI_Controller {
	
	/*
	 * @author     		RENDY
	 * @author_email    rendyardian199@gmail.com
	 *  KOTA TANGERANG 
	 */

	public function __construct()
	{
		parent::__construct();
		$this->load->model('kegiatan_model', 'kegiatan');		
		$this->db_ba = $this->load->database('db_pembayaran_ls', TRUE);
		
		if($this->session->userdata('login')==false){
			redirect(base_url('site/login'));
		}
	}
	
	public function index()
	{
		$content['class_name'] = get_class($this);	
		$content['content'] = $this->load->view('kegiatan/index',@$content,true);
		$this->load->view('ba/index', @$content,false,false);
	}
	
	public function ajax_list()
	{
		$this->load->helper('url');

		$list = $this->kegiatan->get_datatables();
		// var_dump($list);exit;
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $person) {
			$no++;
			$row = array();			
			
			$row[] = '
						<div class="btn-group">
								<button type="button" class="btn btn-primary dropdown-toggle btn-xs" data-toggle="dropdown"><i class="fa fa-gears fa-fw"></i> Aksi <span class="caret"></span></button>
								<ul class="dropdown-menu" role="menu">
								  <li><a href="javascript:void(0)" onclick="edit_kegiatan('.$person->id_kegiatan.')"><i class="glyphicon glyphicon-edit"></i> Edit </a></li>
								  <li><a href="javascript:void(0)" onclick="delete_kegiatan('.$person->id_kegiatan.')"><i class="glyphicon glyphicon-trash"></i> Hapus </a></li>
								</ul>
							  </div>';
			$row[] = $person->nama_kegiatan;
			$row[] = $person->kode_unor;
			//add html for action
		
			$data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->kegiatan->count_all(),
						"recordsFiltered" => $this->kegiatan->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}
	
	public function ajax_form()
	{		
		$content['class_name'] = get_class($this);	
		
		$post = $this->input->post();
		$id_kegiatan = (!empty($post['id_kegiatan'])) ? $post['id_kegiatan'] : '';
		
		//ini apabila mau menampilkan data untuk diedit
		if(!empty($id_kegiatan)){
			$data_penyedia = $this->kegiatan->get_by_id($id_kegiatan);
			$content['data_penyedia'] = $data_penyedia;
		}
		
		
		
		$content['content'] = $this->load->view('kegiatan/form',@$content);
	}
	
	public function ajax_save()
	{
		$this->_validate();
		$post = $this->input->post();
		$data = $post;
		unset($data['id']); //hapus
		$id = (!empty($post['id'])) ? $post['id'] : '';
		
		if(!empty($id)){
			$created = array(
				'mdb'=> '',
				'mdi' => 'web'
			);
		}else{
			$created = array(
				'cdb'=> '',
				'kode_unor'=> '',
				'cdi' => 'web'
			);
		}
		
		// action to save surat
		$result = $this->kegiatan->save($id, $data, $created);
		
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

		if($this->input->post('nama_kegiatan') == '')
		{
			$data['inputerror'][] = 'nama_kegiatan';
			$data['error_string'][] = 'Nama Kegiatan harus diisi';
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
		
		$hasil = $this->kegiatan->update(array('id_kegiatan' => $id), $data);
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
