<?php

class User_model extends CI_Model {
    
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
       
    public function set_session($id, $session, $datas = NULL) {
        try {
            $this->load->model('class/user_role');
            $this->load->model('class/client_model');
            
            $this->db->select('*');
            $this->db->from('users');
            $this->db->where(array('id' => "$id"));
            $user_data = $this->db->get()->row_array();

            if (count($user_data)) {                
                $session->set_userdata('id', $user_data['id']);
                $session->set_userdata('name', $user_data['name']);
                $session->set_userdata('login', $user_data['login']);                
                $session->set_userdata('brazilian', $user_data['brazilian']);
                $session->set_userdata('email', $user_data['email']);
                $session->set_userdata('telf', $user_data['telf']);
                $session->set_userdata('role_id', $user_data['role_id']);
                $session->set_userdata('status_id', $user_data['status_id']);
                $session->set_userdata('init_date', $user_data['init_date']);
                $session->set_userdata('language', $user_data['language']);                
                $session->set_userdata('module', "LEADS");                
                $session->set_userdata('admin', 0);                
                if($user_data['brazilian']==1){
                    $session->set_userdata('currency_symbol', "R$");               
                }
                else {
                    $session->set_userdata('currency_symbol', "US$");                
                }
                
                $session->set_userdata('is_admin', FALSE);
                if($user_data['role_id']== user_role::ADMIN){
                    $session->set_userdata('is_admin', TRUE);               
                }
                
                //$campaing = $this->client_model->load_campaings($user_data['id']);
                //$session->set_userdata('campaing', $campaing);                
                //$array_id_campaings = $this->client_model->client_get_campaings($user_data['id'],'id');
                //$session->set_userdata('array_id_campaings', $array_id_campaings);                
                
                return true;
            } else {
                return false;
            }
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos durante el login';
        }
    }
    
    /*devuele un registro con el usuario (no cancelado) 
      dado login e contrasena (obligatoria para el login)*/
    
     public function verify_account($datas){
         $user_row = NULL;
         $this->load->model('class/user_status');            
         try{
            $this->db->select('*');
            $this->db->from('users');
            $this->db->where( array('login' => $datas['client_login']) );                       
            $this->db->where('status_id !=', user_status::DELETED); //not canceled
            $user_row =  $this->db->get()->row_array();
            
            if($user_row && $datas['check_pass'])
            {   $password =  $datas['client_pass'];
                $password_hased = $user_row['pass'];
                $result_login = password_verify($password, $password_hased);
                if(!$result_login){
                    $user_row = NULL;
                }                
            }
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos durante la verificacion de usario';
        } finally {
            return $user_row;
        }
    }
    
    /*devuele un registro con el usuario (no cancelado) 
      dado login (o e-mail) e contrasena (obligatoria para el login)*/
    
     public function verify_account_email($datas, $type){
         $user_row = NULL;
         $this->load->model('class/user_status');            
         try{
            $this->db->select('*');
            $this->db->from('users');
            $this->db->where('status_id !=', user_status::DELETED); //not canceled
            
            if($type === 0){
                $this->db->where( array('login' => $datas['client_login']) );                       
                $user_row =  $this->db->get()->row_array();
            
                if($user_row && $datas['check_pass'])
                {   $password =  $datas['client_pass'];
                    $password_hased = $user_row['pass'];
                    $result_login = password_verify($password, $password_hased);
                    if(!$result_login){
                        $user_row = NULL;
                    }                
                }
            }
            else{
                $this->db->where( array('email' => $datas['client_login']) );                       
                $user_row_array =  $this->db->get()->result_array();
                $password =  $datas['client_pass'];
                foreach($user_row_array as $user){
                    $password_hased = $user['pass'];
                    $result_login = password_verify($password, $password_hased);
                    if($result_login){
                        $user_row = $user;
                    }   
                }
            }
            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos durante la verificacion de usario';
        } finally {
            return $user_row;
        }
    }
    
     public function get_user_by_email($email, $login = NULL){
         $user_row = NULL;
         $this->load->model('class/user_status');            
         try{
            $this->db->select('*');
            $this->db->from('users');
            $this->db->where( array('email' => $email) );
            if($login)
                $this->db->where( array('login' => $login) );                       
            $this->db->where('status_id !=', user_status::DELETED); //not canceled
            if($login)
                $user_row =  $this->db->get()->row_array();
            else
                $user_row =  $this->db->get()->result_array()[0];            
            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos durante la verificacion de usario';
        } finally {
            return $user_row;
        }
    }
    
