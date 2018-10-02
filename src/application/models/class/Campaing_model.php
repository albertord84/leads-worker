<?php

class Campaing_model extends CI_Model {
    
    
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
    
    public function insert_campaing($datas){
        $campaing_row = NULL;
        $watchdog_row = NULL;
        $this->load->model('class/user_model');                                    
        try{
            $data_campaing['client_id'] = $datas['client_id'];             
            $data_campaing['campaing_type_id'] = $datas['campaing_type_id'];             
            $data_campaing['campaing_status_id'] = $datas['campaing_status_id']; 
            $data_campaing['total_daily_value'] = $datas['total_daily_value'];
            $data_campaing['available_daily_value'] = $datas['available_daily_value'];
            $data_campaing['client_objetive'] = $datas['client_objetive'];            
            $data_campaing['created_date'] = $datas['created_date'];            
            $data_campaing['end_date'] = $datas['end_date'];
            $data_campaing['last_accesed'] = $datas['last_accesed'];
            $this->db->insert('campaings',$data_campaing);
            $campaing_row = $this->db->insert_id();
            $watchdog_row = $this->user_model->insert_watchdog($datas['client_id'],'CREATE CAMPAIGN');
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $campaing_row;
            //return $watchdog_row;
        }
    }
    
    public function update_campaing_status($id_campaing, $new_status, $status_date = NULL){
        $campaing_row = NULL;
        $this->load->model('class/campaing_status');        
         try{
            if($new_status == campaing_status::DELETED)
                $end_date = $status_date;
            $this->db->where('id', $id_campaing);
            $this->db->update('campaings', array('campaing_status_id' => $new_status, 'end_date' => $end_date));                        
            $campaing_row = $this->db->affected_rows();
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $campaing_row;
        }        
    }
    
    public function update_daily_value($id_campaing, $new_total_daily_value, $new_available_daily_value){
        $campaing_row = NULL;
        $this->load->model('class/campaing_status');        
         try{            
            $this->db->where('id', $id_campaing);
            $this->db->update('campaings', array('total_daily_value' => $new_total_daily_value, 'available_daily_value' => $new_available_daily_value));                        
            $campaing_row = $this->db->affected_rows();
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $campaing_row;
        }        
    }
    
    public function cancel_campaing($id_campaing, $status_date){
        
        $campaing_row = NULL;
        $this->load->model('class/campaing_status');        
        $this->load->model('class/profiles_status');
         try{
            $campaing_row = $this->update_campaing_status($id_campaing, campaing_status::DELETED, $status_date);
            
            $this->db->where('campaing_id', $id_campaing);
            $this->db->update('profiles', array('profile_status_id' => profiles_status::CANCELED,
                                                'profile_status_date' => $status_date));                        
          
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $campaing_row;
        }        
    }
    
    public function cancel_campaings($campaings_id, $status_date){
        $result = NULL;
        $this->load->model('class/campaing_status');
        $this->load->model('class/profiles_status');        
         try{
            $this->db->where('campaing_status_id !=', campaing_status::DELETED);
            $this->db->where_in('id', $campaings_id);
            $this->db->update('campaings', array(   'campaing_status_id' => campaing_status::DELETED,
                                                    'end_date' => $status_date));              
            $result =  $this->db->affected_rows();
            $this->db->where_in('campaing_id', $campaings_id);
            $this->db->update('profiles', array('profile_status_id' => profiles_status::CANCELED,
                                                'profile_status_date' => $status_date));  
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $result;
        }        
    }
    
    public function pause_campaings($campaings_id, $status_date){
        $result = NULL;
        $this->load->model('class/campaing_status');
         try{
            $this->db->where('campaing_status_id !=', campaing_status::DELETED);
            $this->db->where('campaing_status_id !=', campaing_status::CREATED);
            $this->db->where('campaing_status_id !=', campaing_status::ENDED);
            $this->db->where_in('id', $campaings_id);
            $this->db->update('campaings', array(   'campaing_status_id' => campaing_status::PAUSED,
                                                    'end_date' => $status_date));              
            $result =  $this->db->affected_rows();
            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $result;
        }        
    }
    
    //retorna campañas del cliente, dada una fila de la tabla cliente
    public function get_campaings_client($id_client_row, $cancel_campaing = NULL){
        $this->load->model('class/campaing_status');        
        $campaings_rows = NULL;
        try{
            $this->db->select('*');
            $this->db->from('campaings');
            $this->db->where( array('client_id' => $id_client_row['user_id']) );
            if(!$cancel_campaing)
                $this->db->where('campaing_status_id !=', campaing_status::DELETED);
            $campaings_rows =  $this->db->get()->result_array();
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $campaings_rows;
        }
    }  
    
