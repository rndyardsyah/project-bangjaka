<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Satker extends CI_Controller {

	public $u = 'r35t4p1k3u';
	public $p = 'QvRnW74sS5yuYegj5ZQakC29FVwrVtryymUZ9jqU';

	public function __construct()
	{ 
		parent::__construct();
		$this->load->model('satker_model', 'satker');
		$this->db_ba = $this->load->database('db_pembayaran_ls', TRUE);	
		if($this->session->userdata('login')==false){
			redirect(base_url('site/login'));
		}
	}

	public function index()
	{
		//$data['view'] = 'satker';
		$content['class_name'] = get_class($this);	
		$content['content'] = $this->load->view('satker/satker',@$content,true);
		$this->load->view('ba/index', @$content,false,false);
	}

	public function ajax_list()
	{
		$this->load->helper('url');
		$list = $this->satker->get_datatables();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $satker) {
			$no++;
			$row = array();			
			$row[] = $satker->id_satuan_kerja;
			$row[] = $satker->nama;
			$row[] = $satker->created_at;
			$row[] = $satker->updated_at;
			$row[] = '<a href="javascript:void(0)" onclick="detail('.$satker->id_satuan_kerja.')"><i class="glyphicon glyphicon-search"></i> Detail </a>';
			$data[] = $row;
		}
		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->satker->count_filtered(),
			"recordsFiltered" => $this->satker->count_filtered(),
			"data" => $data,
		);
		echo json_encode($output);
	}

	public function detail()
	{
		$v = $this->input->post('v');
		$cari = $this->satker->detail($v, 'satker', 'id_satuan_kerja');
		if($cari->num_rows() > 0) {
			$row = $cari->row();
			echo json_encode($row);
		}

	}

	public function sync()
	{
		$this->curl->create('https://openkeuda.tangerangkota.go.id/services/inaproc/satker');
		$this->curl->http_login($this->u, $this->p);
		$data = json_decode($this->curl->execute(), true);
		if($data != NULL) {
			$cekdata = $data['data'];
			if($cekdata != NULL) {
				$source_data = $data['data'];
				foreach($source_data as $row_data) {
					$this->db_ba->select('id_satuan_kerja');
					$this->db_ba->from('satker');
					$this->db_ba->where('id_satuan_kerja', $row_data['id_satuan_kerja']);
					$cek = $this->db_ba->get();
					$jumlah = $cek->num_rows();
					if($jumlah > 0) {
						$arr = array(
							'id_satuan_kerja' => $row_data['id_satuan_kerja'],
							'id' => $row_data['id'],
							'idsatker' => $row_data['idsatker'],
							'idkldi' => $row_data['idkldi'],
							'isdeleted' => $row_data['isdeleted'],
							'createdby' => $row_data['createdby'],
							'createdon' => $row_data['createdon'],
							'nama' => $row_data['nama'],
							'auditupdate' => $row_data['auditupdate'],
							'tahunaktif' => $row_data['tahunaktif'],
							'updated_at' => date('Y-m-d H:i:s')
						);
						$this->db_ba->where('id_satuan_kerja' , $row_data['id_satuan_kerja']);
						$this->db_ba->update('satker', $arr);
						$result = $this->db_ba->affected_rows();
					}
					else {
						$arr = array(
							'id_satuan_kerja' => $row_data['id_satuan_kerja'],
							'id' => $row_data['id'],
							'idsatker' => $row_data['idsatker'],
							'idkldi' => $row_data['idkldi'],
							'isdeleted' => $row_data['isdeleted'],
							'createdby' => $row_data['createdby'],
							'createdon' => $row_data['createdon'],
							'nama' => $row_data['nama'],
							'auditupdate' => $row_data['auditupdate'],
							'tahunaktif' => $row_data['tahunaktif'],
							'created_at' => date('Y-m-d H:i:s')
						);
						$this->db_ba->insert('satker', $arr);
						$result = $this->db_ba->affected_rows();
					}
				}
				$msg = array('stat' => 1, 'msg' => '');
				echo json_encode($msg);
			}
			else {
				//NULL
				$msg = array('stat' => 0, 'msg' => '');
				echo json_encode($msg);
			}
		}
		else {
			//NULL
			$msg = array('stat' => 0, 'msg' => '');
			echo json_encode($msg);
		}

	}

}
