<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prokeg_sub_hir_model extends CI_Model {

    public function __construct(){
        parent::__construct();
    }

    public function get_by_id($id){
        $this->db_ba->from('prokeg_sub_hir');
        $this->db_ba->where('id',$id);
        $query = $this->db_ba->get();
        return $query->row();
    }

    public function get_by_id_prokeg_aktif($id_prokeg_aktif){
        $this->db_ba->from('prokeg_sub_hir');
        $this->db_ba->where('id_prokeg_aktif',$id_prokeg_aktif);
        $this->db_ba->where('status','1');
        $query = $this->db_ba->get();
        return $query->result();
    }

    public function get_child($id_parent){
        $this->db_ba->from('prokeg_sub_hir');
		$this->db_ba->where('prokeg_sub_hir.id_parent', $id_parent);
		$query = $this->db_ba->get();
		return $query->result();
	}

    public function get_pagu($id_prokeg_aktif){
		$this->db_ba->select('
            SUM(prokeg_sub_hir.pagu) as pagu,
            SUM(prokeg_sub_hir.rekeurp_01) as rekeurp_01,
            SUM(prokeg_sub_hir.rekeurp_02) as rekeurp_02,
            SUM(prokeg_sub_hir.rekeurp_03) as rekeurp_03,
            SUM(prokeg_sub_hir.rekeurp_04) as rekeurp_04,
            SUM(prokeg_sub_hir.rekeurp_05) as rekeurp_05,
            SUM(prokeg_sub_hir.rekeurp_06) as rekeurp_06,
            SUM(prokeg_sub_hir.rekeurp_07) as rekeurp_07,
            SUM(prokeg_sub_hir.rekeurp_08) as rekeurp_08,
            SUM(prokeg_sub_hir.rekeurp_09) as rekeurp_09,
            SUM(prokeg_sub_hir.rekeurp_10) as rekeurp_10,
            SUM(prokeg_sub_hir.rekeurp_11) as rekeurp_11,
            SUM(prokeg_sub_hir.rekeurp_12) as rekeurp_12
        ');
		$this->db_ba->from('prokeg_sub_hir');
		$this->db_ba->where('id_prokeg_aktif', $id_prokeg_aktif);
		$this->db_ba->where('id_parent', NULL);
		$query = $this->db_ba->get();
		return $query->row();
	}

    public function get_pagu_by_id($id){
		$this->db_ba->select('
            SUM(prokeg_sub_hir.pagu) as pagu,
            SUM(prokeg_sub_hir.rekeurp) as rekeurp,
            SUM(prokeg_sub_hir.rekeurp_01) as rekeurp_01,
            SUM(prokeg_sub_hir.rekeurp_02) as rekeurp_02,
            SUM(prokeg_sub_hir.rekeurp_03) as rekeurp_03,
            SUM(prokeg_sub_hir.rekeurp_04) as rekeurp_04,
            SUM(prokeg_sub_hir.rekeurp_05) as rekeurp_05,
            SUM(prokeg_sub_hir.rekeurp_06) as rekeurp_06,
            SUM(prokeg_sub_hir.rekeurp_07) as rekeurp_07,
            SUM(prokeg_sub_hir.rekeurp_08) as rekeurp_08,
            SUM(prokeg_sub_hir.rekeurp_09) as rekeurp_09,
            SUM(prokeg_sub_hir.rekeurp_10) as rekeurp_10,
            SUM(prokeg_sub_hir.rekeurp_11) as rekeurp_11,
            SUM(prokeg_sub_hir.rekeurp_12) as rekeurp_12
        ');
		$this->db_ba->from('prokeg_sub_hir');
		$this->db_ba->where('prokeg_sub_hir.id_parent', $id);
		$query = $this->db_ba->get();
		return $query->row();
	}

    public function get_refisikpersen($id_prokeg_aktif){
		$this->db_ba->select('
			prokeg_sub_hir.pagu as pagu,
			prokeg_sub_hir.refisikpersen as refisikpersen
		');
		$this->db_ba->from('prokeg_sub_hir');
		$this->db_ba->where('id_prokeg_aktif', $id_prokeg_aktif);
		$this->db_ba->where('id_parent', NULL);
		$query = $this->db_ba->get()->result();

		$refisikpersen = 0;
		foreach($query as $row){
			$refisikpersen += @($row->pagu/$this->get_pagu($id_prokeg_aktif)*$row->refisikpersen);
		}
		return $refisikpersen;
	}

    public function get_rencana_fisik($id_prokeg_aktif){
		$this->db_ba->select('
			prokeg_sub_hir.pagu as pagu,
			prokeg_sub_hir.fisik_01 as fisik_01,
			prokeg_sub_hir.fisik_02 as fisik_02,
			prokeg_sub_hir.fisik_03 as fisik_03,
			prokeg_sub_hir.fisik_04 as fisik_04,
			prokeg_sub_hir.fisik_05 as fisik_05,
			prokeg_sub_hir.fisik_06 as fisik_06,
			prokeg_sub_hir.fisik_07 as fisik_07,
			prokeg_sub_hir.fisik_08 as fisik_08,
			prokeg_sub_hir.fisik_09 as fisik_09,
			prokeg_sub_hir.fisik_10 as fisik_10,
			prokeg_sub_hir.fisik_11 as fisik_11,
			prokeg_sub_hir.fisik_12 as fisik_12
		');
        $this->db_ba->from('prokeg_sub_hir');
		$this->db_ba->where('id_prokeg_aktif', $id_prokeg_aktif);
		$this->db_ba->where('id_parent', NULL);
		$query = $this->db_ba->get()->result();

		$fisik_01 = 0;
		$fisik_02 = 0;
		$fisik_03 = 0;
		$fisik_04 = 0;
		$fisik_05 = 0;
		$fisik_06 = 0;
		$fisik_07 = 0;
		$fisik_08 = 0;
		$fisik_09 = 0;
		$fisik_10 = 0;
		$fisik_11 = 0;
		$fisik_12 = 0;

		foreach($query as $row){
			$fisik_01 += @($row->pagu/$this->get_pagu($id_prokeg_aktif)*$row->fisik_01);
			$fisik_02 += @($row->pagu/$this->get_pagu($id_prokeg_aktif)*$row->fisik_02);
			$fisik_03 += @($row->pagu/$this->get_pagu($id_prokeg_aktif)*$row->fisik_03);
			$fisik_04 += @($row->pagu/$this->get_pagu($id_prokeg_aktif)*$row->fisik_04);
			$fisik_05 += @($row->pagu/$this->get_pagu($id_prokeg_aktif)*$row->fisik_05);
			$fisik_06 += @($row->pagu/$this->get_pagu($id_prokeg_aktif)*$row->fisik_06);
			$fisik_07 += @($row->pagu/$this->get_pagu($id_prokeg_aktif)*$row->fisik_07);
			$fisik_08 += @($row->pagu/$this->get_pagu($id_prokeg_aktif)*$row->fisik_08);
			$fisik_09 += @($row->pagu/$this->get_pagu($id_prokeg_aktif)*$row->fisik_09);
			$fisik_10 += @($row->pagu/$this->get_pagu($id_prokeg_aktif)*$row->fisik_10);
			$fisik_11 += @($row->pagu/$this->get_pagu($id_prokeg_aktif)*$row->fisik_11);
			$fisik_12 += @($row->pagu/$this->get_pagu($id_prokeg_aktif)*$row->fisik_12);
		}
		return array(
            'fisik_01' => $fisik_01,
            'fisik_02' => $fisik_02,
            'fisik_03' => $fisik_03,
            'fisik_04' => $fisik_04,
            'fisik_05' => $fisik_05,
            'fisik_06' => $fisik_06,
            'fisik_07' => $fisik_07,
            'fisik_08' => $fisik_08,
            'fisik_09' => $fisik_09,
            'fisik_10' => $fisik_10,
            'fisik_11' => $fisik_11,
            'fisik_12' => $fisik_12
        );
	}

    public function get_rekeurp($id_prokeg_aktif){
		$this->db_ba->select('SUM(prokeg_sub_hir.rekeurp) as rekeurp');
		$this->db_ba->from('prokeg_sub_hir');
		$this->db_ba->where('id_prokeg_aktif', $id_prokeg_aktif);
		$this->db_ba->where('id_parent', NULL);
		$query = $this->db_ba->get();
		return $query->row()->rekeurp;
	}

    public function insert($data){
        $this->db_ba->insert('prokeg_sub_hir', $data);        
		// var_dump($this->db_ba->last_query());exit;
		return $this->db_ba->insert_id();
    }

    public function insert_batch($data){
        $this->db_ba->insert_batch('prokeg_sub_hir', $data);
    }

    public function update($where, $data){
        $this->db_ba->update('prokeg_sub_hir', $data, $where);	
		// var_dump($this->db_ba->last_query());exit;
		return $this->db_ba->affected_rows();
    }

    public function delete($id){
        $this->db_ba->where('id', $id);
        $this->db_ba->delete('prokeg_sub_hir');
    }

    public function truncate(){
        $this->db_ba->from('prokeg_sub_hir');
        $this->db_ba->truncate();
    }
	
	public function get_where($where=''){
		$this->db_ba->select('id, id_parent, id_prokeg_aktif, id_prokeg_jenis, kode_rek, uraian, pagu, volume, satuan, harga_satuan');
		$query = $this->db_ba->get_where('prokeg_sub_hir', $where);
		return $query->row_array(); 
	}
	
	public function get_where_criteria($where='', $field=''){
		$this->db_ba->select($field);
		$query = $this->db_ba->get_where('prokeg_sub_hir', $where);
		return $query->row_array(); 
	}
}
