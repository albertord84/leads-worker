<!DOCTYPE html>
<html lang="pt-BR">
    <head>
            <?php  $CI =& get_instance();?>
            <?php $this->load->model('class/payment_type');?>
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
            <link rel="stylesheet" href="<?php echo base_url().'assets/css/estilo.css?'.$SCRIPT_VERSION;?>"/>
            <link rel="stylesheet" href="<?php echo base_url().'assets/css/definicoes.css?'.$SCRIPT_VERSION;?>"/>
            <link rel="stylesheet" href="<?php echo base_url().'assets/css/media.css?'.$SCRIPT_VERSION;?>"/>
            <link rel="stylesheet" href="<?php echo base_url().'assets/css/ladda-themeless.min.css'?>">
            
            <!-- jQuery -->
            <script type="text/javascript" src="<?php echo base_url().'assets/js/jquery.js';?>"></script>
            
            <script type="text/javascript" src="<?php echo base_url().'assets/js/front.js?'.$SCRIPT_VERSION;?>"></script>                
            <script type="text/javascript" src="<?php echo base_url().'assets/js/admin_page.js?'.$SCRIPT_VERSION;?>"></script>
            <script type="text/javascript" src="<?php echo base_url().'assets/js/translation.js?'.$SCRIPT_VERSION;?>"></script> 
            
            <script src="<?php echo base_url().'assets/js/spin.min.js'?>"></script>
            <script src="<?php echo base_url().'assets/js/ladda.min.js'?>"></script> 
            <style>
                .modal { overflow: auto !important; }
            </style>
<style>
/* unvisited link */
a:link {
    color: white;
}

/* visited link */
a:visited {
    color: white;
}

/* mouse over link */
a:hover {
    color: white;
}

