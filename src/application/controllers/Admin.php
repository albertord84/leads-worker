<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {
    
    //------------ADMIN desenvolvido para DUMBU-LEADS-------------------   
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
                $this->session->sess_destroy();
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
    
    
    public function show_users() {
    /*    $this->load_language();
        if ($this->session->userdata('id')){            
            $this->load->model('class/admin_model');
            $datas = $this->input->post();
            $users_results = $this->admin_model->get_users($datas);
            $users = array();
            foreach($users_results as $user){
                $users[] = array(
                                'id' => $user['id'],
                                'login' => $user['login'],
                                'email' => $user['email'],
                                'status_id' => $user['status_id'],
                                'init_date' => $user['init_date']
                                );
            }
            if(count($users) > 0){                    
                $result['success'] = true;
                $result['message'] = 'Existem usuários';
                $result['resource'] = 'index';
                $result['users_array'] = $users;
            } else{
                $result['success'] = false;
                $result['message'] = $this->T("Não existem usuários para esses filtros", array(), $GLOBALS['language']); 
                $result['resource'] = 'index';
            }
        }
        else{
            $result['success'] = false;
            $result['message'] = $this->T("Não existe sessão ativa", array(), $GLOBALS['language']);
            $result['resource'] = 'index';
        }
        echo json_encode($result);*/
               // $this =& get_instance();
        $this->load->model('class/user_role');        
        $this->load_language();
        $lang= $this->session->userdata('language');
        $lang1=$lang1.'<option value="1">'.$this->T("ATIVO", array(),$lang).'</option>';                                        
        $lang1=$lang1.'<option value="2">'.$this->T("BLOQUEADO POR PAGAMENTO", array(),$lang).'</option>';                                        
        $lang1=$lang1.'<option value="4">'.$this->T("ELIMINADO", array(),$lang).'</option>';                                        
        $lang1=$lang1.'<option value="6">'.$this->T("PENDENTE POR PAGAMENTO", array(),$lang).'</option>';                                        
        $lang1=$lang1.'<option value="8">'.$this->T("INICIANTE", array(),$lang).'</option>';                                        
        //$lang1=$lang1.'<option value="11">'.$CI->T("NÃO MOLESTAR", array(),$lang).'</option>';
        $lang1=$lang1.'<option value="12">'.$this->T("OCUPADO", array(),$lang).'</option>';
        $lang1=$lang1.'<option value="11">NÃO USAR MAIS</option>';
        $lang1=$lang1.'<option value="12">OCUPADO</option>';
        if ($this->session->userdata('role_id')==user_role::ADMIN){            
          $this->load->model('class/admin_model');
          $this->load->model('class/user_status');
          $this->load->model('class/credit_card_model');
          $datas = $this->input->post();
          $users_results = $this->admin_model->get_users($datas);
          $users = array();
          if(count($users_results)>0)
          {    
            foreach($users_results as $user){                   
                $id = 0;
                if(!$datas['status_id'] || $datas['status_id'] == $this->user_status::BEGINNER){ 
                    $id = $user['id_usr'];                    
                }
                else{
                    $id = $user['id_usr'];                    
                }
                $card = $this->credit_card_model->get_credit_card($id);
                $st_nam= $this->T($user['st_name'], array(), $lang);
                //$st_nam=
           /* foreach ($user as $k => $dat) 
            {
             if($k!=-50)
                 echo $k;
            }  */ 
                
                $users[] = array(
                                //'user_id' => $user['id'],
                                'user_id' => $user['id_usr'],
                                'role_id' => $user['role_id'],
                                'name' => $user['name'],
                                'login' => $user['login'],
                                //'pass' => $user['pass'],
                                'email' => $user['email'],
                                'telf' => $user['telf'],
                                'status_id' => $user['status_id'],
                                //'status_date' => $user['status_date'],
                                //'language' => $user['language'],
                                'init_date' => $user['init_date'],
                                //'end_date' => $user['end_date'],
                                //'credit_card_name' => $card['credit_card_name'],
                                //'utm_source' => $user['utm_source'],
                                //'brazilian' => $user['brazilian'],
                                //'promotional_code' => $user['promotional_code'],
                                //'campaing_type' => $user['campaing_type'],
                                //'payment_type' => $user['payment_type'],
                                'amount_in_cents' =>$user['amount_in_cents'],
                                'date'=>$user['idpay'],
                                'status_date'=>$user['status_date'],
                                'st_name'=>$st_nam,
                                );
                //if(!$datas['card_name'])
                               
              }
            }
            else 
            {
              if (!($datas['prf_client1']==''&& $datas['eml_client1']==''&& $datas['card_name']==''&& $datas['client_id']==''))
              {
                        $datas['lst_access1']='';
                        $datas['lst_access3']='';
                        $datas['req_card']=0;
                        $users_results = $this->admin_model->get_users($datas);
          if(count($users_results)>0)
          {    
            $user_arr=array();
              foreach($users_results as $user){
              if(!isset($user_arr[$user['id_usr']])){
                $user_arr[$user['id_usr']]=1;  
                $id = 0;
                if(!$datas['status_id'] || $datas['status_id'] == $this->user_status::BEGINNER){ 
                    $id = $user['id_usr'];                    
                }
                else{
                    $id = $user['id_usr'];                    
                }
                $card = $this->credit_card_model->get_credit_card($id);
                $st_nam= $this->T($user['st_name'], array(), $lang);
                //$st_nam=
           /* foreach ($user as $k => $dat) 
            {
             if($k!=-50)
                 echo $k;
            }  */ 
                
                $users[] = array(
                                //'user_id' => $user['id'],
                                'user_id' => $user['id_usr'],
                                'role_id' => $user['role_id'],
                                'name' => $user['name'],
                                'login' => $user['login'],
                                //'pass' => $user['pass'],
                                'email' => $user['email'],
                                'telf' => $user['telf'],
                                'status_id' => $user['status_id'],
                                //'status_date' => $user['status_date'],
                                //'language' => $user['language'],
                                'init_date' => $user['init_date'],
                                //'end_date' => $user['end_date'],
                                //'credit_card_name' => $card['credit_card_name'],
                                //'utm_source' => $user['utm_source'],
                                //'brazilian' => $user['brazilian'],
                                //'promotional_code' => $user['promotional_code'],
                                //'campaing_type' => $user['campaing_type'],
                                //'payment_type' => $user['payment_type'],
                                'amount_in_cents' =>0,
                                'date'=>$user['idpay'],
                                'status_date'=>$user['status_date'],
                                'st_name'=>$st_nam,
                                );
                //if(!$datas['card_name'])
                //break;             
              
              }
             }
            }
              } 
            }
            $result['options']=$lang1;
            if(count($users) > 0){                    
                $result['success'] = true;
                $result['message'] = 'Existem usuários';
                $result['resource'] = 'index';
                $result['users_array'] = $users;
            } else{
                $result['success'] = false;
                $result['message'] = $this->T("Não existem usuários para esses filtros", array(), $GLOBALS['language']); 
                $result['resource'] = 'index';
            }
        }
        else{
            $result['success'] = false;
            $result['message'] = $this->T("Não existe sessão ativa", array(), $GLOBALS['language']);
            $result['resource'] = 'index';
        }
        //echo json_encode($result);
        $json = json_encode($result);
        $msg=json_last_error_msg();
        echo $json;
    }

    public function show_robots() {
        $this->load->model('class/user_role');        
        //$this =& get_instance();
        $this->load_language();
        $lang= $this->session->userdata('language');
        $lang1=$lang1.'<option value="1">'.$this->T("ATIVO", array(),$lang).'</option>';                                        
        $lang1=$lang1.'<option value="2">'.$this->T("BLOQUEADO POR PAGAMENTO", array(),$lang).'</option>';                                        
        $lang1=$lang1.'<option value="4">'.$this->T("ELIMINADO", array(),$lang).'</option>';                                        
        //$lang1=$lang1.'<option value="6">'.$this->T("PENDENTE POR PAGAMENTO", array(),$lang).'</option>';                                        
        //$lang1=$lang1.'<option value="8">'.$this->T("INIthisANTE", array(),$lang).'</option>';                                        
        //$lang1=$lang1.'<option value="11">'.$this->T("NÃO MOLESTAR", array(),$lang).'</option>';
        //$lang1=$lang1.'<option value="12">'.$this->T("OCUPADO", array(),$lang).'</option>';
        $lang1=$lang1.'<option value="11">NÃO USAR MAIS</option>';
        $lang1=$lang1.'<option value="12">OCUPADO</option>';
        if ($this->session->userdata('role_id')==user_role::ADMIN){            
            $this->load->model('class/admin_model');
            $datas = $this->input->post();
            $robots_results = $this->admin_model->get_robots($datas);
            $robots = array();
            foreach($robots_results as $robot){
                $robots[] = array(
                                'id' => $robot['id'],
                                'login' => $robot['login'],
                                'pass' => $robot['pass'],
                                'ds_user_id' => $robot['ds_user_id'],
                                'status_id' => $robot['status_id'],
                                'profile_theme' => $robot['profile_theme'],
                                'recuperation_email_account' => $robot['recuperation_email_account'],
                                'recuperation_email_pass' => $robot['recuperation_email_pass'],
                                'creator_email' => $robot['creator_email'],
                                'recuperation_phone' => $robot['recuperation_phone'],
                                'init' => $robot['init'],
                                'end' => $robot['end']
                                );
            }
            $result['options']=$lang1;
            if(count($robots) > 0){                    
                $result['success'] = true;
                $result['message'] = 'Existem robots';
                $result['resource'] = 'index';
                $result['robots_array'] = $robots;
            } else{
                $result['success'] = false;
                $result['message'] = $this->T("Não existem robots para esses filtros", array(), $GLOBALS['language']); 
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

        public function get_robot_by_id() {
        $this->load->model('class/user_role');        
        //$this =& get_instance();
        //$this->load_language();
        if ($this->session->userdata('role_id')==user_role::ADMIN){            
            $this->load->model('class/admin_model');
            $datas = $this->input->post();
            $robots_results = $this->admin_model->get_robot_by_id($datas);
            $robots = array();
            foreach($robots_results as $robot){
                $robots[] = array(
                                'id' => $robot['id'],
                                'login' => $robot['login'],
                                'pass' => $robot['pass'],
                                'ds_user_id' => $robot['ds_user_id'],
                                'status_id' => $robot['status_id'],
                                'profile_theme' => $robot['profile_theme'],
                                'recuperation_email_account' => $robot['recuperation_email_account'],
                                'recuperation_email_pass' => $robot['recuperation_email_pass'],
                                'creator_email' => $robot['creator_email'],
                                'recuperation_phone' => $robot['recuperation_phone'],
                                'init' => $robot['init'],
                                'end' => $robot['end']
                                );
            }
            if(count($robots) > 0){                    
                $result['success'] = true;
                $result['message'] = 'Existem robots';
                $result['resource'] = 'index';
                $result['robots_array'] = $robots;
            } else{
                $result['success'] = false;
                $result['message'] = $this->T("Não existem robots para esses filtros", array(), $GLOBALS['language']); 
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
    
    public function insert_robot(){        
        $this->load->model('class/admin_model');        
        
        $datas = $this->input->post();
        //$language = $datas['new_language'];

        if ($this->session->userdata('id')){            
        //    if($language != "PT" && $language != "ES" && $language != "EN"){
        //        $language = $this->session->userdata('language');
        //    }

         $this->admin_model->insert_robot($datas);

         $result['success'] = true;
                //$result['message'] = $this->T("Robot alterado!", array(), $GLOBALS['language']);
         $result['message'] ="Robot inserido com sucesso!";
         $result['resource'] = 'robot_page';
        }
        else{
            
            //if($language != "PT" && $language != "ES" && $language != "EN"){
                //$language = $GLOBALS['language'];
            //}
            //else{
              //  $GLOBALS['language'] = $language;
            //}
            
            $result['success'] = false;
            $result['message'] = $this->T("Não existe sessão ativa", array(), $GLOBALS['language']);
            $result['resource'] = 'admin_page';
        }
        echo json_encode($result);
    }    
    

    public function update_robot(){        
        $this->load->model('class/admin_model');        
        
        $datas = $this->input->post();
        //$language = $datas['new_language'];

        if ($this->session->userdata('id')){            
        //    if($language != "PT" && $language != "ES" && $language != "EN"){
        //        $language = $this->session->userdata('language');
        //    }

            $result_update = $this->admin_model->update_robot($datas);

                $result['success'] = true;
                //$result['message'] = $this->T("Robot alterado!", array(), $GLOBALS['language']);
                $result['message'] ="Robot alterado com sucesso!";
                $result['resource'] = 'robot_page';
        }
        else{
            
            //if($language != "PT" && $language != "ES" && $language != "EN"){
                //$language = $GLOBALS['language'];
            //}
            //else{
              //  $GLOBALS['language'] = $language;
            //}
            
            $result['success'] = false;
            $result['message'] = $this->T("Não existe sessão ativa", array(), $GLOBALS['language']);
            $result['resource'] = 'robot_page';
        }
        echo json_encode($result);
    }    

    
    public function index() {    
        $this->load->model('class/user_role');        
        if ($this->session->userdata('role_id')==user_role::ADMIN){
            $this->load->view('admin_view', $param);
        }
        else{
            $this->load->view('user_view', $param);
        }
    }
    
    
    public function login_user() {            
        $this->load_language();
        $this->load->model('class/admin_model');
        $this->load->model('class/user_role');        
        if ($this->session->userdata('role_id')==user_role::ADMIN){        
            
            $datas = $this->input->post();
            $id_user = $datas['id_user'];
            $user_row = $this->admin_model->verify_account_user($id_user);
            
            if($user_row){  
                //$this->session->sess_destroy();
                $this->admin_model->set_session_as_client($user_row,$this->session);
                   
                $result['success'] = true;
                $result['message'] = 'Login success';
                if($user_row['role_id'] == user_role::CLIENT){
                    $result['resource'] = 'client';
                }else{
                    $result['resource'] = 'index';
                }
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
    
    public function payed_ticket(){
        $this->load_language();
        $this->load->model('class/admin_model');
        $this->load->model('class/user_role');        
        if ($this->session->userdata('role_id') == user_role::ADMIN){        
            
            $datas = $this->input->post();
            $order_number = $datas['order_number'];
            $valor_pago = $datas['valor_pago'];
            $data_pago = $datas['data_pago'];
            
            $validate = $this->validate_payed_date($order_number, $valor_pago, $data_pago);
            if($validate['success']){
                
                $result_update = $this->admin_model->payed_ticket_bank($order_number, $valor_pago, $data_pago);

                if($result_update){                      
                    $result['success'] = true;
                    $result['message'] = $this->T("Pagamento do boleto guardado corretamente.", array(), $GLOBALS['language']);                 
                    $id_client = $this->admin_model->client_by_order($order_number);                
                    $result_activate = $this->admin_model->activate_stoped_client($id_client, $order_number, $valor_pago);

                } else{
                    $result['success'] = false;
                    $result['message'] = $this->T("Não foi possível atualizar o pagamento do boleto.", array(), $GLOBALS['language']);                 
                }
            }
            else{
                    $result['success'] = false;
                    $result['message'] = $validate['message']; 
                }
        }
        else{
            $result['success'] = false;
            $result['message'] = $this->T("Não existe sessão ativa", array(), $GLOBALS['language']);
            $result['resource'] = 'index';
        }
        echo json_encode($result);
    }

    public function validate_payed_date($order, $value, $date){
        $result['success'] = false;
        $result['message'] = '';
        if( !( preg_match("/^[1-9][0-9]*([\.,][0-9]{1,2})?$/", $value) || 
                 preg_match("/^[0][\.,][1-9][0-9]?$/", $value) || 
                 preg_match("/^[0][\.,][0-9]?[1-9]$/", $value)) ){
            $result['message'] = 'O Valor Pago deve ser um valor monetário';
            return $result;
        }
        if( !(is_numeric($order)) ){
            $result['message'] = 'A ordem deve ser um número';
            return $result;
        }
        if( !(is_numeric($date)) ){
            $result['message'] = 'Problemas com a data selecionada';
            return $result;
        }
        $result['success'] = true;
        return $result;
    }

    //------------ADMIN desenvolvido para DUMBU-FOLLOWS-------------------
   
    public function admin_do_login() {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/system_config.php';
        $GLOBALS['sistem_config'] = new dumbu\cls\system_config();
        $datas['SERVER_NAME'] = $GLOBALS['sistem_config']->SERVER_NAME;        
        $datas = $this->input->post();        
        $this->load->model('class/user_model');
        $this->load->model('class/user_status');
        $this->load->model('class/user_role');
        $query = 'SELECT * FROM users'.
                ' WHERE login="' . $datas['user_login'] . '" AND pass="' . md5($datas['user_pass']) .
                '" AND role_id=' . user_role::ADMIN . ' AND status_id=' . user_status::ACTIVE;
        $user = $this->user_model->execute_sql_query($query);
        if(count($user)){
            $this->user_model->set_sesion($user[0]['id'], $this->session, '');
            $result['role'] = 'ADMIN';
            $result['authenticated'] = true;
            echo json_encode($result);
        } else{
            $result['resource'] = 'index#lnk_sign_in_now';
            $result['message'] = 'Credenciais incorretas';
            $result['cause'] = 'signin_required';
            $result['authenticated'] = false;
            echo json_encode($result);
        }
    }
    
    public function log_out() {
        $data['user_active'] = false;
        $this->load->model('class/user_model');
        $this->user_model->insert_washdog($this->session->userdata('id'),'CLOSING SESSION');
        $this->session->sess_destroy();
        header('Location: ' . base_url() . 'index.php/admin/');
    } 
    
        public function robot() {
        $this->load->model('class/user_role');                
        $this->load->model('class/system_config');
        
        if ($this->session->userdata('role_id')==user_role::ADMIN){
            //2. cargar los datos necesarios para pasarselos a la vista como parametro
            $param = array();            
            $param['language'] = $this->session->userdata('language');
            $this->load->view('robot_view', $param);
        }
        else{            
            $this->session->sess_destroy();
            $this->index();
        }        
    }

    
    public function view_admin(){
        $this->load->model('class/user_model');
        $this->load->model('class/user_role');
        if ($this->session->userdata('id') && $this->session->userdata('role_id')==user_role::ADMIN) {
            require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/system_config.php';
            $GLOBALS['sistem_config'] = new dumbu\cls\system_config();
            $datas['SERVER_NAME'] = $GLOBALS['sistem_config']->SERVER_NAME;
            $query = 'SELECT DISTINCT utm_source FROM clients';
            $datas['utm_source_list'] = $this->user_model->execute_sql_query($query);
            $data['SCRIPT_VERSION'] = $GLOBALS['sistem_config']->SCRIPT_VERSION;
            $data['section1'] = $this->load->view('responsive_views/admin/admin_header_painel', '', true);
            $data['section2'] = $this->load->view('responsive_views/admin/admin_body_painel', $datas, true);
            $data['section3'] = $this->load->view('responsive_views/admin/users_end_painel', '', true);
            $this->load->view('view_admin', $data);
        }        
    }

    public function list_filter_view_or_get_emails() {
        $this->load->model('class/user_role');
        if ($this->session->userdata('id') && $this->session->userdata('role_id')==user_role::ADMIN) {
            $this->load->model('class/admin_model');
            $form_filter = $this->input->get();
            require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/system_config.php';
            $GLOBALS['sistem_config'] = new dumbu\cls\system_config();
            $datas['SERVER_NAME'] = $GLOBALS['sistem_config']->SERVER_NAME;
            $datas['result'] = $this->admin_model->view_clients_or_get_emails_by_filter($form_filter);
            $datas['form_filter'] = $form_filter;
            $this->load->model('class/user_model');
            $this->user_model->insert_washdog($this->session->userdata('id'),'GET EMAILS');
            $query = 'SELECT DISTINCT utm_source FROM clients';
            $datas['utm_source_list'] = $this->user_model->execute_sql_query($query);
            $data['section1'] = $this->load->view('responsive_views/admin/admin_header_painel', '', true);
            $data['section2'] = $this->load->view('responsive_views/admin/admin_body_painel', $datas, true);
            $data['section3'] = $this->load->view('responsive_views/admin/users_end_painel', '', true);
            $this->load->view('view_admin', $data);
        } else{
            echo "Não pode acessar a esse recurso, deve fazer login!!";
        }
    }
    
    public function list_filter_view_pendences() {
        $this->load->model('class/user_role');
        if ($this->session->userdata('id') && $this->session->userdata('role_id')==user_role::ADMIN) {
            $this->load->model('class/user_model');
            $this->user_model->insert_washdog($this->session->userdata('id'),'VIEW PENDENCES');
            $this->load->model('class/admin_model');
            $form_filter = $this->input->get();
            $datas['result'] = $this->admin_model->view_pendences_by_filter($form_filter);
            $datas['form_filter'] = $form_filter;
            $data['section1'] = $this->load->view('responsive_views/admin/admin_header_painel', '', true);
            $data['section2'] = $this->load->view('responsive_views/admin/admin_body_painel_pendences', $datas, true);
            $data['section3'] = $this->load->view('responsive_views/admin/users_end_painel', '', true);
            $this->load->view('view_admin', $data);
        }
        else {
            echo "Não pode acessar a esse recurso, deve fazer login!!";
        }
    }
        
    public function create_pendence() {
        $this->load->model('class/user_role');
        if ($this->session->userdata('id') && $this->session->userdata('role_id')==user_role::ADMIN) {
            $this->load->model('class/admin_model');
            $form_filter = $this->input->get();
            $datas['result'] = $this->admin_model->create_pendence_by_form($form_filter);
            $datas['form_filter'] = $form_filter;
            $data['section1'] = $this->load->view('responsive_views/admin/admin_header_painel', '', true);
            $data['section2'] = $this->load->view('responsive_views/admin/admin_body_painel_pendences', $datas, true);
            $data['section3'] = $this->load->view('responsive_views/admin/users_end_painel', '', true);
            $this->load->view('view_admin', $data);
        }
        else {
            echo "Não pode acessar a esse recurso, deve fazer login!!";
        }
    }
    
    public function update_pendence() {
        $this->load->model('class/user_role');
        if ($this->session->userdata('id') && $this->session->userdata('role_id')==user_role::ADMIN) {
            $this->load->model('class/admin_model');
            $form_filter = $this->input->get();
            $datas['result'] = $this->admin_model->update_pendence($form_filter);
            $datas['form_filter'] = $form_filter;
            $data['section1'] = $this->load->view('responsive_views/admin/admin_header_painel', '', true);
            $data['section2'] = $this->load->view('responsive_views/admin/admin_body_painel_pendences', $datas, true);
            $data['section3'] = $this->load->view('responsive_views/admin/users_end_painel', '', true);
            $this->load->view('view_admin', $data);
        }
        else {
            echo "Não pode acessar a esse recurso, deve fazer login!!";
        }
    }
    
    public function resolve_pendence() {
        $this->load->model('class/user_role');
        if ($this->session->userdata('id') && $this->session->userdata('role_id')==user_role::ADMIN) {
            $this->load->model('class/admin_model');
            $form_filter = $this->input->get();
            $datas['result'] = $this->admin_model->resolve_pendence($form_filter);
            $datas['form_filter'] = $form_filter;
            $data['section1'] = $this->load->view('responsive_views/admin/admin_header_painel', '', true);
            $data['section2'] = $this->load->view('responsive_views/admin/admin_body_painel_pendences', $datas, true);
            $data['section3'] = $this->load->view('responsive_views/admin/users_end_painel', '', true);
            $this->load->view('view_admin', $data);
            
        }
        else {
            echo "Não pode acessar a esse recurso, deve fazer login!!";
        }
    }

    public function desactive_client() {
        $this->load->model('class/user_role');
        if ($this->session->userdata('id') && $this->session->userdata('role_id')==user_role::ADMIN) {
            $this->load->model('class/user_model');
            $this->load->model('class/user_status');
            $id = $this->input->post()['id'];
            try {
                require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/DB.php';
                $DB = new \dumbu\cls\DB();
                $DB->delete_daily_work_client($id);
                $this->user_model->update_user($id, array(
                    'status_id' => user_status::DELETED,
                    'end_date' => time()));
            } catch (Exception $exc) {
                echo $exc->getTraceAsString();
                $result['success'] = false;
                $result['message'] = "Erro no banco de dados. Contate o grupo de desenvolvimento!";
            } finally {
                $result['success'] = true;
                $result['message'] = "Cliente desativado com sucesso!";
            }
            echo json_encode($result);
        } else{
            echo "Não pode acessar a esse recurso, deve fazer login!!";
        }
    }

    public function recorrency_cancel() {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/system_config.php';
        $GLOBALS['sistem_config'] = new dumbu\cls\system_config();
        $this->load->model('class/user_role');
        if ($this->session->userdata('id') && $this->session->userdata('role_id')==user_role::ADMIN) {
            $this->load->model('class/client_model');
            $id = $this->input->post()['id'];
            $client = $this->client_model->get_client_by_id($id)[0];
            require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/Payment.php';
            $Payment = new \dumbu\cls\Payment();
            $status_cancelamento=0;
            if(count($client['initial_order_key'])>3){
                $response = json_decode($Payment->delete_payment($client['initial_order_key']));
                if ($response->success) 
                    $status_cancelamento=1;
            }
            $response = json_decode($Payment->delete_payment($client['order_key']));
            if ($response->success) 
                $status_cancelamento=$status_cancelamento+2;
                
            if ($status_cancelamento==0){
                $result['success'] = false;
                $result['message'] = 'Não foi possivel cancelar o pagamento, faça direito na Mundipagg!!';
            } else 
            if ($status_cancelamento==1){
                $result['success'] = true;
                $result['message'] = 'ATENÇÂO: somente foi cancelado o initial_order_key. Cancele manualmente a Recurrancia!!';
            }else
            if ($status_cancelamento==2){
                $result['success'] = true;
                $result['message'] = 'ATENÇÂO: somente foi cancelada a Recurrencia. Confira se o cliente não tem Initial Order Key!!';
            }else
            if ($status_cancelamento==3){
                $result['success'] = true;
                $result['message'] = 'Initial_Order_Key e Recurrencia cancelados corretamente!!';
            }
            echo json_encode($result);
        } else{
            echo "Não pode acessar a esse recurso, deve fazer login!!";
        }
    }

    public function reference_profile_view() {
        $this->load->model('class/user_role');
        //if ($this->session->userdata('id') && $this->session->userdata('role_id')==user_role::ADMIN) {
            $this->load->model('class/client_model');
            $this->load->model('class/user_model');
            $id = $this->input->get()['id'];
            
            $sql = 'SELECT plane_id FROM clients WHERE user_id='.$id;
            $plane_id = $this->user_model->execute_sql_query($sql);
            
            $sql = 'SELECT * FROM plane WHERE id='.$plane_id[0]['plane_id'];
            $plane_datas = $this->user_model->execute_sql_query($sql);
            
            $active_profiles = $this->client_model->get_client_active_profiles($id);
            $canceled_profiles = $this->client_model->get_client_canceled_profiles($id);
            $datas['active_profiles'] = $active_profiles;
            $datas['canceled_profiles'] = $canceled_profiles;
            $datas['my_daily_work'] = $this->get_daily_work($active_profiles);
            $datas['plane_datas'] = $plane_datas[0]['to_follow'];
            $data['section1'] = $this->load->view('responsive_views/admin/admin_header_painel', '', true);
            $data['section2'] = $this->load->view('responsive_views/admin/admin_body_painel_reference_profile', $datas, true);
            $data['section3'] = $this->load->view('responsive_views/admin/users_end_painel', '', true);
            $this->load->view('view_admin', $data);
        //} else{
            //echo "Não pode acessar a esse recurso, deve fazer login!!";
        //}
    }

    public function pendences() {
        $this->load->model('class/user_role');
        if ($this->session->userdata('id') && $this->session->userdata('role_id')==user_role::ADMIN) {
            /*$this->load->model('class/client_model');
            $id = $this->input->get()['id'];
            $active_profiles = $this->client_model->get_client_active_profiles($id);
            $canceled_profiles = $this->client_model->get_client_canceled_profiles($id);
            $datas['active_profiles'] = $active_profiles;
            $datas['canceled_profiles'] = $canceled_profiles;
            $datas['my_daily_work'] = $this->get_daily_work($active_profiles);*/
            $data['section1'] = $this->load->view('responsive_views/admin/admin_header_painel', '', true);
            $data['section2'] = $this->load->view('responsive_views/admin/admin_body_painel_pendences', '', true);
            $data['section3'] = $this->load->view('responsive_views/admin/users_end_painel', '', true);
            $this->load->view('view_admin', $data);
        }
        else {
            echo "Não pode acessar a esse recurso, deve fazer login!!";
        }
    }
        
    public function change_ticket_peixe_urbano_status_id() {
        $this->load->model('class/user_role');
        if ($this->session->userdata('id') && $this->session->userdata('role_id')==user_role::ADMIN){
            $this->load->model('class/client_model');
            $datas=$this->input->post();
            if($this->client_model->update_cupom_peixe_urbano_status($datas)){
                $result['success'] = true;
                $result['message'] = 'Stauts de Cupom atualizado corretamente';
            } else{
                $result['success'] = false;
                $result['message'] = 'Erro actualizando status do Cupom';
            }
            echo json_encode($result);
        } else{
            echo "Não pode acessar a esse recurso, deve fazer login!!";
        }
    }
    
    public function get_daily_work($active_profiles) {
        $this->load->model('class/client_model');
        $this->load->model('class/user_role');
        $n = count($active_profiles);
        $my_daily_work = array();
        //if($this->session->userdata('id') && $this->session->userdata('role_id')==user_role::ADMIN){
            for ($i = 0; $i < $n; $i++){
                $work = $this->client_model->get_daily_work_to_profile($active_profiles[$i]['id']);
                if (count($work)) {
                    $work = $work[0];
                }
                if (count($work)) {
                    $to_follow = $work['to_follow'];
                    $to_unfollow = $work['to_unfollow'];
                } else {
                    $to_follow = '----';
                    $to_unfollow = '----';
                }
                $tmp = array('profile' => $active_profiles[$i]['insta_name'],
                    'id' => $active_profiles[$i]['id'],
                    'to_follow' => $to_follow,
                    'to_unfollow' => $to_unfollow,
                    'end_date' => $active_profiles[$i]['end_date']
                );
                $my_daily_work[$i] = $tmp;
            }
            return $my_daily_work;
        //} else return 0;
        
    }
    
    public function watchdog() {
        $this->load->model('class/user_role');
        if ($this->session->userdata('id') && $this->session->userdata('role_id')==user_role::ADMIN) {
            $data['section1'] = $this->load->view('responsive_views/admin/admin_header_painel', '', true);
            $data['section2'] = $this->load->view('responsive_views/admin/admin_body_painel_watchdog', '' , true);
            $data['section3'] = $this->load->view('responsive_views/admin/users_end_painel', '', true);
            $this->load->view('view_admin', $data);
        }
        else {
            echo "Não pode acessar a esse recurso, deve fazer login!!";
        }
    }
    
    public function list_filter_view_watchdog() {
        $this->load->model('class/user_role');
        if ($this->session->userdata('id') && $this->session->userdata('role_id')==user_role::ADMIN) {
            $this->load->model('class/admin_model');
            $form_filter = $this->input->get();
            $datas['result'] = $this->admin_model->view_watchdog_by_filter($form_filter);
            $datas['form_filter'] = $form_filter;
            
            $daily_report = $this->get_daily_report($form_filter['user_id']);
            $datas['followings'] = $daily_report['followings'];
            $datas['followers']  = $daily_report['followers'];
            
            $data['section1'] = $this->load->view('responsive_views/admin/admin_header_painel', '', true);
            $data['section2'] = $this->load->view('responsive_views/admin/admin_body_painel_watchdog', $datas, true);
            $data['section3'] = $this->load->view('responsive_views/admin/users_end_painel', '', true);
            $this->load->view('view_admin', $data);
        } else{
            echo "Não pode acessar a esse recurso, deve fazer login!!";
        }
    }
    
    public function get_daily_report($id) {
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
    
    public function send_curl() {
        $this->load->model('class/user_role');
        if ($this->session->userdata('id') && $this->session->userdata('role_id')==user_role::ADMIN) {
            $datas = $this->input->post();
            $client_id = $datas['client_id'];
            $curl = urldecode($datas['curl']);
            
            try {
                require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/Robot.php';
                $Robot = new \dumbu\cls\Robot();
                $Robot->set_client_cookies_by_curl($client_id, $curl, NULL);
//                $result['success'] = false;
//                $result['message'] = "Test!";
//                echo json_encode($result);
            } catch (Exception $exc) {
                //echo $exc->getTraceAsString();
                $result['success'] = false;
                $result['message'] = "Erro no banco de dados. Contate o grupo de desenvolvimento!";
            } finally {
                $result['success'] = true;
                $result['message'] = "cURL enviada com sucesso!";
            }
            echo json_encode($result);
        } else {
            echo "Não pode acessar a esse recurso, deve fazer login!!";
        }
    }
    
    public function clean_cookies() {
        $this->load->model('class/user_role');
        if ($this->session->userdata('id') && $this->session->userdata('role_id')==user_role::ADMIN) {
            $client_id = $this->input->post()['client_id'];
            
            try {
                require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/DB.php';
                (new \dumbu\cls\DB())->set_cookies_to_null($client_id);
            } catch (Exception $exc) {
                $result['success'] = false;
                $result['message'] = "Erro no banco de dados. Contate o grupo de desenvolvimento!";
            } finally {
                $result['success'] = true;
                $result['message'] = "Cookies limpadas com sucesso!";
            }
            
            echo json_encode($result);
        } else {
            echo "Não pode acessar a esse recurso, deve fazer login!!";
        }
    }
}
