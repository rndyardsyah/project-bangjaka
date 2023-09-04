<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pemberkasan extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Berkas_model', 'berkas');
		$this->load->model('Pemberkasan_model', 'pemberkasan');
		$this->db_ba = $this->load->database('db_pembayaran_ls', TRUE);
		if($this->session->userdata('login')==false){
			redirect(base_url('site/login'));
		}
	}

	public function index()
	{
		$this->load->view('pemberkasan/index', @$content);
	}
	
	public function ajax_form(){
		
		$post = $this->input->post();
		$content['class_name'] = get_class($this);			
		$content['id_spk'] = $post['id_spk'];
		$this->load->view('pemberkasan/form',@$content);
	}
	
	public function ajax_save(){
		
		$this->_validate();
		
		$post = $this->input->post();
		
		$id_history_berkas = (!empty($post['id'])) ? stripslashes(preg_replace("/[^A-Za-z0-9.]/", "" ,$post['id'])) : '';
		$id_spk = (!empty($post['id_spk'])) ? stripslashes(preg_replace("/[^A-Za-z0-9.]/", "" ,$post['id_spk'])) : '';
		$id_berkas = (!empty($post['id_berkas'])) ? stripslashes(preg_replace("/[^A-Za-z0-9.]/", "" ,$post['id_berkas'])) : '';
		$result_nama_berkas = $this->berkas->get_by_id($id_berkas);
		$nama_berkas = (!empty($result_nama_berkas)) ? $result_nama_berkas->nama_berkas : '';
		$keterangan = (!empty(stripslashes(preg_replace("/[^A-Za-z0-9.]/", "" ,$post['keterangan'])))) ?  $post['keterangan'] : '';		
		
		if(!empty($_FILES['file']['name']))
		{
			$upload = $this->_do_upload('file');
			$data['file'] = $upload;
		}
		
		if(!empty($this->session->userdata('id_penyedia'))){
			$created = array(
				'cdb_penyedia'=> $this->session->userdata('id_penyedia'),
				'cdi' => 'web'
			);

		}else{
			$created = array(
				'cdb'=> $this->session->userdata('id_pegawai'),
				'cdi' => 'web'
			);
		}
		
		$data['id_berkas'] = $id_berkas;
		$data['keterangan'] = $keterangan;
		$data['nama_berkas'] = $nama_berkas;
		$data['id_spk'] = $id_spk;
		
		// action to save surat
		$result = $this->pemberkasan->save($id_history_berkas, $data, $created);
		
		if($result){			
			$content['status'] = true;
			$content['notif'] = '<div class="alert alert-success" role="alert"><i class="fa fa-thumbs-o-up fa-fw"></i> Data berhasil disimpan</div>';		
		}else{
			$content['status'] = false;
			$content['notif'] = '<div class="alert alert-danger" role="alert"><i class="fa fa-thumbs-o-down fa-fw"></i> Data gagal tersimpan!</div>';
		}
		
		echo json_encode($content);
	}
	
	private function _do_upload($nameFile='')
	{
		$config['upload_path']          = 'assets/file/berkas';
        $config['allowed_types']        = 'pdf';
        $config['max_size']             = 10000; //set max size allowed in Kilobyte
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
	
	public function ajax_list()
	{
		$list = $this->pemberkasan->get_datatables();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $person) {
			$no++;
			$row = array();
			//<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="edit_berkas('."'".$person->id_history_berkas."'".')"><i class="glyphicon glyphicon-pencil"></i></a>
			$row[] = '
				  <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="delete_berkas('."'".$person->id_history_berkas."'".')"><i class="glyphicon glyphicon-trash"></i></a>';
			$row[] = $person->nama_berkas;
			$row[] = $person->keterangan;
			$file = (!empty($person->file)) ?  '<a href="'.base_url().'assets/file/berkas/'.$person->file.'" target="_blank"><i class="fa fa-link" aria-hidden="true"></i> file</a>' : '';
			$row[] = $file;
			//add html for action
		
			$data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->pemberkasan->count_all(),
						"recordsFiltered" => $this->pemberkasan->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}

	public function ajax_edit($id)
	{
		$data = $this->berkas->get_by_id($id);
		$data->dob = ($data->dob == '0000-00-00') ? '' : $data->dob; // if 0000-00-00 set tu empty for datepicker compatibility
		echo json_encode($data);
	}

	public function ajax_add()
	{
		$this->_validate();
		
		$data = array(
				'firstName' => $this->input->post('firstName'),
				'lastName' => $this->input->post('lastName'),
				'gender' => $this->input->post('gender'),
				'address' => $this->input->post('address'),
				'dob' => $this->input->post('dob'),
			);

		$insert = $this->berkas->save($data);

		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update()
	{
		$this->_validate();
		$data = array(
				'firstName' => $this->input->post('firstName'),
				'lastName' => $this->input->post('lastName'),
				'gender' => $this->input->post('gender'),
				'address' => $this->input->post('address'),
				'dob' => $this->input->post('dob'),
			);
		$this->berkas->update(array('id' => $this->input->post('id')), $data);
		echo json_encode(array("status" => TRUE));
	}

	
	public function ajax_delete()
	{
		$post = $this->input->post();
		$id = (!empty($post['id'])) ? $post['id'] : '';
		
		//delete file	
		$data = array("active" => 0);
		
		$hasil = $this->pemberkasan->update(array('id_history_berkas' => $id), $data);
		if($hasil > 0){
			$content['status'] = true;
			$content['notif'] = '<div class="alert alert-success" role="alert"><i class="fa fa-thumbs-o-up fa-fw"></i> Data berhasil di hapus</div>';			

		}else{
		  $content['status'] = false;
		  $content['notif'] = '<div class="alert alert-danger" role="alert"><i class="fa fa-thumbs-o-down fa-fw"></i> Data gagal dihapus !</div>';
		}
		
		
		echo json_encode($content);
	}	

	public function ajax_bulk_delete()
	{
		$list_id = $this->input->post('id');
		var_dump($list_id);
		exit;
		foreach ($list_id as $id) {
			$this->berkas->delete_by_id($id);
		}
		echo json_encode(array("status" => TRUE));
	}

	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if($this->input->post('id_berkas') == '')
		{
			$data['inputerror'][] = 'id_berkas';
			$data['error_string'][] = 'Berkas wajib dipilih';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

}
