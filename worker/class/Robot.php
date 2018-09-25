<?php

namespace leads\cls {
    require_once 'DB.php';
    require_once 'Gmail.php';
    require_once 'Profile.php';
    require_once 'InstaAPI.php';
    require_once 'Utils.php';
    require_once 'profile_type.php';
    require_once 'profiles_status.php';

    ini_set('xdebug.var_display_max_depth', 64);
    ini_set('xdebug.var_display_max_children', 256);
    ini_set('xdebug.var_display_max_data', 8024);
    
    set_time_limit(0);
    date_default_timezone_set('UTC');
    require __DIR__.'/../externals/vendor/autoload.php';
    
    class Robot{
        public $id;
        public $IP;
        public $IPS;
        public $dir;
        public $config;
        public $daily_work;
        public $utils;
        public $csrftoken = NULL;        
        public $next_work;
        public $profile_table_key;
        public $ig;
        public $DB;
        
        //--------principal functions--------------
        function __construct($DB = NULL, $conf_file = "/../../../LEADS-WORKER.INI") {
            $config = parse_ini_file(dirname(__FILE__) . $conf_file, true);
            $this->DB = $DB ? $DB : new \leads\cls\DB();
            $this->utils = new \leads\cls\Utils();
        }
        
        public function do_instagram_login_by_API($login, $pass){ //return $ig object
             try {
                $result = $this->make_login($login, $pass);
                return $result;
            } catch (\Exception $e) {
                if ((strpos($e->getMessage(), 'Challenge required') !== FALSE) || (strpos($e->getMessage(), 'Checkpoint required') !== FALSE) || (strpos($e->getMessage(), 'challenge_required') !== FALSE)) {
                    return 'VERIFY_ACCOUNT';
                } else if (strpos($e->getMessage(), 'password you entered is incorrect') !== FALSE)
                    return 'BLOCKED_BY_INSTA';
                else
                    //$result->json_response->message = $e->getMessage();
                    return 'NOT LOGGED';
            }
        }
        
        public function make_login($login, $pass){ //return $ig object
            $instaAPI = new \leads\cls\InstaAPI();
            $result ="";
            try {
                $resp = $instaAPI->login($login, $pass);
                $result =(object)array(
                    'ig'=>$resp,
                    'cookies'=>$instaAPI->Cookies
                );
                
            } catch (\Exception $exc) {
                throw $exc;
            }
            return $result;                
        }
               
