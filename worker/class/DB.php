<?php
namespace leads\cls {
    require_once 'user_status.php';    

    class DB {

        protected $host;
        protected $db;
        protected $user;
        protected $pass;        
        public $connection = NULL;
        public $profile_table_key;
        
        //-------------PRINCIPALS FUNCTIONS------------------------------

        public function __construct($conf_file = "/../../../LEADS-WORKER.INI") {
            $this->connect($conf_file);
        }

        public function connect($conf_file = NULL) {
            if (!$this->connection) {
                // Connect to DB
                $config = parse_ini_file(dirname(__FILE__) . $conf_file, true);
                $this->host = $config["database"]["host"];
                $this->db = $config["database"]["db"];
                $this->user = $config["database"]["user"];
                $this->pass = $config["database"]["pass"];
                $this->connection = mysqli_connect($this->host, $this->user, $this->pass, $this->db) or die("Cannot connect to database.");
            }
        }
        
        public function reset_robot_profiles() {
            $sql='UPDATE dumbu_emails_db.robots_profiles SET status_id = '.user_status::ACTIVE.' WHERE status_id > 0 AND status_id <> '.user_status::DELETED.' AND status_id <> '.user_status::DONT_DISTURB.';';
            return mysqli_query($this->connection, $sql);
        }
        
