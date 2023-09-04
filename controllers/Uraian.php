<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Uraian extends CI_Controller {
	
	/*
	 * @author     		RENDY
	 * @author_email    rendyardian199@gmail.com
	 *  KOTA TANGERANG 
	 */

	public function __construct()
	{
		parent::__construct();
		$this->load->model('uraian_model', 'uraian');		
		$this->db_ba = $this->load->database('db_pembayaran_ls', TRUE);
		if($this->session->userdata('login')==false){
			redirect(base_url('site/login'));
		}
	}
	
	public function index()
	{
		$content['class_name'] = get_class($this);	
		$content['id_hasil_pekerjaan'] = @$_POST['id_hasil_pekerjaan'];	
		
		$content['content'] = $this->load->view('uraian/index',@$content);
		/* $content['content'] = $this->load->view('uraian/index',@$content,true);
		$this->load->view('ba/index', @$content,false,false); */
	}
	
	public function ajax_list()
	{
		$this->load->helper('url');

		$list = $this->uraian->get_datatables();
		// var_dump($list);exit;
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $person) {
			$no++;
			$row = array();			
			
			
			$row[] = $person->uraian;
			$row[] = $person->volume;
			$row[] = $person->satuan;
			$row[] = $person->keterangan;
			$row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="edit_uraian('."'".$person->id_uraian_pekerjaan."'".')"><i class="glyphicon glyphicon-pencil"></i> </a>
				  <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="delete_uraian('."'".$person->id_uraian_pekerjaan."'".')"><i class="glyphicon glyphicon-trash"></i> </a>';
			//add html for action
		
			$data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->uraian->count_filtered(),
						"recordsFiltered" => $this->uraian->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}
	
	public function ajax_form()
	{		
		$content['class_name'] = get_class($this);	
		
		$post = $this->input->post();
		/* $id_penyedia = (!empty($post['id_penyedia'])) ? $post['id_penyedia'] : '';
		
		//ini apabila mau menampilkan data untuk diedit
		if(!empty($id_penyedia)){
			$data_penyedia = $this->uraian->get_by_id($id_penyedia);
			$content['data_penyedia'] = $data_penyedia;
		} */
		
		
		
		$content['content'] = $this->load->view('uraian/form',@$content);
	}
	
	public function ajax_save()
	{
		$this->_validate();
		$post = $this->input->post();
		$data = $post;
		unset($data['id']); //hapus
		$id = (!empty($post['id'])) ? $post['id'] : '';
		// $harga_satuan = (!empty($post['harga_satuan'])) ? str_replace(',','',$post['harga_satuan']) : '';
		// $jumlah = (!empty($post['jumlah'])) ? str_replace(',','',$post['jumlah']) : '';
		
		// $data['harga_satuan'] = $harga_satuan;
		// $data['jumlah'] = $jumlah;
		
		if(!empty($id)){
			$created = array(
				'mdb'=> '',
				'mdi' => 'web'
			);
		}else{
			$created = array(
				'cdb'=> '',
				// 'kode_unor'=> substr($this->session->userdata('kode_unor'),0,5),
				'cdi' => 'web'
			);
		}
		
		// action to save surat
		$result = $this->uraian->save($id, $data, $created);
		
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

		if($this->input->post('uraian') == '')
		{
			$data['inputerror'][] = 'uraian';
			$data['error_string'][] = 'Uraian harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('volume') == '')
		{
			$data['inputerror'][] = 'volume';
			$data['error_string'][] = 'Volume harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('satuan') == '')
		{
			$data['inputerror'][] = 'satuan';
			$data['error_string'][] = 'Satuan harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('keterangan') == '')
		{
			$data['inputerror'][] = 'keterangan';
			$data['error_string'][] = 'keterangan harus diisi';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}
		
	public function ajax_edit($id)
	{
		$data = $this->uraian->get_by_id($id, 'id_uraian_pekerjaan, uraian, volume, satuan, keterangan, harga_satuan, jumlah');
		$data->harga_satuan = number_format($data->harga_satuan, 2);
		$data->jumlah = number_format($data->jumlah, 2);
		echo json_encode($data);
	}
	
	public function ajax_delete()
	{
		$post = $this->input->post();
		$id = (!empty($post['id'])) ? $post['id'] : '';
		
		//delete file
		$field = 'status';		
		$data = array("status" => 0);
		
		$hasil = $this->uraian->update(array('id_uraian_pekerjaan' => $id), $data);
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
