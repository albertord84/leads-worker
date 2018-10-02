<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Translation{
    protected $ci;
    
    function __construct(){
      $this->ci = & get_instance();
    }
   
    function T($token,$array_params){ 
        $this->ci->load->model('Translation_model');
        return $this->ci->Translation_model->get_text_by_token('COMO FUNCIONA');        
    }
}

?> 