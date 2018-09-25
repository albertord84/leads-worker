<?php



namespace leads\cls {
    
    require_once 'User.php';
    require_once 'campaing_status.php';  
    require_once 'profiles_status.php';
    require_once 'DB.php';
    
        
    class Campaings {
        public $id;
        public $client_id;
        public $campaing_type_id;
        public $campaing_status_id;
        public $total_daily_value; //orçamento
        public $available_daily_value; //dinheiro disponível para ser gasto no dia        
        public $client_objetive;
        //public $last_soled; eso es controlado desde el cliente
        public $created_date;
        public $end_date;
        public $last_accesed;
        
        public function __construct($DB = NULL) {
            $this->Robot = new Robot($DB);
            $this->Robot->config = $GLOBALS['sistem_config'];
            $this->Gmail = new Gmail();
            $this->DB = $DB ? $DB : new \leads\cls\DB();
        }        

        public function get_campaings_of_client($client_id,$campaing_status) {  
            $Campaings = array();
            $DB = new \leads\cls\DB();
            try {
                $DB->connect();
                $sql = ""
                        . "SELECT * FROM campaings "
                        . " WHERE campaings.client_id = $client_id "
                        . " AND campaings.campaing_status_id = $campaing_status "
                        . "AND campaings.total_daily_value > 0 "
                        . " ORDER BY campaings.id; ";
                $client_campaings = mysqli_query($DB->connection, $sql);
                while ($campaing_data= $client_campaings->fetch_array()) {
                    array_push($Campaings,$this->fill_client_campaing_data($campaing_data));
                }
                return $Campaings;
            } catch (\Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }
        
        public function fill_client_campaing_data($campaing_data){
            if ($campaing_data) {
                $datas = array(
                    'id'=> $campaing_data['id'],
                    'client_id'=> $campaing_data['client_id'],
                    'campaing_type_id'=> $campaing_data['campaing_type_id'],
                    'campaing_status_id'=> $campaing_data['campaing_status_id'],
                    'total_daily_value'=> $campaing_data['total_daily_value'],
                    'available_daily_value'=> $campaing_data['available_daily_value'],
                    'last_accesed'=> $campaing_data['last_accesed'],
                    'client_objetive'=> $campaing_data['client_objetive'],
                    //'last_soled'=> $campaing_data['last_soled'],
                    'created_date'=> $campaing_data['created_date'],
                    'end_date'=> $campaing_data['end_date']
                );
            }
            return  (object)$datas;
         }
         
        public function get_profiles_of_campaings($campaing_id) {  
            $Profiles = array(); 
            $DB = new \leads\cls\DB();
            try {
                $DB->connect();
                $sql = ""
                        . "SELECT * FROM profiles "
                        . " WHERE profiles.campaing_id = $campaing_id "
                        . " AND profiles.profile_status_id =".profiles_status::ACTIVE
                        . " ORDER BY profiles.id; ";
                $campaing_profiles = mysqli_query($DB->connection, $sql);
                while ($profile_data = $campaing_profiles->fetch_array()) {
                    array_push($Profiles,$this->fill_profiles_campaing_data($profile_data));
                }
                return $Profiles;
            } catch (\Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }
        
        public function fill_profiles_campaing_data($profile_data){
            if ($profile_data) {
                $datas = array(
                    'id'=> $profile_data['id'],
                    'campaing_id'=> $profile_data['campaing_id'],
                    'profile'=> $profile_data['profile'],
                    'insta_id'=> $profile_data['insta_id'],
                    'cursor'=> $profile_data['cursor'],
                    'last_accesed'=> $profile_data['last_accesed']
                );
            }
            return  (object)$datas;
         }
    }
}
?>
