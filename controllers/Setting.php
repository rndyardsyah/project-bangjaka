<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting extends CI_Controller {
	
	/*
	 * @author     		RENDY
	 * @author_email    rendyardian199@gmail.com
	 *  KOTA TANGERANG 
	 */

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Setting_model', 'hak_akses');	
		$this->db_ba = $this->load->database('db_pembayaran_ls', TRUE);
		if($this->session->userdata('login')==false){
			redirect(base_url('site/login'));
		}
		
	}
	
	public function index()
	{
		$content['class_name'] = get_class($this);	
		
		$skpd = getSkpd();
		
		$content['selectListSkpd'] = $skpd;		
		$content['content'] = $this->load->view('setting/index',@$content,true);
		$this->load->view('ba/index', @$content,false,false);
		var_dump($content);
	}
  
		function cekprokegsub($kode_unor=''){  

			$content['class_name'] = get_class($this);	
			if(empty($kode_unor)) return false;

			// $data_arr['tahun'] = date('Y');
			// $data_arr['id_tahap'] = 7; //otomatis ambil dari t_prokeg_skpd di db sipkdi90
			$get_prokegsub = getProkegsub($kode_unor);
			
			$content['data_prokegsub'] = $get_prokegsub;
			$content['kode_unor'] = $kode_unor;
			$content['content'] = $this->load->view('setting/view_prokegsub',@$content);
			
		}
		
		
		function ajax_get_form()
		{
			$post = $this->input->post();
			$id_prokeg_skpd = $post['id_prokeg_skpd'];
			$nama_sub = $post['nama_sub'];
			$kode_unor = $post['kode_unor'];
			
			$cek_data = $this->hak_akses->get($start = null, $length = null, $sort = null, $order = null, $where = array('id_prokeg_skpd'=> $id_prokeg_skpd), $like = null, $table='t_setting_pejabat', $field='*', $row_array=true, $join=false, $group_by=false);
			
			$id_pegawai_pptk = '';
			$id_pegawai_ppk = '';
			$id_pegawai_pengadaan = '';
			if(!empty($cek_data['id_prokeg_skpd'])){				
				$id_pegawai_pptk = $cek_data['id_pegawai_pptk'];
				$id_pegawai_ppk = $cek_data['id_pegawai_ppk'];
				$id_pegawai_pengadaan = $cek_data['id_pegawai_pengadaan'];
			}
			
			$data_pegawai = get_pegawai($kode_unor);
			$ppk = getformselect_servicev2($data_pegawai, 'id_pegawai_ppk', 'id_pegawai', 'nama_pegawai', $id_pegawai_ppk);
			$pptk = getformselect_servicev2($data_pegawai, 'id_pegawai_pptk', 'id_pegawai', 'nama_pegawai', $id_pegawai_pptk);
			$pengadaan = getformselect_servicev2($data_pegawai, 'id_pegawai_pengadaan', 'id_pegawai', 'nama_pegawai', $id_pegawai_pengadaan);
			
			
			$content['ppk'] = $ppk;
			$content['pptk'] = $pptk;
			$content['pengadaan'] = $pengadaan;
			$content['id_prokeg_skpd'] = $id_prokeg_skpd;
			$content['nama_sub'] = $nama_sub;
			$content['kode_unor'] = $kode_unor;
			$content['content'] = $this->load->view('setting/form_pejabat',@$content);
		}
		
		function ajax_save_proses(){
			$post = $this->input->post();
			
			$kode_unor = $post['kode_unor'];
			$id_prokeg_skpd = $post['id'];
			$json_pptk = $post['id_pegawai_pptk'];
			$json_ppk = $post['id_pegawai_ppk'];
			$json_pengadaan = $post['id_pegawai_pengadaan'];
			$id_pegawai_pptk = json_decode($json_pptk, true)['id_pegawai'];
			$id_pegawai_ppk = json_decode($json_ppk, true)['id_pegawai'];
			$id_pegawai_pengadaan = json_decode($json_pengadaan, true)['id_pegawai'];
			
			$where['id_prokeg_skpd'] = $id_prokeg_skpd;
			$cek_ready = $this->hak_akses->get($start = null, $length = null, $sort = null, $order = null, $where = array('id_prokeg_skpd'=> $id_prokeg_skpd), $like = null, $table='t_setting_pejabat', $field='*', $row_array=true, $join=false, $group_by=false);
			
			$data_sub = getProkegsub($kode_unor, $id_prokeg_skpd_sub=array('id_prokeg'=> $id_prokeg_skpd));
			
			
			$data['id_pegawai_pptk'] = $id_pegawai_pptk;
			$data['id_pegawai_ppk'] = $id_pegawai_ppk;
			$data['id_pegawai_pengadaan'] = $id_pegawai_pengadaan;
			$data['json_pptk'] = $json_pptk;
			$data['json_ppk'] = $json_ppk;
			$data['json_pengadaan'] = $json_pengadaan;
			$data['json_sub_kegiatan'] = json_encode($data_sub);
			
			$data['cdd'] = date('Y-m-d H:i:s');

			if(!empty($cek_ready['id_prokeg_skpd']))
			{
				$insert = $this->hak_akses->update($where=array('id_prokeg_skpd'=> $id_prokeg_skpd), $data, $table='t_setting_pejabat');
			}else{
				$data['id_prokeg_skpd'] = $id_prokeg_skpd;
				$insert = $this->hak_akses->save($data, $table='t_setting_pejabat');
			}

			
			if($insert){
				$result['status'] = true;
				$result['notif'] = '<div class="alert alert-success" role="alert"><i class="fa fa-thumbs-o-up fa-fw"></i> Berhasil</div>';
			}else{				
				$result['status'] = false;
				$result['notif'] = '<div class="alert alert-danger" role="alert"><i class="fa fa-thumbs-o-down fa-fw"></i> Silahkan Ulangi Kembali!</div>';
			}
			
			echo json_encode($result);
		}
	
}
