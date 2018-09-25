<?php

namespace leads\cls {
    
    require_once 'User.php';
    require_once 'campaing_status.php';  
    require_once 'profiles_status.php';
    require_once 'DB.php';
    require_once 'user_role.php';
    require_once 'user_status.php';
    require_once 'payment_type.php';
    
        
    class Payment_BD {

        public function get_clients_to_pay() {  
            $clients = array();
            $DB = new \leads\cls\DB();
            $CLIENT = user_role::CLIENT;
            $BEGINNER = user_status::BEGINNER;
            $DONT_DISTURB = user_status::DONT_DISTURB;
            $DELETED = user_status::DELETED;
            $BLOCKED_BY_PAYMENT = user_status::BLOCKED_BY_PAYMENT;
            try {
                $DB->connect();
                $sql = ""
                        . "SELECT * FROM clients "
                        . "INNER JOIN users ON clients.user_id = users.id "
                        . "WHERE users.role_id = $CLIENT "
                        . "AND users.status_id <> $BEGINNER "
                        . "AND users.status_id <> $DONT_DISTURB "
                        . "AND users.status_id <> $DELETED "
                        . "AND users.status_id <> $BLOCKED_BY_PAYMENT; ";
                
                $clients_to_pay = mysqli_query($DB->connection, $sql);
                while ($client_data= $clients_to_pay->fetch_array()) {
                    array_push($clients,$this->fill_client_data($client_data));
                }
                return $clients;
            } catch (\Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }
        
        public function fill_client_data($client_data){
            if ($client_data) {
                $datas = array(
                    'user_id'=> $client_data['user_id'],
                    'status_date'=> $client_data['status_date'],
                    'email'=> $client_data['email'],
                    'status_id'=> $client_data['status_id'],
                    'brazilian'=> $client_data['brazilian'],
                    'login'=> $client_data['login'],
                    'language'=> $client_data['language']
                );
            }
            return  $datas;
         }
         
         public function leads_to_pay($id_client) {  
            $leads = array();
            $DB = new \leads\cls\DB();
            
            try {
                $DB->connect();
                $sql = ""
                        . "SELECT l.id "
                        . "FROM leads l "
                        . "INNER JOIN profiles p ON p.id = l.reference_profile_id "
                        . "INNER JOIN campaings c ON c.id = p.campaing_id "
                        . "INNER JOIN clients t ON c.client_id = t.user_id "
                        . "WHERE t.user_id = $id_client "
                        . "AND l.sold = 0; ";
                
                $leads_to_pay = mysqli_query($DB->connection, $sql);
                while ($leads_data= $leads_to_pay->fetch_array()) {
                    array_push($leads, $leads_data);//$this->fill_client_data($leads_data));
                }
                return $leads;
            } catch (\Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }
        
        public function get_charged_bank_ticket($id_client) {  
            $ticket = array();
            $DB = new \leads\cls\DB();
            
            try {
                $DB->connect();
                $sql = ""
                        . "SELECT * "
                        . "FROM bank_ticket "                        
                        . "WHERE client_id = $id_client; ";
                
                $ticket_query = mysqli_query($DB->connection, $sql);
                while ($ticket_data= $ticket_query->fetch_array()) {
                    array_push($ticket, $ticket_data);
                }
                return $ticket;
            } catch (\Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }
        
        public function update_bank_ticket($id_client, $id_ticket, $new_amount) {  
            $DB = new \leads\cls\DB();            
            try {
                $DB->connect();
                $sql = ""
                        . "UPDATE bank_ticket "
                        . "SET amount_used_value = $new_amount "                        
                        . "WHERE client_id = $id_client "
                        . "AND id = $id_ticket; ";   
                } catch (\Exception $exc) {
                    echo $exc->getTraceAsString();
                }

            return mysqli_query($DB->connection, $sql);                
        }
        
        public function get_credit_card($id_client) {  
            $card_row = array();
            $DB = new \leads\cls\DB();
            
            try {
                $DB->connect();
                $sql = ""
                        . "SELECT * "
                        . "FROM credit_cards "                        
                        . "WHERE client_id = $id_client; ";
                
                $card_query = mysqli_query($DB->connection, $sql);
                $card_row= $card_query->fetch_array();
                if(!$card_row)
                    return NULL;
                
                $key_number = md5($id_client.$card_row['credit_card_exp_month'].$card_row['credit_card_exp_year']);
                $key_cvc = md5($key_number);
                $cipher_number = $card_row['credit_card_number'];
                $cipher_cvc = $card_row['credit_card_cvc'];
                $card_row['credit_card_number'] = openssl_decrypt ( $cipher_number , "aes-256-ctr" , $key_number);                
                $card_row['credit_card_cvc'] = openssl_decrypt ( $cipher_cvc , "aes-256-ctr" , $key_cvc);
                
                return $card_row;
            } catch (\Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }
        
        public function set_pendent_client($id_client, $status_date) {  
            $DB = new \leads\cls\DB(); 
            $PAUSED = campaing_status::PAUSED;
            $DELETED = campaing_status::DELETED;
            $CREATED = campaing_status::CREATED;
            $ENDED = campaing_status::ENDED;
            $PENDENT = user_status::PENDENT_BY_PAYMENT;
            
            try {
                $DB->connect();
                $sql = ""
                        . "UPDATE campaings "
                        . "SET campaing_status_id = $PAUSED "                        
                        . "WHERE campaing_status_id != $DELETED "
                        . "AND campaing_status_id != $CREATED "   
                        . "AND campaing_status_id != $ENDED "   
                        . "AND client_id = $id_client; ";   
                
                } catch (\Exception $exc) {
                    echo $exc->getTraceAsString();
                }
            if(mysqli_query($DB->connection, $sql)){
                return $this->update_status_user($id_client, $PENDENT, $status_date);
            }
        }
        
        public function set_blocked_client($id_client, $status_date) {  
            $DB = new \leads\cls\DB(); 
            $PAUSED = campaing_status::PAUSED;
            $DELETED = campaing_status::DELETED;
            $CREATED = campaing_status::CREATED;
            $ENDED = campaing_status::ENDED;
            $BLOCKED = user_status::BLOCKED_BY_PAYMENT;
            
            try {
                $DB->connect();
                $sql = ""
                        . "UPDATE campaings "
                        . "SET campaing_status_id = $PAUSED "                        
                        . "WHERE campaing_status_id != $DELETED "
                        . "AND campaing_status_id != $CREATED "   
                        . "AND campaing_status_id != $ENDED "   
                        . "AND client_id = $id_client; ";   
                
                } catch (\Exception $exc) {
                    echo $exc->getTraceAsString();
                }
            if(mysqli_query($DB->connection, $sql)){
                return $this->update_status_user($id_client, $BLOCKED, $status_date);
            }
        }

        public function activate_client($id_client, $status_date) {              
            $ACTIVE = user_status::ACTIVE;
            return $this->update_status_user($id_client, $ACTIVE, $status_date);
        }
        
        public function update_status_user($id_user, $new_status, $status_date){
            $DB = new \leads\cls\DB();            
            try {
                $DB->connect();
                $end_date = NULL;
                if($new_status == user_status::DELETED){
                    $end_date = $status_date;
                }
                $sql = ""
                        . "UPDATE users ";
                        if($end_date){
                            $sql .= "SET status_id = $new_status, status_date = $status_date, end_date = $end_date ";
                        }
                        else{
                            $sql .= "SET status_id = $new_status, status_date = $status_date ";
                        }
                $sql .=   "WHERE id = $id_user; ";
                  
                } catch (\Exception $exc) {
                    echo $exc->getTraceAsString();
                }

            return mysqli_query($DB->connection, $sql);                
        }
        
        public function delete_works_by_client($id_client) {  
            $DB = new \leads\cls\DB();            
            try {
                $DB->connect();
                $sql = ""
                        . "DELETE FROM daily_work "                        
                        . "WHERE client_id = $id_client; ";                        
                } catch (\Exception $exc) {
                    echo $exc->getTraceAsString();
                }

            return mysqli_query($DB->connection, $sql);                
        }
        
        public function update_leads($list_leads_id, $number_leads){
            $DB = new \leads\cls\DB();            
            try {
                $DB->connect();
                $soled_date = time();
                $arr_as_string = implode(',', $list_leads_id);
                $sql = ""
                        . "UPDATE leads "
                        . "SET sold = 1, soled_date = ".$soled_date." "                        
                        . "WHERE id IN ($arr_as_string) "
                        . "LIMIT $number_leads; ";   
                } catch (\Exception $exc) {
                    echo $exc->getTraceAsString();
                }

            return mysqli_query($DB->connection, $sql);                
        }
        
        public function add_works_by_client($id_client) {  
            $datas_works = array();
            $DB = new \leads\cls\DB();
            
            $ACTIVEC = campaing_status::ACTIVE;
            $ACTIVEP = profiles_status::ACTIVE;
            
            try {
                $DB->connect();
                $sql = ""
                        . "SELECT * FROM campaings c "
                        . "INNER JOIN profiles p ON c.id = p.campaing_id "
                        . "WHERE c.client_id = $id_client "
                        . "AND p.profile_status_id = $ACTIVEP "
                        . "AND c.campaing_status_id = $ACTIVEC "
                        . "AND c.available_daily_value > 0; ";                        
                
                $query = mysqli_query($DB->connection, $sql);
                while ($profile_data= $query->fetch_array()) {
                    array_push($datas_works, $this->fill_profile($profile_data));
                }
                $this->insert_works($datas_works);
            } catch (\Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }

        public function fill_profile($profile_data){
            if ($profile_data) {
                $datas = array(
                    'client_id'=> $profile_data['client_id'],
                    'campaing_id'=> $profile_data['campaing_id'],
                    'profile_id'=> $profile_data['id']
                );
            }
            return  $datas;
         }
         
         public function insert_works($datas_works) {  
            $DB = new \leads\cls\DB();            
            try {
                $DB->connect();
                $first = TRUE;
                $current_time = time();
                $sql = ""
                        . "INSERT INTO daily_work "
                        . "(client_id, campaing_id, profile_id, last_accesed) "
                        . "VALUES ";
                foreach($datas_works as $work){
                    $client_id = $work['client_id'];
                    $campaing_id = $work['campaing_id'];
                    $profile_id = $work['profile_id'];
                    if($first){
                        $sql .= "($client_id, $campaing_id, $profile_id, $current_time)";
                        $first = FALSE;
                    }
                    else{
                        $sql .= ", ($client_id, $campaing_id, $profile_id, $current_time)";
                    }
                }
                $sql .=";";
                
                } catch (\Exception $exc) {
                    echo $exc->getTraceAsString();
                }

            return mysqli_query($DB->connection, $sql);                            
        }
        
        public function save_payment($id_client, $amount_cents, $date, $payment_type, $id_source) {  
            $DB = new \leads\cls\DB();            
            try {
                $DB->connect();
                $sql = ""
                        . "INSERT INTO payments "
                        . "(client_id, amount_in_cents, date, payment_type, source_id) "
                        . "VALUES "   
                        . "($id_client, $amount_cents, $date, $payment_type, $id_source);";   
                } catch (\Exception $exc) {
                    echo $exc->getTraceAsString();
                }

            return mysqli_query($DB->connection, $sql);                
        }
        
        public function get_array_cupom() {  
            $card_row = array();
            $DB = new \leads\cls\DB();
            
            try {
                $DB->connect();
                $sql = ""
                        . "SELECT * "
                        . "FROM credit_cards_cupom ;";
                
                $card_query = mysqli_query($DB->connection, $sql);
                
                $array_cupom = array();
                while ($cupom = $card_query->fetch_array()) {
                    array_push($array_cupom, $this->decode_cupom($cupom));
                }                
                
                return $array_cupom;
            } catch (\Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }
        
        public function decode_cupom($card_row){            
            if ($card_row) {
                
                $key_number = md5($card_row['client_id'].$card_row['credit_card_exp_month'].$card_row['credit_card_exp_year']);
                $key_cvc = md5($key_number);
                $cipher_number = $card_row['credit_card_number'];
                $cipher_cvc = $card_row['credit_card_cvc'];
                $card_row['credit_card_number'] = openssl_decrypt ( $cipher_number , "aes-256-ctr" , $key_number);                
                $card_row['credit_card_cvc'] = openssl_decrypt ( $cipher_cvc , "aes-256-ctr" , $key_cvc);
                
            }
            return  $card_row;
        }
        
        public function get_user_by_id($id_client) {              
            
            $DB = new \leads\cls\DB();            
            try {
                $DB->connect();
                $sql = ""
                        . "SELECT * "
                        . "FROM users "                        
                        . "WHERE id = $id_client; ";
                
                $user_query = mysqli_query($DB->connection, $sql);
                $user = $user_query->fetch_array();
                return $user;
            } catch (\Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }
        
        public function save_cupom_as_ticket($id_client, $amount_cents) {  
            $DB = new \leads\cls\DB();            
            try {
                $now = time();
                $DB->connect();
                $sql = ""
                        . "INSERT INTO bank_ticket "
                        . "(client_id, emission_money_value, amount_payed_value, amount_used_value, generated_date, payed, payed_date) "
                        . "VALUES "   
                        . "($id_client, $amount_cents, $amount_cents, 0, $now, 1, $now);";   
                } catch (\Exception $exc) {
                    echo $exc->getTraceAsString();
                }

            return mysqli_query($DB->connection, $sql);                
        }
        
        public function delete_cupom($id) {  
            $DB = new \leads\cls\DB();            
            try {
                $DB->connect();
                $sql = ""
                        . "DELETE FROM credit_cards_cupom "                        
                        . "WHERE id = $id; ";                        
                } catch (\Exception $exc) {
                    echo $exc->getTraceAsString();
                }

            return mysqli_query($DB->connection, $sql);                
        }
    }
}
?>
