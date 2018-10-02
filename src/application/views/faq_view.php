 <!DOCTYPE html>
<html lang="en">
    <head>
          <?php  $CI =& get_instance();?>
          <?php $datas= $this->input->post();
           if(count($datas)<1)
           {
            $datas= $this->input->get();   
           }
          ?>        
        <script type="text/javascript">var base_url ='<?php echo base_url()?>';</script>
        <script type="text/javascript">var language ='<?php echo $datas['language'];?>';</script>
        <meta charset="utf-8">
        <title>FAQ</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="title" content="Extraia dados de futuros clientes usando o Instagram. Sem limite de extração diária. | Mais de 500 milhões de Leads que podem ser seus agora! ">
        <meta name="description" content="Extraia dados de futuros clientes usando o Instagram. www.dumbu.pro te permite extrair leads no Instagram 100% reais e qualificados. Extraia dados de futuros clientes usando o Instagram.">
        <meta name="keywords" content="ganhar, leads, Instagram, clientes segmentados, hashtags, followers, geolocalizção, perfiles, vendas">
        <meta name="revisit-after" content="7 days">
        <meta name="robots" content="index, leads">
        <meta name="distribution" content="global"> 
        
        <link rel="shortcut icon" href="https://dumbu.pro/leads/src/assets/img/logo.png"> 
        <!-- jQuery -->
        <script type="text/javascript" src="https://dumbu.pro/leads/src/assets/js/jquery.js"></script>
        <!-- Bootstrap -->
        <link href="https://dumbu.pro/leads/src/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet"  />
        <!--<link href="https://dumbu.pro/leads/src/assets/bootstrap/css/loading.css" rel="stylesheet" />-->
        <!--<link href="https://dumbu.pro/follows/src/assets/bootstrap/css/style.css" rel="stylesheet"/>-->
<!--        <link rel="stylesheet" type="text/css" href="https://dumbu.pro/leads/src/assets/css/style2.css" />
        <link rel="stylesheet" type="text/css" href="https://dumbu.pro/follows/src/assets/css/stylenew.css" />
        <link rel="stylesheet" type="text/css" href="https://dumbu.pro/leads/src/assets/css/estilo.css" />
        <link rel="stylesheet" type="text/css" href="https://dumbu.pro/leads/src/assets/css/media.css" />
        <link rel="stylesheet" type="text/css" href="https://dumbu.pro/leads/src/assets/font-awesome/font-awesome.min.css" />
        <link rel="stylesheet" type="text/css" href="https://dumbu.pro/leads/src/assets/css/definicoes.css" />
        <link rel="stylesheet" href="https://dumbu.pro/leads/src/assets/css/ladda-themeless.min.css">
        
        <script type="text/javascript" src="https://dumbu.pro/leads/src/assets/js/modernizr.custom.js"></script>                
        <script src="https://dumbu.pro/leads/src/assets/js/spin.min.js"></script>
        <script src="https://dumbu.pro/leads/src/assets/js/ladda.min.js"></script>                
        <script type="text/javascript">var base_url = 'https://dumbu.pro/leads/src/';</script>
        <script type="text/javascript">var language = 'PT';</script>
        <script type="text/javascript">var SERVER_NAME = 'PRO';</script>
        <script type="text/javascript" src="https://dumbu.pro/leads/src/assets/js/translation.js?"></script>
        <script type="text/javascript" src="https://dumbu.pro/leads/src/assets/js/talkme_painel.js"></script>-->                

        <!--<script type="text/javascript" src="https://dumbu.pro/leads/src/assets/js/user.js?"></script>
        <script type="text/javascript" src="https://dumbu.pro/leads/src/assets/js/sign_painel.js"></script>
        <script type="text/javascript" src="https://dumbu.pro/follows/src/assets/js/controllers.js"></script>--> 
        <script type="text/javascript" src="https://dumbu.pro/follows/src/assets/bootstrap/js/bootstrapold.min.js"></script>

        <!--<link href="https://dumbu.pro/follows/src/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet"  />-->
        <link href="https://dumbu.pro/follows/src/assets/bootstrap/css/loading.css" rel="stylesheet" />
        <link href="https://dumbu.pro/follows/src/assets/bootstrap/css/style.css" rel="stylesheet"/>
        <link rel="stylesheet" type="text/css" href="https://dumbu.pro/follows/src/assets/css/style.css" />
        <link rel="stylesheet" type="text/css" href="https://dumbu.pro/follows/src/assets/css/stylenew.css" />
        <link rel="stylesheet" type="text/css" href="https://dumbu.pro/follows/src/assets/css/defaultnew.css" />
        <link rel="stylesheet" type="text/css" href="https://dumbu.pro/follows/src/assets/fonts/font-awesome.min.css" />
        <link rel="stylesheet" type="text/css" href="https://dumbu.pro/follows/src/assets/css/component.css" />
        <link rel="stylesheet" href="https://dumbu.pro/follows/src/assets/css/ladda-themeless.min.css">
        
        <script type="text/javascript" src="https://dumbu.pro/follows/src/assets/js/modernizr.custom.js"></script>                
        <script src="https://dumbu.pro/follows/src/assets/js/spin.min.js"></script>
        <script src="https://dumbu.pro/follows/src/assets/js/ladda.min.js"></script>                
        <!--<script type="text/javascript">var base_url = 'https://dumbu.pro/follows/src/';</script>-->
        <!--<script type="text/javascript">var language = 'PT';</script>-->
        <script type="text/javascript">var SERVER_NAME = 'PRO';</script>
        <script type="text/javascript" src="https://dumbu.pro/follows/src/assets/js/PT/internalization.js?"></script>
        <!--<script type="text/javascript" src="https://dumbu.pro/follows/src/assets/js/user.js?"></script>-->
        <script type="text/javascript" src="https://dumbu.pro/follows/src/assets/js/sign_painel.js"></script>
        <script type="text/javascript" src="https://dumbu.pro/follows/src/assets/js/talkme_painel.js"></script>                
        <script type="text/javascript" src="https://dumbu.pro/follows/src/assets/js/controllers.js"></script> 
