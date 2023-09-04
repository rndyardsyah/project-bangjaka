<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// include_once (dirname(__FILE__) . "/Pencairan.php");

class Spk extends CI_Controller {
	
	/*
	 * @author     		RENDY
	 * @author_email    rendyardian199@gmail.com
	 *  KOTA TANGERANG 
	 */

	public function __construct()
	{
		parent::__construct();
		$this->load->model('kegiatan_model', 'kegiatan');	
		$this->load->model('spk_model', 'spk');		
		$this->load->model('inbox_model', 'inbox');		
		$this->load->model('penyedia_model', 'penyedia');		
		$this->load->model('pencairan_model', 'pencairan');		
		$this->load->model('Prokeg_sub_hir_model', 'prokeg_sub_hir');
		$this->load->model('Rincian_model', 'rincian');
		$this->load->model('Setting_model', 'setting');
		$this->db_ba = $this->load->database('db_pembayaran_ls', TRUE);
		$this->db = $this->load->database('db_pembayaran_ls', TRUE);
		if($this->session->userdata('login')==false){
			redirect(base_url('site/login'));
		}
	}
	
	public function index()
	{
		$id_surat = (!empty($_POST['id_surat'])) ? $_POST['id_surat'] : false;

		if($id_surat){
			$get_surat_pengantar = $this->inbox->get($start = null, $length = null, $sort = null, $order = null, $wherex = array('a.id_surat'=> $id_surat, 'a.active'=> 1), $like = null, $table='t_surat a', $field='a.id_surat, a.id_parent', $row_array=true, $join=false);

			$get_surat_notdin_pemenang = $this->inbox->get($start = null, $length = null, $sort = null, $order = null, $wherex = array('a.id_parent'=> $get_surat_pengantar['id_parent'], 'a.id_bentuk'=> 2, 'a.active'=> 1), $like = null, $table='t_surat a', $field='a.data_penyedia, a.id_parent', $row_array=true, $join=false);

			// $get_surat_spt = $this->inbox->get($start = null, $length = null, $sort = null, $order = null, $wherex = array('a.id_surat'=> $get_surat_pengantar['id_parent']), $like = null, $table='t_surat a', $field='a.id_surat, a.id_parent', $row_array=true, $join=false);

			// $get_surat_permohonan = $this->inbox->get($start = null, $length = null, $sort = null, $order = null, $wherex = array('a.id_surat'=> $get_surat_spt['id_parent']), $like = null, $table='t_surat a', $field='a.id_surat, a.id_parent, a.idrup_penyedia, a.json_rup_penyedia, a.waktu_pelaksanaan', $row_array=true, $join=false);

			
			$data_penyedia = json_decode($get_surat_notdin_pemenang['data_penyedia'], true);

			$arr_penyedia = array();
			foreach($data_penyedia as $rss)
			{
				unset($rss['harga_penawaran'], $rss['negosiasi'], $rss['data_tenagaahli']);
				
				$row = array();
				$row = $rss;
				$row['status'] = 1;

				$row['kode_unor'] = substr($this->session->userdata('kode_unor'),0,5);

				$cek_penyedia = $this->inbox->get($start = null, $length = null, $sort = null, $order = null, $wherex = array('a.npwp'=> $rss['npwp'], ), $like = null, $table='m_penyedia a', $field='a.id_penyedia', $row_array=true, $join=false);
				if(!empty($cek_penyedia['id_penyedia'])){
					//update
					$row['mdd'] = date('Y-m-d H:i:s');
					$row['mdb'] = $this->session->userdata('id_pegawai');
					$update = $this->spk->update($where=array('id_penyedia'=> $cek_penyedia['id_penyedia']), $row, $table='m_penyedia');
					$results = $cek_penyedia['id_penyedia'];
				}else{
					//insert
					$row['cdd'] = date('Y-m-d H:i:s');
					$row['cdb'] = $this->session->userdata('id_pegawai');
					$results = $this->spk->save_v2($row, $table='m_penyedia');
				}

				$row['id_penyedia'] = $results;
				$arr_penyedia[] = $row;
			}
			$content['arr_penyedia'] = $arr_penyedia;	
		}

		$content['id_surat'] = $id_surat;	
		$content['class_name'] = get_class($this);	
		$content['content'] = $this->load->view('spk/index',@$content,true);
		$this->load->view('ba/index', @$content,false,false);
	}
	
