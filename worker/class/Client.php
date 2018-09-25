<?php

namespace leads\cls {
    
    ini_set('xdebug.var_display_max_depth', 150);
    ini_set('xdebug.var_display_max_children', 256);
    ini_set('xdebug.var_display_max_data', 2024);
    
    require_once 'User.php';
    require_once 'Robot.php';
    require_once 'DB.php';
    
    class Client extends User {
        public $insta_id; //poderia ser ou não um perfil de IG        
        public $HTTP_SERVER_VARS;        
        public $last_accesed;
        public $observation;

        public function get_clients() { //workables clients
            try {
                $Clients = array();
                $DB = new \leads\cls\DB();
                $CLIENT = user_role::CLIENT;
                $ACTIVE = user_status::ACTIVE;
                $sql = ""
                        . "SELECT * FROM users "
                        . "INNER JOIN clients ON clients.user_id = users.id "
                        . "WHERE users.role_id = $CLIENT "
                        . "AND users.status_id = $ACTIVE "
                        . "ORDER BY users.id; ";
                $clients_data = mysqli_query($DB->connection, $sql);
                if($clients_data !=NULL)
                    while ($client_data = $clients_data->fetch_object()) {
                        $Client = $this->fill_client_data($client_data);
                        array_push($Clients, $Client);
                    }
                else
                    echo '********** IMPOSSIBLE CONNECT TO DATABASE***************';
                return $Clients;
            } catch (\Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }
        
        public function fill_client_data($client_data) {
            $Client = NULL;
            if ($client_data){
                $Client = new Client();                
                $Client->id = $client_data->user_id;
                $Client->role_id = $client_data->role_id;
                $Client->name = $client_data->name;
                $Client->login = $client_data->login;
                $Client->pass = $client_data->pass;
                $Client->email = $client_data->email;
                $Client->telf = $client_data->telf;
                $Client->status_id = $client_data->status_id;
                $Client->status_date = $client_data->status_date;
                $Client->language = $client_data->language;
                $Client->init_date = $client_data->init_date;
                $Client->end_date = $client_data->end_date;                
                $Client->insta_id = $client_data->insta_id;                       
                $Client->HTTP_SERVER_VARS = $client_data->HTTP_SERVER_VARS;        
                $Client->last_accesed = $client_data->last_accesed;
                $Client->observation = $client_data->observation;          
            }
            return $Client;
        }
        
        
        
        
        

        
        
        
        
        
        
        
        
        
        //----------------------------------8888888888888888888888888888888
        public function sign_in($Client) {
            $debug = false;
            $truncatedDebug = true;
            $ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);
            try {
                $ig->setUser($Client->login, $Client->pass);
                $ig->login();               
                if (is_object($ig) /*&& isset($ig->json_response->authenticated)*/) {
                    return $ig;
                } else{
                    $this->set_client_status($Client->id, user_status::BLOCKED_BY_INSTA);                
                    return NULL;                
                }
            } catch (\Exception $e) {
                if (is_string($ig)&& strpos($e->getMessage(),'checkpoint_required')) {
                    $this->set_client_status($Client->id, user_status::VERIFY_ACCOUNT);
                } else
                    echo 'Something went wrong trying to login: ' . $e->getMessage() . "\n";                
            }
        }
        
        
        public function set_client_cookies($client_id = NULL, $cookies = NULL) {
            try {
                $client_id = $client_id ? $client_id : $this->id;
                $cookies = $cookies ? $cookies : $this->cookies;
                $DB = new \leads\cls\DB();
                $result = $DB->set_client_cookies($client_id, $cookies);
                if ($result) {
                    print "Client $client_id cookies changed!!!";
                } else {
                    print "FAIL CHANGING Client $client_id cookies!!!";
                }
            } catch (Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }
        
        public function create_daily_work($client_id) {
            $DB = new DB();
            $Client = $this->get_client($client_id);
            if (count($Client->reference_profiles) > 0) {
                $DIALY_REQUESTS_BY_CLIENT = $Client->to_follow;
                $to_follow_unfollow = $DIALY_REQUESTS_BY_CLIENT / count($Client->reference_profiles);
//                $to_follow_unfollow = $GLOBALS['sistem_config']->DIALY_REQUESTS_BY_CLIENT / count($Client->reference_profiles);
                // If User status = UNFOLLOW he do 0 follows
                $to_follow = $Client->status_id != user_status::UNFOLLOW ? $to_follow_unfollow : 0;
                $to_unfollow = $to_follow_unfollow;
                foreach ($Client->reference_profiles as $Ref_Prof) { // For each reference profile
//$Ref_prof_data = $this->Robot->get_insta_ref_prof_data($Ref_Prof->insta_name);
                    $DB->insert_daily_work($Ref_Prof->id, $to_follow, $to_unfollow, $Client->cookies);
                }
            } else {
                echo "Not reference profiles: $Client->login <br>\n<br>\n";
            }
        }

        public function delete_daily_work($client_id) {
            $DB = new DB();
            $DB->delete_daily_work_client($client_id);
        }
        
        
        

        public function set_client_status($client_id = NULL, $status_id = NULL) {
            try {
                $client_id = $client_id ? $client_id : $this->id;
                $status_id = $status_id ? $status_id : $this->status_id;
                $DB = new \leads\cls\DB();
                $result = $DB->set_client_status($client_id, $status_id);
                if ($result) {
                    print "Client $client_id to status $status_id!!!";
                } else {
                    print "FAIL CHANGING Client $client_id to status $status_id!!!";
                }
            } catch (Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }

        public function get_reference_profiles($client_id = NULL) {
            try {
                $client_id = $client_id ? $client_id : $this->id;
                $DB = new \leads\cls\DB();
                $ref_profs_data = $DB->get_reference_profiles_data($client_id);
                while ($prof_data = $ref_profs_data->fetch_object()) {
                    $Ref_Prof = new \leads\cls\Reference_profile();
                    $Ref_Prof->id = $prof_data->id;
                    $Ref_Prof->insta_id = $prof_data->insta_id;
                    $Ref_Prof->insta_name = $prof_data->insta_name;
                    $Ref_Prof->insta_follower_cursor = $prof_data->insta_follower_cursor;
                    $Ref_Prof->deleted = $prof_data->deleted;
                    $Ref_Prof->type = $prof_data->type;
                    array_push($this->reference_profiles, $Ref_Prof);
//                    }
                }
            } catch (Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }

    }
}
?>