        public function do_robot_extract_leads($ig, $cookies, $multi_level) {
            $result = array();
            $result['has_exception'] = false;
            $result['success']=false;
            try{
                //0. inicialização da variáveis
                $cursor = $this->next_work->profile->cursor;
                $rp = $this->next_work->profile->profile;
                $pk = $this->next_work->profile->insta_id;
                if($pk == NULL){ //salvar el ds_user_id del RP
                    $pk = $ig->people->getUserIdForName($rp); 
                    $this->DB->update_field_in_DB('profiles', 
                        'id', $this->next_work->profile->id, 
                        'insta_id', $pk);
                }
                
                //1. obter seguidores segundo o tipo de perfil de referencia
                if($this->next_work->profile->profile_type_id == profile_type::REFERENCE_PROFILE){
                    
                    $rankToken = \InstagramAPI\Signatures::generateUUID();
                    $userId = $ig->people->getUserIdForName($rp);
                    $response = $ig->people->getFollowers($userId, $rankToken, null, $cursor);
                    $followers = $response->getUsers();
                    $new_cursor = $response->getNextMaxId();
                } else
                if($this->next_work->profile->profile_type_id== profile_type::GEOLOCATION) {
                    //$followers deve ser un array con los nombres de los seguidores
                    $resp = $this->get_profiles_from_geolocation($this->next_work->profile->insta_id, $cookies,50, $cursor);
                    $followers = $resp->followers; //array de nomes de perfis
                    $new_cursor = $resp->cursor; //string com o cursor ou null se chegou no final
                }else
                if($this->next_work->profile->profile_type_id== profile_type::HASHTAG) {
                    $resp = $this->get_profiles_from_hastag($this->next_work->profile->profile, $cookies,10, $cursor);
                    $followers = $resp->followers; //array de nomes de perfis
                    $new_cursor = $resp->cursor; //string com o cursor ou null se chegou no final
                }
                
                //2. Extrair as leads de cada seguidor dessa pagina                
                $extracted_leads = array();
                $real=0; $total=0;
                
                foreach ($followers as $user) {//para cada seguidor
                    //2.1 extaer las leads do perfil atual
                    if($this->next_work->profile->profile_type_id == profile_type::REFERENCE_PROFILE){
                        $username = $user->getUsername();
                        sleep(2);
                    }
                    else
                        $username = $user;
                    $leads = (object)$this->extract_leads($ig, 'username', $username);
                    
                    //2.2 incrementar a quantidade total de perfis analisados dessa perfil
                    if(!$this->next_work->profile->amount_analysed_profiles)
                        $this->next_work->profile->amount_analysed_profiles = 0;
                    $this->next_work->profile->amount_analysed_profiles ++;
                    $this->DB->update_field_in_DB('profiles', 
                        'id', $this->next_work->profile->id, 
                        'amount_analysed_profiles', $this->next_work->profile->amount_analysed_profiles
                    );
                    
                    $existing_lead = true;
                    if($leads->private_email || $leads->biography_email || $leads->public_email){
                        $existing_lead = $this->DB->existing_lead($leads->ds_user_id);
                    }
                            
                    //2. determinar tabelas para inserir as leads e possivel RP futuro
                    $multi_level_result = $this->get_multi_level_hash($leads->ds_user_id, $multi_level);
                    
                    //2.3 salvar as leads extraidas do perfil atual e a informacion correspondiente
                    if(($leads->private_email || $leads->biography_email || $leads->public_email) && !$existing_lead){
                        //A.1 salvar a lead
                        $resp = $this->DB->save_extracted_crypt_leads($this->next_work->profile->id, $leads, $multi_level_result->table_to_leads);
                        //A.2 incrementar a quantidade total de leads extraidos da campanha
                        if($this->next_work->client->brazilian ==1)
                            $fixed_price = $this->config->FIXED_LEADS_PRICE;
                        else    
                            $fixed_price = $this->config->FIXED_LEADS_PRICE_EX;
                        if($resp){
                            $result['success']=true;
                            if(!$this->next_work->profile->amount_leads)
                                $this->next_work->profile->amount_leads = 0;
                            $this->next_work->profile->amount_leads ++;
                            $this->DB->update_field_in_DB('profiles',
                                'id', $this->next_work->profile->id,
                                'amount_leads', $this->next_work->profile->amount_leads
                            );
                            //A.3 atualizar o orçamento disponível da campanha
                            
                            $this->next_work->campaing->available_daily_value -= $fixed_price;
                            $this->DB->update_field_in_DB('campaings', 
                                'id', $this->next_work->campaing->id,
                                'available_daily_value', $this->next_work->campaing->available_daily_value
                            );
                            echo "<br>\n<br>\n ---------->Leads extracted and save successfully from profile ".($leads->ds_user_id)." na tabela ".$multi_level_result->table_to_leads."<br>\n<br>\n";
                        } else{
                            echo "<br>\n<br>\nERROR: erro salvando a leads do perfil ".($leads->ds_user_id)." na tabela ".$multi_level_result->table_to_leads."<br>\n<br>\n";
                        }
                            
                        //A.4 se nao tiver orçamento disponivel, eliminar trabalho dessa campanha
                        if($this->next_work->campaing->available_daily_value < $fixed_price){ //não tem orçamento disponível nem pra uma leads mais
                            $this->DB->delete_daily_work_by_campaing($this->next_work->campaing->id);
                            break;
                        }
                    }
                    
                    //2.4 salvar o perfil para ser usado no futuro
                    if($multi_level && $leads->follower_count && $leads->follower_count>300 && $multi_level_result->table_to_profiles !=='not_insert_profile_to_future'){
                        $this->DB->insert_future_reference_profile( $multi_level_result->table_to_profiles, $leads->ds_user_id, $leads->username);
                    }                    
                }
                //3. ver se perfil chegou ao fim, se não, salvar o cursor
                if($cursor!==null && $new_cursor===null){
                    $this->DB->delete_daily_work_by_profile($this->next_work->profile->id);
                    /*$this->DB->update_field_in_DB('profiles',
                        'id', $this->next_work->profile->id,
                        'profile_status_id',profiles_status::ENDED);
                    $this->DB->update_field_in_DB('profiles',
                        'id', $this->next_work->profile->id,
                        'profile_status_date',time());
                    $this->DB->update_field_in_DB('profiles',
                        'id', $this->next_work->profile->id,
                        '`cursor`','NULL');*/
                } else{
                    $this->DB->update_field_in_DB('profiles', 
                        'id', $this->next_work->profile->id, 
                        '`cursor`', "$new_cursor");
                }   
            } catch (\Exception $e) {
                $result['has_exception'] = true;
                $result['exception_message'] = $e->getMessage();                
                return (object)$result;
            }
            return (object)$result;
        }
        
