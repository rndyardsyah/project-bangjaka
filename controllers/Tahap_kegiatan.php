<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tahap_kegiatan extends CI_Controller {
	
	/*
	 * @author     		RENDY
	 * @author_email    rendyardian199@gmail.com
	 *  KOTA TANGERANG 
	 */

	public function __construct()
	{
		parent::__construct();
		$this->load->model('tahap_kegiatan_model', 'tahap_kegiatan');		
		$this->db_ba = $this->load->database('db_pembayaran_ls', TRUE);
		if($this->session->userdata('login')==false){
			redirect(base_url('site/login'));
		}
	}
	
	public function index()
	{
		$content['class_name'] = get_class($this);	
		$content['content'] = $this->load->view('tahap_kegiatan/index',@$content,true);
		$this->load->view('ba/index', @$content,false,false);
	}
	
	public function ajax_list()
	{
		$this->load->helper('url');

		$list = $this->tahap_kegiatan->get_datatables();
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
								  <li><a href="javascript:void(0)" onclick="edit_penyedia('.$person->id_tahap.')"><i class="glyphicon glyphicon-edit"></i> Edit </a></li>
								  <li><a href="javascript:void(0)" onclick="delete_penyedia('.$person->id_tahap.')"><i class="glyphicon glyphicon-trash"></i> Hapus </a></li>
								</ul>
							  </div>';
							  
			$row[] = $person->nama_tahap;
			$row[] = GetFullDateFull($person->start);
			$row[] = GetFullDateFull($person->finish);
			//add html for action
		
			$data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->tahap_kegiatan->count_filtered(),
						"recordsFiltered" => $this->tahap_kegiatan->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}
	
	public function ajax_form()
	{		
		$content['class_name'] = get_class($this);	
		
		$post = $this->input->post();
		$id_tahap = (!empty($post['id_tahap'])) ? $post['id_tahap'] : '';
		
		//ini apabila mau menampilkan data untuk diedit
		if(!empty($id_tahap)){
			$data_penyedia = $this->tahap_kegiatan->get_by_id($id_tahap);
			$content['data_penyedia'] = $data_penyedia;
		}
		
		
		
		$content['content'] = $this->load->view('tahap_kegiatan/form',@$content);
	}
	
	public function ajax_save()
	{
		$this->_validate();
		$post = $this->input->post();
		$data = $post;
		unset($data['id'], $data['tgl_aktif'], $data['tgl_finish']); //hapus
		$id = (!empty($post['id'])) ? $post['id'] : '';
		if($post['tgl_aktif'] > $post['tgl_finish']){
			$content['status'] = false;
			$content['notif'] = '<div class="alert alert-danger" role="alert"><i class="fa fa-thumbs-o-down fa-fw"></i> Tanggal Aktif Lebih Besar dari Tanggal Selesai!</div>';
			echo json_encode($content);
			exit;
		}
		
		//cek tahap_kegiatan
		$cek_tahap = $this->tahap_kegiatan->get_where(array('tahap_kegiatan'=> $post['tahap_kegiatan']), '*');
		if($cek_tahap){
			$content['status'] = false;
			$content['notif'] = '<div class="alert alert-danger" role="alert"><i class="fa fa-thumbs-o-down fa-fw"></i> Tahap Kegiatan Sudah Ada, Tidak Boleh Ganda!</div>';
			echo json_encode($content);
			exit;
		}
		
		$data['start'] = date('Y-m-d', strtotime($post['tgl_aktif']));
		$data['finish'] =  date('Y-m-d', strtotime($post['tgl_finish']));
		
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
		$result = $this->tahap_kegiatan->save($id, $data, $created);
		
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

		if($this->input->post('tahap_kegiatan') == '')
		{
			$data['inputerror'][] = 'tahap_kegiatan';
			$data['error_string'][] = 'Tahap Kegiatan harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('nama_tahap') == '')
		{
			$data['inputerror'][] = 'nama_tahap';
			$data['error_string'][] = 'Nama Tahap harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('tgl_aktif') == '')
		{
			$data['inputerror'][] = 'tgl_aktif';
			$data['error_string'][] = 'Tanggal Aktif harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('tgl_finish') == '')
		{
			$data['inputerror'][] = 'tgl_finish';
			$data['error_string'][] = 'Tanggal Finish harus diisi';
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
		
		$hasil = $this->tahap_kegiatan->delete_by_id($id);
		$content['status'] = true;
		$content['notif'] = '<div class="alert alert-success" role="alert"><i class="fa fa-thumbs-o-up fa-fw"></i> Data berhasil di hapus</div>';			

		
		
		echo json_encode($content);
	}
	
	
}
