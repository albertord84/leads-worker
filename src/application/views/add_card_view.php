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
        <script type="text/javascript" src="<?php echo base_url().'assets/js/leadsjs/front.js'?>"></script>                
        <script type="text/javascript" src="<?php echo base_url().'assets/js/leadsjs/client_page.js'?>"></script>  
  </head>
  <body>
      
    <div class="pay fleft100 input-form">
        
        <input id="credit_card_name" onkeyup="javascript:this.value = this.value.toUpperCase();"  placeholder="Name in card" required style="text-transform:uppercase;">
        
        <!--<input type="text" placeholder="E-mail"  id="client_email" type="email"  required>-->

        <input id="credit_card_number" type="text" placeholder="Card number" data-mask="0000 0000 0000 0000" maxlength="20" required>
        
        <input id="credit_card_cvc" type="text" placeholder="CVV/CVC" maxlength="5" required>

        <div class="col-md-4 col-sm-4 col-xs-12 pd-r15 m-t10">
            <fieldset>
                <div class="select"> 
                    <select name="local" id="credit_card_exp_month" > 
                        <option>01</option><option>02</option><option>03</option><option>04</option><option>05</option><option>06</option><option>01</option><option>02</option><option>03</option><option>04</option><option>05</option><option>06</option><option>07</option><option>08</option><option>09</option><option>10</option><option>11</option><option>12</option>
                    </select>
                </div>
            </fieldset>
        </div>
        <div class="col-md-4 col-sm-4 col-xs-12 no-pd m-t10">
            <fieldset>
                <div class="select">
                    <select name="local" id="credit_card_exp_year" class="btn-primeiro sel">                                        
                        <option>2018</option><option>2019</option><option>2020</option><option>2021</option><option>2022</option><option>2023</option><option>2024</option><option>2025</option><option>2026</option><option>2027</option><option>2028</option><option>2029</option><option>2030</option><option>2031</option><option>2032</option><option>2033</option><option>2034</option><option>2035</option><option>2036</option><option>2037</option><option>2038</option><option>2039</option>
                    </select>
                </div>
            </fieldset>
        </div>  
    </div>

    <button id="do_add_card" type="button" class="btn btn-success active text-center ladda-button" data-style="expand-left" data-spinner-color="#ffffff">
      <spam class="ladda-label"><div style="color:white; font-weight:bold">Add credit card</div></spam>
    </button>
  </body>
</html>