<?php

class cupom_model extends CI_Model {
    
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
    
    public function get_cupom($code){        
        $cupom_row = NULL;
        try{
            $this->db->select('*');
            $this->db->from('cupom_type');
            $this->db->where( array('code' => $code) );                       
            $cupom_row =  $this->db->get()->row_array();
            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $cupom_row;
        }
    }
    
    public function is_used_cupom($id_client, $id_cupom){        
        $result = false;
        try{
            $this->db->select('*');
            $this->db->from('cupom_client');
            $this->db->where( array('client_id' => $id_client, 'cupom_type_id' => $id_cupom) );                       
            $cupom_row =  $this->db->get()->row_array();
            if($cupom_row)
                $result = true;
            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $result;
        }
    }
    
    public function add_cupom($client_id, $cupom_type_id){        
        $cupom_row = NULL;
        try{
            $data_cupom['client_id'] = $client_id;
            $data_cupom['cupom_type_id'] = $cupom_type_id;
            
            $this->db->insert('cupom_client',$data_cupom);
            $cupom_row = $this->db->insert_id();
            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $cupom_row;
        }
    }
    
}

?>