    public function verify_campaing_client($id_client, $id_campaing){         
        $result = NULL;
        try{
            $this->db->select('*');
            $this->db->from('campaings');            
            $this->db->where(array('client_id' => $id_client));
            $this->db->where(array('id' => $id_campaing));
            $result = $this->db->get()->row_array();            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $result;
        }   
    } 
    
    //inserta un perfil en una campaña
    public function insert_profile($datas){
        $id_profile_row = NULL;
        try{
            $data_profile['insta_id'] = $datas['insta_id'];
            $data_profile['campaing_id'] = $datas['campaing_id'];
            $data_profile['profile'] = $datas['profile'];
            $data_profile['cursor'] = $datas['cursor'];            
            //$data_profile['end_date'] = $datas['end_date'];
            $data_profile['profile_status_id'] = $datas['profile_status_id'];
            $data_profile['profile_status_date'] = $datas['profile_status_date'];
            $data_profile['profile_type_id'] = $datas['profile_type_id'];
            $data_profile['amount_leads'] = $datas['amount_leads'];
            $data_profile['amount_analysed_profiles'] = $datas['amount_analysed_profiles'];
            
            $this->db->insert('profiles',$data_profile);
            $id_profile_row = $this->db->insert_id();
            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $id_profile_row;
        }
    }    
    
    //inserta perfiles en una campaña
    public function insert_profiles($datas){
        $results_profile_rows = NULL;
        try{            
            $this->db->insert_batch('profiles',$datas);
            $results_profile_rows = $this->db->affected_rows();
            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $results_profile_rows;
        }
    }    
    
    public function get_profiles($id_campaing, $fields = '*'){
        $profiles_rows = NULL;
        $this->load->model('class/profiles_status');
        try{
            $this->db->select($fields);
            $this->db->from('profiles');
            $this->db->where( array('campaing_id' => $id_campaing) );
            $this->db->where('profile_status_id <>', profiles_status::CANCELED);
            //$this->db->where('profile_status_id <>', profiles_status::MISSING);
            $profiles_rows =  $this->db->get()->result_array();
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $profiles_rows;
        }
    }

    public function get_profile($id_campaing, $profile){
        $profile_row = NULL;
        $this->load->model('class/profiles_status');
        try{
            $this->db->select('*');
            $this->db->from('profiles');
            $this->db->where( array('campaing_id' => $id_campaing) );
            $this->db->where( array('profile' => $profile) );
            $this->db->where('profile_status_id <>', profiles_status::CANCELED);
            //$this->db->where('profile_status_id <>', profiles_status::MISSING);
            $profile_row =  $this->db->get()->row_array();
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $profile_row;
        }
    }
    
    public function delete_profile($id_campaing, $profile_insta){
        $result = NULL;
        $this->load->model('class/profiles_status');
        try{            
            $this->db->where( array('campaing_id' => $id_campaing) );
            $this->db->where( array('insta_id'=> $profile_insta) );
            $this->db->update('profiles', array('profile_status_id' => profiles_status::CANCELED,
                                                'profile_status_date' => time()));                                                
            $result = $this->db->affected_rows();
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $result;
        }
    }    
    
    public function update_profile_accesed($id_campaing, $id_profile, $time_to_update){
        $result = NULL;
        try{            
            $this->db->where( array('campaing_id' => $id_campaing) );
            $this->db->where( array('id'=> $id_profile) );
            $this->db->update('profiles', array( 'last_accesed' => $time_to_update ));                        
            $result = $this->db->affected_rows();
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $result;
        }
    }    

    public function in_status($id_campaing, $status){
        $result = false;
        try{
            $this->db->select('*');
            $this->db->from('campaings');
            $this->db->where( array('id' => $id_campaing) );
            $this->db->where( array('campaing_status_id' => $status) );
            $campaing_row =  $this->db->get()->row_array();
            if($campaing_row)
                $result = true;
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $result;
        }    
    }
    
    public function has_type($id_campaing, $profile_type){
        $result = false;
        try{
            $this->db->select('*');
            $this->db->from('campaings');
            $this->db->where( array('id' => $id_campaing) );
            $this->db->where( array('campaing_type_id' => $profile_type) );
            $campaing_row =  $this->db->get()->row_array();
            if($campaing_row)
                $result = true;
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $result;
        }    
    }
    
