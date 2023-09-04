<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Catatan_surat extends CI_Controller {
	
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
		$keterangan = @$post['keterangan'];
		$id_read = $post['id_read']; //id read ini bisa jadi id_hasil_pekerjaan, bisa jadi id_pembayaran tergantung dari nilai keterangan 1 atau 0, jika pembayaran maka keterangannya 1, jika hasil pekerjaan maka keterangannya 0
		$content['class_name'] = get_class($this);			
		$bukanBA = false;
		$data_riwayat='';
		if(isset($keterangan) && isset($id_read)){
			
			if($keterangan !== '2'){				
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
					id_pembayaran,
					cdd
				';
				
				$idtext = ($keterangan == 0) ? "id_hasil_pekerjaan ='".$id_read."'" : "id_pembayaran ='".$id_read."'";

				$sql ="SELECT ".$field." FROM t_inbox
				WHERE ".$idtext." AND `paraf` = '0' ORDER BY `cdd` ASC, `id_inbox` ASC
				";
				$query = $this->db_ba->query($sql);
				
				$data_riwayat = $query->result_array();
			}else{
				$data_inbox = $this->inbox->get($start = null, $length = null, $sort = null, $order = null, $where = array('a.id_surat'=> $id_read), $like = null, $table='t_verif_komen a', $field='a.*', $row_array=false, $join=false);

				$data_riwayat = $data_inbox;
				$bukanBA = true;

			}
			
		}
		
		$content['bukanBA'] = $bukanBA;
		$content['data'] = $data_riwayat;
		$content['content'] = $this->load->view('catatan_surat/index',@$content);
	}
	
}
