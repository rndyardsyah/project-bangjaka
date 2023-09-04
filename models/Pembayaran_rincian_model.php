<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pembayaran_rincian_model extends CI_Model {

	/*
	 * @author     		RENDY
	 * @author_email    rendyardian199@gmail.com
	 *  KOTA TANGERANG 
	 */

	var $table = 't_pembayaran_rincian';

	public function __construct()
	{
		parent::__construct();
		// $this->load->database();		
	}
	
	public function save($id=false,$data=array(),$created=array()) 
	{    
		$where = array('id_pembayaran_rinci' => $id);
		$this->db_ba
		->where($where)
		->from($this->table);
		$exist = $this->db_ba->get()->row();	
				
		$id_pembayaran_rinci = ($exist) ? $id :  getID('id_pembayaran_rinci', $this->table);
		
		$prop = array('id_pembayaran_rinci'=> $id_pembayaran_rinci);				
		
		$data = array_merge($prop,$data);	
		
		//check for data set for escaped field
		$data = $this->_check_escaped_data_set($data);	
		
		// check if dataexist already exist
		if($exist)			
		{	
			$this->db_ba->where('id_pembayaran_rinci' , $id_pembayaran_rinci);
			$data = array_merge($data,array('mdd'=> date('Y-m-d H:i:s')),$created);
			$this->db_ba->update($this->table, $data);
			$result = $id_pembayaran_rinci;
		}
		else					
		{
			$data = array_merge($data,array('cdd'=> date('Y-m-d H:i:s')),$created);		
			$this->db_ba->insert($this->table, $data);
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
	
	public function get_by_id($id, $field = '*', $where = false)
	{
		$this->db_ba->select($field);
		$this->db_ba->from($this->table);
		// $this->db_ba->join('rincian_detail_spk b', 'a.id_rincian_detail_spk = b.id');
		$this->db_ba->where('id_pembayaran',$id);
		// $this->db_ba->where('b.status', 1);
		// $this->db_ba->where('status',1);
		if($where){
			$this->db_ba->where_in('id_rincian_detail_spk',$where);
		}
		$this->db_ba->order_by('id_rincian_detail_spk','ASC');
		$query = $this->db_ba->get();
		// var_dump($this->db_ba->last_query());exit;
		return $query->result_array();
	}
	
	public function get_by_idv2($id, $field = '*', $where = false)
	{
		$this->db_ba->select($field);
		$this->db_ba->from($this->table.' a');
		$this->db_ba->join('rincian_detail_spk b', 'a.id_rincian_detail_spk = b.id');
		$this->db_ba->where('a.id_pembayaran',$id);
		$this->db_ba->where('b.status', 1);
		// $this->db_ba->where('status',1);
		if($where){
			$this->db_ba->where_in('a.id_rincian_detail_spk',$where);
		}
		$this->db_ba->order_by('a.id_rincian_detail_spk','ASC');
		$query = $this->db_ba->get();
		// var_dump($this->db_ba->last_query());exit;
		return $query->result_array();
	}
	
	public function update($where, $data)
	{
		$this->db_ba->update($this->table, $data, $where);
		return $this->db_ba->affected_rows();
	}
		
	function get_where($where='', $field='*'){
		$this->db_ba->select($field);
		
		$query = $this->db_ba->get_where($this->table, $where);
		return $query->result_array(); 
	}

}
