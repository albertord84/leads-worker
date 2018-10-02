<?php

class Admin_model extends CI_Model {
    
   
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
         $this->load->model('class/user_role');
         $this->load->model('class/payment_type');
         $identify=false;
         try{
            $this->db->select('users.id as id_usr, login, email, telf, status_id, amount_in_cents, init_date, status_date, payments.id as idpay, user_status.name as st_name');
            $this->db->from('users');
            $this->db->join('user_status','users.status_id = user_status.id');
            if(/*$filter['req_cam']||*/$filter['card_name'])
              $this->db->join('credit_cards', 'users.id = credit_cards.client_id');
            //$this->db->join('payments','users.id=payments.client_id','left');
            //if($filter['req_cam'])
                //$this->db->join('campaings','users.id=campaings.client_id');
            //$this->db->join('clients','users.id=clients.user_id','left');
          $this->db->where('role_id <>',$this->user_role::ADMIN);
             if(/*$filter['status_id']>0 &&*/ $filter['status_id']!= user_status::BEGINNER)
             {    
              if($filter['req_cam'])
                $this->db->join('campaings','users.id=campaings.client_id');
              $cnf=0;$cnf2=1;
              if($filter['prf_client1']==''&& $filter['eml_client1']==''&&$filter['card_name']==''&&$filter['client_id']=='')
              {  
               $cnf=1;
               
              }
              if($filter['lst_access2']!=''|| $filter['lst_access4']!=''){
                  //$this->db->where('clients.last_accesed =null'); 
               
                 $this->db->join('clients','users.id=clients.user_id');    
                   // $cnf=0;
               }
               $lig='users.id=payments.client_id';
             /*  if($cnf)
               {
              if($filter['req_card'])
              {
               
                //$this->db->where(array('payments_type' => $this->payment_type::CREDIT_CARD));
                $frq= $filter['req_card']; 
                //$this->db->where(array('payment_type' => "$frq"));
                $lig=$lig.' and payment_type="'.$frq.'"';
              }
            
              if($filter['lst_access1']!=''){
               //if($filter['req_card'])
               //{
                //$cnf=0;
                //$this->db->where('date >=',strtotime($filter['lst_access1'].' 00:00:00'));
               //}  
                $lig=$lig.' and date>="'.strtotime($filter['lst_access1'].' 00:00:00').'"';
              }
              if($filter['lst_access3']!=''){
               //if($filter['req_card'])
               //{
                //$cnf=0;
                //$this->db->where('date <=',strtotime($filter['lst_access3'].' 23:59:59'));
                $lig=$lig.' and date<="'.strtotime($filter['lst_access3'].' 23:59:59').'"';
                  
               }   
                   
               }*/    
               
               //if($cnf)
               //$this->db->join('payments',$lig,'left');
               
               // $cnf=1;             
                 if($filter['status_id']){
                $status_id = $filter['status_id'];
                $this->db->where(array('status_id' => "$status_id"));
                
              }
            
              if($filter['date_from']!=''){
                $this->db->where('status_date >=',strtotime($filter['date_from'].' 00:00:00'));
                //$cnf=0;
              }
              if($filter['date_to']!=''){
                $this->db->where('status_date <=',strtotime($filter['date_to'].' 23:59:59'));
                //$cnf=0;
              }
             
              if( $filter['asn_date_to']!=''){
               $this->db->where('init_date <=',strtotime($filter['asn_date_to'].' 23:59:59'));
               //$cnf=0;
              }

              if($filter['asn_date_from']!=''){
               $this->db->where('init_date >=',strtotime($filter['asn_date_from'].' 00:00:00'));
                //$cnf=0;
              }
              
              
              /*if($filter['lst_access1']!=''){
               $this->db->where('clients.last_accesed >=',strtotime($filter['lst_access1'].' 00:00:00'));
               $this->db->where('clients.last_accesed <=',strtotime($filter['lst_access1'].' 23:59:59'));
            }*/
            //if(!$cnf)
            //{    
              if($filter['req_card'])
              {
              
                //$this->db->where(array('payments_type' => $this->payment_type::CREDIT_CARD));
                $frq= $filter['req_card']; 
                $this->db->where(array('payment_type' => "$frq"));
                $cnf2=0;
              }
            
              if($filter['lst_access1']!=''){
               //if($filter['req_card'])
               //{
                $cnf2=0;
                $this->db->where('date >=',strtotime($filter['lst_access1'].' 00:00:00'));
               //}  
              }
              if($filter['lst_access3']!=''){
               //if($filter['req_card'])
               //{
                $cnf2=0;
                $this->db->where('date <=',strtotime($filter['lst_access3'].' 23:59:59'));
               }
            //}   
              if($filter['req_cam'])
              {
               if($filter['campaigns_from']!='')
               {
                  $this->db->where('created_date >=',strtotime($filter['campaigns_from'].' 00:00:00'));
               } 
                  //$cnf=0;
              } 
             //if(!$filter['req_card']&&!$filter['req_cam'])
             //{
             //  $this->db->where('clients.last_accesed >=',strtotime($filter['lst_access1'].' 00:00:00'));
             //  $this->db->where('clients.last_accesed <=',strtotime($filter['lst_access1'].' 23:59:59'));
             //}
                  

               //}  
              if($filter['req_cam'])
              {
               if($filter['campaigns_to']!='')
               {
                $this->db->where('created_date <=',strtotime($filter['campaigns_to'].' 23:59:59'));
               }
               //$cnf=0;
              } 
             //if(!$filter['req_card']&&!$filter['req_cam'])
             //{
             //  $this->db->where('clients.last_accesed >=',strtotime($filter['lst_access1'].' 00:00:00'));
             //  $this->db->where('clients.last_accesed <=',strtotime($filter['lst_access1'].' 23:59:59'));
             //}
               /*
               if($filter['lst_access2']!=''|| $filter['lst_access4']!=''){
                  //$this->db->where('clients.last_accesed =null'); 
               
                 $this->db->join('clients','users.id=clients.user_id');    
                   // $cnf=0;
               }*/
               
               if($filter['lst_access2']!=''){
                 $this->db->where('last_accesed >=',strtotime($filter['lst_access2'].' 00:00:00'));
            //if(!$filter['req_card']&&!$filter['req_cam'])
             //{
             //  $this->db->where('clients.last_accesed >=',strtotime($filter['lst_access1'].' 00:00:00'));
             //  $this->db->where('clients.last_accesed <=',strtotime($filter['lst_access1'].' 23:59:59'));
             //}
                //$cnf=0;
               }   
               if($filter['lst_access4']!=''){
                 $this->db->where('last_accesed <=',strtotime($filter['lst_access4'].' 23:59:59'));
            //if(!$filter['req_card']&&!$filter['req_cam'])
             //{
             //  $this->db->where('clients.last_accesed >=',strtotime($filter['lst_access1'].' 00:00:00'));
             //  $this->db->where('clients.last_accesed <=',strtotime($filter['lst_access1'].' 23:59:59'));
             //}
                //$cnf=0;
               }
            
            //if($filter['req_cam'])
            //  $this->db->join('campaings','clients.user_id=campaings.client_id');
              $cnf1=1;
              if($filter['prf_client1']==''&& $filter['eml_client1']==''&& $filter['card_name']==''&& $filter['client_id']=='')
              {  
               $cnf1=0;
               
              }
              else 
              {
                  $cnf1=1;
            if($filter['prf_client1']=='')
            {
                if($filter['eml_client1']=='')
                {
                    if($filter['client_id']=='')
                    {    
                        $card_name = $filter['card_name'];
                        $this->db->where(array('credit_card_name' => "$card_name"));
                    }
                    else {
                        $client1 = $filter['client_id'];
                        $this->db->where(array('users.id' => "$client1"));
                       
                    }
                    
                }
                else
                {
                    $eml_client1 = $filter['eml_client1'];
                    $this->db->where(array('email' => "$eml_client1"));
                }
                
            }
            else
            {
                $prf_client1 = $filter['prf_client1'];
                $this->db->where(array('login' => "$prf_client1"));
            }
                     
                     
              }
                    
             //if(!$cnf)
             //{
              if($cnf2||$cnf)
              { 
                $this->db->join('payments','users.id=payments.client_id','left');
              } 
              else 
              {
                $this->db->join('payments','users.id=payments.client_id');
              }
             //} 
              /*
              if($filter['req_cam'])
                $this->db->join('campaings','users.id=campaings.client_id');
               */
             }
             else
             {
              /*if($filter['status_id']){
                $status_id = $filter['status_id'];
                $this->db->where(array('status_id' => "$status_id"));
              }*/
              //$this->db->join('payments','users.id=payments.client_id','left');
            $this->db->join('payments','users.id=payments.client_id','left');
              if($filter['status_id']){
                $status_id = $filter['status_id'];
                $this->db->where(array('status_id' => "$status_id"));
              }
            
              if($filter['date_from']!=''){
                $this->db->where('status_date >=',strtotime($filter['date_from'].' 00:00:00'));
              }
              if($filter['date_to']!=''){
                $this->db->where('status_date <=',strtotime($filter['date_to'].' 23:59:59'));
              }
             
              if( $filter['asn_date_to']!=''){
               $this->db->where('init_date <=',strtotime($filter['asn_date_to'].' 23:59:59'));
              }

              if($filter['asn_date_from']!=''){
               $this->db->where('init_date >=',strtotime($filter['asn_date_from'].' 00:00:00'));
              }

              
             }
         
            $user_rows =  $this->db->get()->result_array(); 
            /*$a=array();
            foreach ($user_rows as $usr) {
                foreach ($usr as $key => $value) {
                    $ide=$key;
                    
                } 
                if($usr['login']=='a')
                {
                    $a[$usr['id']]=1;
                }
            }
            $l= count($a);*/
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos durante la verificacion de usario';
        } finally {
            return $user_rows;
        }
    }
    
    
            public function insert_robot($datas){
                       
        $robot_row=NULL;
        try{

            $this->db->insert('robots_profiles',$datas);
            $robot_row = $this->db->insert_id();
    
            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $robot_row;
        }
        
    }

    
    
    public function update_robot($datas){
                       
        $id_robot=$datas['id'];
        $update_result;
        try{            
            foreach ($datas as $k => $dat) 
            {
             if($k!='id')
             {   
               $this->db->where('id',$id_robot);
               $this->db->update('robots_profiles', array($k => $dat));
               $update_result +=  $this->db->affected_rows();
             }
            }   
/*               $this->db->where('id',$id_robot);                        
               $this->db->update('robots_profiles', array(
                                'id' => $datas['id'],
                                'login' => $datas['login'],
                                'pass' => $datas['pass'],
                                'status_id' => $datas['status_id'],
                                'profile_theme' => $datas['profile_theme'],
                                'recuperation_email_account' => $datas['recuperation_email_account'],
                                'recuperation_email_pass' => $datas['recuperation_email_pass'],
                                'creator_email' => $datas['creator_email'],
                                'recuperation_phone' => $datas['recuperation_phone'],
                                'init' => $datas['init'],
                                'end' => $datas['end']
                                ));                                    
            */
            //$this->session->set_userdata('language', $language);                
            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos durante el cancelamiento';
        } finally {
            return $update_result;
        }       
        
    }

    public function get_robot_by_id($filter = NULL){
         $robot_rows = NULL;
         $this->load->model('class/user_status');            
         try{
            $this->db->select('*');
            $this->db->from('robots_profiles');
            $id = $filter['id'];
            $this->db->where(array('id' => "$id"));
            $robot_rows =  $this->db->get()->result_array();           
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos durante la verificacion de usario';
        } finally {
            return $robot_rows;
        }
    }

    
    public function get_robots($filter = NULL){
         $robot_rows = NULL;
         $this->load->model('class/user_status');            
         try{
            $this->db->select('*');
            $this->db->from('robots_profiles');
            if($filter['status_id']){
                $status_id = $filter['status_id'];
                $this->db->where(array('status_id' => "$status_id"));
            }
                if($filter['date_from']!='' && $filter['date_to']!=''){
                   $this->db->where('init >=',strtotime($filter['date_from'].' 00:00:00'));
                   $this->db->where('end <=',strtotime($filter['date_to'].' 23:59:59'));
                }
                
            
            $robot_rows =  $this->db->get()->result_array();           
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos durante la verificacion de usario';
        } finally {
            return $robot_rows;
        }
    }

    public function verify_account_user($id_user){
         $user_row = NULL;                     
         try{
            $this->db->select('*');
            $this->db->from('users');
            $this->db->where( array('id' => $id_user) );    
            $user_row =  $this->db->get()->row_array();          
            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos durante la verificacion de usario';
        } finally {
            return $user_row;
        }
    }  
    
    public function set_session_as_client($user_row, $session, $datas=NULL) {
        try {
            $this->load->model('class/user_role');
            $this->load->model('class/client_model');

            if ($user_row) {                
                $session->set_userdata('id', $user_row['id']);
                //$session->set_userdata('name', $user_row['name']);
                $session->set_userdata('login', $user_row['login']);                
                $session->set_userdata('brazilian', $user_row['brazilian']);
                //$session->set_userdata('email', $user_data['email']);
                //$session->set_userdata('telf', $user_data['telf']);
                $session->set_userdata('role_id', $user_row['role_id']);
                $session->set_userdata('status_id', $user_data['status_id']);
                $session->set_userdata('init_date', $user_data['init_date']);
                $session->set_userdata('language', $user_data['language']);                
                $session->set_userdata('module', "LEADS");                
                $session->set_userdata('admin', 1);                
                if($user_row['brazilian']==1){
                    $session->set_userdata('currency_symbol', "R$");               
                }
                else {
                    $session->set_userdata('currency_symbol', "US$");                
                }
                
                $session->set_userdata('is_admin', FALSE);
                
                return true;
            } else {
                return false;
            }
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos durante el login';
        }
    }
    
    public function payed_ticket_bank($order, $value, $date) {
        $result = NULL;
         try{
            $this->db->set('amount_payed_value', 'amount_payed_value + ' . (int) $value, FALSE); 
            $this->db->set('payed', 1); 
            $this->db->set('payed_date', $date); 
            $this->db->where('document_number',$order);                        
            $this->db->limit(1);
            $this->db->update('bank_ticket');                                    
            $result =  $this->db->affected_rows();
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $result;
        }
    }
    
    public function client_by_order($order_number){         
        $result = NULL;
         try{
            $this->db->select('client_id');                
            $this->db->from('bank_ticket');            
            $this->db->where('document_number',$order_number);
            $result = $this->db->get()->row_array()['client_id'];
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $result;
        }                
    }
    
    public function ticket_by_order($order_number){         
        $result = NULL;
         try{
            $this->db->select('id');                
            $this->db->from('bank_ticket');            
            $this->db->where('document_number',$order_number);
            $result = $this->db->get()->row_array()['id'];
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $result;
        }                
    }
    
    public function activate_stoped_client($client_id, $order_number, $valor_pago){         
        $this->load->model('class/user_status');
        $this->load->model('class/system_config');
        $GLOBALS['sistem_config'] = $this->system_config->load();
        $result = NULL;
         try{
            $this->db->select('*');                
            $this->db->from('users');            
            $this->db->where('id',$client_id);
            $user = $this->db->get()->row_array();
            //
            if($user['status_id'] == user_status::PENDENT_BY_PAYMENT || $user['status_id'] == user_status::BLOCKED_BY_PAYMENT){
                $factor_conversion = 1;
                if($user['brazilian']==1){
                    $price_per_lead = $GLOBALS['sistem_config']->FIXED_LEADS_PRICE;
                }
                else{
                    $price_per_lead = $GLOBALS['sistem_config']->FIXED_LEADS_PRICE_EX;
                    $factor_conversion = $GLOBALS['sistem_config']->DOLLAR_TO_REAL;
                }
                
                $leads_to_pay = $this->leads_to_pay($client_id);
                $amount_leads = count($leads_to_pay);
                $amount_to_pay = $amount_leads * $price_per_lead;
                if($amount_to_pay <= $valor_pago){
                    $leads_sold = $amount_to_pay;
                    //activar cliente y adicionar trabajo
                    //$this->add_works_by_client($client_id);   //el cliente debe hacer eso
                    $this->activate_client($client_id, time());
                }
                else{
                    $leads_sold = $valor_pago;
                }          
                $source_id = $this->ticket_by_order($order_number);
                $this->save_payment($client_id, $leads_sold, $source_id);
                $this->update_amount_used($source_id, $leads_sold);
                $leads_sold /= $price_per_lead;
                foreach ($leads_to_pay as $lead_to_pay) {
                        $list_leads_id[] = $lead_to_pay['id'];
                    }
                $this->update_leads($list_leads_id, $leads_sold);
            }
            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $result;
        }                
    }

    public function save_payment($client_id, $amount_in_cents, $source_id){        
        $this->load->model('class/payment_type');
        $payment_row = NULL;
        try{//client_id, amount_in_cents, date, payment_type, source_id
            $data_payment['client_id'] = $client_id;
            $data_payment['amount_in_cents'] = $amount_in_cents;
            $data_payment['date'] = time();
            $data_payment['payment_type'] = payment_type::TICKET_BANK;
            $data_payment['source_id'] = $source_id;
            
            $this->db->insert('payments',$data_payment);
            $payment_row = $this->db->insert_id();
            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos durante';
        } finally {
            return $payment_row;
        }
    }
    
    public function update_amount_used($id_ticket, $value) {
        $result = NULL;
         try{
            $this->db->set('amount_used_value', 'amount_used_value + ' . (int) $value, FALSE);             
            $this->db->where('id',$id_ticket);                        
            $this->db->limit(1);
            $this->db->update('bank_ticket');                                    
            $result =  $this->db->affected_rows();
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $result;
        }
    }

    public function update_leads($list_leads_id, $number_leads){
        $result = NULL;
         try{
            $this->db->where('sold',0);            
            $this->db->where_in( 'id', $list_leads_id );
            $this->db->limit($number_leads);
            $this->db->update('leads', array('sold' => 1));                                    
            $result =  $this->db->affected_rows();
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $result;
        }                
    }
    
    public function leads_to_pay($id_client, $id_campaing = NULL, $all = NULL){         
        $result = NULL;
         try{
            $this->db->select('leads.id');                
            $this->db->from('leads');            
            $this->db->join('profiles', 'profiles.id = leads.reference_profile_id');
            $this->db->join('campaings', 'campaings.id = profiles.campaing_id');
            $this->db->join('clients', 'campaings.client_id = clients.user_id');
            $this->db->where('clients.user_id',$id_client);                  
            if($id_campaing){
                $this->db->where('campaings.id',$id_campaing);                  
            } 
            if(!$all){
                $this->db->where('leads.sold',0);
            }
            $result = $this->db->get()->result_array();
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $result;
        }                
    }
    
    /*actualiza el status dado nuevo status y fecha*/
    private function update_status_user($id_user, $new_status, $status_date){
        $this->load->model('class/user_status');                
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
            }
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos durante el update';
        } finally {
            return $update_result;
        }
    }
    
    public function activate_client($id_user, $status_date){
        $this->load->model('class/user_status');                
        return $this->update_status_user($id_user, user_status::ACTIVE, $status_date);
    }
    
    public function add_works_by_client($id_client){
        $this->load->model('class/campaing_status');
        $this->load->model('class/profiles_status');
        
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
}

?>