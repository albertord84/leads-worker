<?php

namespace leads\cls {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/dumbu/worker/libraries/APICIELO3.0/vendor/autoload.php';
    require_once 'system_config.php';

    use Cielo\API30\Merchant;
    use Cielo\API30\Ecommerce\Environment;
    use Cielo\API30\Ecommerce\Sale;
    use Cielo\API30\Ecommerce\CieloEcommerce;
    //use Cielo\API30\Ecommerce\Payment as PaymentCielo;
    use Cielo\API30\Ecommerce\Request\CieloRequestException;

//    require_once('libraries/mundipagg/init.php');
//    require_once('class/system_config.php');

    /**
     * class Payment
     * 
     */
    class PaymentCielo {
        /** Aggregations: */
        /** Compositions: */
        /** Attributes:   */

        /**
         * 
         * @access public
         */
        public $id;

        /**
         * 
         * @access public
         */
        public $value;

        /**
         * 
         * @access public
         */
        public $date;

        /**
         * 
         * @param type $payment_data
         * @param type $recurrence Default to infinite (0)
         * @param type $$paymentMethodCode (20) | 5 Cielo -> 1.5 | 32 -> eRede | 20 -> Stone | 42 -> Cielo 3.0 | 0 -> Auto;
         * @return string
         */
        public function create_recurrency_payment($payment_data, $recurrence = 0, $paymentMethodCode = 20) {
            
        }

        /**
         * 
         * @param type $payment_data
         */
        public function create_payment_debit($payment_data) {
            // ...
            // Configure o ambiente
//            $environment = $environment = Environment::sandbox();
            $environment = $environment = Environment::production();

            // Configure seu merchant
            $merchant = new Merchant('472a5d6b-6ba8-476c-9bd6-377e19eafe9d', 'Z87vM3TKvBfG4Zj2BgHGoYfkBqFJMcXTBuWZhJj1');
            //1077629602 estabelecimento
            // Crie uma instância de Sale informando o ID do pagamento
            $sale = new Sale($payment_data['credit_card_number']);

            // Crie uma instância de Customer informando o nome do cliente
            $customer = $sale->customer($payment_data['credit_card_name']);

            // Crie uma instância de Payment informando o valor do pagamento
            $payment = $sale->payment(1000);

            // Defina a URL de retorno para que o cliente possa voltar para a loja
            // após a autenticação do cartão
            $payment->setReturnUrl('http://localhost/dumbu/src/?777');

            // Crie uma instância de Debit Card utilizando os dados de teste
            // esses dados estão disponíveis no manual de integração
            $payment->debitCard($payment_data['credit_card_cvc'], $payment_data['credit_card_flag'])
                    ->setExpirationDate($payment_data['credit_card_exp_month'] . '/' . $payment_data['credit_card_exp_year'])
                    ->setCardNumber($payment_data['credit_card_number'])
                    ->setHolder($payment_data['credit_card_name']);

            // Crie o pagamento na Cielo
            try {
                // Configure o SDK com seu merchant e o ambiente apropriado para criar a venda
                $sale = (new CieloEcommerce($merchant, $environment))->createSale($sale);

                // Com a venda criada na Cielo, já temos o ID do pagamento, TID e demais
                // dados retornados pela Cielo
                $paymentId = $sale->getPayment()->getPaymentId();

                // Utilize a URL de autenticação para redirecionar o cliente ao ambiente
                // de autenticação do emissor do cartão
                $authenticationUrl = $sale->getPayment()->getAuthenticationUrl();
                
                return $sale;
            } catch (CieloRequestException $e) {
                // Em caso de erros de integração, podemos tratar o erro aqui.
                // os códigos de erro estão todos disponíveis no manual de integração.
                $error = $e->getCieloError();
//                var_dump($error);
            }
        }

        // end of member function add_payment

        public static function detectCardType($num) {
            $re = array(
                "visa" => "/^4[0-9]{12}(?:[0-9]{3})?$/",
                "mastercard" => "/^5[1-5][0-9]{14}$/",
                "amex" => "/^3[47][0-9]{13}$/",
                "discover" => "/^6(?:011|5[0-9]{2})[0-9]{12}$/",
                "diners" => "/^3[068]\d{12}$/",
                "elo" => "/^((((636368)|(438935)|(504175)|(451416)|(636297))\d{0,10})|((5067)|(4576)|(4011))\d{0,12})$/",
                "hipercard" => "/^(606282\d{10}(\d{3})?)|(3841\d{15})$/",
            );

            if (preg_match($re['visa'], $num)) {
                return 'Visa';
            } else if (preg_match($re['mastercard'], $num)) {
                return 'Mastercard';
            } else if (preg_match($re['amex'], $num)) {
                return 'Amex';
            } else if (preg_match($re['discover'], $num)) {
                return 'Discover';
            } else if (preg_match($re['diners'], $num)) {
                return 'Diners';
            } else if (preg_match($re['hipercard'], $num)) {
                return 'Hipercard';
            } else {
                return false;
            }
        }

        /**
         * 
         *
         * @return bool
         * @access public
         */
        public function cancel_payment($order_key) {
            
        }

        // end of member function delete_payment

        /**
         * 
         *
         * @return Payment
         * @access public
         */
        public function update_payment() {
            
        }

        // end of member function update_payment

        /**
         * 
         *
         * @return Payment
         * @access public
         */
        public function check_payment($payment_id) {
            $url = "https://apiquery.cieloecommerce.cielo.com.br/1/sales/$payment_id";
            $curl_str = "curl --request GET '$url' ";
            $curl_str .= "-H 'content-type: application/json' ";
            $curl_str .= "-H 'MerchantId: 472a5d6b-6ba8-476c-9bd6-377e19eafe9d' ";
            $curl_str .= "-H 'MerchantKey: Z87vM3TKvBfG4Zj2BgHGoYfkBqFJMcXTBuWZhJj1' ";
            $curl_str .= "--data-binary --verbose --insecure";
            exec($curl_str, $output, $status);
            //print_r($output);
            //print("-> $status<br><br>");
            $json = json_decode($output[0]);
            return $json;
        }

        // end of member function update_payment

        function queryOrder($order_key) {
            
        }

        function retry_payment_recurrency($order_key, $transaction_key, $cvc = NULL) {
            
        }

        /**
         * Check whether the $order_key have any payment done
         * @param type $order_key
         * @return boolean
         */
        public function check_client_order_paied($order_key) {
            
        }

        // end of Payment
    }

}

?>
