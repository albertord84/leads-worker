<?php

namespace leads\cls {
    
    class Profile{ //information from any Instagram profile
        
        public $id;        
        public $insta_id;
        public $campaing_id;
        public $profile;
        public $cursor;
        public $last_access;
        public $end_date;
        public $deleted;
        public $profile_type_id;
        public $amount_leads;
        public $amount_analysed_profiles;
                        
        public function __construct($DB = NULL) {
            $this->Robot = new Robot($DB);
            $this->Robot->config = $GLOBALS['sistem_config'];
            $this->Gmail = new Gmail();
            $this->DB = $DB ? $DB : new \leads\cls\DB();
        }
        
        public function is_profile_private($ref_prof) {
            $ref_prof_data = $this->get_insta_ref_prof_data($ref_prof);
            return $ref_prof_data ? $ref_prof_data->is_private : NULL;
        }
        
        public function get_insta_ref_prof_data($ref_prof) {
            $Robot = new Robot();
            return $Robot->get_insta_ref_prof_data($ref_prof);
        }
        
        public function get_insta_ref_prof_leads($ref_prof) {
            $Robot = new Robot();
            return $Robot->get_insta_ref_prof_leads($ref_prof);
        }
        
        public function save_insta_ref_prof_leads($ref_prof) {
            $Robot = new Robot();
            return $Robot->get_insta_ref_prof_leads($ref_prof);
        }
        
    }
}

?>