        public function truncate_daily_work() {
            try{
                $sql = "TRUNCATE dumbu_emails_db.daily_work;";
                $result = mysqli_query($this->connection, $sql);
                return $result;
            } catch (\Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }
        
        public function insert_daily_work($client_id, $campaing_id, $ref_prof_id) {
            try {
                $t=time();
                $sql = ""
                        . "INSERT INTO dumbu_emails_db.daily_work "
                        . "(client_id, campaing_id, profile_id, last_accesed) "
                        . "VALUES "
                        . "($client_id, $campaing_id, $ref_prof_id, $t);";
                $result = mysqli_query($this->connection, $sql);                
//                $this->update_field_in_DB('clients', 'user_id', $client_id, 'last_accesed', $t);
//                $this->update_field_in_DB('campaings', 'id', $campaing_id, 'last_accesed', $t);
//                $this->update_field_in_DB('profiles', 'id', $ref_prof_id, 'last_accesed', $t);                  
                return $result;
            } catch (\Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }

        public function delete_daily_work_by_profile($ref_prof_id) {
            try {
                $sql ="DELETE FROM dumbu_emails_db.daily_work WHERE reference_id = $ref_prof_id; ";
                $result = mysqli_query($this->connection, $sql);
                return $result;
            } catch (\Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }

        public function delete_daily_work_by_campaing($campaing_id) {
            try {
                $sql = "DELETE FROM dumbu_emails_db.daily_work WHERE daily_work.campaing_id = $campaing_id;";
                $result = mysqli_query($this->connection, $sql);
                return $result;
            } catch (\Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }
       
        public function delete_daily_work_by_client($client_id) {
            try {
                $sql = "DELETE FROM dumbu_emails_db.daily_work WHERE daily_work.campaing_id = $client_id; ";
                $result = mysqli_query($this->connection, $sql);
                return $result;
            } catch (\Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }
        
        public function set_campaing_available_daily_value($campaing_id,$value){
            try {
                $sql = ""
                    . "UPDATE dumbu_emails_db.campaings "                    
                    . "SET campaings.available_daily_value = '$value' "
                    . "WHERE campaings.id = $campaing_id; ";
                return mysqli_query($this->connection, $sql);
            } catch (\Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }
        
        public function get_next_work() {
            //TODO: veo más eficiente adicionar um last_accessed a cada registro en el daily_work
            try {
                //1. obter a registro do daily_work mais antigo sem trabalhar
                $sql = ""
                    . " SELECT * FROM dumbu_emails_db.daily_work "
                    . " ORDER BY daily_work.last_accesed ASC "
                    . " LIMIT 1;";
                $result = mysqli_query($this->connection, $sql);
                $next_work = $result->fetch_object();
                
                //2. atualizar os tempos last_accesed
                $t = time();
                $this->update_field_in_DB('daily_work',
                        'profile_id', $next_work->profile_id,
                        'last_accesed', $t);
                $this->update_field_in_DB('profiles',
                        'id', $next_work->profile_id,
                        'last_accesed', $t);
                $this->update_field_in_DB('clients',
                        'user_id', $next_work->client_id,
                        'last_accesed', $t);
                $this->update_field_in_DB('campaings',
                        'id', $next_work->campaing_id,
                        'last_accesed', $t);
                
                //3. obter o cliente mais antigo sem ter trabalhado segundo o daily work
                $sql = ""
                    . " SELECT * FROM dumbu_emails_db.clients "
                    . " INNER JOIN users ON users.id = clients.user_id "
                    . " WHERE clients.user_id = '$next_work->client_id';";
                $result = mysqli_query($this->connection, $sql);
                $old_client = $result->fetch_object();
                
                //4. obter a campanha mais antiga sem ter trabalhado segundo o daily work
                $sql = ""
                    . " SELECT * FROM dumbu_emails_db.campaings "
                    . " WHERE campaings.id = '$next_work->campaing_id';";
                $result = mysqli_query($this->connection, $sql);
                $old_campaing = $result->fetch_object();
                
                //5. obter o perfil asociado
                $sql = ""
                    . " SELECT * FROM dumbu_emails_db.profiles "
                    . " WHERE profiles.id = '$next_work->profile_id';";
                $result = mysqli_query($this->connection, $sql);
                $old_profile = $result->fetch_object();
                
                return (object)array(
                    'client'=>$old_client,
                    'campaing'=>$old_campaing,
                    'profile'=>$old_profile
                );                        
                
            } catch (\Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }   
        
        public function update_field_in_DB($table, $key_field, $key_value, $field, $value){
            $sql = ""
                . "UPDATE dumbu_emails_db.$table "                    
                . "SET $field = '$value' "
                . "WHERE $key_field = '$key_value'; ";
            $v = mysqli_query($this->connection, $sql);
            return $v;
        }
        
        public function get_init_range_value() {
            $sql = "SELECT value FROM dumbu_emails_db.dumbu_emails_system_config WHERE name='INIT_RANGE_VALUE';";
            $result = mysqli_query($this->connection, $sql);
            $result = (int)($result->fetch_object()->value);
            return $result;
        }
        
        public function get_system_config_vars() {
            try {
                $this->connect();
                $sql = "SELECT * FROM dumbu_emails_db.dumbu_emails_system_config;";
                $result = mysqli_query($this->connection, $sql);
                return $result ? $result : NULL;
            } catch (\Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }

       
        public function insert_future_reference_profile($table_to_profiles, $ds_user_id, $username){
            try {
                $sql = "INSERT INTO dumbu_emails_db.$table_to_profiles "
                    . " (insta_id,profile)"
                    . " VALUES ('$ds_user_id', '$username');";
                $result = mysqli_query($this->connection, $sql);
                return $result ? $result : NULL;
            } catch (\Exception $exc) {
                echo $exc->getTraceAsString();
            }
            
        }
         
         public function existing_lead($ds_user_id){            
            try {                
                $sql = ""
                        . "SELECT * "
                        . "FROM leads "                        
                        . "WHERE ds_user_id = $ds_user_id; ";
                
                $result_query = mysqli_query($this->connection, $sql);
                $lead = $result_query->fetch_array();
                if ($lead) {
                    return true;
                }
                else{
                    return false;//nuevo lead adquirido!!!
                }
                
            } catch (\Exception $exc) {
                echo $exc->getTraceAsString();
                return true;
            }
         }
         
         public function save_extracted_crypt_leads($profile_id, $lead, $table){
            try {
                //1. criptografar os dados sensíveis da leads
                $lead->username = $this->crypt(str_replace("'", "\'",$lead->username));
                $lead->private_email = $this->crypt(str_replace("'", "\'",$lead->private_email));
                $lead->biography_email = $this->crypt(($lead->biography_email));
                $lead->public_email = $this->crypt(str_replace("'", "\'",$lead->public_email));
                $lead->public_phone_country_code = $this->crypt(str_replace("'", "\'",$lead->public_phone_country_code));
                $lead->public_phone_number = $this->crypt(str_replace("'", "\'",$lead->public_phone_number));
                $lead->contact_phone_number = $this->crypt(str_replace("'", "\'",$lead->contact_phone_number));
                
                $lead->phone_number = $this->crypt(str_replace("'", "\'",$lead->phone_number));
                $lead->address_street = $this->crypt(str_replace("'", "\'",$lead->address_street));
                $lead->biography = $this->crypt(str_replace("'", "\'",$lead->biography));
                $lead->birthday = $this->crypt(str_replace("'", "\'",$lead->birthday));
                $lead->city_id = $this->crypt(str_replace("'", "\'",$lead->city_id));
                $lead->city_name = $this->crypt(str_replace("'", "\'",$lead->city_name));
                $lead->zip_code = $this->crypt(str_replace("'", "\'",$lead->zip_code));
                $lead->coef_f_weight = $this->crypt(str_replace("'", "\'",$lead->coef_f_weight));
                $lead->country_code = $this->crypt(str_replace("'", "\'",$lead->country_code));
                $lead->external_url = $this->crypt(str_replace("'", "\'",$lead->external_url));
                $lead->fb_page_call_to_action_id = $this->crypt(str_replace("'", "\'",$lead->fb_page_call_to_action_id));
                $lead->fb_u_id = $this->crypt(str_replace("'", "\'",$lead->fb_u_id));
                //$lead->follower_count = $this->crypt($lead->follower_count);
                $lead->full_name = $this->crypt(str_replace("'", "\'",$lead->full_name));
                $lead->is_verified = $this->crypt(str_replace("'", "\'",$lead->is_verified));
                //$lead->media_count = $this->crypt($lead->media_count);
                $lead->get_show_insights_terms = $this->crypt(str_replace("'", "\'",$lead->get_show_insights_terms));
                
                
                //2. inserir a lead na tabela correspondente
                $t= time();
                if($lead->category)
                    $category = $lead->category;
                else
                    $category = 'single_profile';
                $sql = ""
                    . "INSERT INTO dumbu_emails_db.$table "
                    . "(reference_profile_id, username, ds_user_id, is_private, is_business, "
                    . "private_email, biography_email, public_email, "
                    . "public_phone_country_code, public_phone_number, contact_phone_number, phone_number, "
                    . "gender, category, extracted_date, "
                    . "address_street, biography, birthday, city_id, city_name, zip_code, coef_f_weight, country_code, external_url, "
                    . "fb_page_call_to_action_id, fb_u_id, follower_count, full_name, is_verified, media_count, get_show_insights_terms"
                    . ") "
                    . "VALUES ( '$profile_id', '$lead->username', '$lead->ds_user_id', '$lead->is_private', '$lead->is_business', "
                        . " '$lead->private_email', '$lead->biography_email', '$lead->public_email', "
                        . " '$lead->public_phone_country_code', '$lead->public_phone_number', '$lead->contact_phone_number', '$lead->phone_number', "
                        . " '$lead->gender', '$category', '$t', "
                        . " '$lead->address_street', '$lead->biography', '$lead->birthday', '$lead->city_id', '$lead->city_name', '$lead->zip_code', "
                        . " '$lead->coef_f_weight', '$lead->country_code', '$lead->external_url', '$lead->fb_page_call_to_action_id', '$lead->fb_u_id', '$lead->follower_count', "
                        . " '$lead->full_name', '$lead->is_verified', '$lead->media_count', '$lead->get_show_insights_terms' "
                    . ");";
                $result = mysqli_query($this->connection, $sql);
                if(!$result){
                    echo "<br>/n<br>/n".$sql."<br>/n<br>/n";
                }
                
            } catch (\Exception $exc) {
                echo "<br><br>Leads com ds_user_id = ".$lead->ds_user_id." não foi inserida <br><br>";
                var_dump($exc->getTraceAsString());
            }
            return $result;
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
         
         
        public function show_all_leads(){
            $sql = "SELECT * FROM dumbu_emails_db.dumbu_emails_1kM;";
            $result = mysqli_query($this->connection, $sql);
            while($lead = $result->fetch_object()){
                var_dump($lead->ds_user_id);
                var_dump($this->decrypt($lead->username));
                var_dump($this->decrypt($lead->public_email));
                var_dump($this->decrypt($lead->biography));                
                var_dump($this->decrypt($lead->public_phone_number));
                var_dump($this->decrypt($lead->address_street));
                var_dump($this->decrypt($lead->city_id));
                var_dump($this->decrypt($lead->city_name));
                echo '<br><br>********************************************************<br><br>';
            }
         }
         
        public function set_id_in_profile(){
            $sql = "SELECT * FROM dumbu_emails_db.profiles;";
            $result = mysqli_query($this->connection, $sql);
            $k=1;
            while($lead = $result->fetch_object()){
                $sql = "UPDATE  dumbu_emails_db.profiles set id='$k' where insta_id='$lead->insta_id';";
                $resp = mysqli_query($this->connection, $sql);
                $k++;
                echo "'$k'<br>";
            }
         }
         
//         public function creating_profiles_from_reference_profiles(){
//            $sql = "SELECT * FROM dumbu_emails_db.reference_profile where type='0';";
//            $result = mysqli_query($this->connection, $sql);
//            $k=1;
//            try{
//                while($rp = $result->fetch_object()){
//                    $result2=false;
//                    if($rp->insta_id!=null && $rp->insta_id!='' && $rp->insta_name!=null && $rp->insta_name!=''){
//                        $sql = "INSERT INTO profiles (campaing_id,insta_id,profile)"
//                            . "VALUES (1, '$rp->insta_id', '$rp->insta_name');";
//                        $result2 = mysqli_query($this->connection, $sql);                
//                        echo $k."----------".$rp->insta_name."----------".$result2."<br>"; $k++;
//                    }
//                }
//            }
//            catch (\Exception $exc){
//
//            }
//         }
         
//        public function copy_all_leads(){
//            $sql = "SELECT * FROM leads_tmp;";
//            $result = mysqli_query($this->connection, $sql);
//            while($lead = $result->fetch_object()){
//                
//                $lead->username = $this->crypt($lead->username);
//                $lead->private_email = $this->crypt($lead->private_email);
//                $lead->biography_email = $this->crypt($lead->biography_email);
//                $lead->public_email = $this->crypt($lead->public_email);
//                $lead->public_phone_country_code = $this->crypt($lead->public_phone_country_code);
//                $lead->public_phone_number = $this->crypt($lead->public_phone_number);
//                $lead->contact_phone_number = $this->crypt($lead->contact_phone_number);
//                
//                $this->save_extracted_crypt_leads($lead->reference_profile_id, $lead);
//                echo $lead->id."<br>";
//            }
//        }
        
//        public function codify_base64_all_leads(){ //aplicar solo a las que ya fueron encriptadas
//            $table="leads_10kM";
//            
//            $sql = "ALTER TABLE `dumbu_emails_db`.`".$table."` RENAME TO  `dumbu_emails_db`.`".$table."_tmp`;" ;
//            $result1 = mysqli_query($this->connection, $sql);
//            
//            $sql = "CREATE TABLE `dumbu_emails_db`.`".$table."` (
//                `ds_user_id` VARCHAR(20) CHARACTER SET 'utf8' NOT NULL,
//                `reference_profile_id` INT(11) NULL DEFAULT NULL,
//                `extracted_date` VARCHAR(11) CHARACTER SET 'utf8' NULL DEFAULT NULL,
//                `sold` INT(1) NULL DEFAULT '0',
//                `username` VARCHAR(45) CHARACTER SET 'utf8' NULL DEFAULT NULL,
//                `is_private` VARCHAR(11) CHARACTER SET 'utf8' NULL DEFAULT NULL,
//                `is_business` VARCHAR(45) CHARACTER SET 'utf8' NULL DEFAULT NULL,
//                `private_email` VARCHAR(80) CHARACTER SET 'utf8' NULL DEFAULT NULL,
//                `biography_email` VARCHAR(80) CHARACTER SET 'utf8' NULL DEFAULT NULL,
//                `public_email` VARCHAR(80) CHARACTER SET 'utf8' NULL DEFAULT NULL,
//                `public_phone_country_code` VARCHAR(5) CHARACTER SET 'utf8' NULL DEFAULT NULL,
//                `public_phone_number` VARCHAR(20) CHARACTER SET 'utf8' NULL DEFAULT NULL,
//                `contact_phone_number` VARCHAR(20) CHARACTER SET 'utf8' NULL DEFAULT NULL,
//                `phone_number` VARCHAR(20) CHARACTER SET 'utf8' NULL DEFAULT NULL,
//                `gender` VARCHAR(45) CHARACTER SET 'utf8' NULL DEFAULT NULL,
//                `category` VARCHAR(45) CHARACTER SET 'utf8' NULL DEFAULT NULL,
//                `address_street` VARCHAR(200) CHARACTER SET 'utf8' NULL DEFAULT NULL,
//                `biography` VARCHAR(500) CHARACTER SET 'utf8' NULL DEFAULT NULL,
//                `birthday` VARCHAR(11) CHARACTER SET 'utf8' NULL DEFAULT NULL,
//                `city_name` VARCHAR(100) CHARACTER SET 'utf8' NULL DEFAULT NULL,
//                `city_id` VARCHAR(11) CHARACTER SET 'utf8' NULL DEFAULT NULL,
//                `zip_code` VARCHAR(15) CHARACTER SET 'utf8' NULL DEFAULT NULL,
//                `coef_f_weight` VARCHAR(11) CHARACTER SET 'utf8' NULL DEFAULT NULL,
//                `country_code` VARCHAR(11) CHARACTER SET 'utf8' NULL DEFAULT NULL,
//                `external_url` VARCHAR(200) CHARACTER SET 'utf8' NULL DEFAULT NULL,
//                `fb_page_call_to_action_id` VARCHAR(200) CHARACTER SET 'utf8' NULL DEFAULT NULL,
//                `fb_u_id` VARCHAR(20) CHARACTER SET 'utf8' NULL DEFAULT NULL,
//                `follower_count` INT(20) NULL DEFAULT NULL,
//                `full_name` VARCHAR(200) CHARACTER SET 'utf8' NULL DEFAULT NULL,
//                `is_verified` VARCHAR(7) CHARACTER SET 'utf8' NULL DEFAULT NULL,
//                `media_count` INT(20) NULL DEFAULT NULL,
//                `get_show_insights_terms` VARCHAR(100) CHARACTER SET 'utf8' NULL DEFAULT NULL,
//                PRIMARY KEY (`ds_user_id`));
//            ";
//            $result2 = mysqli_query($this->connection, $sql);
//            
//            if($result1 && $result2){
//                $sql = "SELECT * FROM dumbu_emails_db.".$table."_tmp;";
//                $result = mysqli_query($this->connection, $sql);
//                while($lead = $result->fetch_object()){
//
//                    $lead->username = base64_encode($lead->username);
//                    $lead->private_email = base64_encode($lead->private_email);
//                    $lead->biography_email = base64_encode($lead->biography_email);
//                    $lead->public_email = base64_encode($lead->public_email);
//                    $lead->public_phone_country_code = base64_encode($lead->public_phone_country_code);
//                    $lead->public_phone_number = base64_encode($lead->public_phone_number);
//                    $lead->contact_phone_number = base64_encode($lead->contact_phone_number);                
//
//                    $lead->phone_number = $this->crypt($lead->phone_number);
//                    $lead->address_street = $this->crypt($lead->address_street);
//                    $lead->biography = $this->crypt($lead->biography);
//                    $lead->birthday = $this->crypt($lead->birthday);
//                    $lead->city_id = $this->crypt($lead->city_id);
//                    $lead->city_name = $this->crypt($lead->city_name);
//                    $lead->zip_code = $this->crypt($lead->zip_code);
//                    $lead->coef_f_weight = $this->crypt($lead->coef_f_weight);
//                    $lead->country_code = $this->crypt($lead->country_code);
//                    $lead->external_url = $this->crypt($lead->external_url);
//                    $lead->fb_page_call_to_action_id = $this->crypt($lead->fb_page_call_to_action_id);
//                    $lead->fb_u_id = $this->crypt($lead->fb_u_id);
//                    //$lead->follower_count = $this->crypt($lead->follower_count);
//                    $lead->full_name = $this->crypt($lead->full_name);
//                    $lead->is_verified = $this->crypt($lead->is_verified);
//                    //$lead->media_count = $this->crypt($lead->media_count);
//                    $lead->get_show_insights_terms = $this->crypt($lead->get_show_insights_terms);
//
//                    $this->save_extracted_crypt_leads($lead->reference_profile_id, $lead,$table);
//                    echo $lead->ds_user_id."<br>";
//                }
//            }
//            
//         }
//         
//        public function save_extracted_leads($profile_id, $lead){
//            try {
//                $result=false;
//                if($profile_id>11){
//                    $sql = "SELECT * FROM leads WHERE reference_profile_id=".$profile_id." AND ds_user_id=".$lead->ds_user_id.";";
//                    $result = mysqli_query($this->connection, $sql);
//                    $result = $result->fetch_object();
//                    $cnt = count($result);
//                }else{
//                    $cnt=0;
//                }
//                if(!$cnt){
//                    $t= time();
//                    if($lead->category)
//                        $category = $lead->category;
//                    else
//                        $category = 'single_profile';
//                    $sql = ""
//                        . "INSERT INTO leads "
//                        . "(reference_profile_id, username, ds_user_id, is_private, is_business, "
//                        . "private_email, biography_email, public_email, "
//                        . "public_phone_country_code, public_phone_number, contact_phone_number, phone_number, "
//                        . "gender, category, extracted_date, "
//                        . "address_street, biography, birthday, city_id, city_name, zip_code, coef_f_weight, country_code, external_url, "
//                        . "fb_page_call_to_action_id, fb_u_id, follower_count, full_name, is_verified, media_count, get_show_insights_terms"
//                        . ") "
//                        . "VALUES ( '$profile_id', '$lead->username', '$lead->ds_user_id', '$lead->is_private', '$lead->is_business', "
//                            . " '$lead->private_email', '$lead->biography_email', '$lead->public_email', "
//                            . " '$lead->public_phone_country_code', '$lead->public_phone_number', '$lead->contact_phone_number', '$lead->phone_number', "
//                            . " '$lead->gender', '$category', '$t', "
//                            . " '$lead->address_street', '$lead->biography', '$lead->birthday', '$lead->city_id', '$lead->city_name', '$lead->zip_code', "
//                            . " '$lead->coef_f_weight', '$lead->country_code', '$lead->external_url', '$lead->fb_page_call_to_action_id', '$lead->fb_u_id', '$lead->follower_count', "
//                            . " '$lead->full_name', '$lead->is_verified', '$lead->media_count', '$lead->get_show_insights_terms' "
//                            . ");";
//                    
//                    
//                    $result = mysqli_query($this->connection, $sql);                    
//                }
//            } catch (\Exception $exc) {
//                echo $exc->getTraceAsString();
//            }
//            return $result;
//        }
         
     }

}
