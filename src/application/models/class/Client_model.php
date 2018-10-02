<?php

class Client_model extends CI_Model {
    
    
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
    
    public function insert_client($datas){
        $client_row_results = NULL;
        try{
            $data_client['user_id']=$datas['user_id'];  
            $data_client['insta_id']=$datas['insta_id'];
            $data_client['HTTP_SERVER_VARS']=$datas['HTTP_SERVER_VARS'];          
            $data_client['last_accesed']= $datas['last_accesed'];
            $data_client['observation']= $datas['observation'];            
            
            $this->db->insert('clients',$data_client);
            $client_row_results = $this->db->affected_rows();
            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $client_row_results;
        }
    }
   
    //retorna un registro con el cliente que posea el id dado como parametro 
    public function get_client_by_id($id_user){
        $client_row = NULL;
         try{
            $this->db->select('*');
            $this->db->from('clients');
            $this->db->where(array('user_id' => $id_user));
            $client_row =  $this->db->get()->row_array();
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $client_row;
        }
    }
    
    //obtener todas las campañas de un cliente o una, especificando campos (opcional)
    public function client_get_campaings($id_client, $fields = '*', $id_campaing = NULL, $cancel_campaing = NULL){
        $this->load->model('class/campaing_status');        
        $result = NULL;
         try{
            $this->db->select($fields);
            $this->db->from('campaings');            
            $this->db->where('client_id',$id_client);
            if(!$cancel_campaing)
                $this->db->where('campaing_status_id !=', campaing_status::DELETED);
            if($id_campaing){
                $this->db->where('id',$id_campaing);
                $result = $this->db->get()->row_array();
            }
            else{
                $result =  $this->db->get()->result_array();
            }
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $result;
        }        
    }
    
    public function client_cancel_campaings($id_client, $status_date){
        $this->load->model('class/campaing_status');
        $this->load->model('class/campaing_model');
        $result = NULL;
        try{          
            $campaings = $this->client_get_campaings($id_client,'id');
            
            foreach ($campaings as $campaing) {
                $campaings_id[] = $campaing['id'];
            }            
            $result = $this->campaing_model->cancel_campaings($campaings_id, $status_date);
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $result;
        }        
    }
    
    public function client_pause_campaings($id_client, $status_date){
        $this->load->model('class/campaing_status');
        $this->load->model('class/campaing_model');
        $result = NULL;
        try{          
            $campaings = $this->client_get_campaings($id_client,'id');
            
            foreach ($campaings as $campaing) {
                $campaings_id[] = $campaing['id'];
            }            
            $result = $this->campaing_model->pause_campaings($campaings_id, $status_date);
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $result;
        }        
    }
    
    /*obtiene la(s) campañas(s) de un cliente con los perfiles no borrados. 
     * Puede filtrarse además por el status de la campaña*/    
    public function get_campaings_and_profiles($id_client, $id_campaing = NULL, $status = NULL, $init_date = NULL, $end_date = NULL, $active_prof = NULL){
        $result = NULL;
        $this->load->model('class/profiles_status');
         try{
            $this->db->select('*');
            $this->db->from('campaings');            
            $this->db->join('profiles', 'campaings.id = profiles.campaing_id');
            $this->db->where('campaings.client_id',$id_client);
            if($active_prof)
                $this->db->where('profiles.profile_status_id', profiles_status::ACTIVE);
            
            if($id_campaing)
                $this->db->where('campaings.id',$id_campaing);
            
            if($status)
                $this->db->where('campaings.campaing_status_id', $status);
            
            if($init_date)
                $this->db->where('campaings.created_date >= ', $init_date);
            
            if($end_date)
                $this->db->where('campaings.created_date <= ', $end_date);
            
            $this->db->where('profiles.profile_status_id <>', profiles_status::CANCELED);
            
            $this->db->order_by('campaings.created_date','DESC');
            
            $result =  $this->db->get()->result_array();
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $result;
        }        
    }
    