        public function mysql_escape_mimic($inp) {
            if(is_array($inp))
                return array_map(__METHOD__, $inp);

            if(!empty($inp) && is_string($inp)) {
                return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp);
            }

            return $inp;
        } 
        
        public function extract_leads($ig, $method, $method_value){
            $leads = array();
            if($method ==='username')
                $profileInfo = $ig->people->getInfoByName($method_value);
            elseif($method === 'user_id')
                $profileInfo = $ig->people->getInfoById($method_value);  
            $user=$profileInfo->getUser();
            
            $leads['username']= $user->getUsername();
            $leads['ds_user_id'] = $user->getPk();
            $leads['is_private']= $user->getIsPrivate();
            $leads['is_business']= $user->getIsBusiness();
            $leads['gender']= $user->getGender();
            $leads['private_email']= $user->getEmail();
            $leads['public_email']= $user->getPublicEmail();
            $leads['biography_email']= null;
            $biography = $user->getBiography();
            if($biography !== null && $biography !=='null')
                $leads['biography_email']= $this->utils->extractEmail($biography);
            elseif(!$leads['biography_email']){
                $full_name = $user->getFullName();
                if(!$leads['biography_email'] && $full_name !== null && $full_name)
                $leads['biography_email']= $this->utils->extractEmail($full_name);
            }
            $leads['public_phone_country_code']= $user->getPublicPhoneCountryCode();
            $leads['public_phone_number']= $user->getPublicPhoneNumber();
            $leads['contact_phone_number']= $user->getContactPhoneNumber();
            $leads['phone_number']= $user->getPhoneNumber();
            $leads['category'] = $user->getCategory();            
            $leads['address_street'] = $user->getAddressStreet();
            $leads['biography'] = $user->getBiography();
            $leads['birthday'] = $user->getBirthday();
            $leads['city_name'] = $user->getCityName();
            $leads['city_id'] = $user->getCityId();
            $leads['zip_code'] = $user->getZip();
            $leads['coef_f_weight'] = $user->getCoeffWeight();
            $leads['country_code'] = $user->getCountryCode();
            $leads['external_url'] = $user->getExternalUrl();
            $leads['fb_page_call_to_action_id'] = $user->getFbPageCallToActionId();
            $leads['fb_u_id'] = $user->getFbuid();
            $leads['follower_count'] = $user->getFollowerCount();
            $leads['full_name'] = $user->getFullName();
            $leads['is_verified'] = $user->getIsVerified();
            $leads['media_count'] = $user->getMediaCount();
            $leads['get_show_insights_terms'] = $user->getShowInsightsTerms();
            
            $leads = $this->mysql_escape_mimic($leads);
            return $leads;
        }
        