    public function save_recovery_token($email, $id_user, $login, $token){
        $recover_row = NULL;
        $now = time();
        try{            
            $this->db->select('*');
            $this->db->from('recover_pass');            
            $this->db->where( array('login' => $login) );                                   
            $recover_row =  $this->db->get()->row_array();           
            
            if($recover_row){
                $this->db->where( array('id' => $recover_row['id']) );            
                $update_result = $this->db->update( 'recover_pass', array('token' => $token,
                                                                          'send_date'=> $now )
                                                    );
                if($update_result){
                    $recover_row['token'] = $token;
                    $recover_row['send_date'] = $now;
                }
                else{
                    $recover_row = NULL;
                }
            }
            else{            
                $data_recover['user_id'] = $id_user;         
                $data_recover['login'] = $login;         
                $data_recover['email'] = $email;         
                $data_recover['token'] = $token;         
                $data_recover['send_date'] = $now;         

                $this->db->insert('recover_pass',$data_recover);
                $recover_row = $this->db->insert_id();
            }
            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos durante el login';
        } finally {
            return $recover_row;
        }
    }
    
    public function get_recover_data($login, $token){
        $recover_row = NULL;
        try{            
            $valid_date = time() - 24*3600;
            $this->db->select('*');
            $this->db->from('recover_pass');            
            $this->db->where( array('login' => $login) );
            $this->db->where( array('token' => $token) );
            $this->db->where('send_date >',$valid_date);
            
            $recover_row =  $this->db->get()->row_array();            
            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos durante el login';
        } finally {
            return $recover_row;
        }
    }
    
    public function update_password($id_user, $new_pass){
        $update_result = NULL;
        try{            
            $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
            $this->db->where( array('id' => $id_user) );            
            $update_result = $this->db->update( 'users', array('pass' => $hashed_pass));            
            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos durante el login';
        } finally {
            return $update_result;
        }
    }
    
    public function expire_token($id_token){
        $update_result = NULL;
        try{
            $expired = time()-25*3600;
            $this->db->where( array('id' => $id_token) );            
            $update_result = $this->db->update( 'recover_pass', array('send_date' => $expired));            
            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos durante el login';
        } finally {
            return $update_result;
        }
    }
    
    public function insert_user($datas){
        $user_row = NULL;
        try{
            $data_user['name'] = $datas['client_name'];             //desde el formulario de logueo
            $data_user['email'] = $datas['client_email'];             //desde el formulario de logueo
            $data_user['login'] = $datas['client_login'];             //desde el formulario de logueo            
            $data_user['pass'] = password_hash($datas['client_pass'], PASSWORD_DEFAULT);               //desde el formulario de logueo
            $data_user['role_id'] = $datas['role_id'];                //desde el controlador
            $data_user['status_id'] = $datas['status_id'];            //desde el controlador            
            $data_user['init_date'] = $datas['init_date'];
            $data_user['status_date'] = $datas['status_date'];
            $data_user['language'] = $datas['language']; 
            $data_user['brazilian'] = $datas['brazilian']; 
            $data_user['telf'] = $datas['telf']; 
            $data_user['utm_source'] = $datas['utm_source'];         
            $data_user['promotional_code'] = $datas['promotional_code'];         
            $this->db->insert('users',$data_user);
            $user_row = $this->db->insert_id();
            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos durante el login';
        } finally {
            return $user_row;
        }
    }      
    
    
    public function cancel_user($user_row, $status_date){
        $this->load->model('class/client_model');
        $this->load->model('class/user_status');                
        $update_result = NULL;
        try{            
            $update_result = $this->update_status_user($user_row['id'], user_status::DELETED, $status_date);            
            $this->client_model->client_cancel_campaings($user_row['id'], $status_date);
            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos durante el cancelamiento';
        } finally {
            return $update_result;
        }
    }
   
    /*actualiza el status dado nuevo status y fecha*/
    private function get_status_cad($id_status) {
    $this->load->model('class/user_status');
    if($id_status== user_status::ACTIVE){
    return 'ACTIVE';
    }
    if($id_status== user_status::BLOCKED_BY_PAYMENT){
    return 'BLOCKED BY PAYMENT';
    }
    if($id_status== user_status::DELETED){
    return 'DELETED';
    }
    if($id_status== user_status::PENDENT_BY_PAYMENT){
    return 'PENDENT BY PAYMENT';
    }
    if($id_status== user_status::DONT_DISTURB){
    return 'DONT DISTURB';
    }
    }
    private function update_status_user($id_user, $new_status, $status_date){
        $this->load->model('class/user_status');
        $watchdog_result = NULL;
        $update_result = NULL;
        try{
            if($new_status == user_status::DELETED)
                $end_date = $status_date;
            $this->db->where( array('id' => $id_user) );            
            $update_result = $this->db->update( 'users', array('end_date' => $end_date,
                                                'status_date' => $status_date,
                                                'status_id' => $new_status));            
            if($update_result){
                $this->session->set_userdata('status_id', $new_status);
                $watchdog_result =$this->insert_watchdog($id_user, 'FOR '.$this->get_status_by_id($new_status)['name'].' STATUS');
            }
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos durante el update';
        } finally {
            return $update_result;
        }
    }
    