    public function get_canceled_campaings_and_profiles($id_client, $init_date = NULL, $end_date = NULL){
        $result = NULL;
        $this->load->model('class/campaing_status');
        $this->load->model('class/profiles_status');
         try{
            $this->db->select('*,campaings.end_date');
            $this->db->from('campaings');            
            $this->db->join('profiles', 'campaings.id = profiles.campaing_id');
            $this->db->where('campaings.client_id',$id_client);
            //$this->db->where('profiles.profile_status_id <>', profiles_status::ACTIVE);
            //$this->db->where('profiles.profile_status_id <>', profiles_status::ENDED);
            $this->db->where('campaings.campaing_status_id', campaing_status::DELETED);                        
            
            if($init_date)
                $this->db->where('campaings.created_date >= ', $init_date);
            
            if($end_date)
                $this->db->where('campaings.created_date <= ', $end_date);                         
            
            $this->db->order_by('campaings.end_date','DESC');
            
            $result =  $this->db->get()->result_array();
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $result;
        }        
    }
    
    
    public function check_for_repeated_profiles($id_client, $new_profiles, $type_profile, $check_deleted = NULL){
        $result = NULL;
        $this->load->model('class/profiles_status');
         try{
            $this->db->select('*');
            $this->db->from('profiles');
            $this->db->join('campaings', 'campaings.id = profiles.campaing_id');
            $this->db->where( array('campaings.client_id' => $id_client) );
            if(!$check_deleted){
                $this->db->where('profiles.profile_status_id <>', profiles_status::CANCELED);
                $this->db->where('profiles.profile_status_id <>', profiles_status::MISSING);
            }
            $this->db->where('profiles.profile_type_id', $type_profile);
            $this->db->where_in('profiles.insta_id', $new_profiles );  
            
            $result =  $this->db->get()->result_array();
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $result;
        }        
    }
    
    public function load_campaings($id_cliente, $campaing_id = NULL, $init_date = NULL, $end_date = NULL){
        $this->load->model('class/campaing_status');
        $this->load->model('class/campaing_type');
        
        $status = [ campaing_status::CREATED => 'CRIADA', campaing_status::ACTIVE => 'ATIVA',
                    campaing_status::PAUSED => 'PAUSADA', campaing_status::DELETED => 'CANCELADA',
                    campaing_status::ENDED => 'TERMINADA'];
        $type = [   campaing_type::REFERENCE_PROFILE => 'PERFIS', 
                    campaing_type::GEOLOCATION => 'GEOLOCALICAÇÃO',
                    campaing_type::HASHTAG => 'HASHTAG'
                    ];
        
        $total_captados = 0;
        $result = $this->get_campaings_and_profiles($id_cliente, $campaing_id, NULL, $init_date, $end_date);
        $id_campaing = 0;        
        $index_campaing = -1;
        $campaing = [];
        foreach( $result as $row ){
            if($row['campaing_status_id'] != campaing_status::DELETED){
                if($id_campaing != $row['campaing_id']){
                    $id_campaing = $row['campaing_id'];
                    $index_campaing++;
                    $campaing[$index_campaing]['amount_leads'] = $this->fast_leads_to_pay_by_campaing($id_campaing);                    
                    //$campaing[$index_campaing]['amount_leads'] = $this->amount_leads_to_pay($id_cliente, $id_campaing, true);

                    $campaing[$index_campaing]['campaing_id'] = $row['campaing_id'];
                    $campaing[$index_campaing]['campaing_type_id'] = $row['campaing_type_id'];
                    $campaing[$index_campaing]['campaing_type_id_string'] = $type[$row['campaing_type_id']];
                    $campaing[$index_campaing]['campaing_status_id'] = $row['campaing_status_id'];
                    $campaing[$index_campaing]['campaing_status_id_string'] = $status[$row['campaing_status_id']];
                    $campaing[$index_campaing]['total_daily_value'] = $row['total_daily_value'];
                    $campaing[$index_campaing]['available_daily_value'] = $row['available_daily_value'];
                    $campaing[$index_campaing]['client_objetive'] = $row['client_objetive'];
                    $campaing[$index_campaing]['created_date'] = $row['created_date'];
                    $campaing[$index_campaing]['end_date'] = $row['end_date'];
                    //$campaing[$index_campaing]['amount_leads'] = $row['amount_leads'];                        
                    //$campaing[$index_campaing]['amount_analysed_profiles'] = $row['amount_analysed_profiles'];                        
                }

                $profile['profile'] = $row['profile'];
                $profile['insta_id'] = $row['insta_id'];
                $profile['id'] = $row['id'];
                //$campaing[$index_campaing]['amount_leads'] += $row['amount_leads'];
                //$campaing[$index_campaing]['amount_leads'] += $row['amount_leads'];
                $campaing[$index_campaing]['profile'][] = $profile;
            }
        }
        if(!$campaing_id){
            $result = $this->get_canceled_campaings_and_profiles($id_cliente, $init_date, $end_date);
            
            foreach( $result as $row ){
                if($id_campaing != $row['campaing_id']){
                    $id_campaing = $row['campaing_id'];
                    $index_campaing++;
                    $campaing[$index_campaing]['amount_leads'] = $this->fast_leads_to_pay_by_campaing($id_campaing);                    
                    //$campaing[$index_campaing]['amount_leads'] = $this->amount_leads_to_pay($id_cliente, $id_campaing, true);
                                        
                    $campaing[$index_campaing]['campaing_id'] = $row['campaing_id'];
                    $campaing[$index_campaing]['campaing_type_id'] = $row['campaing_type_id'];
                    $campaing[$index_campaing]['campaing_type_id_string'] = $type[$row['campaing_type_id']];
                    $campaing[$index_campaing]['campaing_status_id'] = $row['campaing_status_id'];
                    $campaing[$index_campaing]['campaing_status_id_string'] = $status[$row['campaing_status_id']];
                    $campaing[$index_campaing]['total_daily_value'] = $row['total_daily_value'];
                    $campaing[$index_campaing]['available_daily_value'] = $row['available_daily_value'];
                    $campaing[$index_campaing]['client_objetive'] = $row['client_objetive'];
                    $campaing[$index_campaing]['created_date'] = $row['created_date'];
                    $campaing[$index_campaing]['end_date'] = $row['end_date'];
                    //$campaing[$index_campaing]['amount_leads'] = $row['amount_leads'];                        
                    //$campaing[$index_campaing]['amount_analysed_profiles'] = $row['amount_analysed_profiles'];                        
                }

                $profile['profile'] = $row['profile'];
                $profile['insta_id'] = $row['insta_id'];
                $profile['id'] = $row['id'];
                //$campaing[$index_campaing]['amount_leads'] += $row['amount_leads'];

                $campaing[$index_campaing]['profile'][] = $profile;
            }
        }
        return $campaing;
    }
    
