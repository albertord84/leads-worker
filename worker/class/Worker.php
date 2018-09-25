<?php

namespace leads\cls {
    require_once 'DB.php';    
    require_once 'Profile.php';
    require_once 'Client.php';
    require_once 'Robot.php';
    require_once 'Robot_Profile.php';
    require_once 'Gmail.php';
    require_once 'Campaings.php';
    require_once 'profiles_status.php';
    require_once 'user_status.php';
    
    
    class Worker {
        public $id;
        public $ig;
        public $DB;
        public $Robot;
        public $Gmail;
        public $robot_profile;
//        public $robot_profile_login;
//        public $robot_profile_pass;
//        public $init;
//        public $end;
        public $next_work;
//        public $cookies;
        
        //--------principal functions--------------
        public function __construct($DB = NULL) {
            $this->Robot = new Robot($DB);
            $this->config = $GLOBALS['sistem_config'];
            $this->Robot->config = $GLOBALS['sistem_config'];
            $this->Gmail = new Gmail();
            $this->robot_profile = new Robot_Profile();
            $this->DB = $DB ? $DB : new \leads\cls\DB();
        }
        
        function truncate_daily_work() {
            $this->DB->truncate_daily_work();
        }
        
        function prepare_all_robot_profiles() {
            /*
            //1. recuperar todos lo rp que pueden trabajar
            */            
        }
        
        function prepare_daily_work() {
            //0. botar todos os robot_profiles em activo
            $this->DB->reset_robot_profiles();
            //1. seleccionar clientes activos (que pueden trabajar)
            $GLOBALS['all_client_cookies']=array();
            $objClient=new Client();
            $all_clients=$objClient->get_clients();// clientes ativos
            foreach ($all_clients as $Client) {
                echo "<br> \n <br> \n ------------------------------------------------------------------------ <br> \n <br> \n";
                echo "Cliente: ".$Client->login." <br> \n";                
                //2. slecionar as campanhas ativas do cliente
                $objCampaing = new Campaings();
                $client_campaings = $objCampaing->get_campaings_of_client($Client->id,campaing_status::ACTIVE);
                if(!count($client_campaings)){
                    echo "Cliente sem campanhas trabalhaveis<br>\n";
                }else{
                    //3. seleccionar los perfiles activos por campana
                    foreach ($client_campaings as $Campaing) {
                        echo " <br> \n Campanha id: ".$Campaing->id." <br> \n";
                        $profiles_campaing = $objCampaing->get_profiles_of_campaings($Campaing->id);
                        if(!count($profiles_campaing)){
                            echo "Campanha sem RP ativos<br> \n";
                            if(!$this->DB->set_campaing_available_daily_value($Campaing->id,$Campaing->total_daily_value))
                               echo "Erro estabelecendo o orçamento do dia da campanha: ".$Client->id." <br> \n";
                        }else {
                            //4. insertar trabajo en la tabla daily_work
                            foreach ($profiles_campaing as $Profile) {
                                echo "Prefil a trabalhar: ".$Profile->profile." <br> \n";
                                if(!$this->DB->insert_daily_work($Client->id, $Campaing->id, $Profile->id))
                                   echo "Erro ao inserir o perfil ".$Profile->profile." no daily_work<br> \n";
                            }
                            //5. inicializar o available_daily_value com o total_daily_value
                            if(!$this->DB->set_campaing_available_daily_value($Campaing->id,$Campaing->total_daily_value))
                               echo "Erro estaabelecendo o orçamento do dia da campanha: ".$Client->id." <br> \n";
                        }
                    }
                }
            }
        }
      
