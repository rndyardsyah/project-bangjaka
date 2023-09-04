<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rincian_detail extends CI_Controller {
	
	/*
	 * @author     		RENDY
	 * @author_email    rendyardian199@gmail.com
	 *  KOTA TANGERANG 
	 */

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Prokeg_sub_hir_model', 'prokeg_sub_hir');
		$this->load->model('Rincian_model', 'rincian');
		$this->db_ba = $this->load->database('db_pembayaran_ls', TRUE);
		if($this->session->userdata('login')==false){
			redirect(base_url('site/login'));
		}
	}
	
	public function ajax_list($id_prokeg_aktif='', $id_spk = ''){
		
		$data = array();
		$sub = $this->prokeg_sub_hir->get_by_id_prokeg_aktif($id_prokeg_aktif);
		$children = $this->buildtree_sub($sub, $id_spk);
		
		echo json_encode($children);
    }
	
	 private function buildtree_sub($src_arr, $id_spk, $id_parent = 0, $tree = array()){
		$data = array();
		$data_2 = array();
		foreach($src_arr as $idx => $row){
			
			if($row->id_parent == $id_parent){
				foreach($row as $k => $v){
					$tree[$k] = $v;
				}
				unset($src_arr[$idx]);

				$children = $this->buildtree_sub($src_arr, $id_spk, $row->id);
				
				if(!$children){
					
					$cek_data = $this->rincian->get_cek_history($row->id_prokeg_aktif, $row->id);
					
					if($cek_data == 0){
						$tree['uraian'] = '<input type="checkbox" class="data-check" id="rincian_'.$row->id.'" 
						onchange="toggleCheckbox(\''.htmlentities(json_encode($row)).'\');">' . $row->uraian;
					}else{
						if($id_spk > 0){
							$cek_data = $this->rincian->get_cek_history_by_id($row->id_prokeg_aktif, $row->id, $id_spk);
							
							if($cek_data == 0){
								$tree['uraian'] = $row->uraian;
							}else{
								$tree['uraian'] = '<input type="checkbox" class="data-check" id="rincian_'.$row->id.'" onchange="toggleCheckbox(\''.htmlentities(json_encode($row)).'\');">' . $row->uraian;
							}
						}else{
							$tree['uraian'] = $row->uraian;
						}
					}
					
					
				}
				
				$tree['kode'] = $row->kode_rek;				
				$tree['text'] = $row->uraian;
				$tree['children'] = $children;
				
				
				$data[] = $tree;
			}
		}
		return $data;
	}
	
	function getData_History(){
		
		$post = $this->input->post();
		$data = $this->rincian->get_by_id_multi($post['id'], 'id_prokeg_sub_hir');
		$result = array();
		if(!empty($data)){
			foreach($data as $row){
				$result[] = $row['id_prokeg_sub_hir'];
			}
			
			echo json_encode(array("status"=> true, "data"=> $result));
		}else{
			echo json_encode(array("status"=> false));
		}
		
	}
	
}