    public function load_campaings1($id_cliente, $campaing_id = NULL){
        $this->load->model('class/campaing_status');
        $this->load->model('class/campaing_type');
        
        $status = [ campaing_status::CREATED => 'CRIADA', campaing_status::ACTIVE => 'ATIVA',
                    campaing_status::PAUSED => 'PAUSADA', campaing_status::DELETED => 'CANCELADA',
                    campaing_status::ENDED => 'TERMINADA'];
        $type = [   campaing_type::REFERENCE_PROFILE => 'PERFIS', 
                    campaing_type::GEOLOCATION => 'GEOLOCALICAÇÃO',
                    campaing_type::HASHTAG => 'HASHTAG'
                    ];
        
        $result = $this->get_campaings_and_profiles($id_cliente, $campaing_id);
        $id_campaing = 0;        
        $index_campaing = -1;
        $campaing = [];
        foreach( $result as $row ){
            if($id_campaing != $row['campaing_id']){
                $id_campaing = $row['campaing_id'];
                $index_campaing++;
                $campaing[$index_campaing]['amount_leads'] = count($this->leads_to_pay($id_cliente, $id_campaing, true));                
                
                $campaing[$index_campaing]['campaing_id'] = $row['campaing_id'];
                $campaing[$index_campaing]['campaing_type_id'] = $row['campaing_type_id'];
                $campaing[$index_campaing]['campaing_type_id_string'] = $type[$row['campaing_type_id']];
                $campaing[$index_campaing]['campaing_status_id'] = $row['campaing_status_id'];
                $campaing[$index_campaing]['campaing_status_id_string'] = $status[$row['campaing_status_id']];
                $campaing[$index_campaing]['total_daily_value'] = $row['total_daily_value'];
                $campaing[$index_campaing]['available_daily_value'] = $row['available_daily_value'];
                $campaing[$index_campaing]['client_objetive'] = $row['client_objetive'];
                $campaing[$index_campaing]['created_date'] = $row['created_date'];
                //$campaing[$index_campaing]['amount_leads'] = $row['amount_leads'];                        
                //$campaing[$index_campaing]['amount_analysed_profiles'] = $row['amount_analysed_profiles'];                        
            }
            
            $profile['profile'] = $row['profile'];
            $profile['insta_id'] = $row['insta_id'];
            $profile['id'] = $row['id'];
            //$campaing[$index_campaing]['amount_leads'] += $row['amount_leads'];

            $campaing[$index_campaing]['profile'][] = $profile;
        }
        if(!$campaing_id){
            $result = $this->client_get_campaings($id_cliente, '*', NULL, true);
            foreach( $result as $row ){
                if($row['campaing_status_id'] == campaing_status::DELETED){
                    $index_campaing++;
                    $campaing[$index_campaing]['amount_leads'] = count($this->leads_to_pay($id_cliente, $id_campaing, true));
                
                    $campaing[$index_campaing]['campaing_id'] = $row['campaing_id'];
                    $campaing[$index_campaing]['campaing_type_id'] = $row['campaing_type_id'];
                    $campaing[$index_campaing]['campaing_type_id_string'] = $type[$row['campaing_type_id']];
                    $campaing[$index_campaing]['campaing_status_id'] = $row['campaing_status_id'];
                    $campaing[$index_campaing]['campaing_status_id_string'] = $status[$row['campaing_status_id']];
                    $campaing[$index_campaing]['total_daily_value'] = $row['total_daily_value'];
                    $campaing[$index_campaing]['available_daily_value'] = $row['available_daily_value'];
                    $campaing[$index_campaing]['client_objetive'] = $row['client_objetive'];
                    $campaing[$index_campaing]['created_date'] = $row['created_date'];
                    $campaing[$index_campaing]['end_date'] = $row['end_date'];
                    $campaing[$index_campaing]['profile'][] = NULL;
                }
            }
        }
        return $campaing;
    }
    