<!--        <script type="text/javascript" src="https://dumbu.pro/follows/src/assets/bootstrap/js/bootstrapold.min.js"></script>
-->
            <script type="text/javascript" src="<?php echo base_url().'assets/js/jquery.js';?>"></script>        
            <script type="text/javascript" src="<?php echo base_url().'assets/js/faq_page.js?'.$SCRIPT_VERSION;?>"></script>                
            <script type="text/javascript" src="<?php echo base_url().'assets/js/translation.js?'.$SCRIPT_VERSION;?>"></script>                
            
            <!--<script type="text/javascript" src="<?php echo base_url().'assets/js/front.js'?>"></script>                
            <script type="text/javascript" src="<?php echo base_url().'assets/js/faq_page.js'?>"></script>
            <script type="text/javascript" src="<?php echo base_url().'assets/js/translation.js'?>"></script> -->
    
    </head>
<body id="my_body">
                
                
		<div class="windows8">
                    <div class="wBall" id="wBall_1">
                     <div class="wInnerBall"></div>
                    </div>
                    <div class="wBall" id="wBall_2">
                     <div class="wInnerBall"></div>
                    </div>
                    <div class="wBall" id="wBall_3">
                     <div class="wInnerBall"></div>
                    </div>
                    <div class="wBall" id="wBall_4">
                     <div class="wInnerBall"></div>
                    </div>
                    <div class="wBall" id="wBall_5">
                     <div class="wInnerBall"></div>
                    </div>
		</div>
    
       
    <header class=""><!--bk-black-->
			<div class="container">
				<div id="dl-menu" class="dl-menuwrapper">
					<button class="dl-trigger">Open Menu</button>
                                        <ul class="dl-menu">
                                            <li>
                                                <a id="fechar_faq_cell" href="#"><?php echo $CI->T("SAIR", array(), $language); ?></a>
                                            </li>
                                        </ul>
					<!--<ul class="dl-menu">
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
                                                <li>
                                                    <a id="fechar_faq" href="#">FECHAR</a>
                                                </li>
					</ul>-->
				</div><!-- /dl-menuwrapper -->
                                
				<nav class="navbar navbar-default navbar-static-top">
					<!-- Collect the nav links, forms, and other content for toggling -->
					<div class="logo pabsolute fleft100 text-center">
						<a class="navbar-brand i-block" href="https://dumbu.pro/follows/src/index.php">
                                                    <img alt="Brand" src="https://dumbu.pro/follows/src/assets/images/logo.png">
						</a>
					</div>
					<ul class="nav navbar-nav navbar-right menu-principal">
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
                                                <li>
                                                    <a id="fechar_faq" href="#"><?php echo $CI->T("SAIR",array(), $language); ?></a>
                                                </li>
					</ul>
				</nav>
			</div>
		</header>
        
    <div class="container">
        <h2 id= "cabeçalho" class="text-center"><?php echo $CI->T("Confira abaixo as perguntas mais frequentes.", array(), $language)?></h2>
                
        
            <div class="accordion-container">
                <a href="#" class="accordion-titulo">
                <?php echo $CI->T("Como escolho o meu público alvo?",array(), $language); ?>
                    <span class="toggle-icon"></span></a><div class="accordion-content">
                        <p><p ALIGN="justify">
                  <?php 
                  echo $CI->T(
                          "Nossa ferramenta de leads é 100% segmentada. Você pode escolher captar Leads através de perfis, locais ou hashtags. Ou seja, você irá escolher perfis, locais e hashtags do Instagram que possivelmente tenham seguidores que irão adquirir seu serviço, produto ou conteúdo.",array(), $language); ?>
                          <p><p ALIGN="justify">
                  <?php 
                  echo $CI->T(
                          "Foque em escolher uma estratégia onde os usuários ligados a sua campanha tenha algo a ver com o serviço que você oferece. Por exemplo: Se você trabalha com moda, utilize hashtags ligadas a moda e beleza. Uma dica boa é: Escolha perfis, locais e hashtags que possuem muitos seguidores e interação."
                           ,array(), $language); ?>
                            
                        </p>
 </p></div><a href="#" class="accordion-titulo">