    public function activate_client($id_user, $status_date){
        $this->load->model('class/user_status');                
        $update_result = $this->update_status_user($id_user, user_status::ACTIVE, $status_date);
        return $update_result;
    }

    public function set_pendent_client($id_user, $status_date){
        $this->load->model('class/user_status');                
        $this->load->model('class/client_model');
        $this->client_model->client_pause_campaings($id_user, $status_date);
        return $this->update_status_user($id_user, user_status::PENDENT_BY_PAYMENT, $status_date);
    }

    public function set_blocked_client($id_user, $status_date){
        $this->load->model('class/user_status');                
        $this->client_model->client_pause_campaings($id_user, $status_date);
        return $this->update_status_user($id_user, user_status::BLOCKED_BY_PAYMENT, $status_date);
    }

    public function has_payment($id_user, $status){
        $this->load->model('class/user_status');                
        $this->load->model('class/credit_card_model');
        $this->load->model('class/bank_ticket_model');   
        /*if($status == user_status::BLOCKED_BY_PAYMENT || $status == user_status::PENDENT_BY_PAYMENT)
            return false;*/
        $boleto = $this->bank_ticket_model->get_charged_bank_ticket($id_user);
        $credit_card = $this->credit_card_model->get_credit_card($id_user);
        if(!$boleto && !$credit_card){
            return false;
        }
        return true;
    }


    public function execute_sql_query($query) {
        return $this->db->query($query)->result_array();
    }

    public function update_language($id_user, $language){
                       
        $update_result = NULL;
        try{            
            $this->db->where('id',$id_user);                        
            $this->db->update('users', array('language' => $language));                                    
            $update_result =  $this->db->affected_rows();
            $this->session->set_userdata('language', $language);                
            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos durante el cancelamiento';
        } finally {
            return $update_result;
        }       
        
    }
    
    public function get_user($id_user){
        $user_row = NULL;
         try{
            $this->db->select('*');
            $this->db->from('users');
            $this->db->where(array('id' => $id_user));
            $user_row =  $this->db->get()->row_array();
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $user_row;
        }
    }





    //------------desenvolvido para DUMBU-FOLLOW-UNFOLLOW-------------------
    public function set_sesion_DUMBU($id, $session, $datas = NULL) {
        try {
            $this->load->model('class/user_role');
            /* $this->db->select('*');
              $this->db->from('users');
              $this->db->where(array('id'=>$id));
              $user_data = $this->db->get()->row_array(); */

            $this->db->select('*');
            $this->db->from('users');
            $this->db->where(array('id' => "$id"));
            $user_data = $this->db->get()->row_array();

            if (count($user_data)) {
                if($user_data['role_id'] == user_role::CLIENT) {
                    $this->db->select('*');
                    $this->db->from('clients');
                    $this->db->join('plane', 'plane.id = clients.plane_id');
                    $this->db->where(array('user_id' => "$id"));
                    $client_data = $this->db->get()->row_array();
                    if($client_data['plane_id']==1)
                        $session->set_userdata('plane_id', 4);
                    else
                        $session->set_userdata('plane_id', $client_data['plane_id']);
                    $session->set_userdata('to_follow', (int) $client_data['to_follow']);
                    $session->set_userdata('normal_val', (int) $client_data['normal_val']);
                    $session->set_userdata('cookies', $client_data['cookies']);
                    $session->set_userdata('unfollow_total', (int) $client_data['unfollow_total']);
                    $session->set_userdata('autolike', (int) $client_data['like_first']);
                    $session->set_userdata('play_pause', (int) $client_data['paused']);
                }
                $session->set_userdata('id', $user_data['id']);
                $session->set_userdata('name', $user_data['name']);
                $session->set_userdata('login', $user_data['login']);
                $session->set_userdata('pass', $user_data['pass']);
                $session->set_userdata('email', $user_data['email']);
                $session->set_userdata('role_id', $user_data['role_id']);
                $session->set_userdata('status_id', $user_data['status_id']);
                $session->set_userdata('init_date', $user_data['init_date']);
                $session->set_userdata('language', $user_data['language']);
                $session->set_userdata('insta_datas', $datas);
                return true;
            } else {
                return false;
            }
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos durante el login';
        }
    }
    