	public function ajax_insert(){
        
        $post = $this->input->post();
        $data = (!empty($_POST['data'])) ? json_decode($_POST['data'], true): '';
        $kode_rek = (!empty($data['kode_path'])) ? $data['kode_path']: '';
        $id_prokeg_jenis = (!empty($data['id_prokeg_jenis'])) ? $data['id_prokeg_jenis']+1: ''; 
        $id_prokeg_aktif = (!empty($data['id_prokeg_skpd'])) ? $data['id_prokeg_skpd']: ''; 
        $id_prokeg_skpd = (!empty($data['id_prokeg_skpd'])) ? $data['id_prokeg_skpd']: '';  
        $id_spk = $post['id_spk'];  
        
        $tahun = date('Y');     
        // $data_arr            = getBlKodeRekKeg($post);
        // $data_arr        = get_RincianKegiatan($kode_rek, @$post['tahap_kegiatan'], $tahun);
        
        // $data_arr = getProkegsub($kode_unor = substr($this->session->userdata('kode_unor'),0,5), $id_prokeg_skpd_sub=$id_prokeg_skpd, $textfield='id_prokeg_skpd');
        $data_arr = getProkegsub($kode_unor = substr($this->session->userdata('kode_unor'),0,5), $data);
        
        
        $content['class_name'] = get_class($this);  
        $content['id_prokeg_aktif'] = $id_prokeg_aktif; 
        $content['id_spk'] = $id_spk;   
        $content['data_arr'] = $data_arr;   
        $content['data_sub'] = $data;   
        
        if(!empty($id_spk))
        {
            //buat otomatis ceklist
            $data_ready = $this->inbox->get($start = null, $length = null, $sort = null, $order = null, $wherex = array('id_spk'=> $id_spk), $like = null, $table='rincian_detail_spk', $field='id_prokeg_sub_hir, kode_rek', $row_array=false, $join=false);
            
            
            
            $content['data_ready'] = $data_ready;
        }
        
        $content['content'] = $this->load->view('spk/list_rincianv2',@$content);
    }
    public function ajax_insert_test(){
        $post = $this->input->post();
        $data = (!empty($_POST['data'])) ? json_decode($_POST['data'], true): '';
        $kode_rek = (!empty($data['kode_path'])) ? $data['kode_path']: '';
        $id_prokeg_jenis = (!empty($data['id_prokeg_jenis'])) ? $data['id_prokeg_jenis']+1: ''; 
        $id_prokeg_aktif = (!empty($data['id_prokeg_skpd'])) ? $data['id_prokeg_skpd']: ''; 
        $id_prokeg_skpd = (!empty($data['id_prokeg_skpd'])) ? $data['id_prokeg_skpd']: '';  
        $id_spk = $post['id_spk'];  
        $tahun = date('Y');     
        // $data_arr            = getBlKodeRekKeg($post);
        // $data_arr        = get_RincianKegiatan($kode_rek, @$post['tahap_kegiatan'], $tahun);
        
        // $data_arr = getProkegsub($kode_unor = substr($this->session->userdata('kode_unor'),0,5), $id_prokeg_skpd_sub=$id_prokeg_skpd, $textfield='id_prokeg_skpd');
        $data_arr = getProkegsub($kode_unor = substr($this->session->userdata('kode_unor'),0,5), $data);
        $content['class_name'] = get_class($this);  
        $content['id_prokeg_aktif'] = $id_prokeg_aktif; 
        $content['id_spk'] = $id_spk;   
        $content['data_arr'] = $data_arr;   
        $content['data_sub'] = $data;   
        // var_dump($content['data_sub']);
        if(!empty($id_spk))
        {
            //buat otomatis ceklist
            $data_ready = $this->inbox->get($start = null, $length = null, $sort = null, $order = null, $wherex = array('id_spk'=> $id_spk), $like = null, $table='rincian_detail_spk', $field='id_prokeg_sub_hir, kode_rek', $row_array=false, $join=false);
            
            
            
            $content['data_ready'] = $data_ready;
        }
        $content['content'] = $this->load->view('spk/list_rincianv2',@$content);
        // echo "huya";
        
        /*
        */
    }

	public function ajax_insert_child_update($id_parent = null, $detail='', $id_prokeg_aktif='', $id_prokeg_jenis=''){
		
		if(!empty($detail))
		{							
			foreach ($detail as $key => $row) {
				$kode_path_ 	= substr($row['kode_path'], -1);
				$kode_path_ 	= ($kode_path_ != '.') ? $row['kode_path'].'.' : $row['kode_path'];
				$kode_path[]  	= $kode_path_;
			}
			asort($kode_path); //sort array low to high number
			
			foreach ($detail as $key => $row) {
				$kode_path_ 	= substr($row['kode_path'], -1);
				$kode_path_ 	= ($kode_path_ != '.') ? $row['kode_path'].'.' : $row['kode_path'];
				$countStrlen[]  = strlen($kode_path_);
			}
			asort($countStrlen); //count strlen
			
			$result 	= array_values(array_unique($countStrlen));	//array unik remove yg sama, array values resset nomer array
			asort($result); //count strlen
			
			$no = 1; 
			foreach($kode_path as $rss){
				foreach($detail as $row)
				{					
					$kode_path = substr($row['kode_path'], -1);
					$kode_path = ($kode_path != '.') ? $row['kode_path'].'.' : $row['kode_path'];
					
					if($rss == $kode_path){						
						// if($row['uraian'] != ' ')
						// {
							$data_cek = array('id_prokeg_aktif' => $id_prokeg_aktif, 'kode_rek' => $kode_path, 'status' => '1');
							$cek_ready = $this->prokeg_sub_hir->get_where($data_cek);
							
							if(empty($cek_ready) || $cek_ready == null)
							{
								$kode_rek = getKodePathArray_sub($row, $detail, $result);
								
								if(!empty($kode_rek)){
									$where = array('id_prokeg_aktif' => $id_prokeg_aktif, 'kode_rek' => $kode_rek, 'status' => '1');
									$cek = $this->prokeg_sub_hir->get_where($where);		
									
									$id_prokeg_jenis = $cek['id_prokeg_jenis']+1;
									if(!empty($cek)){										
										$data = array(
											'id_parent' => $cek['id'],
											'id_prokeg_aktif' => $id_prokeg_aktif,
											'id_prokeg_jenis' => $id_prokeg_jenis,
											'kode_rek' => $kode_path,
											'uraian' => $row['uraian'],
											'pagu' =>  str_replace(',', '.', str_replace('.', '', $row['jumlah'])),
											'volume' =>  $row['volume'],
											'satuan' =>  $row['satuan'],
											'harga_satuan' =>  str_replace(',', '.', str_replace('.', '', $row['harga_satuan'])),
											'create_user' => 0,
											'create_date' => date('Y-m-d H:i:s'),
											'status' => 1,
										);
										$insert = $this->prokeg_sub_hir->insert($data);
									}
								}else{
									$data = array(
										'id_parent' => $id_parent,
										'id_prokeg_aktif' => $id_prokeg_aktif,
										'id_prokeg_jenis' => $id_prokeg_jenis+1,
										'kode_rek' => $kode_path,
										'uraian' => $row['uraian'],
										'pagu' =>  str_replace(',', '.', str_replace('.', '', $row['jumlah'])),
										'volume' =>  $row['volume'],
										'satuan' =>  $row['satuan'],
										'harga_satuan' =>  str_replace(',', '.', str_replace('.', '', $row['harga_satuan'])),
										'create_user' => 0,
										'create_date' => date('Y-m-d H:i:s'),
										'status' => 1,
									);
									$insert = $this->prokeg_sub_hir->insert($data);
								}
							}else{					
								//update jika data beda
								$this->update_data_sub($cek_ready, $row);
							}					
						// }
						break;
					}
				}
			}			
		}		
    }
		