        public function get_profiles_from_geolocation($rp_insta_id, $cookies, $quantity, $cursor) {
            $Profiles = array();
            try{
                $json_response = $this->get_insta_geomedia($cookies, $rp_insta_id, $quantity, $cursor);
                if (is_object($json_response) && $json_response->status == 'ok') {
                    if (isset($json_response->data->location->edge_location_to_media)) { // if response is ok
                        $page_info = $json_response->data->location->edge_location_to_media->page_info;
                        foreach ($json_response->data->location->edge_location_to_media->edges as $Edge) {
                            $profile = new \stdClass();
                            $profile->node = $this->get_geo_post_user_info($cookies, $rp_insta_id, $Edge->node->shortcode);
                            array_push($Profiles, $profile->node->username);
                        }
                        $error = FALSE;
                    } else {
                        $page_info->end_cursor = NULL;
                        $page_info->has_next_page = false;
                    }
                }
                return (object)array(
                    'followers'=> $Profiles,
                    'cursor'=>$cursor
                );
            }
            catch (\Exception $exc) {
                //echo $exc->getTraceAsString();
                throw new \Exception("Not followers from geolocation");
            }
        }
        
        public function get_insta_geomedia($cookies, $location, $N, &$cursor = NULL) {
            try {
//                $tag_query = 'ac38b90f0f3981c42092016a37c59bf7';
                $tag_query = 'ac38b90f0f3981c42092016a37c59bf7';
                $variables = "{\"id\":\"$location\",\"first\":$N,\"after\":\"$cursor\"}";
                $curl_str = $this->make_curl_followers_query($tag_query, $variables, $cookies);
                if ($curl_str === NULL)
                    return NULL;
                exec('/usr/bin/'.$curl_str, $output, $status);
                $json = NULL;
                if(is_array($output)){
                    $json = json_decode($output[0]);
                    echo "line 308"; var_dump($output);
                    if(isset($json->data->location->edge_location_to_media) && isset($json->data->location->edge_location_to_media->page_info)) {
                        $cursor = $json->data->location->edge_location_to_media->page_info->end_cursor;
                        if (count($json->data->location->edge_location_to_media->edges) == 0) {
                            $cursor = null;
    //                        $this->DB->update_field_in_DB('profiles',
    //                        'id', $this->next_work->profile->id,
    //                        '`cursor`','NULL');                        
                            $this->DB->delete_daily_work_by_profile($this->next_work->profile->id);
                            echo ("<br>\n Goelocation ".$this->next_work->profile->id." Set end_cursor to NULL!!!!!!!! Deleted daily work!!!!!!!!!!!!");
                        }
                    } else 
                        if (isset($json->data) && $json->data->location == NULL) {
                            print_r($curl_str);
                            $cursor = null;
        //                    $this->DB->update_field_in_DB('profiles',
        //                    'id', $this->next_work->profile->id,
        //                    '`cursor`','NULL');                        
                            $this->DB->delete_daily_work_by_profile($this->next_work->profile->id);
                            echo ("<br>\n Goelocation ".$this->next_work->profile->id." Set end_cursor to NULL!!!!!!!! Deleted daily work!!!!!!!!!!!!");
                        } else {
                            var_dump($output);
                            print_r($curl_str);
                            print_r("------JSON-------");
                            print_r($json);
                            echo ("<br>\n Untrated error in Geolocation!!!");
                            throw new \Exception("Not followers from geolocation");
                        }
                }
                
                return $json;
            } catch (\Exception $exc) {
                //echo $exc->getTraceAsString();
                throw new \Exception("Not followers from geolocation");
            }
        }
        