    public function get_user_match_from_login_password($datas){
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('login',$datas['login']);
        $this->db->where('pass',$datas['pass']);
        $this->db->where('status_id <', 11);
        $this->db->where('status_id <>', 8);
        $this->db->where('status_id <>', 4);
        return $this->db->get()->row_array();
    }
    
    public function get_language_of_client($user_id){
        $this->db->select('language');
        $this->db->from('users');
        $this->db->where('id',$user_id);
        $xxx=$this->db->get()->row_array();
        return $xxx;
    }
    
    public function set_language_of_client($user_id,$language){        
        $this->db->where('id', $user_id);
        $this->db->update('users', $language);
    }
        
    public function get_all_users() {
        $this->db->select('id,status_id,plane_id');
        $this->db->from('users');
        $this->db->join('clients', 'clients.user_id = users.id');
        $this->db->where('status_id <', 11);
        $this->db->where('status_id <>', 8);
        $this->db->order_by("plane_id","asc");        
        $this->db->order_by("user_id","asc");        
        $a = $this->db->get()->result_array();
        return $a;
    }
    
    public function get_daily_report($user_id) {
        $this->db->select('followers,date');
        $this->db->from('daily_report');
        $this->db->where('client_id', $user_id);
        $this->db->order_by("date","asc");
        $a = $this->db->get()->result_array();
        return $a;
    }
    
    public function time_of_live_model($status_id=NULL) {
        $this->db->select('id,init_date,end_date,plane_id');        
        $this->db->from('clients');
        $this->db->join('users', 'users.id = clients.user_id');        
        $this->db->where('status_id', $status_id);
        $this->db->where('init_date is NOT NULL', NULL, FALSE);
        if($status_id=='4')
            $this->db->where('end_date is NOT NULL', NULL, FALSE);
        $this->db->order_by("plane_id","asc");
        $this->db->order_by("init_date","asc");
        $a = $this->db->get()->result_array();
        return $a;
    }
    public function get_user_role($user_login, $user_pass) {
        $this->db->select('role_id');
        $this->db->from('users');
        $this->db->where('login', $user_login);
        $this->db->where('pass', $user_pass);
        $a = $this->db->get()->row_array();
        return $a;
    }
    
    public function get_signin_date($id) {
        $this->db->select('init_date');
        $this->db->from('users');
        $this->db->where('id', $id);
        return $this->db->get()->row_array()['init_date'];        
    }

