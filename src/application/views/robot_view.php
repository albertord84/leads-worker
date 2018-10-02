<!DOCTYPE html>
<html lang="pt-BR">
    <head>
            <?php  $CI =& get_instance();?>
            <script type="text/javascript">var base_url ='<?php echo base_url()?>';</script>
            <script type="text/javascript">var language ='<?php echo $this->session->userdata('language');?>';</script>
                       
            <meta charset="UTF-8">
            <title>Dumbu-Leads</title>
            <meta name="viewport" content="width=device-width">
            <link rel="icon" type="image/png" href="<?php echo base_url().'assets/img/icon.png'?>">

            <!-- Font Awesome -->
            <!--<link rel="stylesheet" href="<?php // echo base_url().'assets/fonts/font-awesome.min.css'?>">-->            
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">            
            
            <!-- Tooltip -->
            <link rel="stylesheet" href="<?php echo base_url().'assets/js/popper.min.js'?>">
            
            <!-- Bootstrap -->
            <link rel="stylesheet" href="<?php echo base_url().'assets/bootstrap/css/bootstrap.min.css'?>">
            <link rel="stylesheet" href="<?php echo base_url().'assets/bootstrap/css/bootstrap-multiselect.css'?>">
            <link rel="stylesheet" href="<?php echo base_url().'assets/bootstrap/css/bootstrap-datepicker.min.css'?>">

            <!-- CSS -->
            <link rel="stylesheet" href="<?php echo base_url().'assets/css/estilo.css'?>"/>
            <link rel="stylesheet" href="<?php echo base_url().'assets/css/definicoes.css'?>"/>
            <link rel="stylesheet" href="<?php echo base_url().'assets/css/media.css'?>"/>
            <link rel="stylesheet" href="<?php echo base_url().'assets/css/ladda-themeless.min.css'?>">
            
            <!-- jQuery -->
            <script type="text/javascript" src="<?php echo base_url().'assets/js/jquery.js';?>"></script>
            
            <script type="text/javascript" src="<?php echo base_url().'assets/js/front.js'?>"></script>                
            <script type="text/javascript" src="<?php echo base_url().'assets/js/robot_page.js'?>"></script>
            <script type="text/javascript" src="<?php echo base_url().'assets/js/translation.js'?>"></script> 
            
            <script src="<?php echo base_url().'assets/js/spin.min.js'?>"></script>
            <script src="<?php echo base_url().'assets/js/ladda.min.js'?>"></script>           
    </head>
    <body style="background-color:#fff">
            <section class="topo-home fleft100 bk-black">
                    <header class="fleft100 pd-tb20">
                            <div class="container">
                                    <div class="col-md-2 col-sm-6 col-xs-6 col-md-offset-2">
                                        <a href=""><img src="<?php echo base_url().'assets/img/logo.png'?>" alt=""></a>
                                    </div>
                                    <div class="col-md-8 col-sm-6 col-xs-6 text-right menu">
                                        <button id="do_logout" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="b-none bk-none cl-fff ft-size13">
                                            <img src="<?php echo base_url().'assets/img/user.png'?>" style="position: relative;top: -2px;right: 5px;">
                                            <?php echo $CI->T("SAIR", array(),$language);?>
                                        </button>
                                    </div>					
                            </div>
                    </header>
            </section>
            <!--Admin Painel-->
            <!--<select name="" id="status_select">-->
                <?php
                   // echo '<option value="1">'.$CI->T("ATIVO", array(),$language).'</option>';                                        
                   // echo '<option value="2">'.$CI->T("BLOQUEADO POR PAGAMENTO", array(),$language).'</option>';                                        
                   // echo '<option value="4">'.$CI->T("ELIMINADO", array(),$language).'</option>';                                        
                   // echo '<option value="6">'.$CI->T("PENDENTE POR PAGAMENTO", array(),$language).'</option>';                                        
                   // echo '<option value="8">'.$CI->T("INICIANTE", array(),$language).'</option>';                                        
                   // echo '<option value="11">'.$CI->T("NÃO MOLESTAR", array(),$language).'</option>';                                        
                ?>
            <!--</select>
            <button type="button" id="do_show_users" class="btn btn-success">-->
                <?php 
                //echo $CI->T("MOSTRAR USUÁRIOS", array(),$language);
                ?>
            <!--</button>
            <div id = "container_users">                
            </div> -->
        <div id="link_robot">
            
        </div>    
        <div id="login_container2">
            <div class="row">
            <div class="col-xs-10" style="margin-left: 100px;">
            <table class="table">
            <tr class="list-group-item-success">
            <td style="width:100%; padding:5px"><b>Configuração da filtragem</b></td>
            </tr>
            </table>
            </div>
            </div>
            <div id="admin_form" class="row">
                <div class="col-md-1"></div>
                <div class="col-md-2">
                    <div class="center filters">
                        <b>Status</b>   
                        <select id="status_select" class="form-control">
                            <option value="-1">--SELECT--</option>
                            <option value="0">TODOS OS STATUS</option>
                            <?php
                             echo '<option value="1">'.$CI->T("ATIVO", array(),$language).'</option>';                                        
                             //echo '<option value="2">'.$CI->T("BLOQUEADO POR PAGAMENTO", array(),$language).'</option>';                                        
                             echo '<option value="4">'.$CI->T("ELIMINADO", array(),$language).'</option>';                                        
                             //echo '<option value="6">'.$CI->T("PENDENTE POR PAGAMENTO", array(),$language).'</option>';                                        
                             //echo '<option value="8">'.$CI->T("INICIANTE", array(),$language).'</option>';                                        
                             echo '<option value="11">'.$CI->T("NÃO USAR MAIS", array(),$language).'</option>';
                             //echo '<option value="12">'.$CI->T("OCUPADO", array(),$languaje).'</option>';
                            ?>
                            <!--<option value="11">NÃO USAR MAIS</option>-->
                            <option value="12">OCUPADO</option>
                            <!--<option value="1">ACTIVE</option>
                            <option value="2">BLOCKED_BY_PAYMENT</option>
                            <option value="3">BLOCKED_BY_PASSWORD</option>
                            <option value="4">DELETED</option>
                            <option value="6">PENDENT_BY_PAYMENT</option>
                            <option value="7">UNFOLLOW</option>
                            <option value="8">BEGINNER</option>
                            <option value="9">VERIFY_ACCOUNT</option>
                            <option value="10">BLOCKED_BY_TIME</option>-->
                        </select>
                    </div> 
                </div>
                <!--<br>-->
                <!--<div class="col-md-4">
                       <div class="center filters">
                       <b>Assinatura (inic)</b>
                        <input id = "signin_initial_date" type="text" class="form-control"  placeholder="MM/DD/YYYY" >
                        <b>Data da assinatura</b>
                    </div>
                    <div class="col-xs-1">
                        <b>do</b>
                    </div>
                    <div class="col-xs-5">
                        <input type="text" id="date_from" name="date_from" placeholder="mm/dd/yyyy" class="form-control">
                    </div>
                    <div class="col-xs-1">
                        <b>até</b>
                    </div>
                    <div class="col-xs-5">
                        <input type="text" id="date_to" name="date_to" placeholder="mm/dd/yyyy" class="form-control">
                    </div>-->
                    <!-- <div class="center">
                        <input type="text" id="date_from" name="date_from" placeholder="mm/dd/yyyy" class="form-control" style="max-width:160px">
                        <label for="date_to">até</label>
                        <input type="text" id="date_to" name="date_to" placeholder="mm/dd/yyyy" class="form-control" style="max-width:160px">
                    </div> 
                         <table>
                            <tr>
                                <th class="center filters" colspan="5">Data da assinatura</th>
                                <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                <th class="center filters">Observações</th>
                            </tr>
                            <tr>
                                <td><select id="day" class="form-control" style="min-width: 60px">
                                    <option value="0">Dia</option>
                                    <?php //for ($day = 1; $day <= 31; $day++) { ?>
                                    <option value="<?php //echo strlen($day)==1 ? '0'.$day : $day; ?>"><?php //echo strlen($day)==1 ? '0'.$day : $day; ?></option>
                                    <?php //} ?>
                                    </select></td>
                                    <td>&nbsp;<b>/</b>&nbsp;</td>
                                <td><select id="month" class="form-control" style="min-width: 70px">
                                    <option value="0">Mês</option>
                                    <?php //for ($month = 1; $month <= 12; $month++) { ?>
                                    <option value="<?php //echo strlen($month)==1 ? '0'.$month : $month; ?>"><?php //echo strlen($month)==1 ? '0'.$month : $month; ?></option>
                                    <?php //} ?>
                                    </select></td>
                                <td>&nbsp;<b>/</b>&nbsp;</td>
                                <td><select id="year" class="form-control" style="min-width: 75px">
                                    <option value="0">Ano</option>
                                    <?php //for ($year = 2016; $year <= date('Y'); $year++) { ?>
                                    <option value="<?php //echo $year; ?>"><?php //echo $year; ?></option>
                                    <?php //} ?>
                                    </select></td>
                                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                <td><select id="observations" class="form-control" >                            
                                    <option>NAO</option>
                                    <option>SIM</option>
                                </select></td>
                            </tr>
                        </table>
                    </div> 
                </div> -->
                <div class="col-md-4">
                    <div class="center filters">
                        <b>Data do status</b>
                    </div>
                    <div class="col-xs-1">
                        <b>do</b>
                    </div>
                    <div class="col-xs-5">
                        <input type="date" id="status_date_from" name="status_date_from" placeholder="mm/dd/yyyy" class="form-control">
                    </div>
                    <div class="col-xs-1">
                        <b>até</b>
                    </div>
                    <div class="col-xs-5">
                        <input type="date" id="status_date_to" name="status_date_to" placeholder="mm/dd/yyyy" class="form-control">
                    </div>
                </div>   
                <div class="col-md-1"></div>
                    <br>
                    <div class="col-xs-1">
                    <table> 
                        <tr><td>    
                <div class="col-md-2">
                    <div class="col-xs-5">
                    <div class="center filters">
                        <button  style="min-width:150px" id = "do_show_robots" type="button" class="btn btn-success ladda-button"  data-style="expand-left" data-spinner-color="#ffffff">
                            <span class="ladda-label">Listar</span>
                        </button>
                    </div>
                    </div>    
                </div>
    
                            </td><td>
                <!--<div class="col-md-2">
                    <div class="col-xs-5">
                    <div class="center filters">
                        
                        <button  style="min-width:150px" id = "execute_query_email" type="button" class="btn btn-success ladda-button"  data-style="expand-left" data-spinner-color="#ffffff">
                            <span class="ladda-label">Obter emails</span>
                        </button>
                    </div>
                    </div>    
                </div>-->    
                </td></tr></table> 
                </div>
            </div>
            <br>                
            
            <div class="row">
            <div class="col-xs-10" style="margin-left: 100px;">
            <table class="table">
            <tr class="list-group-item-success">
            <td style="width:100%; padding:5px"><b>Dados do novo robot-profile</b></td>
            </tr>
            </table>
            </div>
            </div>

            <div class="row">
            <div class="col-xs-10" style="margin-left: 100px;">
            <table class="table">
            <tr class="list-group-item-success">
            <td style="width:32%; padding:5px"><b>Dados gerais</b></td>
            <td style="width:26%; padding:5px"><b>Estado atual</b></td>
            <td style="width:32%; padding:5px"><b>Dados de contato</b></td>
            <td style="width:10%; padding:5px"><b>Operações</b></td>
            </tr>
            </table>
            </div>
            </div>
            <div id="tablarobots">
            <div class="col-xs-10" style="margin-left: 100px;">
            <table class="table">

                            <tr class="list-group-item-success" id="row-client" style="visibility: visible;display: block; background-color: #dff0d8">
                                <td style="text-align:right; width:5%; padding:5px">
                                    <b></b>
                                    </td>                                
                                    <td style="width:30%; padding:5px">
                                    <!--<b>Dumbu ID: </b><input type="text" name="naminprobdumbuid" id= "idinprobdumbuid" value=""><br><br>-->
                                    <b>Profile: </b><input type="text" name="naminprobprofile" id= "idinprobprofile" value=""><br><br>
                             
                                    <b>Password: </b><input type="text" name="naminprobpass" id= "idinprobpass" value=""><br><br>
                                    <b>DS ID: </b><input type="text" name="naminprobdsid" id= "idinprobdsid" value=""><br><br>
                             
                                    <b>Tema: </b><input type="text" name="naminprobtheme" id= "idinprobtheme" value=""><br><br></td>
                                    <td style="width:25%; padding:5px">
                                    <b>Status: </b><br>
                                    <select class="robot_atribute" id="idselestatus" name="nameselestatus" value="">
                            <option value="-1">--SELECT--</option>
                            <option value="0">TODOS OS STATUS</option>
                            <?php
                             echo '<option value="1">'.$CI->T("ATIVO", array(),$language).'</option>';                                        
                             //echo '<option value="2">'.$CI->T("BLOQUEADO POR PAGAMENTO", array(),$language).'</option>';                                        
                             echo '<option value="4">'.$CI->T("ELIMINADO", array(),$language).'</option>';                                        
                             //echo '<option value="6">'.$CI->T("PENDENTE POR PAGAMENTO", array(),$language).'</option>';                                        
                             //echo '<option value="8">'.$CI->T("INICIANTE", array(),$language).'</option>';                                        
                             echo '<option value="11">'.$CI->T("NÃO USAR MAIS", array(),$language).'</option>';
                             //echo '<option value="12">'.$CI->T("OCUPADO", array(),$languaje).'</option>';
                            ?>
                            <!--<option value="11">NÃO USAR MAIS</option>-->
                            <option value="12">OCUPADO</option>
                            <!--<option value="1">ACTIVE</option>
                            <option value="2">BLOCKED_BY_PAYMENT</option>
                            <option value="3">BLOCKED_BY_PASSWORD</option>
                            <option value="4">DELETED</option>
                            <option value="6">PENDENT_BY_PAYMENT</option>
                            <option value="7">UNFOLLOW</option>
                            <option value="8">BEGINNER</option>
                            <option value="9">VERIFY_ACCOUNT</option>
                            <option value="10">BLOCKED_BY_TIME</option>-->
                                    </select>
                                    <br>
                                    <br>
                                    <b>Data de inicio: </b><br>
                                    <input id="idselinit" name="nameseleinit" type="date" class="robot_atribute" value="">
                                    </input>
                                    <br>
                                    <br>
                                    <b>Data final: </b><br>
                                    <input id="idselend" name="nameselend" type="date" class="robot_atribute" value="">
                                    </input>
                                    </td>
                                    <td style="width:30%; padding:5px">
                                    <b>Recobrar senha usando email: </b><br><input type="text" name="naminprobpassemail" id= "idinprobpassemail" value=""><br><br>
                                    <b>Email de creação da conta: </b><br><input type="text" name="naminprobcreatoremail" id= "idinprobcreatoremail" value=""><br><br>
                             
                                    <b>Recobrar conta usando email: </b><br><input type="text" name="naminprobaccountemail" id= "idinprobaccountemail" value=""><br><br>
                                    </td>
                                    <td style="width:10%; padding:5px">
                                    <button  style="min-width:150px" id = "idbtnapply" name="namebtnapply" type="button" class="robotok"  data-spinner-color="#ffffff">                                    
                                    Inserir</button>
                                    <!--<br>
                                    <br>
                                    <button  style="min-width:150px" id = "idbtnapply" name="namebtnapply" type="button" class="robotcancel"  data-spinner-color="#ffffff"> 
                                    Cancel</button>-->
                                    </td>
                               
                                   
                                    </tr>
 
                
            </table>
            </div>
            </div>
                
            </div>
            <div class="row">
                <!--<div class="col-md-1"></div>
                <div class="col-md-2">
                    <div class="center filters">
                        <b>Não recebe trabalho há mais de</b>
                        <input id="days_no_work"  class="form-control" placeholder="Número de dias">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-1"></div>
                <div class="col-md-2">
                    <div class="center filters">
                        <b>Perfis de Ref. ativos</b> 
                        <select id="pr_ativos" class="form-control" >
                            <option>--SELECT--</option>
                            <option>NAO</option>
                            <option>SIM</option>
                        </select>    
                    </div>
                </div>
                <div class="col-md-2">
                    <?php if ($SERVER_NAME == "ONE") { ?>
                        <div class="center filters">
                            <b>Idioma</b> 
                            <select id="idioma" class="form-control" >
                                <option>--SELECT--</option>
                                <option value="EN">EN - English</option>
                                <option value="PT">PT - Português</option>
                                <option value="ES">ES - Español</option>
                            </select>    
                        </div>
                    <?php } else { ?>
                        <input id="idioma" name="idioma" type="hidden" value="--SELECT--">
                    <?php } ?>
                </div>-->
                <div style="text-align:center;">
            
                        <div id="container_robots">
                        </div>
                </div> 
            </div>    
            <footer class="fleft100 pd-tb50 bk-fff text-center">
                    <div class="container">
                            <div class="fleft100 m-top40">
                                    <img src="<?php echo base_url().'assets/img/copy.png'?>" alt="">
                                    <span class="fleft100 cp m-top15">DUMBU - 2016 - <?php echo $CI->T("TODOS OS DIREITOS RESERVADOS", array(),$language);?></span>
                            </div>
                    </div>
            </footer>
    
    <!--modal_container_alert_message-->
    <div class="modal fade" style="top:30%" id="modal_alert_message" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div id="modal_container_alert_message" class="modal-dialog modal-sm" role="document">                                                          
            <div class="modal-content">
                <div class="modal-header">
                    <button id="btn_modal_close" type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <img src="<?php echo base_url().'assets/img/FECHAR.png'?>" alt="cancel"> <!--<spam aria-hidden="true">&times;</spam>-->
                    </button>
                    <h5 class="modal-title" id="myModalLabel"><b><?php echo $CI->T("Mensagem", array(),$language) ?></b></h5>                        
                </div>
                <div class="modal-body">                                            
                    <p id="message_text"></p>                        
                </div>
                <div class="modal-footer text-center">
                    <button id="accept_modal_alert_message" type="button" class="btn btn-default active text-center ladda-button" data-style="expand-left" data-spinner-color="#ffffff">
                        <spam class="ladda-label"><div style="color:white; font-weight:bold">OK</div></spam>
                    </button>                    
                </div>
            </div>
        </div>                                                        
    </div>
    </body>
    
    
    <!--[if lt IE 9]>
    <script src="js/jquery-1.9.1.js"></script>
    <![endif]-->
    <!--[if gte IE 9]><!-->
<!--    <script src="<?php //echo base_url().'assets/js/jquery-3.1.1.min.js'?>"></script>-->
    <!--<![endif]-->
    <script src="<?php echo base_url().'assets/bootstrap/js/bootstrap.min.js'?>"></script>
    <script src="<?php echo base_url().'assets/bootstrap/js/bootstrap-multiselect.js'?>"></script>
    <script src="<?php echo base_url().'assets/bootstrap/js/bootstrap-datepicker.min.js'?>"></script>
    <script src="<?php echo base_url().'assets/bootstrap/js/bootstrap-datepicker.pt-BR.min.js'?>"></script>
    <!-- FILTRAR -->
    <script src="<?php echo base_url().'assets/js/filtrar.js'?>"></script> 
    <!-- VALIDATE -->
    <script src="<?php echo base_url().'assets/js/validate.js'?>" type="text/javascript"></script>
    <!-- MASCARAS -->
    <script src="<?php echo base_url().'assets/js/maskinput.js'?>" type="text/javascript"></script>
    <!-- Scripts -->
    <script src="<?php echo base_url().'assets/js/script.js'?>" type="text/javascript"></script>

</html>