	public function ajax_insert_child($id_parent = null, $detail='', $id_prokeg_aktif='', $id_prokeg_jenis=''){
		
		if(!empty($detail))
		{							
			foreach ($detail as $key => $row) {
				$kode_path_ 	= substr($row['kode_path'], -1);
				$kode_path_ 	= ($kode_path_ != '.') ? $row['kode_path'].'.' : $row['kode_path'];
				$kode_path[]  	= $kode_path_;
			}
			asort($kode_path); //sort array low to high number
			
			foreach ($detail as $key => $row) {
				$kode_path_ 	= substr($row['kode_path'], -1);
				$kode_path_ 	= ($kode_path_ != '.') ? $row['kode_path'].'.' : $row['kode_path'];
				$countStrlen[]  = strlen($kode_path_);
			}
			asort($countStrlen); //count strlen
			
			$result 	= array_values(array_unique($countStrlen));	//array unik remove yg sama, array values resset nomer array
			asort($result); //count strlen
			
			$no = 1; 
			foreach($kode_path as $rss){
				foreach($detail as $row)
				{					
					$kode_path = substr($row['kode_path'], -1);
					$kode_path = ($kode_path != '.') ? $row['kode_path'].'.' : $row['kode_path'];
					
					if($rss == $kode_path){						
						// if($row['uraian'] != ' ')
						// {
							$data_cek = array('id_prokeg_aktif' => $id_prokeg_aktif, 'kode_rek' => $kode_path, 'status' => '1', 'volume'=> $row['volume'], 'satuan'=> $row['satuan'], 'harga_satuan'=> $row['harga_satuan']);
							$cek_ready = $this->prokeg_sub_hir->get_where($data_cek);
							
							if(empty($cek_ready) || $cek_ready == null)
							{
								$kode_rek = getKodePathArray_sub($row, $detail, $result);
								
								if(!empty($kode_rek)){
									$where = array('id_prokeg_aktif' => $id_prokeg_aktif, 'kode_rek' => $kode_rek, 'status' => '1');
									$cek = $this->prokeg_sub_hir->get_where($where);		
									
									$id_prokeg_jenis = $cek['id_prokeg_jenis']+1;
									if(!empty($cek)){										
										$data = array(
											'id_parent' => $cek['id'],
											'id_prokeg_aktif' => $id_prokeg_aktif,
											'id_prokeg_jenis' => $id_prokeg_jenis,
											'kode_rek' => $kode_path,
											'uraian' => $row['uraian'],
											'pagu' =>  str_replace(',', '.', str_replace('.', '', $row['jumlah'])),
											'volume' =>  $row['volume'],
											'satuan' =>  $row['satuan'],
											'harga_satuan' =>  str_replace(',', '.', str_replace('.', '', $row['harga_satuan'])),
											'create_user' => 0,
											'create_date' => date('Y-m-d H:i:s'),
											'status' => 1,
										);
										$insert = $this->prokeg_sub_hir->insert($data);
									}
								}else{
									$data = array(
										'id_parent' => $id_parent,
										'id_prokeg_aktif' => $id_prokeg_aktif,
										'id_prokeg_jenis' => $id_prokeg_jenis+1,
										'kode_rek' => $kode_path,
										'uraian' => $row['uraian'],
										'pagu' =>  $row['jumlah'],
										'volume' =>  $row['volume'],
										'satuan' =>  $row['satuan'],
										'harga_satuan' =>  str_replace(',', '.', str_replace('.', '', $row['harga_satuan'])),
										'create_user' => 0,
										'create_date' => date('Y-m-d H:i:s'),
										'status' => 1,
									);
									$insert = $this->prokeg_sub_hir->insert($data);
								}
							}else{					
								//update jika data beda
								$this->update_data_sub($cek_ready, $row);
							}					
						// }
						break;
					}
				}
			}			
		}		
    }
		
	public function update_data($cek_ready='', $row='')
	{
		$pagu = (!empty($row['jumlah_perub'])) ? $row['jumlah_perub'] : $row['jumlah_murni'];
		if($cek_ready['uraian'] != $row['uraian'])
		{
			$data = array(
                'uraian' => $row['uraian'],
				'last_edit_user' => $this->session->id,
				'last_edit_date' => date('Y-m-d H:i:s')
            );
			$this->prokeg_sub_hir->update(array('id' => $cek_ready['id']), $data);
		}
		
		if($cek_ready['pagu'] != $pagu)
		{
			$data = array(
                'pagu' => $pagu,
				'last_edit_user' => $this->session->id,
				'last_edit_date' => date('Y-m-d H:i:s')
            );
			$this->prokeg_sub_hir->update(array('id' => $cek_ready['id']), $data);
		}
		
	}
	
	public function update_data_sub($cek_ready='', $row='')
	{
		
		// if($cek_ready['kode_rek'] == '5.2.2.21.04.01.01.'){
		// var_dump($cek_ready);
		// exit;			
		// }
		
		$pagu = str_replace(',', '.', str_replace('.', '', $row['jumlah']));
		if($cek_ready['uraian'] != $row['uraian'])
		{
			$data = array(
                'uraian' => $row['uraian'],
				'last_edit_user' => $this->session->id,
				'last_edit_date' => date('Y-m-d H:i:s')
            );
			$this->prokeg_sub_hir->update(array('id' => $cek_ready['id']), $data);
		}
		
		if($cek_ready['pagu'] != $pagu)
		{
			$data = array(
                'pagu' => $pagu,
				'last_edit_user' => $this->session->id,
				'last_edit_date' => date('Y-m-d H:i:s')
            );
			$this->prokeg_sub_hir->update(array('id' => $cek_ready['id']), $data);
		}
		
		if($cek_ready['volume'] != $row['volume']){			
			$data = array(
				'volume' =>  $row['volume'],
				'last_edit_user' => $this->session->id,
				'last_edit_date' => date('Y-m-d H:i:s')
            );
			$this->prokeg_sub_hir->update(array('id' => $cek_ready['id']), $data);
		}
		
		if($cek_ready['satuan'] != $row['satuan']){			
			$data = array(
				'satuan' =>  $row['satuan'],
				'last_edit_user' => $this->session->id,
				'last_edit_date' => date('Y-m-d H:i:s')
            );
			$this->prokeg_sub_hir->update(array('id' => $cek_ready['id']), $data);
		}
		
		if(str_replace(',', '.', str_replace('.', '', $cek_ready['harga_satuan'])) != str_replace(',', '.', str_replace('.', '', $row['harga_satuan']))){			
			$data = array(
				'harga_satuan' =>  str_replace(',', '.', str_replace('.', '', $row['harga_satuan'])),
				'last_edit_user' => $this->session->id,
				'last_edit_date' => date('Y-m-d H:i:s')
            );
			$this->prokeg_sub_hir->update(array('id' => $cek_ready['id']), $data);
		}
		
	}
	
