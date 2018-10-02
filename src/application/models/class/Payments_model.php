<?php

class Payments_model extends CI_Model {
    
    
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
    
    public function getPromotionalCodeFrequency($str){
        $amount = 0;
         try{
            $this->db->select('*');
            $this->db->from('users');
            $this->db->where('promotional_code', $str);
            $resp=$this->db->get()->result_array();
            $amount = count($resp);
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $amount;
        }
    }




    //------------desenvolvido para DUMBU-LEADS-------------------
    
}

?>