/* selected link */
a:active {
    color: white;
}
</style>
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
                                        </button><br>
                                        <!--<div id="link_robot"> -->
                                        <!--<font color="white">-->
                                            <a target="_blank" href=<?php echo base_url().'index.php/admin/robot' ?>>GERENCIAR ROBOTS PROFILES</a><br>
                                            <a style="color:white;" class="pointer_mouse" data-toggle="modal" data-target="#reporta_boleto" ><?php echo $CI->T("REPORTAR PAGO DE BOLETO", array(),$language);?></a>                                            
                                        <!--</font>-->
                                        <!--</div> -->   

                                    </div>					
                            </div>
                    </header>
            </section>
            <!--Admin Painel-->
            
            <!-- Modal Reportar Boleto -->
            <div id="reporta_boleto" class="modal fade" role="dialog">                
              <div class="modal-dialog mxw-600">
                <div class="modal-content fleft100 text-center pd-20">
                    <input id = "id_campaing_leads" type="hidden" value = ""></input>                     
                            <a class="close" data-dismiss="modal" >&times;</a>
                            <!--<button type="button" class="bk-none b-none pull-right" data-dismiss="modal"><img src="img/close.png" alt=""></button>-->
                            <hr class="fleft100">
                            <span class="fleft100 m-top10 m-b30"><?php echo $CI->T("INFORMAÇÕES DO PAGAMENTO DO BOLETO", array(),$language);?></span>
                            <div align = "left" class="col-md-6 col-sm-6 col-xs-12">
                                    <div>
                                        <?php echo $CI->T("Número do documento", array(),$language);?><br>
                                        <input id = "order_number" type="text" placeholder="500000000XXX"><br><br>
                                    </div>
                                    
                                    <div>
                                        <?php echo $CI->T("Valor Pago em", array(),$language);?> $R<br>
                                        <input id = "valor_pago" type="text" placeholder="150.00"><br><br>
                                    </div>
                                    <div>
                                        
                                    </div>                                    
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div>    
                                    <?php echo $CI->T("Data do pagamento", array(),$language);?><br>
                                    <input id = "data_pago" type="date" placeholder="dd/mm/yyyy" min='2018-06-01' max='<?php echo date('Y-m-d');?>'><br>
                                </div>                                      
                            </div>
                            <hr class="fleft100">
                            <div class="col-md-3 col-sm-3 col-xs-12 text-center">                                    
                            </div>                            
                            <div class="col-md-6 col-sm-6 col-xs-12 text-center">
                                    <button id = "do_payed_ticket" type="button" class="btn btn-mlds btngreen m-top10"><?php echo $CI->T("Reportar Pago", array(),$language);?></button>
                            </div>                            
                            <div class="col-md-3 col-sm-3 col-xs-12 text-center m-top60">                            
                            </div>        
                </div>
              </div>
            </div>
            <!-- Fecha Modal Reportar Boleto -->
            
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
        <!--<div id="link_robot"> -->
        <!--    <a target=" blank" href=-->
            <?php 
            //echo base_url().'index.php/admin/robot' 
            ?>
        <!-- >Gerenciar robot-profiles</a> -->
        <!--</div> -->   
        <div id="login_container1">
            <div id="admin_form" class="row">
                <div class="col-md-1"></div>
                <div class="col-md-2">
                    <div class="center filters">
                        <b>Status</b>   
                        <select id="client_status" class="form-control">
                            <option value="-1">--SELECT--</option>
                            <option value="0">TODOS OS STATUS</option>
                            <?php
                             echo '<option value="1">'.$CI->T("ATIVO", array(),$language).'</option>';                                        
                             echo '<option value="2">'.$CI->T("BLOQUEADO POR PAGAMENTO", array(),$language).'</option>';                                        
                             echo '<option value="4">'.$CI->T("ELIMINADO", array(),$language).'</option>';                                        
                             echo '<option value="6">'.$CI->T("PENDENTE POR PAGAMENTO", array(),$language).'</option>';                                        
                             echo '<option value="8">'.$CI->T("INICIANTE", array(),$language).'</option>';                                        
                             echo '<option value="11">'.$CI->T("NÃO USAR MAIS", array(),$language).'</option>';                                        
                            ?>
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
                <div class="col-md-4">
                    <div class="center filters">
                    <!--<b>Assinatura (inic)</b>
                        <input id = "signin_initial_date" type="text" class="form-control"  placeholder="MM/DD/YYYY" >-->
                        <b>Data da assinatura</b>
                    </div>
                    <div class="col-xs-1">
                        <b>do</b>
                    </div>
                    <div class="col-xs-5">
                        <input type="date" id="assin_date_from1" name="assin_date_from1" placeholder="mm/dd/yyyy" class="form-control">
                    </div>
                    <div class="col-xs-1">
                        <b>até</b>
                    </div>
                    <div class="col-xs-5">
                        <input type="date" id="assin_date_to1" name="assin_date_to1" placeholder="mm/dd/yyyy" class="form-control">
                    </div>
                    <!-- <div class="center">
                        <input type="text" id="date_from" name="date_from" placeholder="mm/dd/yyyy" class="form-control" style="max-width:160px">
                        <label for="date_to">até</label>
                        <input type="text" id="date_to" name="date_to" placeholder="mm/dd/yyyy" class="form-control" style="max-width:160px">
                    </div> -->
                        <!-- <table>
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
                    </div> -->
                </div>
                <div class="col-md-4">
                    <div class="center filters">
                        <b>Data do status</b>
                    </div>
                    <div class="col-xs-1">
                        <b>do</b>
                    </div>
                    <div class="col-xs-5">
                        <input type="date" id="status_date_from1" name="status_date_from1" placeholder="mm/dd/yyyy" class="form-control">
                    </div>
                    <div class="col-xs-1">
                        <b>até</b>
                    </div>
                    <div class="col-xs-5">
                        <input type="date" id="status_date_to1" name="status_date_to1" placeholder="mm/dd/yyyy" class="form-control">
                    </div>
                </div>   
                <div class="col-md-1"></div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-1"></div>
                <div class="col-md-2">
                    <div class="center filters">
                        <b>Perfil do cliente</b>
                        <input id = "profile_client1" type="text" class="form-control"  placeholder="Perfil do cliente">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="center filters">
                        <b>Email do cliente</b>                        
                        <input id="email_client1" type="email" class="form-control" placeholder="Email do cliente">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="center filters">
                        <b>ID do cliente</b>
                        <input id="client_id1" class="form-control" placeholder="ID do cliente">
                    </div>
                </div>
                <!--<div class="col-md-2">
                    <div class="center filters">
                        <b>Insta ID</b>
                        <input id="ds_user_id" class="form-control" placeholder="ds_user_id">
                    </div>
                </div>-->
                <div class="col-md-2">
                    <div class="center filters">
                        <b>Credit Card nome</b>
                        <input id="credit_card_name1" class="form-control" placeholder="Credit Card Name">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="center filters">
                        <b>Cód. Promocional</b>
                        <select disabled id="cod_promocional" class="form-control" >                            
                            <option>--SELECT--</option>
                            <option>PEIXE URBANO</option>
                            <option title="7 dias de graça">INSTA-DIRECT</option>
                            <option title="7 dias de graça">MALADIRETA</option>
                            <option title="15 dias de graça">INSTA15D</option>
                            <option title="1 mês de graça">AMIGOSDOPEDRO</option>
                            <option title="20% de desconto de por vida">DUMBUDF20</option>
                            <option title="50% de desconto o primeiro mês">INSTA50P</option>
                            <option title="50% de desconto o primeiro mês">BACKTODUMBU</option>
                            <option title="50% de desconto o primeiro mês">BACKTODUMBU-DNLO</option>
                            <option title="50% de desconto o primeiro mês">BACKTODUMBU-EGBTO</option>
                            <option>FITNESS</option>
                            <option>SHENIA</option>
                            <option>VANESSA</option>
                            <option>CAROL</option>
                            <option>NINA</option>
                            <option>NICOLE</option>
                        </select>
                    </div>
                </div>
            </div>
            <br>

            <div class="row">
                <div class="col-md-1"></div>
                <!--<div class="col-md-2">
                    <div class="center filters">
                        <b>Plano</b>   
                        <select disabled id="plane" class="form-control">
                            <option value="0">--SELECT--</option>
                            <option value="1">1</option>
                            <option value="2">2 (LOW)</option>
                            <option value="3">3 (MODERATED)</option>
                            <option value="4">4 (FAST)</option>
                            <option value="5">5 (TURBO)</option>
                        </select>
                    </div> 
                </div>-->
                <!--<div class="col-md-2">
                    <div class="center filters">
                        <b>Mais de </b>   
                        <select disabled id="tentativas" class="form-control">
                            <option value="0">--SELECT--</option>
                            <?php for ($tentativas = 1; $tentativas <= 9; $tentativas++) { ?>
                                    <option value="<?php echo $tentativas; ?>"><?php echo $tentativas; ?></option>
                            <?php } ?>
                        </select>
                        <b>tentativas de compra</b> 
                    </div> 
                </div>-->
                <!--<div class="col-md-2">
                    <div class="center filters">
                        <b>Observações</b> 
                        <select disabled id="observations" class="form-control" >
                            <option>--SELECT--</option>
                            <option>NAO</option>
                            <option>SIM</option>
                        </select>    
                    </div>
                </div>-->
              
                <!--<div class="col-md-4">
                    <div class="center filters">
                    <b>Assinatura (inic)</b>
                        <input id = "signin_initial_date" type="text" class="form-control"  placeholder="MM/DD/YYYY" >
                        <b>Requerir: </b>
                    </div>
                    <div class="col-xs-1">
                        <b>Campanha: </b>
                    </div>
                    <div class="col-xs-5">
                        <input type="checkbox" id="requercampaing">
                    </div>
                    <div class="col-xs-1">
                        <b>Cartão: </b>
                    </div>
                    <div class="col-xs-5">
                        <input type="checkbox" id="requercard">
                    </div>
                    <div class="col-xs-1">
                        <b>Campanha: </b><input type="checkbox" id="requercampaing">
                    </div>
                    <div class="col-xs-1">
                        <b>Cartão: </b> <input type="checkbox" id="requercard">
                    </div>
                </div> -->   
                <!--<div class="col-md-2">
                    <div class="center filters">
                        <b>Order Key</b>
                        <input id="order_key_client"  class="form-control" placeholder="Order Key">
                    </div>
                </div>-->
            </div>
            <div class="row">
                <div class="col-md-1"></div>
                <div class="col-md-2">
                    <div class="center filters">
                        <b>Não recebe trabalho há mais de</b>
                        <input disabled id="days_no_work"  class="form-control" placeholder="Número de dias">
                    </div>
                </div>
                <!--<div class="col-md-1"></div>-->
                <div class="col-md-4"><br>
                    <div class="center filters">
                        <b>Intervalo de campanhas:</b>
                    </div>
                    <div class="col-xs-1">
                        <b>do</b>
                    </div>
                    <div class="col-xs-5">
                        <input type="date" id="campaigns_from" name="campaigns_from" placeholder="mm/dd/yyyy" class="form-control">
                    </div>
                    <div class="col-xs-1">
                        <b>até</b>
                    </div>
                    <div class="col-xs-5">
                        <input type="date" id="campaigns_to" name="campaigns_to" placeholder="mm/dd/yyyy" class="form-control">
                    </div>
                </div>   
                <!--<div class="col-md-2">
                    <div class="center filters">
                        <br>
                        <b>Paused</b> 
                        <select disabled id="paused" class="form-control" >
                            <option value="-1">--SELECT--</option>
                            <option value="0">NAO</option>
                            <option value="1">SIM</option>
                        </select>    
                    </div>
                </div>-->
                <!--<div class="col-md-2">
                    <div class="center filters">
                        <br>
                        <b>Total Unfollow</b> 
                        <select id="total_unfollow" class="form-control" >
                            <option value="-1">--SELECT--</option>
                            <option value="0">NAO</option>
                            <option value="1">SIM</option>
                        </select>    
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="center filters">
                        <br>
                        <b>Autolike</b> 
                        <select id="autolike" class="form-control" >
                            <option value="-1">--SELECT--</option>
                            <option value="0">NAO</option>
                            <option value="1">SIM</option>
                        </select>    
                    </div>
                </div>-->
                <div class="col-md-2">
                    <div class="center filters">
                    <!--<b>Assinatura (inic)</b>
                        <input id = "signin_initial_date" type="text" class="form-control"  placeholder="MM/DD/YYYY" >-->
                        <br>
                        <b>Requerimento(s)</b>
                        <br>
                        <b>Criar Campanha:</b> 
                        <!--<select id="total_unfollow" class="form-control" >
                            <option value="-1">--SELECT--</option>
                            <option value="0">NAO</option>
                            <option value="1">SIM</option>
                        </select>-->   
                        <input type="checkbox" id="createcampaing">
                    </div>
                </div>
                <!--<div class="col-md-2">
                    <div class="center filters">
                        <br>
                        <br>
                        <b>Usar Cartão</b> 
                        <select id="autolike" class="form-control" >
                            <option value="-1">--SELECT--</option>
                            <option value="0">NAO</option>
                            <option value="1">SIM</option>
                        </select> 
                        <input type="checkbox" id="usecard">
                    </div>
                </div>-->
                <div class="col-md-2">
                    <div class="center filters">
                        <br>
                        <b>Pagamento: </b> 
                        <select id="payments_types" class="form-control" >
                            <option value="0">--SELECT--</option>
                            <option value="<?php echo payment_type::CREDIT_CARD; ?>">CREDIT_CARD</option>
                            <option value="<?php echo payment_type::TICKET_BANK; ?>">TICKET_BANK</option>
                        </select>    
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-1"></div>
                <div class="col-md-2">
                    <div class="center filters">
                        <b>UTM source</b> 
                        <select disabled id="utm_source" class="form-control" >
                            <option>--SELECT--</option>
                            <?php
                                if (isset($utm_source_list)){
                                    $num_rows = count($utm_source_list);
                                    for ($i = 0; $i < $num_rows; $i++) {
                                        if ($utm_source_list[$i]['utm_source'] === null)
                                            echo '<option title="null in database">---</option>';
                                        else
                                            echo '<option>'.$utm_source_list[$i]['utm_source'].'</option>';
                                    }
                                }
                            ?>
                        </select>    
                    </div>
                </div>
                <!--<div class="col-md-2">
                    <div class="center filters">
                        <b>Perfis de Ref. ativos</b> 
                        <select disabled id="pr_ativos" class="form-control" >
                            <option>--SELECT--</option>
                            <option>NAO</option>
                            <option>SIM</option>
                        </select>    
                    </div>
                </div>-->
                <!--<div class="col-md-1"></div>-->
                <!--<div class="col-md-1"></div>
                <div class="col-md-2">
                    <div class="center filters">
                        <b>Data solicitada:</b> 
                       <input type="date" id="last_access1" name="last_access1" placeholder="mm/dd/yyyy" class="form-control">
                      </input>
                    </div>
                </div>
                <div class="col-md-1"></div>
                <div class="col-md-2">
                    <div class="center filters">
                        <b>Ùltimo acesso:</b> 
                       <input type="date" id="last_access2" name="last_access2" placeholder="mm/dd/yyyy" class="form-control">
                      </input>
                    </div>
                </div>-->
                <div class="col-md-4">
                    <div class="center filters">
                    <!--<b>Assinatura (inic)</b>
                        <input id = "signin_initial_date" type="text" class="form-control"  placeholder="MM/DD/YYYY" >-->
                        <b>Procurar pagamento</b>
                    </div>
                    <div class="col-xs-1">
                        <b>do</b>
                    </div>
                    <div class="col-xs-5">
                        <input type="date" id="last_access1" name="last_access1" placeholder="mm/dd/yyyy" class="form-control">
                    </div>
                    <div class="col-xs-1">
                        <b>até</b>
                    </div>
                    <div class="col-xs-5">
                        <input type="date" id="last_access3" name="last_access3" placeholder="mm/dd/yyyy" class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="center filters">
                        <b>Último acesso:</b>
                    </div>
                    <div class="col-xs-1">
                        <b>do</b>
                    </div>
                    <div class="col-xs-5">
                        <input type="date" id="last_access2" name="last_access2" placeholder="mm/dd/yyyy" class="form-control">
                    </div>
                    <div class="col-xs-1">
                        <b>até</b>
                    </div>
                    <div class="col-xs-5">
                        <input type="date" id="last_access4" name="last_access4" placeholder="mm/dd/yyyy" class="form-control">
                    </div>
                </div>   
            </div>  
            <!--<div class="row">-->
            <br>
                        <!--<table class="table">

                            <tr class="list-group-item-success" id="row-client" style="text-align:center; width:100%; align-items:center; visibility: visible; background-color: #dff0d8">
                                <td style="text-align:center; align-items:center; width:50%; padding:5px">
                                        <div class="col-md-1"></div>
                                        <div class="col-md-2">
                                           <div class="center">
                                              <button  style="min-width:150px" id = "execute_query" type="button" class="btn btn-success ladda-button"  data-style="expand-center" data-spinner-color="#ffffff">
                                                <span class="ladda-label">Listar</span>
                                              </button>
                                           </div>
                                        </div>
                                </td>                                
                                <td style="text-align:center; align-items:center; width:50%; padding:5px">
                                    <div class="col-md-1"></div>
                                      <div class="col-md-2">
                                        <div class="center">
                                             <button  style="min-width:150px" id = "execute_query_email" type="button" class="btn btn-success ladda-button"  data-style="expand-center" data-spinner-color="#ffffff">
                                                 <span class="ladda-label">Obter emails</span>
                                             </button>
                                        </div>
                                     </div>
                                </td>
                            </tr>    
                        </table>-->
            <div id="user_form" class="row">
                <div class="col-md-1"></div>
                <div class="col-md-2">
                  <!--<br><p><b style="color:red">Total de registros: </b><b id="total_users">
                   </b></p><br>-->
                                       <div class="center">
                                              <button  style="min-width:150px" id = "execute_query" type="button" class="btn btn-success ladda-button"  data-style="expand-center" data-spinner-color="#ffffff">
                                                <span class="ladda-label">Listar</span>
                                              </button>
                                        </div>
            </div>
                <div class="col-md-1"></div>
                <div class="col-md-2" id="totalpago">
            <!--<br><p><b style="color:red">Pagamento Total: </b><b id="totalpayment"></b></p><br>-->
                                       <div class="center">
                                             <button  style="min-width:150px" id = "execute_query_email" type="button" class="btn btn-success ladda-button"  data-style="expand-center" data-spinner-color="#ffffff">
                                                 <span class="ladda-label">Obter emails</span>
                                             </button>
                                        </div>
                 </div>
            </div>
                         
                    <br>
                <div class="col-md-1"></div>
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
                </div>
                <div style="text-align:center;">
            
                        <div id="container_users1">
                        </div>
                </div> 
                
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
                    <button id="accept_modal_alert_message" type="button" class="btn btngreen active text-center ladda-button" data-style="expand-left" data-spinner-color="#ffffff">
                        <spam class="ladda-label"><div style="color:white; font-weight:bold">OK</div></spam>
                    </button>                    
                </div>
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
