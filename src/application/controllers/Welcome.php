<?php

ini_set('xdebug.var_display_max_depth', 64);
ini_set('xdebug.var_display_max_children', 256);
ini_set('xdebug.var_display_max_data', 8024);


class Welcome extends CI_Controller {
    
    private $security_purchase_code; //random number in [100000;999999] interval and coded by md5 crypted to antihacker control
    public $language =NULL;

        //------------desenvolvido para DUMBU-LEADS-------------------
    public function load_language($language = NULL){
        if (!$this->session->userdata('id')){
            
            $this->load->model('class/system_config');
            $GLOBALS['sistem_config'] = $this->system_config->load();
            if($language != "PT" && $language != "EN" && $language != "ES")
                $language = NULL;
            if(!$language)
                $GLOBALS['language'] = $GLOBALS['sistem_config']->LANGUAGE;            
            else
                $GLOBALS['language'] = $language;
        }
        else
        {
            $GLOBALS['language'] = $this->session->userdata('language');
        }
    }
    
    public function real_end_date($date){
        $end_date = $date;
        $now = time();
        if(date("Ymd",$date) == date("Ymd",$now))
            return $now;
        return $end_date;
    }
    
    public function is_brazilian_ip(){
        /*
        $prefixos_br = array(   '45.','72.','93.','128','131','132','138','139',
                                '139','143','146','147','150','152','155','157','161','164',
                                '170','177','179','181','186','187','189','190','191','200','201');
        $prefixo_ip = substr($_SERVER['REMOTE_ADDR'], 0, 3);

        if (in_array($prefixo_ip, $prefixos_br)){
            return 1;
        }
        else{
            return 0;
        }*/
        if($_SERVER['REMOTE_ADDR'] === "127.0.0.1")
            return 1;
        
        if($_SERVER['REMOTE_ADDR'] === "191.252.100.122")
            return 1;
        
        return 1;
        return 0;
//        $datas = file_get_contents('https://ipstack.com/ipstack_api.php?ip='.$_SERVER['REMOTE_ADDR']);//
//        $response = json_decode($datas);
//        if(is_object($response) && $response->country_code == "BR")
//            return 1;
//        return 0;
    }
    
    public function mysql_escape_mimic($inp) {
        if(is_array($inp))
            return array_map(__METHOD__, $inp);

        if(!empty($inp) && is_string($inp)) {
            return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp);
        }