<?php echo $CI->T("Como posso utilizar meus Leads?",array(), $language); ?>     
     <span class="toggle-icon"></span></a><div class="accordion-content">
                          <p><p ALIGN="justify">
                 <?php 
                  echo $CI->T(
          "Com os Leads exportados, você poderá criar campanhas direcionadas através de ferramentas de marketing como Google, Facebook e Instagram, para os usuários que possivelmente se interessam pelo seu conteúdo.",
                          array(), $language); ?>
                              </p>
</p></div><a href="#" class="accordion-titulo">
    <?php echo $CI->T("Quantos Leads posso captar por dia?", array(), $language); ?>                          
    <span class="toggle-icon"></span></a><div class="accordion-content">
                          <p><p ALIGN="justify">???</p>
</p></div><a href="#" class="accordion-titulo">
                 <?php echo $CI->T("Quanto custa cada lead?", array(), $language); ?>
<span class="toggle-icon"></span></a><div class="accordion-content">
                          <p><p ALIGN="justify">
                <?php echo $CI->T("O valor por Lead é de R$0,25 (Vinte e cinco centavos por Lead)", array(), $language); ?>                          
</p></p></div><a href="#" class="accordion-titulo">
<?php echo $CI->T("Como o serviço é cobrado?", array(), $language); ?>
    <span class="toggle-icon"></span></a><div class="accordion-content">
                          <p><p ALIGN="justify">
                  <?php 
                  echo $CI->T(
                        "A cobrança é feita diariamente de acordo com a captação feita durante o dia. Lembre-se que você pode controlar o orçamento diário de cada campanha, tendo assim, o controle de gasto diariamente.",
array(), $language); ?>
</p> 
</p></div><a href="#" class="accordion-titulo">
                <?php echo $CI->T("Qual o método de pagamento?", array(), $language); ?>
    <span class="toggle-icon"></span></a><div class="accordion-content">
                          <p><p ALIGN="justify">
                  <?php 
                  echo $CI->T(
                          "Cartão de crédito - Você pode cadastrar seu cartão de crédito, a cobrança é feita automaticamente durante os dias.",array(), $language); ?>
                          <p><p ALIGN="justify">
                  <?php 
                  echo $CI->T(
                          "Boleto bancário - Você irá escolher o valor que deseja investir e esse valor entra como crédito em sua conta após o boleto ser compensado.",
array(), $language); ?>
                              
                          </p>
</p></div><a href="#" class="accordion-titulo">
                <?php echo $CI->T( "Como criar uma campanha?",array(), $language); ?>
<span class="toggle-icon"></span></a><div class="accordion-content">
                          <p><p ALIGN="justify">
                <?php 
                  echo $CI->T(
                          "Para criar uma campanha, primeiro você definirá o orçamento diário da campanha, depois você irá selecionar se deseja captar os leads utilizando um perfis (Ex.: @neymar), uma hashtags (Ex. #moda) ou localizações.", 
                         array(), $language); ?>
</p></p></div><a href="#" class="accordion-titulo">
                    <?php echo $CI->T( "Como vou exportar meus Leads?",array(), $language); ?>