        public function do_work(){
            //level 0. using the dumbu.pro RP as the RP of one single campaing
            //1. obtener un robot_profile para trabalhar
            $param_id = filter_input(INPUT_GET, 'id',FILTER_VALIDATE_INT); //identificador usado pro robot i-esimo           
            $robot_profile=new Robot_Profile();
            $result = $robot_profile->get_robot_profile_from_backup($param_id);
            if(!$result){
                echo "<br>\n<br>\n----------->Robot ".$param_id." not initialized by not sufficient robot_profiles actives<br>\n<br>\n<br>\n<br>\n";
            } else{
                //2. trabalhar enquanto exista trabalho e robot_profile disponíveis
                $this->ig = $robot_profile->ig;
                echo "Robot".$param_id." initialized with robot_profile ".$robot_profile->login."<br>\n<br>\n";                
                while (true){
                    //3. obter um trabalho
                    $this->next_work = NULL;
                    $this->next_work = $this->DB->get_next_work();
                    if($this->next_work->client){
                        $this->Robot->next_work = $this->next_work;
                        
                        //4. processar trabalho atual e analise de exepções
                        $result = $this->Robot->do_robot_extract_leads($robot_profile->ig, $robot_profile->cookies,$this->config->MULTI_LEVEL);
                        if($result->has_exception){
                            $this->process_instagram_api_exception($result->exception_message,$robot_profile);
                            $message = $result->exception_message;
                        } else{
                            $message =' proccess correctly';
                        }
                        echo " Client ---> ".$this->next_work->client->login." ".
                            " Campaing --> ".$this->next_work->campaing->id. " ".
                            " Profile --> ".$this->next_work->profile->profile. ": ".$message."<br><br>\n\n";                        
                    } else{
                        if($param_id<=2){ //2 robots trabajando hasta reiniciar el servidor, los otros mueren
                            sleep((int)($this->config->TIME_SLEEP_ROBOT_WITHOUT_WORK)); 
                            echo "Robot waiting 5 minutes by not dispose work --------<br> \n <br> \n";
                        }
                        else{                            
                            die("<br> \n <br> \nCongratulations!!! Job done... with robot_profiles ".$result['robot_profile_login']."!<br> \n");
                        }
                    }
                }
            }
        }
        
        
        public function process_instagram_api_exception($exception_message,$robot_profile){            
            if(strpos($exception_message, 'Throttled by Instagram because of too many API requests')!== FALSE){
                sleep(120);
            }
            else
            if(strpos($exception_message, 'Requested resource does not exist') !== FALSE){
            }
            else
            if(strpos($exception_message, 'User not found') !== FALSE){
                $this->DB->delete_daily_work_by_profile($this->next_work->profile->id);
//                $this->DB->update_field_in_DB('profiles', 'id', $this->next_work->profile->id, 'profile_status_id', profiles_status::MISSING);
//                $this->DB->update_field_in_DB('profiles', 'id', $this->next_work->profile->id, 'date', time());
            }
            else
            if(strpos($exception_message, 'No response from server. Either a connection or configuration error') !== FALSE){
                sleep(4);
            }
            else
            if(strpos($exception_message, 'Challenge required') !== FALSE){                
                $this->DB->update_field_in_DB('robots_profiles', 'id', $robot_profile->id, 'status_id', user_status::VERIFY_ACCOUNT);
                $administrators=array('danilo.oliveiira@hotmail.com', 'josergm86@gmail.com');                        
                    foreach($administrators as $admin){
                            $this->Gmail->send_mail($admin, $admin,
                                "' CONCERTAR ISSO!!! Robot_profile login = ' $robot_profile->login ' in verify account",
                                "' CONCERTAR ISSO!!! Robot_profile login = ' $robot_profile->login ' in verify account");                            
                    }
                die("<br>\n<br>\nRobot_profile ".$robot_profile->login." to Challenge required state<br>\n<br>\n");
            }
            else
            if(strpos($exception_message, 'Not followers from geolocation') !== FALSE){                
                $this->DB->update_field_in_DB('robots_profiles', 'id', $robot_profile->id, 'status_id', user_status::VERIFY_ACCOUNT);
                $administrators=array('josergm86@gmail.com');                        
                /*foreach($administrators as $admin){
                    $this->Gmail->send_mail($admin, $admin,
                        "'Robot_profile ' $robot_profile->login ' has cookies session problem",
                        "'Robot_profile ' $robot_profile->login ' has cookies session problem");                            
                }
                die("<br>\n<br>\nRobot_profile ".$robot_profile->login." has cookies session problem<br>\n<br>\n");
                */
                sleep(180);
            }
            else
            if(strpos($exception_message, 'Not followers from hashtag') !== FALSE){                
                $this->DB->update_field_in_DB('robots_profiles', 'id', $robot_profile->id, 'status_id', user_status::VERIFY_ACCOUNT);
                $administrators=array('josergm86@gmail.com');                        
                foreach($administrators as $admin){
                    $this->Gmail->send_mail($admin, $admin,
                        "'Robot_profile ' $robot_profile->login ' has cookies session problem",
                        "'Robot_profile ' $robot_profile->login ' has cookies session problem");                            
                }
                die("<br>\n<br>\nRobot_profile ".$robot_profile->login." has cookies session problem<br>\n<br>\n");
            }
            else{
                sleep(180);
            }
        }
        
        
        
    }

}