        public function get_geo_post_user_info($cookies, $location_id, $post_reference) {
            //echo " -------Obtindo dados de perfil que postou na geolocalizacao------------<br>\n<br>\n";
            $csrftoken = isset($cookies->csrftoken) ? $cookies->csrftoken : 0;
            $ds_user_id = isset($cookies->ds_user_id) ? $cookies->ds_user_id : 0;
            $sessionid = isset($cookies->sessionid) ? $cookies->sessionid : 0;
            $mid = isset($cookies->mid) ? $cookies->mid : 0;
            $url = "https://www.instagram.com/p/$post_reference/?taken-at=$location_id&__a=1";
            $curl_str = "curl '$url' ";
            $curl_str .= "-H 'Accept-Encoding: gzip, deflate, br' ";
            $curl_str .= "-H 'X-Requested-With: XMLHttpRequest' ";
            $curl_str .= "-H 'Accept-Language: pt-BR,pt;q=0.8,en-US;q=0.6,en;q=0.4' ";
            $curl_str .= "-H 'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:49.0) Gecko/20100101 Firefox/49.0' ";
            $curl_str .= "-H 'Accept: */*' ";
            $curl_str .= "-H 'Referer: https://www.instagram.com/' ";
            $curl_str .= "-H 'Authority: www.instagram.com' ";
            $curl_str .= "-H 'Cookie: mid=$mid; sessionid=$sessionid; s_network=; ig_pr=1; ig_vw=1855; csrftoken=$csrftoken; ds_user_id=$ds_user_id' ";
            $curl_str .= "--compressed ";
            $result = exec('/usr/bin/'.$curl_str, $output, $status);
            $object = NULL;
            if(is_array($output)){
                $object = json_decode($output[0]);
                echo "line 366"; var_dump($output);
            }
            if(is_object($object) && isset($object->graphql->shortcode_media->owner)) {
                return $object->graphql->shortcode_media->owner;
            }
            return NULL;
        }
        
        public function make_curl_followers_query($query, $variables, $cookies=NULL){            
            $variables = urlencode($variables);
            $url = "https://www.instagram.com/graphql/query/?query_hash=$query&variables=$variables";            
            $curl_str = "curl '$url' ";
            if($cookies !== NULL)
            {
                if($cookies->mid == NULL|| $cookies->csrftoken == NULL || $cookies->sessionid == NULL ||
                        $cookies->ds_user_id == NULL)
                    return NULL;
               $curl_str .= "-H 'Cookie: mid=$cookies->mid; sessionid=$cookies->sessionid; s_network=; ig_pr=1; ig_vw=1855; csrftoken=$cookies->csrftoken; ds_user_id=$cookies->ds_user_id' ";            
               $curl_str .= "-H 'X-CSRFToken: $cookies->csrftoken' ";
            }
            
            $curl_str .= "-H 'Origin: https://www.instagram.com' ";
            $curl_str .= "-H 'Accept-Encoding: gzip, deflate' ";
            $curl_str .= "-H 'Accept-Language: pt-BR,pt;q=0.8,en-US;q=0.6,en;q=0.4' ";
            $curl_str .= "-H 'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:49.0) Gecko/20100101 Firefox/49.0' ";
            $curl_str .= "-H 'X-Requested-with: XMLHttpRequest' ";
            //$curl_str .= "-H 'X-Instagram-ajax: 1' ";
            $curl_str .= "-H 'content-type: application/x-www-form-urlencoded' ";
            $curl_str .= "-H 'Accept: */*' ";
            $curl_str .= "-H 'Referer: https://www.instagram.com/' ";
            $curl_str .= "-H 'Authority: www.instagram.com' ";
            $curl_str .= "--compressed ";
            return $curl_str;
        }
        
        
        
