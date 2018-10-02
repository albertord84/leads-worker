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

}