	public function ajax_list()
	{
		$this->load->helper('url');

		$wherexz['(a.id_pegawai_pptk = "'.$this->session->userdata('id_pegawai').'" or a.id_pegawai_ppk = "'.$this->session->userdata('id_pegawai').'")'] = NULL;
		$get_pptk_ppk =  $this->setting->get($start = null, $length = null, $sort = null, $order = null, $wherexz, $like = null, $table='t_setting_pejabat a', $field='a.id_prokeg_skpd', $row_array=false, $join=false, $group_by=false);

		if(!empty($get_pptk_ppk))
		{
			$data_arrx = array();
			foreach($get_pptk_ppk as $rsz)
			{
				$data_arrx[] = $rsz['id_prokeg_skpd'];
			}
			$_POST['data_id'] = $data_arrx;
		}

		$list = $this->spk->get_datatables();
		// var_dump($list);exit;
		$datatombol = '';
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $person) {
			$no++;
			$row = array();			
			
			$nama_penyedia = ($person->kategori > 0) ? $person->nama_perusahaan : $person->nama_penyedia;
			$data_spk = '<font style="font-weight: bold;">' . $person->no_spk . '</font><br>'.
							 '<font style="font-size:8pt;">Nama Pekerjaan :'.$person->nama_pekerjaan.'</font><br>'.
							 '<font style="font-size:8pt;">Nama Kegiatan : '.$person->nama_kegiatan.'</font><br>'.
							 '<font style="font-size:8pt;">No. DPA SKPD : '.$person->dpa_skpd.'</font><br>'.
							 '<font style="font-size:8pt;">Kode Rekening SKPD : '.$person->kode_rek.'</font><br>'
			;
			
			if($this->session->userdata('id_akses') == 1 || $this->session->userdata('id_akses') == 3 ||  $this->session->userdata('id_akses') == 4 ||  $this->session->userdata('id_akses') == 8){			
				$datatombol = '
								<div class="btn-group">
									<button type="button" class="btn btn-primary dropdown-toggle btn-xs" data-toggle="dropdown"><i class="fa fa-gears fa-fw"></i> Aksi <span class="caret"></span></button>
									<ul class="dropdown-menu" role="menu">
									  <li><a href="javascript:void(0)" onclick="cek_data_proses('.$person->id_spk.')"><i class="glyphicon glyphicon-edit"></i> Edit </a></li>
									  <li><a href="javascript:void(0)" onclick="delete_spk('.$person->id_spk.')"><i class="glyphicon glyphicon-trash"></i> Hapus </a></li>
									  <li><a href="javascript:void(0)" onclick="toPemberkasan('.$person->id_spk.', '.$person->id_penyedia.')"><i class="glyphicon glyphicon-file"></i> Pemberkasan </a></li>
									</ul>
								  </div>';
			}else{
				$datatombol = '
								<div class="btn-group">
									<button type="button" class="btn btn-primary dropdown-toggle btn-xs" data-toggle="dropdown"><i class="fa fa-gears fa-fw"></i> Aksi <span class="caret"></span></button>
									<ul class="dropdown-menu" role="menu">
									  <li><a href="javascript:void(0)" onclick="toPemberkasan('.$person->id_spk.', '.$person->id_penyedia.')"><i class="glyphicon glyphicon-file"></i> Pemberkasan </a></li>
									</ul>
								  </div>';
			}
			
			if(@$person->cdb == @$person->mdb || @$person->mdb == null){				
				$row[] = $datatombol;
			}else{				
				if($person->mdb == $this->session->userdata('id_pegawai')){
					$row[] = $datatombol;
				}else if($this->session->userdata('id_penyedia') == $person->id_penyedia){
					$row[] = $datatombol;
				}else{
					$row[] = '';
				}
			}
						  
			$file = (!empty($person->file)) ?  '<br><a href="'.base_url().'assets/file/kontrak/'.$person->file.'" target="_blank"><img class="imageThumb" src="'.base_url().'assets/file/kontrak/icon_pdf.png" title="Cover Kontrak"/></a>' : '';
			$row[] = $data_spk;
			$row[] = $nama_penyedia . $file;
			$row[] = GetFullDateFull($person->tgl_pekerjaan);
			//add html for action
		
			$data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->spk->count_filtered(),
						"recordsFiltered" => $this->spk->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}
	
