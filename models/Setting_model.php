<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_model extends CI_Model {

	var $table = 'm_user';
	var $column_order = array(null,null,null,null); //set column field database for datatable orderable
	var $column_search = array(
		'a.nama_kelas',
		'a.thn_akademik',
		'a.nama_dosen',
	); //set column field database for datatable searchable just firstname , lastname , address are searchable
	var $order = array('a.id_prodi' => 'asc', 'a.semester'=> 'asc', 'a.nama_kelas'=> 'asc'); // default order 

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query($field='*')
	{
		
		$this->db->select($field);
		$this->db->from($this->table.' a');		
		// $this->db->join('m_paket b', 'a.id_paket = b.id_paket');
		// $this->db->join('m_detail_paket c', 'c.id_paket = a.id_paket');
		// $this->db->join('t_pemesan d', 'd.id_t_paket = a.id_t_paket');
		// $this->db->join('m_user e', 'a.cdb = e.id_user');
		if($this->session->userdata('level') == 3){
			$this->db->where('a.id_dosen', $this->session->userdata('id_user'));
		}
		
		if($this->session->userdata('level') == 1 AND @$_POST['kaprodi'] >= 1){
			$this->db->where('a.id_prodi', @$_POST['kaprodi']);
			$this->db->or_where('a.id_dosen', $this->session->userdata('id_user'));
		}

		$i = 0;
	
		foreach ($this->column_search as $item) // loop column 
		{
			if(!empty($_POST['search']['value'])) // if datatable send POST for search
			{
				
				if($i===0) // first loop
				{
					$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					// exit;
					$this->db->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if(count($this->column_search) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order))
		{
			$order = $this->order;
			$this->db->order_by('a.id_prodi asc, a.semester asc, a.nama_kelas asc');
		}
	}

	function get_datatables($field='*')
	{
		$this->_get_datatables_query($field);
		$length = (!empty($_POST['length'])) ? $_POST['length'] : false;
		if($length != -1){
			$this->db->limit($length, @$_POST['start']);
		}
		$query = $this->db->get();
		
		// echo '<pre>';
		// var_dump($this->db->last_query());
		// exit;
		return $query->result();
	}

	function count_filtered()
	{
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all()
	{
		$this->db->from($this->table);
		return $this->db->count_all_results();
	}

	public function save($data, $table='m_user')
	{
		
		return $this->db->insert($table, $data);
	}

	public function update($where, $data, $table='m_user')
	{
		$this->db->update($table, $data, $where);
		return $this->db->affected_rows();
	}

	public function delete_by_id($id, $field='id_user', $table = 'm_user')
	{
		$this->db->where($field, $id);
		$this->db->delete($table);
	}
		
    public function get($start = null, $length = null, $sort = null, $order = null, $where = null, $like = null, $table='m_user', $field='*', $row_array=false, $join=false, $group_by=false, $like_khusus=false, $like_khusus_not=false){
		
        $this->db->select($field);
        $this->db->from($table);
		
		if($join){
			foreach($join as $rs){
				$this->db->join($rs['nama_tabel'], $rs['kunci']);
			}
		}
		
        if($start !== null && $length !== null){
            $this->db->limit($length, $start);
        }
        if($sort !== null && $order !== null){
            $this->db->order_by($sort, $order);
        }
        if($where !== null){
            $this->db->where($where);
        }
        if($like !== null){						
			$this->db->like($like);
        }

		if($like_khusus){	
			foreach($like_khusus as $rss)
			{
				$this->db->like('a.json_sub_kegiatan', $rss, 'both'); 
			}
		}
		if($like_khusus_not){	
			foreach($like_khusus_not as $rss)
			{
				$this->db->not_like('a.json_sub_kegiatan', $rss, 'both'); 
			}
		}
        if($group_by){						
			$this->db->group_by($group_by);
        }
        $query = $this->db->get();
		// if($kondisi){
			// var_dump($this->db->last_query());exit;
		// }
		if($row_array){
			return $query->row_array();			
		}else{
			return $query->result_array();
		}
    }


}
