<?php

class Robot_model extends CI_Model {
    
   
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
    
    //------------desenvolvido para DUMBU-LEADS------------------- 
    public function get_users($filter = NULL){
         $user_rows = NULL;
         $this->load->model('class/user_status');            
         try{
            $this->db->select('*');
            $this->db->from('users');
            if($filter['status_id']){
                $status_id = $filter['status_id'];
                $this->db->where(array('status_id' => "$status_id"));
            }
            $user_rows =  $this->db->get()->result_array();           
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos durante la verificacion de usario';
        } finally {
            return $user_rows;
        }
    }
         

}


?>