	public function ajax_form_old()
	{		
		$content['class_name'] = get_class($this);	
		
		$post = $this->input->post();
		$id_spk = (!empty($post['id_spk'])) ? $post['id_spk'] : '';
		$nip_session = $this->session->userdata('nip');
		// var_dump($data_kegiatan);exit;
		
		//ini apabila mau menampilkan data untuk diedit
		if(!empty($id_spk)){
			$data_spk = $this->spk->get_by_id($id_spk);
			$content['data_spk'] = $data_spk;
			$tahun = date('Y', strtotime($data_spk->tgl_pekerjaan));			
			// $data_kegiatan = getKegiatanPPTK($nip_session, $tahun);
					
			$arr_data_kegiatan = $this->setting->get($start = null, $length = null, $sort = null, $order = null, $where = array('id_pegawai_pptk'=> $this->session->userdata('id_pegawai')), $like = null, $table='t_setting_pejabat', $field='*', $row_array=true, $join=false, $group_by=false);
			
			if(!empty($arr_data_kegiatan)){
				$data_kegiatan[] = json_decode($arr_data_kegiatan['json_sub_kegiatan'], true);
			}else{
				$data_kegiatan = '';
			}
			$content['data_kegiatan'] = getformselect_Kegiatan($data_kegiatan, 'id_prokeg_skpd', 'uraian', 'id_kegiatan', $data_spk->id_prokeg_aktif);
			$content['data_paket'] = formselect('m_paket', 'id_paket', 'nama_paket', '', $data_spk->id_paket);			
			$content['tahap_kegiatan'] = formselect('m_tahap_kegiatan', 'tahap_kegiatan', 'nama_tahap','', $data_spk->tahap_kegiatan);			
			
			//get data pencairan
			$data_pencairan = $this->pencairan->get_by_id_spk($id_spk);
			$content['nama_penyedia'] = getformselect_penyedia('m_penyedia','id_penyedia','nama_penyedia', 'IF(kategori = 1, nama_perusahaan, nama_penyedia) as nama_penyedia2', 'status = 1', false, @$data_pencairan->id_penyedia);
			$content['data_pencairan'] = $data_pencairan;
			
			
		}else{
			$tahun = date('Y');	
			// $data_kegiatan = getKegiatanPPTK($nip_session, $tahun);
			$arr_data_kegiatan = $this->setting->get($start = null, $length = null, $sort = null, $order = null, $where = array('id_pegawai_pptk'=> $this->session->userdata('id_pegawai')), $like = null, $table='t_setting_pejabat', $field='*', $row_array=true, $join=false, $group_by=false);
			
			if(!empty($arr_data_kegiatan)){
				$data_kegiatan[] = json_decode($arr_data_kegiatan['json_sub_kegiatan'], true);
			}else{
				$data_kegiatan = '';
			}
			
			$content['data_paket'] = formselect('m_paket', 'id_paket', 'nama_paket');			
			$content['tahap_kegiatan'] = formselect('m_tahap_kegiatan', 'tahap_kegiatan', 'nama_tahap', 'WHERE MONTH(start) <="'.date('m').'" AND MONTH(finish) >="'.date('m').'"');			
			$content['data_kegiatan'] = getformselect_Kegiatanv2($data_kegiatan, 'id_prokeg_skpd', 'uraian_prokeg', 'id_kegiatan');
			$content['nama_penyedia'] = getformselect_penyedia('m_penyedia','id_penyedia','nama_penyedia','IF(kategori = 1, nama_perusahaan, nama_penyedia) as nama_penyedia2', 'status = 1');
		}		
		$content['content'] = $this->load->view('spk/form',@$content);
	}
	