    public function get_campaing($id_campaing){
        $campaing_row = NULL;
        try{
            $this->db->select('*');
            $this->db->from('campaings');
            $this->db->where( array('id' => $id_campaing) );            
            $campaing_row =  $this->db->get()->row_array();            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $campaing_row;
        }    
    }
  
    public function get_delete_profile($id_campaing, $profile_insta){
        $profile_row = NULL;
        $this->load->model('class/campaing_status');
        $this->load->model('class/profiles_status');
        try{
            $this->db->select('*');
            $this->db->from('profiles');
            $this->db->where( array('campaing_id' => $id_campaing) );
            $this->db->where( array('insta_id' => $profile_insta) );
            $this->db->where('profile_status_id', profiles_status::CANCELED);
            $profile_row =  $this->db->get()->row_array();
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $profile_row;
        }
    }
    
    public function update_profile($id_campaing, $old_profile_row){
        $profile_row = NULL;        
        $this->load->model('class/campaing_status');
        $this->load->model('class/profiles_status');
        try{
            $new_data['delete'] = $old_profile_row['delete'];
            $id_profile = $old_profile_row['id'];
            $this->db->where('campaing_id', $id_campaing);
            $this->db->where('id', $id_profile);
            $this->db->update('profiles', array('profile_status_id' => profiles_status::ACTIVE,
                                                'profile_status_date' => time()));                                                
            $profile_row = $this->db->affected_rows();
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $profile_row;
        }        
    }
    
    /*Obtiene leads de una campaña 
     * Puede filtrarse además por un perfil*/    
    public function get_leads($id_client, $id_campaing = NULL, $id_profile = NULL, $init_date = NULL, $end_date = NULL, $info_to_get){
        $result = NULL;
        $data_lead = array();        
        $decode_fields = ['perfil' => 'profile',
                          'username' => 'username',
                          'name' => 'full_name',
                          'code_coutry' => 'country_code',
                          'telf' => 'phone_number',
                          'public_telf' => 'public_phone_number',
                          'contact_telf' => 'contact_phone_number',
                          'sexo' => 'gender',
                          'categoria' => 'category',
                          'data_nascimento' => 'birthday',
                          'privativo' => 'is_business'
                            ];
        $to_decode =    ['profile' => FALSE,
                          'username' => TRUE,
                          'full_name' => TRUE,
                          'country_code' => TRUE,
                          'phone_number' => TRUE,
                          'public_phone_number' => TRUE,
                          'contact_phone_number' => TRUE,
                          'gender' => TRUE,
                          'category' => FALSE,
                          'private_email' => TRUE,
                          'biography_email' => TRUE,
                          'public_email' => TRUE,
                          'birthday' => TRUE,
                          'is_business' => TRUE,
                          'campaing_id' => FALSE
                        ];
        
        $fields = 'leads.private_email, leads.biography_email, leads.public_email';
        
        foreach($info_to_get['inf'] as $key => $field){            
            if($field != 'all_email'){                
                if($field != 'perfil' && $decode_fields[$field]){
                    $fields .= ', leads.'.$decode_fields[$field];
                }
                else{
                    $fields .= ', profiles.'.$decode_fields[$field];
                }
            }            
        }
        
        try{
            //$this->db->select('leads.sold');
            $this->db->select( $fields.", campaing_id" );
            $this->db->from('leads');            
            $this->db->join('profiles', 'profiles.id = leads.reference_profile_id');
            $this->db->join('campaings', 'campaings.id = profiles.campaing_id');
            $this->db->join('clients', 'campaings.client_id = clients.user_id');
            $this->db->where('clients.user_id',$id_client);  
            if($id_campaing != NULL)
                $this->db->where('campaings.id',$id_campaing);
            //$this->db->where('profiles.deleted', 0);    //revisar esto
            $this->db->where('leads.sold',1);
                        
            if($id_profile)
                $this->db->where('profiles.id',$id_profile);
            
            if($init_date)
                $this->db->where('leads.extracted_date >= ', $init_date);
            
            if($end_date)
                $this->db->where('leads.extracted_date <= ', $end_date);
            
            $this->db->order_by('profiles.campaing_id', "asc");
            
            $result =  $this->db->get()->result_array();
            
            $cant_leads = count($result);
            for($i = 0; $i < $cant_leads; $i++){
                $all_email = false;
                $data_lead[$i]['id_campaing'] = "ID_".$result[$i]['campaing_id'];
                foreach($result[$i] as $key => $field){
                    if($to_decode[$key])
                        $result[$i][$key] = $this->decrypt($field);
                }
                
                foreach($info_to_get['inf'] as $key => $field){
                    if($field != 'all_email'){
                        $data_lead[$i][$field] = $result[$i][$decode_fields[$field]];
                    }
                    else{
                        $all_email = true;                       
                        $data_lead[$i]['public_email'] = $result[$i]['public_email'];
                        $data_lead[$i]['private_email'] = $result[$i]['private_email'];
                        $data_lead[$i]['biography_email'] = $result[$i]['biography_email'];                        
                    }
                }
                if(!$all_email){
                    if($result[$i]['public_email']){
                        $data_lead[$i]['e_mail'] = $result[$i]['public_email'];
                    }
                    else{
                        if($result[$i]['private_email']){
                            $data_lead[$i]['e_mail'] = $result[$i]['private_email'];
                        }
                        else{
                            if($result[$i]['biography_email']){
                                $data_lead[$i]['e_mail'] = $result[$i]['biography_email'];
                            }
                        }
                    }
                }
            }                
            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $data_lead;
        }        
    }
    