<span class="toggle-icon"></span></a><div class="accordion-content">
    <p><p ALIGN="justify">
                <?php 
                  echo $CI->T(
                          "Para exportar seus Leads você só precisa escolher a campanha que deseja obter os leads captados e clicar em ‘Extrair leads’. Você poderá exportar apenas um período específico ou todo o período da campanha. Você também pode exportar informações específicas, como apenas os nomes de usuário, ou apenas o e-mails dos usuários.",
array(), $language); ?>
</p>
</p></div>            </div>
    </div>
    
        <section id="contato" class="fleft100 input-form">
			<div class="container">
				<spam style="color:black; font-size:1.6em" class="fleft100 text-center m-t10"><?php echo $CI->T("SE FICOU COM ALGUMA DUVIDA FALE CONOSCO",array(),$language); ?></spam>
                                <div class="col-md-3 col-sm-3 col-xs-12"><br></div>
                                <div id="talkme_frm" class="col-md-6 col-sm-6 col-xs-12 no-pd">
                                                                        
					<div class="col-md-6 col-sm-6 col-xs-12 pd-r15">
						<fieldset>
							<input id="visitor_name" type="text" placeholder="<?php echo $CI->T("Nome",array(),$language); ?>">
						</fieldset>
						<fieldset>
							<input id="visitor_email" type="text" placeholder="<?php echo $CI->T("E-mail",array(),$language); ?>">
						</fieldset>
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12 pd-l15">
						<fieldset>
							<input id="visitor_company"( type="text" placeholder="<?php echo $CI->T("Empresa",array(),$language); ?>">
						</fieldset>
						<fieldset>
							<input id="visitor_phone" type="text" placeholder="<?php echo $CI->T("Telefone",array(),$language); ?>">
						</fieldset>
					</div>
					<div class="col-md-12 col-sm-12 col-xs-12 no-pd">
						<textarea id="visitor_message" name="" placeholder="<?php echo $CI->T("Mensagem",array(),$language); ?>" id=""  rows="8"></textarea>
                                                
                                                <p class="text-center"><?php echo $CI->T( "Enviando para: atendimento@dumbu.pro",array(), $language); ?>                                                </p>
                                                
						<div class="text-center">
                                                    <button id="btn_send_message" class="btn-primary btn-475f66 m-t20 ladda-button"  data-style="expand-left" data-spinner-color="#ffffff">
                                                        <?php echo $CI->T( "ENVIAR MENSAGEM",array(), $language); ?>                                                   </button>
                                                </div>
					</div>
				</div>
				<div class="col-md-3 col-sm-3 col-xs-12"><br></div>
                        </div>
        </section>
            
           
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
		<script src="https://dumbu.pro/follows/src/assets/js/jquery.dlmenu.js"></script>
		<script>
			$(function() {
				$( '#dl-menu' ).dlmenu();
			});
		</script>
                
                <!--modal_container_alert_message-->
                <div class="modal fade" style="top:30%" id="modal_alert_message" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div id="modal_container_alert_message" class="modal-dialog modal-sm" role="document">                                                          
                        <div class="modal-content">
                            <div class="modal-header">
                                <!--<button id="btn_modal_close" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                              <img src="https://dumbu.pro/follows/src/assets/images/FECHAR.png" alt="cancel"> <!--<spam aria-hidden="true">&times;</spam>
                                </button>-->
                                <h5 class="modal-title" id="myModalLabel"><b>Mensagem</b></h5>                        
                            </div>
                            <div class="modal-body">                                            
                                <p id="message_text"></p>                        
                            </div>
                            <div class="modal-footer text-center">
                                <button id="accept_modal_alert_message" type="button" class="btn btngreen active text-center ladda-button" data-style="expand-left" data-spinner-color="#ffffff">
                                    <spam class="ladda-label"><div style="color:white; font-weight:bold">ACEITAR</div></spam>
                                </button>
                            </div>
                        </div>
                    </div>                                                        
                </div> 
                
        <!-- Afilio Master Tag Home Page-->
                                <script type="text/javascript" src="https://secure.afilio.com.br/?progid=2289&type=homepage&id_partner=dumbupro&url_product=https://dumbu.pro/follows/src/"></script>        
                                
    
    <div class="container">
        <div class="col-md-3 col-sm-3 col-xs-12"><br></div>
            <footer class="text-center fleft100 m-t30 m-b10"><img src="https://dumbu.pro/follows/src/assets/images/logo-footer.png" class="wauto" alt="Dumbu Footer Logo"></footer>
    </div>
                    
    
    <!--Start of Boostbox Tag Script-->
                <script async="1" src="//tags.fulllab.com.br/scripts/master-tag/produto_dumbu.js"></script>
        <!--End of Boostbox Tag Script-->
        <!-- BEGIN JIVOSITE CODE -->
<script type='text/javascript'>
(function(){ var widget_id = 'Af9Dp13Ek6';var d=document;var w=window;function l(){var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true;s.src = '//code.jivosite.com/script/widget/'+widget_id; var ss = document.getElementsByTagName('script')[0]; ss.parentNode.insertBefore(s, ss);}if(d.readyState=='complete'){l();}else{if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})();
</script>
<!-- END JIVOSITE CODE -->
</body>
</html>    
