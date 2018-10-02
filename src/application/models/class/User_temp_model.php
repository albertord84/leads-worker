<?php

class User_temp_model extends CI_Model {
    
    /*
    const ACTIVE = 1;
    const BLOCKED_BY_PAYMENT = 2;
    const DELETED = 4;
    const PENDENT_BY_PAYMENT = 6;
    const BEGINNER= 8;
    const DONT_DISTURB= 11; 
     */
    
    public $id;

    public function id($value = NULL) {
        if (isset($value)) {
            $this->id = $value;
        } else {
            return $this->id;
        }
    }

    public $name;
    public $login;
    public $pass;
    public $email;
    public $telf;
    public $role_id;
    public $status_id;
    public $language;
    
    function __construct() {
        
    }
    
    //------------desenvolvido para DUMBU-LEADS------------------- 
    
    public function in_confirmation($datas){
         $user_row = NULL;
         $this->load->model('class/user_status');            
         try{
            $now = time();
            $this->db->select('*');
            $this->db->from('users_temp');
            $this->db->where( array('login' => $datas['client_login']) );                       
            $this->db->where('temp_date > ', $now-24*3600); //24 hrs
            $user_row =  $this->db->get()->row_array();            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos durante la verificacion de usario';
        } finally {
            return $user_row;
        }
    }
    
    public function verify_confirmation($datas){
         $user_row = NULL;
         $this->load->model('class/user_status');            
         try{
            $now = time();
            $this->db->select('*');
            $this->db->from('users_temp');
            $this->db->where( array('login' => $datas['client_login']) );                       
            $this->db->where('temp_date > ', $now-24*3600); //24 hrs
            $user_row =  $this->db->get()->row_array();   
            
            $password =  $datas['client_pass'];
            $password_hased = $user_row['pass'];
            $result_confirm = password_verify($password, $password_hased);
            
            if(!$result_confirm){
                $user_row = NULL;
            }else{
                if($datas['number_confirmation'] != $user_row['id_number'])
                    $user_row = NULL;
            } 
                
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos durante la verificacion de usario';
        } finally {
            return $user_row;
        }
    }
    
    public function insert_user($datas){
        $user_row = NULL;
        try{
            $this->db->select('*');
            $this->db->from('users_temp');
            $this->db->where( array('login' => $datas['client_login']) );                                   
            $user_row2 =  $this->db->get()->row_array();    
            
            $data_user['name'] = $datas['client_name'];             //desde el formulario de logueo
            $data_user['telf'] = $datas['client_telf'];             //desde el formulario de logueo
            $data_user['email'] = $datas['client_email'];             //desde el formulario de logueo
            $data_user['login'] = $datas['client_login'];             //desde el formulario de logueo            
            $data_user['pass'] = password_hash($datas['client_pass'], PASSWORD_DEFAULT);               //desde el formulario de logueo
            $data_user['id_number'] = $datas['id_number'];                //desde el controlador
            $data_user['temp_date'] = time();            //desde el controlador                        
            $data_user['language'] = $datas['language']; 
            $data_user['ip'] = $datas['ip']; 
            $data_user['promotional_code'] = $datas['promotional_code']; 
            $data_user['valid_code'] = $datas['valid_code']; 
            $data_user['utm_source'] = $datas['utm_source']; 

            if(!$user_row2){
                $this->db->insert('users_temp',$data_user);
                $user_row = $this->db->insert_id();
            }
            else{
                $this->db->where( array('login' => $datas['client_login']) );                                   
                $user_row = $this->db->update( 'users_temp', $data_user);
                
            }
            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos durante el login';
        } finally {
            return $user_row;
        }
    }
    
    public function delete_temp_user($client_id){
        $delete_results = true;
        try{
            $this->db->delete('users_temp', array(  'id' => $client_id ) );
            if ($this->db->_error_message())
                $delete_results = false;            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos durante el login';
        } finally {
            return $delete_results;
        }
    }
            
}

?>