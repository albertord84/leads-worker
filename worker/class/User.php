<?php

namespace leads\cls {
    require_once 'user_role.php';
    require_once 'user_status.php';
    
    class User{
        public $id; 
        public $role_id;
        public $name;
        public $login;
        public $pass;
        public $email;
        public $telf;
        public $status_id;
        public $status_date;
        public $languaje;
        public $init_date;
        public $end_date;
        
        public function id($value = NULL) {
            if(isset($value)){
                $this->id = $value;
            }
            else{
                return $this->id;
            }
        }
        
        function __construct() {
            
        }
        
        public function do_login($user_name,$user_pass){ 
            echo $user_name;
        }
         
        public function update_user(){     
            
        }
        
        public function load_user($user_id = 0){
            
        }
        
        public function disable_account(){    
            
        }
    }
}
?>
