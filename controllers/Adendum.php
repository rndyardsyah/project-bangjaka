<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Adendum extends CI_Controller {
	
	/*
	 * @author     		RENDY
	 * @author_email    rendyardian199@gmail.com
	 *  KOTA TANGERANG 
	 */

	public function __construct()
	{
		parent::__construct();
		$this->load->model('adendum_model', 'adendum');		
		$this->db_ba = $this->load->database('db_pembayaran_ls', TRUE);
		if($this->session->userdata('login')==false){
			redirect(base_url('site/login'));
		}
	}
	
	public function index()
	{
		$content['class_name'] = get_class($this);	
		$post = $this->input->post();		
		$id_pembayaran = (!empty($post['id_pembayaran'])) ? $post['id_pembayaran'] : '';	
		$content['id_pembayaran'] = $id_pembayaran;
		$content['content'] = $this->load->view('adendum/index',@$content);
	}
	
	public function ajax_edit($id)
	{
		$data = $this->adendum->get_by_id($id, 'id_adendum, no_adendum, tgl_adendum, biaya_adendum, waktu_pelaksanaan_adendum');
		$data->biaya_adendum = number_format($data->biaya_adendum, 2);
		echo json_encode($data);
	}
	
	public function ajax_list()
	{
		$this->load->helper('url');

		$list = $this->adendum->get_datatables();
		
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $person) {
			$no++;
			$row = array();			
			
			$row[] = $person->no_adendum;
			$row[] = $person->tgl_adendum;
			$row[] = number_format($person->biaya_adendum, 2);
			$row[] = $person->waktu_pelaksanaan_adendum;
			$row[] = '
				<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="edit_adendum('."'".$person->id_adendum."'".')"><i class="glyphicon glyphicon-pencil"></i> </a>
				<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="delete_adendum('."'".$person->id_adendum."'".')"><i class="glyphicon glyphicon-trash"></i> </a>';
			//add html for action
		
			$data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->adendum->count_filtered(),
						"recordsFiltered" => $this->adendum->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}
	
	public function ajax_form()
	{		
		$content['class_name'] = get_class($this);	
		
		$post = $this->input->post();
		$id_adendum = (!empty($post['id_adendum'])) ? $post['id_adendum'] : '';		
		$id_pembayaran = (!empty($post['id_pembayaran'])) ? $post['id_pembayaran'] : '';		
		$content['id_adendum'] = $id_adendum;
		$content['id_pembayaran'] = $id_pembayaran;
		$content['content'] = $this->load->view('adendum/form',@$content);
	}
	
	public function ajax_save()
	{
		$this->_validate();
		$post = $this->input->post();
		
		$data = $post;
		unset($data['id']); //hapus
		$id = (!empty($post['id'])) ? $post['id'] : '';
		$tgl_adendum = date('Y-m-d', strtotime($post['tgl_adendum']));
		
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
		$data['tgl_adendum'] = $tgl_adendum;
		$data['biaya_adendum'] = (!empty($post['biaya_adendum'])) ? str_replace(',','',$post['biaya_adendum']) : '';
		// action to save surat
		$result = $this->adendum->save($id, $data, $created);
		
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

		if($this->input->post('no_adendum') == '')
		{
			$data['inputerror'][] = 'no_adendum';
			$data['error_string'][] = 'Nomor Adendum harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('tgl_adendum') == '')
		{
			$data['inputerror'][] = 'tgl_adendum';
			$data['error_string'][] = 'Tanggal Adendum harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('biaya_adendum') == '')
		{
			$data['inputerror'][] = 'biaya_adendum';
			$data['error_string'][] = 'Biaya Adendum harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('waktu_pelaksanaan_adendum') == '')
		{
			$data['inputerror'][] = 'waktu_pelaksanaan_adendum';
			$data['error_string'][] = 'Waktu Pelaksanaan harus diisi';
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
		
		$hasil = $this->adendum->update(array('id_adendum' => $id), $data);
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
