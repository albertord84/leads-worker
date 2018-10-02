<!DOCTYPE html>
<html lang="en">
  <head>
        <!-- jQuery -->
        <script type="text/javascript" src="<?php echo base_url().'assets/js/jquery.js';?>"></script>
        <!-- Bootstrap -->
        <link href="<?php echo base_url().'assets/bootstrap/css/bootstrap.min.css';?>" rel="stylesheet">
        <link href="<?php echo base_url().'assets/css/loading.css';?>" rel="stylesheet">
        <link href="<?php echo base_url().'assets/css/style.css';?>" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="<?php echo base_url().'assets/css/stylenew.css?';?>" />
        <link rel="stylesheet" type="text/css" href="<?php echo base_url().'assets/css/default.css?';?>" />
        <link rel="stylesheet" type="text/css" href="<?php echo base_url().'assets/css/component.css?';?>" />
        <link rel="stylesheet" href="<?php echo base_url().'assets/css/ladda-themeless.min.css'?>">
        <script type="text/javascript" src="<?php echo base_url().'assets/js/modernizr.custom.js';?>"></script>                
        <script src="<?php echo base_url().'assets/js/spin.min.js'?>"></script>
        <script src="<?php echo base_url().'assets/js/ladda.min.js'?>"></script>                
        <script type="text/javascript">var base_url ='<?php echo base_url()?>';</script>
        <script type="text/javascript" src="<?php echo base_url().'assets/js/front.js'?>"></script>                
        <script type="text/javascript" src="<?php echo base_url().'assets/js/client_page.js'?>"></script>
        
        <style>
        div.section_singin {
            position: absolute;
            top: 10px;
            left: 10px;
        } 
        div.section_login {
            position: absolute;
            top: 10px;
            left: 400px;
        }
        div.section_show_campaing {
            position: absolute;            
            top: 270px;
            left: 400px;            
        }
        div.section_create_campaing {
            position: absolute;
            top: 330px;            
        } 
        div.actions_campaing {
            position: absolute;
            top: 100px;
            left: 400px;
        }
        div.campaings {
            position: absolute;
            top: 20px;
            left: 850px;                        
        } 

        table, td, th {
            border: 1px solid black;
        }

        table {
            border-collapse: collapse;
        }

        th {
            text-align: left;
        }

        td {
            vertical-align: top;
        }

        </style>

  </head>
  <body>                 
      <div class = "section_singin">  
        Name <p> <input type="text" id="name_registration"  value="" /></p>        
        Email <p>  <input type="text" id="email_registration" value="" /></p>
        Username <p> <input type="text" id="user_registration"  value="" /></p>        
        Password <p><input type="password" id="pass_registration" value="" /></p>     
               
        <button id="do_signin" type="button" class="btn btn-success active text-center ladda-button" data-style="expand-left" data-spinner-color="#ffffff">
            <spam class="ladda-label"><div style="color:white; font-weight:bold">Create account</div></spam>
        </button>
        <button id="do_cancel_signin" type="button" class="btn btn-success active text-center ladda-button" data-style="expand-left" data-spinner-color="#ffffff">
            <spam class="ladda-label"><div style="color:white; font-weight:bold">Cancel account</div></spam>
        </button>
      </div>  
         
        
        <div class = "section_login">   
            Login<br>
            <input type="text" placeholder="username" id="user_login"  value="" /><br><br>

            <input type="password" placeholder="password" id="pass_login" value="" /><br><br>            

            <button id="do_login" type="button" class="btn btn-success active text-center ladda-button" data-style="expand-left" data-spinner-color="#ffffff">
                <spam class="ladda-label"><div style="color:white; font-weight:bold">LOGIN</div></spam>
            </button>       
        
            <button id="do_logout" type="button" class="btn btn-success active text-center ladda-button" data-style="expand-left" data-spinner-color="#ffffff">
                <spam class="ladda-label"><div style="color:white; font-weight:bold">LOGOUT</div></spam>
            </button>
         </div> 
        
        <div class = "section_create_campaing">   
        <form id ="profileForm">
            
            Campaing type<br> 
            <select id = "campaing_type" name="campaing_type">
                <option value="1">REFERENCE</option>
                <option value="2">GEOLOCATION</option>
                <option value="3">HASHTAG</option>                
              </select> 
            <br>
            Objective <br><input id = "objective" type="text" name="objective"  value="" />
            <br>            
            Profile <br>
            <select id = "profile_type_temp" name="profile_type_temp">
                <option value="1">REFERENCE</option>
                <option value="2">GEOLOCATION</option>
                <option value="3">HASHTAG</option>                
            </select> 
            <br>
            <input id = "profile_temp" type="text" name="profile_temp"  value="" />
            <button id="do_add_profile_temp" type="button" class="btn btn-success active text-center ladda-button" data-style="expand-left" data-spinner-color="#ffffff">
                <spam class="ladda-label"><div style="color:white; font-weight:bold">+</div></spam>
            </button>            
            <button id="do_delete_profile_temp" type="button" class="btn btn-success active text-center ladda-button" data-style="expand-left" data-spinner-color="#ffffff">
                <spam class="ladda-label"><div style="color:white; font-weight:bold">x</div></spam>
            </button>            
        </form>
            Daily value <br><input type="text" id="daily_value"  value="" /><br><br>
            
            <button id="do_save_campaing" type="button" class="btn btn-success active text-center ladda-button" data-style="expand-left" data-spinner-color="#ffffff">
                <spam class="ladda-label"><div style="color:white; font-weight:bold">Save Campaign</div></spam>
            </button>
            
            <div class="actions_campaing">
                Id_campaing <input id = "id_campaing" type="text" name="profile"  value="" />
                <!-- <button id="do_activate_campaing" type="button" class="btn btn-success active text-center ladda-button" data-style="expand-left" data-spinner-color="#ffffff">
                    <spam class="ladda-label"><div style="color:white; font-weight:bold">play</div></spam>
                </button>
                <button id="do_pause_campaing" type="button" class="btn btn-success active text-center ladda-button" data-style="expand-left" data-spinner-color="#ffffff">
                    <spam class="ladda-label"><div style="color:white; font-weight:bold">pause</div></spam>
                </button>
                <button id="do_cancel_campaing" type="button" class="btn btn-success active text-center ladda-button" data-style="expand-left" data-spinner-color="#ffffff">
                    <spam class="ladda-label"><div style="color:white; font-weight:bold">cancel</div></spam>
                </button> -->
                <br>
                Profile in campaing<br>
                <select id = "profile_type" name="profile_type">
                    <option value="1">REFERENCE</option>
                    <option value="2">GEOLOCATION</option>
                    <option value="3">HASHTAG</option>                
                </select> 
                <input id = "profile" type="text" name="profile"  value="" />                
                <button id="do_add_profile" type="button" class="btn btn-success active text-center ladda-button" data-style="expand-left" data-spinner-color="#ffffff">
                    <spam class="ladda-label"><div style="color:white; font-weight:bold">+</div></spam>
                </button>                            
                <button id="do_delete_profile" type="button" class="btn btn-success active text-center ladda-button" data-style="expand-left" data-spinner-color="#ffffff">
                    <spam class="ladda-label"><div style="color:white; font-weight:bold">x</div></spam>
                </button>
            </div>
        </div>   
      <div class = "section_show_campaing"> 
          <button id="do_go_add_card" type="button" class="btn btn-success active text-center ladda-button" data-style="expand-left" data-spinner-color="#ffffff">
                <spam class="ladda-label"><div style="color:white; font-weight:bold">Add credit card</div></spam>
          </button>
          
          <button id="do_go_leads" type="button" class="btn btn-success active text-center ladda-button" data-style="expand-left" data-spinner-color="#ffffff">
                <spam class="ladda-label"><div style="color:white; font-weight:bold">Get leads</div></spam>
          </button>
        
        <button id="do_show_campaings" type="button" class="btn btn-success active text-center ladda-button" data-style="expand-left" data-spinner-color="#ffffff">
          <spam class="ladda-label"><div style="color:white; font-weight:bold">Show Campaigns</div></spam>
        </button>        
      </div>
      <div class = "campaings">     
        <p><div id = "demo_show_campaings">          
            <?php 
            $html = '';
            
            foreach ($campaings as $campaing) {
                $html .= '<div id = divcamp_'.$campaing['campaing_id'].'>';
                    $html .= '<table>';
                        $html .= '<tr>
                                    <th>Id</th>
                                    <th>Objective</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Value per day</th> 
                                    <th>Available</th> 
                                    <th>Profiles</th> 
                                  </tr>';
                        $html .= '<tr>';
                            $html .= '<td>'.$campaing['campaing_id'].'</td>';
                            $html .= '<td>'.$campaing['client_objetive'].'</td>';
                            $html .= '<td>'.$campaing['campaing_type_id'].'</td>';
                            $html .= '<td>'.$campaing['campaing_status_id'].'</td>';
                            $html .= '<td>'.$campaing['total_daily_value'].'</td>';
                            $html .= '<td>'.$campaing['available_daily_value'].'</td>';            
                            $html .= '<td>';
                            foreach ($campaing['profile'] as $profile){
                                $html .= $profile['profile'].'<br>';    
                            }
                            $html .= '</td>';
                        $html .= '</tr>';
                        $html .= '<tr>';
                        $html .='<td colspan="7"> 
                                <button name = "play" id="do_activate_campaing_'.$campaing['campaing_id'].'" type="button" class="btn btn-success active text-center ladda-button play" data-style="expand-left" data-spinner-color="#ffffff">
                                    <spam class="ladda-label"><div style="color:white; font-weight:bold">play</div></spam>
                                </button>
                                <button name = "pause" id="do_pause_campaing_'.$campaing['campaing_id'].'" type="button" class="btn btn-success active text-center ladda-button pause" data-style="expand-left" data-spinner-color="#ffffff">
                                        <spam class="ladda-label"><div style="color:white; font-weight:bold">pause</div></spam>
                                </button>
                                <button name = "cancel" id="do_cancel_campaing_'.$campaing['campaing_id'].'" type="button" class="btn btn-success active text-center ladda-button cancel" data-style="expand-left" data-spinner-color="#ffffff">
                                        <spam class="ladda-label"><div style="color:white; font-weight:bold">cancel</div></spam>
                                </button>                            
                                </td>';                        
                        $html .= '</tr>';
                    $html .= '</table>';
                $html .= '</div>';
            }
            echo $html;
        ?>
        </div></p>
      </div>
  </body>
</html>