    public function get_user_by_id($user_id) {
        try {
            $this->db->select('*');
            $this->db->from('users');
            $this->db->where('id', $user_id);
            return $this->db->get()->result_array();
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }
   
    public function update_user($id, $datas) {
        try {
            $this->db->where('id', $id);
            $this->db->update('users', $datas);
            return true;
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            return false;
        }
    }
    
    public function get_all_dummbu_clients() {
        try {
            $this->db->select('*');
            $this->db->from('users');
            return $this->db->get()->result_array();
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }
    
    

    /**
     * 
     *
     * @param serial user_id 

     * @return User
     * @access public
     */
    public function load_user($user_login, $user_pass) {
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where(array('login' => $user_login, 'pass' => $user_pass));
        return $this->db->get()->row_array();
    }

    /* public function load_all_user($condition) {
      $this->db->select('*');
      $this->db->from('users');
      $this->db->where($condition);
      return $this->db->get()->result_array();
      } */

    

    /**
     * 
     *
     * @return bool
     * @access public
     */
    
    public function insert_watchdog($id,$watch){
   
      $this->load->model('class/user_role');
      $watch_result=NULL;
      if($this->session->userdata('role_id')== user_role::CLIENT){        
        try{
           // $this->load->model('class/user_role');            
            //$this->load->model('class/user_status');            
            /*$this->load->model('class/admin_model');                                    
            $this->load->model('class/client_model');            
            $this->load->model('class/campaing_model');
            $this->load->model('class/campaing_status');            
            $this->load->model('class/profile_model');
            $this->load->model('class/profiles_status');*/
            $watch_row= $this->user_model->get_watchdog_type($watch);
            if(!$watch_row)
            {
                $datas=array();
                $datas['action']=$watch;
                $datas['source']=0;
                $this->insert_watchdog_type($datas);
            }
            $watch_row= $this->get_watchdog_type($watch);
            $watch_id=$watch_row['id'];
            $watchdog_row=array();
            $watchdog_row['user_id']=$id;
            $watchdog_row['type']=$watch_id;
            $watchdog_row['date']=time();
            $watchdog_row['robot']=null;
            $watchdog_row['metadata']=null;
            $watch_result= $this->add_watchdog($watchdog_row);
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $watch_result;
        }
      }
 else {
       return NULL;   
      }
        
    }

    
    public function get_watchdog_type($washdog_action){        
        $watchdog_type_row = NULL;
        try{            
            $this->db->select('*');
            $this->db->from('washdog_type');
            $this->db->where( array('action' => $washdog_action) );           
            $watchdog_type_row =  $this->db->get()->row_array();
            //$watchdog_type_row =  $this->db->get()->result_array();
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $watchdog_type_row;
        }
    }

        public function insert_watchdog_type($datas){
                       
        $watch_row=NULL;
        try{

            $this->db->insert('washdog_type',$datas);
            $watch_row = $this->db->insert_id();
    
            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $watch_row;
        }
        
    }

        public function add_watchdog($datas){
                       
        $watch_row=NULL;
        try{

            $this->db->insert('washdog',$datas);
            $watch_row = $this->db->insert_id();
    
            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $watch_row;
        }
        
    }

    
    public function insert_washdog($user_id,$cad) {
        $this->db->select('id');
        $this->db->from('washdog_type');
        $this->db->where('action',$cad);
        $a=$this->db->get()->row_array()['id'];
        if($a>0)
        $this->db->insert('washdog1',array('user_id'=>$user_id,'type'=>$a,'date'=>time()));
        else 
        $this->db->insert('washdog_type',array('action'=>$cad,'source'=>0));  
    }
    
   
    
    public function get_status_by_id($status_id){
       $this->db->select('name');
       $this->db->from('user_status');
       $this->db->where('id',$status_id);
       return $this->db->get()->row_array();
    }
     
     
    public function get_ranking(){
        $this->db->select('*');
        $this->db->from('users');
        $this->db->join('clients', 'users.id = clients.user_id');
        $this->db->join('plane', 'plane.id = clients.plane_id');
        $this->db->where(array('users.status_id' => 1));
        $client_data = $this->db->get()->result_array();
        return $client_data;
    }
    
    public function get_last_daily_report($client_id){
        $this->db->select('*');
        $this->db->from('daily_report');
        $this->db->where('client_id' , $client_id);
        $this->db->order_by("date","desc");
        $client_data = $this->db->get()->row_array();
        if(count($client_data))
            return $client_data;
        else 
            return null;
    } 
    
    public function client_prevalence() {
        $prevalence=array('in'=>array(),'out'=>array());
        $this->db->select('*');
        $this->db->from('users');
        $all_users=$this->db->get()->result_array();
        $N=count($all_users);
        for($i=0;$i<$N;$i++){
            $user=$all_users[$i];            
            if($user['status_id']!=='8'){
                $init_day=$user['init_date'];
                if($init_day!=='NULL'){
                    $d_init_day= date("j", $init_day);
                    $m_init_day= date("n", $init_day);
                    $y_init_day= date("Y", $init_day);
                    $str_init_day=sprintf('%s/%s/%s',$y_init_day,$m_init_day,$d_init_day);
                    if(!isset($prevalence['in'][$str_init_day])){
                        $prevalence['in'][$str_init_day]=1;
                        if(!isset($prevalence['out'][$str_init_day]))
                            $prevalence['out'][$str_init_day]=0;
                    }
                    else
                        $prevalence['in'][$str_init_day]=$prevalence['in'][$str_init_day]+1;
                }
                $end_day=$user['end_date'];
                if($end_day!=='NULL'){
                    $d_end_day= date("j", $end_day);
                    $m_end_day= date("n", $end_day);
                    $y_end_day= date("Y", $end_day);
                    $str_end_day=sprintf('%s/%s/%s',$y_end_day,$m_end_day,$d_end_day);            
                    if(!isset($prevalence['out'][$str_end_day])){
                        $prevalence['out'][$str_end_day]=1;
                        if(!isset($prevalence['in'][$str_end_day]))
                            $prevalence['in'][$str_end_day]=0;
                    }
                    else
                        $prevalence['out'][$str_end_day]=$prevalence['out'][$str_end_day]+1;
                }
            }
        }        
        foreach($prevalence['in'] as $key =>$value){  
            echo sprintf('%s;%d<br>', $key, $prevalence['in'][$key]);
        }
        echo '<br><br><br><br><br>';
        
        foreach($prevalence['out'] as $key =>$value){  
            echo sprintf('%s;%d<br>', $key, $prevalence['out'][$key]);
        }
    }

}

?>