    public function get_leads_limit($id_client, $id_campaing = NULL, $id_profile = NULL, $init_date = NULL, $end_date = NULL, $info_to_get, &$max_id){
        $result = NULL;
        $data_lead = array();        
        $decode_fields = ['perfil' => 'profile',
                          'username' => 'username',
                          'name' => 'full_name',
                          'code_coutry' => 'country_code',
                          'telf' => 'phone_number',
                          'public_telf' => 'public_phone_number',
                          'contact_telf' => 'contact_phone_number',
                          'sexo' => 'gender',
                          'categoria' => 'category',
                          'data_nascimento' => 'birthday',
                          'privativo' => 'is_business'
                            ];
        $to_decode =    ['profile' => FALSE,
                          'username' => TRUE,
                          'full_name' => TRUE,
                          'country_code' => TRUE,
                          'phone_number' => TRUE,
                          'public_phone_number' => TRUE,
                          'contact_phone_number' => TRUE,
                          'gender' => TRUE,
                          'category' => FALSE,
                          'private_email' => TRUE,
                          'biography_email' => TRUE,
                          'public_email' => TRUE,
                          'birthday' => TRUE,
                          'is_business' => TRUE,
                          'campaing_id' => FALSE,
                          'id' => FALSE
                        ];
        
        $fields = 'leads.private_email, leads.biography_email, leads.public_email';
        
        foreach($info_to_get as $key => $field){            
            if($field != 'all_email'){                
                if($field != 'perfil'){
                    if(in_array($field, $info_to_get) && $decode_fields[$field]){
                        $fields .= ', leads.'.$decode_fields[$field];
                    }
                }
                else{
                    $fields .= ', profiles.'.$decode_fields[$field];
                }
            }            
        }
        
        try{
            //$this->db->select('leads.sold');
            if(!$max_id)
                $max_id = 0;
            $this->db->select( $fields.", campaing_id, leads.id" );
            $this->db->from('leads');            
            $this->db->join('profiles', 'profiles.id = leads.reference_profile_id');
            $this->db->join('campaings', 'campaings.id = profiles.campaing_id');
            $this->db->join('clients', 'campaings.client_id = clients.user_id');
            $this->db->where('clients.user_id',$id_client);  
            $this->db->where('leads.id >',$max_id);
            
            if($id_campaing != NULL)
                $this->db->where('campaings.id',$id_campaing);
            //$this->db->where('profiles.deleted', 0);    //revisar esto
            $this->db->where('leads.sold',1);
                        
            if($id_profile)
                $this->db->where('profiles.id',$id_profile);
            
            if($init_date)
                $this->db->where('leads.extracted_date >= ', $init_date);
            
            if($end_date)
                $this->db->where('leads.extracted_date <= ', $end_date);
            
            $this->db->order_by('leads.id', "asc");
            
            $this->db->limit(10000);    //maximo solicitado por consulta
            $result =  $this->db->get()->result_array();
            
            $cant_leads = count($result);
            for($i = 0; $i < $cant_leads; $i++){                
                $max_id = $result[$i]['id'];
                $all_email = false;
                $data_lead[$i]['id_campaing'] = "ID_".$result[$i]['campaing_id'];
                foreach($result[$i] as $key => $field){
                    if($to_decode[$key])
                        $result[$i][$key] = $this->decrypt($field);
                }
                
                foreach($info_to_get as $key => $field){
                    if($field != 'all_email'){
                        $data_lead[$i][$field] = $result[$i][$decode_fields[$field]];
                    }
                    else{
                        $all_email = true;                       
                        $data_lead[$i]['public_email'] = $result[$i]['public_email'];
                        $data_lead[$i]['private_email'] = $result[$i]['private_email'];
                        $data_lead[$i]['biography_email'] = $result[$i]['biography_email'];                        
                    }
                }
                if(!$all_email){
                    if($result[$i]['public_email']){
                        $data_lead[$i]['e_mail'] = $result[$i]['public_email'];
                    }
                    else{
                        if($result[$i]['private_email']){
                            $data_lead[$i]['e_mail'] = $result[$i]['private_email'];
                        }
                        else{
                            if($result[$i]['biography_email']){
                                $data_lead[$i]['e_mail'] = $result[$i]['biography_email'];
                            }
                        }
                    }
                }
            }                
            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $data_lead;
        }        
    }
    
