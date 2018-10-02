<!DOCTYPE html>
<html lang="pt-BR">
    <head>
            <?php  $CI =& get_instance();?>
            <meta charset="UTF-8">
            <title>Dumbu-Leads</title>
            <meta name="viewport" content="width=device-width">
            <link rel="icon" type="image/png" href="<?php echo base_url().'assets/img/icon.png'?>">

            <script type="text/javascript">var base_url ='<?php echo base_url()?>';</script>
            <script type="text/javascript">var language ='<?php echo $GLOBALS['language']?>';</script>
            
            <!-- Font Awesome -->
            <!--<link rel="stylesheet" href="<?php // echo base_url().'assets/fonts/font-awesome.min.css'?>">-->
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">            
            
            
            <link rel="stylesheet" href="<?php echo base_url().'assets/js/menu_mobile/css/default.css?'.$SCRIPT_VERSION;?>"/>
            <link rel="stylesheet" href="<?php echo base_url().'assets/js/menu_mobile/css/component.css?'.$SCRIPT_VERSION;?>"/>
            
            
            <!-- Bootstrap -->
            <link rel="stylesheet" href="<?php echo base_url().'assets/bootstrap/css/bootstrap.min.css'?>"/>
            <link rel="stylesheet" href="<?php echo base_url().'assets/bootstrap/css/bootstrap-multiselect.css'?>"/>
            <!--<link rel="stylesheet" href="<?php //echo base_url().'assets/bootstrap/css/bootstrap-datepicker.min.css'?>"/>-->
            
            <!-- CSS -->
            <link rel="stylesheet" href="<?php echo base_url().'assets/css/estilo.css?'.$SCRIPT_VERSION;?>"/>
            <link rel="stylesheet" href="<?php echo base_url().'assets/css/style2.css?'.$SCRIPT_VERSION;?>"/>
            <link rel="stylesheet" href="<?php echo base_url().'assets/css/definicoes.css?'.$SCRIPT_VERSION;?>"/>
            <link rel="stylesheet" href="<?php echo base_url().'assets/css/media.css'?>"/>            
            <link rel="stylesheet" href="<?php echo base_url().'assets/css/ladda-themeless.min.css'?>">
                
            <!-- jQuery -->
            <script type="text/javascript" src="<?php echo base_url().'assets/js/jquery.js';?>"></script>        
            <script type="text/javascript" src="<?php echo base_url().'assets/js/front.js?'.$SCRIPT_VERSION;?>"></script>                
            <script type="text/javascript" src="<?php echo base_url().'assets/js/translation.js?'.$SCRIPT_VERSION;?>"></script>                
            
            <script src="<?php echo base_url().'assets/js/spin.min.js'?>"></script>
            <script src="<?php echo base_url().'assets/js/ladda.min.js'?>"></script>
            
            <!-- Global site tag (gtag.js) - Google Ads: 862085589 --> 
            <script async src="https://www.googletagmanager.com/gtag/js?id=AW-862085589">
            </script> 
            <script> 
                window.dataLayer = window.dataLayer || []; 
                function gtag(){dataLayer.push(arguments);} 
                gtag('js', new Date()); gtag('config', 'AW-862085589'); 
            </script>
            <!-- Event snippet for Cadastro Leads conversion page In your html page, add the snippet and call gtag_report_conversion when someone clicks on the chosen link or button. --> 
            <script> 
                function gtag_report_conversion(url) { var callback = function () { if (typeof(url) != 'undefined') { window.location = url; } }; gtag('event', 'conversion', { 'send_to': 'AW-862085589/9HIuCPaghIgBENXDiZsD', 'event_callback': callback }); return false; } 
            </script>
    </head>
    <body>
    <section class="topo-home fleft100 bk-black">