        public function get_profiles_from_hastag($tag_name, $cookies, $quantity, $cursor) {
            $Profiles = array();
            try{
                $json_response = $this->get_insta_tagmedia($cookies, $tag_name, $quantity, $cursor);
                if (is_object($json_response)) {
                    if (isset($json_response->data->hashtag->edge_hashtag_to_media)) { // if response is ok
                        $page_info = $json_response->data->hashtag->edge_hashtag_to_media->page_info;
                        foreach ($json_response->data->hashtag->edge_hashtag_to_media->edges as $Edge) {
                            $profile = new \stdClass();
                            $profile->node = $this->get_tag_post_user_info($cookies,  $Edge->node->shortcode);
                            array_push($Profiles, $profile->node->username);
                        }
                        $error = FALSE;
                    } else {
                        $page_info->end_cursor = NULL;
                        $page_info->has_next_page = false;
                    }
                }
                return (object)array(
                    'followers'=> $Profiles,
                    'cursor'=>$cursor
                );
            }
            catch (\Exception $exc) {
                //echo $exc->getTraceAsString();
                throw new \Exception("Not followers from hastag");
            }
        }
//        
        public function get_insta_tagmedia($cookies, $tag, $N, &$cursor = NULL) {
            try {
//                $tag_query = '298b92c8d7cad703f7565aa892ede943';
                $tag_query = 'ded47faa9a1aaded10161a2ff32abb6b';
                $variables = "{\"tag_name\":\"$tag\",\"first\":2,\"after\":\"$cursor\"}";
                $curl_str = $this->make_curl_followers_query($tag_query, $variables, $cookies);
                if ($curl_str === NULL)
                    return NULL;
                exec('/usr/bin/'.$curl_str, $output, $status);
                $json = NULL;
                if(is_array($output)){
                    $json = json_decode($output[0]);
                    echo "line 443"; var_dump($output);
                }
                //var_dump($output);
                if(isset($json) && $json->status == 'ok')
                {
                    if (isset($json->data->hashtag->edge_hashtag_to_media) && isset($json->data->hashtag->edge_hashtag_to_media->page_info)) {
                        $cursor = $json->data->hashtag->edge_hashtag_to_media->page_info->end_cursor;
                        if (count($json->data->hashtag->edge_hashtag_to_media->edges) == 0) {
                            $cursor = null;
//                            echo ("<br>\n No nodes!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!");
//                            $this->DB->update_reference_cursor($this->daily_work->reference_id, NULL);
//                            $result = $this->DB->delete_daily_work($this->daily_work->reference_id);
                            echo ("<br>\n Hashtag ".$this->next_work->profile->id." Set end_cursor to NULL!!!!!!!! Deleted daily work!!!!!!!!!!!!");
                        }
                    }
                }
                
                else {
                    var_dump($output);
                    print_r($curl_str);
                    echo ("<br>n<br>\n Untrated error!!!<br>\n<br>\n");
                    throw new \Exception("Not followers from hashtag");
                }
                return $json;
            } catch (\Exception $exc) {
                echo $exc->getTraceAsString();
                throw new \Exception("Not followers from hashtag");
            }
        }
        
        public function get_tag_post_user_info($cookies, $post_reference) {
            //echo " -------Obtindo dados de perfil que postou na geolocalizacao------------<br>\n<br>\n";
            $csrftoken = isset($cookies->csrftoken) ? $cookies->csrftoken : 0;
            $ds_user_id = isset($cookies->ds_user_id) ? $cookies->ds_user_id : 0;
            $sessionid = isset($cookies->sessionid) ? $cookies->sessionid : 0;
            $mid = isset($cookies->mid) ? $cookies->mid : 0;
            $url = "https://www.instagram.com/p/$post_reference/?__a=1";
            $curl_str = "curl '$url' ";
            $curl_str .= "-H 'Accept-Encoding: gzip, deflate, br' ";
            $curl_str .= "-H 'X-Requested-With: XMLHttpRequest' ";
            $curl_str .= "-H 'Accept-Language: pt-BR,pt;q=0.8,en-US;q=0.6,en;q=0.4' ";
            $curl_str .= "-H 'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:49.0) Gecko/20100101 Firefox/49.0' ";
            $curl_str .= "-H 'Accept: */*' ";
            $curl_str .= "-H 'Referer: https://www.instagram.com/' ";
            $curl_str .= "-H 'Authority: www.instagram.com' ";
            $curl_str .= "-H 'Cookie: mid=$mid; sessionid=$sessionid; s_network=; ig_pr=1; ig_vw=1855; csrftoken=$csrftoken; ds_user_id=$ds_user_id' ";
            $curl_str .= "--compressed ";
            $result = exec('/usr/bin/'.$curl_str, $output, $status);
            $object = NULL;
            if(is_array($output)){
                $object = json_decode($output[0]);
            }
            if (is_object($object) && isset($object->graphql->shortcode_media->owner)) {
                return $object->graphql->shortcode_media->owner;
            }
            return NULL;
        }
        
