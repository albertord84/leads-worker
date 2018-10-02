<?php

class Translation_model extends CI_Model {

    public $language = NULL;

    function __construct() {
    }

    public function get_text_by_token($token,$lang) {
        if($lang==='PT')
            $this->language ='portugues';
        else
        if($lang==='EN')
            $this->language ='ingles';
        else
        if($lang==='ES')
                $this->language ='espanol';
        else
            $this->language ='portugues';   //default language
            
        $this->db->select($this->language);
        $this->db->from('translation');
        $this->db->where('token', $token);
        $string = $this->db->get()->row_array();

        if (!count($string)) {
            $data['token'] = $token;
            $data['portugues'] = $token;
            $data['ingles'] = 'Not traduction yet';
            $data['espanol'] = 'Not traduction yet';
            $this->db->insert('translation', $data);
            return $token;
        } else
            return $string[$this->language];
    }

}
