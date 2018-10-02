<?php

class daily_work_model extends CI_Model {
    
        
    public $id;

    public function id($value = NULL) {
        if (isset($value)) {
            $this->id = $value;
        } else {
            return $this->id;
        }
    }
    
    function __construct() {
        
    }  
    
    //inserta perfiles seleccionados de una campaña o cliente en la daily_work
    public function insert_works($datas){
        $work_row_results = NULL;
        try{                       
            $this->db->insert_batch('daily_work',$datas);
            $work_row_results = $this->db->affected_rows();
            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $work_row_results;
        }
    }
    
    public function insert_work($datas){
        $work_row_result = NULL;
        try{                       
            $this->db->insert('daily_work',$datas);
            $work_row_results = $this->db->affected_rows();
            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $work_row_results;
        }
    }
    
    //elimina perfiles seleccionados de una campaña en la daily_work
    public function delete_works_by_campaing($id_campaing){
        $delete_results = true;
        try{                       
            $this->db->delete('daily_work', array('campaing_id' => $id_campaing)); 
            if ($this->db->_error_message())
                $delete_results = false;            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $delete_results;
        }
    }
    
    //elimina perfiles seleccionados de un cliente en la daily_work
    public function delete_works_by_client($id_client){
        $delete_results = true;
        try{                       
            $this->db->delete('daily_work', array('client_id' => $id_client)); 
            if ($this->db->_error_message())
                $delete_results = false;            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $delete_results;
        }
    }

    public function delete_work($datas){
        $delete_results = true;
        try{                       
            $this->db->delete('daily_work', array(  'client_id' => $datas['client_id'],
                                                    'campaing_id' => $datas['campaing_id'],
                                                    'profile_id' => $datas['profile_id'])); 
            if ($this->db->_error_message())
                $delete_results = false;            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $delete_results;
        }
    }
    
    public function add_works_by_client($id_client){
        $this->load->model('class/campaing_status');
        $this->load->model('class/profile_status');
        
        $this->db->select('*');
        $this->db->from('campaings');            
        $this->db->join('profiles', 'campaings.id = profiles.campaing_id');
        $this->db->where('campaings.client_id',$id_client);
        $this->db->where('profiles.profile_status_id', profiles_status::ACTIVE);
        $this->db->where('campaings.campaing_status_id', campaing_status::ACTIVE );  
        $this->db->where('campaings.available_daily_value >', 0 );  
        $profiles_in_campaing =  $this->db->get()->result_array();
        $datas_works = [];
        $current_time = time();
        foreach($profiles_in_campaing as $p)
            $datas_works[] = array( 'client_id' => $p['client_id'], 
                                    'campaing_id' => $p['campaing_id'], 
                                    'profile_id' => $p['id'],
                                    'last_accesed' => $current_time);

        $this->insert_works($datas_works);
    }
}

?>