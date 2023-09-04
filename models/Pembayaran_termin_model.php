<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pembayaran_termin_model extends CI_Model {

	/*
	 * @author     		RENDY
	 * @author_email    rendyardian199@gmail.com
	 *  KOTA TANGERANG 
	 */

	var $table = 't_pembayaran_termin';
	
	public function __construct()
	{
		parent::__construct();
		// $this->load->database();		
	}
	
	public function save($id=false,$data=array(),$created=array()) 
	{    
		$where = array('id_pembayaran_termin' => $id);
		$this->db_ba
		->where($where)
		->from($this->table);
		$exist = $this->db_ba->get()->row();	
				
		$id_pembayaran_termin = ($exist) ? $id :  getID('id_pembayaran_termin', $this->table);
		
		$prop = array('id_pembayaran_termin'=> $id_pembayaran_termin);				
		
		$data = array_merge($prop,$data);	
		
		//check for data set for escaped field
		$data = $this->_check_escaped_data_set($data);	
		
		// check if dataexist already exist
		if($exist)			
		{	
			$this->db_ba->where('id_pembayaran_termin' , $id_pembayaran_termin);
			$data = array_merge($data,array('mdd'=> date('Y-m-d H:i:s')),$created);
			$this->db_ba->update($this->table, $data);
			$result = $id_pembayaran_termin;
		}
		else					
		{
			$data = array_merge($data,array('cdd'=> date('Y-m-d H:i:s')),$created);	
			$this->db_ba->insert($this->table,$data);			
			$result = $this->db_ba->insert_id();
		}
		return $result;
	}
	
	public function _check_escaped_data_set($data=array()) 
	{
		foreach($data as $key => $value)
		{
			if(is_array($value))
			{
				$escaped = $value;
				unset($data[$key]);
				
				foreach($escaped as $field => $val)
				{
					if($key == 'date')
					{
						$this->db_ba->set($field,"STR_TO_DATE('".$val."','%d-%m-%Y')",false);
					}else
					{
						$this->db_ba->set($field,$val,false);
					}
				}
			}
		}
		return $data;
	}
	
	public function get_by_id($id_pencairan, $termin)
	{
		$this->db_ba->select('biaya');
		$this->db_ba->from($this->table);
		$this->db_ba->where('id_pencairan', $id_pencairan);
		$this->db_ba->where('termin', $termin);
		$this->db_ba->order_by('cdd', 'DESC');
		$query = $this->db_ba->get();

		$result = $query->row();
		return $result->biaya;
	}	

}