    public function get_clients_to_pay(){
        $this->load->model('class/user_model');        
        $this->load->model('class/user_role');
        $this->load->model('class/user_status');
        
        $clients = NULL;
        try{
            $this->db->select('*');
            $this->db->from('clients');
            $this->db->join('users', 'clients.user_id = users.id');
            $this->db->where('role_id', user_role::CLIENT);
            $this->db->where('status_id <>', user_status::BEGINNER);
            $this->db->where('status_id <>', user_status::DONT_DISTURB);
            $this->db->where('status_id <>', user_status::DELETED);
            $this->db->where('status_id <>', user_status::BLOCKED_BY_PAYMENT);
            $clients = $this->db->get()->result_array();
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $clients;
        }        
    }
    
    public function fast_leads_to_pay_by_campaing($id_campaing){         
        $count = 0;
         try{
            $this->db->select('*');                
            $this->db->from('profiles');                        
            $this->db->where('campaing_id',$id_campaing);                  
            $result = $this->db->get()->result_array();
            foreach ($result as $row) {
                    $count += $row['amount_leads'];
            }
            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $count;
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
    
    public function amount_leads_to_pay($id_client, $id_campaing = NULL, $all = NULL){         
        $result = 0;
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
            $result = $this->db->count_all_results();            
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $result;
        }                
    }
    
    public function amount_leads_by_client($id_client, $id_campaing = NULL, $all = NULL){         
        $result = 0;
         try{
            $query = "SELECT COUNT(dumbu_emails_db.leads.id) FROM dumbu_emails_db.leads ";
            $query .= "INNER JOIN dumbu_emails_db.profiles ON dumbu_emails_db.profiles.id = dumbu_emails_db.leads.reference_profile_id ";
            $query .= "INNER JOIN dumbu_emails_db.campaings ON dumbu_emails_db.campaings.id = dumbu_emails_db.profiles.campaing_id ";
            $query .= "INNER JOIN dumbu_emails_db.clients ON dumbu_emails_db.campaings.client_id = dumbu_emails_db.clients.user_id ";
            $query .= "WHERE dumbu_emails_db.clients.user_id = ".$id_client;
            if($id_campaing){
                $query .= " AND dumbu_emails_db.campaings.id = ".$id_campaing; 
            }
            if(!$all){
                $query .= " AND dumbu_emails_db.leads.sold = 0"; 
            }
            $query .= ";";
            $query_result = $this->db->query($query);
            $result_row = $query_result->row_array();            
            $result = $result_row['COUNT(dumbu_emails_db.leads.id)'];//->num_rows();           
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
    
    public function freeze_client($id_client){
        
    }
    
    public function defrost_client($id_client){
        
    }
    
    public function get_delete_profile($id_client, $profile){
        $this->load->model('class/profiles_status');
        $profile_row = NULL;
        try{            
            $this->db->select('*');
            $this->db->from('profiles');
            $this->db->join('campaings', 'campaings.id = profiles.campaing_id');
            $this->db->where( array('campaings.client_id' => $id_client) );            
            $this->db->where( array('profile' => $profile) );
            //$this->db->where( array('deleted' => 1) );
            $this->db->where('profiles.profile_status_id', profiles_status::CANCELED);
            $profile_row =  $this->db->get()->row_array();
        } catch (Exception $exception) {
            echo 'Error accediendo a la base de datos';
        } finally {
            return $profile_row;
        }
    }
}

?>