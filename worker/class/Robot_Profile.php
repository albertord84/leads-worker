<?php

namespace leads\cls {
    require_once 'user_role.php';
    require_once 'user_status.php';
    require_once 'DB.php';
    require_once 'Robot.php';
    require_once 'Gmail.php';
    
    class Robot_Profile{
        public $id;
        public $ig;
        public $login;
        public $pass;
        public $status_id;
        public $ds_user_id;
        public $cookies;
        public $init;
        public $end;
        public $Gmail;
                
        function __construct() {
            $this->Gmail =  new Gmail();//new leads\cls\Gmail();            
        }
        
        public function get_robot_profile_from_backup(){ //retorna o Robot_Profile que possa ser usado para trabalhar
            //1. selecionar uno de entre todos los robot_profile ativos
            $ACTIVE = user_status::ACTIVE;
            $BLOCKED_BY_INSTA = user_status::BLOCKED_BY_INSTA;
            $VERIFY_ACCOUNT = user_status::VERIFY_ACCOUNT;
            $OCCUPED = user_status::OCCUPED;
            $RP = array();
            $ig = NULL;
            $DB = new DB();
            $id =0;
            while(true){
                $sql = ""
                    . "SELECT * FROM dumbu_emails_db.robots_profiles "
                    . "WHERE (robots_profiles.status_id = $ACTIVE "
                    . "OR robots_profiles.status_id = $BLOCKED_BY_INSTA "
                    . "OR robots_profiles.status_id = $VERIFY_ACCOUNT )"
                    . "AND robots_profiles.id > $id "
                    . "ORDER BY robots_profiles.id "
                    . "LIMIT 1";
                $clients_data = mysqli_query($DB->connection, $sql);                
                $client_data = $clients_data->fetch_object();
                if(!$client_data){
                    return NULL;
                } else{
                    $rp = $this->fill_client_data($client_data);
                    $id = $rp->id;
                    $objRobot=new Robot();
                    $resp = $objRobot->do_instagram_login_by_API($rp->login,$rp->pass);
                    if(is_object($resp) && is_object($resp->ig)){
                        $DB->update_field_in_DB('robots_profiles', 'id', $rp->id, 'status_id', $OCCUPED);
                        $DB->update_field_in_DB('robots_profiles', 'id', $rp->id, 'cookies', json_encode($resp->cookies));
                        $this->id = $rp->id;
                        $this->ig = $resp->ig;
                        $this->login = $rp->login;
                        $this->pass = $rp->pass;
                        $this->status_id = $OCCUPED;
                        $this->cookies = $resp->cookies;
                        $this->init = $rp->init;
                        $this->end = $rp->end;
                        return true;
                    } else{
                        $administrators=array('josergm86@gmail.com','danilo.oliveiira@hotmail.com');                        
                        //$administrators=array('jorge85.mail@gmail.com');                        
                        foreach($administrators as $admin){
                            $this->Gmail->send_mail($admin, $admin,
                            "' CONCERTAR ISSO!!! Problem with login of robot_profile login = '. $rp->login '",
                            "' CONCERTAR ISSO!!! Problem with login of robot_profile login = '. $rp->login '");                            
                        }
                        if($ig==='BLOCKED_BY_INSTA' || $ig==='NOT LOGGED'){
                            $DB->update_field_in_DB('robots_profiles', 'id', $rp->id, 'status_id', $BLOCKED_BY_INSTA);                            
                        } else
                        if($ig==='VERIFY_ACCOUNT'){
                            $DB->update_field_in_DB('robots_profiles','id', $rp->id, 'status_id', $VERIFY_ACCOUNT);                            
                        }
                    }
                }
            }
            return false;
        }
                
        public function fill_client_data($client_data) {
            $RP = NULL;
            if ($client_data){
                $RP = new Robot_Profile();                
                $RP->id = $client_data->id;
                $RP->status_id = $client_data->status_id;           
                $RP->login = $client_data->login;
                $RP->pass = $client_data->pass;
                $RP->ds_user_id = $client_data->ds_user_id;               
                $RP->cookies = $client_data->cookies;               
                $RP->init = $client_data->init;               
                $RP->end = $client_data->end;               
            }
            return $RP;
        }
        
    }
}
?>