        public function get_multi_level_hash($ds_user_id, $multi_level){
            $result=array();
            if($multi_level==false || $multi_level=='false'){
                $result['table_to_leads'] = 'leads';
                $result['table_to_profiles'] = 'not_insert_profile_to_future' ;
                return (object)$result;
            } 
            if($ds_user_id >0 && $ds_user_id< 1000000000){
                $result['table_to_leads'] = 'leads_1kM';
                $result['table_to_profiles'] = 'profiles_1kM' ;
            } else
            if($ds_user_id >1000000000 && $ds_user_id< 2000000000){
                $result['table_to_leads'] = 'leads_2kM';
                $result['table_to_profiles'] = 'profiles_2kM' ;
            } else
            if($ds_user_id >2000000000 && $ds_user_id< 3000000000){
                $result['table_to_leads'] = 'leads_3kM';
                $result['table_to_profiles'] = 'profiles_3kM' ;
            } else
            if($ds_user_id >3000000000 && $ds_user_id< 4000000000){
                $result['table_to_leads'] = 'leads_4kM';
                $result['table_to_profiles'] = 'profiles_4kM' ;
            } else
            if($ds_user_id >4000000000 && $ds_user_id< 5000000000){
                $result['table_to_leads'] = 'leads_5kM';
                $result['table_to_profiles'] = 'profiles_5kM' ;
            } else
            if($ds_user_id >5000000000 && $ds_user_id< 6000000000){
                $result['table_to_leads'] = 'leads_6kM';
                $result['table_to_profiles'] = 'profiles_6kM' ;
            } else
            if($ds_user_id >6000000000 && $ds_user_id< 7000000000){
                $result['table_to_leads'] = 'leads_7kM';
                $result['table_to_profiles'] = 'profiles_7kM' ;
            } else
            if($ds_user_id >7000000000 && $ds_user_id< 8000000000){
                $result['table_to_leads'] = 'leads_8kM';
                $result['table_to_profiles'] = 'profiles_8kM' ;
            } else
            if($ds_user_id >8000000000 && $ds_user_id< 9000000000){
                $result['table_to_leads'] = 'leads_9kM';
                $result['table_to_profiles'] = 'profiles_9kM' ;
            } else
            if($ds_user_id >9000000000 && $ds_user_id< 10000000000){
                $result['table_to_leads'] = 'leads_10kM';
                $result['table_to_profiles'] = 'profiles_10kM' ;
            } 
            return (object)$result;
        }

        /*public function do_robot_extract_leads_by_id($ig, $ds_user_id, $robot_profile_id) {
            try{
                $result['has_exception']=false;
                $result['lead_saved']=false;
                $leads = (object)$this->extract_leads($ig, 'user_id', $ds_user_id); 
                if($leads->private_email || $leads->biography_email || $leads->public_email){
                    echo "<br><br>\n\n The profile ".$ds_user_id." has leads ";
                    $result['lead_saved'] = $this->DB->save_extracted_crypt_leads($robot_profile_id, $leads);
                    if($result['lead_saved'])
                        echo " and was saved succeslly<br><br>\n\n";
                    else 
                        echo " but an ERROR occured in save function<br><br>\n\n";
                } else{
                    echo "<br><br>\n\n The profile ".$ds_user_id." DONT have leads <br><br>\n\n";
                }                
            } catch (\Exception $e) {
                $result['has_exception'] = true;
                $result['exception_message'] = $e->getMessage();      
                echo '<br><br>\n\n An exception occurred with profile '.$ds_user_id.'';
                echo "  EXCEPTION:  ".$result['exception_message']."<br><br>\n\n";
            }
            return (object)$result;
        }*/
        
    }     
}

?>
