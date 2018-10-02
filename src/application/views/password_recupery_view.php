<!DOCTYPE html>
<html lang="pt-BR">
    <head> 
            <meta>            
            <?php  $CI =& get_instance(); ?>            
            <script type="text/javascript">var base_url ='<?php echo base_url()?>';</script>
            <script type="text/javascript">var language ='<?php echo $language;?>';</script>
            <script type="text/javascript">var token ='<?php echo $token;?>';</script>
            <script type="text/javascript">var login ='<?php echo $login;?>';</script>
            
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
            <link rel="stylesheet" href="<?php echo base_url().'assets/bootstrap/css/bootstrap-datepicker.min.css?'.$SCRIPT_VERSION;?>">

            <!-- CSS -->
            <link rel="stylesheet" href="<?php echo base_url().'assets/css/estilo.css?'.$SCRIPT_VERSION;?>"/>
            <link rel="stylesheet" href="<?php echo base_url().'assets/css/definicoes.css?'.$SCRIPT_VERSION;?>"/>
            <link rel="stylesheet" href="<?php echo base_url().'assets/css/media.css?'.$SCRIPT_VERSION;?>"/>
            <link rel="stylesheet" href="<?php echo base_url().'assets/css/ladda-themeless.min.css'?>">
            
            <!-- jQuery -->
            <script type="text/javascript" src="<?php echo base_url().'assets/js/jquery.js';?>"></script>
            
            <script type="text/javascript" src="<?php echo base_url().'assets/js/recovery_pass.js?'.$SCRIPT_VERSION;?>"></script> 
            <script type="text/javascript" src="<?php echo base_url().'assets/js/translation.js?'.$SCRIPT_VERSION;?>"></script> 
            <script type="text/javascript" src="<?php echo base_url().'assets/js/recupery_pass.js?'.$SCRIPT_VERSION;?>"></script> 
            
            <script src="<?php echo base_url().'assets/js/spin.min.js'?>"></script>
            <script src="<?php echo base_url().'assets/js/ladda.min.js'?>"></script>           
            <style>
                .modal { overflow: auto !important; }
            </style>
    </head>
    <body style="background-color:#fff">
            
            <section class="topo-home fleft100 bk-black">
                    <header class="fleft100 pd-tb20">
                            <div class="container">
                                    <div class="col-md-8 col-sm-6 col-xs-6 col-md-offset-2">
                                        <a href=""><img src="<?php echo base_url().'assets/img/logo.png'?>" alt=""></a>
                                    </div>                                    
                                    <div class="col-md-2 col-sm-6 col-xs-6 text-right">                                         
                                        <a style="color:white;text-decoration:none;" href="<?php echo base_url().'index.php/welcome/index?language='.$language; ?>">
                                            <img src="<?php echo base_url().'assets/img/home.png'?>" style="position: relative;top: -4px;right: 5px;">
    <!--                                            <i class="fa fa-home"></i>-->
                                            <?php echo mb_strtoupper($CI->T("HOME", array(),$language));?>
                                        </a>
                                    </div>                                    					
                            </div>
                    </header>
            </section>
            
            <?php            
                if(!isset($token)){
            ?>
                <div class="fleft100 pd-tb30 m-top50 text-center">
                    <div class="container">                            
                        <div class="col-md-4 col-sm-4 col-xs-12 col-md-offset-4 col-sm-offset-4">
                            <div class="col-md-8 col-sm-12 col-xs-12 pd-0 fnone i-block">
                                    <form action="" class="fleft100 fmr-cadastro">					
                                        <div class="fleft100 pd-20 bk-fff text-left">                                                            
                                            <div id="datas_form">
                                                <div class="form-group">
                                                    <label for="nome"><?php echo $CI->T("Nome de usuário", array(),$language);?></label>
                                                    <input class="form-control" id="user_recovery" onkeyup="javascript:this.value=this.value.toLowerCase();" style="text-transform:lowercase;">
                                                </div>
                                                <div class="form-group">
                                                    <label for="email">E-mail(*)</label>
                                                    <input type="email" class="form-control" id="email_recovery">
                                                </div>                                                                   
                                                <div class="form-group">
                                                    <label for="obs"><?php echo '(*)'.$CI->T("Obrigatório", array(),$language);?></label>                                                                    
                                                </div>   
                                            </div>                                                            
                                        </div>
                                        <div id="container_recovery_message" class="form-group pd-lr20" style="text-align:justify;visibility:hidden; font-family:sans-serif; font-size:0.9em">
                                        </div>                                                    
                                        <div class="fleft100 pd-lr20" >
                                            <div id = "button_place">
                                            <button type="button" id="do_recovery" class="btn btn-success fleft100"><?php echo $CI->T("RECUPERAR SENHA", array(),$language);?></button>                                                                                                                 
                                            </div>                                                        
                                        </div>                                                    
                                    </form>	
                            </div>
                        </div>                            
                    </div>
                </div>
            <?php        
                }
                else{
            ?>
                <div class="fleft100 pd-tb30 m-top50 text-center">
                    <div class="container">                            
                        <div class="col-md-4 col-sm-4 col-xs-12 col-md-offset-4 col-sm-offset-4">
                            <div class="col-md-8 col-sm-12 col-xs-12 pd-0 fnone i-block">
                                    <form action="" class="fleft100 fmr-cadastro">					
                                        <div class="fleft100 pd-20 bk-fff text-left">                                                            
                                            <div id="datas_form">
                                                <div class="form-group">
                                                    <label for="senha"><?php echo $CI->T("Nova senha", array(),$language);?>(*)</label>
                                                    <input type="password" class="form-control" id="pass1" onkeyup="javascript:this.value=this.value.toLowerCase();" style="text-transform:lowercase;">
                                                </div>
                                                <div class="form-group">
                                                    <label for="senha2l"><?php echo $CI->T("Repetir senha", array(),$language);?>(*)</label>
                                                    <input type="password" class="form-control" id="pass2">
                                                </div>                                                                   
                                                <div class="form-group">
                                                    <label for="obs"><?php echo '(*)'.$CI->T("Obrigatório", array(),$language);?></label>                                                                    
                                                </div>   
                                            </div>                                                            
                                        </div>
                                        <div id="container_recovery_message" class="form-group pd-lr20" style="text-align:justify;visibility:hidden; font-family:sans-serif; font-size:0.9em">
                                        </div>                                                    
                                        <div class="fleft100 pd-lr20" >
                                            <div id = "button_place2">
                                            <button type="button" id="do_over_write_pass" class="btn btn-success fleft100"><?php echo $CI->T("REDEFINIR SENHA", array(),$language);?></button>                                                                                                                 
                                            </div>                                                        
                                        </div>                                                    
                                    </form>	
                            </div>
                        </div>                            
                    </div>
                </div>
            <?php        
                }                
            ?>
            <footer class="fleft100 pd-tb50 bk-fff text-center">
                    <div class="container">
                            <div class="fleft100 m-top40">
                                    <img src="<?php echo base_url().'assets/img/copy.png'?>" alt="">
                                    <span class="fleft100 cp m-top15">DUMBU - 2018 - <?php echo $CI->T("TODOS OS DIREITOS RESERVADOS", array(),$language);?></span>
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
    <script src="<?php echo base_url().'assets/bootstrap/js/bootstrap-datepicker.min.js?'.$SCRIPT_VERSION;?>"></script>
    <script src="<?php echo base_url().'assets/bootstrap/js/bootstrap-datepicker.pt-BR.min.js?'.$SCRIPT_VERSION;?>"></script>
    <!-- FILTRAR -->
    <script src="<?php echo base_url().'assets/js/filtrar.js?'.$SCRIPT_VERSION;?>"></script> 
    <!-- VALIDATE -->
    <script src="<?php echo base_url().'assets/js/validate.js?'.$SCRIPT_VERSION;?>" type="text/javascript"></script>
    <!-- MASCARAS -->
    <script src="<?php echo base_url().'assets/js/maskinput.js'?>" type="text/javascript"></script>
    <!-- Scripts -->
    <script src="<?php echo base_url().'assets/js/script.js'?>" type="text/javascript"></script>

</html>
