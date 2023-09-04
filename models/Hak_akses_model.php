<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hak_akses_model extends CI_Model {

	/*
	 * @author     		RENDY
	 * @author_email    rendyardian199@gmail.com
	 *  KOTA TANGERANG 
	 */

	var $table = 'm_akses a';
	public function __construct()
	{
		parent::__construct();
		// $this->load->database();		
	}	
	
	public function selectListUserGroup()   {      

		$sql = "SELECT a.id_akses, a.nama_akses FROM ".$this->table." ORDER BY a.nama_akses";									
		$query = $this->db_ba->query($sql);

		$rs = $query->result_array();	

		if($query->num_rows()>0){
			return $rs;
		}else{
			return false;
		}			
	}  	
	
	public function selectListAuthGroup($group_id=''){      

		$cond = "";
		
		if(!empty($group_id)){
			$cond = " WHERE id_akses=".$group_id." ";
		}

		$sql = "SELECT id_menu FROM cms_menu_auth ".$cond;									
		$query = $this->db_ba->query($sql);

		$rs = $query->result_array();	

		if($query->num_rows()>0){
			
			$arr_auth = array();
			foreach($rs as $row){
				$arr_auth[] = $row['id_menu'];
			}
			
			return $arr_auth;			
			
		}else{
			return array();
		}			
	}  
	
	public function selectListMenu(){        	

		$sql = "SELECT * FROM cms_menu WHERE visible=1 AND id_parent=0 ORDER BY sort ASC";									
		$query = $this->db_ba->query($sql);

		$rs = $query->result_array();	

		if($query->num_rows()>0){
			return $rs;
		}else{
			return false;
		}			
	}   
  
	public function selectListMenuChild($id_menu){        	

		$sql = "SELECT * FROM cms_menu WHERE visible=1 AND id_parent=".$id_menu." ORDER BY sort ASC";									
		$query = $this->db_ba->query($sql);

		$rs = $query->result_array();	

		if($query->num_rows()>0){
			return $rs;
		}else{
			return false;
		}			
	}  
		 
	public function save_auth($status=false,$group_id='',$id_menu='') 
	{    					
		// check if dataexist already exist		
		if($status=='true'){
			
			$id_auth = getID('id_auth','cms_menu_auth');		
			$sql = "INSERT INTO cms_menu_auth (id_auth,id_akses,id_menu) VALUES (".$id_auth.",".$group_id.",".$id_menu.")";$this->db_ba->query($sql);
		
		}else{
					
			$sql = "DELETE FROM cms_menu_auth WHERE id_akses=".$group_id." AND id_menu=".$id_menu." ";
			$this->db_ba->query($sql);
		
		}	
		
	}  

}
