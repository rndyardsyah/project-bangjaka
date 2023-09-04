<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Riwayat_surat extends CI_Controller {
	
	/*
	 * @author     		RENDY
	 * @author_email    rendyardian199@gmail.com
	 *  KOTA TANGERANG 
	 */

	public function __construct()
	{
		parent::__construct();
		$this->load->model('inbox_model', 'inbox');	
		$this->db_ba = $this->load->database('db_pembayaran_ls', TRUE);
		$this->db_eoffice = $this->load->database('db_eoffice', TRUE);
		if($this->session->userdata('login')==false){
			redirect(base_url('site/login'));
		}
	}
	
	public function index()
	{
		$post = $this->input->post();
		$keterangan = $post['keterangan'];
		$id_read = $post['id_read']; //id read ini bisa jadi id_hasil_pekerjaan, bisa jadi id_pembayaran tergantung dari nilai keterangan 1 atau 0, jika pembayaran maka keterangannya 1, jika hasil pekerjaan maka keterangannya 0
		$content['class_name'] = get_class($this);			
		
		$result='';
		
		if(isset($keterangan) && isset($id_read)){

			if($keterangan == '0'){
				$where = array('id_hasil_pekerjaan'=> $id_read, 'active'=> '1', 'type'=> '1');
			}
			
			if($keterangan == '1'){
				$where = array('id_pembayaran'=> $id_read, 'active'=> '1', 'type'=> '1');
			}
			if($keterangan == '2'){
				$where = array('id_hasil_pekerjaan'=> $id_read, 'active'=> '1', 'type'=> '2');
			}
			
			$field = '
				id_inbox,
				id_hasil_pekerjaan,
				mailto,
				nip_mailto,
				nama_mailto,
				nomenklatur_jabatan_mailto,
				mailfrom,
				nip_mailfrom,
				nama_mailfrom,
				nomenklatur_jabatan_mailfrom,
				status,
				viewed,
				catatan,
				notif_text,
				paraf,
				id_pembayaran
			';
			
			$data_riwayat = $this->inbox->get_where($where, $field, true);
			
			
			
			$no = 1;
			foreach($data_riwayat as $rss)
			{
				$dilihat = (!empty($rss['viewed'])) ? GetFullDateFull($rss['viewed']) : '<font color="red" style="font-weight: bold;">Belum dibaca</font>';
				
				$result .= '
						<tr>
						 <td>'.$no++.'</td>
						 <td style="text-align: left !Important;"><label title="'.$rss['nomenklatur_jabatan_mailto'].'">'.$rss['nama_mailto'].'</label></a>
						 </td>
						 <td>Surat Masuk</td>
						 <td>'.$dilihat.'</td>
					  </tr>
				';
			}
		}
		
		
		$content['data'] = $result;
		$content['content'] = $this->load->view('ba/riwayat_surat/index',@$content);
	}
	
}