        return $inp;
    } 
        
    public function index() {         
        $this->load->model('class/user_role');        
        $param = array();
        $this->load->model('class/system_config');
        $GLOBALS['sistem_config'] = $this->system_config->load();
        
        $open_session = $this->session->userdata('id')?TRUE:FALSE;
        if($this->session->userdata('id') && $this->session->userdata('module') != "LEADS"){
            $this->session->sess_destroy();
            session_destroy();
            $open_session = FALSE;
        }
        if (!$open_session){            
            $language=$this->input->get();            
            if($language['language'] != "PT" && $language['language'] != "ES" && $language['language'] != "EN")
                    $language['language'] = NULL;
            
            if(isset($language['language']))                
                $param['language']=$language['language'];            
            else
                $param['language'] = $GLOBALS['sistem_config']->LANGUAGE;
            
            $param['brazilian'] = $this->is_brazilian_ip();
                
        }
        else{            
            $param['language'] = $this->session->userdata('language');            
            $param['brazilian'] = $this->session->userdata('brazilian');            
            $param['currency_symbol'] = $this->session->userdata('currency_symbol');              
        }
        
        if($param['brazilian'] == 1){
            $param['currency_symbol'] = "R$";
            $param['price_lead'] = $GLOBALS['sistem_config']->FIXED_LEADS_PRICE;
        }
        else{
            $param['currency_symbol'] = "US$";
            $param['price_lead'] = $GLOBALS['sistem_config']->FIXED_LEADS_PRICE_EX;
        }
        
        $GLOBALS['language']=$param['language'];
        $param['SCRIPT_VERSION'] = $GLOBALS['sistem_config']->SCRIPT_VERSION;
        
        $this->load->view('user_view', $param);        
    }
    
    public function client() {
        $this->load->model('class/user_role');        
        $this->load->model('class/client_model');        
        $this->load->model('class/user_model');
        $this->load->model('class/bank_ticket_model');
        $this->load->model('class/system_config');
        
        if ($this->session->userdata('role_id')==user_role::CLIENT && $this->session->userdata('module') == "LEADS"){
            //2. cargar los datos necesarios para pasarselos a la vista como parametro
            
            $param = array();
            
            $param['language'] = $this->session->userdata('language');
            $param['profiles_temp'] = $this->session->userdata('profiles_temp');
            $param['profiles_type_temp'] = $this->session->userdata('profiles_type_temp');
            $param['profiles_insta_temp'] = $this->session->userdata('profiles_insta_temp');
                        
            $init_day = $this->session->userdata('init_day');
            if(!$init_day){
                $init_day = $this->session->userdata('init_date');
            }
            $end_day = $this->session->userdata('end_day');
            if(!$end_day){
                $end_day = date(time());
            }
            $param['date_filter'] = ['init_day' => $init_day, 'end_day' => $end_day];
            
            $param['campaings'] = $this->client_model->load_campaings($this->session->userdata('id'), NULL, $init_day, $end_day);
            
            $client_data['has_payment'] = $this->user_model->has_payment($this->session->userdata('id'), $this->session->userdata('status_id')) ;
            
            $param['client_data'] = $client_data;
                    
            if(count($param['campaings']) == 0)
                $param['campaings'] = NULL;
            
            $GLOBALS['sistem_config'] = $this->system_config->load();
                                
            if($this->session->userdata('brazilian')==1){
                $param['price_lead'] = $GLOBALS['sistem_config']->FIXED_LEADS_PRICE;
                $param['currency_symbol'] = "R$";
                $param['available_ticket'] = $this->bank_ticket_model->get_available_ticket_bank_money($this->session->userdata('id'));
            }
            else{
                $param['price_lead'] = $GLOBALS['sistem_config']->FIXED_LEADS_PRICE_EX;
                $param['currency_symbol'] = "US$";
            }
            //3. cargar la vista con los parâmetros                        
            
            $param['min_daily_value'] = $GLOBALS['sistem_config']->MINIMUM_DAILY_VALUE;
            $param['min_ticket_bank'] = $GLOBALS['sistem_config']->MINIMUM_TICKET_VALUE;
            
            $param['SCRIPT_VERSION'] = $GLOBALS['sistem_config']->SCRIPT_VERSION;
            
            $this->load->view('client_view', $param);
        }
        else{            
            $this->session->sess_destroy();
            session_destroy();
            $this->index();
        }        
    }
    
    public function admin() {
        $this->load->model('class/user_role');                
        $this->load->model('class/system_config');
        
        if ($this->session->userdata('role_id')==user_role::ADMIN){
            //2. cargar los datos necesarios para pasarselos a la vista como parametro
            $GLOBALS['sistem_config'] = $this->system_config->load();
            $param = array();            
            $param['language'] = $this->session->userdata('language');
            $param['SCRIPT_VERSION'] = $GLOBALS['sistem_config']->SCRIPT_VERSION;
            $this->load->view('admin_view', $param);
        }
        else{            
            $this->session->sess_destroy();
            session_destroy();
            $this->index();
        }        
    }
    
    public function password_recovery() {                
        $this->load->model('class/system_config');
        $GLOBALS['sistem_config'] = $this->system_config->load();
        
        $input = $this->input->get(); 
        $language = $input['language'];
        if($language != "PT" && $language != "ES" && $language != "EN")
                $language = "PT";
        $param = [];
        $param['language'] = $language;
        $param['token'] = $input['token'];
        $param['login'] = $input['login'];
        $param['SCRIPT_VERSION'] = $GLOBALS['sistem_config']->SCRIPT_VERSION;
        if (!$this->session->userdata('id')){
            $this->load->view('password_recupery_view', $param);
        }
        else{
            $this->index();
        }
    }
    
    public function recover_pass() {                        
        if (!$this->session->userdata('id')){
            $this->load->model('class/user_model');
            
            $datas = $this->input->post();
            $login = trim($datas['login']);
            $email = $datas['email'];
            $language = $datas['language'];
            if($language != "PT" && $language != "ES" && $language != "EN"){
                $language = "PT";
            }
        
            if ( $login === '' || $this->is_valid_user_name($login) ){
                if ( $this->is_valid_email($email) ){                   
                    
                    $token = mt_rand().mt_rand().mt_rand();
                    
                    if($login === ''){
                        $login = NULL;
                    }
                    $user_row = $this->user_model->get_user_by_email($email, $login);
                    if($user_row){
                        $this->user_model->save_recovery_token($email, $user_row['id'], $user_row['login'], $token);
                        $this->load->model('class/system_config');                    
                        $GLOBALS['sistem_config'] = $this->system_config->load();
                        $this->load->library('gmail');

                        $result_message = $this->gmail->send_recovery_pass
                                            (
                                                $email,
                                                $user_row['login'],
                                                $token,
                                                $language
                                            );
                        $result['success'] = true;
                        //$result['message'] = $this->T("Não pode ser ", array(), $GLOBALS['language']); 
                        $result['token'] = $token;
                    }
                    else{
                        $result['success'] = false;
                        $result['message'] = $this->T("Não foi encontrado login/email.", array(), $GLOBALS['language']); 
                        $result['resource'] = 'front_page';
                    }
                }
                else{
                    $result['success'] = false;
                    $result['message'] = $this->T("Estrutura incorreta do e-mail.", array(), $GLOBALS['language']); 
                    $result['resource'] = 'front_page';
                }
            }
            else{
                $result['success'] = false;
                $result['message'] = $this->T("Estrutura incorreta do e-mail.", array(), $GLOBALS['language']); 
                $result['resource'] = 'front_page';
            }
        }
        else{
            $result['success'] = false;
            $result['message'] = $this->T("Esta operação não pode ser feita com uma sessão aberta", array(), $GLOBALS['language']); 
            $result['resource'] = 'front_page';
        }
        echo json_encode($result);
    }
    
    public function over_write_pass() {                        
        if (!$this->session->userdata('id')){
            $this->load->model('class/user_model');
            
            $datas = $this->input->post();            
            $new_pass = $datas['new_pass'];
            $token = $datas['token'];
            $login = $datas['login'];
            $language = $datas['language'];
            
            $user_row = $this->user_model->get_recover_data($login, $token);
            if($user_row){
                $result_update = $this->user_model->update_password($user_row['user_id'], $new_pass);
                
                if($result_update){
                    $this->user_model->expire_token($user_row['id']);
                    $result['success'] = true;
                    //$result['message'] = $this->T("Não pode ser ", array(), $GLOBALS['language']); 
                    $result['token'] = $token;
                }
                else{
                    $result['success'] = false;
                    $result['message'] = $this->T("A senha não pudo ser atualizada", array(), $GLOBALS['language']); 
                    $result['resource'] = 'front_page';
                }
            }
            else{
                $result['success'] = false;
                $result['message'] = $this->T("Token não válido ou expirado", array(), $GLOBALS['language']); 
                $result['resource'] = 'front_page';
            }
        }
        else{
            $result['success'] = false;
            $result['message'] = $this->T("Esta operação não pode ser feita com uma sessão aberta", array(), $GLOBALS['language']); 
            $result['resource'] = 'front_page';
        }
        echo json_encode($result);
    }

    public function reduce_profile($profile){
        if(strlen($profile) >= 9){
            return substr($profile,0,7).'...';
        }
        else{
            return $profile;
        }
    }
    
    public function same_type_of_profiles($profiles_type, $campaing_type){
        foreach ($profiles_type as $profile_type) {
            if($profile_type != $campaing_type){
                return false;
            }
        }
        return true;
    }
    
    public function is_valid_email($email)    {
        return preg_match("/^[a-zA-Z0-9\._-]+[@]([a-zA-Z0-9-]{2,}[.])*[a-zA-Z]{2,4}$/", $email);
    }
    
    public function is_valid_cpe($cpe)    {
        return preg_match("/^[0-9]{8,8}$/", $cpe);
    }
    
    public function is_valid_phone($email)    {
        return preg_match("/^[0-9]{0,15}$/", $email);
    }
    
    public function is_valid_user_name($user_name)    {
        return preg_match("/^[a-zA-Z][\._a-zA-Z0-9]{0,99}$/", $user_name);
    }
    
    public function is_valid_profile($user_name)    {
        return preg_match("/^[a-zA-Z][^=+*#&<>\[\]\\\"~;$^%{}?]{0,99}$/", $user_name);
    }
    
    public function is_valid_string($user_name)    {
        return preg_match("/^[a-zA-Z][^=+*#&<>\[\]\\\"~;$^%{}?]{0,99}$/", $user_name);
    }
    
    public function is_valid_currency($money)   {        
        return ( preg_match("/^[1-9][0-9]*([\.,][0-9]{1,2})?$/", $money) || 
                 preg_match("/^[0][\.,][1-9][0-9]?$/", $money) || 
                 preg_match("/^[0][\.,][0-9]?[1-9]$/", $money));
    }
    
    public function is_valid_credit_card_name($name){
        return preg_match("/^[A-Z ]{4,50}$/", $name);
    }
    
    public function is_valid_credit_card_number($number){
        if(!preg_match("/^[0-9]{10,20}$/", $number))
            return  false;
        return ( preg_match("/^(?:4[0-9]{12}(?:[0-9]{3})?)$/", $number) || // Validating a Visa card starting with 4, length 13 or 16 digits.
                 preg_match("/^(?:5[1-5][0-9]{14})$/", $number) || // Validating a MasterCard starting with 51 through 55, length 16 digits.
                 preg_match("/^(?:3[47][0-9]{13})$/", $number) || // // Validating a American Express credit card starting with 34 or 37, length 15 digits.
                 preg_match("/^(?:6(?:011|5[0-9][0-9])[0-9]{12})$/", $number) || // Validating a Discover card starting with 6011, length 16 digits or starting with 5, length 15 digits.
                 preg_match("/^(?:3(?:0[0-5]|[68][0-9])[0-9]{11})$/", $number) || // Validating a Diners Club card starting with 300 through 305, 36, or 38, length 14 digits.
                 preg_match("/^(?:((((636368)|(438935)|(504175)|(451416)|(636297))[0-9]{0,10})|((5067)|(4576)|(4011))[0-9]{0,12}))$/", $number) || // Validating a Elo credit card
                 preg_match("/^(?:(606282[0-9]{10}([0-9]{3})?)|(3841[0-9]{15}))$/", $number) // Validating a Hypercard
                );
    }
    
    public function is_valid_credit_card_cvc($cvc){
        return preg_match("/^[0-9]{3,4}$/", $cvc);
    }
    
    public function is_valid_month($month){
        return preg_match("/^(0?[1-9]|1[012])$/", $month);
    }
    
    public function is_valid_year($year){
        return preg_match("/^([2-9][0-9]{3})$/", $year);
    }
    
    public function errors_in_credit_card_datas($name, $number, $cvc, $month, $year){        
        $this->load_language();
        $message = NULL;
        if(!$this->is_valid_credit_card_name($name)){
            return $this->T("Erro no formato do nome.", array(), $GLOBALS['language']);
        }
        if(!$this->is_valid_credit_card_number($number)){
            return $this->T("Erro no formato do número.", array(), $GLOBALS['language']);            
        }
        if(!$this->is_valid_credit_card_cvc($cvc)){
            return $this->T("Erro no formato do CVC.", array(), $GLOBALS['language']);                        
        }
        if(!$this->is_valid_month($month)){
            return $this->T("Erro no formato do mes.", array(), $GLOBALS['language']);                        
        }
        if(!$this->is_valid_year($year)){
            return $this->T("Erro no formato do ano.", array(), $GLOBALS['language']);                        
        }
        $now = new \DateTime('now');
        $curr_month = $now->format('m');
        $curr_year = $now->format('Y');
        
        if($year < $curr_year || ($year == $curr_year && $month <= $curr_month + 1)){
            return $this->T("Seu cartão está muito próximo de expirar.", array(), $GLOBALS['language']);                        
        }
    }
    
    public function errors_in_bank_ticket($nome, $cpf, $cpe, $money, $comp, $endereco, $bairro, $municipio, $estado){        
        $this->load_language();
        $this->load->model('class/system_config');
        $GLOBALS['sistem_config'] = $this->system_config->load();
        $min_value = $GLOBALS['sistem_config']->MINIMUM_TICKET_VALUE;            
                
        $message = NULL;
        if(!$this->validaCPF($cpf)){
            return $this->T("CPF incorreto.", array(), $GLOBALS['language']);
        }
        if(!$this->is_valid_cpe($cpe)){
            return $this->T("CPE deve conter só números.", array(), $GLOBALS['language']);
        }
        if(!$this->is_valid_currency($money)){
            return $this->T("Deve fornecer um valor monetário válido.", array(), $GLOBALS['language']);
        }else{
            if($money < $min_value){
                return  $this->T("O valor minimo por boleto deve ser a partir de ", array(), $GLOBALS['language']).
                        number_format((float)($min_value/100), 2, '.', '').
                        $this->T(" reais.", array(), $GLOBALS['language']);
            }
        }
        if(!$this->is_valid_string($nome)){
            return $this->T("Deve fornecer um nome válido.", array(), $GLOBALS['language']);
        }
        if(!$this->is_valid_string("A".$comp)){
            return $this->T("Deve fornecer um complemento válido.", array(), $GLOBALS['language']);
        }
        if(!$this->is_valid_string($endereco)){
            return $this->T("Deve fornecer um endereço válido.", array(), $GLOBALS['language']);
        }
        if(!$this->is_valid_string($bairro)){
            return $this->T("Deve fornecer um bairro válido.", array(), $GLOBALS['language']);
        }
        if(!$this->is_valid_string($municipio)){
            return $this->T("Deve fornecer um municipio válido.", array(), $GLOBALS['language']);
        }
        if(!$this->is_valid_string($estado)){
            return $this->T("Deve fornecer um estado válido.", array(), $GLOBALS['language']);
        }
        
    }
    
    public function signin() {
        $datas = $this->input->post();
        $this->load_language($datas['language']);
        $promotion = $this->validate_promotional_code($datas);
        if(!$promotion['success']){
            $result['success'] = false;
            $result['message'] = $promotion['message'];
            $result['resource'] = 'front_page';
        }else{
            if (!$this->session->userdata('id')){
                $this->load->model('class/user_model');
                $this->load->model('class/user_temp_model');
                $this->load->model('class/user_role');
                $this->load->model('class/user_status');                                                                                                                                                                                                                            
                $this->load->model('class/client_model');

                if ( $this->is_valid_user_name($datas['client_login']) ){
                    if ( $this->is_valid_phone($datas['client_telf']) ){
                        if ( $this->is_valid_email($datas['client_email']) ){
                            $datas['check_pass'] = false;    //check only by the user name
                            //verificar si se puede cadastar cliente                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        
                            $user_row = $this->user_model->verify_account($datas);
                            if(!$user_row){  
                                $user_row = $this->user_temp_model->in_confirmation($datas);
                                if(!$user_row){
                                    $datas['id_number'] = rand(1000, 9999);                                 
                                    $datas['name']= "";//$datas['client_name'];
                                    $datas['telf']= $datas['client_telf'];
                                    $datas['ip']= $_SERVER['REMOTE_ADDR'];
                                    $datas['valid_code']= $promotion['valid_code'];

                                    $cadastro_id = $this->user_temp_model->insert_user($datas);

                                    if($cadastro_id){
                                        
                                        $this->load->model('class/system_config');                    
                                        $GLOBALS['sistem_config'] = $this->system_config->load();
                                        $this->load->library('gmail');
                                        //$this->Gmail = new \leads\cls\Gmail();

                                        $result_message = $this->gmail->send_number_confirm
                                                            (
                                                                $datas['client_email'],
                                                                $datas['client_login'],
                                                                $datas['id_number'],
                                                                $GLOBALS['language']
                                                            );
                                        $result['success'] = true;
                                        $result['message'] = 'Signin success ';
                                        $result['resource'] = 'client';
                                        $result['number'] = true;
                                    }else
                                    {
                                        $result['success'] = false;
                                        $result['message'] = $this->T("Erro no cadastro", array(), $GLOBALS['language']);                        
                                        $result['resource'] = 'front_page';
                                    }
                                }
                                else{
                                    $result['success'] = true;
                                    $result['message'] = $this->T("Usuário em fase de cadastro. Por favor insira o número de 4 dígitos enviado a seu e-mail.", array(), $GLOBALS['language']); 
                                    $result['resource'] = 'front_page'; 
                                    $result['number'] = true;
                                }
                            }
                            else{
                                $result['success'] = false;
                                $result['message'] = $this->T("Usuário existente no sistema, por favor faça o login.", array(), $GLOBALS['language']); 
                                $result['resource'] = 'front_page'; 
                            }
                        }
                        else{
                            $result['success'] = false;
                            $result['message'] = $this->T("Estrutura incorreta do e-mail.", array(), $GLOBALS['language']); 
                            $result['resource'] = 'front_page';
                        }
                    }
                    else{
                            $result['success'] = false;
                            $result['message'] = $this->T("O telefone só deve conter números!", array(), $GLOBALS['language']); 
                            $result['resource'] = 'front_page';
                        }
                }
                else{
                    $result['success'] = false;
                    $result['message'] = $this->T("Estrutura incorreta para o nome de usuário.", array(), $GLOBALS['language']); 
                    $result['resource'] = 'front_page';
                }
            }
            else{
                $result['success'] = false;
                $result['message'] = $this->T("Verifique que nenhuma sessão no sistema está aberta.", array(), $GLOBALS['language']); 
                $result['resource'] = 'front_page';
            }
        }
        echo json_encode($result);
    }
    
    public function signin_number() {
        $datas = $this->input->post();
        $this->load_language($datas['language']);
        
        if (!$this->session->userdata('id')){
            $this->load->model('class/user_model');
            $this->load->model('class/user_temp_model');
            $this->load->model('class/bank_ticket_model');
            $this->load->model('class/user_role');
            $this->load->model('class/user_status');                                                                                                                                                                                                                            
            //$datas = $this->input->post();
            
            if ( $this->is_valid_user_name($datas['client_login']) ){                
                if ( $this->is_valid_phone($datas['client_telf']) ){
                    if ( $this->is_valid_email($datas['client_email']) ){
                        $datas['check_pass'] = false;    //check only by the user name
                        //verificar si se puede cadastar cliente                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        
                        $user_row = $this->user_model->verify_account($datas);
                        if(!$user_row){  
                            $user_row = $this->user_temp_model->verify_confirmation($datas);
                            if($user_row){
                                
                                $datas['role_id'] = user_role::CLIENT;
                                $datas['status_id'] = user_status::BEGINNER;
                                $datas['init_date']= time();
                                $datas['status_date']= $datas['init_date'];
                                $datas['name']= $datas['client_name'];
                                $datas['telf']= $datas['client_telf'];
                                $datas['brazilian'] = $this->is_brazilian_ip();
                                if($user_row['valid_code']){
                                    $datas['promotional_code'] = $user_row['promotional_code'];                                  
                                }

                                $this->user_temp_model->delete_temp_user($user_row['id']);
                                $cadastro_id = $this->user_model->insert_user($datas);

                                if($cadastro_id){                                    
                                    if($user_row['valid_code']){
                                        //crear boleto de 90 reales
                                        $code['FIRST-SIGN-IN-BUY'] = 90*100;
                                        $code['53C0ND-S1GN-1N-8UY'] = 5000*100;
                                        $code['TENR-SIGN-IN-BUY'] = 10*100;
                                        
                                        $value_code = $code[$datas['promotional_code']];
                                        if(is_numeric($value_code))
                                        {
                                            $datas_ticket = ["user_id" => $cadastro_id, "emission_money_value" => $value_code];
                                            $this->bank_ticket_model->insert_promotional_ticket($datas_ticket);
                                        }
                                    }
                                    $this->load->model('class/system_config');                    
                                    $GLOBALS['sistem_config'] = $this->system_config->load();
                                    $this->load->library('gmail');
                                    //$this->Gmail = new \leads\cls\Gmail();

                                    $result_message = $this->gmail->send_welcome
                                                        (
                                                            $datas['client_email'],
                                                            $datas['client_login'],
                                                            $GLOBALS['language']
                                                        );
                                    
                                    $this->user_model->set_session($cadastro_id,$this->session);
                                    
                                    $this->send_email_marketing($datas['client_login'], $datas['client_email'], $datas['client_telf']);
                                    $this->write_spreadsheet($datas['client_login'], $datas['client_email'], $datas['client_telf']);
                                    
                                    $result['success'] = true;
                                    $result['message'] = 'Signin success ';
                                    $result['resource'] = 'client';                                    
                                }else
                                {
                                    $result['success'] = false;
                                    $result['message'] = $this->T("Erro no cadastro", array(), $GLOBALS['language']);                        
                                    $result['resource'] = 'front_page';
                                }
                            }
                            else{
                                $result['success'] = false;
                                $result['message'] = $this->T("Verifique os dados proporcionados para concluir o cadastro.", array(), $GLOBALS['language']); 
                                $result['resource'] = 'front_page'; 
                            }
                        }
                        else{
                            $result['success'] = false;
                            $result['message'] = $this->T("Usuário existente no sistema, por favor faça o login.", array(), $GLOBALS['language']); 
                            $result['resource'] = 'front_page'; 
                        }
                    }
                    else{
                        $result['success'] = false;
                        $result['message'] = $this->T("Estrutura incorreta do e-mail.", array(), $GLOBALS['language']); 
                        $result['resource'] = 'front_page';
                    }
                }
                else{
                        $result['success'] = false;
                        $result['message'] = $this->T("O telefone só deve conter números!", array(), $GLOBALS['language']); 
                        $result['resource'] = 'front_page';
                    }
            }
            else{
                $result['success'] = false;
                $result['message'] = $this->T("Estrutura incorreta para o nome de usuário.", array(), $GLOBALS['language']); 
                $result['resource'] = 'front_page';
            }
        }
        else{
            $result['success'] = false;
            $result['message'] = $this->T("Verifique que nenhuma sessão no sistema está aberta.", array(), $GLOBALS['language']); 
            $result['resource'] = 'front_page';
        }
        echo json_encode($result);
    }
    
    public function signout() {           
        $this->load_language();
        if ($this->session->userdata('id')){            
            $this->load->model('class/user_model');
            $this->load->model('class/daily_work_model');        
            $datas = $this->input->post();
            $datas['client_login'] = $this->session->userdata('login');
            $datas['check_pass'] = false;    //check only by the user name
            
            $user_row = $this->user_model->verify_account($datas);
            
            if($user_row){
                $this->daily_work_model->delete_works_by_client($user_row['id']);
                $cancelamento = $this->user_model->cancel_user($user_row,time());
                if($cancelamento){
                    $this->load->model('class/system_config');                    
                    $GLOBALS['sistem_config'] = $this->system_config->load();
                    $this->load->library('gmail');                    
                    //$this->Gmail = new \leads\cls\Gmail();

                    $result_message = $this->gmail->send_client_cancel_status
                                        (
                                            $this->session->userdata('email'),
                                            $this->session->userdata('login'),
                                            $this->session->userdata('language')
                                        );
                    
                    $this->session->sess_destroy();
                    session_destroy();
                    $result['success'] = true;
                    $result['message'] = 'Signout success';
                    $result['resource'] = 'front_page';
                } 
                else{
                    $result['success'] = false;
                    $result['message'] = 'Signout wrong';
                    $result['resource'] = 'client_painel';
                }
            }
            else{
                $result['success'] = false;
                $result['message'] = $result['message'] = $this->T("Não existe nome de usuário/senha", array(), $GLOBALS['language']);
                $result['resource'] = 'front_page'; 
            }
        }
        else{
            $result['success'] = false;
            $result['message'] = $this->T("Não existe sessão ativa", array(), $GLOBALS['language']);
            $result['resource'] = 'front_page';            
        }
        echo json_encode($result);
        
    }
    
    public function login() {
        $datas = $this->input->post();
        $this->load_language($datas['language']);
        
        if (!$this->session->userdata('id')){
            $this->load->model('class/user_role'); 
            $this->load->model('class/user_model');
            //$datas = $this->input->post();
            if ($this->is_valid_user_name($datas['client_login']) || $this->is_valid_email($datas['client_login']) ){
                $datas['check_pass'] = true; 
                $type = 0;
                if($this->is_valid_email($datas['client_login']))
                    $type = 1;
                //verificar si se existe cliente        
                $user_row = $this->user_model->verify_account_email($datas, $type);
                //$verificar = true;
                if($user_row){      
                    /*if($datas['language'] != "PT" && $datas['language'] != "ES" && $datas['language'] != "EN")
                        $datas['language'] = $user_row['language'];            
                    if($user_row['language'] != $datas['language']){
                        $this->user_model->update_language($user_row['id'], $datas['language']);
                    }*/
                        
                    $this->user_model->set_session($user_row['id'],$this->session);
                   
                    $result['success'] = true;
                    $result['message'] = 'Login success';
                    if($user_row['role_id'] == user_role::CLIENT){
                        $result['resource'] = 'client';
                    }
                    else{
                        if($user_row['role_id'] == user_role::ADMIN)
                            $result['resource'] = 'admin';
                        else{
                            $result['resource'] = 'index';
                        }                            
                    }
                } else{
                    $result['success'] = false;
                    $result['message'] = $this->T("Não existe nome de usuário/senha", array(), $GLOBALS['language']);
                    $result['resource'] = 'index';
                }                
            }
            else{
                $result['success'] = false;
                $this->T("Estrutura incorreta para o nome de usuário.", array(), $GLOBALS['language']); 
                $result['resource'] = 'index';
            }
        
        }
        else {
            $result['success'] = false;
            $result['message'] = $this->T("Verifique que nenhuma sessão no sistema está aberta.", array(), $GLOBALS['language']); 
            $result['resource'] = 'index';
        }
        echo json_encode($result);
    }
    
    public function logout() {
        $this->load_language();
        if ($this->session->userdata('id')){            
            $this->load->model('class/user_model');
            $datas = $this->input->post();
            $datas['check_pass'] = false; 
            $datas['client_login'] = $this->session->userdata('login');
            
            //verificar si se existe cliente        
            $user_row = $this->user_model->verify_account($datas);
            
            if($user_row){    
                //$this->user_model->insert_washdog($this->session->userdata('id'),'CLOSING SESSION');
                $this->user_model->insert_watchdog($this->session->userdata('id'),'CLOSING SESSION');
                $this->session->sess_destroy();
                session_destroy();
                $result['success'] = true;
                $result['message'] = 'Logout success';
                $result['resource'] = 'index';
            } else{
                $result['success'] = false;
                $result['message'] = $this->T("Usuário inexistente.", array(), $GLOBALS['language']); 
                $result['resource'] = 'index';
            }
        }
        else{
            $result['success'] = false;
            $result['message'] = $this->T("Não existe sessão ativa", array(), $GLOBALS['language']);
            $result['resource'] = 'index';
        }
        echo json_encode($result);
    }
        
    public function add_temp_profile(){
        $this->load_language();
        $this->load->model('class/user_role');        
        $this->load->model('class/client_model');        
        
        if ($this->session->userdata('role_id')==user_role::CLIENT){
            $this->load->model('class/system_config');
            $GLOBALS['sistem_config'] = $this->system_config->load();
            $max_amount = $GLOBALS['sistem_config']->REFERENCE_PROFILE_AMOUNT;            
            $profiles_temp = $this->session->userdata('profiles_temp');
            $profiles_type_temp = $this->session->userdata('profiles_type_temp');
            $profiles_insta_temp = $this->session->userdata('profiles_insta_temp');
            
            if( count($profiles_insta_temp) < $max_amount ){
                $datas = $this->input->post();
                
                if($datas['profile_insta_temp'] > 0 && !$profiles_insta_temp[$datas['profile_insta_temp']]){
                    
                    $repeated_profiles = $this->client_model->check_for_repeated_profiles($this->session->userdata('id'), [$datas['profile_insta_temp'] => $datas['profile_insta_temp']], $datas['profile_type_temp']);
                    if(!$repeated_profiles){
                    
                        $profiles_temp[$datas['profile_insta_temp']] = $datas['profile_temp'];
                        $profiles_type_temp[$datas['profile_insta_temp']] = $datas['profile_type_temp'];
                        $profiles_insta_temp[$datas['profile_insta_temp']] = $datas['profile_insta_temp'];
                        $this->session->set_userdata('profiles_temp', $profiles_temp);
                        $this->session->set_userdata('profiles_type_temp', $profiles_type_temp);
                        $this->session->set_userdata('profiles_insta_temp', $profiles_insta_temp);

                        $result['success'] = true;            
                        $result['message'] = "Perfil adicionado";//'Profiles: '.$string_profiles;
                        $result['resource'] = 'client_painel';                        
                    }
                    else{
                        $result['success'] = false;            
                        $result['message'] = $this->T("Não se adicionou o perfil por já estar sendo usado em suas campanhas", array(), $GLOBALS['language']);
                        $result['resource'] = 'client_painel';
                    }
                }
                else{
                    $result['success'] = false;            
                    if($datas['profile_insta_temp'] > 0){
                        $result['message'] = $this->T("Este perfil já existe nesta campanha", array(), $GLOBALS['language']);
                    }
                    else
                    {
                        $result['message'] = $this->T("Deve fornecer um perfil", array(), $GLOBALS['language']);
                    }
                    $result['resource'] = 'client_painel';
                }
            }
            else{
                $result['success'] = false;            
                $result['message'] = $this->T("O número máximo de perfis permitido es ", array(), $GLOBALS['language']).$max_amount;
                $result['resource'] = 'client_painel';
            }
        }
        else{
            $result['success'] = false;
            $result['message'] = $this->T("Não existe sessão ativa", array(), $GLOBALS['language']);
            $result['resource'] = 'front_page';
        }
        echo json_encode($result);
    }
    
    public function html_for_new_campaing($campaing){        
        $html = '<div id = "campaing_'.$campaing['campaing_id'].'" class="fleft100 bk-silver camp camp-blue m-top20 center-xs">                                            
            <div class="col-md-2 col-sm-2 col-xs-12 m-top10">
                    <span class="bol fw-600 fleft100 ft-size15"><i></i> '.$this->T("Campanha", array(), $GLOBALS['language']).'</span>
                    <span id = "campaing_status_'.$campaing['campaing_id'].'" class="fleft100">'.ucfirst(strtolower($this->T("Criada", array(), $GLOBALS['language']))).'</span>
                    <span class="ft-size13">'.$this->T("Inicio", array(), $GLOBALS['language']).': '.date('d/m/Y', $campaing['created_date']).'</span>
                    <ul class="fleft75 bs2">
                        <li><a id="action_'.$campaing['campaing_id'].'" class = "mini_play pointer_mouse"><i id = "action_text_'.$campaing['campaing_id'].'" class="fa fa-play-circle"> '.$this->T("ATIVAR", array(), $GLOBALS['language']).'</i></a></li>                                                          
                    </ul>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-12">
                    <ul class="key m-top20-xs">
                        <div id = "profiles_view_'.$campaing['campaing_id'].'">';
                                            
                            foreach ($campaing['profile'] as $profile) {
                                if($profile){
                                    if($campaing['campaing_type_id'] == 1){
                                        $char_type = "";
                                    }
                                    if($campaing['campaing_type_id'] == 2){
                                        $char_type = "@";
                                    }
                                    if($campaing['campaing_type_id'] == 3){
                                        $char_type = "#";
                                    }
                                    $html .= '<li id = "___'.$profile['insta_id'].'"><span data-toggle="tooltip" data-placement="top" title="'.$profile['profile'].'">';
                                    $html .= $char_type.$this->reduce_profile($profile['profile']).'</span></li>';                                                                                        
                                    
                                }
                            }                                                                
                            
        $html .=         '</div>        
                    </ul>
            </div>
            <div class="col-md-3 col-sm-3 col-xs-12 m-top20-xs">
                    <span class="fleft100 ft-size12">Tipo: <span class="cl-green">'.$this->T($campaing['campaing_type_id_string'], array(), $GLOBALS['language']).'</span></span>
                    <span class="fleft100 fw-600 ft-size16"><label id="capt_'.$campaing['campaing_id'].'">'.$campaing['amount_leads'].'</label> '.$this->T("leads captados", array(), $GLOBALS['language']).'</span>
                    <span class="ft-size11 fw-600 m-top8 fleft100">'.$this->T("Gasto atual", array(), $GLOBALS['language']).': <br>'.$this->session->userdata('currency_symbol').' <label id="show_gasto_'.$campaing['campaing_id'].'">'.number_format((float)($campaing['total_daily_value'] - $campaing['available_daily_value'])/100, 2, '.', '').'</label> de <span class="cl-green">'.$this->session->userdata('currency_symbol').' <label id="show_total_'.$campaing['campaing_id'].'">'.number_format((float)$campaing['total_daily_value']/100, 2, '.', '').'</label></span></span>
            </div>';
        $html .= '<div id="divcamp_'.$campaing['campaing_id'].'" class="col-md-3 col-sm-3 col-xs-12 text-center m-top15">
                    <div class="col-md-6 col-sm-6 col-xs-6">                                                            
                            <a href="" class="cl-black">
                                <img src="'.base_url().'assets/img/down.png" alt="">
                                    <span class="fleft100 ft-size11 m-top8 fw-600">'.$this->T("Extrair leads", array(), $GLOBALS['language']).'</span>
                            </a>
                    </div>';
        $html .= '  <div class="col-md-6 col-sm-6 col-xs-6">';                            
        $html .= '           <div id="edit_campaing_'.$campaing['campaing_id'].'">';
        $html .= '              <a href="" class="cl-black edit_campaing" data-toggle="modal" data-id="editar_'.$campaing['campaing_id'].'" >';
        $html .= '                   <img src="'.base_url().'assets/img/editar.png" alt="">';
        $html .= '                      <span class="fleft100 ft-size11 m-top8 fw-600">'.$this->T("Editar", array(), $GLOBALS['language']).'</span>';
        $html .= '</a> </div> </div>';
        $html .=' </div></div>';
        return $html;
    }    
    
    public function delete_temp_profile(){
        $this->load_language();
        $this->load->model('class/user_role');        
        if ($this->session->userdata('role_id')==user_role::CLIENT){
            
            $profiles_temp = $this->session->userdata('profiles_temp');
            $profiles_type_temp = $this->session->userdata('profiles_type_temp');
            $profiles_insta_temp = $this->session->userdata('profiles_insta_temp');
            
            if( count($profiles_insta_temp) > 0 ){
                $datas = $this->input->post();
                
                unset($profiles_temp[$datas['profile_insta_temp']]);
                unset($profiles_type_temp[$datas['profile_insta_temp']]);
                unset($profiles_insta_temp[$datas['profile_insta_temp']]);
                
                $this->session->set_userdata('profiles_temp', $profiles_temp);
                $this->session->set_userdata('profiles_type_temp', $profiles_type_temp);
                $this->session->set_userdata('profiles_insta_temp', $profiles_insta_temp);

                /*foreach ($profiles_temp as $profile_temp) {
                    $string_profiles .= $profile_temp." ";
                }*/
                $result['success'] = true;            
                $result['message'] = 'Perfil eliminado';
                $result['resource'] = 'client_painel';
            }
            else{
                $result['success'] = false;            
                $result['message'] = $this->T("Nenhum perfil para eliminar", array(), $GLOBALS['language']);
                $result['resource'] = 'client_painel';
            }
        }
        else{
            $result['success'] = false;
            $result['message'] = $this->T("Não existe sessão ativa", array(), $GLOBALS['language']);
            $result['resource'] = 'front_page';
        }
        echo json_encode($result);
    }
    


    public function save_campaing() { 
        $this->load_language();
        $this->load->model('class/user_role');        
        if ($this->session->userdata('role_id')==user_role::CLIENT){
            $this->load->model('class/user_model');                                    
            $this->load->model('class/user_role');            
            $this->load->model('class/user_status');            
            $this->load->model('class/client_model');            
            $this->load->model('class/campaing_model');
            $this->load->model('class/campaing_status');            
            $this->load->model('class/profile_model');
            $this->load->model('class/profiles_status');
            
            if($this->session->userdata('status_id') != user_status::BLOCKED_BY_PAYMENT &&
               $this->session->userdata('status_id') != user_status::DELETED && 
               $this->session->userdata('status_id') != user_status::DONT_DISTURB){            
              
                $this->load->model('class/system_config');
                $GLOBALS['sistem_config'] = $this->system_config->load();
                $min_daily_value = $GLOBALS['sistem_config']->MINIMUM_DAILY_VALUE;            

                $datas = $this->input->post();
                $datas['check_pass'] = false;
                $datas['client_login'] = $this->session->userdata('login');

                if($this->is_valid_currency( $datas['total_daily_value'] && $datas['total_daily_value']>=$min_daily_value)){
                    if( $this->session->userdata('profiles_temp') && $this->same_type_of_profiles($this->session->userdata('profiles_type_temp'), $datas['campaing_type_id'])){
                        $user_row = $this->user_model->verify_account($datas);
                        //$ativou=0;
                        if($user_row){
                            //activate the user status for beginners                            
                            $update_user_result = true;
                            $time_add_campaing = time();
                            if($user_row['status_id'] == user_status::BEGINNER){                                
                                $update_user_result = $this->user_model->activate_client($this->session->userdata('id'), $time_add_campaing);
                                //$ativou=1;
                                
                            }
                            if($update_user_result){
                               //update the client table if it is necessary
                               
                               $client_row = $this->client_model->get_client_by_id($this->session->userdata('id'));
                               $insert_client_result = true;
                               if(!$client_row ){
                                   $insert_client_result = false;
                                   $client_row['user_id'] = $user_row['id'];
                                   $client_row['HTTP_SERVER_VARS'] = json_encode($_SERVER);
                                   $client_row['insta_id'] = NULL;
                                   $client_row['last_accesed'] = NULL;
                                   $client_row['observation'] = NULL;
                                   $insert_client_result = $this->client_model->insert_client($client_row);
                                }
                                if($insert_client_result){
                                    $profiles_temp = $this->session->userdata('profiles_temp');                                    
                                    $profiles_insta_temp = $this->session->userdata('profiles_insta_temp');                                    
                                    $repeated_profiles = $this->client_model->check_for_repeated_profiles($client_row['user_id'], $profiles_insta_temp, $datas['campaing_type_id']);
                                    if(!$repeated_profiles){
                                        //insert the campaing                            
                                        $campaing_row['client_id'] = $client_row['user_id'];
                                        $campaing_row['campaing_type_id'] = $datas['campaing_type_id'];
                                        $campaing_row['campaing_status_id'] = campaing_status::CREATED;
                                        $campaing_row['created_date'] = $time_add_campaing;
                                        $campaing_row['last_accesed'] = NULL;
                                        $campaing_row['client_objetive'] = $datas['client_objetive'];                                        
                                        $campaing_row['end_date'] = NULL;
                                        $campaing_row['total_daily_value']=$datas['total_daily_value'];
                                        $campaing_row['available_daily_value']=$datas['available_daily_value'];
                                        
                                        $id_campaing = $this->campaing_model->insert_campaing($campaing_row);
                                        
                                        if($id_campaing){
                                            //make the array of profiles                                    
                                            foreach($profiles_insta_temp as $profile_insta_temp){                                                
                                                //ver los otros campos despues
                                                $data_profiles[] = array ('campaing_id' => $id_campaing,
                                                                          'profile' => $profiles_temp[$profile_insta_temp],
                                                                          'insta_id' => $profile_insta_temp,
                                                                          'profile_status_id' => profiles_status::ACTIVE,
                                                                          'profile_status_date' => $time_add_campaing,
                                                                          'profile_type_id' => $datas['campaing_type_id'],
                                                                          'amount_leads' => 0,
                                                                          'amount_analysed_profiles' => 0                                                        
                                                                          );//despues ver el tipo de los perfiles
                                            }
                                            
                                            $result_profile = $this->campaing_model->insert_profiles($data_profiles);

                                            if($result_profile){
                                                $this->session->set_userdata('profiles_temp',NULL);
                                                $this->session->set_userdata('profiles_type_temp',NULL);
                                                $this->session->set_userdata('profiles_insta_temp',NULL);
                                                $result['success'] = true;
                                                $result['message'] = $this->T("Campanha criada", array(), $GLOBALS['language']);
                                                $result['resource'] = 'client_painel';
                                                $campaings = $this->client_model->load_campaings($this->session->userdata('id'), $id_campaing);
                                                $result['html'] = $this->html_for_new_campaing($campaings[0]);
                                            }
                                            else{
                                                $result['success'] = false;
                                                $result['message'] = $this->T("Erro inserindo o perfil na campanha", array(), $GLOBALS['language']);
                                                $result['resource'] = 'client_painel';
                                            }                                    
                                        }
                                        else{
                                            $result['success'] = false;
                                            $result['message'] = $this->T("Erro criando a campanha", array(), $GLOBALS['language']);
                                            $result['resource'] = 'client_painel';
                                        }
                                    }
                                    else{
                                        $result['success'] = false;
                                        $result['message'] = $this->T("Algunos dos perfis proporcionados estão se usando em outra de suas campanhas", array(), $GLOBALS['language']);
                                        $result['resource'] = 'client_painel';
                                    } 
                                }
                                else{
                                    $result['success'] = false;
                                    $result['message'] = $this->T("Erro ativando o cliente", array(), $GLOBALS['language']);
                                    $result['resource'] = 'client_painel';
                                }                        
                            }
                            else {
                                $result['success'] = false;
                                $result['message'] = $this->T("Erro atualizando o estado do usuario", array(), $GLOBALS['language']);
                                $result['resource'] = 'client_painel';
                            }                 
                        }
                        else{
                            $result['success'] = false;
                            $result['message'] = $this->T("Usuário inexistente.", array(), $GLOBALS['language']); 
                            $result['resource'] = 'front_page';
                        }
                    }
                    else{
                        if($this->session->userdata('profiles_temp')){
                            $result['success'] = false;
                            $result['message'] = $this->T("Os tipos da campanha e os perfis devem coincidir.", array(), $GLOBALS['language']); 
                            $result['resource'] = 'client_painel';
                        }
                        else{
                            $result['success'] = false;                            
                            $result['message'] = $this->T("Deve forncecer ao menos um perfil.", array(), $GLOBALS['language']); 
                            $result['resource'] = 'client_painel';
                        }
                    }
                }
                else{
                    $result['success'] = false;                    
                    $result['message'] = $this->T("O orçamento diário deve ser um valor monetario com até dois lugares decimais a partir de ", array(), $GLOBALS['language']).
                                        number_format((float)($min_daily_value/100), 2, '.', '').
                                        $this->T(" reais.", array(), $GLOBALS['language']);
                    $result['resource'] = 'client_painel';
                }
            }
            else{
                $result['success'] = false;
                $result['message'] = $this->T("Este usuário não pode fazer esta operação.", array(), $GLOBALS['language']); 
                $result['resource'] = 'client_painel';
            }
        }
        else{
            $result['success'] = false;
            $result['message'] = $this->T("Não existe sessão ativa", array(), $GLOBALS['language']);
            $result['resource'] = 'front_page';
        }
        echo json_encode($result);
    }
        
    public function get_campaing_data(){        
        $this->load_language();
        $this->load->model('class/user_role');  
        $this->load->model('class/user_status');
        $this->load->model('class/client_model');        
        
        if ($this->session->userdata('role_id')==user_role::CLIENT){
            if($this->session->userdata('status_id') != user_status::DELETED){
                $datas = $this->input->post();                
                $campaing_id = $datas['campaing_id'];
                
                $campaings = $this->client_model->load_campaings($this->session->userdata('id'), $campaing_id);
                
                $result['success'] = true;
                $result['message'] = "Campaing loaded";
                $result['resource'] = 'client_painel';
                $result['data'] = $campaings;                               
            }
            else{
                $result['success'] = false;
                $result['message'] = $this->T("Este usuário não pode accesar a este recurso.", array(), $GLOBALS['language']); 
                $result['resource'] = 'client_painel';
            }            
        }
        else{
            $result['success'] = false;
            $result['message'] = $this->T("Não existe sessão ativa", array(), $GLOBALS['language']);
            $result['resource'] = 'front_page';
        }
        echo json_encode($result);
    }
    
    public function get_campaings(){        
        $this->load_language();
        $this->load->model('class/user_role');  
        $this->load->model('class/user_status');
        $this->load->model('class/client_model');        
        
        if ($this->session->userdata('role_id')==user_role::CLIENT){
            if($this->session->userdata('status_id') != user_status::DELETED){
                $datas = $this->input->post();                
                
                $init_date = $this->session->userdata('init_day');
                $end_date = $this->session->userdata('end_day');
                
                if($datas["refresh"] != true){
                    $init_date = $datas['init_date'];
                    $end_date = $this->real_end_date($datas['end_date']);

                    if(!is_numeric($init_date))
                        $init_date = NULL;
                    if(!is_numeric($end_date))
                        $end_date = NULL;
                    if($init_date!=NULL && $end_date!=NULL && $init_date == $end_date){
                        $end_date = $init_date + 24*3600-1;
                    }

                    $this->session->set_userdata('init_day', $init_date);
                    $this->session->set_userdata('end_day', $end_date);
                }
                
                $campaings = $this->client_model->load_campaings($this->session->userdata('id'),NULL,$init_date, $end_date);
                
                $result['success'] = true;
                $result['message'] = "Campaings loaded";
                $result['resource'] = 'client_painel';
                $result['data'] = $campaings;                               
                
                if(!$init_date){
                    $init_date = $this->session->userdata('init_date');                
                }
                if(!$end_date){
                    $end_date = time();                
                }
                $result['date_interval'] = ['init_date' => $init_date, 'end_date' => $end_date];                               
            }
            else{
                $result['success'] = false;
                $result['message'] = $this->T("Este usuário não pode accesar a este recurso.", array(), $GLOBALS['language']); 
                $result['resource'] = 'client_painel';
            }            
        }
        else{
            $result['success'] = false;
            $result['message'] = $this->T("Não existe sessão ativa", array(), $GLOBALS['language']);
            $result['resource'] = 'front_page';
        }
        echo json_encode($result);
    }
    
    public function activate_campaing(){
        $this->load_language();
        $this->load->model('class/user_role');        
        $this->load->model('class/user_status');        
        $this->load->model('class/campaing_status');
        $this->load->model('class/campaing_model');
        $this->load->model('class/client_model');        
        $this->load->model('class/daily_work_model');        
        if ($this->session->userdata('role_id') == user_role::CLIENT){            
            if( $this->session->userdata('status_id') != user_status::BLOCKED_BY_PAYMENT &&
                $this->session->userdata('status_id') != user_status::PENDENT_BY_PAYMENT &&
                $this->session->userdata('status_id') != user_status::DELETED && 
                $this->session->userdata('status_id') != user_status::DONT_DISTURB){            
                
                $datas = $this->input->post();                
                $profiles_in_campaing = $this->client_model->get_campaings_and_profiles($this->session->userdata('id'), $datas['id_campaing']);
                
                //any record has the value 'campaing_status_id', so i use the index 0 :)
                if( $profiles_in_campaing[0]['campaing_status_id'] == campaing_status::CREATED ||
                    $profiles_in_campaing[0]['campaing_status_id'] == campaing_status::PAUSED){
                    
                    $previous_date = $profiles_in_campaing[0]['campaing_status_id'];
                    $results_update = $this->campaing_model->update_campaing_status($profiles_in_campaing[0]['campaing_id'], campaing_status::ACTIVE);
                        
                    if($profiles_in_campaing[0]['available_daily_value'] > 0){
                        $current_time = time();
                        foreach($profiles_in_campaing as $p){
                            if($p['profile_status_id'] == profiles_status::ACTIVE){
                                $datas_works[] = array( 'client_id' => $p['client_id'], 'campaing_id' => $p['campaing_id'], 'profile_id' => $p['id'], 'last_accesed'=>$current_time);
                                if($previous_state == campaing_status::CREATED){
                                    $this->campaing_model->update_profile_accesed($p['campaing_id'], $p['id'], $current_time-24*3600);
                                }
                            }
                        }
                        
                        $this->daily_work_model->insert_works($datas_works);
                    }
                    
                    if($results_update){                        
                        $result['success'] = true;
                        $result['message'] = $this->T("Campanha ativada!", array(), $GLOBALS['language']);
                        $result['resource'] = 'client_painel';                        
                    }
                    else{
                        $result['success'] = false;
                        $result['message'] = $this->T("Problema ativando a campanha", array(), $GLOBALS['language']);
                        $result['resource'] = 'client_painel';
                    }
                }
                else{
                    $result['success'] = false;
                    if( $profiles_in_campaing[0]['campaing_status_id'] == campaing_status::ACTIVE ){
                        $result['message'] = $this->T("Esta campanha já está ativa.", array(), $GLOBALS['language']);
                    }
                    else{
                        $result['message'] = $this->T("Esta campanha não pode ser ativada.", array(), $GLOBALS['language']);    
                    }
                    $result['resource'] = 'client_painel';
                }
            }
            else{
                $result['success'] = false;
                $result['message'] = $this->T("Este usuário não pode fazer esta operação.", array(), $GLOBALS['language']); 
                $result['resource'] = 'client_painel';
            }
        }
        else{
            $result['success'] = false;
            $result['message'] = $this->T("Não existe sessão ativa", array(), $GLOBALS['language']);
            $result['resource'] = 'front_page';
        }
        echo json_encode($result);
    }
    
    public function pause_campaing(){
        $this->load_language();
        $this->load->model('class/user_role');        
        $this->load->model('class/user_status');        
        $this->load->model('class/campaing_status');
        $this->load->model('class/campaing_model');
        $this->load->model('class/client_model');        
        $this->load->model('class/daily_work_model');        
        if ($this->session->userdata('role_id') == user_role::CLIENT){
            if( $this->session->userdata('status_id') != user_status::BLOCKED_BY_PAYMENT &&
                $this->session->userdata('status_id') != user_status::PENDENT_BY_PAYMENT &&
                $this->session->userdata('status_id') != user_status::DELETED && 
                $this->session->userdata('status_id') != user_status::DONT_DISTURB){            
                
                $datas = $this->input->post();
                $campaing_row = $this->client_model->client_get_campaings($this->session->userdata('id'),'*',$datas['id_campaing']);
                                
                if( $campaing_row['campaing_status_id'] == campaing_status::ACTIVE ){
                    
                    $results_update = $this->campaing_model->update_campaing_status($campaing_row['id'], campaing_status::PAUSED);
                    $this->daily_work_model->delete_works_by_campaing($campaing_row['id']);
                    
                    if($results_update){                        
                        $result['success'] = true;
                        $result['message'] = $this->T("Campanha pausada!", array(), $GLOBALS['language']);
                        $result['resource'] = 'client_painel';                        
                    }
                    else{
                        $result['success'] = false;
                        $result['message'] = $this->T("Problema pausando a campanha", array(), $GLOBALS['language']);
                        $result['resource'] = 'client_painel';
                    }
                }
                else{
                    $result['success'] = false;
                    if( $campaing_row['campaing_status_id'] == campaing_status::PAUSED ){
                        $result['message'] = $this->T("Esta campanha já está pausada.", array(), $GLOBALS['language']);
                    }
                    else{
                        $result['message'] = $this->T("Esta campanha não pode ser pausada.", array(), $GLOBALS['language']);    
                    }
                    $result['resource'] = 'client_painel';
                }
            }
            else{
                $result['success'] = false;
                $result['message'] = $this->T("Este usuário não pode fazer esta operação.", array(), $GLOBALS['language']); 
                $result['resource'] = 'client_painel';
            }
        }
        else{
            $result['success'] = false;
            $result['message'] = $this->T("Não existe sessão ativa", array(), $GLOBALS['language']);
            $result['resource'] = 'front_page';
        }
        echo json_encode($result);
    }
    
    public function cancel_campaing(){
        $this->load_language();
        $this->load->model('class/user_role');        
        $this->load->model('class/user_status');        
        $this->load->model('class/campaing_status');
        $this->load->model('class/campaing_model');
        $this->load->model('class/client_model');        
        $this->load->model('class/daily_work_model');        
        if ($this->session->userdata('role_id') == user_role::CLIENT){
            if( $this->session->userdata('status_id') != user_status::BLOCKED_BY_PAYMENT &&
                $this->session->userdata('status_id') != user_status::PENDENT_BY_PAYMENT &&
                $this->session->userdata('status_id') != user_status::DELETED && 
                $this->session->userdata('status_id') != user_status::DONT_DISTURB){            
                
                $datas = $this->input->post();
                $campaing_row = $this->client_model->client_get_campaings($this->session->userdata('id'),'*', $datas['id_campaing']);
                                
                if($campaing_row && $campaing_row['campaing_status_id'] != campaing_status::DELETED ){
                    
                    $this->daily_work_model->delete_works_by_campaing($campaing_row['id']);
                    $result_cancel = $this->campaing_model->cancel_campaing($campaing_row['id'],time());
                    
                    if($result_cancel){                        
                        $result['success'] = true;
                        $result['message'] = $this->T("Campanha terminada", array(), $GLOBALS['language']);
                        $result['resource'] = 'client_painel';
                    }
                    else{
                        $result['success'] = false;
                        $result['message'] = $this->T("Problema terminando a campanha", array(), $GLOBALS['language']);
                        $result['resource'] = 'client_painel';
                    }
                }
                else{
                    $result['success'] = false;
                    if($campaing_row['campaing_status_id'] == campaing_status::DELETED ){                    
                        $result['message'] = $this->T("Esta campanha já está cancelada.", array(), $GLOBALS['language']);
                    }
                    else{
                        $this->T("Esta campanha não pode ser cancelada.", array(), $GLOBALS['language']);    
                    }
                    $result['resource'] = 'client_painel';
                }
            }
            else{
                $result['success'] = false;
                $result['message'] = $this->T("Este usuário não pode fazer esta operação.", array(), $GLOBALS['language']); 
                $result['resource'] = 'client_painel';
            }
        }
        else{
            $result['success'] = false;
            $result['message'] = $this->T("Não existe sessão ativa", array(), $GLOBALS['language']);
            $result['resource'] = 'front_page';
        }
        echo json_encode($result);
    }
    
    public function add_profile(){
        $this->load_language();
        $this->load->model('class/user_role');        
        $this->load->model('class/campaing_model');        
        $this->load->model('class/user_status');        
        $this->load->model('class/campaing_status');
        $this->load->model('class/profiles_status');        
        $this->load->model('class/client_model');        
        $this->load->model('class/daily_work_model');        
        if ($this->session->userdata('role_id')==user_role::CLIENT ){
            if( $this->session->userdata('status_id') != user_status::BLOCKED_BY_PAYMENT &&
                $this->session->userdata('status_id') != user_status::PENDENT_BY_PAYMENT &&
                $this->session->userdata('status_id') != user_status::DELETED && 
                $this->session->userdata('status_id') != user_status::DONT_DISTURB){
                
                $this->load->model('class/system_config');
                $GLOBALS['sistem_config'] = $this->system_config->load();
                $max_amount = $GLOBALS['sistem_config']->REFERENCE_PROFILE_AMOUNT;            

                $datas = $this->input->post();
                $profile = $datas['profile'];
                $profile_insta = $datas['insta_id'];
                $profile_type = $datas['profile_type'];
                $id_campaing = $datas['id_campaing'];
                $campaing_row = $this->campaing_model->get_campaing($id_campaing);

                if($profile && $profile_type == $campaing_row['campaing_type_id']){
                    if( $this->campaing_model->verify_campaing_client($this->session->userdata('id'), $id_campaing) ){

                        $profiles_in_campaing = $this->campaing_model->get_profiles($id_campaing,'insta_id');

                        if( count($profiles_in_campaing) < $max_amount){

                            $repeated_profiles = $this->client_model->check_for_repeated_profiles($this->session->userdata('id'),  [$profile_insta => $profile_insta], $campaing_row['campaing_type_id']);
                            if(!$repeated_profiles){
                                $data_profile = array ( 'campaing_id' => $id_campaing,
                                                        'profile' => $profile,
                                                        'insta_id' => $profile_insta,
                                                        'profile_status_id' => profiles_status::ACTIVE,
                                                        'profile_status_date' => time(),
                                                        'profile_type_id' => $profile_type,
                                                        'amount_leads' => 0,
                                                        'amount_analysed_profiles' => 0
                                                         );
                                $old_profile_row = $this->campaing_model->get_delete_profile($id_campaing, $profile_insta);
                                if(!$old_profile_row){
                                    $id_profile = $this->campaing_model->insert_profile($data_profile);
                                    //add in daily work for active campaing
                                    if( $campaing_row['campaing_status_id'] == campaing_status::ACTIVE && $campaing_row['available_daily_value'] > 0){////////
                                        $this->daily_work_model->insert_work(array( 'client_id' => $this->session->userdata('id'), 
                                                                                    'campaing_id' => $id_campaing, 
                                                                                    'profile_id' => $id_profile,
                                                                                    'last_accesed' => time() ) );
                                    }                            
                                    if($id_profile){                                                                   
                                        $result['success'] = true;
                                        $result['message'] = 'Profile added';
                                        $result['resource'] = 'client_painel';
                                    }
                                    else{
                                        $result['success'] = false;
                                        $result['message'] = $this->T("Erro inserindo o perfil", array(), $GLOBALS['language']);
                                        $result['resource'] = 'client_painel';
                                    }
                                }
                                else{
                                    $result['success'] = false;
                                    $result['message'] = $this->T("Este perfil já existe nesta campanha", array(), $GLOBALS['language']);
                                    $result['resource'] = 'client_painel';
                                    $result['old_profile'] = $old_profile_row;
                                }
                            }
                            else{
                                $result['success'] = false;            
                                $result['message'] = $this->T("Não se adicionou o perfil por já estar sendo usado em suas campanhas", array(), $GLOBALS['language']);
                                $result['resource'] = 'client_painel';
                            }
                        }
                        else{
                            $result['success'] = false;            
                            $result['message'] = $this->T("O número máximo de perfis permitido es ", array(), $GLOBALS['language']).$max_amount;
                            $result['resource'] = 'client_painel';
                        }
                    }
                    else{
                        $result['success'] = false;            
                        $result['message'] = $this->T("Esta campanha não pertençe a este usuário.", array(), $GLOBALS['language']);    
                        $result['resource'] = 'client_painel';
                    }
                }
                else{
                    $result['success'] = false; 
                    if($profile){
                        $result['message'] = $this->T("Os tipos da campanha e os perfis devem coincidir.", array(), $GLOBALS['language']); 
                    }
                    else
                    {
                        $result['message'] = $this->T("Deve fornecer um perfil", array(), $GLOBALS['language']);
                    }
                    $result['resource'] = 'client_painel';
                }
            }
            else
            {
                $result['success'] = false;
                $result['message'] = $this->T("Este usuário não pode fazer esta operação.", array(), $GLOBALS['language']); 
                $result['resource'] = 'client_painel';
            }
        }
        else{
            $result['success'] = false;
            $result['message'] = $this->T("Não existe sessão ativa", array(), $GLOBALS['language']);
            $result['resource'] = 'front_page';
        }
        echo json_encode($result);
    }
    
    public function add_existing_profile(){
        $this->load_language();
        $this->load->model('class/user_role');        
        $this->load->model('class/user_status');        
        $this->load->model('class/campaing_model');        
        $this->load->model('class/campaing_status');        
        $this->load->model('class/client_model');        
        $this->load->model('class/daily_work_model');        
        if ($this->session->userdata('role_id')==user_role::CLIENT){
            if( $this->session->userdata('status_id') != user_status::BLOCKED_BY_PAYMENT &&
                $this->session->userdata('status_id') != user_status::PENDENT_BY_PAYMENT &&
                $this->session->userdata('status_id') != user_status::DELETED && 
                $this->session->userdata('status_id') != user_status::DONT_DISTURB){
                $this->load->model('class/system_config');
                $GLOBALS['sistem_config'] = $this->system_config->load();
                $max_amount = $GLOBALS['sistem_config']->REFERENCE_PROFILE_AMOUNT;            

                $datas = $this->input->post();
                $old_profile_row = $datas['old_profile'];                  
                $id_campaing = $old_profile_row['campaing_id'];

                if( $this->campaing_model->verify_campaing_client($this->session->userdata('id'), $id_campaing) ){

                    $profiles_in_campaing = $this->campaing_model->get_profiles($id_campaing,'insta_id');

                    if( count($profiles_in_campaing) < $max_amount){
                            $campaing_row = $this->campaing_model->get_campaing($id_campaing);
                            $old_profile_row['delted'] = 0;      
                            $result_update = $this->campaing_model->update_profile($id_campaing, $old_profile_row);
                            //add in daily work for active campaing
                            if( $campaing_row['campaing_status_id'] == campaing_status::ACTIVE && $campaing_row['available_daily_value'] > 0){
                                $this->daily_work_model->insert_work(array( 'client_id' => $this->session->userdata('id'), 
                                                                            'campaing_id' => $id_campaing, 
                                                                            'profile_id' => $old_profile_row['id'],
                                                                            'last_accesed' => time()) );
                            }                            
                            if($result_update){                            
                                $result['success'] = true;
                                $result['message'] = 'Profile added';
                                $result['resource'] = 'client_painel';
                            }
                            else{
                                $result['success'] = false;
                                $result['message'] = $this->T("Erro inserindo o perfil na campanha", array(), $GLOBALS['language']);
                                $result['resource'] = 'client_painel';
                            }                    
                    }
                    else{
                        $result['success'] = false;            
                        $result['message'] = $this->T("O número máximo de perfis permitido es ", array(), $GLOBALS['language']).$max_amount;
                        $result['resource'] = 'client_painel';
                    }
                }
                else{
                    $result['success'] = false;            
                    $result['message'] = $this->T("Esta campanha não pertençe a este usuário.", array(), $GLOBALS['language']);    
                    $result['resource'] = 'client_painel';
                }  
            }
            else{
                $result['success'] = false;
                $result['message'] = $this->T("Este usuário não pode fazer esta operação.", array(), $GLOBALS['language']); 
                $result['resource'] = 'client_painel';
            }
        }
        else{
            $result['success'] = false;
            $result['message'] = $this->T("Não existe sessão ativa", array(), $GLOBALS['language']);
            $result['resource'] = 'front_page';
        }
        echo json_encode($result);
    }
    
    public function delete_profile(){
        $this->load_language();
        $this->load->model('class/user_role');        
        $this->load->model('class/campaing_model');        
        $this->load->model('class/client_model');        
        $this->load->model('class/user_status');        
        $this->load->model('class/campaing_status');        
        $this->load->model('class/daily_work_model');        
        if ($this->session->userdata('role_id')==user_role::CLIENT){            
            if( $this->session->userdata('status_id') != user_status::BLOCKED_BY_PAYMENT &&
                $this->session->userdata('status_id') != user_status::PENDENT_BY_PAYMENT &&
                $this->session->userdata('status_id') != user_status::DELETED && 
                $this->session->userdata('status_id') != user_status::DONT_DISTURB){
                
                $datas = $this->input->post();                
                $profile_insta = $datas['insta_id'];
                $id_campaing = $datas['id_campaing'];
                $campaing_row = $this->campaing_model->get_campaing($id_campaing);

                if( $this->campaing_model->verify_campaing_client($this->session->userdata('id'), $id_campaing) ){

                    $profiles_in_campaing = $this->campaing_model->get_profiles($id_campaing,'insta_id');

                    if( count($profiles_in_campaing) > 1 ){                    
                        $profile_row = $this->campaing_model->get_profile($id_campaing, $profile_insta);
                        $result_profile = $this->campaing_model->delete_profile($id_campaing, $profile_insta);
                        //delete from daily work for active campaing                    
                        if( $campaing_row['campaing_status_id'] == campaing_status::ACTIVE ){
                            $this->daily_work_model->delete_work(array( 'client_id' => $this->session->userdata('id'), 
                                                                        'campaing_id' => $id_campaing, 
                                                                        'profile_id' => $profile_row['id'],
                                                                        'last_accesed' => time() ) );
                        }
                        if($result_profile){                            
                            $result['success'] = true;
                            $result['message'] = 'Profile deleted';
                            $result['resource'] = 'client_painel';
                        }
                        else{
                            if($profile_row){
                                $result['success'] = false;
                                $result['message'] = $this->T("Erro eliminando o perfil", array(), $GLOBALS['language']);
                                $result['resource'] = 'client_painel';
                            }else{
                                $result['success'] = false;
                                $result['message'] = $this->T("Perfil não encontrado na campanha", array(), $GLOBALS['language']);
                                $result['resource'] = 'client_painel';
                            }
                        }
                    }
                    else{
                        $result['success'] = false;            
                        $result['message'] = $this->T("A campanha deve ter ao menos um perfil", array(), $GLOBALS['language']);
                        $result['resource'] = 'client_painel';
                    }               
                }
                else{
                    $result['success'] = false;            
                    $result['message'] = $this->T("Esta campanha não pertençe a este usuário.", array(), $GLOBALS['language']);   
                    $result['resource'] = 'client_painel';
                }
            }
            else{
                $result['success'] = false;            
                $result['message'] = $this->T("Este usuário não pode fazer esta operação.", array(), $GLOBALS['language']); 
                $result['resource'] = 'client_painel';
            }
        }
        else{
            $result['success'] = false;
            $result['message'] = $this->T("Não existe sessão ativa", array(), $GLOBALS['language']);
            $result['resource'] = 'front_page';
        }
        echo json_encode($result);
    }
    
    public function update_daily_value(){
        $this->load_language();
        $this->load->model('class/user_role');        
        $this->load->model('class/campaing_model');        
        $this->load->model('class/client_model');        
        $this->load->model('class/user_status');        
        $this->load->model('class/campaing_status');        
        $this->load->model('class/daily_work_model');        
        if ($this->session->userdata('role_id')==user_role::CLIENT){            
            if( $this->session->userdata('status_id') != user_status::BLOCKED_BY_PAYMENT &&
                $this->session->userdata('status_id') != user_status::PENDENT_BY_PAYMENT &&
                $this->session->userdata('status_id') != user_status::DELETED && 
                $this->session->userdata('status_id') != user_status::DONT_DISTURB){
                
                $this->load->model('class/system_config');
                $GLOBALS['sistem_config'] = $this->system_config->load();
                $min_daily_value = $GLOBALS['sistem_config']->MINIMUM_DAILY_VALUE;            
                
                $datas = $this->input->post();
                $new_daily_value = $datas['new_daily_value'];
                
                if($this->is_valid_currency( $new_daily_value && $new_daily_value >= $min_daily_value)){
                    $id_campaing = $datas['id_campaing'];
                    $campaing_row = $this->campaing_model->get_campaing($id_campaing);
                    $new_available_value = $new_daily_value - ($campaing_row['total_daily_value'] - $campaing_row['available_daily_value']);

                    if( $this->campaing_model->verify_campaing_client($this->session->userdata('id'), $id_campaing) ){
                        if( $campaing_row['campaing_status_id'] == campaing_status::PAUSED || $campaing_row['campaing_status_id'] == campaing_status::CREATED){
                            $result_update = $this->campaing_model->update_daily_value($id_campaing, $new_daily_value, $new_available_value);
                            if($result_update){
                                $result['success'] = true;            
                                $result['message'] = $this->T("Se modificou o orçamento diário da campanha", array(), $GLOBALS['language']);
                                $result['resource'] = 'client_painel';
                            }
                            else{
                                $result['success'] = false;            
                                $result['message'] = $this->T("Não se modificou o orçamento diário da campanha", array(), $GLOBALS['language']);
                                $result['resource'] = 'client_painel';
                            }
                        }
                        else{
                                $result['success'] = false;            
                                $result['message'] = $this->T("Para modificar o orçamento diário a campanha não pode estar ativa ou cancelada", array(), $GLOBALS['language']);
                                $result['resource'] = 'client_painel';
                            }
                    }
                    else{
                        $result['success'] = false;            
                        $result['message'] = $this->T("Esta campanha não pertençe a este usuário.", array(), $GLOBALS['language']);    
                        $result['resource'] = 'client_painel';
                    }
                }
                else{
                    $result['success'] = false;                    
                    $result['message'] = $this->T("O orçamento diário deve ser um valor monetario com até dois lugares decimais a partir de ", array(), $GLOBALS['language']).
                                        number_format((float)($min_daily_value/100), 2, '.', '').
                                        $this->T(" reais.", array(), $GLOBALS['language']);
                    $result['resource'] = 'client_painel'; 
                }
            }
            else{
                $result['success'] = false;            
                $result['message'] = $this->T("Este usuário não pode fazer esta operação.", array(), $GLOBALS['language']); 
                $result['resource'] = 'client_painel';
            }
        }
        else{
            $result['success'] = false;
            $result['message'] = $this->T("Não existe sessão ativa", array(), $GLOBALS['language']);
            $result['resource'] = 'front_page';
        }
        echo json_encode($result);
    }
    
    public function to_csv($values){
	// We can use one implode for the headers =D
	//$csv = implode(",", array_keys(reset($values))) . PHP_EOL;
	$csv = "";
	foreach ($values as $row) {
            foreach ($row as $elem){
                    $csv .= $elem.",";	    
            }
	    $csv .= PHP_EOL;
	}
	return $csv;
    }
    
    function str_putcsv2($data) {
            # Generate CSV data from array
            $fh = fopen('php://temp', 'rw'); # don't create a file, attempt
                                             # to use memory instead

            # write out the headers
            fputcsv($fh, array_keys(current($data)));

            # write out the data
            foreach ( $data as $row ) {
                    fputcsv($fh, $row);
            }
            rewind($fh);
            $csv = stream_get_contents($fh);
            fclose($fh);

            return $csv;
    }
    
    public function convert_from_latin1_to_utf8_recursively($dat)
   {
      if (is_string($dat)) {
         return mb_convert_encoding($dat, 'UTF-8', 'UTF-8');//utf8_encode($dat);
      } elseif (is_array($dat)) {
         $ret = [];
         foreach ($dat as $i => $d) $ret[ $i ] = $this->convert_from_latin1_to_utf8_recursively($d);

         return $ret;
      } elseif (is_object($dat)) {
         foreach ($dat as $i => $d) $dat->$i = $this->convert_from_latin1_to_utf8_recursively($d);

         return $dat;
      } else {
         return $dat;
      }
   }
  
    public function file_leads(){
        $this->load_language();
        $this->load->model('class/user_role');        
        $this->load->model('class/user_status');        
        $this->load->model('class/campaing_model');        
        if ($this->session->userdata('role_id') == user_role::CLIENT &&
            !$this->session->userdata('admin')){            
            if( $this->session->userdata('status_id') != user_status::BEGINNER &&
                $this->session->userdata('status_id') != user_status::DELETED && 
                $this->session->userdata('status_id') != user_status::DONT_DISTURB){            
                
                $datas = $this->input->get();
                
                $profile = $datas['profile'];
                $id_campaing = $datas['id_campaing'];
                if(isset($id_campaing) && !is_numeric($id_campaing))
                    $id_campaing = NULL;
                $init_date = $datas['init_date'];
                $end_date = $this->real_end_date($datas['end_date']);  
                
                if($init_date!=NULL && $end_date!=NULL && $init_date == $end_date){
                    $end_date = $init_date + 24*3600-1;
                }
                
                //parse_str($datas['info_to_get'], $info_to_get);
                //$info_to_get = $datas['info_to_get'];
                $info_to_get = NULL;
                if($datas['info_to_get'])                
                    $info_to_get = explode(',', $datas['info_to_get']);
                
                ////$campaing_row = $this->campaing_model->get_campaing($id_campaing);
                
                if( $id_campaing==NULL || $this->campaing_model->verify_campaing_client($this->session->userdata('id'), $id_campaing) ){
                    $profile_row = $this->campaing_model->get_profile($id_campaing, $profile);
                    $max_id = 0;
                    $result_sql = TRUE;
                    $first_result = TRUE;
                    while ($result_sql){
                        $result_sql = $this->campaing_model->get_leads_limit( $this->session->userdata('id'),
                                                                        $id_campaing,
                                                                        $profile_row['id'],
                                                                        $init_date,
                                                                        $end_date,
                                                                        $info_to_get,
                                                                        $max_id
                                                                        );                    
                        $result_sql = $this->convert_from_latin1_to_utf8_recursively($result_sql);

                        if($first_result && count($result_sql) > 0){
                            $first_result = FALSE;
                            $filename = 'leads_'.date('Ymd', $init_date).'_'.date('Ymd', $end_date).'.csv'; 
                            header("Content-Description: File Transfer"); 
                            header("Content-Disposition: attachment; filename=$filename"); 
                            header("Content-Type: application/csv; ");

                            // file creation 
                            $file = fopen('php://output', 'w');

                            fputcsv($file, array_keys(current($result_sql)));                            
                        }
                        
                        foreach ($result_sql as $key=>$line){ 
                          fputcsv($file,$line);                          
                        }
                    }
                    if(!$first_result)
                        fclose($file); 
                    exit;
                
                
                    $result['success'] = true;
                    $result['message'] = '';
                    $result['resource'] = 'leads_view';
                    
                }
                else{
                    $result['success'] = false;            
                    $result['message'] = $this->T("Esta campanha não pertençe a este usuário.", array(), $GLOBALS['language']);    
                    $result['resource'] = 'client_painel';
                }
            }
            else{
                $result['success'] = false;
                $result['message'] = $this->T("Seu estado atual no sistema não permite a descarga de leads.", array(), $GLOBALS['language']);
                $result['resource'] = 'front_page';
            }
        }
        else{
            $result['success'] = false;
            $result['message'] = $this->T("Não existe sessão ativa", array(), $GLOBALS['language']);
            $result['resource'] = 'front_page';
        } 
        $this->load->view('user_view');
    }
    
    public function get_leads_client(){
        $this->load_language();
        $this->load->model('class/user_role');        
        $this->load->model('class/user_status');        
        $this->load->model('class/campaing_model');        
        if ($this->session->userdata('role_id') == user_role::CLIENT &&
            !$this->session->userdata('admin')){            
            if( $this->session->userdata('status_id') != user_status::BEGINNER &&
                $this->session->userdata('status_id') != user_status::DELETED && 
                $this->session->userdata('status_id') != user_status::DONT_DISTURB){            
                
                $datas = $this->input->post();
                
                $profile = $datas['profile'];
                $id_campaing = $datas['id_campaing'];
                if(isset($id_campaing) && !is_numeric($id_campaing))
                    $id_campaing = NULL;
                $init_date = $datas['init_date'];
                $end_date = $this->real_end_date($datas['end_date']);  
                
                if($init_date!=NULL && $end_date!=NULL && $init_date == $end_date){
                    $end_date = $init_date + 24*3600-1;
                }
                                
                $info_to_get = $datas['info_to_get'];
                
                if( $id_campaing==NULL || $this->campaing_model->verify_campaing_client($this->session->userdata('id'), $id_campaing) ){
                    $profile_row = $this->campaing_model->get_profile($id_campaing, $profile);
                    $num_leads = 0;
                    $num_leads = $this->campaing_model->get_num_leads( $this->session->userdata('id'),
                                                                        $id_campaing,
                                                                        $profile_row['id'],
                                                                        $init_date,
                                                                        $end_date,
                                                                        $info_to_get                                                                        
                                                                        );                    
                    if($num_leads > 0){
                        $result['success'] = true;
                        $result['message'] = "";
                    }
                    else{
                        $result['success'] = false;
                        $result['message'] = $this->T("Você não possui leads pagos no periodo solicitado", array(), $GLOBALS['language']);                                                    
                    }
                }
                else{
                    $result['success'] = false;            
                    $result['message'] = $this->T("Esta campanha não pertençe a este usuário.", array(), $GLOBALS['language']);    
                    $result['resource'] = 'client_painel';
                }
            }
            else{
                $result['success'] = false;
                $result['message'] = $this->T("Seu estado atual no sistema não permite a descarga de leads.", array(), $GLOBALS['language']);
                $result['resource'] = 'front_page';
            }
        }
        else{
            $result['success'] = false;
            $result['message'] = $this->T("Não existe sessão ativa", array(), $GLOBALS['language']);
            $result['resource'] = 'front_page';
        }
        
        $json = json_encode($result);        
        echo $json;
    }
    
    public function get_leads_campaing_old(){/*obsoleta*/
        $this->load_language();
        $this->load->model('class/user_role');        
        $this->load->model('class/user_status');        
        $this->load->model('class/campaing_model');        
        if ($this->session->userdata('role_id') == user_role::CLIENT &&
            !$this->session->userdata('admin')){                      
            if( $this->session->userdata('status_id') != user_status::BEGINNER &&
                $this->session->userdata('status_id') != user_status::DELETED && 
                $this->session->userdata('status_id') != user_status::DONT_DISTURB){            
                
                $datas = $this->input->post();
                $profile = $datas['profile'];
                $id_campaing = $datas['id_campaing'];
                if(isset($id_campaing) && !is_numeric($id_campaing))
                    $id_campaing = NULL;
                $init_date = $datas['init_date'];
                $end_date = $this->real_end_date($datas['end_date']);  
                
                if($init_date!=NULL && $end_date!=NULL && $init_date == $end_date){
                    $end_date = $init_date + 24*3600-1;
                }
                
                parse_str($datas['info_to_get'], $info_to_get);
                ////$campaing_row = $this->campaing_model->get_campaing($id_campaing);
                
                if( $id_campaing==NULL || $this->campaing_model->verify_campaing_client($this->session->userdata('id'), $id_campaing) ){
                    $profile_row = $this->campaing_model->get_profile($id_campaing, $profile);
                    $result_sql = $this->campaing_model->get_leads( $this->session->userdata('id'),
                                                                    $id_campaing,
                                                                    $profile_row['id'],
                                                                    $init_date,
                                                                    $end_date,
                                                                    $info_to_get
                                                                    );                    
                    $result_sql = $this->convert_from_latin1_to_utf8_recursively($result_sql);
                    $out = $this->str_putcsv2($result_sql);                    

                    $result['success'] = true;
                    $result['message'] = '';
                    $result['resource'] = 'leads_view';
                    $result['file'] = count($result_sql)>0?$out:NULL;
                }
                else{
                    $result['success'] = false;            
                    $result['message'] = $this->T("Esta campanha não pertençe a este usuário.", array(), $GLOBALS['language']);    
                    $result['resource'] = 'client_painel';
                }
            }
            else{
                $result['success'] = false;
                $result['message'] = $this->T("Seu estado atual no sistema não permite a descarga de leads.", array(), $GLOBALS['language']);
                $result['resource'] = 'front_page';
            }
        }
        else{
            $result['success'] = false;
            $result['message'] = $this->T("Não existe sessão ativa", array(), $GLOBALS['language']);
            $result['resource'] = 'front_page';
        }
        
        $json = json_encode($result);        
        echo $json;
//        $msg = json_last_error_msg();
//        if ($json)
//            echo $json;
//        else
//            echo json_last_error_msg();
//        
    }
    
    public function add_credit_card(){
        $this->load_language();
        $this->load->model('class/user_role');        
        $this->load->model('class/credit_card_model');        
        if ($this->session->userdata('role_id')==user_role::CLIENT){            
            $datas = $this->input->post();
            $message_error = $this->errors_in_credit_card_datas($datas['credit_card_name'],
                                                                $datas['credit_card_number'],
                                                                $datas['credit_card_cvc'], 
                                                                $datas['credit_card_exp_month'], 
                                                                $datas['credit_card_exp_year']);
            if(!$message_error){
                $datas['client_id'] = $this->session->userdata('id');
                $datas['payment_order'] = NULL; //revisar despues

                $old_credit_card = $this->credit_card_model->get_credit_card($this->session->userdata('id'));
                if(!$old_credit_card){

                    $result_insert = $this->credit_card_model->insert_credit_card($datas);

                    if($result_insert){
                        $result['success'] = true;
                        $result['message'] = $this->T("Adicionado cartão de crédito", array(), $GLOBALS['language']);
                        $result['resource'] = 'client_page';
                    }
                    else{
                        $result['success'] = false;
                        $result['message'] = $this->T("Erro adicionando o cartão de crédito", array(), $GLOBALS['language']);
                        $result['resource'] = 'client_page';
                    }
                }
                else{
                    $result['success'] = false;
                    $result['message'] = 'Existing credit card';
                    $result['resource'] = 'client_page';
                    $result['existing_card'] = 1;//$old_credit_card['id'];
                    $result['payment_order'] = $old_credit_card['payment_order'];
                }
            }
            else
            {
                $result['success'] = false;
                $result['message'] = $message_error;
                $result['resource'] = 'client_page';
            }
        }
        else{            
            $result['success'] = false;
            $result['message'] = $this->T("Não existe sessão ativa", array(), $GLOBALS['language']);
            $result['resource'] = 'front_page';
        }
        echo json_encode($result);
    }  
    
    public function update_credit_card(){
        $this->load_language();
        $this->load->model('class/user_role');        
        $this->load->model('class/credit_card_model');        
        if ($this->session->userdata('role_id')==user_role::CLIENT){            
            $datas = $this->input->post();
            $message_error = $this->errors_in_credit_card_datas($datas['credit_card_name'],
                                                                $datas['credit_card_number'],
                                                                $datas['credit_card_cvc'], 
                                                                $datas['credit_card_exp_month'], 
                                                                $datas['credit_card_exp_year']);
            if(!$message_error){
                $datas['client_id'] = $this->session->userdata('id');

                $result_update = $this->credit_card_model->update_credit_card($datas);

                if($result_update){
                    $result['success'] = true;
                    $result['message'] = $this->T("Atualizados os dados do cartão!", array(), $GLOBALS['language']);
                    $result['resource'] = 'client_page';
                }
                else{
                    $result['success'] = false;
                    $result['message'] = $this->T("Erro atualizando os dados do cartão!", array(), $GLOBALS['language']);
                    $result['resource'] = 'client_page';
                }
            }
            else
            {
                $result['success'] = false;
                $result['message'] = $this->T($message_error, array(), $GLOBALS['language']);
                $result['resource'] = 'client_page';
            }
        }
        else{            
            $result['success'] = false;
            $result['message'] = $this->T("Não existe sessão ativa", array(), $GLOBALS['language']);
            $result['resource'] = 'front_page';
        }
        echo json_encode($result);
    }  
    
    public function add_bank_ticket(){
        $this->load_language();
        $this->load->model('class/user_role');        
        $this->load->model('class/bank_ticket_model');        
        $this->load->model('class/user_model');        
        if ($this->session->userdata('role_id')==user_role::CLIENT){            
            
            $datas = $this->input->post();
            $message_error = $this->errors_in_bank_ticket($datas['name_in_ticket'],
                                                            $datas['cpf'],
                                                            $datas['cep'], 
                                                            $datas['emission_money_value'], 
                                                            $datas['house_number'],             
                                                            $datas['street_address'],             
                                                            $datas['neighborhood_address'],             
                                                            $datas['municipality_address'],             
                                                            $datas['state_address']);             

            if(!$message_error){
                $num = $this->bank_ticket_model->get_number_order();                
                
                if($num){
                    $datas['client_id'] = $this->session->userdata('id');
                    $datas['document_number'] = $num['value'];
                    //generar y obtener el link
                    $payment_data['AmountInCents']=$datas['emission_money_value'];
                    $payment_data['DocumentNumber']=$datas['document_number']; //'3';
                    $payment_data['OrderReference']=$datas['document_number']; //'3';
                    $payment_data['id']=$datas['client_id']; 
                    $payment_data['name']=$datas['name_in_ticket'];
                    $payment_data['cpf']=$datas['cpf']; 
                    $payment_data['cep']=$datas['cep'];
                    $payment_data['street_address']=$datas['street_address'];
                    $payment_data['house_number']=$datas['house_number'];
                    $payment_data['neighborhood_address']=$datas['neighborhood_address'];
                    $payment_data['municipality_address']=$datas['municipality_address'];
                    $payment_data['state_address']=$datas['state_address'];   
                    
                    $resp = $this->check_mundipagg_boleto($payment_data);
                    if($resp['success']){
                        $datas['ticket_link'] = $resp['ticket_url'];
                        $datas['ticket_order_key'] = $resp['ticket_order_key'];
                        
                        $result_insert = $this->bank_ticket_model->insert_bank_ticket($datas);

                        if($result_insert){                            
                            $this->load->model('class/system_config');                    
                            $GLOBALS['sistem_config'] = $this->system_config->load();
                            $this->load->library('gmail');                    
                            //$this->Gmail = new \leads\cls\Gmail();
                            
                            $result_message = $this->gmail->send_client_ticket_success(
                                                                $this->session->userdata('email'),
                                                                $this->session->userdata('login'),
                                                                $datas['ticket_link'],
                                                                $this->session->userdata('language')
                                                            );
                            
                            $result['success'] = true;
                            $result['message'] = $this->T("Seu boleto bancário foi gerado com sucesso! Consulte seu e-mail e siga as indicações.", array(), $GLOBALS['language']);
                            $result['resource'] = 'client_page';
                            $result['link'] = $datas['ticket_url'];
                        }
                        else{
                            $result['success'] = false;
                            $result['message'] = $this->T("Erro adicionando dados do boleto bancário", array(), $GLOBALS['language']);
                            $result['resource'] = 'client_page';
                        }                
                    }
                    else{
                        $result['success'] = false;
                        $result['message'] = $this->T("Erro criando o boleto bancário, por favor tente mais tarde", array(), $GLOBALS['language']);
                        $result['resource'] = 'client_page';
                    }
                }
                else{
                    $result['success'] = false;
                    $result['message'] = $this->T("Erro criando o boleto bancário, por favor tente de novo", array(), $GLOBALS['language']);
                    $result['resource'] = 'client_page';
                }
            }
            else
            {
                $result['success'] = false;
                $result['message'] = $message_error;
                $result['resource'] = 'client_page';
            }
        }
        else{            
            $result['success'] = false;
            $result['message'] = $this->T("Não existe sessão ativa", array(), $GLOBALS['language']);
            $result['resource'] = 'front_page';
        }
        echo json_encode($result);
    } 
    
    public function add_credit_card_cupom(){
        $this->load_language();
        $this->load->model('class/user_role');        
        $this->load->model('class/credit_card_model');        
        if ($this->session->userdata('role_id')==user_role::CLIENT){            
            $datas = $this->input->post();
            if(!is_numeric($datas['option']) || $datas['option'] < 1 || $datas['option'] > 4){
                $datas['option'] = 2;
            }
            $prepay = ['1' => 10000, '2' => 50000, '3' => 100000, '4' => 200000];
            
            $message_error = $this->errors_in_credit_card_datas($datas['credit_card_name'],
                                                                $datas['credit_card_number'],
                                                                $datas['credit_card_cvc'], 
                                                                $datas['credit_card_exp_month'], 
                                                                $datas['credit_card_exp_year']);
            if(!$message_error){
                $datas['client_id'] = $this->session->userdata('id');
                $datas['payment_order'] = NULL; //revisar despues
                $datas['amount'] = $prepay[ $datas['option'] ]; //revisar despues

                $old_credit_card_cupom = $this->credit_card_model->get_credit_card_cupom($this->session->userdata('id'));
                if(!$old_credit_card_cupom){

                    $result_insert = $this->credit_card_model->insert_credit_card_cupom($datas);

                    if($result_insert){
                        $result['success'] = true;
                        $result['message'] = $this->T("Solicitado pré-pago com cartão de crédito. Por favor, espere o nosso e-mail de confirmação.", array(), $GLOBALS['language']);
                        $result['resource'] = 'client_page';
                    }
                    else{
                        $result['success'] = false;
                        $result['message'] = $this->T("Erro solicitando o pré-pago com cartão de crédito", array(), $GLOBALS['language']);
                        $result['resource'] = 'client_page';
                    }
                }
                else{
                    $result['success'] = false;
                    $result['message'] = $this->T("Você solicitou previamente um pré-pago que ainda não foi confirmado. Espere a confirmação para poder solicitar o próximo!", array(), $GLOBALS['language']);
                    $result['resource'] = 'client_page';                    
                }
            }
            else
            {
                $result['success'] = false;
                $result['message'] = $message_error;
                $result['resource'] = 'client_page';
            }
        }
        else{            
            $result['success'] = false;
            $result['message'] = $this->T("Não existe sessão ativa", array(), $GLOBALS['language']);
            $result['resource'] = 'front_page';
        }
        echo json_encode($result);
    }
    
    public function update_language(){        
        $this->load->model('class/user_role');        
        $this->load->model('class/user_model');        
        
        $datas = $this->input->post();
        $language = $datas['new_language'];

        if ($this->session->userdata('id')){            
            if($language != "PT" && $language != "ES" && $language != "EN"){
                $language = $this->session->userdata('language');
            }

            $result_update = $this->user_model->update_language($this->session->userdata('id'), $language);

            if($result_update){
                $result['success'] = true;
                $result['message'] = $this->T("Linguagem cambiada!", array(), $GLOBALS['language']);
                $result['resource'] = 'client_page';
            }
            else{
                $result['success'] = false;
                $result['message'] = $this->T("Não se cambiou a linguagem!", array(), $GLOBALS['language']);
                $result['resource'] = 'client_page';
            }            
        }
        else{
            
            if($language != "PT" && $language != "ES" && $language != "EN"){
                $language = $GLOBALS['language'];
            }
            else{
                $GLOBALS['language'] = $language;
            }
            
            $result['success'] = true;
            $result['message'] = $this->T("Não existe sessão ativa", array(), $GLOBALS['language']);
            $result['resource'] = 'front_page';
        }
        echo json_encode($result);
    }    
    
    public function message() {        
        $this->load->model('class/system_config');                    
        $GLOBALS['sistem_config'] = $this->system_config->load();
        $this->load->library('gmail');                    
        
        $language=$this->input->get();
        if(isset($language['language']))
            $param['language']=$language['language'];
        else
            $param['language'] = $GLOBALS['sistem_config']->LANGUAGE;
        $param['SERVER_NAME'] = $GLOBALS['sistem_config']->SERVER_NAME;
        $GLOBALS['language']=$param['language'];
        $datas = $this->input->post();
        $result = $this->gmail->send_client_contact_form($datas['name'], $datas['email'], $datas['message'], $datas['company'], $datas['telf']);
        if ($result['success']) {
            $result['message'] = $this->T('Mensagem enviada, agradecemos seu contato', array(), $GLOBALS['language']);
        }
        echo json_encode($result);
    }
    
    public function T($token, $array_params=NULL, $lang=NULL) {
        if(!$lang){
            $this->load->model('class/system_config');
            $GLOBALS['sistem_config'] = $this->system_config->load();
            
            if(isset($language['language']))
                $param['language']=$language['language'];
            else
                $param['language'] = $GLOBALS['sistem_config']->LANGUAGE;
            //$param['SERVER_NAME'] = $GLOBALS['sistem_config']->SERVER_NAME;        
            $GLOBALS['language']=$param['language'];
            $lang=$param['language'];
        }
        $this->load->model('class/translation_model');
        $text = $this->translation_model->get_text_by_token($token,$lang);
        $N = count($array_params);
        for ($i = 0; $i < $N; $i++) {
            $text = str_replace('@' . ($i + 1), $array_params[$i], $text);
        }
        return $text;
    }
        
    public function get_cep_datas(){
        if ($this->session->userdata('id')){            
            $cep = $this->input->post()['cep'];
            $datas = file_get_contents('https://viacep.com.br/ws/'.$cep.'/json/');
            if(!$datas || strpos($datas,'erro')>0){
                $response['success']=false;
            } else{
                $response['success']=true;
            }
            $response['datas'] = json_decode($datas);
        }
        else{
            $response['success']=false;
        }
        echo json_encode($response);
    }
    
    public function check_mundipagg_credit_card($datas) {
        $this->is_ip_hacker();
        $this->load->model('class/system_config');
        $GLOBALS['sistem_config'] = $this->system_config->load();
        require_once $_SERVER['DOCUMENT_ROOT'] . '/leads/src/application/libraries/Payment.php';
        $Payment = new \leads\cls\Payment();
        $payment_data['credit_card_number'] = $datas['credit_card_number'];
        $payment_data['credit_card_name'] = $datas['credit_card_name'];
        $payment_data['credit_card_exp_month'] = $datas['credit_card_exp_month'];
        $payment_data['credit_card_exp_year'] = $datas['credit_card_exp_year'];
        $payment_data['credit_card_cvc'] = $datas['credit_card_cvc'];
        $payment_data['amount_in_cents'] = $datas['amount_in_cents'];
        $payment_data['pay_day'] = time();        
        $bandeira = $this->detectCardType($payment_data['credit_card_number']);        
        if ($bandeira)
            $response = $Payment->create_payment($payment_data);
        else
            $response = array("message" => $this->T("Confira seu número de cartão e se está certo entre em contato com o atendimento.", array(), $GLOBALS['language']));
        
        return $response;
    }    

    public function check_mundipagg_boleto($datas) {
        $this->is_ip_hacker();
        $this->load->model('class/system_config');
        $GLOBALS['sistem_config'] = $this->system_config->load();
        require_once $_SERVER['DOCUMENT_ROOT'] . '/leads/src/application/libraries/Payment.php';
        $Payment = new \leads\cls\Payment();
        
        $payment_data['AmountInCents']=$datas['AmountInCents'];
        $payment_data['DocumentNumber']=$datas['DocumentNumber'];
        $payment_data['OrderReference']=$datas['OrderReference'];
        $payment_data['id']=$datas['pk'];
        $payment_data['name']=$datas['name'];
        $payment_data['cpf']=$datas['cpf'];        
        $payment_data['cep']=$datas['cep'];
        $payment_data['street_address']=$datas['street_address'];
        $payment_data['house_number']=$datas['house_number'];
        $payment_data['neighborhood_address']=$datas['neighborhood_address'];
        $payment_data['municipality_address']=$datas['municipality_address'];
        $payment_data['state_address']=$datas['state_address'];   
        
        return $Payment->create_boleto_payment( $payment_data);        
    }
    
    
    
    public function detectCardType($num) {
        $re = array(
            "visa" => "/^4[0-9]{12}(?:[0-9]{3})?$/",
            "mastercard" => "/^5[1-5][0-9]{14}$/",
            "amex" => "/^3[47][0-9]{13}$/",
            "discover" => "/^6(?:011|5[0-9]{2})[0-9]{12}$/",
            "diners" => "/^3[068]\d{12}$/",
            "elo" => "/^((((636368)|(438935)|(504175)|(451416)|(636297))\d{0,10})|((5067)|(4576)|(4011))\d{0,12})$/",
            "hipercard" => "/^(606282\d{10}(\d{3})?)|(3841\d{15})$/"
        );

        if (preg_match($re['visa'], $num)) {
            return 'Visa';
        } else if (preg_match($re['mastercard'], $num)) {
            return 'Mastercard';
        } else if (preg_match($re['amex'], $num)) {
            return 'Amex';
        } else if (preg_match($re['discover'], $num)) {
            return 'Discover';
        } else if (preg_match($re['diners'], $num)) {
            return 'Diners';
        } else if (preg_match($re['elo'], $num)) {
            return 'Elo';
        } else if (preg_match($re['hipercard'], $num)) {
            return 'Hipercard';
        } else {
            return false;
        }
    }
    
    public function is_ip_hacker(){
        $IP_hackers= array(
            '191.176.169.242', '138.0.85.75', '138.0.85.95', '177.235.130.16', '191.176.171.14', '200.149.30.108', '177.235.130.212', '66.85.185.69',
            '177.235.131.104', '189.92.238.28', '168.228.88.10', '201.86.36.209', '177.37.205.210', '187.66.56.220', '201.34.223.8', '187.19.167.94',
            '138.0.21.188', '168.228.84.1', '138.36.2.18', '201.35.210.135', '189.71.42.124', '138.121.232.245', '151.64.57.146', '191.17.52.46', '189.59.112.125',
            '177.33.7.122', '189.5.107.81', '186.214.241.146', '177.207.99.29', '170.246.230.138', '201.33.40.202', '191.53.19.210', '179.212.90.46', '177.79.7.202',
            '189.111.72.193', '189.76.237.61', '177.189.149.249', '179.223.247.183', '177.35.49.40', '138.94.52.120', '177.104.118.22', '191.176.171.14', '189.40.89.248',
            '189.89.31.89', '177.13.225.38',  '186.213.69.159', '177.95.126.121', '189.26.218.161', '177.193.204.10', '186.194.46.21', '177.53.237.217', '138.219.200.136',
            '177.126.106.103', '179.199.73.251', '191.176.171.14', '179.187.103.14', '177.235.130.16', '177.235.130.16', '177.235.130.16', '177.47.27.207'
            );
        if(in_array($_SERVER['REMOTE_ADDR'],$IP_hackers)){
            die('Error IP: Sua solicitação foi negada. Por favor, contate nosso atendimento');
        }
    }
    
    public function validate_promotional_code($datas){
        $this->load->model('class/payments_model');
        if(isset($datas['promotional_code'])){
            if(trim($datas['promotional_code'])==''){
                $response['success']=true;
                $response['valid_code']=0;
                return $response;
            }
                       
            $code['FIRST-SIGN-IN-BUY'] = 100;
            $code['53C0ND-S1GN-1N-8UY'] = 2;
            $code['TENR-SIGN-IN-BUY'] = 500;
            
            $count_code = $code[$datas['promotional_code']];
            
            if($count_code){
                //contar si la cantidad en la base de datos es menor que 100 personas usando
                $cnt =$this->payments_model->getPromotionalCodeFrequency($datas['promotional_code']);
                if($cnt < $count_code){
                    
                    $response['success']=true;
                    $response['valid_code']=1;
                }
                else{
                    $response['success']=false;
                    $response['message']=$this->T('Código promocional esgotado', array(), $this->session->userdata('language'));
                    $response['valid_code']=0;
                }
            }else{
                $response['success']=false;
                $response['message']=$this->T('Código promocional errado', array(), $this->session->userdata('language'));
                $response['valid_code']=0;
            }            
        }
        return $response;
    }
    
    public function send_email_marketing($name, $email, $phone){
        
        $postData = array(
            'id' => 180608,
           'pid' => 6378385,
           'list_id' => 180608,
           'provider' => 'leadlovers',
           'name' => $name,
           'email' => $email,
           'phone' => $phone,           
           'source' => "https://dumbu.pro/leads/src/"           
        );        
        
        $postFields = http_build_query($postData);
        
        $url = 'https://leadlovers.com/Pages/Index/180608';
        $handler = curl_init();
        curl_setopt($handler, CURLOPT_URL, $url);  
        curl_setopt($handler, CURLOPT_POST,true);  
        curl_setopt($handler, CURLOPT_RETURNTRANSFER,true);  
        curl_setopt($handler, CURLOPT_POSTFIELDS, $postFields);  
        $response = curl_exec($handler);                
        curl_close($handler);
    }
    
    public function save_cupom50(){
        $this->load_language();
        $this->load->model('class/user_role');        
        $this->load->model('class/bank_ticket_model');        
        $this->load->model('class/cupom_model');        
        if ($this->session->userdata('role_id')==user_role::CLIENT){            
            $datas = $this->input->post();
            $cupom_code = trim($datas['code']);
            $document = substr($cupom_code, 9);
            $cupom_code = substr($cupom_code, 0, 8);            
            
            if($cupom_code){
                $type_cupom = $this->cupom_model->get_cupom($cupom_code);
                $multiplicator = 100 / $type_cupom['percent'];
                if($type_cupom){                    
                    $used_code = $this->cupom_model->is_used_cupom($this->session->userdata('id'), $type_cupom['id']);                    
                    
                    if(!$used_code){                        
                        $payed_ticket = $this->bank_ticket_model->get_ticket_by_order($this->session->userdata('id'), $document, $type_cupom['value']);
                        
                        if($payed_ticket){
                            $this->cupom_model->add_cupom($this->session->userdata('id'), $type_cupom['id']); 
                            $result_payed_ticket = $this->bank_ticket_model->multiplicate_ticket_value($this->session->userdata('id'), $payed_ticket['id'], $payed_ticket['emission_money_value']*$multiplicator);
                            $result['success'] = true;
                            $result['message'] = $this->T("Código de cupom guardado corretamente.", array(), $GLOBALS['language']);
                            $result['resource'] = 'client_page';
                        }
                        else{
                            $result['success'] = false;
                            $result['message'] = $this->T("Para usar este cupom você deve gerar e pagar um boleto de ", array(), $GLOBALS['language'])." ".($type_cupom['value']/100)." reais";
                            $result['resource'] = 'client_page';
                        }
                    }
                    else{
                        $result['success'] = false;
                        $result['message'] = $this->T("Você já usou este cupom", array(), $GLOBALS['language']);
                        $result['resource'] = 'client_page';
                    }
                }
                else{
                    $result['success'] = false;
                    $result['message'] = $this->T("Deve fornecer um código válido!", array(), $GLOBALS['language']);
                    $result['resource'] = 'client_page';                    
                }
            }
            else
            {
                $result['success'] = false;
                $result['message'] = $this->T("Deve fornecer um código válido!", array(), $GLOBALS['language']);
                $result['resource'] = 'client_page';
            }
        }
        else{            
            $result['success'] = false;
            $result['message'] = $this->T("Não existe sessão ativa", array(), $GLOBALS['language']);
            $result['resource'] = 'front_page';
        }
        echo json_encode($result);
    }

    public function write_spreadsheet($name, $email, $phone){
        
        $postFields = "";
        $postFields .=  "entry.2027130557=".htmlspecialchars($name).
                        "&entry.739069715=".htmlspecialchars($email).
                        "&entry.517560190=".htmlspecialchars($phone);
        
        //We will use the URL
        //$url = "https://sheets.googleapis.com/v4/spreadsheets/" . $spreadsheetId . "/append/Sheet1";        
        $url = 'https://docs.google.com/forms/d/e/1FAIpQLSccLqdm_VoYpeAMrWqOGHBwTB-DL9SutKUr-yASdpRw8fKKbA/formResponse';
        //Start cURL
        $ch = curl_init($url);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);  
        curl_setopt($ch, CURLOPT_POST,true);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);  
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);  
        $response = curl_exec($ch);                
        curl_close($ch);
        $error = curl_error($ch);        
        curl_close($ch);
    }