	public function ajax_form()
	{		
		$content['class_name'] = get_class($this);	
		
		$post = $this->input->post();
		$id_spk = (!empty($post['id_spk'])) ? $post['id_spk'] : '';
		$nip_session = $this->session->userdata('nip');
		$id_surat = (!empty($post['id_surat'])) ? $post['id_surat'] : false;
		$id_penyedia = (!empty($post['id_penyedia'])) ? $post['id_penyedia'] : false;
		// var_dump($data_kegiatan);exit;
		
		//ini apabila mau menampilkan data untuk diedit
		if(!empty($id_spk)){
			$data_spk = $this->spk->get_by_id($id_spk);

			// echo '<pre>';
			// var_dump($data_spk);
			// exit;
			$content['data_spk'] = $data_spk;
			$tahun = date('Y', strtotime($data_spk->tgl_pekerjaan));			
			// $data_kegiatan = getKegiatanPPTK($nip_session, $tahun);
					
			$arr_data_kegiatan = $this->setting->get($start = null, $length = null, $sort = null, $order = null, $where = array('(id_pegawai_pptk = "'.$this->session->userdata('id_pegawai').'" or id_pegawai_ppk = "'.$this->session->userdata('id_pegawai').'")'=> NULL, 'id_prokeg_skpd'=> $data_spk->id_prokeg_aktif), $like = null, $table='t_setting_pejabat', $field='*', $row_array=false, $join=false, $group_by=false);
			
			
			$datax = array();
			if(!empty($arr_data_kegiatan)){
				foreach($arr_data_kegiatan as $rss)
				{
					$datax = json_decode($rss['json_sub_kegiatan'], true);					

					if(!empty($datax[0]['id_prokeg_skpd']))
					{
						foreach($datax as $rop)
						{							
							$data_kegiatan[] = $rop;
						}
					}else{						
						$data_kegiatan[] = $datax;
					}
				}
			}else{
				$data_kegiatan = '';
			}

			$content['data_kegiatan'] = getformselect_Kegiatan($data_kegiatan, 'id_prokeg_skpd', 'uraian', 'id_kegiatan', $data_spk->id_prokeg_aktif);
			$content['data_paket'] = formselect('m_paket', 'id_paket', 'nama_paket', '', $data_spk->id_paket);			
			$content['tahap_kegiatan'] = formselect('m_tahap_kegiatan', 'tahap_kegiatan', 'nama_tahap','', $data_spk->tahap_kegiatan);			
			
			//get data pencairan
			$data_pencairan = $this->pencairan->get_by_id_spk($id_spk);
			
			$content['nama_penyedia'] = getformselect_penyedia('m_penyedia','id_penyedia','nama_penyedia', 'IF(kategori = 1, nama_perusahaan, nama_penyedia) as nama_penyedia2', 'status = 1', false, @$data_pencairan->id_penyedia);
			$content['data_pencairan'] = $data_pencairan;
			
			
		}else{
			$tahun = date('Y');	
			// $data_kegiatan = getKegiatanPPTK($nip_session, $tahun);
			$arr_data_kegiatan = $this->setting->get($start = null, $length = null, $sort = null, $order = null, $where = array('(id_pegawai_pptk = "'.$this->session->userdata('id_pegawai').'" or id_pegawai_ppk = "'.$this->session->userdata('id_pegawai').'")'=> NULL, 'active'=> 1), $like = array('json_sub_kegiatan'=> '"tahun":"'.date('Y').'"'), $table='t_setting_pejabat', $field='*', $row_array=false, $join=false, $group_by=false);

			// echo '<pre>';
			// var_dump($this->db->last_query());
			// exit;

			

			$datax = array();
			if(!empty($arr_data_kegiatan)){
				foreach($arr_data_kegiatan as $rss)
				{
					$datax = json_decode($rss['json_sub_kegiatan'], true);					

					if(!empty($datax[0]['id_prokeg_skpd']))
					{
						foreach($datax as $rop)
						{							
							$data_kegiatan[] = $rop;
						}
					}else{						
						$data_kegiatan[] = $datax;
					}
				}
			}else{
				$data_kegiatan = '';
			}
			
			$content['data_paket'] = formselect('m_paket', 'id_paket', 'nama_paket');			
			$content['tahap_kegiatan'] = formselect('m_tahap_kegiatan', 'tahap_kegiatan', 'nama_tahap', 'WHERE MONTH(start) <="'.date('m').'" AND MONTH(finish) >="'.date('m').'"');			
			$content['data_kegiatan'] = getformselect_Kegiatanv2($data_kegiatan, 'id_prokeg_skpd', 'uraian_prokeg', 'id_kegiatan');
			
			$content['nama_penyedia'] = getformselect_penyedia('m_penyedia','id_penyedia','nama_penyedia','IF(kategori = 1, nama_perusahaan, nama_penyedia) as nama_penyedia2', 'status = 1');
		}
		
		if($id_surat)
		{
			$datapenyedia = getformselect_penyedia('m_penyedia','id_penyedia','nama_penyedia', 'IF(kategori = 1, nama_perusahaan, nama_penyedia) as nama_penyedia2', 'status = 1', false, @$id_penyedia);
			$content['nama_penyedia'] = $datapenyedia;

			
			$get_surat_pengantar = $this->inbox->get($start = null, $length = null, $sort = null, $order = null, $wherex = array('a.id_surat'=> $id_surat, 'a.active'=> 1), $like = null, $table='t_surat a', $field='a.id_surat, a.id_parent', $row_array=true, $join=false);

			$get_surat_notdin_pemenang = $this->inbox->get($start = null, $length = null, $sort = null, $order = null, $wherex = array('a.id_parent'=> $get_surat_pengantar['id_parent'], 'a.id_bentuk'=> 2, 'a.active'=> 1), $like = null, $table='t_surat a', $field='a.data_penyedia, a.id_parent', $row_array=true, $join=false);

			$get_surat_spt = $this->inbox->get($start = null, $length = null, $sort = null, $order = null, $wherex = array('a.id_surat'=> $get_surat_pengantar['id_parent'], 'a.active'=> 1), $like = null, $table='t_surat a', $field='a.id_surat, a.id_parent', $row_array=true, $join=false);

			$get_surat_permohonan = $this->inbox->get($start = null, $length = null, $sort = null, $order = null, $wherex = array('a.id_surat'=> $get_surat_spt['id_parent'], 'a.active'=> 1), $like = null, $table='t_surat a', $field='a.id_surat, a.id_parent, a.idrup_penyedia, a.json_rup_penyedia, a.waktu_pelaksanaan', $row_array=true, $join=false);

			$json_rup = json_decode($get_surat_permohonan['json_rup_penyedia'], true);

			$data_penyedia = json_decode($get_surat_notdin_pemenang['data_penyedia'], true);
			
			$get_paket = $this->setting->get($start = null, $length = null, $sort = null, $order = null, $where = array('a.nama_paket'=> $json_rup['JENIS']), $like = null, $table='m_paket a', $field='a.id_paket', $row_array=true, $join=false, $group_by=false);


			$get_data_penyedia = $this->setting->get($start = null, $length = null, $sort = null, $order = null, $where = array('a.id_penyedia'=> $id_penyedia), $like = null, $table='m_penyedia a', $field='a.id_penyedia, a.npwp', $row_array=true, $join=false, $group_by=false);

			$data_pencairan = array();
			$tenaga_ahli = array();
			foreach($data_penyedia as $rsx)
			{
				if($rsx['npwp'] == $get_data_penyedia['npwp'])
				{
					// data_pencairan->nominal_bayar
					$data_pencairan['nominal_bayar'] = str_replace(',','.', str_replace('.','',$rsx['negosiasi']));
					
					$tenaga_ahli = json_encode($rsx);
					break;
				}
			}

			$data_spk['data_tenagaahli'] = $tenaga_ahli;
			$data_spk['nama_pekerjaan'] = $json_rup['PAKET'];
			$data_spk['waktu_pelaksanaan'] = $get_surat_permohonan['waktu_pelaksanaan'];
			$content['data_spk'] = (object) $data_spk;
			$content['data_pencairan'] = (object) $data_pencairan;
			$content['data_paket'] = formselect('m_paket', 'id_paket', 'nama_paket', '', $get_paket['id_paket']);			
		}
		$content['content'] = $this->load->view('spk/form',@$content);
	}
	
	public function ajax_save_pencairan($id_spk=''){
		$post = $this->input->post();
			
		
		// Data Penyedia
		$data_penyedia = $this->penyedia->get_by_id($post['id_penyedia'], 'id_penyedia,nama_penyedia,alamat,bank,no_rekening_penyedia,atas_nama_rekening,npwp,cabang_bank'); //get data penyedia
		$data_penyedia_convert = (array)$data_penyedia; // conver array stdClass to Array
		
		// Data SPK
		$data_spk = $this->spk->get_by_id($id_spk, 'id_spk,id_prokeg_aktif'); //get data SPK
		$data_spk_convert = (array)$data_spk; // conver array stdClass to Array		
		
		$data['nominal_bayar'] = str_replace(',','',$post['nominal_bayar']);
		
		
		// if($this->session->userdata('id_pegawai') == '41057'){
			// echo '<pre>';
			// var_dump($post['nominal_bayar']);
			// exit;
		// }
		$data['nominal_bayar_terbilang'] = terbilang_rupiah(str_replace(',','',$post['nominal_bayar']));
		$data['pekerjaan_termin'] = $post['pekerjaan_termin'];
		$data['id_penyedia'] = $post['id_penyedia'];
		
		$gabung = array_merge($data, $data_penyedia_convert, $data_spk_convert);
		
		$id = (!empty($post['id'])) ? $post['id'] : '';
		
		if(!empty($id)){
			$created = array(
				'mdb'=> $this->session->userdata('id_pegawai'),
				'mdi' => 'web'
			);
		}else{
			$created = array(
				'cdb'=> $this->session->userdata('id_pegawai'),
				'cdi' => 'web'
			);
		}
		
		$result = $this->pencairan->save($id, $gabung, $created);
	}
	
