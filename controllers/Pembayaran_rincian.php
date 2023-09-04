<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pembayaran_rincian extends CI_Controller {
	
	/*
	 * @author     		RENDY
	 * @author_email    rendyardian199@gmail.com
	 *  KOTA TANGERANG 
	 */

	public function __construct()
	{
		parent::__construct();
		$this->load->model('pembayaran_rincian_model', 'pembayaran_rincian');		
		$this->load->model('rincian_model', 'rincian');		
		$this->load->model('pencairan_model', 'pencairan');	
		$this->db_ba = $this->load->database('db_pembayaran_ls', TRUE);
		if($this->session->userdata('login')==false){
			redirect(base_url('site/login'));
		}
	}
	
	public function ajax_check($id=false, $id_pencairan=false)
	{
		
		if($id && $id_pencairan){
			$data_id_spk = $this->pencairan->get_by_id($id_pencairan, 'id_spk');
			$data_rincian_spk = $this->rincian->get_where(array('id_spk'=>$data_id_spk->id_spk, 'status'=>1),'id');	
			$data_rincian = $this->pembayaran_rincian->get_by_idv2($id, 'a.id_pembayaran_rinci');
			
			if(count($data_rincian_spk) == count($data_rincian)){
				$content['status'] = true;
			}else{			
				$content['status'] = false;
			}
		}else{
			$content['status'] = false;
		}
		
		echo json_encode($content);
	}

	
	public function getDataRincian(){
		$post = $this->input->post();
		
		$content['class_name'] = get_class($this);	
		$id_rincian_detail = str_replace('rinci', '', $post['id_rincian_detail']);
		$id_pembayaran = $post['id_pembayaran'];
		
		
		$data_rincian = $this->pembayaran_rincian->get_where(array('id_rincian_detail_spk'=> $id_rincian_detail, 'id_pembayaran'=> $id_pembayaran));
		if(!$data_rincian){			
			$data_rincian = $this->rincian->get_where(array('id'=> $id_rincian_detail), 'id, uraian, volume, satuan');
		}
		// var_dump($data_rincian);exit;
		// var_dump($data_rincian);exit;
		$content['data_rincian'] = $data_rincian;
		$content['content'] = $this->load->view('pembayaran/form_lampiran_rincian',@$content);
	}
	
	public function ajax_save()
	{
		$this->_validate();
		$post = $this->input->post();
		$data = array(
			"id_rincian_detail_spk" => $post['id_rincian_detail_spk'],
			"id_pembayaran" => $post['id_pembayaran'],
			"uraian_dpa" => $post['uraian_dpa'],
			"satuan" => str_replace(',','',$post['satuan']),
			"volume" => str_replace(',','',$post['volume']),
			"harga_satuan" => str_replace(',','',$post['harga_satuan']),
			"jumlah_harga_satuan" => str_replace(',','',$post['jumlah_harga_satuan']),			
		);
		
		$id = (!empty($post['id_pembayaran_rinci'])) ? $post['id_pembayaran_rinci'] : '';
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
		$result = $this->pembayaran_rincian->save($id, $data, $created);
		// $result = true;
		
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

		if($this->input->post('uraian_dpa') == '')
		{
			$data['inputerror'][] = 'uraian_dpa';
			$data['error_string'][] = 'Jenis Uraian Pekerjaan harus diisi';
			$data['status'] = FALSE;
		}
		
		if($this->input->post('satuan') == '')
		{
			$data['inputerror'][] = 'satuan';
			$data['error_string'][] = 'Satuan harus diisi';
			$data['status'] = FALSE;
		}
		
		if($this->input->post('volume') == '')
		{
			$data['inputerror'][] = 'volume';
			$data['error_string'][] = 'Volume harus diisi';
			$data['status'] = FALSE;
		}
		
		if($this->input->post('harga_satuan') == '')
		{
			$data['inputerror'][] = 'harga_satuan';
			$data['error_string'][] = 'Harga Satuan harus diisi';
			$data['status'] = FALSE;
		}
		
		if($this->input->post('jumlah_harga_satuan') == '')
		{
			$data['inputerror'][] = 'jumlah_harga_satuan';
			$data['error_string'][] = 'Jumlah Harga Satuan harus diisi';
			$data['status'] = FALSE;
		}
		
		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}

	}
	
	
}