    public function get_num_leads($id_client, $id_campaing = NULL, $id_profile = NULL, $init_date = NULL, $end_date = NULL, $info_to_get){
        $result = 0;
        $data_lead = array();        
        $decode_fields = ['perfil' => 'profile',
                          'username' => 'username',
                          'name' => 'full_name',
                          'code_coutry' => 'country_code',
                          'telf' => 'phone_number',
                          'public_telf' => 'public_phone_number',
                          'contact_telf' => 'contact_phone_number',
                          'sexo' => 'gender',
                          'categoria' => 'category',
                          'data_nascimento' => 'birthday',
                          'privativo' => 'is_business'
                            ];
        $to_decode =    ['profile' => FALSE,
                          'username' => TRUE,
                          'full_name' => TRUE,
                          'country_code' => TRUE,
                          'phone_number' => TRUE,
                          'public_phone_number' => TRUE,
                          'contact_phone_number' => TRUE,
                          'gender' => TRUE,
                          'category' => FALSE,
                          'private_email' => TRUE,
                          'biography_email' => TRUE,
                          'public_email' => TRUE,
                          'birthday' => TRUE,
                          'is_business' => TRUE,
                          'campaing_id' => FALSE,
                          'id' => FALSE
                        ];
        
        $fields = 'leads.private_email, leads.biography_email, leads.public_email';
        
        foreach($info_to_get as $key => $field){
            if($field != 'all_email'){                
                if($field != 'perfil'){
                    $fields .= ', leads.'.$decode_fields[$field];
                }
                else{
                    $fields .= ', profiles.'.$decode_fields[$field];
                }
            }
        }
        
        try{
            //$this->db->select('leads.sold');        
            $this->db->select( "leads.id" );
            $this->db->from('leads');            
            $this->db->join('profiles', 'profiles.id = leads.reference_profile_id');
            $this->db->join('campaings', 'campaings.id = profiles.campaing_id');
            $this->db->join('clients', 'campaings.client_id = clients.user_id');
            $this->db->where('clients.user_id',$id_client);              
            
            if($id_campaing != NULL)
                $this->db->where('campaings.id',$id_campaing);
            //$this->db->where('profiles.deleted', 0);    //revisar esto
            $this->db->where('leads.sold',1);
                        
            if($id_profile)
                $this->db->where('profiles.id',$id_profile);
            
            if($init_date)
                $this->db->where('leads.extracted_date >= ', $init_date);
            
            if($end_date)
                $this->db->where('leads.extracted_date <= ', $end_date);
            
            $this->db->order_by('leads.id', "asc");
                        
            $result = $this->db->count_all_results();            
            return $result;  
            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $result;
        }        
    }
    
    public function crypt($str_plane){
        $seed = "mi chicho lindo";
        $key_number = md5($seed);
        $cipher = "aes-256-ctr";
        $tag = 'GCM';
        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $str = openssl_encrypt ($str_plane, $cipher, $key_number,$options=0, '1234567812345678');
        return base64_encode($str);
    }

    public function decrypt($str_encrypted){
        $seed = "mi chicho lindo";
        $key_number = md5($seed);
        $cipher = "aes-256-ctr";
        $tag = 'GCM';
        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $str_encrypted= base64_decode($str_encrypted);
        $str = openssl_decrypt ($str_encrypted, $cipher, $key_number,$options=0, '1234567812345678');
        return $str;
    }
}

?>