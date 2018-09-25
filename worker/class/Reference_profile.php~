<?php

namespace dumbu\cls {
    require_once 'Profile.php';
    require_once 'DB.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/class/DB.php';
    //require_once 'Robot.php';    
    /**
     * class Reference_profile
     * 
     */
    class Reference_profile extends Profile {
        /** Aggregations: */
        /** Compositions: */
        /*         * * Attributes: ** */

        /**
         * 
         * @access public
         */
        public $follows;

        /**
         * 
         * @access public
         */
        public $insta_follower_cursor;

        /**
         * 
         * @param type $ref_prof_id
         * @return type
         */
        public function get_follows() {
            return $this->id ? static_get_follows($this->id) : 0;
        }

        /**
         * 
         * @param type $ref_prof_id
         * @return type
         */
        static function static_get_follows($ref_prof_id) {
            $DB = new DB();
            $follows_count = $DB->get_reference_profiles_follows($ref_prof_id);
            return $follows_count;
        }

        /**
         * 
         *
         * @return Reference_profile
         * @access public
         */
        public function update_reference_profile() {
            
        }

// end of member function update_reference_profile

        /**
         * 
         *
         * @return bool
         * @access public
         */
        public function remove_reference_profile() {
            
        }

// end of member function remove_reference_profile

        /**
         * 
         *
         * @return Reference_profile
         * @access public
         */
        public function add_reference_profile() {
            
        }

// end of member function add_reference_profile
        
        function __set($name, $value) {
            if (method_exists($this, $name)) {
                $this->$name($value);
            } else {
                // Getter/Setter not defined so set as property of object
                $this->$name = $value;
            }
        }

        function __get($name) {
            if (method_exists($this, $name)) {
                return $this->$name();
            } elseif (property_exists($this, $name)) {
                // Getter/Setter not defined so return property if it exists
                return $this->$name;
            }
            return null;
        }

 // end of generic setter an getter definition
        
    }

    // end of Reference_profile
}

?>