	function ajax_get_data_rek_sipd($id_prokeg_skpd=false)
	{
		$get_json = $this->inbox->get($start = null, $length = null, $sort = null, $order = null, $where = array('id_prokeg_skpd'=> $id_prokeg_skpd), $like = null, $table='t_setting_pejabat a', $field='a.json_sub_kegiatan', $row_array=true, $join=false);
		$decode_sub = json_decode($get_json['json_sub_kegiatan'], true);
		
		$data = getDataHirarki($decode_sub);
		
		if(!empty($data)){
		  $content['dpa_skpd'] = $data[1]['kode_path_sipd'];
		  $content['kode_rek'] = $data[0]['kode_path_sipd'];
		  $content['status'] = true;
		  $content['coba'] = $decode_sub;
		}else{		
		  	$content['status'] = false;
		}
		
		
		echo json_encode($content);
	}
	
	public function ajax_save()
	{
		$this->_validate();
		$post = $this->input->post();
		
		if(empty($post['data_rincian_input']))
		{			
		  $content['status'] = false;
		  $content['notif'] = '<div class="alert alert-danger" role="alert"><i class="fa fa-thumbs-o-down fa-fw"></i> Rincian Detail Program Kegiatan Harus di Pilih!</div>';
		  echo json_encode($content);
		  exit;
		}
		
		if(str_replace(',','',$post['nominal_bayar']) == 0)
		{			
		  $content['status'] = false;
		  $content['notif'] = '<div class="alert alert-danger" role="alert"><i class="fa fa-thumbs-o-down fa-fw"></i> Nominal Biaya Kontrak tidak boleh nol!</div>';
		  echo json_encode($content);
		  exit;
		}

		if(strtotime($post['tgl_mulai']) > strtotime($post['tgl_akhir']))
		{			
		  $content['status'] = false;
		  $content['notif'] = '<div class="alert alert-danger" role="alert"><i class="fa fa-thumbs-o-down fa-fw"></i> Tanggal Mulai Kontrak Tidak Boleh Lebih Dari Tanggal Akhir Kontrak!</div>';
		  echo json_encode($content);
		  exit;
		}
		
		
		$data = $post;
		unset($data['id'], $data['id_kegiatan'], $data['data_rincian_input'], $data['nominal_bayar'], $data['pekerjaan_termin'], $data['id_penyedia'], $data['tenaga_ahli']); //hapus
		
		
		$id = (!empty($post['id'])) ? $post['id'] : '';
		$id_kegiatan = (!empty($post['id_kegiatan'])) ? $post['id_kegiatan'] : '';
		$tgl_pekerjaan = date('Y-m-d', strtotime($post['tgl_pekerjaan']));
		
		$tgl_mulai = date('Y-m-d', strtotime($post['tgl_mulai']));
		$tgl_akhir = date('Y-m-d', strtotime($post['tgl_akhir']));
		// $data_nama_kegiatan = $this->kegiatan->get_by_id($id_kegiatan, 'nama_kegiatan');
		// $nama_kegiatan = (!empty($data_nama_kegiatan)) ? $data_nama_kegiatan->nama_kegiatan : '';
		$data_rincian_input = (!empty($post['data_rincian_input'])) ? $post['data_rincian_input'] : '';
		$tenaga_ahli = (!empty($post['tenaga_ahli'])) ? $post['tenaga_ahli'] : null;
		
		$pagu = str_replace(',','',$post['pagu']);
		$nominal_bayar = str_replace(',','',$post['nominal_bayar']);
		
		$kode_unor = $this->session->userdata('kode_unor');
		//marge memperbarui pola format tanggal dari form input
		$data['tgl_pekerjaan'] = $tgl_pekerjaan;
		$data['tgl_mulai'] = $tgl_mulai;
		$data['tgl_akhir'] = $tgl_akhir;
		// $data['nama_kegiatan'] = $nama_kegiatan;
		$data['pagu'] = $pagu;
		
				
		if($nominal_bayar > $pagu){
			$content['status'] = false;
			$content['notif'] = '<div class="alert alert-danger" role="alert"><i class="fa fa-thumbs-o-down fa-fw"></i> Data Nominal Biaya Kontrak Lebih dari Pagu!</div>';
			echo json_encode($content);
			return false;
		}
		
		$data_json = '';
		if(json_decode($_POST['id_kegiatan'], true))
		{
			$data_json = json_decode($_POST['id_kegiatan'], true);
			
			$nama_kegiatan = $data_json['uraian_prokeg'];
			$id_prokeg_aktif = $data_json['id_prokeg_skpd'];
			
			//marge memperbarui pola format tanggal dari form input
			$data['nama_kegiatan'] = $nama_kegiatan;
			$data['id_prokeg_aktif'] = $id_prokeg_aktif;
			//marge memperbarui pola format tanggal dari form input
		}
		
		
		if(!empty($id)){
			$created = array(
				'mdb'=> $this->session->userdata('id_pegawai'),
				'mdi' => 'web'
			);
		}else{
			$created = array(
				'cdb'=> $this->session->userdata('id_pegawai'),
				'kode_unor'=> $kode_unor,
				'cdi' => 'web'
			);
		}
		
		if(!empty($_FILES['file']['name']))
		{
			$upload = $this->_do_upload('file');
			$data['file'] = $upload;
		}
		
		
		// action to save surat
		$data['data_tenagaahli'] = $tenaga_ahli;
		$result = $this->spk->save($id, $data, $created);
		
		if($result){
			
			//mengambil data awal
			$list_data = $this->rincian->get_by_id_multi($id, 'id_prokeg_sub_hir');
			$data_awal = array();
			$data_baru = array();
			foreach($list_data as $row){
				$data_awal[] = $row['id_prokeg_sub_hir'];
			}
			
			
			//insert histori rincian kegiatan
			$get_hirarki = getDataHirarki($data_json);
			
			
			foreach($data_rincian_input as $row){
				$row = json_decode($row, true);
				$convert_data = $row;
				$convert_data['id_prokeg_sub_hir'] = $row['id'];
				$convert_data['id_prokeg_aktif'] = $data_json['id_prokeg_skpd'];
				$convert_data['id_spk'] = $result;	
				unset($convert_data['id'], $convert_data['create_date'], $convert_data['create_user'], $convert_data['last_edit_date'], $convert_data['last_edit_user'], $created['kode_unor']);		
				
				// $cek_update = $this->rincian->update(array('id_spk'=> $id, 'id_prokeg_sub_hir'=> $row['id'], 'status'=> 0), $convert_data);
				// var_dump($cek_update);
				// exit;
				$cek_data_ready = $this->rincian->get_where(array("id_prokeg_sub_hir"=> $row['id'], "id_spk"=> $id, "status"=> 1));
				$convert_data['data_prokeg_json'] = json_encode($get_hirarki);
								
				if($cek_data_ready){
					$resultt = $this->rincian->update(array('id_spk'=> $id, 'id_prokeg_sub_hir'=> $row['id'], 'status'=> 0), $convert_data);
					
				}else{
					$cek_data_ready = $this->rincian->get_where(array("id_prokeg_sub_hir"=> $row['id'], "id_spk"=> $id, "status"=> 1));
					$convert_data['status'] = 1;
					
					if(!$cek_data_ready){
						$resultt = $this->rincian->save('', $convert_data, $created);
					}else{
						$resultt = $this->rincian->update(array('id_spk'=> $id, 'id_prokeg_sub_hir'=> $row['id'], 'status'=> 1), $convert_data);
					}
				}
				
				$data_baru[] = $row['id'];
			}
			//insert histori rincian kegiatan
			
			
			//menghapus data yg telah di ceklist namun di unceklist
			foreach($data_awal as $row)
			{
				if(in_array($row, $data_baru))
				{
					
				}else{
					$where = array("id_prokeg_sub_hir" => $row, "id_spk" => $id);
					$datar = array("status" => 0);
					$this->rincian->update($where, $datar);
				}
			}			
			
			
			//insert ke t_pencairan
			$result_pencairan = $this->ajax_save_pencairan($result);
			//insert ke t_pencairan
			
			
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

		if($this->input->post('no_spk') == '')
		{
			$data['inputerror'][] = 'no_spk';
			$data['error_string'][] = 'No SPK harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('nama_pekerjaan') == '')
		{
			$data['inputerror'][] = 'nama_pekerjaan';
			$data['error_string'][] = 'Nama Pekerjaan harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('tahap_kegiatan') == '')
		{
			$data['inputerror'][] = 'tahap_kegiatan';
			$data['error_string'][] = 'Tahap Kegiatan harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('id_kegiatan') == '')
		{
			$data['inputerror'][] = 'id_kegiatan';
			$data['error_string'][] = 'Nama Kegiatan harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('tgl_pekerjaan') == '')
		{
			$data['inputerror'][] = 'tgl_pekerjaan';
			$data['error_string'][] = 'Tanggal pekerjaan harus diisi';
			$data['status'] = FALSE;
		}
		
		// if($this->input->post('dpa_skpd') == '')
		// {
			// $data['inputerror'][] = 'dpa_skpd';
			// $data['error_string'][] = 'Nomor DPA SKPD harus diisi';
			// $data['status'] = FALSE;
		// }
		
		// if($this->input->post('kode_rek') == '')
		// {
			// $data['inputerror'][] = 'kode_rek';
			// $data['error_string'][] = 'Kode Rekening SKPD harus diisi';
			// $data['status'] = FALSE;
		// }
		
		
		if($this->input->post('id_penyedia') == '')
		{
			$data['inputerror'][] = 'id_penyedia';
			$data['error_string'][] = 'Nama Penyedia harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('pekerjaan_termin') == '')
		{
			$data['inputerror'][] = 'pekerjaan_termin';
			$data['error_string'][] = 'Pekerjaan Termin harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('nominal_bayar') == '')
		{
			$data['inputerror'][] = 'nominal_bayar';
			$data['error_string'][] = 'Nominal Biaya harus diisi';
			$data['status'] = FALSE;
		}
		
		if($this->input->post('id_paket') == '')
		{
			$data['inputerror'][] = 'id_paket';
			$data['error_string'][] = 'Jenis Paket harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('waktu_pelaksanaan') == '')
		{
			$data['inputerror'][] = 'waktu_pelaksanaan';
			$data['error_string'][] = 'Waktu Pelaksanaan harus diisi';
			$data['status'] = FALSE;
		}
		
		if($this->input->post('type_kontrak') == '')
		{
			$data['inputerror'][] = 'type_kontrak';
			$data['error_string'][] = 'Type Kontrak harus diisi';
			$data['status'] = FALSE;
		}
		
		if($this->input->post('cara_pembayaran') == '')
		{
			$data['inputerror'][] = 'cara_pembayaran';
			$data['error_string'][] = 'Cara Pembayaran harus diisi';
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
		
		$hasil = $this->spk->update(array('id_spk' => $id), $data);
		$hasil_pencairan = $this->pencairan->updatev2(array('id_spk' => $id), $data);
		$rincian_detail = $this->rincian->delete_by_id($id);
		if($hasil > 0){
			$content['status'] = true;
			$content['notif'] = '<div class="alert alert-success" role="alert"><i class="fa fa-thumbs-o-up fa-fw"></i> Data berhasil di hapus</div>';			

		}else{
		  $content['status'] = false;
		  $content['notif'] = '<div class="alert-modal alert-danger" role="alert"><i class="fa fa-thumbs-o-down fa-fw"></i> Data gagal dihapus !</div>';
		}
		
		
		echo json_encode($content);
	}	
	
	private function _do_upload($nameFile='')
	{
		$config['upload_path']          = 'assets/file/kontrak';
        $config['allowed_types']        = 'pdf';
        $config['max_size']             = 2000; //set max size allowed in Kilobyte
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
	
}