//------------desenvolvido para DUMBU-FOLLOW-UNFOLLOW-------------------

    public function language() {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/system_config.php';
        $GLOBALS['sistem_config'] = new dumbu\cls\system_config();
        $param['language'] = $GLOBALS['sistem_config']->LANGUAGE;
        $this->load->library('recaptcha');
        $this->load->view('user_view', $param);
    }

    public function purchase() {
        $datas = $this->input->get();
        if(isset($datas['access_token'])){
            $this->load->model('class/client_model');
            $client = $this->client_model->get_client_by_access_token($datas['access_token']); 
            if(count($client)){
                $this->client_model->update_client($client['user_id'], 
                        array('access_token' =>'---***###!!!---'.$client['user_id']));
                $this->user_model->set_sesion($client['user_id'], $this->session);
                $this->user_model->insert_washdog($client['user_id'],'REDIRECTED FROM TICKET-BANK EMAIL LINK');
                $this->user_model->insert_watchdog($client['user_id'],'REDIRECTED FROM TICKET-BANK EMAIL LINK');
            } else{
                header("Location: ".base_url());
                die();
            }
        }
        if ($this->session->userdata('id')){
            //$datas = $this->input->get();
            $this->load->model('class/user_model');
            $this->user_model->insert_washdog($this->session->userdata('id'),'SUCCESSFUL PURCHASE');            
            $this->user_model->insert_watchdog($this->session->userdata('id'),'SUCCESSFUL PURCHASE');            
            require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/system_config.php';
            $GLOBALS['sistem_config'] = new dumbu\cls\system_config();
            $datas['user_id'] = $this->session->userdata('id');
            $datas['profiles'] = $this->create_profiles_datas_to_display();            
            $datas['SERVER_NAME'] = $GLOBALS['sistem_config']->SERVER_NAME;            
            if(isset($datas['language'])&& $datas['language']!=''){
                 $GLOBALS['language'] =  $datas['language'];
            }
            else{
                $datas['language'] = $GLOBALS['sistem_config']->LANGUAGE;
                 $GLOBALS['language'] = $GLOBALS['sistem_config']->LANGUAGE;
            }
            $datas['Afilio_UNIQUE_ID'] = $this->session->userdata('id');
            $query='SELECT * FROM plane WHERE id='.$this->session->userdata('plane_id');
            $result = $this->user_model->execute_sql_query($query);
            $datas['Afilio_order_price']=$result[0]['initial_val'];
            $datas['Afilio_total_value']=$result[0]['normal_val'];
            $datas['Afilio_product_id']= $this->session->userdata('plane_id');            
            $datas['client_login_profile'] = $this->session->userdata('login');
            $datas['client_email']= $this->session->userdata('email');
            $this->client_model->Create_Followed($this->session->userdata('id'));
            $this->load->view('purchase_view', $datas);
        }else
            echo 'Access error';
    }

    public function client_dumbu_old() {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/system_config.php';
        $GLOBALS['sistem_config'] = new dumbu\cls\system_config();
        $this->load->model('class/user_role');
        $this->load->model('class/user_model');
        $this->load->model('class/client_model');
        $this->load->model('class/user_status');
        $status_description = array(1 => 'ATIVO', 2 => 'DESABILITADO', 3 => 'INATIVO', 4 => '', 5 => '', 6 => 'ATIVO'/* 'PENDENTE' */, 7 => 'NÂO INICIADO', 8 => '', 9 => 'INATIVO', 10 => 'LIMITADO');
        if (isset($this->session) && $this->session->userdata('role_id') == user_role::CLIENT) {
            $language=$this->input->get();           
            if(isset($language['language'])){
                 $GLOBALS['language']=$language['language'];
                $this->user_model->set_language_of_client($this->session->userdata('id'),$language);
            }
            else
                 $GLOBALS['language']=$this->user_model->get_language_of_client($this->session->userdata('id'))['language'];
            $datas1['SERVER_NAME'] = $GLOBALS['sistem_config']->SERVER_NAME;
            $datas1['WHATSAPP_PHONE'] = $GLOBALS['sistem_config']->WHATSAPP_PHONE;
            $datas1['SCRIPT_VERSION'] = $GLOBALS['sistem_config']->SCRIPT_VERSION;
            require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/Robot.php';
            $this->Robot = new \dumbu\cls\Robot();
            $datas1['MAX_NUM_PROFILES'] = $GLOBALS['sistem_config']->REFERENCE_PROFILE_AMOUNT;
            //$my_profile_datas = $this->Robot->get_insta_ref_prof_data($this->session->userdata('login'));
            $my_profile_datas = $this->Robot->get_insta_ref_prof_data_from_client(json_decode($this->session->userdata('cookies')), $this->session->userdata('login'));
            if(isset($my_profile_datas->profile_pic_url))
                $datas1['my_img_profile'] = $my_profile_datas->profile_pic_url;
            else
                $datas1['my_img_profile']="Blocked";
            //$datas1['dumbu_id'] = $this->session->userdata('id');


            $sql = "SELECT * FROM clients WHERE clients.user_id='" . $this->session->userdata('id') . "'";
            $init_client_datas = $this->user_model->execute_sql_query($sql);

            $sql = "SELECT * FROM reference_profile WHERE client_id='" . $this->session->userdata('id') . "' AND type='0'";
            $reference_profile_used= $this->user_model->execute_sql_query($sql);
            $datas1['reference_profile_used'] =count($reference_profile_used);

            $sql = "SELECT * FROM reference_profile WHERE client_id='" . $this->session->userdata('id') . "' AND type='1'";
            $geolocalization_used= $this->user_model->execute_sql_query($sql);
            $datas1['geolocalization_used'] =count($geolocalization_used);

            $sql = "SELECT SUM(follows) as followeds FROM reference_profile WHERE client_id = " . $this->session->userdata('id')." AND type='0'";
            $amount_followers_by_reference_profiles = $this->user_model->execute_sql_query($sql);
            $amount_followers_by_reference_profiles =(string)$amount_followers_by_reference_profiles[0]["followeds"];
            $datas1['amount_followers_by_reference_profiles'] = $amount_followers_by_reference_profiles;

            $sql = "SELECT SUM(follows) as followeds FROM reference_profile WHERE client_id = " . $this->session->userdata('id')." AND type='1'";
            $amount_followers_by_geolocalization = $this->user_model->execute_sql_query($sql);
            $amount_followers_by_geolocalization =(string)$amount_followers_by_geolocalization[0]["followeds"];
            $datas1['amount_followers_by_geolocalization'] = $amount_followers_by_geolocalization;


            if(isset($my_profile_datas->follower_count))
                $datas1['my_actual_followers'] = $my_profile_datas->follower_count;
            else
                $datas1['my_actual_followers']="Blocked";            

            if(isset($my_profile_datas->following))
               $datas1['my_actual_followings'] = $my_profile_datas->following;
            else
                $datas1['my_actual_followings']="Blocked";

            $datas1['my_sigin_date'] = $this->session->userdata('init_date');
            date_default_timezone_set('Etc/UTC');
            $datas1['today'] = date('d-m-Y', time());
            $datas1['my_initial_followers'] = $init_client_datas[0]['insta_followers_ini'];
            $datas1['my_initial_followings'] = $init_client_datas[0]['insta_following'];            

            $datas1['my_login_profile'] = $this->session->userdata('login');
            $datas1['unfollow_total'] = $this->session->userdata('unfollow_total');
            $datas1['autolike'] = $this->session->userdata('autolike');
            $datas1['play_pause'] = (int) $init_client_datas[0]['paused'];
            $datas1['plane_id'] = $this->session->userdata('plane_id');
            $datas1['all_planes'] = $this->client_model->get_all_planes();
            $datas1['currency'] = $GLOBALS['sistem_config']->CURRENCY;
            $datas1['language'] =  $GLOBALS['language'];

            $daily_report = $this->get_daily_report($this->session->userdata('id'));
            $datas1['followings'] = $daily_report['followings'];
            $datas1['followers']  = $daily_report['followers'];

            if ($this->session->userdata('status_id') == user_status::VERIFY_ACCOUNT || $this->session->userdata('status_id') == user_status::BLOCKED_BY_INSTA) {
                $insta_login = $this->is_insta_user($this->session->userdata('login'), $this->session->userdata('pass'),'false');
                if ($insta_login['status'] === 'ok') {
                    if ($insta_login['authenticated']) {
                        //1. actualizar estado a ACTIVO
                        $this->user_model->update_user($this->session->userdata('id'), array(
                            'status_id' => user_status::ACTIVE));
                        //2. actualizar la cookies
                        if ($insta_login['insta_login_response']) {
                            $this->client_model->update_client($this->session->userdata('id'), array(
                                'cookies' => json_encode($insta_login['insta_login_response'])));
                            //3. crearle trabajo si ya tenia perfiles de referencia y si todavia no tenia trabajo insertado
                            $active_profiles = $this->client_model->get_client_active_profiles($this->session->userdata('id'));
                            $N = count($active_profiles);
                            for ($i = 0; $i < $N; $i++) {
                                $sql = 'SELECT * FROM daily_work WHERE reference_id=' . $active_profiles[$i]['id'];
                                $response = count($this->user_model->execute_sql_query($sql));
                                if (!$response && $active_profiles[$i]['end_date']!=='NULL')
                                    $this->client_model->insert_profile_in_daily_work($active_profiles[$i]['id'], $insta_login['insta_login_response'], $i, $active_profiles, $this->session->userdata('to_follow'));
                            }
                        }
                        //4. actualizar la sesion
                        $this->user_model->set_sesion($this->session->userdata('id'), $this->session, $insta_login['insta_login_response']);
                    } else {
                        if ($insta_login['message'] == 'checkpoint_required' || $insta_login['message'] == '') {
                            //actualizo su estado
                            $this->user_model->update_user($this->session->userdata('id'), array(
                                'status_id' => user_status::VERIFY_ACCOUNT));
                            //eliminar su trabajo si contrasenhas son diferentes
                            $active_profiles = $this->client_model->get_client_active_profiles($this->session->userdata('id'));
                            $N = count($active_profiles);
                            for ($i = 0; $i < $N; $i++) {
                                $this->client_model->delete_work_of_profile($active_profiles[$i]['id']);
                            }
                            //establezco la sesion
                            $this->user_model->set_sesion($this->session->userdata('id'), $this->session);
                            $datas1['verify_account_datas'] = $insta_login;
                        } else {
                            $this->user_model->update_user($this->session->userdata('id'), array(
                            'status_id' => user_status::BLOCKED_BY_INSTA));
                            $this->user_model->set_sesion($this->session->userdata('id'), $this->session);
                        }
                    }
                } else
                if ($insta_login['status'] === 'fail') {
                    ;
                }
            }
            $datas1['status'] = array('status_id' => $this->session->userdata('status_id'), 'status_name' => $status_description[$this->session->userdata('status_id')]);
            $datas1['profiles'] = $this->create_profiles_datas_to_display();
            $data['head_section1'] = $this->load->view('responsive_views/client/client_header_painel', '', true);
            $data['body_section1'] = $this->load->view('responsive_views/client/client_body_painel', $datas1, true);
            $data['body_section4'] = $this->load->view('responsive_views/user/users_talkme_painel', '', true);
            $data['body_section_cancel'] = $this->load->view('responsive_views/client/client_cancel_painel', '', true);
            $data['body_section5'] = $this->load->view('responsive_views/user/users_end_painel', '', true);
            $this->load->view('client_view', $data);
        } else {
            echo "Session can't be stablished";
            $this->display_access_error();
        }
    }

    public function user_do_login($datas=NULL) {
        $this->load->model('class/user_role');          
        $login_by_client=false;
        if(!isset($datas)){
            $datas = $this->input->post();
            $language=$this->input->get();
            $login_by_client=true;
        }

        require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/system_config.php';
        $GLOBALS['sistem_config'] = new dumbu\cls\system_config();
        if(isset($language['language']))
            $param['language']=$language['language'];
        else
            $param['language'] = $GLOBALS['sistem_config']->LANGUAGE;    
        $param['SERVER_NAME'] = $GLOBALS['sistem_config']->SERVER_NAME;        
        $GLOBALS['language']=$param['language'];

        $query = "SELECT * FROM users WHERE "
                  ."login= '".$datas['user_login']."' and pass = '".$datas['user_pass']."' and role_id = '".user_role::CLIENT."'";
        $real_status = $this->get_real_status_of_user($query);

        if($real_status==2 || $datas['force_login']=='true'){
            $result = $this->user_do_login_second_stage($datas,$GLOBALS['language']);                
        }else{                
            $result['message'] = $this->T('Credenciais erradas', array(), $GLOBALS['language']);
            $result['message_force_login'] = $this->T('Seguro que são suas credencias de IG', array(), $GLOBALS['language']);
            $result['cause'] = 'force_login_required';
            $result['authenticated'] = false;
        }            
        if($login_by_client)
            echo json_encode($result);
        else
            return $result;
    }

    public function get_real_status_of_user($query){            
        $this->load->model('class/user_status');
        $this->load->model('class/user_model');            
        $user = $this->user_model->execute_sql_query($query);            
        $N = count($user);
        $real_status = 0; //No existe, eliminado o inactivo
        $index = 0;
        for ($i = 0; $i < $N; $i++) {
            if ($user[$i]['status_id'] == user_status::BEGINNER) {
                $real_status = 1; //Beginner
                $index = $i;
                break;
            } else
            if ($user[$i]['status_id'] != user_status::DELETED && $user[$i]['status_id'] != user_status::INACTIVE && $user[$i]['status_id']<user_status::DONT_DISTURB) {
                $real_status = 2; //cualquier otro estado
                $index = $i;
                break;
            }
        }
        return $real_status;
    }

    public function user_do_login_second_stage($datas,$language) {
        /*$login_by_client=false;
        if(!isset($datas)){
            $datas = $this->input->post();
            $language=$this->input->get();
            $login_by_client=true;
        } */      
        require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/system_config.php';
        $GLOBALS['sistem_config'] = new dumbu\cls\system_config();
        if(isset($language['language']))
            $param['language']=$language['language'];
        else
            $param['language'] = $GLOBALS['sistem_config']->LANGUAGE;    
        $param['SERVER_NAME'] = $GLOBALS['sistem_config']->SERVER_NAME;        
        $GLOBALS['language']=$param['language'];
        $this->load->model('class/user_model');
        $this->load->model('class/client_model');
        $this->load->model('class/user_role');
        $this->load->model('class/user_status');
        //Is an active Administrator?        
            /*$query = 'SELECT * FROM users' .
                    ' WHERE login="' . $datas['user_login'] . '" AND pass="' .mad5($datas['user_pass']) .
                    '" AND role_id=' . user_role::ADMIN.' AND status_id=' . user_status::ACTIVE;
            $user = $this->user_model->execute_sql_query($query);
            if(count($user)){
                $result['resource'] = 'client';
                $result['message'] = base_url().'index.php/admin/';
                $result['role'] = 'ADMIN';
                $result['authenticated'] = true;
            } else{     */   
                //Is an actually Instagram user?
                
                ($datas['force_login']=='true')? $force_login=true :$force_login=false;
                $data_insta = $this->is_insta_user($datas['user_login'], $datas['user_pass'], $force_login);
                if($data_insta==NULL){
                    /*$result['message'] = $this->T('Não foi possível conferir suas credencias com o Instagram', array(), $GLOBALS['language']);
                    $result['cause'] = 'error_login';
                    $result['authenticated'] = false;*/
                    $result['message'] = $this->T('Credenciais erradas', array(), $GLOBALS['language']);
                    $result['message_force_login'] = $this->T('Seguro que são suas credencias de IG', array(), $GLOBALS['language']);
                    $result['cause'] = 'force_login_required';
                    $result['authenticated'] = false;
                } else
                    
                if ($data_insta['authenticated']) {
                    //Is a DUMBU Client by Insta ds_user_id?
                    $query = 'SELECT * FROM users,clients' .
                            ' WHERE clients.insta_id="' . $data_insta['insta_id'] . '" AND clients.user_id=users.id';
                    $user = $this->user_model->execute_sql_query($query);

                    $N = count($user);
                    $real_status = 0; //No existe, eliminado o inactivo
                    $index = 0;
                    for ($i = 0; $i < $N; $i++) {
                        if ($user[$i]['status_id'] == user_status::BEGINNER) {
                            $real_status = 1; //Beginner
                            $index = $i;
                            break;
                        } else
                        if ($user[$i]['status_id'] != user_status::DELETED && $user[$i]['status_id'] != user_status::INACTIVE) {
                            $real_status = 2; //cualquier otro estado
                            $index = $i;
                            break;
                        }
                    }
                    if ($real_status > 1) {
                        $st = (int) $user[$index]['status_id'];
                        if ($st == user_status::BLOCKED_BY_INSTA || $st == user_status::VERIFY_ACCOUNT) {
                            $this->user_model->update_user($user[$index]['id'], array(
                                'name' => $data_insta['insta_name'],
                                'login' => $datas['user_login'],
                                'pass' => $datas['user_pass'],
                                'status_id' => user_status::ACTIVE));                                                        
                            if ($data_insta['insta_login_response']) {
//                                $this->client_model->update_client($user[$index]['id'], array(
//                                    'cookies' => json_encode($data_insta['insta_login_response'])));
                                $this->user_model->set_sesion($user[$index]['id'], $this->session, $data_insta['insta_login_response']);
                            }
                            if($st!=user_status::ACTIVE)
                                $this->user_model->insert_washdog($user[$index]['id'],'FOR ACTIVE STATUS');                            
                                $this->user_model->insert_watchdog($user[$index]['id'],'FOR ACTIVE STATUS');                            
                            //quitar trabajo si contrasenhas son diferentes
                            $active_profiles = $this->client_model->get_client_active_profiles($this->session->userdata('id'));
                            if ($user[$index]['pass'] != $datas['user_pass']) {
                                $N = count($active_profiles);
                                //quitar trabajo si contrasenhas son diferentes
                                for ($i = 0; $i < $N; $i++) {
                                    $this->client_model->delete_work_of_profile($active_profiles[$i]['id']);
                                }
                            }
                            //crearle trabajo si ya tenia perfiles de referencia y si todavia no tenia trabajo insertado
                            //$active_profiles = $this->client_model->get_client_active_profiles($this->session->userdata('id'));                                
                            if($data_insta['insta_login_response']) {
                                $N = count($active_profiles);
                                for ($i = 0; $i < $N; $i++) {
                                    $sql = 'SELECT * FROM daily_work WHERE reference_id=' . $active_profiles[$i]['id'];
                                    $response = count($this->user_model->execute_sql_query($sql));
                                    if (!$response && $active_profiles[$i]['end_date']!=='NULL')
                                        $this->client_model->insert_profile_in_daily_work($active_profiles[$i]['id'], $data_insta['insta_login_response'], $i, $active_profiles, $this->session->userdata('to_follow'));
                                }
                            }
                            $result['resource'] = 'client';
                            $result['message'] = $this->T('Usuário @1 logueado', array(0 => $datas['user_login']), $GLOBALS['language']);
                            $result['role'] = 'CLIENT';
                            $this->client_model->Create_Followed($this->session->userdata('id'));
                            $result['authenticated'] = true;
                        } else
                        if ($st == user_status::ACTIVE || $st == user_status::BLOCKED_BY_PAYMENT || $st == user_status::PENDING || $st == user_status::UNFOLLOW || user_status::BLOCKED_BY_TIME) {
                            if ($st == user_status::ACTIVE) {
                                if ($user[$index]['pass'] != $datas['user_pass']) {
                                    $active_profiles = $this->client_model->get_client_active_profiles($user[$index]['id']);
                                    $N = count($active_profiles);
                                    //quitar trabajo si contrasenhas son diferentes
                                    for ($i = 0; $i < $N; $i++) {
                                        $this->client_model->delete_work_of_profile($active_profiles[$i]['id']);
                                    }
                                    //crearle trabajo si ya tenia perfiles de referencia y si todavia no tenia trabajo insertado
                                    for ($i = 0; $i < $N; $i++) {
                                        if($active_profiles[$i]['end_date']!=='NULL')
                                            $this->client_model->insert_profile_in_daily_work($active_profiles[$i]['id'], $data_insta['insta_login_response'], $i, $active_profiles, $this->session->userdata('to_follow'));
                                    }
                                }
                            }

                            if ($st == user_status::UNFOLLOW && $data_insta['insta_following'] < $GLOBALS['sistem_config']->INSTA_MAX_FOLLOWING - $GLOBALS['sistem_config']->MIN_MARGIN_TO_INIT) {
                                $st = user_status::ACTIVE;
                                $active_profiles = $this->client_model->get_client_active_profiles($user[$index]['id']);
                                $N = count($active_profiles);
                                //crearle trabajo si ya tenia perfiles de referencia y si todavia no tenia trabajo insertado
                                for ($i = 0; $i < $N; $i++) {
                                    if($active_profiles[$i]['end_date']!=='NULL')
                                        $this->client_model->insert_profile_in_daily_work($active_profiles[$i]['id'], $data_insta['insta_login_response'], $i, $active_profiles, $this->session->userdata('to_follow'));
                                }
                            }

                            $this->user_model->update_user($user[$index]['id'], array(
                                'name' => $data_insta['insta_name'],
                                'login' => $datas['user_login'],
                                'pass' => $datas['user_pass'],
                                'status_id' => $st));
                            $cad=$this->user_model->get_status_by_id($st)['name'];
                            if ($data_insta['insta_login_response']) {
//                                $this->client_model->update_client($user[$index]['id'], array(
//                                    'cookies' => json_encode($data_insta['insta_login_response'])));
                            }
                            $this->user_model->set_sesion($user[$index]['id'], $this->session, $data_insta['insta_login_response']);
                            if($st!=user_status::ACTIVE){
                                $this->user_model->insert_washdog($this->session->userdata('id'),'FOR STATUS '.$cad);
                                $this->user_model->insert_watchdog($this->session->userdata('id'),'FOR '.$cad.'STATUS ');
                            }    
                            $result['resource'] = 'client';
                            $result['message'] = $this->T('Usuário @1 logueado', array(0 => $datas['user_login']), $GLOBALS['language']);                            
                            $result['role'] = 'CLIENT';
                            $this->client_model->Create_Followed($this->session->userdata('id'));
                            $result['authenticated'] = true;
                        } else
                        if ($st == user_status::BEGINNER) {
                            $result['resource'] = 'index#lnk_sign_in_now';
                            $result['message'] = $this->T('Falha no login! Seu cadastro esta incompleto. Por favor, termine sua assinatura.', array(), $GLOBALS['language']);
                            $result['cause'] = 'signin_required';
                            $result['authenticated'] = false;
                        } else
                        if ($st == user_status::DELETED || $st == user_status::INACTIVE) {
                            $result['resource'] = 'index#lnk_sign_in_now';
                            $result['message'] = $this->T('Falha no login! Você deve assinar novamente para receber o serviço', array(), $GLOBALS['language']);
                            $result['cause'] = 'signin_required';
                            $result['authenticated'] = false;
                        }
                    } else {
                        $result['resource'] = 'index#lnk_sign_in_now';
                        $result['message'] = $this->T('Falha no login! Você deve assinar novamente para receber o serviço', array(), $GLOBALS['language']);
                        $result['cause'] = 'signin_required';
                        $result['authenticated'] = false;
                    }
                } else
                if ($data_insta['message'] == 'incorrect_password') {
                    //Is a client with oldest Instagram credentials?
                    //Buscarlo en BD por el nombre y senha
                    $query = 'SELECT * FROM users' .
                            ' WHERE users.login="' . $datas['user_login'] .
                            '" AND users.pass="' . $datas['user_pass'] .
                            '" AND users.role_id="' . user_role::CLIENT . '"';
                    $user = $this->user_model->execute_sql_query($query);
                    $N = count($user);
                    $real_status = 0; //No existe, eliminado o inactivo
                    $index = 0;
                    for ($i = 0; $i < $N; $i++) {
                        if ($user[$i]['status_id'] == user_status::BEGINNER) {
                            $real_status = 1; //Beginner
                            $index = $i;
                            break;
                        } else
                        if ($user[$i]['status_id'] != user_status::DELETED && $user[$i]['status_id'] != user_status::INACTIVE) {
                            $real_status = 2; //cualquier otro estado
                            $index = $i;
                            break;
                        }
                    }
                    if ($real_status > 0) {
                        if ($user[$index]['status_id'] != user_status::DELETED && $user[$index]['status_id'] != user_status::INACTIVE) {
                            /*$result['resource'] = 'index';
                            $result['message'] = $this->T('Falha no login! Entre com suas credenciais do Instagram.', array(), $GLOBALS['language']);
                            $result['cause'] = 'credentials_update_required';
                            $result['authenticated'] = false;*/
                            $result['message'] = $this->T('Credenciais erradas', array(), $GLOBALS['language']);
                            $result['message_force_login'] = $this->T('Seguro que são suas credencias de IG', array(), $GLOBALS['language']);
                            $result['cause'] = 'force_login_required';
                            $result['authenticated'] = false;
                        } else {
                            $result['resource'] = 'index#lnk_sign_in_now';
                            $result['message'] = $this->T('Você deve assinar novamente para receber o serviço.', array(), $GLOBALS['language']);
                            $result['cause'] = 'signin_required';
                            $result['authenticated'] = false;
                        }
                    } else {
                        //Verificar que el userLogin y respectivo ds_user_id pueden pertenecer a un usuario que
                        //esta intentando entrar por 3 o mas veces con senha antigua
                        //Buscarlo en BD por pk obtenido por el nombre de usuario informado
                        $data_profile = $this->check_insta_profile($datas['user_login']);
                        if ($data_profile) {
                            $query = 'SELECT * FROM users,clients' .
                                    ' WHERE clients.insta_id="' . $data_profile->pk . '" AND clients.user_id=users.id';
                            $user = $this->user_model->execute_sql_query($query);
                            $N = count($user);
                            $real_status = 0; //No existe, eliminado o inactivo
                            $index = 0;
                            for ($i = 0; $i < $N; $i++) {
                                if ($user[$i]['status_id'] == user_status::BEGINNER) {
                                    $real_status = 1; //Beginner
                                    $index = $i;
                                    break;
                                } else
                                if ($user[$i]['status_id'] != user_status::DELETED && $user[$i]['status_id'] != user_status::INACTIVE) {
                                    $real_status = 2; //cualquier otro estado
                                    $index = $i;
                                    break;
                                }
                            }
                            if ($real_status > 0) {
                                //perfil exite en instagram y en la base de datos, senha incorrecta           
                                /*$result['message'] = $this->T('Senha incorreta!. Entre com sua senha de Instagram.', array(), $GLOBALS['language']);
                                $result['cause'] = 'error_login';
                                $result['authenticated'] = false;*/
                                $result['message'] = $this->T('Credenciais erradas', array(), $GLOBALS['language']);
                                $result['message_force_login'] = $this->T('Seguro que são suas credencias de IG', array(), $GLOBALS['language']);
                                $result['cause'] = 'force_login_required';
                                $result['authenticated'] = false;
                            } else {
                                //el perfil existe en instagram pero no en la base de datos
                                /*$result['message'] = $this->T('Falha no login! Certifique-se de que possui uma assinatura antes de entrar.', array(), $GLOBALS['language']);
                                $result['cause'] = 'error_login';
                                $result['authenticated'] = false;*/
                            }
                        } else {
                            //nombre de usuario informado no existe en instagram
                            /*$result['message'] = $this->T('Falha no login! O nome de usuário fornecido não existe no Instagram.', array(), $GLOBALS['language']);
                            $result['cause'] = 'error_login';
                            $result['authenticated'] = false;*/
                            $result['message'] = $this->T('Credenciais erradas', array(), $GLOBALS['language']);
                            $result['message_force_login'] = $this->T('Seguro que são suas credencias de IG', array(), $GLOBALS['language']);
                            $result['cause'] = 'force_login_required';
                            $result['authenticated'] = false;
                        }
                    }
                } else
                if ($data_insta['message'] == 'checkpoint_required') {
                    $data_profile = $this->check_insta_profile($datas['user_login']);
                    $query = 'SELECT * FROM users,clients' .
                            ' WHERE clients.insta_id="' . $data_profile->pk . '" AND clients.user_id=users.id';
                    $user = $this->user_model->execute_sql_query($query);
                    $N = count($user);
                    $real_status = 0; //No existe, eliminado o inactivo
                    $index = 0;
                    for ($i = 0; $i < $N; $i++) {
                        if ($user[$i]['status_id'] == user_status::BEGINNER) {
                            $real_status = 1; //Beginner
                            $index = $i;
                            break;
                        } else
                        if ($user[$i]['status_id'] != user_status::DELETED && $user[$i]['status_id'] != user_status::INACTIVE) {
                            $real_status = 2; //cualquier otro estado
                            $index = $i;
                            break;
                        }
                    }
                    if ($real_status == 2) {
                        $status_id = $user[$index]['status_id'];
                        if ($user[$index]['status_id'] != user_status::BLOCKED_BY_PAYMENT && $user[$index]['status_id'] != user_status::PENDING) {
                            $status_id = user_status::VERIFY_ACCOUNT;
                            $this->user_model->insert_washdog($user[$index]['id'],'FOR VERIFY ACCOUNT STATUS');
                            $this->user_model->insert_watchdog($user[$index]['id'],'FOR VERIFY_ACCOUNT STATUS');
                       }
                        $this->user_model->update_user($user[$index]['id'], array(
                            'login' => $datas['user_login'],
                            'pass' => $datas['user_pass'],
                            'status_id' => $status_id
                        ));
                        $cad=$this->user_model->get_status_by_id($status_id)['name']; 
                        //$this->session->sess_time_to_update = 7200;
                        $this->session->cookie_secure = true;
                        $this->user_model->set_sesion($user[$index]['id'], $this->session);
                        if ($status_id != user_status::ACTIVE){
                            $this->user_model->insert_washdog($this->session->userdata('id'),'FOR  STATUS '.$cad);
                            $this->user_model->insert_watchdog($this->session->userdata('id'),'FOR '.$cad.' STATUS');
                        }    
                        $result['role'] = 'CLIENT'; // agregado por Ruslan pa resolver problema en login
                        $result['resource'] = 'client';                        
                        $result['verify_link'] = $data_insta['verify_account_url'];
                        $result['return_link'] = 'client';
                        $result['message'] = $this->T('Sua conta precisa ser verificada no Instagram', array(), $GLOBALS['language']);
                        $result['cause'] = 'checkpoint_required';
                        $this->client_model->Create_Followed($this->session->userdata('id'));
                        $result['authenticated'] = true;
                    } else {
                        //usuario informado no es usuario de dumbu y lo bloquearon por mongolico
                        /*$result['message'] = $this->T('Falha no login! Certifique-se de que possui uma assinatura antes de entrar.', array(), $GLOBALS['language']);
                        $result['cause'] = 'error_login';
                        $result['authenticated'] = false;*/
                        $result['message'] = $this->T('Credenciais erradas', array(), $GLOBALS['language']);
                        $result['message_force_login'] = $this->T('Seguro que são suas credencias de IG', array(), $GLOBALS['language']);
                        $result['cause'] = 'force_login_required';
                        $result['authenticated'] = false;
                    }
                } else
                if ($data_insta['message'] == '' || $data_insta['message'] == 'phone_verification_settings') {
                    if (isset($data_insta['obfuscated_phone_number'])) {
                        $data_profile = $this->check_insta_profile($datas['user_login']);
                        $query = 'SELECT * FROM users,clients' .
                                ' WHERE clients.insta_id="' . $data_profile->pk . '" AND clients.user_id=users.id';
                        $user = $this->user_model->execute_sql_query($query);
                        $N = count($user);
                        $real_status = 0; //No existe, eliminado o inactivo
                        $index = 0;
                        for ($i = 0; $i < $N; $i++) {
                            if ($user[$i]['status_id'] == user_status::BEGINNER) {
                                $real_status = 1; //Beginner
                                $index = $i;
                                break;
                            } else
                            if ($user[$i]['status_id'] != user_status::DELETED && $user[$i]['status_id'] != user_status::INACTIVE) {
                                $real_status = 2; //cualquier otro estado
                                $index = $i;
                                break;
                            }
                        }
                        if ($real_status == 2) {
                            $status_id = $user[$index]['status_id'];
                            if ($user[$index]['status_id'] != user_status::BLOCKED_BY_PAYMENT && $user[$index]['status_id'] != user_status::PENDING) {
                                $status_id = user_status::VERIFY_ACCOUNT;
                                $this->user_model->insert_washdog($user[$index]['id'],'FOR VERIFY ACCOUNT STATUS');
                                $this->user_model->insert_watchdog($user[$index]['id'],'FOR VERIFY_ACCOUNT STATUS');
                        }
                            $this->user_model->update_user($user[$index]['id'], array(
                                'login' => $datas['user_login'],
                                'pass' => $datas['user_pass'],
                                'status_id' => $status_id
                            ));
                            $cad=$this->user_model->get_status_by_id($status_id)['name'];
                            $this->user_model->set_sesion($user[$index]['id'], $this->session);
                            $this->user_model->insert_washdog($this->session->userdata('id'),'FOR STATUS '.$cad);
                            $this->user_model->insert_watchdog($this->session->userdata('id'),'FOR '.$cad.'STATUS ');
                            $result['return_link'] = 'index';
                            $result['message'] = $this->T('Sua conta precisa ser verificada no Instagram com código enviado ao numero de telefone que comtênm os digitos ', array(0 => $data_insta['obfuscated_phone_number']), $GLOBALS['language']);
                            $result['cause'] = 'phone_verification_settings';
                            $result['verify_link'] = '';
                            $result['obfuscated_phone_number'] = $data_insta['obfuscated_phone_number'];
                            $result['authenticated'] = false;
                        } else {
                            //usuario informado no es usuario de dumbu y lo bloquearon por mongolico
                            /*$result['message'] = $this->T('Falha no login! Certifique-se de que possui uma assinatura antes de entrar.', array(), $GLOBALS['language']);
                            $result['cause'] = 'error_login';
                            $result['authenticated'] = false;*/
                            $result['message'] = $this->T('Credenciais erradas', array(), $GLOBALS['language']);
                            $result['message_force_login'] = $this->T('Seguro que são suas credencias de IG', array(), $GLOBALS['language']);
                            $result['cause'] = 'force_login_required';
                            $result['authenticated'] = false;
                        }
                    } else
                    if ($data_insta['message'] === 'empty_message') {
                        $data_profile = $this->check_insta_profile($datas['user_login']);
                        $query = 'SELECT * FROM users,clients' .
                                ' WHERE clients.insta_id="' . $data_profile->pk . '" AND clients.user_id=users.id';
                        $user = $this->user_model->execute_sql_query($query);
                        $N = count($user);
                        $real_status = 0; //No existe, eliminado o inactivo
                        $index = 0;
                        for ($i = 0; $i < $N; $i++) {
                            if ($user[$i]['status_id'] == user_status::BEGINNER) {
                                $real_status = 1; //Beginner
                                $index = $i;
                                break;
                            } else
                            if ($user[$i]['status_id'] != user_status::DELETED && $user[$i]['status_id'] != user_status::INACTIVE) {
                                $real_status = 2; //cualquier otro estado
                                $index = $i;
                                break;
                            }
                        }
                        if ($real_status == 2) {
                            $status_id = $user[$index]['status_id'];
                            if ($user[$index]['status_id'] != user_status::BLOCKED_BY_PAYMENT && $user[$index]['status_id'] != user_status::PENDING) {
                                $status_id = user_status::VERIFY_ACCOUNT;
                                $this->user_model->insert_washdog($user[$index]['id'],'FOR VERIFY ACCOUNT STATUS');
                                $this->user_model->insert_watchdog($user[$index]['id'],'FOR VERIFY_ACCOUNT STATUS');
               }
                            $this->user_model->update_user($user[$index]['id'], array(
                                'login' => $datas['user_login'],
                                'pass' => $datas['user_pass'],
                                'status_id' => $status_id
                            ));
                            $cad=$this->user_model->get_status_by_id($status_id)['name'];
                            $this->user_model->set_sesion($user[$index]['id'], $this->session);
                            $this->user_model->insert_washdog($this->session->userdata('id'),'FOR STATUS '.$cad);
                            $this->user_model->insert_watchdog($this->session->userdata('id'),'FOR '.$cad.'STATUS ');
                            $result['resource'] = 'client';
                            $result['return_link'] = 'index';
                            $result['verify_link'] = '';
                            $result['message'] = $this->T('Sua conta esta presentando problemas temporalmente no Instagram. Entre em contato conosco para resolver o problema', array(), $GLOBALS['language']);
                            $result['cause'] = 'empty_message';
                            $result['authenticated'] = false;
                        } else {
                            //usuario informado no es usuario de dumbu y lo bloquearon por mongolico
                            /*$result['message'] = $this->T('Falha no login! Certifique-se de que possui uma assinatura antes de entrar.', array(), $GLOBALS['language']);
                            $result['cause'] = 'error_login';
                            $result['authenticated'] = false;*/
                            $result['message'] = $this->T('Credenciais erradas', array(), $GLOBALS['language']);
                            $result['message_force_login'] = $this->T('Seguro que são suas credencias de IG', array(), $GLOBALS['language']);
                            $result['cause'] = 'force_login_required';
                            $result['authenticated'] = false;
                        }
                    } else
                    if ($data_insta['message'] == 'unknow_message') {
                        $data_profile = $this->check_insta_profile($datas['user_login']);
                        $query = 'SELECT * FROM users,clients' .
                                ' WHERE clients.insta_id="' . $data_profile->pk . '" AND clients.user_id=users.id';
                        $user = $this->user_model->execute_sql_query($query);
                        $N = count($user);
                        $real_status = 0; //No existe, eliminado o inactivo
                        $index = 0;
                        for ($i = 0; $i < $N; $i++) {
                            if ($user[$i]['status_id'] == user_status::BEGINNER) {
                                $real_status = 1; //Beginner
                                $index = $i;
                                break;
                            } else
                            if ($user[$i]['status_id'] != user_status::DELETED && $user[$i]['status_id'] != user_status::INACTIVE) {
                                $real_status = 2; //cualquier otro estado
                                $index = $i;
                                break;
                            }
                        }
                        if ($real_status == 2) {
                            $status_id = $user[$index]['status_id'];
                            if ($user[$index]['status_id'] != user_status::BLOCKED_BY_PAYMENT && $user[$index]['status_id'] != user_status::PENDING) {
                                $status_id = user_status::VERIFY_ACCOUNT;
                                $this->user_model->insert_washdog($user[$index]['id'],'FOR VERIFY ACCOUNT STATUS');
                            }
                            $this->user_model->update_user($user[$index]['id'], array(
                                'login' => $datas['user_login'],
                                'pass' => $datas['user_pass'],
                                'status_id' => $status_id
                            ));
                            $cad=$this->user_model->get_status_by_id($status_id)['name'];
                            if($st!=user_status::ACTIVE){
                                $this->user_model->insert_washdog($user[$index]['id'],'FOR STATUS '.$cad);
                                $this->user_model->insert_watchdog($user[$index]['id'],'FOR '.$cad.'STATUS ');
                            }    
                            $result['resource'] = 'client';
                            $result['return_link'] = 'index';
                            $result['verify_link'] = '';
                            $result['message'] = $data_insta['unknow_message'];
                            $result['cause'] = 'unknow_message';
                            $result['authenticated'] = false;
                        } else {
                            //usuario informado no es usuario de dumbu y lo bloquearon por mongolico
                            /*$result['message'] = $this->T('Falha no login! Certifique-se de que possui uma assinatura antes de entrar.', array(), $GLOBALS['language']);
                            $result['cause'] = 'error_login';
                            $result['authenticated'] = false;*/
                            $result['message'] = $this->T('Credenciais erradas', array(), $GLOBALS['language']);
                            $result['message_force_login'] = $this->T('Seguro que são suas credencias de IG', array(), $GLOBALS['language']);
                            $result['cause'] = 'force_login_required';
                            $result['authenticated'] = false;
                        }
                    }
                } else {
                    /*$result['message'] = $this->T('Se o problema no login continua, por favor entre em contato com o Atendimento', array(), $GLOBALS['language']);
                    $result['cause'] = 'error_login';
                    $result['authenticated'] = false;*/
                    $result['message'] = $this->T('Credenciais erradas', array(), $GLOBALS['language']);
                    $result['message_force_login'] = $this->T('Seguro que são suas credencias de IG', array(), $GLOBALS['language']);
                    $result['cause'] = 'force_login_required';
                    $result['authenticated'] = false;
                }
            //
        if($result['authenticated'] == true){
            $this->load->model('class/user_model');
            $this->user_model->insert_washdog($this->session->userdata('id'),'DID LOGIN ');
            $this->user_model->insert_watchdog($this->session->userdata('id'),'DID LOGIN');
        }
//        if($login_by_client)
//            echo json_encode($result);
//        else
            return $result;
    }

    public function check_ticket_peixe_urbano() {
        $this->load->model('class/client_model');
        $datas = $this->input->post();
        if(true){
            $this->client_model->update_client($datas['pk'], array(
                'ticket_peixe_urbano'=>$datas['cupao_number']));
            $result['success'] = true;
            $result['message'] = 'CUPOM de desconto verificado corretamennte';
        } else{
            $result['success'] = false;
            $result['message'] = 'CUPOM de desconto incorreto';
        }
        echo json_encode($result);
    }
    
       
    //Sign-in functions
    //Passo 1. Chequeando usuario em IG
    public function check_user_for_sing_in($datas=NULL) { //sign in with passive instagram profile verification
        require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/system_config.php';
        $GLOBALS['sistem_config'] = new dumbu\cls\system_config();
        $this->load->model('class/client_model');
        $this->load->model('class/user_model');
        $this->load->model('class/user_status');
        $this->load->model('class/user_role');
        $origin_datas=$datas;
        if(!$datas){
            $datas = $this->input->post();
             $GLOBALS['language']=$datas['language'];
        }

        $datas['utm_source'] = isset($datas['utm_source']) ? urldecode($datas['utm_source']) : "NULL";
        
        $data_insta = $this->check_insta_profile($datas['client_login']);
        if ($data_insta) {
            if (!isset($data_insta->following))
                $data_insta->following = 0;
            $query = 'SELECT * FROM users,clients WHERE clients.insta_id="' . $data_insta->pk . '"' .
                    'AND clients.user_id=users.id';
            $client = $this->user_model->execute_sql_query($query);
            $N = count($client);
            $real_status = -1; //No existe
            $early_client_canceled = false;
            $index = 0;
            for ($i = 0; $i < $N; $i++) {
                if ($client[$i]['status_id'] == user_status::DELETED || $client[$i]['status_id'] == user_status::INACTIVE) {
                    $real_status = 0; //cancelado o inactivo
                    $early_client_canceled = true;
                    $index = $i;
                    //break;
                } else
                if ($client[$i]['status_id'] == user_status::BEGINNER) {
                    $real_status = 1; //Beginner
                    $index = $i;
                    break;
                } else
                if ($client[$i]['status_id'] != user_status::DELETED && $client[$i]['status_id'] != user_status::INACTIVE) {
                    $real_status = 2; //cualquier otro estado
                    break;
                }
            }
            if ($real_status == -1 || $real_status == 0) {
                $datas['role_id'] = user_role::CLIENT;
                $datas['status_id'] = user_status::BEGINNER;
                $datas['HTTP_SERVER_VARS'] = json_encode($_SERVER);
                $datas['purchase_counter'] =$GLOBALS['sistem_config']->MAX_PURCHASE_RETRY;
                $id_user = $this->client_model->insert_client($datas, $data_insta);
                $response['pk'] = $id_user;
                if ($real_status == 0 || $early_client_canceled)
                    $response['early_client_canceled'] = true;
                else
                    $response['early_client_canceled'] = false;
                $response['datas'] = json_encode($data_insta);
                $response['success'] = true;
                $security_code=rand(100000,999999);
                $this->security_purchase_code=md5("$security_code");
                //TODO: enviar para el navegador los datos del usuario logueado en las cookies para chequearlas en los PASSOS 2 y 3
            } else {
                if ($real_status ==1) {
                    $this->user_model->update_user($client[$i]['id'], array(
                        'name' => $data_insta->full_name,
                        'email' => $datas['client_email'],
                        'login' => $datas['client_login'],
                        'pass' => $datas['client_pass'],
                        'language' =>  $GLOBALS['language'],
                        'init_date' => time()));
                    $this->client_model->update_client($client[$i]['id'], array(
                        'insta_followers_ini' => $data_insta->follower_count,
                        'insta_following' => $data_insta->following,
                        'utm_source'=>$datas['utm_source'],
                        'HTTP_SERVER_VARS' => json_encode($_SERVER)));
                    
                    $this->client_model->insert_initial_instagram_datas($client[$i]['id'], array(
                        'followers' => $data_insta->follower_count,
                        'followings' => $data_insta->following,
                        'date' => time()));
                    $response['datas'] = json_encode($data_insta);
                    if ($early_client_canceled)
                        $response['early_client_canceled'] = true;
                    else
                        $response['early_client_canceled'] = false;
                    $response['pk'] = $client[$index]['user_id'];
                    $response['success'] = true;
                } else {
                    $response['success'] = false;
                    $response['message'] = $this->T('O usuario informado já tem cadastro no sistema.', array(), $GLOBALS['language']);
                }
            }
            if ($response['success'] == true) {
                $response['need_delete'] = ($GLOBALS['sistem_config']->INSTA_MAX_FOLLOWING - $data_insta->following);
                //TODO: guardar esta cantidad en las cookies para trabajar con lo que este en la cookie
                $response['MIN_MARGIN_TO_INIT'] = $GLOBALS['sistem_config']->MIN_MARGIN_TO_INIT;
            }
        } else {
            $response['success'] = false;
            $response['cause'] = 'missing_user';
            $response['message'] = $this->T('O nome de usuario informado não é um perfil do Instagram.', array(), $GLOBALS['language']);
        }
        if(!$origin_datas)
            echo json_encode($response);
        else
            return $response;
    }
    
    
    //Passo 2.1 Pagamento por boleto bancario
    public function check_client_ticket_bank($datas=NULL) {  
        require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/system_config.php';
        $GLOBALS['sistem_config'] = new dumbu\cls\system_config();
        $origin_datas=$datas;        
        $datas = $this->input->post();
        $datas['plane_id']=intval($datas['plane_type']);
        $datas['ticket_bank_option']=intval($datas['ticket_bank_option']);
        
        //1. analisar se é possivel gerar boleto para esse cliente ()
        if(!true){ 
            //TODO YANETXY
            $result['success'] = false;
            $result['message'] = $this->T('Número de tentativas esgotadas. Contate nosso atendimento', array(), $GLOBALS['language']);
        }else
            
        //2. conferir los datos recebidos
        if(!$this->validaCPF($datas['cpf'])){
            $result['success'] = false;
            $result['message'] = 'CPF incorreto';
        } else
        if( !( $datas['plane_id']>1 && $datas['plane_id']<=5 )){
            $result['success'] = false;
            $result['message'] = 'Plano informado incorreto';
        } else
        if( !( $datas['ticket_bank_option']>=1 && $datas['ticket_bank_option']<=3 )){
            $result['success'] = false;
            $result['message'] = 'Selecione um periodo de tempo válido pra ganhar desconto';
        } else{

        //3. gerar boleto bancario e salvar dados
        $this->load->model('class/user_model');
        $query='SELECT * FROM plane WHERE id='.$datas['plane_id'];
        $plane_datas = $this->user_model->execute_sql_query($query)[0];
        if($datas['ticket_bank_option']==1)
            $datas['AmountInCents'] = round($plane_datas['normal_val']*0.85*3);
        else
        if($datas['ticket_bank_option']==2)
            $datas['AmountInCents'] = round($plane_datas['normal_val']*0.75*6);
        else
        if($datas['ticket_bank_option']==3)
            $datas['AmountInCents'] = round($plane_datas['normal_val']*0.60*12);
                
        $this->load->model('class/client_model');
        $query="SELECT value FROM dumbu_system_config WHERE name='TICKET_BANK_DOCUMENT_NUMBER'";
        $DocumentNumber = $this->client_model->execute_sql_query($query)[0]['value'];
        
        $datas['DocumentNumber'] = $DocumentNumber+1;
        $datas['OrderReference']=$DocumentNumber+1;
        $datas['user_id'] = $datas['pk'];
        $datas['name']=$datas['ticket_bank_client_name'];
        $response = $this->check_mundipagg_boleto($datas);
        
        
        //4. enviar email com link do boleto e o link da success_purchase com access token encriptada com md5

        //5. retornar response e tomar decisão no cliente
            
            
        //OBS: o cliente ainda continua em BEGINNER
        }
        echo json_encode($result);
    }
    
    public function  validaCPF($cpf = null) {
        //$cpf='06266544750';
        if(empty($cpf)) 
            return false; 
        $cpf = preg_replace('[^0-9]', '', $cpf);
        $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);
        if (strlen($cpf) != 11)
            return false;    
        else if ($cpf == '00000000000' || 
            $cpf == '11111111111' || $cpf == '22222222222' || $cpf == '33333333333' || 
            $cpf == '44444444444' || $cpf == '55555555555' || $cpf == '66666666666' || 
            $cpf == '77777777777' || $cpf == '88888888888' || $cpf == '99999999999') {
            return false;
         } else {   
            for ($t = 9; $t < 11; $t++) {
                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf{$c} * (($t + 1) - $c);
                }
                $d = ((10 * $d) % 11) % 10;
                if ($cpf{$c} != $d) {
                    return false;
                }
            }
            return true;
        }
    }

    //Passo 2.2 CChequeando datos bancarios y guardando datos y estado del cliente pagamento     
    public function check_client_data_bank($datas=NULL) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/system_config.php';
        $GLOBALS['sistem_config'] = new dumbu\cls\system_config();
        $origin_datas=$datas;
        if($datas==NULL)
            $datas = $this->input->post();
        $this->load->model('class/client_model');
        $query='SELECT status_id FROM users WHERE id='.$datas['pk'];
        $aaa=$this->client_model->execute_sql_query($query);   
        $aaa=$aaa[0]['status_id'];
        if($aaa==='8' || $aaa==='4'){
            $query='SELECT purchase_counter FROM clients WHERE user_id='.$datas['pk'];
            $purchase_counter = ($this->client_model->execute_sql_query($query));
            $purchase_counter=(int)$purchase_counter[0]['purchase_counter'];
            if($purchase_counter>0){
                $this->load->model('class/user_model');
                $this->load->model('class/user_status');
                $this->load->model('class/credit_card_status');
                if($this->validate_post_credit_card_datas($datas)){
                    //0. salvar datos del carton de credito
                    try {
                        $this->client_model->update_client($datas['pk'], array(
                            'credit_card_number' => $datas['credit_card_number'],
                            'credit_card_cvc' => $datas['credit_card_cvc'],
                            'credit_card_name' => $datas['credit_card_name'],
                            'credit_card_exp_month' => $datas['credit_card_exp_month'],
                            'credit_card_exp_month' => $datas['credit_card_exp_month'],
                            'credit_card_exp_year' => $datas['credit_card_exp_year']//,
                            //'card_type' => $card_type
                        ));

                        $this->client_model->update_client($datas['pk'], array(
                            'plane_id' => $datas['plane_type']));

                        if(isset($datas['ticket_peixe_urbano'])){
                                $ticket=trim($datas['ticket_peixe_urbano']);                        
                                $this->client_model->update_client($datas['pk'], array(
                                    'ticket_peixe_urbano' => $ticket
                                ));
                            }
                                                
                    } catch (Exception $exc) {
                        $result['success'] = false;
                        $result['exception'] = $exc->getTraceAsString();
                        $result['message'] = $this->T('Error actualizando en base de datos', array(), $GLOBALS['language'], $GLOBALS['language']);
                        //2. hacel el pagamento segun el plano
                    } finally {
                        // TODO: Hacer clase Plane
                        if ($datas['plane_type'] === '2' || $datas['plane_type'] === '3' || $datas['plane_type'] === '4' || $datas['plane_type'] === '5' || $datas['plane_type'] === '1') {
                            $sql = 'SELECT * FROM plane WHERE id=' . $datas['plane_type'];
                            $plane_datas = $this->user_model->execute_sql_query($sql)[0];
                            if($card_type==0)
                                $response = $this->do_payment_by_plane($datas, $plane_datas['initial_val'], $plane_datas['normal_val']);                            
                        } else
                            $response['flag_initial_payment'] = false;
                    }
                    //3. si pagamento correcto: logar cliente, establecer sesion, actualizar status, emails, initdate

                    if($response['flag_initial_payment']) {
                        $this->load->model('class/user_model');
                        $data_insta = $this->is_insta_user($datas['user_login'], $datas['user_pass'],$datas['force_login']);
                        //$this->user_model->insert_washdog($datas['pk'],'SUCCESSFUL PURCHASE');
                        if ($data_insta['status'] === 'ok' && $data_insta['authenticated']) {
                            /*if ($datas['need_delete'] < $GLOBALS['sistem_config']->MIN_MARGIN_TO_INIT)
                                $datas['status_id'] = user_status::UNFOLLOW;
                            else*/
                                $datas['status_id'] = user_status::ACTIVE;
                            $this->user_model->update_user($datas['pk'], array(
                                'init_date' => time(),
                                'status_id' => $datas['status_id']));
                            if($data_insta['insta_login_response']) {
                                $this->client_model->update_client($datas['pk'], array(
                                    'cookies' => json_encode($data_insta['insta_login_response'])));
                            }
                            $this->user_model->set_sesion($datas['pk'], $this->session, $data_insta['insta_login_response']);
                        
                        } else
                        if ($data_insta['status'] === 'ok' && !$data_insta['authenticated']) {
                            $this->user_model->update_user($datas['pk'], array(
                                'init_date' => time(),
                                'status_id' => user_status::BLOCKED_BY_INSTA));
                            $this->user_model->set_sesion($datas['pk'], $this->session);
                        } else
                        if ($data_insta['status'] === 'fail' && $data_insta['message'] == 'checkpoint_required') {
                            $this->user_model->update_user($datas['pk'], array(
                                'init_date' => time(),
                                'status_id' => user_status::VERIFY_ACCOUNT));
                            $result['resource'] = 'client';
                            $result['verify_link'] = $data_insta['verify_account_url'];
                            $result['return_link'] = 'client';
                            $result['message'] = 'Sua conta precisa ser verificada no Instagram';
                            $result['cause'] = 'checkpoint_required';
                            $this->user_model->set_sesion($datas['pk'], $this->session);
                        } else
                        if ($data_insta['status'] === 'fail' && $data_insta['message'] == '') {
                            $this->user_model->update_user($datas['pk'], array(
                                'init_date' => time(),
                                'status_id' => user_status::VERIFY_ACCOUNT));
                            $result['resource'] = 'client';
                            $result['verify_link'] = '';
                            $result['return_link'] = 'client';
                            $this->user_model->set_sesion($datas['pk'], $this->session);
                        } else {
                            $this->user_model->update_user($datas['pk'], array(
                                'init_date' => time(),
                                'status_id' => user_status::BLOCKED_BY_INSTA));
                            $this->user_model->set_sesion($datas['pk'], $this->session);
                        }
                        //Email com compra satisfactoria a atendimento y al cliente
                        //$this->email_success_buy_to_atendiment($datas['user_login'], $datas['user_email']);
                        if ($data_insta['status'] === 'ok' && $data_insta['authenticated'])
                            $this->email_success_buy_to_client($datas['user_email'], $data_insta['insta_name'], $datas['user_login'], $datas['user_pass']);
                        else
                            $this->email_success_buy_to_client($datas['user_email'], $datas['user_login'], $datas['user_login'], $datas['user_pass']);
                        $result['success'] = true;
                        $result['flag_initial_payment'] = $response['flag_initial_payment'];
                        $result['flag_recurrency_payment'] = $response['flag_recurrency_payment'];
                        $result['message'] = $this->T('Usuário cadastrado com sucesso', array(), $GLOBALS['language']);
                    } else {
                        $value['purchase_counter']=$purchase_counter-1;
                        $this->client_model->decrement_purchase_retry($datas['pk'],$value);
                        $result['success'] = false;
                        $result['message'] = $response['message'];
                    }
                } else {
                    $result['success'] = false;
                    $result['message'] = $this->T('Acesso não permitido', array(), $GLOBALS['language']);
                } 
            }else{
                $result['success'] = false;
                $result['message'] = $this->T('Alcançõu a quantidade máxima de retentativa de compra, por favor, entre en contato con o atendimento', array(), $GLOBALS['language']);
            }
        }else{
            $result['success'] = false;
            $result['message'] = $this->T('Acesso não permitido', array(), $GLOBALS['language']);
        }
        
        if(!$origin_datas)
            echo json_encode($result);
        else
            return $result;
    }

    public function do_payment_by_plane($datas, $initial_value, $recurrency_value) {
        $this->load->model('class/client_model');
        require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/system_config.php';
        $GLOBALS['sistem_config'] = new dumbu\cls\system_config();
        
        //Amigos de Pedro
        if(isset($datas['ticket_peixe_urbano']) && strtoupper($datas['ticket_peixe_urbano'])==='AMIGOSDOPEDRO'){
                //1. recurrencia para un mes mas alante
                $datas['amount_in_cents'] = $recurrency_value;
                if ($datas['early_client_canceled'] === 'true'){
                    $resp = $this->check_mundipagg_credit_card($datas);
                    if(!(is_object($resp) && $resp->isSuccess()&& $resp->getData()->CreditCardTransactionResultCollection[0]->CapturedAmountInCents>0)){
                        $response['flag_recurrency_payment'] = false;
                        $response['flag_initial_payment'] = false;
                        if(is_array($resp))
                            $response['message'] = 'Error: '.$resp["message"]; 
                        else
                            $response['message'] = 'Incorrect credit card datas!!';
                        return $response;
                    }
                }
                $datas['pay_day'] = strtotime("+1 month", time());
                $resp = $this->check_recurrency_mundipagg_credit_card($datas, 0);
                if (is_object($resp) && $resp->isSuccess()) {
                    $this->client_model->update_client($datas['pk'], array(
                        'order_key' => $resp->getData()->OrderResult->OrderKey,
                        'pay_day' => $datas['pay_day']));
                    $response['flag_initial_payment'] = true;
                    $response['flag_recurrency_payment'] = true;
                } else {
                    $response['flag_recurrency_payment'] = false;
                    $response['flag_initial_payment'] = false;
                    $response['message'] = $this->T('Compra não sucedida. Problemas com o pagamento', array(), $GLOBALS['language']);
                } 
        } else 
        //OLX
        if(isset($datas['ticket_peixe_urbano']) && ($datas['ticket_peixe_urbano']==='OLX' || $datas['ticket_peixe_urbano']==='INSTA50P')){
                $resp=1;
                if ($datas['early_client_canceled'] === 'true'){
                    $datas['amount_in_cents'] = $recurrency_value/2;
                    $datas['pay_day']=time();
                    $resp = $this->check_mundipagg_credit_card($datas);                    
                    if(!(is_object($resp) && $resp->isSuccess()&& $resp->getData()->CreditCardTransactionResultCollection[0]->CapturedAmountInCents>0)){
                        $response['flag_recurrency_payment'] = false;
                        $response['flag_initial_payment'] = false;
                        if(is_array($resp))
                            $response['message'] = 'Error: '.$resp["message"]; 
                        else
                            $response['message'] = 'Incorrect credit card datas!!';
                        return $response;
                    }
                } else{
                    $kk=$GLOBALS['sistem_config']->PROMOTION_N_FREE_DAYS;
                    $t=time();
                    $datas['pay_day'] = strtotime("+" . $GLOBALS['sistem_config']->PROMOTION_N_FREE_DAYS . " days", $t);
                    $t2=$datas['pay_day'];
                    $datas['amount_in_cents'] = $recurrency_value/2;
                    $resp = $this->check_recurrency_mundipagg_credit_card($datas,1);  
                }
            
                //guardo el initial order key
                if(is_object($resp) && $resp->isSuccess()){
                    $this->client_model->update_client($datas['pk'], array('initial_order_key' => $resp->getData()->OrderResult->OrderKey));                    
                    $response['flag_initial_payment'] = true;

                    //genero una recurrencia un mes mas alante
                    $datas['amount_in_cents'] = $recurrency_value;
                    $datas['pay_day'] = strtotime("+1 month", $datas['pay_day']);
                    $resp = $this->check_recurrency_mundipagg_credit_card($datas, 0);
                    if (is_object($resp) && $resp->isSuccess()) {
                        $this->client_model->update_client($datas['pk'], array(
                            'order_key' => $resp->getData()->OrderResult->OrderKey,
                            'pay_day' => $datas['pay_day']));
                        $response['flag_recurrency_payment'] = true;
                    } else {
                        $response['flag_recurrency_payment'] = false;
                        if(is_array($resp))
                            $response['message'] = 'Error: '.$resp["message"]; 
                        else
                            $response['message'] = 'Incorrect credit card datas!!';
                        if(is_object($resp) && isset($resp->getData()->OrderResult->OrderKey)) {                        
                            $this->client_model->update_client($datas['pk'], array('order_key' => $resp->getData()->OrderResult->OrderKey));
                        }
                    }
                } else{
                    $response['flag_recurrency_payment'] = false;
                    $response['flag_initial_payment'] = false;
                    if(is_array($resp))
                        $response['message'] = 'Error: '.$resp["message"]; 
                    else
                        $response['message'] = 'Incorrect credit card datas!!';
                    if(is_object($resp) && isset($resp->getData()->OrderResult->OrderKey)) {                        
                        $this->client_model->update_client($datas['pk'], array('initial_order_key' => $resp->getData()->OrderResult->OrderKey));
                }
            }
        }else
        //DUMBUDF20
        if(isset($datas['ticket_peixe_urbano']) &&  $datas['ticket_peixe_urbano']==='DUMBUDF20'){
                $datas['amount_in_cents'] = round(($recurrency_value*8)/10);
                if ($datas['early_client_canceled'] === 'true'){
                    $resp = $this->check_mundipagg_credit_card($datas);
                    if(!(is_object($resp) && $resp->isSuccess()&& $resp->getData()->CreditCardTransactionResultCollection[0]->CapturedAmountInCents>0)){
                        $response['flag_recurrency_payment'] = false;
                        $response['flag_initial_payment'] = false;
                        if(is_array($resp))
                            $response['message'] = 'Error: '.$resp["message"]; 
                        else
                            $response['message'] = 'Incorrect credit card datas!!';
                        return $response;
                    } else{
                        $datas['pay_day'] = strtotime("+1 month", time());                
                        $resp = $this->check_recurrency_mundipagg_credit_card($datas,0);
                    }
                } else{
                    $datas['pay_day'] = strtotime("+" . $GLOBALS['sistem_config']->PROMOTION_N_FREE_DAYS . " days", time());                
                    $resp = $this->check_recurrency_mundipagg_credit_card($datas,0);
                }
                if (is_object($resp) && $resp->isSuccess()) {
                    $this->client_model->update_client($datas['pk'], array(
                        'order_key' => $resp->getData()->OrderResult->OrderKey,
                        'pay_day' => $datas['pay_day']));
                    $this->client_model->update_client($datas['pk'], array(
                        'actual_payment_value' => $datas['amount_in_cents']));
                    $response['flag_recurrency_payment'] = true;
                    $response['flag_initial_payment'] = true;
                } else {
                    $response['flag_recurrency_payment'] = false;
                    $response['flag_initial_payment'] = false;
                    if(is_array($resp))
                        $response['message'] = 'Error: '.$resp["message"]; 
                    else
                        $response['message'] = 'Incorrect credit card datas!!';
                    if(is_object($resp) && isset($resp->getData()->OrderResult->OrderKey)) {                        
                        $this->client_model->update_client($datas['pk'], array('order_key' => $resp->getData()->OrderResult->OrderKey));
                    }
                }
            } else
        //INSTA-DIRECT
        if(isset($datas['ticket_peixe_urbano']) && ($datas['ticket_peixe_urbano']==='INSTA-DIRECT' || $datas['ticket_peixe_urbano']==='MALADIRETA')){
                $datas['amount_in_cents'] = $recurrency_value;
                if ($datas['early_client_canceled'] === 'true'){
                    $resp = $this->check_mundipagg_credit_card($datas);
                    if(!(is_object($resp) && $resp->isSuccess()&& $resp->getData()->CreditCardTransactionResultCollection[0]->CapturedAmountInCents>0)){
                        $response['flag_recurrency_payment'] = false;
                        $response['flag_initial_payment'] = false;
                        if(is_array($resp))
                            $response['message'] = 'Error: '.$resp["message"]; 
                        else
                            $response['message'] = 'Incorrect credit card datas!!';
                        return $response;
                    } else{
                        $datas['pay_day'] = strtotime("+1 month", time());
                    }
                } else{
                    $datas['pay_day'] = strtotime("+" .'7'. " days", time());
                }                          
                $resp = $this->check_recurrency_mundipagg_credit_card($datas,0);
                if (is_object($resp) && $resp->isSuccess()) {
                    $this->client_model->update_client($datas['pk'], array(
                        'order_key' => $resp->getData()->OrderResult->OrderKey,
                        'pay_day' => $datas['pay_day']));
                    $response['flag_recurrency_payment'] = true;
                    $response['flag_initial_payment'] = true;
                } else {
                    $response['flag_recurrency_payment'] = false;
                    $response['flag_initial_payment'] = false;
                    if(is_array($resp))
                        $response['message'] = 'Error: '.$resp["message"]; 
                    else
                        $response['message'] = 'Incorrect credit card datas!!';
                    if(is_object($resp) && isset($resp->getData()->OrderResult->OrderKey)) {                        
                        $this->client_model->update_client($datas['pk'], array('order_key' => $resp->getData()->OrderResult->OrderKey));
                    }
                }
            }else  
        //INSTA15D
        if(isset($datas['ticket_peixe_urbano']) && $datas['ticket_peixe_urbano']==='INSTA15D'){
                $datas['amount_in_cents'] = $recurrency_value;
                if ($datas['early_client_canceled'] === 'true'){
                    $resp = $this->check_mundipagg_credit_card($datas);
                    if(!(is_object($resp) && $resp->isSuccess()&& $resp->getData()->CreditCardTransactionResultCollection[0]->CapturedAmountInCents>0)){
                        $response['flag_recurrency_payment'] = false;
                        $response['flag_initial_payment'] = false;
                        if(is_array($resp))
                            $response['message'] = 'Error: '.$resp["message"]; 
                        else
                            $response['message'] = 'Incorrect credit card datas!!';
                        return $response;
                    } else{
                        $datas['pay_day'] = strtotime("+1 month", time());
                    }
                } else{
                    $datas['pay_day'] = strtotime("+" .'15'. " days", time());
                }
                $resp = $this->check_recurrency_mundipagg_credit_card($datas,0);
                if (is_object($resp) && $resp->isSuccess()) {
                    $this->client_model->update_client($datas['pk'], array(
                        'order_key' => $resp->getData()->OrderResult->OrderKey,
                        'pay_day' => $datas['pay_day']));
                    $response['flag_recurrency_payment'] = true;
                    $response['flag_initial_payment'] = true;
                } else {
                    $response['flag_recurrency_payment'] = false;
                    $response['flag_initial_payment'] = false;
                    if(is_array($resp))
                        $response['message'] = 'Error: '.$resp["message"]; 
                    else
                        $response['message'] = 'Incorrect credit card datas!!';
                    if(is_object($resp) && isset($resp->getData()->OrderResult->OrderKey)) {                        
                        $this->client_model->update_client($datas['pk'], array('order_key' => $resp->getData()->OrderResult->OrderKey));
                    }
                }
            } else
        //SIBITE30D
        if(isset($datas['ticket_peixe_urbano']) && $datas['ticket_peixe_urbano']==='SIBITE30D'){ //30 dias de graça
                $datas['amount_in_cents'] = $recurrency_value;
                if ($datas['early_client_canceled'] === 'true'){
                    $resp = $this->check_mundipagg_credit_card($datas);
                    if(!(is_object($resp) && $resp->isSuccess()&& $resp->getData()->CreditCardTransactionResultCollection[0]->CapturedAmountInCents>0)){
                        $response['flag_recurrency_payment'] = false;
                        $response['flag_initial_payment'] = false;
                        if(is_array($resp))
                            $response['message'] = 'Error: '.$resp["message"]; 
                        else
                            $response['message'] = 'Incorrect credit card datas!!';
                        return $response;
                    } else{
                        $datas['pay_day'] = strtotime("+1 month", time());
                    }
                } else{
                    $datas['pay_day'] = strtotime("+" .'30'. " days", time());
                }
                $resp = $this->check_recurrency_mundipagg_credit_card($datas,0);
                if (is_object($resp) && $resp->isSuccess()) {
                    $this->client_model->update_client($datas['pk'], array(
                        'order_key' => $resp->getData()->OrderResult->OrderKey,
                        'pay_day' => $datas['pay_day']));
                    $response['flag_recurrency_payment'] = true;
                    $response['flag_initial_payment'] = true;
                } else {
                    $response['flag_recurrency_payment'] = false;
                    $response['flag_initial_payment'] = false;
                    if(is_array($resp))
                        $response['message'] = 'Error: '.$resp["message"]; 
                    else
                        $response['message'] = 'Incorrect credit card datas!!';
                    if(is_object($resp) && isset($resp->getData()->OrderResult->OrderKey)) {
                        $this->client_model->update_client($datas['pk'], array('order_key' => $resp->getData()->OrderResult->OrderKey));
                    }
                }
            }else
        //FREE5
        if(isset($datas['ticket_peixe_urbano']) && $datas['ticket_peixe_urbano']==='FREE5'){ //30 dias de graça
                $datas['amount_in_cents'] = $recurrency_value;
                if ($datas['early_client_canceled'] === 'true'){
                    $resp = $this->check_mundipagg_credit_card($datas);
                    if(!(is_object($resp) && $resp->isSuccess()&& $resp->getData()->CreditCardTransactionResultCollection[0]->CapturedAmountInCents>0)){
                        $response['flag_recurrency_payment'] = false;
                        $response['flag_initial_payment'] = false;
                        if(is_array($resp))
                            $response['message'] = 'Error: '.$resp["message"]; 
                        else
                            $response['message'] = 'Incorrect credit card datas!!';
                        return $response;
                    } else{
                        $datas['pay_day'] = strtotime("+1 month", time());
                    }
                } else{
                    $datas['pay_day'] = strtotime("+" .'5'. " days", time());
                }
                $resp = $this->check_recurrency_mundipagg_credit_card($datas,0);
                if(is_object($resp) && $resp->isSuccess()) {
                    $this->client_model->update_client($datas['pk'], array(
                        'order_key' => $resp->getData()->OrderResult->OrderKey,
                        'pay_day' => $datas['pay_day']));
                    $response['flag_recurrency_payment'] = true;
                    $response['flag_initial_payment'] = true;
                } else {
                    $response['flag_recurrency_payment'] = false;
                    $response['flag_initial_payment'] = false;
                    if(is_array($resp))
                        $response['message'] = 'Error: '.$resp["message"]; 
                    else
                        $response['message'] = 'Incorrect credit card datas!!';
                    if(is_object($resp) && isset($resp->getData()->OrderResult->OrderKey)) {
                        $this->client_model->update_client($datas['pk'], array('order_key' => $resp->getData()->OrderResult->OrderKey));
                    }
                }
            }else
        //FREE7DAYS
        if(isset($datas['ticket_peixe_urbano']) && $datas['ticket_peixe_urbano']==='FREE7DAYS'){ //30 dias de graça
                $datas['amount_in_cents'] = $recurrency_value;
                if ($datas['early_client_canceled'] === 'true'){
                    $resp = $this->check_mundipagg_credit_card($datas);
                    if(!(is_object($resp) && $resp->isSuccess()&& $resp->getData()->CreditCardTransactionResultCollection[0]->CapturedAmountInCents>0)){
                        $response['flag_recurrency_payment'] = false;
                        $response['flag_initial_payment'] = false;
                        if(is_array($resp))
                            $response['message'] = 'Error: '.$resp["message"]; 
                        else
                            $response['message'] = 'Incorrect credit card datas!!';
                        return $response;
                    } else{
                        $datas['pay_day'] = strtotime("+1 month", time());
                    }
                } else{
                    $datas['pay_day'] = strtotime("+" .'7'. " days", time());
                }
                $resp = $this->check_recurrency_mundipagg_credit_card($datas,0);
                if(is_object($resp) && $resp->isSuccess()) {
                    $this->client_model->update_client($datas['pk'], array(
                        'order_key' => $resp->getData()->OrderResult->OrderKey,
                        'pay_day' => $datas['pay_day']));
                    $response['flag_recurrency_payment'] = true;
                    $response['flag_initial_payment'] = true;
                } else {
                    $response['flag_recurrency_payment'] = false;
                    $response['flag_initial_payment'] = false;
                    if(is_array($resp))
                        $response['message'] = 'Error: '.$resp["message"]; 
                    else
                        $response['message'] = 'Incorrect credit card datas!!';
                    if(is_object($resp) && isset($resp->getData()->OrderResult->OrderKey)) {
                        $this->client_model->update_client($datas['pk'], array('order_key' => $resp->getData()->OrderResult->OrderKey));
                    }
                }
            }else
        //BACKTODUMBU
        if(isset($datas['ticket_peixe_urbano']) && (strtoupper($datas['ticket_peixe_urbano'])==='BACKTODUMBU' || strtoupper($datas['ticket_peixe_urbano'])==='BACKTODUMBU-DNLO' ||strtoupper($datas['ticket_peixe_urbano'])==='BACKTODUMBU-EGBTO') && ($datas['early_client_canceled'] === 'true' || $datas['early_client_canceled'] === true) ){
                //cobro la mitad en la hora
                $datas['pay_day'] = time();
                $datas['amount_in_cents'] = $recurrency_value/2;
                $resp = $this->check_mundipagg_credit_card($datas);
                if(is_object($resp) && $resp->isSuccess()&& $resp->getData()->CreditCardTransactionResultCollection[0]->CapturedAmountInCents>0){
                    $this->client_model->update_client(
                            $datas['pk'], 
                            array('initial_order_key' => $resp->getData()->OrderResult->OrderKey));                    
                    $response['flag_initial_payment'] = true;
                    //genero una recurrencia un mes mas alante
                    $datas['amount_in_cents'] = $recurrency_value;
                    $datas['pay_day'] = strtotime("+1 month", $datas['pay_day']);
                    $resp = $this->check_recurrency_mundipagg_credit_card($datas, 0);
                    if (is_object($resp) && $resp->isSuccess()) {
                        $this->client_model->update_client($datas['pk'], array(
                            'order_key' => $resp->getData()->OrderResult->OrderKey,
                            'pay_day' => $datas['pay_day']));
                        $response['flag_recurrency_payment'] = true;
                    } else {
                        $response['flag_recurrency_payment'] = false;
                        if(is_array($resp))
                            $response['message'] = 'Error: '.$resp["message"]; 
                        else
                            $response['message'] = 'Incorrect credit card datas!!';
                        if(is_object($resp) && isset($resp->getData()->OrderResult->OrderKey)) {                        
                            $this->client_model->update_client($datas['pk'], array('order_key' => $resp->getData()->OrderResult->OrderKey));
                        }
                    }
                } else{
                    $response['flag_recurrency_payment'] = false;
                    $response['flag_initial_payment'] = false;
                    if(is_array($resp))
                        $response['message'] = 'Error: '.$resp["message"]; 
                    else
                        $response['message'] = 'Incorrect credit card datas!!';
                    if(is_object($resp) && isset($resp->getData()->OrderResult->OrderKey)) {                        
                        $this->client_model->update_client($datas['pk'], array('initial_order_key' => $resp->getData()->OrderResult->OrderKey));
                    }
                }
            } else { //si es un cliente sin codigo promocional
                    $datas['amount_in_cents'] = $recurrency_value;
                    if ($datas['early_client_canceled'] === 'true'){
                        $resp = $this->check_mundipagg_credit_card($datas);
                        if(!(is_object($resp) && $resp->isSuccess()&& $resp->getData()->CreditCardTransactionResultCollection[0]->CapturedAmountInCents>0)){
                            $response['flag_recurrency_payment'] = false;
                            $response['flag_initial_payment'] = false;
                            if(is_array($resp))
                                $response['message'] = 'Error: '.$resp["message"]; 
                            else
                                $response['message'] = 'Incorrect credit card datas!!';
                            return $response;
                        } else{
                            $datas['pay_day'] = strtotime("+1 month", time());
                        }
                    } else{
                        $datas['pay_day'] = strtotime("+" . $GLOBALS['sistem_config']->PROMOTION_N_FREE_DAYS . " days", time());
                    }       

                    $resp = $this->check_recurrency_mundipagg_credit_card($datas, 0);
                    if (is_object($resp) && $resp->isSuccess()) {
                        $this->client_model->update_client($datas['pk'], array(
                            'order_key' => $resp->getData()->OrderResult->OrderKey,
                            'pay_day' => $datas['pay_day']));
                        $response['flag_recurrency_payment'] = true;
                        $response['flag_initial_payment'] = true;
                    } else {
                        $response['flag_recurrency_payment'] = false;
                        $response['flag_initial_payment'] = false;
                        if(is_array($resp))
                            $response['message'] = 'Error: '.$resp["message"];
                        else
                            $response['message'] = 'Incorrect credit card datas!!';
                        if(is_object($resp) && isset($resp->getData()->OrderResult->OrderKey)) {
                            $this->client_model->update_client($datas['pk'], array('order_key' => $resp->getData()->OrderResult->OrderKey));
                        }
                    }
            }
         return $response;
    }
    
    public function check_recurrency_mundipagg_credit_card($datas, $cnt) {
        $payment_data['credit_card_number'] = $datas['credit_card_number'];
        $payment_data['credit_card_name'] = $datas['credit_card_name'];
        $payment_data['credit_card_exp_month'] = $datas['credit_card_exp_month'];
        $payment_data['credit_card_exp_year'] = $datas['credit_card_exp_year'];
        $payment_data['credit_card_cvc'] = $datas['credit_card_cvc'];
        $payment_data['amount_in_cents'] = $datas['amount_in_cents'];
        $payment_data['pay_day'] = $datas['pay_day'];
        require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/Payment.php';
        $Payment = new \dumbu\cls\Payment();
        $bandeira = $this->detectCardType($payment_data['credit_card_number']);
        
        if ($bandeira) {
            if ($bandeira == "Visa" || $bandeira == "Mastercard") {
                //5 Cielo -> 1.5 | 32 -> eRede | 20 -> Stone | 42 -> Cielo 3.0 | 0 -> Auto;        
                $response = $Payment->create_recurrency_payment($payment_data, $cnt, 20);
                
                if (is_object($response) && $response->isSuccess()) {
                    return $response;
                } else {
                    $response = $Payment->create_recurrency_payment($payment_data, $cnt, 42);
                }
            }
            else if ($bandeira == "Hipercard") {
                $response = $Payment->create_recurrency_payment($payment_data, $cnt, 20);
            }
            else {
                $response = $Payment->create_recurrency_payment($payment_data, $cnt, 42);
            }
        }
        else {
            $response = array("message" => $this->T("Confira seu número de cartão e se está certo entre em contato com o atendimento.", array(), $GLOBALS['language']));
        }
        
        return $response;
    }

    public function delete_recurrency_payment($order_key) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/Payment.php';
        $Payment = new \dumbu\cls\Payment();
        $response = $Payment->delete_payment($order_key);
        return $response;
    }

    public function unfollow_total() {
        $this->load->model('class/user_role');
        $this->load->model('class/client_model');
        if ($this->session->userdata('role_id') == user_role::CLIENT) {
            $datas = $this->input->post();
            $datas['unfollow_total'] = (int) $datas['unfollow_total'];
            //if($this->session->userdata('unfollow_total')==!$datas['unfollow_total']){
            if ($datas['unfollow_total'] == 1) {
                
            } elseif ($datas['unfollow_total'] == 0) {
                
            }
            
            ($datas['unfollow_total']==0)?$ut='DISABLED':$ut='ACTIVATED';
            $this->load->model('class/user_model');
            $this->user_model->insert_washdog($this->session->userdata('id'),'TOTAL UNFOLLOW '.$ut);
            $this->user_model->insert_watchdog($this->session->userdata('id'),'TOTAL UNFOLLOW '.$ut);
            
            $this->client_model->update_client($this->session->userdata('id'), array(
                'unfollow_total' => $datas['unfollow_total']
            ));
            $response['success'] = true;
            $response['unfollow_total'] = $datas['unfollow_total'];
            
        }
        echo json_encode($response);
    }
    
    public function autolike() {
        $this->load->model('class/user_role');
        $this->load->model('class/client_model');
        if ($this->session->userdata('role_id') == user_role::CLIENT) {
            $datas = $this->input->post();
            $al=(int) $datas['autolike'];
            $this->client_model->update_client($this->session->userdata('id'), array(
                'like_first' => $al
            ));
            
            ($al==0)?$ut='DISABLED':$ut='ACTIVATED';
            $this->load->model('class/user_model');
            $this->user_model->insert_washdog($this->session->userdata('id'),'AUTOLIKE '.$ut);
            $this->user_model->insert_watchdog($this->session->userdata('id'),'AUTOLIKE '.$ut);
            
            $response['success'] = true;
            $response['autolike'] = $datas['AUTOLIKE'];
        }
        echo json_encode($response);
    }
    
    public function play_pause() {
        $this->load->model('class/user_role');
        $this->load->model('class/client_model');
        if ($this->session->userdata('role_id') == user_role::CLIENT) {
            $datas = $this->input->post();
            $pp = (int) $datas['play_pause'];
            $this->client_model->update_client($this->session->userdata('id'), array(
                'paused' => $pp
            ));
            
            $ut = 'PAUSED';
            
            if ($pp == 1) {
                $ut = 'PAUSED';
                $active_profiles = $this->client_model->get_client_active_profiles($this->session->userdata('id'));
                $N = count($active_profiles);
                //quitar trabajo si el cliente pauso la herramienta
                for ($i = 0; $i < $N; $i++) {
                    $this->client_model->delete_work_of_profile($active_profiles[$i]['id']);
                }
            }
            else {
                $ut = 'REACTIVATED';
                //no hacer nada, el robot le pone trabajo al cliente al siguiente dia
            }
            
            $this->load->model('class/user_model');
            $this->user_model->insert_washdog($this->session->userdata('id'),'TOOL '.$ut);
            $this->user_model->insert_watchdog($this->session->userdata('id'),'TOOL '.$ut);

            
            $response['success'] = true;
            $response['play_pause'] = $datas['play_pause'];
        }
        echo json_encode($response);
    }
    
    public function update_client_datas() {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/system_config.php';
        $GLOBALS['sistem_config'] = new dumbu\cls\system_config();
        $language=$this->input->get();
        if(isset($language['language']))
            $param['language']=$language['language'];
        else
            $param['language'] = $GLOBALS['sistem_config']->LANGUAGE;
        $param['SERVER_NAME'] = $GLOBALS['sistem_config']->SERVER_NAME;        
        $GLOBALS['language']=$param['language'];
        
        if ($this->session->userdata('id')) {
            $this->load->model('class/client_model');
            $this->load->model('class/user_model');
            $this->load->model('class/user_status');
            $this->load->model('class/credit_card_status');
            $datas = $this->input->post();
            $now = time();
            if($this->validate_post_credit_card_datas($datas)) {
                $client_data = $this->client_model->get_client_by_id($this->session->userdata('id'))[0];                               
                if($now<$client_data['pay_day'] && 
                        (  $client_data['ticket_peixe_urbano']==='AGENCIALUUK'
                        || $client_data['ticket_peixe_urbano']==='DUMBUDF20'
                        || $client_data['ticket_peixe_urbano']==='AMIGOSDOPEDRO'
                        || $client_data['ticket_peixe_urbano']==='BACKTODUMBU' 
                        )){                    
                    $result['success'] = false;
                    $result['message'] = 'Você não pode atualizar no primeiro mês, entre em contato com nosso atendimento';
                } else {
                    if ($this->session->userdata('status_id') == user_status::BLOCKED_BY_PAYMENT) {
                        if ($now < $client_data['pay_day']) {
                            $payments_days['pay_day'] = strtotime("+30 days", $now);
                            $payments_days['pay_now'] = true;
                            $datas['pay_day'] = $payments_days['pay_day'];
                        } else {
                            $payments_days['pay_day'] = time();
                            $payments_days['pay_now'] = false;
                            $datas['pay_day'] = $payments_days['pay_day'];
                        }
                    } else {
                        $payments_days = $this->get_pay_day($client_data['pay_day']);
                        $datas['pay_day'] = $payments_days['pay_day'];
                    }
                    if ($payments_days['pay_day'] != null) { //dia de actualizacion diferente de dia de pagamento                    
                        try {
                            $this->user_model->update_user($this->session->userdata('id'), array(
                                'email' => $datas['client_email']));
                            $this->client_model->update_client($this->session->userdata('id'), array(
                                'credit_card_number' => $datas['credit_card_number'],
                                'credit_card_cvc' => $datas['credit_card_cvc'],
                                'credit_card_name' => $datas['credit_card_name'],
                                'credit_card_exp_month' => $datas['credit_card_exp_month'],
                                'credit_card_exp_year' => $datas['credit_card_exp_year'],
                                'pay_day' => $datas['pay_day']
                            ));
                        } catch (Exception $exc) {
                            $result['success'] = false;
                            $result['exception'] = $exc->getTraceAsString();
                            $result['message'] = $this->T('Erro actualizando em banco de dados', array(), $GLOBALS['language']);
                        } finally {
                            $flag_pay_now = false;
                            $flag_pay_day = false;

                            //Determinar valor inicial del pagamento
                            if ($datas['client_update_plane'] == 1)
                                $datas['client_update_plane'] = 4;
                            if ($now < $client_data['pay_day'] && ($datas['client_update_plane'] <= $this->session->userdata('plane_id'))) {
                                $pay_values['initial_value'] = $this->client_model->get_promotional_pay_value($datas['client_update_plane']);
                                $pay_values['normal_value'] = $this->client_model->get_normal_pay_value($datas['client_update_plane']);
                            } else
                            if ($now < $client_data['pay_day'] && ($datas['client_update_plane'] > $this->session->userdata('plane_id'))) {
                                $pay_values['initial_value'] = $this->client_model->get_promotional_pay_value($datas['client_update_plane']) - $this->client_model->get_promotional_pay_value($this->session->userdata('plane_id'));
                                $pay_values['normal_value'] = $this->client_model->get_normal_pay_value($datas['client_update_plane']);
                            } else
                            if ($datas['client_update_plane'] > $this->session->userdata('plane_id')) {
                                $promotional_time_range = $this->user_model->get_signin_date($this->session->userdata('id'));
                                $promotional_time_range = strtotime("+" . $GLOBALS['sistem_config']->PROMOTION_N_FREE_DAYS . " days", $promotional_time_range);
                                $promotional_time_range = strtotime("+1 month", $promotional_time_range);
                                if (time() < $promotional_time_range) {//mes promocional
                                    $pay_values['initial_value'] = $this->client_model->get_promotional_pay_value($datas['client_update_plane']) - $this->client_model->get_promotional_pay_value($this->session->userdata('plane_id'));
                                } else {
                                    $pay_values['initial_value'] = $this->client_model->get_normal_pay_value($datas['client_update_plane']) - $this->client_model->get_normal_pay_value($this->session->userdata('plane_id'));
                                }
                                $pay_values['normal_value'] = $this->client_model->get_normal_pay_value($datas['client_update_plane']);
                                $payments_days['pay_now'] = true;
                            } else
                            if ($datas['client_update_plane'] < $this->session->userdata('plane_id')) {
                                $pay_values['initial_value'] = $this->client_model->get_normal_pay_value($datas['client_update_plane']);
                                $pay_values['normal_value'] = $this->client_model->get_normal_pay_value($datas['client_update_plane']);
                            } else {
                                $pay_values['initial_value'] = $this->client_model->get_normal_pay_value($this->session->userdata('plane_id'));
                                
                                if($client_data['actual_payment_value']!=null)
                                    $pay_values['normal_value'] = $client_data['actual_payment_value'];
                                else
                                    $pay_values['normal_value'] = $this->client_model->get_normal_pay_value($this->session->userdata('plane_id'));
                            }
                            
                            //si necesitara hacer un pagamento ahora
                            if ($payments_days['pay_now']) {                                                    
                                $datas['pay_day'] = time();
                                if($client_data['ticket_peixe_urbano']==='AGENCIALUUK' || $client_data['ticket_peixe_urbano']==='DUMBUDF20') 
                                    $datas['amount_in_cents'] = round(($pay_values['initial_value']*8)/10);
                                else
                                if($client_data['ticket_peixe_urbano']==='OLX')
                                    //$datas['amount_in_cents'] = round(($pay_values['initial_value']*5)/10);
                                    if($now < $client_data['pay_day'])
                                        $datas['amount_in_cents'] = $pay_values['normal_value']/2;
                                    else
                                        $datas['amount_in_cents'] = $pay_values['normal_value'];
                                else
                                    $datas['amount_in_cents'] = $pay_values['initial_value'];
                                $resp_pay_now = $this->check_mundipagg_credit_card($datas);
                                if (is_object($resp_pay_now) && $resp_pay_now->isSuccess() && $resp_pay_now->getData()->CreditCardTransactionResultCollection[0]->CapturedAmountInCents>0) {
                                    $this->client_model->update_client($this->session->userdata('id'), array(
                                        'pending_order_key' => $resp_pay_now->getData()->OrderResult->OrderKey));
                                    $flag_pay_now = true;
                                }
                            }

                            if (($payments_days['pay_now'] && $flag_pay_now) || !$payments_days['pay_now']) {
                                $response_delete_early_payment = '';
                                $datas['pay_day'] = $payments_days['pay_day'];
                                if($client_data['ticket_peixe_urbano']==='AGENCIALUUK' || $client_data['ticket_peixe_urbano']==='DUMBUDF20')
                                    $datas['amount_in_cents'] = round(($pay_values['normal_value']*8)/10);
                                else
                                    $datas['amount_in_cents'] = $pay_values['normal_value'];

                                $resp_pay_day = $this->check_recurrency_mundipagg_credit_card($datas, 0);
                                if (is_object($resp_pay_day) && $resp_pay_day->isSuccess()) {
                                    $flag_pay_day = true;
                                    try {
                                        $this->client_model->update_client($this->session->userdata('id'), array(
                                            'plane_id' => $datas['client_update_plane'],
                                            'pay_day' => $datas['pay_day'],
                                            'order_key' => $resp_pay_day->getData()->OrderResult->OrderKey));
                                        if ($client_data['order_key'])
                                            $response_delete_early_payment = $this->delete_recurrency_payment($client_data['order_key']);
                                        if ($this->session->userdata('status_id') == user_status::BLOCKED_BY_PAYMENT || $this->session->userdata('status_id') == user_status::PENDING) {
                                            $datas['status_id'] = user_status::ACTIVE; //para que Payment intente hacer el pagamento y si ok entonces lo active y le ponga trabajo
                                        } else
                                            $datas['status_id'] = $this->session->userdata('status_id');
                                        $this->user_model->update_user($this->session->userdata('id'), array(
                                            'status_id' => $datas['status_id']));
                                        if ($this->session->userdata('status_id') == user_status::BLOCKED_BY_PAYMENT) {
                                            $active_profiles = $this->client_model->get_client_active_profiles($this->session->userdata('id'));
                                            $N = count($active_profiles);
                                            for ($i = 0; $i < $N; $i++) {
                                                if($active_profiles[$i]['end_date']!=='NULL')
                                                $this->client_model->insert_profile_in_daily_work($active_profiles[$i]['id'], $this->session->userdata('insta_datas'), $i, $active_profiles, $this->session->userdata('to_follow'));
                                            }
                                        }
                                        $this->session->set_userdata('plane_id', $datas['client_update_plane']);
                                        $this->session->set_userdata('status_id', $datas['status_id']);
                                    } catch (Exception $exc) {
                                        $this->user_model->update_user($datas['pk'], array(
                                            'status_id' => $this->session->userdata('status_id'))); //the previous
                                        $this->client_model->update_client($datas['pk'], array(
                                            'pay_day' => $client_data['pay_day'], //the previous
                                            'order_key' => $client_data['order_key'])); //the previous
                                        $result['success'] = false;
                                        $result['exception'] = $exc->getTraceAsString();
                                        $result['message'] = $this->T('Erro actualizando em banco de dados', array(), $GLOBALS['language']);
                                    } finally {
                                        $result['success'] = true;
                                        $result['resource'] = 'client';
                                        $result['message'] = $this->T('Dados bancários atualizados corretamente', array(), $GLOBALS['language']);
                                        $result['response_delete_early_payment'] = $response_delete_early_payment;
                                    }
                                }
                            }

                            if (($payments_days['pay_now'] && !$flag_pay_now) || (!$payments_days['pay_now'] && !$flag_pay_day)) {
                                //restablecer en la base de datos los datos anteriores
                                $this->client_model->update_client($this->session->userdata('id'), array(
                                    'credit_card_number' => $client_data['credit_card_number'],
                                    'credit_card_cvc' => $client_data['credit_card_cvc'],
                                    'credit_card_name' => $client_data['credit_card_name'],
                                    'credit_card_exp_month' => $client_data['credit_card_exp_month'],
                                    'credit_card_exp_year' => $client_data['credit_card_exp_year'],
                                    'pay_day' => $client_data['pay_day'],
                                    'order_key' => $client_data['order_key']
                                ));
                                $result['success'] = false;
                                $result['resource'] = 'client';
                                if ($payments_days['pay_now'] && !$flag_pay_now)
                                    $result['message'] = is_array($resp_pay_now) ? $resp_pay_now["message"] : $this->T("Erro inesperado! Provávelmente Cartão inválido, entre em contato com o atendimento.", array(), $GLOBALS['language']);
                                else
                                    $result['message'] = is_array($resp_pay_day) ? $resp_pay_day["message"] : $this->T("Erro inesperado! Provávelmente Cartão inválido, entre em contato com o atendimento.", array(), $GLOBALS['language']);
                            } else
                            if (($payments_days['pay_now'] && $flag_pay_now && !$flag_pay_day)) {
                                //se hiso el primer pagamento bien, pero la recurrencia mal
                                $result['success'] = true;
                                $result['resource'] = 'client';
                                $result['message'] = $this->T('Actualização bem sucedida, mas deve atualizar novamente até a data de pagamento ( @1 )', array(0 => $payments_days['pay_now']));
                            }
                        }
                    } else {
                        $result['success'] = false;
                        $result['message'] = $this->T('Você não pode atualizar seu cartão no dia do pagamento', array(), $GLOBALS['language']);
                    }
                }
                
            } else {
                $result['success'] = false;
                $result['message'] = $this->T('Acesso não permitido', array(), $GLOBALS['language']);
            }
            
            if($this->session->userdata('id') && $result['success'] == true){
                $this->load->model('class/user_model');
                 $this->user_model->insert_washdog($this->session->userdata('id'),'CORRECT CARD UPDATE');
                 $this->user_model->insert_watchdog($this->session->userdata('id'),'CORRECT CARD UPDATE');
            } else{
                if($this->session->userdata('id')){
                    $this->load->model('class/user_model');
                    $this->user_model->insert_washdog($this->session->userdata('id'),'INCORRECT CARD UPDATE');
                    $this->user_model->insert_watchdog($this->session->userdata('id'),'INCORRECT CARD UPDATE');
                }
            }
            
            echo json_encode($result);
        }
    }

    public function get_pay_day($pay_day) {
        $this->load->model('class/user_status');
        $now = time();
        $datas['pay_now'] = false;

        $d_today = date("j", $now);
        $m_today = date("n", $now);
        $y_today = date("Y", $now);
        $d_pay_day = date("j", $pay_day);
        $m_pay_day = date("n", $pay_day);
        $y_pay_day = date("Y", $pay_day);

        if ($now < $pay_day) {
            $datas['pay_day'] = $pay_day;
        } else
        if ($d_today < $d_pay_day) {
            if ($this->session->userdata('status_id') == (string) user_status::PENDING)
                $datas['pay_now'] = true;
            //1. mes anterior respecto a hoy
            $previous_month = strtotime("-30 days", $now);
            //var_dump(date('d-m-Y',$previous_month));
            //2. dia de pagamento en el mes anterior al actual
            $previous_payment_date = strtotime($d_pay_day . '-' . date("n", $previous_month) . '-' . date("Y", $previous_month));
            //var_dump(date('d-m-Y',$previous_payment_date));
            //3. nuevo dia de pagamento para el mes actual
            $datas['pay_day'] = strtotime("+30 days", $previous_payment_date);
            //var_dump(date('d-m-Y',$datas['pay_day']));
        } else
        if ($d_today > $d_pay_day) {
            //0. si pendiente por pagamento, inidcar que se debe hacer pagamento
            //if($this->session->userdata('status_id') == user_status::PENDING)                
            if ($this->session->userdata('status_id') == (string) user_status::PENDING)
                $datas['pay_now'] = true;
            $recorrency_date = strtotime($d_pay_day . '-' . $m_today . '-' . $y_today); //mes actual com el dia de pagamento
            //var_dump(date('d-m-Y',$recorrency_date));
            $datas['pay_day'] = strtotime("+30 days", $recorrency_date); //proximo mes
            //var_dump(date('d-m-Y',$datas['pay_day']));
        } else
            $datas['pay_day'] = false;
        return $datas;
    }

    
    //functions for geolocalizations
    public function client_insert_geolocalization() {
        if ($this->session->userdata('id')) {
            require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/system_config.php';
            $GLOBALS['sistem_config'] = new dumbu\cls\system_config();      
            $language=$this->input->get();
            if(isset($language['language']))
                $param['language']=$language['language'];
            else
                $param['language'] = $GLOBALS['sistem_config']->LANGUAGE;    
            $param['SERVER_NAME'] = $GLOBALS['sistem_config']->SERVER_NAME;  
            $GLOBALS['language']=$param['language'];            
            $this->load->model('class/client_model');
            $this->load->model('class/user_status');
            $profile = $this->input->post();
            $active_profiles = $this->client_model->get_client_active_profiles($this->session->userdata('id'));
            $N = count($active_profiles);
            $N_geolocalization=0;
            $is_active_profile = false;
            $is_active_geolocalization = false;
            for ($i = 0; $i < $N; $i++) {
                if($active_profiles[$i]['type']==='1' && $active_profiles[$i]['deleted']==='0')
                    $N_geolocalization=$N_geolocalization+1;
                if ($active_profiles[$i]['insta_name'] == $profile['geolocalization']) {
                    if($active_profiles[$i]['deleted'] == false)
                        if($active_profiles[$i]['type'] ==='0')
                            $is_active_profile=true;
                        elseif($active_profiles[$i]['type'] ==='1')
                            $is_active_geolocalization=true;
                    break;
                }
            }
            if (/*!$is_active_profile &&*/ !$is_active_geolocalization) {
                if ($N_geolocalization < $GLOBALS['sistem_config']->REFERENCE_PROFILE_AMOUNT) {
                    //$profile_datas = $this->check_insta_profile($profile['geolocalization']);
                    $profile_datas = $this->check_insta_geolocalization($profile['geolocalization']);                    
                    
                    if($profile_datas) {                                                
                        //if(!$profile_datas->is_private) {
                            $p = $this->client_model->insert_insta_profile($this->session->userdata('id'), $profile_datas->slug, $profile_datas->location->pk, '1');
                            if ($p) {
                                if ($this->session->userdata('status_id') == user_status::ACTIVE && $this->session->userdata('insta_datas'))
                                    $q = $this->client_model->insert_profile_in_daily_work($p, $this->session->userdata('insta_datas'), $N, $active_profiles, $this->session->userdata('to_follow'));
                                else
                                    $q = true;
                                //$profile_datas = $this->check_insta_profile($profile['geolocalization'], $p);
                                $result['success'] = true;
                                $result['img_url'] = base_url().'assets/images/avatar_geolocalization_present.jpg';
                                $result['profile'] = $profile['geolocalization'];
                                $result['geolocalization_pk'] = $profile_datas->location->pk;
                                $result['follows_from_profile'] = 0;
                                if ($q) {
                                    $result['message'] = $this->T('Geolocalização adicionada corretamente', array(), $GLOBALS['language']);
                                } else {
                                    $result['message'] = $this->T('O trabalho com a geolocalização começara depois', array(), $GLOBALS['language']);
                                }
                            } else {
                                $result['success'] = false;
                                $result['message'] = $this->T('Erro no sistema, tente novamente', array(), $GLOBALS['language']);
                            }
                        /*} else {
                            $result['success'] = false;
                            $result['message'] = $this->T('A geolocalização @1 é um perfil privado', array(0 => $profile['geolocalization']));
                        }*/
                    } else {
                        $result['success'] = false;
                        $result['message'] = $this->T('@1 não é uma geolocalização do Instagram', array(0 => $profile['geolocalization']));
                    }
                } else {
                    $result['success'] = false;
                    $result['message'] = $this->T('Você alcançou a quantidade máxima de geolocalizações ativas', array(), $GLOBALS['language']);
                }
            } else {
                $result['success'] = false;                    
                if($is_active_profile)
                    $result['message'] = $this->T('A geolocalização informada é um perfil ativo', array(), $GLOBALS['language']);
                else
                    $result['message']=$this->T('A geolocalizaçao informada ja está ativa', array(), $GLOBALS['language']);                
            }
            
            if( $result['success'] == true){
                $this->load->model('class/user_model');
                // $this->user_model->insert_washdog($this->session->userdata('id'),'GEOCALIZATION INSERTED '.$profile['geolocalization']);
                $this->user_model->insert_washdog($this->session->userdata('id'),'GEOCALIZATION INSERTED');
                $this->user_model->insert_watchdog($this->session->userdata('id'),'GEOCALIZATION INSERTED');
            }
            echo json_encode($result);
        }
    }
        
    public function client_desactive_geolocalization() {
        if ($this->session->userdata('id')) {
            require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/system_config.php';
            $GLOBALS['sistem_config'] = new dumbu\cls\system_config(); 
            $language=$this->input->get();
            if(isset($language['language']))
                $param['language']=$language['language'];
            else
                $param['language'] = $GLOBALS['sistem_config']->LANGUAGE;    
            $param['SERVER_NAME'] = $GLOBALS['sistem_config']->SERVER_NAME;
            $GLOBALS['language']=$param['language'];
            $this->load->model('class/client_model');
            $profile = $this->input->post();
            if ($this->client_model->desactive_profiles($this->session->userdata('id'), $profile['geolocalization'])) {
                $result['success'] = true;
                $result['message'] = $this->T('Geolocalização eliminada', array(), $GLOBALS['language']);
            } else {
                $result['success'] = false;
                $result['message'] = $this->T('Erro no sistema, tente novamente', array(), $GLOBALS['language']);
            }
            
            if( $result['success'] == true){
                $this->load->model('class/user_model');
                //$this->user_model->insert_washdog($this->session->userdata('id'),'GEOCALIZATION ELIMINATED '.$profile['geolocalization']);
                $this->user_model->insert_washdog($this->session->userdata('id'),'GEOCALIZATION ELIMINATED');
                $this->user_model->insert_watchdog($this->session->userdata('id'),'GEOCALIZATION ELIMINATED');
            }
            echo json_encode($result);
        }
    }
    
    public function check_insta_geolocalization($profile) {
        if ($this->session->userdata('id')) {
            require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/Robot.php';
            $this->Robot = new \dumbu\cls\Robot();
            $datas_of_profile = $this->Robot->get_insta_geolocalization_data_from_client(json_decode($this->session->userdata('cookies')),$profile);
            if (is_object($datas_of_profile)) {
                return $datas_of_profile;
            } else {
                return NULL;
            }
        }
    }

    
    //functions for reference profiles
    public function client_insert_profile() {
        $id = $this->session->userdata('id');
        if ($this->session->userdata('id')) {
            require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/system_config.php';
            $GLOBALS['sistem_config'] = new dumbu\cls\system_config();
            $language=$this->input->get();
            if(isset($language['language']))
                $param['language']=$language['language'];
            else
                $param['language'] = $GLOBALS['sistem_config']->LANGUAGE;    
            $param['SERVER_NAME'] = $GLOBALS['sistem_config']->SERVER_NAME;  
            $GLOBALS['language']=$param['language'];
            $this->load->model('class/client_model');
            $this->load->model('class/user_status');
            $profile = $this->input->post();
            $active_profiles = $this->client_model->get_client_active_profiles($this->session->userdata('id'));
            $N = count($active_profiles);
            $N_profiles=0;
            $is_active_profile = false;
            $is_active_geolocalization = false;
            for ($i = 0; $i < $N; $i++) {
                if($active_profiles[$i]['type']==='0' && $active_profiles[$i]['deleted']==='0')
                    $N_profiles=$N_profiles+1;
                if ($active_profiles[$i]['insta_name'] == $profile['profile']) {
                    if($active_profiles[$i]['deleted'] == false)
                        if($active_profiles[$i]['type'] ==='0')
                            $is_active_profile=true;
                        elseif($active_profiles[$i]['type'] ==='1')
                            $is_active_geolocalization=true;
                    break;
                }
            }
            if (!$is_active_profile/*&& !$is_active_geolocalization*/) {
                if ($N_profiles<$GLOBALS['sistem_config']->REFERENCE_PROFILE_AMOUNT) {
                    $profile_datas=$this->check_insta_profile_from_client($profile['profile']);
                    if($profile_datas) {
                        if(!$profile_datas->is_private) {
                            $p = $this->client_model->insert_insta_profile($this->session->userdata('id'), $profile['profile'], $profile_datas->pk, '0');
                            if ($p) {
                                if ($this->session->userdata('status_id') == user_status::ACTIVE && $this->session->userdata('insta_datas'))
                                    $q = $this->client_model->insert_profile_in_daily_work($p, $this->session->userdata('insta_datas'), $N, $active_profiles, $this->session->userdata('to_follow'));
                                else
                                    $q = true;
                                $result['success'] = true;
                                $result['img_url'] = $profile_datas->profile_pic_url;
                                $result['profile'] = $profile['profile'];
                                $result['follows_from_profile'] = $profile_datas->follows;
                                if ($q) {
                                    $result['message'] = $this->T('Perfil adicionado corretamente', array(), $GLOBALS['language']);
                                } else {
                                    $result['message'] = $this->T('O trabalho com o perfil começara depois', array(), $GLOBALS['language']);
                                }
                            } else {
                                $result['success'] = false;
                                $result['message'] = $this->T('Erro no sistema, tente novamente', array(), $GLOBALS['language']);
                            }
                        } else {
                            $result['success'] = false;
                            $result['message'] = $this->T('O perfil @1 é um perfil privado', array(0 => $profile['profile']),$GLOBALS['language']);
                        }                    
                    } else {
                        $result['success'] = false;
                        $result['message'] = $this->T('Confira que o perfil @1 existe no Instagram e não tem bloqueado você', array(0 => $profile['profile']),$GLOBALS['language']);
                    }
                } else {
                    $result['success'] = false;
                    $result['message'] = $this->T('Você alcançou a quantidade máxima de perfis ativos', array(), $GLOBALS['language']);
                }
            } else {
                $result['success'] = false;                    
                if($is_active_profile)
                    $result['message']=$this->T('O perfil informado ja está ativo', array(), $GLOBALS['language']);    
                else
                    $result['message'] = $this->T('O perfil informado é uma geolocalização ativa', array(), $GLOBALS['language']);                
            }
            
            if( $result['success'] == true){
                $this->load->model('class/user_model');
                //$this->user_model->insert_washdog($this->session->userdata('id'),'REFERENCE PROFILE INSERTED '.$profile['profile']);
                $this->user_model->insert_washdog($this->session->userdata('id'),'REFERENCE PROFILE INSERTED');
                $this->user_model->insert_watchdog($this->session->userdata('id'),'REFERENCE PROFILE INSERTED');
            }
            
            echo json_encode($result);
        }
    }

    public function client_desactive_profiles() {
        if ($this->session->userdata('id')) {
            require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/system_config.php';
            $GLOBALS['sistem_config'] = new dumbu\cls\system_config();
            $language=$this->input->get();
            if(isset($language['language']))
                $param['language']=$language['language'];
            else
                $param['language'] = $GLOBALS['sistem_config']->LANGUAGE;    
            $param['SERVER_NAME'] = $GLOBALS['sistem_config']->SERVER_NAME;  
            $GLOBALS['language']=$param['language'];
            
            $this->load->model('class/client_model');
            $profile = $this->input->post();
            if ($this->client_model->desactive_profiles($this->session->userdata('id'), $profile['profile'])) {
                $result['success'] = true;
                $result['message'] = $this->T('Perfil eliminado', array(), $GLOBALS['language']);
            } else {
                $result['success'] = false;
                $result['message'] = $this->T('Erro no sistema, tente novamente', array(), $GLOBALS['language']);
            }
            
            if( $result['success'] == true){
                $this->load->model('class/user_model');
                //$this->user_model->insert_washdog($this->session->userdata('id'),'REFERENCE PROFILE ELIMINATED '.$profile['profile']);
                $this->user_model->insert_washdog($this->session->userdata('id'),'REFERENCE PROFILE ELIMINATED');
                $this->user_model->insert_watchdog($this->session->userdata('id'),'REFERENCE PROFILE ELIMINATED');
            }
            
            echo json_encode($result);
        }
    }

    public function check_insta_profile($profile) {
        //if ($this->session->userdata('id')) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/Robot.php';
        $this->Robot = new \dumbu\cls\Robot();       
        $data = $this->Robot->get_insta_ref_prof_data($profile);
        if (is_object($data)) {
            return $data;
        } else {
            return NULL;
        }
        //}
    }    
    
    public function check_insta_profile_from_client($profile) {        
        require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/Robot.php';
        $this->Robot = new \dumbu\cls\Robot();       
        $data = $this->Robot->get_insta_ref_prof_data_from_client(json_decode($this->session->userdata('cookies')),$profile);
        if(is_object($data)){
            return $data;
        }
        else 
            if(is_string($data)){
                return json_decode($data);
            } else {
                return NULL;
            }
    }
    
    public function message_old() {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/system_config.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/Gmail.php';
        $GLOBALS['sistem_config'] = new dumbu\cls\system_config();
        $this->Gmail = new \dumbu\cls\Gmail();
        $language=$this->input->get();
        if(isset($language['language']))
            $param['language']=$language['language'];
        else
            $param['language'] = $GLOBALS['sistem_config']->LANGUAGE;    
        $param['SERVER_NAME'] = $GLOBALS['sistem_config']->SERVER_NAME;  
        $GLOBALS['language']=$param['language'];
        $datas = $this->input->post();
        $result = $this->Gmail->send_client_contact_form($datas['name'], $datas['email'], $datas['message'], $datas['company'], $datas['telf']);
        if ($result['success']) {
            $result['message'] = $this->T('Mensagem enviada, agradecemos seu contato', array(), $GLOBALS['language']);
        }
        echo json_encode($result);
    }
    
    public function email_success_buy_to_atendiment($username, $useremail) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/Gmail.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/system_config.php';
        $GLOBALS['sistem_config'] = new \dumbu\cls\system_config();
        $this->Gmail = new \dumbu\cls\Gmail();
        $result = $this->Gmail->send_new_client_payment_done($username, $useremail);
        if ($result['success'])
            return TRUE;
        return false;
    }

    public function email_success_buy_to_client($useremail, $username, $userlogin, $userpass) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/Gmail.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/system_config.php';
        $GLOBALS['sistem_config'] = new \dumbu\cls\system_config();
        $this->Gmail2 = new \dumbu\cls\Gmail();
        $result = $this->Gmail2->send_client_payment_success($useremail, $username, $userlogin, $userpass);
    }

    //auxiliar function
    public function validate_post_credit_card_datas($datas) {
        //TODO: validate emial and datas of credit card using regular expresions
        /* if (preg_match('^[0-9]{16,16}$',$datas['credit_card_number']) &&
          preg_match('^[0-9 ]{3,3}$',$datas['credit_card_cvc']) &&
          preg_match('^[A-Z ]{4,50}$',$datas['credit_card_name']) &&
          preg_match('^[0-10-9]{2,2}$',$datas['credit_card_exp_month']) &&
          preg_match('^[2-20-01-20-9]{4,4}$',$datas['credit_card_exp_year']) &&
          preg_match('^[a-zA-Z0-9\._-]+@([a-zA-Z0-9-]{2,}[.])*[a-zA-Z]{2,4}$',$datas['client_email']))
          return true;
          else
          return false; */
        return true;
    }

    public function is_insta_user($client_login, $client_pass, $force_login) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/Robot.php';
        $this->Robot = new \dumbu\cls\Robot();
        $data_insta = NULL;
        $login_data = $this->Robot->bot_login($client_login, $client_pass,$force_login);
        if (isset($login_data->json_response->status) && $login_data->json_response->status === "ok") {
            $data_insta['status'] = $login_data->json_response->status;
            if($login_data->json_response->authenticated) {
                $data_insta['authenticated'] = true;
                $data_insta['insta_id'] = $login_data->ds_user_id;
                
                //$user_data = $this->Robot->get_insta_ref_prof_data($client_login);
                
                $user_data = $this->Robot->get_insta_ref_prof_data_from_client($login_data,$client_login);
                
                if($data_insta && isset($user_data->follower_count))
                    $data_insta['insta_followers_ini'] = $user_data->follower_count;
                else
                    $data_insta['insta_followers_ini'] = 'Access denied';
                
                if($data_insta && isset($user_data->following))
                    $data_insta['insta_following'] = $user_data->following;
                else
                    $data_insta['insta_following'] = 'Access denied';
                
                if($data_insta && isset($user_data->full_name))
                    $data_insta['insta_name']=$user_data->full_name;
                else
                    $data_insta['insta_name']='Access denied';
                
                if(is_object($login_data))
                    $data_insta['insta_login_response'] = $login_data;
                else
                    $data_insta['insta_login_response'] = NULL;
            } else {
                $data_insta['authenticated'] = false;
                
                if($login_data->json_response->message) {
                    $data_insta['message'] = $login_data->json_response->message;
                    
                    if ($login_data->json_response->message === "checkpoint_required") {
                    $data_insta['message'] = $login_data->json_response->message;
                    if(strpos($login_data->json_response->verify_link,'challenge'))
                        $data_insta['verify_account_url'] = 'https://www.instagram.com'.$login_data->json_response->verify_link;
                    else
                    if(strpos($login_data->json_response->verify_link,'integrity'))
                        $data_insta['verify_account_url'] =$login_data->json_response->verify_link;
                    else
                        $data_insta['verify_account_url'] = $login_data->json_response->verify_link;
                    
                    } else
                    if ($login_data->json_response->message === "") {
                        if (isset($login_data->json_response->phone_verification_settings) && is_object($login_data->json_response->phone_verification_settings)) {
                            $data_insta['message'] = 'phone_verification_settings';
                            $data_insta['obfuscated_phone_number'] = $login_data->json_response->two_factor_info->obfuscated_phone_number;
                        } else {
                            $data_insta['message'] = 'empty_message';
                            $data_insta['cause'] = 'empty_message';
                        }
                    } else 
                    if ($login_data->json_response->message !== "incorrect_password") { 
                        $data_insta['message'] = 'unknow_message';
                        $data_insta['unknow_message'] = $login_data->json_response->message;
                    }
                }
            }
        } else {
            if (isset($login_data->json_response->status) && $login_data->json_response->status === "fail") {
                $data_insta['status'] = $login_data->json_response->status;
            } else
            if (isset($login_data->json_response->status) && $login_data->json_response->status === "") {
                ;
            }
        }
        return $data_insta;
    }

    //functions for load ad dispay the diferent funtionalities views 
    public function sign_client_update() {
        $this->load->model('class/user_role');
        if ($this->session->userdata('role_id') == user_role::CLIENT) {
            $data['user_active'] = true;
            $this->load->model('class/user_model');
            $this->load->model('class/client_model');
            $user_data = $this->user_model->get_user_by_id($this->session->userdata('id'))[0];
            $client_data = $this->client_model->get_client_by_id($this->session->userdata('id'))[0];
            $datas['upgradable_datas'] = array('email' => $user_data['email'],
                'credit_card_number' => $client_data['credit_card_number'],
                'credit_card_cvc' => $client_data['credit_card_cvc'],
                'credit_card_name' => $client_data['credit_card_name'],
                'credit_card_exp_month' => $client_data['credit_card_exp_month'],
                'credit_card_exp_year' => $client_data['credit_card_exp_year']);
            //$data['content_header'] = $this->load->view('my_views/users_header', '', true);
            $data['content'] = $this->load->view('my_views/client_update_painel', $datas, true);
            $data['content_footer'] = $this->load->view('my_views/general_footer', '', true);
            $this->load->view('welcome_message', $data);
        } else {
            $this->display_access_error();
        }
    }

    public function log_out() {
        $data['user_active'] = false;
        $this->load->model('class/user_model');
        $this->user_model->insert_washdog($this->session->userdata('id'),'CLOSING SESSION');
        $this->user_model->insert_watchdog($this->session->userdata('id'),'CLOSING SESSION');
        $this->session->sess_destroy();
        header('Location: ' . base_url());
    }

    public function create_profiles_datas_to_display() {
        if ($this->session->userdata('id')) {
            require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/Robot.php';
            $this->Robot = new \dumbu\cls\Robot();
            $this->load->model('class/client_model');
            $array_profiles=array();
            $array_geolocalization=array();
            $client_active_profiles = $this->client_model->get_client_active_profiles($this->session->userdata('id'));
            $N = count($client_active_profiles);
            $cnt_ref_prof=0;
            $cnt_geolocalization=0;            
            if ($N > 0) {
//                $array_profiles = array(0);   
                for ($i = 0; $i < $N; $i++) {
                    $name_profile = $client_active_profiles[$i]['insta_name'];
                    $id_profile = $client_active_profiles[$i]['id'];
                    if($client_active_profiles[$i]['type']==='0'){ //es un perfil de referencia
                    $datas_of_profile = $this->Robot->get_insta_ref_prof_data_from_client(json_decode($this->session->userdata('cookies')),$name_profile, $id_profile);
                    if($datas_of_profile!=NULL){
                            $array_profiles[$cnt_ref_prof]['login_profile'] = $name_profile;
                            $array_profiles[$cnt_ref_prof]['follows_from_profile'] = $datas_of_profile->follows;
                            if (!$datas_of_profile) { //perfil existia pero fue eliminado de IG
                                $array_profiles[$cnt_ref_prof]['status_profile'] = 'deleted';
                                $array_profiles[$cnt_ref_prof]['img_profile'] = base_url().'assets/images/profile_deleted.jpg';
                            } else
                            if ($client_active_profiles[$cnt_ref_prof]['end_date']) { //perfil
                                $array_profiles[$cnt_ref_prof]['status_profile'] = 'ended';
                                $array_profiles[$cnt_ref_prof]['img_profile'] = $datas_of_profile->profile_pic_url;
                            } else
                            if ($datas_of_profile->is_private) { //perfil paso a ser privado
                                $array_profiles[$cnt_ref_prof]['status_profile'] = 'privated';
                                $array_profiles[$cnt_ref_prof]['img_profile'] = base_url().'assets/images/profile_privated.jpg';
                            } else{
                                $array_profiles[$cnt_ref_prof]['status_profile'] = 'active';
                                $array_profiles[$cnt_ref_prof]['img_profile'] = $datas_of_profile->profile_pic_url;
                            }
                            $cnt_ref_prof=$cnt_ref_prof+1;
                        } else{
                            $array_profiles[$cnt_ref_prof]['status_profile'] = 'blocked';
                            $array_profiles[$cnt_ref_prof]['img_profile'] = base_url().'assets/images/profile_privated.jpg';
                            $array_profiles[$cnt_ref_prof]['login_profile'] = $name_profile;
                            $array_profiles[$cnt_ref_prof]['follows_from_profile'] = '-+-';
                            $cnt_ref_prof=$cnt_ref_prof+1;
                        }
                    } else{ //es una geolocalizacion      
                        $datas_of_profile = $this->Robot->get_insta_geolocalization_data_from_client(json_decode($this->session->userdata('cookies')),$name_profile, $id_profile);
                        $array_geolocalization[$cnt_geolocalization]['login_geolocalization'] = $name_profile;
                        $array_geolocalization[$cnt_geolocalization]['geolocalization_pk'] = $client_active_profiles[$i]['insta_id'];
                        if($datas_of_profile)
                            $array_geolocalization[$cnt_geolocalization]['follows_from_geolocalization'] = $datas_of_profile->follows;                        
                        $array_geolocalization[$cnt_geolocalization]['img_geolocalization'] = base_url().'assets/images/avatar_geolocalization_present.jpg';
                        if(!$datas_of_profile){
                            $array_geolocalization[$cnt_geolocalization]['img_geolocalization'] = base_url().'assets/images/avatar_geolocalization_deleted.jpg';
                            $array_geolocalization[$cnt_geolocalization]['status_geolocalization'] = 'deleted';
                        } else
                        if ($client_active_profiles[$cnt_geolocalization]['end_date']) { //perfil
                            $array_geolocalization[$cnt_geolocalization]['status_geolocalization'] = 'ended';
                        } else{
                            $array_geolocalization[$cnt_geolocalization]['status_geolocalization'] = 'active';
                        }
                        $cnt_geolocalization=$cnt_geolocalization+1;                        
                    }
                }
                
                if($cnt_ref_prof)
                    $response['array_profiles'] = $array_profiles;
                else
                    $response['array_profiles']=array();
                $response['N'] = $cnt_ref_prof;  
                if($cnt_geolocalization)
                    $response['array_geolocalization'] = $array_geolocalization;
                else
                    $response['array_geolocalization'] = array();                
                $response['N_geolocalization'] = $cnt_geolocalization;
                $response['message'] = 'Profiles loaded';
                
            } else {
                $response['N'] =0;
                $response['N_geolocalization'] =0;
                $response['array_profiles'] = NULL;
                $response['array_geolocalization'] =NULL;
                $response['message'] = 'Profiles unloaded';
            }            
            return json_encode($response);
        } else {
            $this->display_access_error();
        }
    }

    public function dicas_geoloc() {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/system_config.php';
        $GLOBALS['sistem_config'] = new dumbu\cls\system_config();
        $param['language'] = $GLOBALS['sistem_config']->LANGUAGE;        
        $this->load->model('class/user_model');
        $this->user_model->insert_washdog($this->session->userdata('id'),'LOOKING AT GEOCALIZATION TIPS');
        $this->user_model->insert_watchdog($this->session->userdata('id'),'LOOKING AT GEOCALIZATION TIPS');
        $this->load->view('dicas_geoloc', $param);
    }
    
    public function help() {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/system_config.php';
        $GLOBALS['sistem_config'] = new dumbu\cls\system_config();
        $language=$this->input->get();
        if(isset($language['language']))
            $param['language']=$language['language'];
        else
            $param['language'] = $GLOBALS['sistem_config']->LANGUAGE;        
       $this->load->view('Dicas', $param);
    }
    
    public function FAQ_function($language) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/system_config.php';
        $GLOBALS['sistem_config'] = new dumbu\cls\system_config();
        $result['SERVER_NAME']= $GLOBALS['sistem_config']->SERVER_NAME;
        $language=$this->input->get();
        if(isset($language['language']))
            $result['language']=$language['language'];
        else
            $result['language'] = $GLOBALS['sistem_config']->LANGUAGE;
        $this->load->model('class/client_model');       
        $cuestions =$this->client_model->geting_FAQ($result);
        $this->load->model('class/user_model');
        $this->user_model->insert_washdog($this->session->userdata('id'),'LOOKING AT FAQ');
        $result['info']=$cuestions;
        $this->load->view('FAQ',$result);
    }
   
    public function create_profiles_datas_to_display_as_json() {
        echo($this->create_profiles_datas_to_display());
    }

    public function display_access_error() {
        $this->session->sess_destroy();
        header('Location: ' . base_url().'index.php/welcome/');
    }
    
    public function client_acept_discont(){
        $this->load->model('class/client_model');       
        $this->load->model('class/user_model');       
        $values = $this->client_model->get_plane($this->session->userdata('plane_id'))[0];
        $value=$values['normal_val'];
        $sql = "SELECT * FROM clients WHERE clients.user_id='" . $this->session->userdata('id') . "'";
        $client = $this->user_model->execute_sql_query($sql);
        
        $recurrency_order_key=$client[0]['order_key'];
        
        
        $result['success'] = true;
        echo json_encode($result);
    }
    
    public function get_names_by_chars() {
        if($this->session->userdata('id')){
            $cookies=json_decode($this->session->userdata('cookies'));
            //$datas = $this->input->post();
            $datas = $this->input->get();
            $str=$datas['str'];
            $profile_type=$datas['profile_type'];            
            $mid=$cookies->mid;
            $csrftoken=$cookies->csrftoken;
            $ds_user_id=$cookies->ds_user_id;
            $sessionid=$cookies->sessionid;            
            $headers = array();
            $headers[] = 'Host: www.instagram.com';
            $headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:52.0) Gecko/20100101 Firefox/52.0';
            $headers[] = 'Accept: */*';
            $headers[] = 'Accept-Language: es-ES,es;q=0.8,en-US;q=0.5,en;q=0.3'; //--compressed 
            $headers[] = 'Referer: https://www.instagram.com/'; 
            $headers[] = 'X-Requested-With: XMLHttpRequest'; 
            $headers[] = 'Cookie: mid='.$mid.'; csrftoken='.$csrftoken.'; ds_user_id='.$ds_user_id.'; sessionid='.$sessionid.';';
            $headers[] = "Connection: keep-alive";            
            $url = 'https://www.instagram.com/web/search/topsearch/?context=blended&query='.$str.'/';
            $ch = curl_init("https://www.instagram.com/");
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            $output = curl_exec($ch);
            $info = curl_error($ch);
            $output=json_decode($output);
            if($profile_type==='places')
                $output=$output->places;
            else
            if($profile_type==='users')
                $output=$output->users;
            
            $result=array();
            $N=count($output);                    
            for($i=0;$i<$N;$i++){
                if($profile_type==='places'){
                    $result[$i]=$output[$i]->place->slug;
                }else 
                if($profile_type==='users'){
                    $result[$i]=$output[$i]->user->username;
                }
            }
            echo json_encode($result);
        }
    }
    
    public function admin_making_client_login(){
        $datas = $this->input->get();
        $datas['user_pass']=urldecode($datas['user_pass']);
        $result=$this->user_do_login($datas);
        if($result['authenticated']===true){
            $this->client();
        }
        else
            echo 'Esse cliente deve ter senha errada ou mudou suas credenciais no IG';
    }

    

    public function scielo_view() {
        $this->load->view('scielo');
    }

    public function scielo() {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/system_config.php';
        $GLOBALS['sistem_config'] = new dumbu\cls\system_config();
        $datas = $this->input->post();
        $datas['amount_in_cents'] = 100;
        $resp = $this->check_mundipagg_credit_card($datas);
        if (is_object($resp) && $resp->isSuccess()) {
            $order_key = $resp->getData()->OrderResult->OrderKey;
            $response['success'] = true;
            $response['message'] = "Compra relizada com sucesso! Chave da compra na mundipagg: $order_key";
        } else if (is_object($resp)) {
            $order_key = $resp->getData()->OrderResult->OrderKey;
            $response['success'] = false;
            $response['message'] = "Compra recusada! Chave da compra na mundipagg: $order_key";
        } else {
            $response['success'] = false;
            $response['message'] = "Compra recusada!";
        }
        echo json_encode($response);
    }
    
    public function get_daily_report($id) {
        if ($this->session->userdata('id')) {
            $this->load->model('class/user_model');
            $sql = "SELECT * FROM daily_report WHERE followings != '0' AND followers != '0' AND client_id=" . $id . " ORDER BY date ASC;" ;  // LIMIT 30
            $result = $this->user_model->execute_sql_query($sql);
            $followings = array();
            $followers = array();
            $N = count($result);
            for ($i = 0; $i < $N; $i++) {
                if(isset($result[$i]['date'])){
                $dd = date("j", $result[$i]['date']);
                $mm = date("n", $result[$i]['date']);
                $yy = date("Y", $result[$i]['date']);
                $followings[$i] = (object) array('x' => ($i+1), 'y' => intval($result[$i]['followings']), "yy" => $yy, "mm" => $mm, "dd" => $dd);
                $followers[$i] = (object) array('x' => ($i + 1), 'y' => intval($result[$i]['followers']), "yy" => $yy, "mm" => $mm, "dd" => $dd);
                }
            }
            $response= array(
                'followings' => json_encode($followings),
                'followers' => json_encode($followers)
            );
            return $response;
        }
    }
    
    public function get_img_profile($profile){
        $this->load->model('class/client_model');
        $datas= $this->check_insta_profile($profile);
        if($datas)
            return $datas->profile_pic_url;
        else
            return 'missing_profile';
    }
        
    public function client_black_list(){
        if($this->session->userdata('id')){
            $this->load->model('class/client_model');
            try {
                $bl=$this->client_model->get_client_black_or_white_list_by_id($this->session->userdata('id'),0);                
                $dados=array();
                $N=count($bl);
                for($i=0;$i<$N;$i++){
                    $dados[$i]=(object)array('profile'=>$bl[$i]['profile'],'url_foto'=> $this->get_img_profile($bl[$i]['profile']));
                }
                $response['client_black_list'] = $dados;
                $response['success'] = true;
                $response['cnt'] = $N;
            } catch (Exception $ex) {
                $response['success'] = false;
            }
            echo json_encode($response);
        }
    }
        
    public function insert_profile_in_black_list(){
        if ($this->session->userdata('id')) {
            require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/system_config.php';
            $GLOBALS['sistem_config'] = new dumbu\cls\system_config();            
            if(isset($language['language']))
                $param['language']=$language['language'];
            else
                $param['language'] = $GLOBALS['sistem_config']->LANGUAGE;
            $param['SERVER_NAME'] = $GLOBALS['sistem_config']->SERVER_NAME;        
            $GLOBALS['language']=$param['language'];
        
            $this->load->model('class/client_model');
            $profile = $this->input->post()['profile'];   
            $datas=$this->check_insta_profile($profile);
            if($datas){
                $resp=$this->client_model->insert_in_black_or_white_list_model($this->session->userdata('id'),$datas->pk,$profile,0);
                if($resp['success']){
                    $result['success'] = true;
                    $result['url_foto'] = $datas->profile_pic_url;    
                    $this->load->model('class/user_model');
                    //$this->user_model->insert_washdog($this->session->userdata('id'),'INSERTING PROFILE '.$profile.'IN BLACK LIST');
                    $this->user_model->insert_washdog($this->session->userdata('id'),'INSERTING PROFILE IN BLACK LIST');
                } else{
                    $result['success'] = false;
                    $result['message'] = $this->T('O perfil '.$resp['message'], array(), $GLOBALS['language']);
                }
            } else{
                $result['success'] = false;
                $result['message'] = $this->T('O perfil não existe no Instagram', array(), $GLOBALS['language']);
            }            
            echo json_encode($result);
        }
    }
    
    public function delete_client_from_black_list(){
        if ($this->session->userdata('id')) {
            require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/system_config.php';
            $GLOBALS['sistem_config'] = new dumbu\cls\system_config();
            if(isset($language['language']))
                $param['language']=$language['language'];
            else
                $param['language'] = $GLOBALS['sistem_config']->LANGUAGE;
            $param['SERVER_NAME'] = $GLOBALS['sistem_config']->SERVER_NAME;        
            $GLOBALS['language']=$param['language'];

            $this->load->model('class/client_model');
            $profile = $this->input->post()['profile'];
            if($this->client_model->delete_in_black_or_white_list_model($this->session->userdata('id'),$profile,0)){
                $result['success'] = true;
                $this->load->model('class/user_model');
                //$this->user_model->insert_washdog($this->session->userdata('id'),'DELETING PROFILE '.$profile.' IN BLACK LIST');
                $this->user_model->insert_washdog($this->session->userdata('id'),'DELETING PROFILE IN BLACK LIST');
            } else{
                $result['success'] = false;
                $result['message'] = $this->T('Erro eliminando da lista negra', array(), $GLOBALS['language']);
            }
            echo json_encode($result);
        }
    }
    
    public function client_white_list(){
        if($this->session->userdata('id')){
            $this->load->model('class/client_model');
            try {
                $bl=$this->client_model->get_client_black_or_white_list_by_id($this->session->userdata('id'),1);                
                $dados=array();
                $N=count($bl);
                for($i=0;$i<$N;$i++){
                    $dados[$i]=(object)array('profile'=>$bl[$i]['profile'],'url_foto'=> $this->get_img_profile($bl[$i]['profile']));
                }
                $response['client_white_list'] = $dados;
                $response['success'] = true;
                $response['cnt'] = $N;   
            } catch (Exception $ex) {
                $response['success'] = false;
            }
            echo json_encode($response);
        }
    }
    
    public function insert_profile_in_white_list(){
        if ($this->session->userdata('id')) {
            require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/system_config.php';
            $GLOBALS['sistem_config'] = new dumbu\cls\system_config();
            if(isset($language['language']))
                $param['language']=$language['language'];
            else
                $param['language'] = $GLOBALS['sistem_config']->LANGUAGE;
            $param['SERVER_NAME'] = $GLOBALS['sistem_config']->SERVER_NAME;        
            $GLOBALS['language']=$param['language'];
            $this->load->model('class/client_model');
            $profile = $this->input->post()['profile'];   
            $datas=$this->check_insta_profile($profile);
            if($datas){
                $resp=$this->client_model->insert_in_black_or_white_list_model($this->session->userdata('id'),$datas->pk,$profile,1);
                if($resp['success']){
                    $result['success'] = true;
                    $result['url_foto'] = $datas->profile_pic_url;    
                    $this->load->model('class/user_model');
                    //$this->user_model->insert_washdog($this->session->userdata('id'),'INSERTING PROFILE '.$profile.'IN WHITE LIST ');
                    $this->user_model->insert_washdog($this->session->userdata('id'),'INSERTING PROFILE IN WHITE LIST');
                } else{
                    $result['success'] = false;
                    $result['message'] = $this->T('O perfil '.$resp['message'], array(), $GLOBALS['language']);
                }
            } else{
                $result['success'] = false;
                $result['message'] = $this->T('O perfil não existe no Instagram', array(), $GLOBALS['language']);
            }            
            echo json_encode($result);
        }
    }
    
    public function delete_client_from_white_list(){
        if ($this->session->userdata('id')) {
            require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/system_config.php';
            $GLOBALS['sistem_config'] = new dumbu\cls\system_config();
            if(isset($language['language']))
                $param['language']=$language['language'];
            else
                $param['language'] = $GLOBALS['sistem_config']->LANGUAGE;
            $param['SERVER_NAME'] = $GLOBALS['sistem_config']->SERVER_NAME;        
            $GLOBALS['language']=$param['language'];
            $this->load->model('class/client_model');
            $profile = $this->input->post()['profile'];
            if($this->client_model->delete_in_black_or_white_list_model($this->session->userdata('id'),$profile,1)){
                $result['success'] = true;
                $this->load->model('class/user_model');
                //$this->user_model->insert_washdog($this->session->userdata('id'),'DELETING PROFILE '.$profile.' IN WHITE LIST');
                $this->user_model->insert_washdog($this->session->userdata('id'),'DELETING PROFILE IN WHITE LIST');
            } else{
                $result['success'] = false;
                $result['message'] = $this->T('Erro eliminando da lista negra', array(), $GLOBALS['language']);
            }
            echo json_encode($result);
        }
    }  
    

    
    public function paypal() {
        $this->load->view('test_view');
    }
    
    public function update_client_after_retry_payment_success($user_id) {  
        require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/system_config.php';
        $GLOBALS['sistem_config'] = new dumbu\cls\system_config();        
        $this->load->model('class/client_model');
        $this->load->model('class/user_model');
        $this->load->model('class/user_status');
        //1. recuperar el cliente y su plano
        $client = $this->client_model->get_all_data_of_client($user_id)[0];
        $plane = $this->client_model->get_plane($client['plane_id'])[0];
        //3. crear nueva recurrencia en la Mundipagg para el proximo mes   
        date_default_timezone_set('Etc/UTC');
        $payment_data['credit_card_number'] = $client['credit_card_number'];
        $payment_data['credit_card_name'] = $client['credit_card_name'];
        $payment_data['credit_card_exp_month'] = $client['credit_card_exp_month'];
        $payment_data['credit_card_exp_year'] = $client['credit_card_exp_year'];
        $payment_data['credit_card_cvc'] = $client['credit_card_cvc'];
        if($client['actual_payment_value']!='' && $client['actual_payment_value']!=null)
            $payment_data['amount_in_cents'] = $client['actual_payment_value'];
        else
            $payment_data['amount_in_cents'] = $plane['normal_val'];
        $payment_data['pay_day'] = strtotime("+1 month", time());
        $resp = $this->check_recurrency_mundipagg_credit_card($payment_data, 0);
        //4. salvar nuevos pay_day e order_key
        if (is_object($resp) && $resp->isSuccess()) {
            //2. eliminar recurrencia actual en la Mundipagg
            $this->delete_recurrency_payment($client['order_key']);
            $this->client_model->update_client($user_id, array(
                'initial_order_key' => '',
                'order_key' => $resp->getData()->OrderResult->OrderKey,
                'pay_day' => $payment_data['pay_day']));
            echo '<br>Client '.$user_id.' updated correctly. New order key is:  '.$resp->getData()->OrderResult->OrderKey;
            //5. actualizar status del cliente
            $data_insta = $this->is_insta_user($client['login'], $client['pass']);
            if($data_insta['status'] === 'ok' && $data_insta['authenticated']) {
                $this->user_model->update_user($user_id, array(
                    'status_date' => time(),
                    'status_id' => user_status::ACTIVE
                ));
                echo ' STATUS = '.user_status::ACTIVE;
            } else
            if ($data_insta['status'] === 'ok' && !$data_insta['authenticated']){
                $this->user_model->update_user($user_id, array(
                    'status_date' => time(),
                    'status_id' => user_status::BLOCKED_BY_INSTA
                ));
                echo ' STATUS = '.user_status::BLOCKED_BY_INSTA;
            }
            else{
                $this->user_model->update_user($user_id, array(
                    'status_date' => time(),
                    'status_id' => user_status::BLOCKED_BY_INSTA
                ));
                echo ' STATUS = '.user_status::VERIFY_ACCOUNT;
            }
        } else{
            $this->user_model->update_user($user_id, array(            
                'status_date' => time(),
                'status_id' => 1)); 
            $this->delete_recurrency_payment($client['order_key']);
            $this->client_model->update_client($user_id, array(
                'initial_order_key' => '',
                'order_key' => '',
                'observation' => 'NÃO CONSEGUIDO DURANTE RETENTATIVA - TENTAR CRIAR ANTES DE DATA DE PAGAMENTO',
                'pay_day' => $payment_data['pay_day']));
            //TO-DO:Ruslan: inserta una pendencia automatica aqui
            
            if (is_object($resp))
                echo '<br>Client '.$user_id.' DONT updated. Wrong order key is:  '.$resp->getData()->OrderResult->OrderKey;
            else 
                echo '<br>Client '.$user_id.' DONT updated. Missing order key';
        }
        
        $this->client_model->update_client($user_id, array(            
            'initial_order_key' => '')); 
    }
           
    public function buy_retry_for_clients_with_puchase_counter_in_zero() {
        $this->load->model('class/client_model');
        $cl=$this->client_model->beginners_with_purchase_counter_less_value(9);
        for($i=1;$i<count($cl);$i++){            
            $clients=$cl[$i];
            $datas=array('client_login'=>$clients['login'],
                         'client_pass'=>$clients['pass'],
                         'client_email'=>$clients['email']);
            $resp=$this->check_user_for_sing_in($datas);
            
            if($resp['success']){
                $datas=array(
                    'pk'=>$clients['user_id'],
                    'credit_card_number'=>$clients['credit_card_number'],
                    'credit_card_cvc'=>$clients['credit_card_cvc'],
                    'credit_card_name'=>$clients['credit_card_name'],
                    'credit_card_exp_month'=>$clients['credit_card_exp_month'],
                    'credit_card_exp_year'=>$clients['credit_card_exp_year'],

                    'plane_type'=>$clients['plane_id'],
                    'ticket_peixe_urbano'=>$clients['ticket_peixe_urbano'],
                    'user_email'=>$clients['email'],
                    'insta_name'=>$clients['name'],
                    'user_login'=>$clients['login'],
                    'user_pass'=>$clients['pass'],
                );            
                $resp=$this->check_client_data_bank($datas);
                if($resp['success']){
                    echo 'Cliente ('.$clients['login'].')   '.$clients['login'].'comprou satisfatoriamente\n<br>';
                } else{
                    $this->client_model->update_client($clients['user_id'], array(
                        'purchase_counter' => -100 ));
                    echo 'Cliente '.$clients['login'].' ERRADO\n<br>';
                }
            } else{
                $this->client_model->update_client($clients['user_id'], array(
                        'purchase_counter' => -100 ));
                echo 'Cliente ('.$clients['login'].') '.$clients['login'].'não passou passo 1\n<br>';
            }
        }
    }
        
    public function Pedro(){
        $this->load->model('class/user_model');
        $users= $this->user_model->get_all_users();
        $L=count($users);
        echo 'Num clientes '.$L."<br>";
        $file = fopen("media_pro.txt","w");
        for($i=0;$i<$L;$i++){
            $result=$this->user_model->get_daily_report($users[$i]['id']);
            $Ndaily_R=count($result);
            //echo $i.'----'.$users[$i]['id'].'-----'.count($users).'<br>';
            $N=0; $sum=0;
            if($Ndaily_R>5){
                for($j=1;$j<$Ndaily_R;$j++){
                    $diferencia = $result[$j]['date']-$result[$j-1]['date']; 
                    $horas = (int)($diferencia/(60*60)); 
                    if( $horas>20 && $horas <=30){
                        $N++;
                        $sum=$sum+($result[$j]['followers'] - $result[$j-1]['followers']);
                    }
                }
                //fwrite($file, ($users[$i]['id'].'---'.$users[$i]['status_id'].'---'.$users[$i]['plane_id'].'---'.((int)($sum/$N)).'<br>'));
                echo $users[$i]['id'].'---'.$users[$i]['status_id'].'---'.$users[$i]['plane_id'].'---'.((int)($sum/$N)).'<br>';
                
            }            
        }
        echo 'fin';
        fclose($file);
    }
    
    public function update_ds_user_id() {
        $this->load->model('class/client_model');
        $resul=$this->client_model->select_white_list_model();
        foreach ($resul as $key => $value) {
            $data_insta = $this->check_insta_profile($value['profile']);
            $this->client_model->update_ds_user_id_white_list_model($value['id'],$data_insta->pk);
        }
    }   
    
    public function login_all_clients(){
        $this->load->model('class/user_model');
        $a=$this->user_model->get_all_dummbu_clients();
        $N=count($a);
        for($i=0;$i<$N;$i++){
            $st=$a[$i]['status_id'];
            if($st!=='4' && $st!=='8' && $st!=='11' && $a[$i]['role_id']==='2'){
                echo $i;
                $login=$a[$i]['login'];
                $pass=$a[$i]['pass'];
                $datas['user_login']=$login;
                $datas['user_pass']=$pass;
                $result= $this->user_do_login($datas);
            }
        }
    }
    
    public function time_of_live() {
        $this->load->model('class/user_model');
        $result=$this->user_model->time_of_live_model(4);
        $response=array(
            '0-2-dias'=>array(0,0,0,0,0),
            '2-30-dias'=>array(0,0,0,0,0),
            '30-60-dias'=>array(0,0,0,0,0),
            '60-90-dias'=>array(0,0,0,0,0),
            '90-120-dias'=>array(0,0,0,0,0),
            '120-150-dias'=>array(0,0,0,0,0),
            '150-180-dias'=>array(0,0,0,0,0),
            '180-210-dias'=>array(0,0,0,0,0),
            '210-240-dias'=>array(0,0,0,0,0),
            '240-270-dias'=>array(0,0,0,0,0),
            'mais-270'=>array(0,0,0,0,0));
        
        foreach ($result as $user) {
            $difference=$user['end_date']-$user['init_date'];
            $second = 1;
            $minute = 60*$second;
            $hour   = 60*$minute;
            $day    = 24*$hour;
            
            $plane=$user['plane_id'];
            
            $num_days=floor($difference/$day);            
            if ($num_days<=2) 
                $response['0-2-dias'][$plane]=$response['0-2-dias'][$plane]+1;
            else
            if ($num_days>2 &&$num_days<=30) 
                $response['2-30-dias'][$plane]=$response['2-30-dias'][$plane]+1;
            else
            if ($num_days>30 &&$num_days<=60) 
                $response['30-60-dias'][$plane]=$response['30-60-dias'][$plane]+1;
            else
            if ($num_days>60 &&$num_days<=90) 
                $response['60-90-dias'][$plane]=$response['60-90-dias'][$plane]+1;            
            else
            if ($num_days>90 &&$num_days<=120) 
                $response['90-120-dias'][$plane]=$response['90-120-dias'][$plane]+1;
            else
            if ($num_days>120 &&$num_days<=150) 
                $response['120-150-dias'][$plane]=$response['120-150-dias'][$plane]+1;
            else
            if ($num_days>150 &&$num_days<=180) 
                $response['150-180-dias'][$plane]=$response['150-180-dias'][$plane]+1;
            else
            if ($num_days>180 &&$num_days<=210) 
                $response['180-210-dias'][$plane]=$response['180-210-dias'][$plane]+1;
            else
            if ($num_days>210 &&$num_days<=240) 
                $response['210-240-dias'][$plane]=$response['210-240-dias'][$plane]+1;
            else
            if ($num_days>240 &&$num_days<=270) 
                $response['240-270-dias'][$plane]=$response['240-270-dias'][$plane]+1;
            else 
                $response['mais-270'][$plane]=$response['mais-270'][$plane]+1;
        }        
        var_dump($response);        
    }
    
    public function users_by_month_and_plane() {
        $status = $this->input->get()['status'];
        $this->load->model('class/user_model');
        $result=$this->user_model->time_of_live_model($status);
                
        foreach ($result as $user) {
            $month=date("n", $user['init_date']);
            $year=date("Y", $user['init_date']);
            $cad=$month.'-'.$year.'<br>';
            $plane_id=$user['plane_id'];
            if(!isset($r[$cad][$plane_id] ))
                $r[$cad][$plane_id]=0;
            else
                $r[$cad][$plane_id]=$r[$cad][$plane_id]+1;
        }        
        var_dump($r);        
    }
        
    /*public function cancel_blocked_by_payment_by_max_retry_payment(){
        require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/system_config.php';
        $GLOBALS['sistem_config'] = new dumbu\cls\system_config();
        $this->load->model('class/user_model');
        $this->load->model('class/client_model');        
        $result=$this->client_model->get_all_clients_by_status_id(2);        
        foreach ($result as $client) {
            if($client['retry_payment_counter']>9){
                try{
                    $this->delete_recurrency_payment($client['initial_order_key']);                
                    $this->delete_recurrency_payment($client['order_key']);                
                    $this->user_model->update_user($client['user_id'], array(  
                        'end_date' => time(),
                        'status_date' => time(),
                        'status_id' => 4));
                    $this->client_model->update_client($client['user_id'], array(
                            'observation' => 'Cancelado automaticamente por mais de 10 retentativas de pagamento sem sucessso'));
                    echo 'Client '.$client['user_id'].' cancelado por maxima de retentativas';
                } catch (Exception $e){
                    echo 'Error deleting cliente '.$client['user_id'].' in database';
                }
            }
        }
    }

    public function buy_tester(){
        
    }
    
    public function update_all_retry_clients(){
        $array_ids=array(176, 192, 419, 1290, 1921, 3046, 3179, 3218, 3590, 12707, 564, 3486, 671, 2300, 4123, 4466, 12356, 12373, 12896, 13786, 23410,25073, 15746, 23636, 24426, 15745);
        $N=count($array_ids);
        for($i=0;$i<$N;$i++){
            $this->update_client_after_retry_payment_success($array_ids[$i]);
        }
    }*/
    
    public function capturer_and_recurrency_for_blocked_by_payment(){
        require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/system_config.php';
        $GLOBALS['sistem_config'] = new dumbu\cls\system_config();
        $this->load->model('class/user_model');
        $this->load->model('class/client_model');
        $params=$this->input->get();
        $result=$this->client_model->get_all_clients_by_status_id(2);
        foreach ($result as $client) {
            $aa=$client['login'];
            $status_id=$client['status_id'];
            if($client['retry_payment_counter']<13){
                if($client['credit_card_number']!=null && $client['credit_card_number']!=null && 
                        $client['credit_card_name']!=null && $client['credit_card_name']!='' && 
                        $client['credit_card_exp_month']!=null && $client['credit_card_exp_month']!='' && 
                        $client['credit_card_exp_year']!=null && $client['credit_card_exp_year']!='' && 
                        $client['credit_card_cvc']!=null && $client['credit_card_cvc']!='' ){

                    $pay_day = time();
                    $payment_data['credit_card_number'] =$client['credit_card_number'];
                    $payment_data['credit_card_name'] = $client['credit_card_name'];
                    $payment_data['credit_card_exp_month'] = $client['credit_card_exp_month'];
                    $payment_data['credit_card_exp_year'] = $client['credit_card_exp_year'];
                    $payment_data['credit_card_cvc'] = $client['credit_card_cvc'];
                    
                    $difference=$pay_day-$client['init_date'];
                    $second = 1;
                    $minute = 60*$second;
                    $hour   = 60*$minute;
                    $day    = 24*$hour;  
                    $num_days=floor($difference/$day); 

                    $payment_data['amount_in_cents'] =0;
                    if($client['ticket_peixe_urbano']==='AMIGOSDOPEDRO' || $client['ticket_peixe_urbano']==='INSTA15D'){
                        $payment_data['amount_in_cents']=$this->client_model->get_normal_pay_value($client['plane_id']);
                    } else
                    if( ($client['ticket_peixe_urbano']==='INSTA50P' ||
                            $client['ticket_peixe_urbano']==='BACKTODUMBU' ||
                            $client['ticket_peixe_urbano']==='BACKTODUMBU-DNLO' ||
                            $client['ticket_peixe_urbano']==='BACKTODUMBU-EGBTO')){
                        $payment_data['amount_in_cents']=$this->client_model->get_normal_pay_value($client['plane_id']);
                        if($num_days<=33)
                            $payment_data['amount_in_cents']=$payment_data['amount_in_cents']/2;
                    } else
                    if($client['ticket_peixe_urbano']==='DUMBUDF20'){
                        $payment_data['amount_in_cents']=$this->client_model->get_normal_pay_value($client['plane_id']);
                        $payment_data['amount_in_cents']=($payment_data['amount_in_cents']*8)/10;
                    } else
                    if($client['ticket_peixe_urbano']==='INSTA-DIRECT' || $client['ticket_peixe_urbano']==='MALADIRETA'){
                        $payment_data['amount_in_cents']=$this->client_model->get_normal_pay_value($client['plane_id']);
                    } else                
                    if($client['actual_payment_value']!=null && 
                            $client['actual_payment_value']!='null' && 
                            $client['actual_payment_value']!='' && 
                            $client['actual_payment_value']!=NULL
                            && $payment_data['amount_in_cents'] ==0
                            )
                        $payment_data['amount_in_cents'] = $client['actual_payment_value'];
                    else
                       $payment_data['amount_in_cents']=$this->client_model->get_normal_pay_value($client['plane_id']);

                    $resp = $this->check_mundipagg_credit_card($payment_data);
                    if((is_object($resp) && $resp->isSuccess()&& $resp->getData()->CreditCardTransactionResultCollection[0]->CapturedAmountInCents>0)){
                        $this->update_client_after_retry_payment_success($client['user_id']);
                        $this->client_model->update_client($client['user_id'], array(
                            'retry_payment_counter' => 0));
                    }else{
                        $this->client_model->update_client($client['user_id'], array(
                        'retry_payment_counter' => $client['retry_payment_counter']+1));
                    }
                }
            } else{
                try{
                    $this->delete_recurrency_payment($client['initial_order_key']);                
                    $this->delete_recurrency_payment($client['order_key']);                
                    $this->user_model->update_user($client['user_id'], array(  
                        'end_date' => time(),
                        'status_date' => time(),
                        'status_id' => 4));
                    $this->client_model->update_client($client['user_id'], array(
                            'observation' => 'Cancelado automaticamente por mais te 10 retentativas de pagamento sem sucessso'));
                    echo '<br>------->Client '.$client['user_id'].' cancelado por maxima de retentativas';
                } catch (Exception $e){
                    echo 'Error deleting cliente '.$client['user_id'].' in database';
                }
            }
        }
    }    

    public function cancel_blocked_by_payment_by_max_retry_payment(){
        require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/system_config.php';
        $GLOBALS['sistem_config'] = new dumbu\cls\system_config();
        $this->load->model('class/user_model');
        $this->load->model('class/client_model');
        $result=$this->client_model->get_all_clients_by_status_id(2);        
        foreach ($result as $client) {
            if($client['retry_payment_counter']>9){
                try{
                    $this->delete_recurrency_payment($client['initial_order_key']);                
                    $this->delete_recurrency_payment($client['order_key']);                
                    $this->user_model->update_user($client['user_id'], array(  
                        'end_date' => time(),
                        'status_date' => time(),
                        'status_id' => 4));
                    $this->client_model->update_client($client['user_id'], array(
                            'observation' => 'Cancelado automaticamente por mais de 10 retentativas de pagamento sem sucessso'));
                    echo 'Client '.$client['user_id'].' cancelado por maxima de retentativas';
                } catch (Exception $e){
                    echo 'Error deleting cliente '.$client['user_id'].' in database';
                }
            }
        }
    }
    
    public function ranking(){ //10 clientes activos que mas han ganado con dumbu               
        //Funcion que deve estimar el ranking general, segun el ranking diario.
        //retorna un array con el ranking, sendo que o clliente na pocisão 0 é o mais ranquado
    }    
    
    public function daily_ranking(){
        $this->load->model('class/user_model');
        $this->load->model('class/ranking_model');
        $result=$this->user_model->get_ranking();
        $N=count($result);
        for($i=0;$i<$N;$i++) {
            $actual_followers=$this->user_model->get_last_daily_report($result[$i]['user_id']);
            if($actual_followers){
                $ndays=time()-$result[$i]['init_date'];
                $ndays=$ndays/(24*60*60);
                $result[$i]['ranking_score']= ($actual_followers['followers'] - $result[$i]['insta_followers_ini'])/$ndays;
            }
            else
                $result[$i]['ranking_score']=0;
        }
        
        foreach ($result as $key => $row) {
            $aux[$key] = $row['ranking_score'];
        }
        array_multisort($aux, SORT_DESC, $result);
        
        $i=0;
        foreach ($result as $key => $row) {
            $datas=array(
                'client_id'=>$result[$i]['user_id'],
                'position'=>($i+1),
                'date'=>time()
            );
            $this->ranking_model->insert_into_ranking($datas);            
            $i++;
            if($i==10)
                break;
        }        
    }

    public function buy_tester(){
        
    }
    
    public function update_all_retry_clients(){
        $array_ids=array();
        $N=count($array_ids);
        for($i=0;$i<$N;$i++){
            $this->update_client_after_retry_payment_success($array_ids[$i]);
        }
    }
    
    public function security_code_request() {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/Robot.php';
        $this->Robot = new \dumbu\cls\Robot();
        $this->load->model('class/user_role');
        $this->load->model('class/user_model');
        
        if ($this->session->userdata('role_id') == user_role::CLIENT) {
            try {
                $checkpoint_data = $this->Robot->checkpoint_requested($this->session->userdata('login'), $this->session->userdata('pass'));
            } catch (Exception $ex) {
                $result['success'] = false;
                $result['message'] = $this->T('Erro ao solicitar código de segurança', array(), $this->session->userdata('language'));
                $this->user_model->insert_washdog($this->session->userdata('id'),'ERROR #4 IN SECURITY CODE REQUEST');
                $this->user_model->insert_washdog($this->session->userdata('id'),'Exception message: '.$ex->getMessage());
                $this->user_model->insert_washdog($this->session->userdata('id'),'Exception stack trace: '.$ex->getTraceAsString());
                echo json_encode($result);
                return;
            }

            if ($checkpoint_data && $checkpoint_data->status == "ok") {
                if ($checkpoint_data->type == "CHALLENGE") {
                    $result['success'] = true;
                    $result['message'] = $this->T('Código de segurança solicitado corretamente', array(), $this->session->userdata('language'));
                    $this->user_model->insert_washdog($this->session->userdata('id'),'SECURITY CODE REQUESTED');
                }
                else if ($checkpoint_data->type == "CHALLENGE_REDIRECTION") {
                    $result['success'] = false;
                    $result['message'] = $this->T('Por favor, entre no seu Instagram e confirme FUI EU. Depois saia do seu Instagram e volte ao Passo 1 nesta página.', array(), $this->session->userdata('language'));
                    $this->user_model->insert_washdog($this->session->userdata('id'),'ERROR #1 IN SECURITY CODE REQUEST');
                }
                else {
                    $result['success'] = false;
                    $result['message'] = $this->T('Erro ao solicitar código de segurança', array(), $this->session->userdata('language'));
                    $this->user_model->insert_washdog($this->session->userdata('id'),'ERROR #2 IN SECURITY CODE REQUEST');
                }
            }
            else {
                $result['success'] = false;
                $result['message'] = $this->T('Erro ao solicitar código de segurança', array(), $this->session->userdata('language'));
                $this->user_model->insert_washdog($this->session->userdata('id'),'ERROR #3 IN SECURITY CODE REQUEST');
            }
            
            echo json_encode($result);
        }
        else {
            $this->display_access_error();
        }
    }
    
    public function security_code_confirmation() {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/Robot.php';
        $this->Robot = new \dumbu\cls\Robot();
        $this->load->model('class/user_role');
        
        if ($this->session->userdata('role_id') == user_role::CLIENT) {
            $security_code = $this->input->post()['security_code'];
            $checkpoint_data = $this->Robot->make_checkpoint($this->session->userdata('login'), $security_code);
            $this->load->model('class/user_model');
            
            if ($checkpoint_data && $checkpoint_data->json_response === 1 && $checkpoint_data->sessionid !== null && $checkpoint_data->ds_user_id !== null) {
                $result['success'] = true;
                $result['message'] = 'Código de segurança confirmado corretamente';
                $this->user_model->insert_washdog($this->session->userdata('id'),'SECURITY CODE CONFIRMATED');
                $this->user_model->insert_watchdog($this->session->userdata('id'),'SECURITY CODE CONFIRMATED');
            } 
            else {
                $result['success'] = false;
                $result['message'] = 'Erro ao confirmar código de segurança';
                $this->user_model->insert_washdog($this->session->userdata('id'),'ERROR IN SECURITY CODE CONFIRMATION');
                $this->user_model->insert_watchdog($this->session->userdata('id'),'ERROR IN SECURITY CODE CONFIRMATION');
            }
            echo json_encode($result);
        }
        else {
            $this->display_access_error();
        }
    }
    

    public function faq() {
        $this->load->model('class/user_role');                
        $this->load->model('class/system_config');
        $this->load->model('class/user_model');
        $datas = $this->input->post();
        $language = $datas['new_language'];
        
        $param = array(); 
        $param['language']=$language;
        /*if($this->session->userdata('id')){
            $this->load->model('class/system_config');
            $GLOBALS['sistem_config'] = $this->system_config->load();

            $language=$this->input->get();            
            if($language['language'] != "PT" && $language['language'] != "ES" && $language['language'] != "EN")
                    $language['language'] = NULL;

            if(isset($language['language']))                
                $param['language']=$language['language'];            
            else
                $param['language'] = $GLOBALS['sistem_config']->LANGUAGE;
        }
        else{
            $param['language'] = $this->session->userdata('language');
        }*/
        if(isset($this) && isset($this->session) && $this->session->userdata('id')){
         $this->user_model->insert_washdog($this->session->userdata('id'),'LOOKING AT FAQ');
        }
        $this->load->view('faq_view', $param);
    }

        public function faqget() {
        $this->load->model('class/user_role');                
        $this->load->model('class/system_config');
        $this->load->model('class/user_model');
        $datas = $this->input->get();
        $language = $datas['language'];
        
        $param = array(); 
        $param['language']=$language;
        /*if($this->session->userdata('id')){
            $this->load->model('class/system_config');
            $GLOBALS['sistem_config'] = $this->system_config->load();

            $language=$this->input->get();            
            if($language['language'] != "PT" && $language['language'] != "ES" && $language['language'] != "EN")
                    $language['language'] = NULL;

            if(isset($language['language']))                
                $param['language']=$language['language'];            
            else
                $param['language'] = $GLOBALS['sistem_config']->LANGUAGE;
        }
        else{
            $param['language'] = $this->session->userdata('language');
        }*/
        if(isset($this) && isset($this->session) && $this->session->userdata('id')){
         $this->user_model->insert_watchdog($this->session->userdata('id'),'LOOKING AT FAQ');
        } 
        $this->load->view('faq_view', $param);
    }

}
