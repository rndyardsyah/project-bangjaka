<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hak_akses extends CI_Controller {
	
	/*
	 * @author     		RENDY
	 * @author_email    rendyardian199@gmail.com
	 *  KOTA TANGERANG 
	 */

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Hak_akses_model', 'hak_akses');	
		$this->db_ba = $this->load->database('db_pembayaran_ls', TRUE);
		if($this->session->userdata('login')==false){
			redirect(base_url('site/login'));
		}
		
	}
	
	public function index()
	{
		$content['class_name'] = get_class($this);	
		
		
		$content['selectListUserGroup'] = $this->selectListUserGroup();		
		$content['content'] = $this->load->view('hak_akses/index',@$content,true);
		$this->load->view('ba/index', @$content,false,false);
	}
	
	
  
  function selectListUserGroup(){
  
  		$result = $this->hak_akses->selectListUserGroup();	
  
		if($result){		
			
			$slc = '<select class="form-control selectpicker show-tick" name="id_akses" id="id_akses" data-live-search="true">';			
			$slc .= '<option value="">Silahkan Pilih</option>';
			
			foreach($result as $row){				
				$slc .='<option value="'.$row['id_akses'].'" >'.$row['nama_akses'].'</option>';	
			}			
			$slc.='</select>';
			return $slc;			
			
		}
  
  }
  
  
  function auth_group($group_id=''){  
  
	$content['class_name'] = get_class($this);	
  	 if(empty($group_id)) return false;
    	
	  // get auth / only checked	
	  $id_menu_group = $this->hak_akses->selectListAuthGroup($group_id);		
	
	  // ALL MENU	
	  $td = '';	
	  
	  $menuParent = $this->hak_akses->selectListMenu();	
	  if($menuParent){
	  
			foreach($menuParent as $row){											
				$count = is_array($id_menu_group) ? count($id_menu_group) : 1;
				$checked = ($count > 1 && in_array($row['id_menu'],$id_menu_group)) ? 'checked="checked"' : '' ;			
			
				$td .= '<tr><td><input type="checkbox" name="id_menu" class="id_menu" value="'.$row['id_menu'].'" '.$checked.'></td>';
				$td .= '<td><i class="'.$row['icon'].'"></i> '.$row['menu'].'</td></tr>';
				
				$td .= $this->childMenu($group_id,$row['id_menu']);
				
			}				
		
		}		
		
		$content['list_menu'] = $td;	  
	  	
		$content['content'] = $this->load->view('hak_akses/auth_group',@$content);
  }
    
  function childMenu($group_id, $id_menu=''){
  
  	  // get auth / only checked	
	  $id_menu_group = $this->hak_akses->selectListAuthGroup($group_id);		
  	
	
	 // ALL MENU	
	  $td = '';			
  
	  $menuChild = $this->hak_akses->selectListMenuChild($id_menu);	
	  if($menuChild){									
			
			foreach($menuChild as $row){		
					
				$count = is_array($id_menu_group) ? count($id_menu_group) : 1;
				$checked = ($count > 1 && in_array($row['id_menu'],$id_menu_group)) ? 'checked="checked"' : '' ;				
			
				$td .= '<tr><td><input type="checkbox" name="id_menu" class="id_menu" value="'.$row['id_menu'].'" '.$checked.'></td>';
				$td .= '<td style="padding-left:30px;"><i class="'.$row['icon'].'"></i> '.$row['menu'].'</td></tr>';
				
				$td .= $this->childMenu($group_id,$row['id_menu']);
				
			}				
		
		}		
		
		return $td;
  
  }
    
  function save_auth($status=false, $group_id, $id_menu){    
		$result = $this->hak_akses->save_auth($status, $group_id, $id_menu);	
  }
	
}
