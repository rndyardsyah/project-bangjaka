<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Integrasiv2 extends CI_Controller {
		
	 public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
		$this->view_folder = 'eoffice/';    
		$this->config->set_item('theme','sb-admin');    	
		$content['class_name'] = get_class($this);	
		
		$content['content'] = $this->load->view('integrasiv2/index',@$content,true);
        $this->view('index',$content,false,false);
		
		if($this->session->userdata('login')==false){
			redirect(base_url('site/login'));
		}
    }
	
    public function belanja_langsung()
    {		
		$this->view_folder = 'eoffice/';    
		$this->config->set_item('theme','sb-admin');    	
		$content['class_name'] = get_class($this);	
		
		$content['content'] = $this->load->view('integrasiv2/belanja_langsung',@$content,true);
        $this->view('index',$content,false,false);
    }
	
    public function belanja_tidak_langsung()
    {		
		$this->view_folder = 'eoffice/';    
		$this->config->set_item('theme','sb-admin');    	
		$content['class_name'] = get_class($this);	
		
		$content['content'] = $this->load->view('integrasiv2/belanja_tidak_langsung',@$content,true);
        $this->view('index',$content,false,false);
    }

    public function belanja_total()
    {		
		$this->view_folder = 'eoffice/';    
		$this->config->set_item('theme','sb-admin');    	
		$content['class_name'] = get_class($this);	
		
		$content['content'] = $this->load->view('integrasiv2/belanja_total',@$content,true);
        $this->view('index',$content,false,false);
    }
	
    public function ajax_list_bl($id_skpd='')
    {		
		$tahun = date('Y');
		$result = getBlSievlapi($id_skpd, $tahun);
		// var_dump($result);exit;
		echo $result['data'];
		
		// $cek = get_RincianKegiatan();
		// echo json_encode($cek);
    }
	
    public function ajax_list_btl($id_skpd='')
    {		
		$result = getBtlSievlapi($id_skpd);
		echo $result['data'];
    }
	
    public function ajax_list_belanja_total()
    {		
		$result = getBelTotalSievlapi();
		echo $result['data'];
    }
	
	
	
	function getIntegration($id_unor) {
        // set_time_limit(-1);    
		$tahun = date('Y');
		$hasil = '';
		$allOpd = all_opd_service();
		
		if($id_unor == 'all'){
			
			if(!empty($allOpd)){
				foreach($allOpd as $rowOpd)
				{
					$id_unor = $rowOpd['id_unor'];
					// if($id_unor == '23085' || $id_unor == '22977')
					// { 
						$get_data_opd = get_kode_unor_by_opd($id_unor, $tahun);		
						$get_datarr = $get_data_opd[0];		
						$nama_opd = $get_data_opd[1];		
						
						$get_data = get_service_sievlap_re($id_unor, $tahun);		
						$get_data_spj = get_spj($get_datarr, $tahun);	
						$get_data_sp2d = get_sp2d($get_datarr, $tahun);		
					
						$data_sievlap = $get_data['data'];
						if(!empty($data_sievlap))
						{
							$tree = getCategoriesByParentId($data_sievlap);
							$rows = data_marge($tree, $get_data_spj['data'], $get_data_sp2d['data']);
							$children = buildTree($rows, $id_unor);
													
							$skpd['ID'] = $id_unor;
							$skpd['ID_PARENT'] = 0;
							$skpd['ID_PROKEG_AKTIF'] = '';
							$skpd['KODE_REK'] = '';
							$skpd['URAIAN'] = strtoupper($nama_opd);
							$skpd['URAIAN_PROKEG_JENIS'] = '';
							$skpd['PAGU'] = '';
							$skpd['RENCANAFISIK'] = '';
							$skpd['REFISIKPERSEN'] = '';
							$skpd['RENCANAREKEURP'] = '';
							$skpd['RENCANAREKEUPERSEN'] = '';
							$skpd['REKEURP'] = '';
							$skpd['REKEUPERSEN'] = '';
							$skpd['REKEUPERSENKAS'] = '';
							$skpd['DEVIASIRP'] = '';
							$skpd['DEVIASIPERSEN'] = '';
							$skpd['DEVIASIFISIK'] = '';
							$skpd['SISARP'] = '';
							$skpd['SISAPERSEN'] = '';
							$skpd['TOTAL_SP2D'] = '';
							$skpd['TOTAL_SPJ'] = '';
							$skpd['CHILDREN'] = $children;
							$data[] = $skpd;
						}
					// }
				}
				$kota['ID'] = 0;
				$kota['ID_PARENT'] = null;
				$kota['ID_PROKEG_AKTIF'] = '';
				$kota['KODE_REK'] = '';
				$kota['URAIAN'] = 'KOTA TANGERANG';
				$kota['URAIAN_PROKEG_JENIS'] = '';
				$kota['PAGU'] = '';
				$kota['RENCANAFISIK'] = '';
				$kota['REFISIKPERSEN'] = '';
				$kota['RENCANAREKEURP'] = '';
				$kota['RENCANAREKEUPERSEN'] = '';
				$kota['REKEURP'] = '';
				$kota['REKEUPERSEN'] = '';
				$kota['REKEUPERSENKAS'] = '';
				$kota['DEVIASIRP'] = '';
				$kota['DEVIASIPERSEN'] = '';
				$kota['DEVIASIFISIK'] = '';
				$kota['SISARP'] = '';
				$kota['SISAPERSEN'] = '';
				$kota['TOTAL_SP2D'] = '';
				$kota['TOTAL_SPJ'] = '';
				$kota['CHILDREN'] = $data;
				$datas[] = $kota;
				$hasil = str_replace("CHILDREN","children", json_encode($datas));
			}
			
		}else{
			$get_data_opd = get_kode_unor_by_opd($id_unor, $tahun);		
			$get_datarr = $get_data_opd[0];		
			$nama_opd = $get_data_opd[1];		
			
			$get_data = get_service_sievlap_re($id_unor, $tahun);		
			$get_data_spj = get_spj($get_datarr, $tahun);	
			$get_data_sp2d = get_sp2d($get_datarr, $tahun);		
		
			$data_sievlap = $get_data['data'];
			if(!empty($data_sievlap)){
				$tree = getCategoriesByParentId($data_sievlap);
				$rows = data_marge($tree, $get_data_spj['data'], $get_data_sp2d['data']);
				$children = buildTree($rows, $id_unor);
				
				
				$skpd['ID'] = $id_unor;
				$skpd['ID_PARENT'] = 0;
				$skpd['ID_PROKEG_AKTIF'] = '';
				$skpd['KODE_REK'] = '';
				$skpd['URAIAN'] = strtoupper($nama_opd);
				$skpd['URAIAN_PROKEG_JENIS'] = '';
				$skpd['PAGU'] = '';
				$skpd['RENCANAFISIK'] = '';
				$skpd['REFISIKPERSEN'] = '';
				$skpd['RENCANAREKEURP'] = '';
				$skpd['RENCANAREKEUPERSEN'] = '';
				$skpd['REKEURP'] = '';
				$skpd['REKEUPERSEN'] = '';
				$skpd['REKEUPERSENKAS'] = '';
				$skpd['DEVIASIRP'] = '';
				$skpd['DEVIASIPERSEN'] = '';
				$skpd['DEVIASIFISIK'] = '';
				$skpd['SISARP'] = '';
				$skpd['SISAPERSEN'] = '';
				$skpd['TOTAL_SP2D'] = '';
				$skpd['TOTAL_SPJ'] = '';
				$skpd['CHILDREN'] = $children;
				$data[] = $skpd;
				$hasil = str_replace("CHILDREN","children", json_encode($data));
			}
		}
		// var_dump($hasil);exit;
		echo $hasil;
    }
		
	function form_detail_prokeg() {
		$post = $this->input->post();	
		$content['id'] = $post['id'];
		$content['uraian'] = $post['uraian'];
		$content['pagu'] = $post['pagu'];
		$content['rencanafisik'] = $post['rencanafisik'];
		$content['rekeurp'] = $post['rekeurp'];
		$content['deviasirp'] = $post['deviasirp'];
		$content['deviasipersen'] = $post['deviasipersen'];
		
		
		$this->load->view('integrasiv2/form_detail_prokeg',@$content);   
    }
	
	function detail_prokeg() {
		$post = $this->input->post();	
		$kode_rek = @$post['kode_rek'];
		$get_data = get_detail_kode_rek($kode_rek);
		//tidak berjalan karena tidak ada tahap dan tahun yang di input ke service
		
		if($get_data['success'] ==  true)
		{
			$data = str_replace("detail","children", json_encode($get_data['data']['rinci']));
		}else{
			$data = '';
		}
		
		$content['data_detail'] = $data;
		$this->load->view('integrasiv2/form_detail_prokeg_v2',@$content);
    }
	
	function test() {				
		$this->Integrasiv2_model->select();
    }
}