<!--            <header class="fleft100 pd-tb20 m-top20">
                    <div class="container pd-lr60 pd-lr15-xs">
                            <div class="col-md-4 col-sm-6 col-xs-7 pull-right text-right m-top10-xs menu">
                                    <a href="www.dumbu.pro/dumbu/src" class="a-border i-block hidden-xs">Quer seguidores? <br>Clique aqui</a>
                                    <div class="dropdown i-block">
                                      <button id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="b-none bk-none cl-fff ft-size13">
                                          <img src="<?//php echo base_url().'assets/img/user.png'?>" alt=""> ENTRAR
                                      </button>
                                      <form class="dropdown-menu" aria-labelledby="dLabel">
                                        <input type="text" class="form-control fleft100" placeholder="Usuário">
                                        <input type="text" class="form-control fleft100 m-top10" placeholder="Senha">
                                        <button type="button" class="btn btn-success fleft100 m-top10">Entrar</button>
                                      </form>
                                    </div>
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-5 pull-right text-center left-xs logo m-top8 m-none-xs">
                                    <a href=""><img src="<?php //echo base_url().'assets/img/logo.png'?>" alt="Logo-image"/></a>
                            </div>		
                    </div>
            </header>-->
            
            <header class="">
                <div class="container">
                    <div id="dl-menu" class="dl-menuwrapper">
                        <button class="dl-trigger">Open Menu</button>
                            <ul class="dl-menu">                                        
                                <li style="text-align: left;">
                                    <a href="#lnk_how_work">
                                        <?php echo $CI->T("COMO FUNCIONA?", array(),$language);?>
                                    </a>
                                </li>
                                <li style="text-align: left;">
                                    <a href="#lnk_sign_in_now">
                                        <?php echo $CI->T("ASSINAR AGORA", array(),$language);?>
                                    </a>
                                </li>                                                                
                                <li>
                                    <!--<a href="#"><?php // echo $CI->T("ENTRAR", array(),$language);?></a>-->
                                    <?php
                                    if($this->session->userdata('login') && $this->session->userdata('module') == "LEADS"){
                                        if($this->session->userdata('is_admin')==TRUE){
                                    ?>  
                                            <!--<li>-->                                    
                                                <a href="<?php echo base_url().'index.php/welcome/admin'; ?>">
                                                    <i class="fa fa-cog"></i>
                                                    <?php echo mb_strtoupper($CI->T("ADMINISTRAR", array(),$language));?>
                                                </a>
                                            </li>
                                    <?php
                                        }
                                        else{
                                    ?>    
                                            <li>                                   
                                                <a href="<?php echo base_url().'index.php/welcome/client'; ?>">
                                                    <i class="fa fa-binoculars"></i>
                                                    <?php echo mb_strtoupper($CI->T("CAMPANHAS", array(),$language));?>
                                                </a>
                                            </li>
                                    <?php
                                        }
                                    }
                                    else {
                                    ?>
                                    
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">
                                        <img src="<?php echo base_url().'assets/img/user.png'?>"  alt="User">
                                            <?php echo $CI->T("ENTRAR", array(),$language);?>                                        
                                    </a>
                                    <ul class="dl-submenu">
                                        <li>                                            
                                            <div id="login_container1">
                                                <form id="usersLoginForm1" action="#" method="#" class="form" role="form" accept-charset="UTF-8">
                                                    <div class="form-group center" style="font-family:sans-serif; font-size:0.9em">
                                                    <?php echo $CI->T("EXCLUSIVO PARA USUÁRIOS", array(),$language);?>
                                                    </div>                                                                        
                                                    <div class="form-group">
                                                            <input id="userLogin1" type="text" class="form-control" placeholder="<?php echo $CI->T("Usuário ou e-mail", array(),$language);?>" onkeyup="javascript:this.value=this.value.toLowerCase();" style="text-transform:lowercase;" required="">
                                                    </div>
                                                    <div class="form-group">
                                                            <input id="userPassword1" type="password" class="form-control" placeholder="<?php echo $CI->T("senha", array(),$language);?>" required="">
                                                    </div>
                                                    <div class="form-group">
                                                            <input type="submit" name="" value="<?php echo $CI->T("ENTRAR", array(),$language);?>" id="btn_dumbu_login1" class="btn btn-success btn-block ladda-button" type="button" data-style="expand-left" data-spinner-color="#ffffff" />
                                                    </div>
                                                    <div id="container_login_message1" class="form-group" style="text-align:justify;visibility:hidden; font-family:sans-serif; font-size:0.9em">
                                                    </div>
                                                    <div>
                                                        <a style="color:blue" href="<?php echo base_url().'index.php/welcome/password_recovery?language='.$GLOBALS['language']; ?>">
                                                            <?php echo $CI->T("Esqueceu sua senha?", array(),$language);?>
                                                        </a>
                                                    </div>
                                                </form>
                                            </div>
                                        </li>
                                    </ul>
                                    <?php }?> 
                                <li>
                                    <a id="lnk_faq_cell" target="_blank" href=<?php echo base_url().'index.php/welcome/faqget?language='.$language ?> >FAQs</a>
                                </li>
                                <li id="locales_cell">
                                    <a style="color:white" id="lnk_language1_cell" href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        &nbsp;&nbsp;&nbsp;
                                        <?php if ($language === 'EN') { ?>
                                            <img id="img_language1" src="<?php echo base_url().'assets/img/en_flag.png'?>" alt="EN" class="wauto us">
                                            <span id="txt_language1">EN</span>
                                        <?php }
                                        elseif ($language === 'PT') { ?> 
                                            <img id="img_language1" src="<?php echo base_url().'assets/img/pt_flag.png'?>" alt="PT" class="wauto us">
                                            <span id="txt_language1">PT</span>
                                        <?php }
                                        else { ?>
                                            <img id="img_language1" src="<?php echo base_url().'assets/img/es_flag.png'?>" alt="ES" class="wauto us">
                                            <span id="txt_language1">ES</span>
                                        <?php } ?>
                                    </a>
                                    <ul class="dl-submenu dropdown-menu">
                                        <li>

                                        <?php if ($language === 'EN') { ?>
                                            <a id="lnk_language2_cell">
                                            <img id="img_language2" src="<?php echo base_url().'assets/img/pt_flag.png'?>" alt="PT" class="wauto us"/>
                                            <span id="txt_language2">PT</span>
                                            </a>
                                        <?php }
                                        elseif ($language === 'PT') { ?>
                                            <a id="lnk_language2_cell" href="#">
                                            <img id="img_language2" src="<?php echo base_url().'assets/img/es_flag.png'?>" alt="ES" class="wauto us"/>
                                            <span id="txt_language2">ES</span>
                                            </a>
                                        <?php }
                                        else { ?>
                                            <a id="lnk_language2_cell" href="#">
                                            <img id="img_language2" src="<?php echo base_url().'assets/img/en_flag.png'?>" alt="EN" class="wauto us"/>
                                            <span id="txt_language2">EN</span>
                                            </a>
                                        <?php } ?>

                                        </li>
                                        <li>

                                        <?php if ($language === 'EN') { ?>
                                            <a id="lnk_language3_cell" href="#">
                                            <img id="img_language3" src="<?php echo base_url().'assets/img/es_flag.png'?>" alt="ES" class="wauto us"/>
                                            <span id="txt_language3">ES</span>
                                            </a>
                                        <?php }
                                        elseif ($language === 'PT') { ?>
                                            <a id="lnk_language3_cell" href="#">
                                            <img id="img_language3" src="<?php echo base_url().'assets/img/en_flag.png'?>" alt="EN" class="wauto us"/>
                                            <span id="txt_language3">EN</span>
                                            </a>
                                        <?php }
                                        else { ?>
                                            <a id="lnk_language3_cell" href="#">
                                            <img id="img_language3" src="<?php echo base_url().'assets/img/pt_flag.png'?>" alt="PT" class="wauto us"/>
                                            <span id="txt_language3">PT</span>
                                            </a>
                                        <?php } ?>

                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>

                        <nav class="navbar navbar-default navbar-static-top" style="background-color:transparent; margin-top:20px">
                            <div class="logo pabsolute fleft100" style="text-align:center; width:95%;">
                                <a class="navbar-brand i-block" href="#">
                                    <img alt="Brand" src="<?php echo base_url().'assets/img/logo.png'?>">
                                </a>
                            </div>
                            <ul class="nav navbar-nav navbar-right menu-principal">
                                <li >
                                    <a href="https://dumbu.pro/dumbu/src" class="a-border i-block hidden-xs text-right"><?php echo $CI->T("Quer seguidores?", array(),$language);?> <br><?php echo $CI->T("Clique aqui", array(),$language);?></a>
                                </li>
                                <li>
                                    <a href="#lnk_sign_in_now">
                                        <?php echo $CI->T("ASSINAR AGORA", array(),$language);?>
                                    </a>
                                </li>
                                <li>
                                    <a id="lnk_faq" target="_blank" href=<?php echo base_url().'index.php/welcome/faqget?language='.$language ?> >FAQs</a>
                                </li>
                               
                                <?php
                                if($this->session->userdata('login')){
                                    if($this->session->userdata('is_admin')==TRUE){
                                ?>  
                                        <li>                                    
                                            <a href="<?php echo base_url().'index.php/welcome/admin'; ?>">
                                                <i class="fa fa-cog"></i>
                                                <?php echo mb_strtoupper($CI->T("ADMINISTRAR", array(),$language));?>
                                            </a>
                                        </li>
                                <?php
                                    }
                                    else{
                                ?>    
                                        <li>                                    
                                            <a href="<?php echo base_url().'index.php/welcome/client'; ?>">
                                                <i class="fa fa-binoculars"></i>
                                                <?php echo mb_strtoupper($CI->T("CAMPANHAS", array(),$language));?>
                                            </a>
                                        </li>
                                <?php
                                    }
                                }
                                else {
                                ?>
                                <li class = "dropdown_open" class="col-md-12">                                    
                                    <a href="#" class="dropdown-toggle menu_login" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">
                                        <img src="<?php echo base_url().'assets/img/user.png'?>" class="wauto us" alt="User">
                                            <?php echo $CI->T("ENTRAR", array(),$language);?>
                                        <spam class="caret"></spam>
                                    </a>                                    
                                    <ul class="dropdown-menu" id = "main_dropdown">
                                        <li>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div id="login_container2">
                                                        <form id="usersLoginForm2" action="#" method="#" class="form" role="form" accept-charset="UTF-8">
                                                            <div class="form-group center" style="font-family:sans-serif; font-size:0.9em">
                                                                <?php echo $CI->T("EXCLUSIVO PARA USUÁRIOS", array(),$language);?>
                                                            </div>
                                                            <div class="form-group center" style="font-family:sans-serif; font-size:0.7em">
                                                                <?php echo $CI->T("Use login e senha", array(),$language);?>
                                                            </div>
                                                            <div class="form-group">
                                                                <input id="userLogin2" type="text" class="form-control" placeholder="<?php echo $CI->T("Usuário ou e-mail", array(),$language);?>" onkeyup="javascript:this.value=this.value.toLowerCase();" style="text-transform:lowercase;" required="">
                                                            </div>
                                                            <div class="form-group">
                                                                    <input id="userPassword2" type="password" class="form-control" placeholder="<?php echo $CI->T("Senha", array(),$language);?>" required="">
                                                            </div>
                                                            <div class="form-group">
                                                                    <button id="btn_dumbu_login2" class="btn btn-success btn-block ladda-button" type="button" data-style="expand-left" data-spinner-color="#ffffff">
                                                                            <spam class="ladda-label"><?php echo $CI->T("Entrar", array(),$language);?></spam>
                                                                    </button>
                                                            </div>
                                                            <div id="container_login_message2" class="form-group" style="text-align:justify;visibility:hidden; font-family:sans-serif; font-size:0.9em">
                                                            </div>
                                                            <div class="center">
                                                                <a id = "recupery_pass" style="text-decoration:none;" href="<?php echo base_url().'index.php/welcome/password_recovery?language='.$GLOBALS['language']; ?>">
                                                                    <?php echo $CI->T("Esqueceu sua senha?", array(),$language);?>
                                                                </a>
                                                            </div>
<!--                                                            <div id="container_login_force_login2" class="form-group" style="text-align:justify;visibility:hidden; font-family:sans-serif; font-size:0.9em">                                                                                                    
                                                                <div class="col-md-2 col-sm-2 col-xs-12">
                                                                    <input type="checkbox" id="check_force_login2" checked="false" style="margin-top: 0;">                                                                                                        
                                                                </div>
                                                                <div id="message_force_login2" style="with:100%"  class="col-md-10 col-sm-10 col-xs-12 text-left">
                                                                </div>
                                                            </div>-->
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>                                    
                                </li> 
                                <?php
                                }
                                ?>
                                <li id="locales" class="">
                                    <a  id="lnk_language1" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">                                 
                                        <?php if($language==='EN'){?>
                                                <img id="img_language1" src="<?php echo base_url().'assets/img/en_flag.png'?>" class="wauto us" alt="EN">
                                                <span id="txt_language1">EN</span>
                                                <span  class="caret"></span>
                                        <?php }else if($language==='PT'){?>
                                                <img id="img_language1" alt="PT" src="<?php echo base_url().'assets/img/pt_flag.png'?>" class="wauto us"/>
                                                <span id="txt_language1">PT</span>
                                                <span  class="caret"></span>
                                        <?php } else {?>
                                                <img id="img_language1" alt="ES" src="<?php echo base_url().'assets/img/es_flag.png'?>" class="wauto us"/>
                                                <span id="txt_language1">ES</span>
                                                <span  class="caret"></span>
                                        <?php }?>                                            
                                    </a>
                                    <ul class="dropdown-menu" style="min-width: 50px">
                                        <li>
                                            <?php if($language==='EN'){?>
                                                <a id="lnk_language2" href="#">
                                                    <img id="img_language2" alt="PT" src="<?php echo base_url().'assets/img/pt_flag.png'?>" class="wauto us"/>
                                                    <span id="txt_language2">PT</span>
                                                </a>
                                            <?php }else if($language==='PT'){?>
                                                <a id="lnk_language2" href="#">
                                                    <img id="img_language2" alt="ES" src="<?php echo base_url().'assets/img/es_flag.png'?>" class="wauto us"/>
                                                    <span id="txt_language2">ES</span>
                                                </a>
                                            <?php } else {?>
                                                <a id="lnk_language2" href="#">
                                                    <img id="img_language2" alt="EN" src="<?php echo base_url().'assets/img/en_flag.png'?>" class="wauto us"/>
                                                    <span id="txt_language2">EN</span>
                                                </a>
                                            <?php }?>
                                        </li>
                                        <li>
                                            <?php if($language==='EN'){?>
                                                <a id="lnk_language3" href="#">
                                                    <img id="img_language3" alt="ES" src="<?php echo base_url().'assets/img/es_flag.png'?>" class="wauto us"/>
                                                    <span id="txt_language3">ES</span>
                                                </a>
                                            <?php }else if($language==='PT'){?>
                                                <a id="lnk_language3" href="#">
                                                    <img id="img_language3" alt="EN" src="<?php echo base_url().'assets/img/en_flag.png'?>" class="wauto us"/>
                                                    <span id="txt_language3">EN</span>
                                                </a>
                                            <?php } else {?>
                                                <a id="lnk_language3" href="#">
                                                    <img id="img_language3" alt="PT" src="<?php echo base_url().'assets/img/pt_flag.png'?>" class="wauto us"/>
                                                    <span id="txt_language3">PT</span>
                                                </a>
                                            <?php }?>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </nav>
                </div>
            </header>






            <div class="container text-center topo cl-fff">
                    <div class="col-md-10 col-sm-12 col-xs-12 fnone i-block">
                            <!--<div class="fleft100 text-left center-xs"><small><?php // echo $CI->T("NOVIDADE", array(),$language);?></small></div>-->
                            <h1 class="cl-blue fw-900 m-top15 fleft100"><?php echo $CI->T("Extraia dados de futuros clientes usando o Instagram.", array(),$language);?></h1>
                            <span class="fleft100 text-center fw-300 ft-size20"><?php echo $CI->T("Sem limite de extração diária. Mais de 500 milhões de Leads que podem ser seus agora!", array(),$language);?></span>

                            <div class="col-md-5 col-sm-12 col-xs-12 m-top40">
                                    <div class="fleft100 blseta">
                                            <div class="col-md-3 col-sm-3 col-xs-12 pd-0">
                                                    <img src="<?php echo base_url().'assets/img/$.png'?>" alt=""/>
                                            </div>
                                            <div class="col-md- 9 col-sm-9 col-xs-12 text-left center-xs m-top5">
                                                    <h4><?php echo $CI->T("A partir de ", array(),$language).$currency_symbol." ".number_format((float)$price_lead/100, 2, '.', '').$CI->T(" por leads ÚNICOS extraídos", array(),$language);?></h4>
                                            </div>
                                    </div>
                                    <div class="fleft100 blseta m-top40">
                                            <div class="col-md-3 col-sm-3 col-xs-12 pd-0 m-top15">
                                                    <img src="<?php echo base_url().'assets/img/alvo.png'?>" alt="">
                                            </div>
                                            <div class="col-md- 9 col-sm-9 col-xs-12 text-left center-xs">
                                                    <h4><?php echo $CI->T("Crie campanhas personalizadas, com filtros completos, como: sexo, localidade e tipo de perfil", array(),$language); ?></h4>
                                            </div>
                                    </div>
                            </div>
                            <div class="col-md-7 col-sm-12 col-xs-12 m-top40">
                                    <div class="blblue fleft100">
                                            <h2 class="fw-600 text-left m-b30 pd-lr15"><?php echo $CI->T("Quais dados consigo exportar?", array(),$language);?></h2>
                                            <div class="fleft100">
                                                    <div class="col-md-4 col-sm-4 col-xs-12  m-top20">
                                                            <div class="col-md-3 col-sm-3 col-xs-12 pd-0">
                                                                    <img src="<?php echo base_url().'assets/img/tel.png'?>" alt="">
                                                            </div>
                                                            <div class="col-md-9 col-sm-9 col-xs-12 text-left pd-lr0-xs center-xs fw-600 ft-size13">
                                                                    <?php echo $CI->T("Número telefônico", array(),$language);?>
                                                            </div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-12  m-top20">
                                                            <div class="col-md-3 col-sm-3 col-xs-12 pd-0">
                                                                    <img src="<?php echo base_url().'assets/img/mail.png'?>" alt="">
                                                            </div>
                                                            <div class="col-md-9 col-sm-9 col-xs-12 text-left pd-lr0-xs center-xs fw-600 ft-size13 m-top5">
                                                                    <?php echo $CI->T("E-mail", array(),$language);?>
                                                            </div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-12  m-top20">
                                                            <div class="col-md-3 col-sm-3 col-xs-12 pd-0">
                                                                    <img src="<?php echo base_url().'assets/img/insta.png'?>" alt="">
                                                            </div>
                                                            <div class="col-md-9 col-sm-9 col-xs-12 text-left pd-lr0-xs center-xs fw-600 ft-size13 m-top5">
                                                                    @<?php echo $CI->T("perfil", array(),$language);?>
                                                            </div>
                                                    </div>
                                            </div>
                                            <div class="fleft100 m-top20">
                                                    <div class="col-md-4 col-sm-4 col-xs-12 m-top20">
                                                            <div class="col-md-3 col-sm-3 col-xs-12 pd-0">
                                                                    <img src="<?php echo base_url().'assets/img/local.png'?>" alt="">
                                                            </div>
                                                            <div class="col-md-9 col-sm-9 col-xs-12 text-left pd-lr0-xs center-xs fw-600 ft-size13 m-top8">
                                                                    <?php echo $CI->T("Local", array(),$language);?>
                                                            </div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-12 m-top20">
                                                            <div class="col-md-4 col-sm-4 col-xs-12 pd-0">
                                                                    <img src="<?php echo base_url().'assets/img/sexo.png'?>" alt="">
                                                            </div>
                                                            <div class="col-md-8 col-sm-8 col-xs-12 text-left pd-lr0-xs center-xs fw-600 ft-size13 m-top5">
                                                                    <?php echo $CI->T("Gênero", array(),$language);?>
                                                            </div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-12 m-top20">
                                                            <div class="col-md-2 col-sm-2 col-xs-12 pd-0">
                                                                    <img src="<?php echo base_url().'assets/img/user.png'?>" alt="">
                                                            </div>
                                                            <div class="col-md-10 col-sm-10 col-xs-12 text-left pd-lr0-xs center-xs fw-600 pd-lr5 ft-size13">
                                                                <div class="col-12">
                                                                     <?php echo $CI->T("Tipo de perfil", array(),$language);?>
                                                                </div>
                                                                <div class="col-12">                                                                    
                                                                    <span class="ft-size8">(<?php echo $CI->T("Pessoal ou Empresa", array(),$language);?>)</span>
                                                                </div>
                                                            </div>
                                                    </div>
                                            </div>
                                    </div>
                            </div>

                            <div class="fleft100 m-top5">
                                    <div class="col-md-6 col-sm-12 col-xs-12 pf pull-right">
                                            <span class="fleft100 m-b10 hidden-xs"><img src="<?php echo base_url().'assets/img/s-up.png'?>" alt=""></span>
                                            <img src="<?php echo base_url().'assets/img/pf.png'?>" alt="">
                                    </div>
                                    <div class="col-md-6 col-sm-12 col-xs-12 m-top80 m-top20-xs m-b20">
                                            <a href="#lnk_sign_in_now" class="bt-trans-green">
                                                <span class="fleft100"><?php echo $CI->T("Cadastre-se GRÁTIS agora mesmo!", array(),$language);?></span> 
                                                <i class="fa fa-chevron-down"></i></a>
                                    </div>
                            </div>
                    </div>
            </div>

            <div class="fleft100 gb cl-fff text-center pd-tb40">
                    <div class="container">
                            <img src="<?php echo base_url().'assets/img/gbl.png'?>" alt="">
                            <h3 class="m-top15"><?php echo $CI->T("Dumbu é global!", array(),$language);?></h3>
                            <span class="ft-size17"><?php echo $CI->T("Temos clientes em mais de 200 países.  Faça parte de uma das Startups que mais cresce nos últimos tempos!", array(),$language);?></span>
                    </div>
            </div>
    </section>
        
    <section class="fleft100">
            <div class="container center-xs">
                    <A name="lnk_how_work"></A>
                    <h1 class="fw-800 fleft100 m-b30 m-top50"><?php echo $CI->T("COMO FUNCIONA?", array(),$language);?></h1>
                    <div class="col-md-4 col-sm-4 col-xs-12 pd-lr5">
                            <div class="fleft100 pass">
                                    <div class="col-md-1 col-sm-2 col-xs-12 pd-0 text-center ft-size24 fw-600">1</div>
                                    <div class="col-md-11 col-sm-10 col-xs-12 pd-lr5 fw-600">
                                            <?php echo $CI->T("Escolha os perfis, hashtags ou locais do Instagram  que deseja captar seus leads", array(),$language);?>
                                    </div>
                            </div>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-12 pd-lr5">
                            <div class="fleft100 pass">
                                    <div class="col-md-1 col-sm-2 col-xs-12 pd-0 text-center ft-size24 fw-600">2</div>
                                    <div class="col-md-11 col-sm-10 col-xs-12 pd-lr5 fw-600 m-top8 m-none-xs">
                                        <?php echo $CI->T("Escolha quanto deseja investir por dia", array(),$language);?>    
                                        
                                    </div>
                            </div>
                    </div>
                    <div class="col-md-5 col-sm-5 col-xs-12 pd-lr5">
                            <div class="fleft100 pass passblue">
                                    <div class="col-md-1 col-sm-2 col-xs-12 pd-0 text-center ft-size24 fw-600">3</div>
                                    <div class="col-md-11 col-sm-10 col-xs-12 pd-lr5 fw-600">
                                        <?php echo $CI->T("A Dumbu fará uma lista  exportável com informações de contas baseada nos perfis, hashtags ou locais que você deseja", array(),$language);?>                                            
                                    </div>
                            </div>
                    </div>
                    <div class="fleft100 m-top50">
                            <div class="col-md-6 col-sm-6 col-xs-12 pd-0">
                                    <div class="fleft100 pd-40 border-blue">
                                            <h1 class="ft-size55 fw-800 col-md-8 col-sm-8 col-xs-12">
                                                <?php echo $CI->T("O que são Leads?", array(),$language);?>                                            
                                                    
                                            </h1>
                                            <div class="col-md-4 col-sm-4 col-xs-12">
                                                    <img src="<?php echo base_url().'assets/img/cf.png'?>" class="mxw-180">
                                            </div>
                                            <p class="fleft100 pd-lr15 fw-400 m-top20">
                                                    <?php echo $CI->T("Geração de leads significa oportunidades de negócio. Lead, em Marketing Digital, é um potencial consumidor de uma marca que demonstrou interesse em consumir o seu produto ou serviço.", array(),$language);?>                                                                                                
                                                    <br><br>
                                                    <?php echo $CI->T("Se baseando em seu público alvo, você só precisa selecionar Perfis do Instagram, Locais e Hashtags que tenham a ver com seu negócio. Nossa ferramenta irá entregar os dados dos usuários que potencialmente podem se tornar consumidores do seu produto ou serviço.", array(),$language);?>                                                                                                
                                                    
                                            </p>
                                    </div>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12 pd-50 m-top30">
                                    <h3 class="fw-600"><?php echo $CI->T("Como usar os dados exportados?", array(),$language);?> </h3>
                                    <p class="fleft100 fw-400 m-top20">
                                            <?php echo $CI->T("Você pode usar os Leads extraídos de várias maneiras. ", array(),$language);?>
                                            
                                            <br><br>
                                            <?php echo $CI->T("Usando o e-mail, além do mais tradicional e-mail marketing, você também pode criar públicos para campanhas de Custom Audience no Facebook e Instagram. ", array(),$language);?>
                                            
                                            <br><br>
                                            <?php echo $CI->T("Já com o número de celular, você pode criar campanhas de SMS ou Whats App Marketing ou fazer ligações através do seu time comercial, por exemplo.", array(),$language);?>
                                             
                                            <br><br>
                                            <?php echo $CI->T("Os demais dados, como: sexo, localidade e tipo de perfil, são excelentes filtros para começar a categorizar e criar campanhas personalizadas, baseadas no público atingido.", array(),$language);?>
                                            
                                    </p>
                            </div>
                    </div>
                </div>
        </section>
        <!--<section class="fleft100">
            ainda não esta pronto o video pra home, mas ele vai aqui, é so botar o link dele que esta no youtube
            <div class="container center-xs m-top50">
                <div class="col-sm-2"></div>
                <div class="col-sm-8">
                    <div class="embed-responsive embed-responsive-16by9">
                    <iframe  class="embed-responsive-item" src="https://www.youtube.com/embed/Eo2Lr1trSKs" allowfullscreen></iframe>                                    </div>
                </div>
                <div class="col-sm-2"></div>
            </div>
        </section>-->
        <section class="fleft100">
            <div class="fleft100 bk-silver pd-tb30 m-top50 text-center">
                    <div class="container">
                            <A name="lnk_sign_in_now"></A>
                            <h3><?php echo $CI->T("Agora que você já sabe como funciona", array(),$language);?>, <b><?php echo $CI->T("crie sua conta grátis!", array(),$language);?></b></h3>
                            <div class="col-md-4 col-sm-4 col-xs-12 col-md-offset-4 col-sm-offset-4 m-top30">
                                    <div class="col-md-8 col-sm-12 col-xs-12 pd-0 fnone i-block">
                                            <form action="" class="fleft100 fmr-cadastro">					
                                                    <div class="fleft100 pd-20 bk-fff text-left" style="<?php if($GLOBALS['id_number'] > 0){ echo "visibility:hidden;";} ?>">
                                                            <div class="fleft100 pd-lr15">
                                                                    <h4 class="fw-600"><img src="<?php echo base_url().'assets/img/profile.png'?>" class="m-r8"> <span><?php echo $CI->T("Crie sua conta", array(),$language);?></span></h4>
                                                                    <span class="ft-size13 m-top15 fleft100 m-b20">
                                                                            <?php echo $CI->T("Você só será cobrado após ativar e rodar sua primeira campanha.", array(),$language);?>                                                                            
                                                                    </span>						
                                                            </div>       
                                                            <div id="datas_form">
                                                                <div class="form-group">
                                                                    <label for="nome"><?php echo $CI->T("Nome de usuário", array(),$language).'(*)';?></label>
                                                                    <input class="form-control" id="user_registration" onkeyup="javascript:this.value=this.value.toLowerCase();" style="text-transform:lowercase;">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="email">E-mail(*)</label>
                                                                    <input type="email" class="form-control" id="email_registration">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="telf"><?php echo $CI->T("Telefone", array(),$language).'(*)';?></label>
                                                                    <input class="form-control" id="telf_registration" maxlength="15">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="pass"><?php echo $CI->T("Senha", array(),$language).'(*)';?></label>
                                                                    <input type="password" class="form-control" id="pass_registration">
                                                                </div>   
                                                                <div class="form-group">
                                                                    <label for="code"><?php echo $CI->T("Código promocional", array(),$language);?></label>
                                                                    <input type="text" class="form-control" id="promotional_code">
                                                                </div>   
                                                                <div class="form-group">
                                                                    <label for="obs"><?php echo '(*)'.$CI->T("Obrigatório", array(),$language);?></label>                                                                    
                                                                </div>   
                                                            </div>
                                                            <div id ="show_number" class="form-group" style="display:none;">
                                                                <label for="num"><?php echo $CI->T("NÚMERO ENVIADO AO E-MAIL: ", array(),$language);?><div id = "email_place"></div></label>
                                                                <input style="text-align:center;" class="form-control" placeholder="_ _ _ _" id="number_confirmation" maxlength="4">
                                                                <button type="button" id="do_signin_number" class="btn btn-success fleft100 m-top30"><?php echo $CI->T("CONFIRMAR CONTA", array(),$language);?></button>
                                                            </div>
                                                    </div>
                                                    <div id="container_sigin_message" class="form-group" style="text-align:justify;visibility:hidden; font-family:sans-serif; font-size:0.9em">
                                                    </div>                                                    
                                                    <div class="fleft100 pd-lr20" >
                                                        <div id = "button_place">
                                                        <button type="button" id="do_signin" class="btn btn-success fleft100 m-top30"><?php echo $CI->T("CRIAR CONTA", array(),$language);?></button>                                                                                                                 
                                                        </div>
                                                        <div class="checkbox m-top10 fleft100">
                                                          <label style="font-size: 11px;">
                                                              <input id = "terms_checkbox" type="checkbox" checked="true" style="position: relative;top:2px;">&nbsp; <?php echo $CI->T("Declaro que li e aceito os ", array(),$language);?>
                                                              <a href="<?php echo base_url()."assets/others/".$GLOBALS['language']."/TERMOS DE USO DUMBU.pdf"; ?>"> <?php echo $CI->T("termos de uso", array(),$language);?> </a>                     
                                                          </label>
                                                        </div>
                                                    </div>                                                    
                                            </form>	
                                    </div>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-12 m-top30 text-left">
                                    <div class="seguro text-center">
                                            <div class="col-md-2 col-sm-2 col-xs-12 pd-0">
                                                    <img src="<?php echo base_url().'assets/img/seg.png'?>" alt="">
                                            </div>
                                            <div class="col-md-10 col-sm-10 col-xs-12 pd-lr5">
                                                    <span class="ft-size11 fleft100 fw-600"><?php echo $CI->T("AMBIENTE 100% SEGURO", array(),$language);?></span>
                                                    <span class="ft-size8 fleft100"><?php echo $CI->T("DADOS CRIPTOGRAFADOS", array(),$language);?></span>
                                            </div>
                                    </div>
                            </div>
                    </div>
            </div>	
    </section>        
    <footer class="fleft100 pd-tb50 bk-fff text-center">
            <div class="container">
                    <div class="col-md-5 col-sm-7 col-xs-12 fnone i-block">
                            <h1 class="fw-800 fleft100 m-b10"><?php echo $CI->T("FALE CONOSCO", array(),$language);?></h1>
                            <div class="col-md-7 col-sm-7 col-xs-12 text-right center-xs t-up fw-800">
                                    <h6><?php echo $CI->T("Write to us! Our service is supported", array(),$language);?> <br><?php echo $CI->T("in more than one language:", array(),$language);?></h6>
                            </div>
                            <div class="col-md-5 col-sm-5 col-xs-12 text-left center-xs">
                                    <img src="<?php echo base_url().'assets/img/linguas.png'?>" alt="">
                            </div>
                            <form action="" class="fmr-contato fleft100 m-top15">
                                <div class="col-md-6 col-sm-6 col-xs-12 pd-lr5 form-group">
                                    <input id="visitor_name" type="text" class="form-control" placeholder="<?php echo $CI->T("Nome", array(),$language);?> *">
                                </div>
                                <div id="" class="col-md-6 col-sm-6 col-xs-12 pd-lr5 form-group">
                                    <input id="visitor_company" type="text" class="form-control" placeholder="<?php echo $CI->T("Empresa", array(),$language);?>">
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12 pd-lr5 form-group">
                                    <input id="visitor_email" type="text" class="form-control" placeholder="<?php echo $CI->T("E-mail", array(),$language);?> *">
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12 pd-lr5 form-group">
                                    <input id="visitor_phone" type="text" class="form-control" placeholder="<?php echo $CI->T("Telefone", array(),$language);?>">
                                </div>
                                <div class="fleft100 pd-lr5 form-group">
                                    <textarea id="visitor_message" type="text" class="form-control fleft100" rows="4" placeholder="<?php echo $CI->T("Mensagem", array(),$language);?> *"></textarea>
                                </div>
                                <div class="fleft100 pd-lr5">
                                    <span>(*) <?php echo $CI->T("Obrigatório", array(),$language);?></span>
                                </div>
                                <div class="fleft100 pd-lr5">
                                    <button id="btn_send_message" type="button" class="btn m-top10"><?php echo $CI->T("ENVIAR MENSAGEM", array(),$language);?></button>
                                </div>
                            </form>

                            <div class="fleft100 m-top40">
                                    <img src="<?php echo base_url().'assets/img/copy.png'?>" alt="">
                                    <span class="fleft100 cp m-top15">DUMBU - 2018 - <?php echo $CI->T("TODOS OS DIREITOS RESERVADOS", array(),$language);?></span>
                            </div>
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
                    <button id="accept_modal_alert_message" type="button" class="btn btngreen active text-center ladda-button" data-style="expand-left" data-spinner-color="#ffffff">
                        <spam class="ladda-label"><div style="color:white; font-weight:bold">OK</div></spam>
                    </button>
                </div>
            </div>
        </div>                                                        
    </div>
    <!-- BEGIN JIVOSITE CODE -->
<script type='text/javascript'>
(function(){ var widget_id = 'Af9Dp13Ek6';var d=document;var w=window;function l(){var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true;s.src = '//code.jivosite.com/script/widget/'+widget_id; var ss = document.getElementsByTagName('script')[0]; ss.parentNode.insertBefore(s, ss);}if(d.readyState=='complete'){l();}else{if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})();
</script>
<!-- END JIVOSITE CODE -->
    </body>
    <!--[if lt IE 9]>
    <script src="js/jquery-1.9.1.js"></script>
    <![endif]-->
    <!--[if gte IE 9]><!-->
    <script src="<?php echo base_url().'assets/js/jquery-3.1.1.min.js'?>"></script>
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
    <script src="<?php echo base_url().'assets/js/talkme_painel.js?'.$SCRIPT_VERSION;?>" type="text/javascript"></script>
        
    <script src="<?php echo base_url().'assets/js/menu_mobile/js/modernizr.custom.js'?>"></script>
    <script src="<?php echo base_url().'assets/js/menu_mobile/js/jquery.dlmenu.js'?>"></script>
    
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-87696730-1"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'UA-87696730-1');
    </script>
</html>
