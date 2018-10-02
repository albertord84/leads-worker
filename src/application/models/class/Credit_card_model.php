<?php

class credit_card_model extends CI_Model {
    
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
    
    public function insert_credit_card($datas){        
        $card_row = NULL;
        try{
            $key_number = md5($datas['client_id'].$datas['credit_card_exp_month'].$datas['credit_card_exp_year']);
            $data_card['client_id'] = $datas['client_id'];
            $data_card['credit_card_name'] = $datas['credit_card_name'];
            $data_card['credit_card_number'] = openssl_encrypt ( $datas['credit_card_number'], "aes-256-ctr", $key_number);
            $data_card['credit_card_cvc'] = openssl_encrypt ( $datas['credit_card_cvc'], "aes-256-ctr", md5($key_number));
            $data_card['credit_card_exp_month'] = $datas['credit_card_exp_month'];
            $data_card['credit_card_exp_year'] = $datas['credit_card_exp_year'];
            $data_card['payment_order'] = $datas['payment_order'];
            $data_card['updating_date'] = time();
            
            $this->db->insert('credit_cards',$data_card);
            $card_row = $this->db->insert_id();
            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $card_row;
        }
    }

    public function update_credit_card($datas){        
        $card_row = NULL;        
        try{
            $key_number = md5($datas['client_id'].$datas['credit_card_exp_month'].$datas['credit_card_exp_year']);
            //$data_card['client_id'] = $datas['client_id'];
            $data_card['credit_card_name'] = $datas['credit_card_name'];
            $data_card['credit_card_number'] = openssl_encrypt ( $datas['credit_card_number'], "aes-256-ctr", $key_number);
            $data_card['credit_card_cvc'] = openssl_encrypt ( $datas['credit_card_cvc'], "aes-256-ctr", md5($key_number));
            $data_card['credit_card_exp_month'] = $datas['credit_card_exp_month'];
            $data_card['credit_card_exp_year'] = $datas['credit_card_exp_year'];
            //$data_card['payment_order'] = $datas['payment_order'];
            $data_card['updating_date'] = time();
            
            $this->db->where('client_id', $datas['client_id']);
            $this->db->update('credit_cards',$data_card);
            $card_row = $this->db->affected_rows();
            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $card_row;
        }
    }
    
    public function get_credit_card($id_client){        
        $card_row = NULL;
        try{            
            $this->db->select('*');
            $this->db->from('credit_cards');
            $this->db->where( array('client_id' => $id_client) );           
            $card_row =  $this->db->get()->row_array();
            if($card_row){
                $key_number = md5($id_client.$card_row['credit_card_exp_month'].$card_row['credit_card_exp_year']);
                $key_cvc = md5($key_number);
                $card_row['credit_card_number'] = openssl_decrypt ( $card_row['credit_card_number'] , "aes-256-ctr" , $key_number);                
                $card_row['credit_card_cvc'] = openssl_decrypt ( $card_row['credit_card_cvc'] , "aes-256-ctr" , $key_cvc);
            }
            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $card_row;
        }
    }
    
    public function get_credit_card_cupom($id_client){        
        $card_row = NULL;
        try{            
            $now = time(); 
            $this->db->select('*');
            $this->db->from('credit_cards_cupom');
            $this->db->where( array('client_id' => $id_client) );           
            //$this->db->where( 'created_date >', $now-2*3600 );           
            $card_row =  $this->db->get()->row_array();
            if($card_row){
                $key_number = md5($id_client.$card_row['credit_card_exp_month'].$card_row['credit_card_exp_year']);
                $key_cvc = md5($key_number);
                $card_row['credit_card_number'] = openssl_decrypt ( $card_row['credit_card_number'] , "aes-256-ctr" , $key_number);                
                $card_row['credit_card_cvc'] = openssl_decrypt ( $card_row['credit_card_cvc'] , "aes-256-ctr" , $key_cvc);
            }
            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $card_row;
        }
    }
    
    public function insert_credit_card_cupom($datas){        
        $card_row = NULL;
        try{
            $key_number = md5($datas['client_id'].$datas['credit_card_exp_month'].$datas['credit_card_exp_year']);
            $data_card['client_id'] = $datas['client_id'];
            $data_card['credit_card_name'] = $datas['credit_card_name'];
            $data_card['credit_card_number'] = openssl_encrypt ( $datas['credit_card_number'], "aes-256-ctr", $key_number);
            $data_card['credit_card_cvc'] = openssl_encrypt ( $datas['credit_card_cvc'], "aes-256-ctr", md5($key_number));
            $data_card['credit_card_exp_month'] = $datas['credit_card_exp_month'];
            $data_card['credit_card_exp_year'] = $datas['credit_card_exp_year'];
            $data_card['payment_order'] = $datas['payment_order'];
            $data_card['created_date'] = time();
            $data_card['amount'] = $datas['amount'];
            
            $this->db->insert('credit_cards_cupom',$data_card);
            $card_row = $this->db->insert_id();
            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $card_row;
        }
    }
    
}

?>