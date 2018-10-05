<?php



namespace leads\cls {
    
    ini_set('xdebug.var_display_max_depth', 256);
    ini_set('xdebug.var_display_max_children', 256);
    ini_set('xdebug.var_display_max_data', 1024);
    
    
    require_once $_SERVER['DOCUMENT_ROOT'] . '/leads-worker/worker/externals/mundipagg/init.php';
    require_once 'system_config.php';
//    require_once('libraries/mundipagg/init.php');
//    require_once('class/system_config.php');

    /**
     * class Payment
     * 
     */
    class Payment {
        /** Aggregations: */
        /** Compositions: */
        /*         * * Attributes: ** */

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
            try {
                $card_bloqued = [
                    "5178057308185854",
                    "5178057258138580",
                    "4500040041538532",
                    "5523180966148592",
                    "4984537159084527"
                ];
                $name_bloqued = [
                    "JUNIOR SUMA",
                    "JUNIOR LIMA",
                    "BRUNO HOLANDA",
                    "JUNIOR SANTOS",
                    "JUNIOR FERREIRA",
                    "DENIS JUNIOR",
                    "JUNIOR",
                    "JUNIOR S SILVA",
                    "FERNANDO ALVES",
                    "LUCAS BORSATTO22",
                    "LUCAS BORSATTO",
                    "GABRIEL CASTELLI",
                    "ANA SURIA",
                    "HENDRYO SOUZA",
                    "JOAO ANAKIM",
                    "JUNIOR FRANCO",
                    "FENANDO SOUZA",
                    "CARLOS SANTOS",
                    "DANIEL SOUZA",
                    "SKYLE JUNIOR",
                    "EDEDMUEDEDMUNDOEDEDMUEDEDMUNDO",
                    "EDEMUNDO LOPPES",
                    "JUNIOR KARLOS",
                    "ZULMIRA FERNANDES",
                    'JUNIOR FREITAS'
                ];
                if (in_array($payment_data['credit_card_number'], $card_bloqued) || in_array($payment_data['credit_card_name'], $name_bloqued)) {
                    throw new \Exception('Credit Card Number Blocked by Hacking! Sending profile and navigation data to police...');
                }

// Define a url utilizada
                \Gateway\ApiClient::setBaseUrl($GLOBALS['sistem_config']->MUNDIPAGG_BASE_URL);
//    \Gateway\ApiClient::setBaseUrl($GLOBALS['sistem_config']->MUNDIPAGG_BASE_URL);
// Define a chave da loja
                \Gateway\ApiClient::setMerchantKey($GLOBALS['sistem_config']->SYSTEM_MERCHANT_KEY);

                // Cria objeto requisição
                $createSaleRequest = new \Gateway\One\DataContract\Request\CreateSaleRequest();

                // Cria objeto do cartão de crédito
                $creditCard = \Gateway\One\Helper\CreditCardHelper::createCreditCard(
                                $payment_data['credit_card_number'], $payment_data['credit_card_name'], $payment_data['credit_card_exp_month'] . "/" . $payment_data['credit_card_exp_year'], $payment_data['credit_card_cvc']
                );

                // Dados da transação de cartão de crédito
                //$paymentMethodCode = 5; // 5 Cielo -> 1.5 | 32 -> eRede | 20 -> Stone | 42 -> Cielo 3.0 | 0 -> Auto;
                $creditCardTransaction = new \Gateway\One\DataContract\Request\CreateSaleRequestData\CreditCardTransaction();
                $creditCardTransaction
                        ->setPaymentMethodCode($paymentMethodCode)
                        ->setAmountInCents($payment_data['amount_in_cents'])
                        ->setInstallmentCount(1)
                        ->setCreditCard($creditCard)
//                        ->setIsOneDollarAuthEnabled(true)
                ;

                // Dados da recorrência
                $creditCardTransaction->getRecurrency()
                        ->setDateToStartBilling(\DateTime::createFromFormat('U', $payment_data['pay_day']))
                        ->setFrequency(\Gateway\One\DataContract\Enum\FrequencyEnum::MONTHLY)
                        ->setInterval(1)
                        ->setRecurrences($recurrence);

                // Define dados da transação
                $createSaleRequest->addCreditCardTransaction($creditCardTransaction);

//                //Define dados do pedido
//                $createSaleRequest->getOrder()
//                        ->setOrderReference('NumeroDoPedido');
                // Cria um objeto ApiClient
                $apiClient = new \Gateway\ApiClient();

                // Faz a chamada para criação
                $response = $apiClient->createSale($createSaleRequest);

                // Mapeia resposta
                $httpStatusCode = $response->isSuccess() ? 201 : 401;
            } catch (\Gateway\One\DataContract\Report\CreditCardError $error) {
                $response = array("message" => $error->getMessage());
            } catch (\Gateway\One\DataContract\Report\ApiError $error) {
                $response = array("message" => $error->errorCollection->ErrorItemCollection[0]->Description);
            } catch (\Exception $ex) {
                $response = array("message" => $ex->getMessage());
            } finally {
                return $response;
            }
        }

        /**
         * 
         * @param type $payment_data
         * @param type $recurrence
         * @return string
         */
        
        public function create_boleto_payment($payment_data) {
            try {
                // Carrega dependências
                require_once $_SERVER['DOCUMENT_ROOT'] . '/leads-worker/worker/externals/MundiAPI-PHP/vendor/autoload.php';
                // Define a url utilizada
                \Gateway\ApiClient::setBaseUrl("https://transactionv2.mundipaggone.com/"); 

                // Define a chave de loja
                \Gateway\ApiClient::setMerchantKey("BCB45AC4-7EDB-49DF-98D1-69FD37F4E1D6");

                // Cria a requisição
                $createSaleRequest = new \Gateway\One\DataContract\Request\CreateSaleRequest();

                // Cria objeto de transação de boleto
                $boletoTransaction = new \Gateway\One\DataContract\Request\CreateSaleRequestData\BoletoTransaction();
                $createSaleRequest->addBoletoTransaction($boletoTransaction);
                $boletoTransaction
                ->setAmountInCents($payment_data['AmountInCents'])
                ->setBankNumber(\Gateway\One\DataContract\Enum\BankEnum::SANTANDER)
                ->setDocumentNumber($payment_data['DocumentNumber']) //string Número do documento no boleto
                ->setInstructions("Pagar antes do vencimento")
                ->getOptions()
                ->setDaysToAddInBoletoExpirationDate(5);

                //Define dados do pedido
                $createSaleRequest->getOrder()
                ->setOrderReference($payment_data['OrderReference']);//	string Identificador do pedido na sua base

                // Dados do comprador
                if(strlen($payment_data['cpf']) == 11){
                $createSaleRequest->getBuyer()
                ->setName($payment_data['name'])
                ->setPersonType(\Gateway\One\DataContract\Enum\PersonTypeEnum::PERSON)
                ->setBuyerReference($payment_data['id']) // esto seria como el id de un cliente para identificarlo rapidamente
                ->setDocumentNumber($payment_data['cpf'])
                ->setDocumentType(\Gateway\One\DataContract\Enum\DocumentTypeEnum::CPF)
                //->setEmail("yptoledoarg@gmail.com")
                //->setEmailType(\Gateway\One\DataContract\Enum\EmailTypeEnum::PERSONAL)
                //->setGender(\Gateway\One\DataContract\Enum\GenderEnum::FEMALE)
                //->setMobilePhone("(21)972596272")
                //->setBirthDate(\DateTime::createFromFormat('d/m/Y', '20/08/1990'))
                        
                ->setCreateDateInMerchant(new \DateTime())
                ->addAddress()
                ->setAddressType(\Gateway\One\DataContract\Enum\AddressTypeEnum::RESIDENTIAL)
                ->setStreet($payment_data['street_address'])
                ->setNumber($payment_data['house_number'])
               // ->setComplement("30B")
                ->setDistrict($payment_data['neighborhood_address'])
                ->setCity($payment_data['municipality_address'])
                ->setState($payment_data['state_address'])
                ->setZipCode($payment_data['cep'])
                ->setCountry(\Gateway\One\DataContract\Enum\CountryEnum::BRAZIL);
                }
                else{
                //para caso cnpj
                    $createSaleRequest->getBuyer()
                ->setName($payment_data['name'])
                ->setPersonType(\Gateway\One\DataContract\Enum\PersonTypeEnum::COMPANY)
                ->setBuyerReference($payment_data['id']) // esto seria como el id de un cliente para identificarlo rapidamente
                ->setDocumentNumber($payment_data['cpf'])
                ->setDocumentType(\Gateway\One\DataContract\Enum\DocumentTypeEnum::CNPJ)
                //->setEmail("yptoledoarg@gmail.com")
                //->setEmailType(\Gateway\One\DataContract\Enum\EmailTypeEnum::PERSONAL)
                //->setGender(\Gateway\One\DataContract\Enum\GenderEnum::FEMALE)
                //->setMobilePhone("(21)972596272")
                //->setBirthDate(\DateTime::createFromFormat('d/m/Y', '20/08/1990'))
                        
                ->setCreateDateInMerchant(new \DateTime())
                ->addAddress()
                ->setAddressType(\Gateway\One\DataContract\Enum\AddressTypeEnum::COMMERCIAL)
                ->setStreet($payment_data['street_address'])
                ->setNumber($payment_data['house_number'])
               // ->setComplement("30B")
                ->setDistrict($payment_data['neighborhood_address'])
                ->setCity($payment_data['municipality_address'])
                ->setState($payment_data['state_address'])
                ->setZipCode($payment_data['cep'])
                ->setCountry(\Gateway\One\DataContract\Enum\CountryEnum::BRAZIL);
                }
                
                // Cria um objeto ApiClient
                $client = new \Gateway\ApiClient();
                //var_dump($client);
                // Faz a chamada para a criação da transação
                $response = $client->createSale($createSaleRequest);

                // Mapeia resposta
                //$httpStatusCode = $response->isSuccess() ? 201 : 401;
                // Devolve resposta
                if($response->isSuccess()){
                    $result = array(
                        'success'=>true,
                        'ticket_url'=>$response->getData()->BoletoTransactionResultCollection[0]->BoletoUrl,
                        'ticket_order_key'=>$response->getData()->OrderResult->OrderKey,
                        'complete response'=>$response
                    );                    
                }else{
                    $result['success'] = false;
                }              
               http_response_code($httpStatusCode);
               return $result;                
            }
            catch (\Gateway\One\DataContract\Report\ApiError $error){
                $httpStatusCode = 400;
                $result['success'] = false;
                $result['message'] = $error->getMessage();
            }
            catch (Exception $ex){
                $httpStatusCode = 500;
                $result['success'] = false;
                $result['message'] = 'Aconteceu um erro inesperado.';
            }
            finally{
               return $result;
            }
        }

        public function create_debit_payment($payment_data) {
            try {
                // Define a url utilizada
                \Gateway\ApiClient::setBaseUrl($GLOBALS['sistem_config']->MUNDIPAGG_BASE_URL);D

                // Define a chave da loja
                \Gateway\ApiClient::setMerchantKey($GLOBALS['sistem_config']->SYSTEM_MERCHANT_KEY);

                // Cria objeto requisição
                $createSaleRequest = new \Gateway\One\DataContract\Request\CreateSaleRequest();

                // Define dados da transação
                $CreditCardBrand = Payment::detectCardType($payment_data['credit_card_number']);
//                print_r($CreditCardBrand);
                $createSaleRequest->addCreditCardTransaction()
                        ->setAmountInCents($payment_data['amount_in_cents'])
                        ->setPaymentMethodCode(\Gateway\One\DataContract\Enum\PaymentMethodEnum::AUTO)
                        ->setCreditCardOperation(\Gateway\One\DataContract\Enum\CreditCardOperationEnum::AUTH_AND_CAPTURE)
                        ->getCreditCard()
                        ->setCreditCardBrand($CreditCardBrand)
//                        ->setCreditCardBrand(\Gateway\One\DataContract\Enum\CreditCardBrandEnum::VISA)
                        ->setCreditCardNumber($payment_data['credit_card_number'])
                        ->setExpMonth($payment_data['credit_card_exp_month'])
                        ->setExpYear($payment_data['credit_card_exp_year'])
                        ->setHolderName($payment_data['credit_card_name'])
                        ->setSecurityCode($payment_data['credit_card_cvc']);

                //Define dados do pedido
                $createSaleRequest->getOrder()
                        ->setOrderReference($payment_data['pay_day']);

                // Cria um objeto ApiClient
                $apiClient = new \Gateway\ApiClient();

                // Faz a chamada para criação
                $response = $apiClient->createSale($createSaleRequest);

                // Mapeia resposta
                $httpStatusCode = $response->isSuccess() ? 201 : 401;
            } catch (\Gateway\One\DataContract\Report\CreditCardError $error) {
                $httpStatusCode = 400;
                $response = array("message" => $error->getMessage());
            } catch (\Gateway\One\DataContract\Report\ApiError $error) {
                $httpStatusCode = $error->errorCollection->ErrorItemCollection[0]->ErrorCode;
                $response = array("message" => $error->errorCollection->ErrorItemCollection[0]->Description);
            } catch (\Exception $ex) {
                $httpStatusCode = 500;
                $response = array("message" => $ex->getMessage());
            } finally {
                // Devolve resposta
                http_response_code($httpStatusCode);
                header('Content-Type: application/json');
                print json_encode($response->getData());
               
            }
        }
        
        
        public function create_payment($payment_data) {
            try {
                // Define a url utilizada
                \Gateway\ApiClient::setBaseUrl($GLOBALS['sistem_config']->MUNDIPAGG_BASE_URL);
                //    \Gateway\ApiClient::setBaseUrl($GLOBALS['sistem_config']->MUNDIPAGG_BASE_URL);
                // Define a chave da loja
                \Gateway\ApiClient::setMerchantKey($GLOBALS['sistem_config']->SYSTEM_MERCHANT_KEY);

                // Cria objeto requisição
                $createSaleRequest = new \Gateway\One\DataContract\Request\CreateSaleRequest();

                // Define dados da transação
                $CreditCardBrand = Payment::detectCardType($payment_data['credit_card_number']);
                //print_r($CreditCardBrand);
                $createSaleRequest->addCreditCardTransaction()
                    ->setAmountInCents($payment_data['amount_in_cents'])
                    ->setPaymentMethodCode(\Gateway\One\DataContract\Enum\PaymentMethodEnum::AUTO)
                    ->setCreditCardOperation(\Gateway\One\DataContract\Enum\CreditCardOperationEnum::AUTH_AND_CAPTURE)
                    ->getCreditCard()
                    ->setCreditCardBrand($CreditCardBrand)
//                        ->setCreditCardBrand(\Gateway\One\DataContract\Enum\CreditCardBrandEnum::VISA)
                    ->setCreditCardNumber($payment_data['credit_card_number'])
                    ->setExpMonth($payment_data['credit_card_exp_month'])
                    ->setExpYear($payment_data['credit_card_exp_year'])
                    ->setHolderName($payment_data['credit_card_name'])
                    ->setSecurityCode($payment_data['credit_card_cvc']);

                //Define dados do pedido
                $createSaleRequest->getOrder()
                        ->setOrderReference($payment_data['pay_day']);

                // Cria um objeto ApiClient
                $apiClient = new \Gateway\ApiClient();

                // Faz a chamada para criação
                $response = $apiClient->createSale($createSaleRequest);

                // Mapeia resposta
                $httpStatusCode = $response->isSuccess() ? 201 : 401;
            } catch (\Gateway\One\DataContract\Report\CreditCardError $error) {
                $httpStatusCode = 400;
                $response = array("message" => $error->getMessage());
            } catch (\Gateway\One\DataContract\Report\ApiError $error) {
                $httpStatusCode = $error->errorCollection->ErrorItemCollection[0]->ErrorCode;
                $response = array("message" => $error->errorCollection->ErrorItemCollection[0]->Description);
            } catch (\Exception $ex) {
                $httpStatusCode = 500;
                $response = array("message" => $ex->getMessage());
            } finally {
                // Devolve resposta
//                http_response_code($httpStatusCode);
//                header('Content-Type: application/json');
                return $response;
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
                "hipercard" => "/^(606282\d{10}(\d{3})?)|(3841\d{15})$/"
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
            } else if (preg_match($re['elo'], $num)) {
                return 'Elo';
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
        public function delete_payment($order_key) {
            try {
// Define a url utilizada
                \Gateway\ApiClient::setBaseUrl($GLOBALS['sistem_config']->MUNDIPAGG_BASE_URL);
//    \Gateway\ApiClient::setBaseUrl($GLOBALS['sistem_config']->MUNDIPAGG_BASE_URL);
// Define a chave da loja
                \Gateway\ApiClient::setMerchantKey($GLOBALS['sistem_config']->SYSTEM_MERCHANT_KEY);

                // Cria objeto requisição
                $request = new \Gateway\One\DataContract\Request\CancelRequest();

                // Define dados da requisição
//                $request->setOrderKey("5f4ef87d-cf0d-4da1-91f6-5a394924c308");
                $request->setOrderKey($order_key);

                //Cria um objeto ApiClient
                $client = new \Gateway\ApiClient();

                // Faz a chamada para criação
                $response = $client->cancel($request);

                // Imprime resposta
                // print "<pre>";
                return json_encode(array('success' => $response->isSuccess(), 'data' => $response->getData()), JSON_PRETTY_PRINT);
                //print "</pre>";
            } catch (\Gateway\One\DataContract\Report\ApiError $error) {
                // Imprime json
                //print "<pre>";
                return json_encode($error, JSON_PRETTY_PRINT);
                //print "</pre>";
            } catch (Exception $ex) {
                // Imprime json
                //print "<pre>";
                return json_encode($ex, JSON_PRETTY_PRINT);
                //print "</pre>";
            }
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
        public function check_payment($order_key) {
            $result = NULL;
            if ($order_key) {
                $result = $this->queryOrder($order_key);
            }
            return $result;
        }
        
        public function get_paymment_data($order_key) {
            if ($order_key) {
                $result = $this->queryOrder($order_key);
                if (is_object($result) && $result->isSuccess())
                {
                     $data = $result->getData();
                    //var_dump($data);
                    $SaleDataCollection = $data->SaleDataCollection[0];
                    $LastSaledData = NULL;
                    // Get last client payment
                    $now = DateTime::createFromFormat('U', time());
                    foreach ($SaleDataCollection->CreditCardTransactionDataCollection as $SaleData) {
                        return  new DateTime($SaleData->CreateDate);
                    }                    
                }
            }
            return null;
        }
        
        public function get_last_paymment_data($order_key) {
            if ($order_key) {
                $result = $this->queryOrder($order_key);
                if (is_object($result) && $result->isSuccess())
                {
                     $data = $result->getData();
                    //var_dump($data);
                    $SaleDataCollection = $data->SaleDataCollection[0];
                    $LastSaledData = NULL;
                    // Get last client payment
                    $now = DateTime::createFromFormat('U', time());
                    foreach ($SaleDataCollection->CreditCardTransactionDataCollection as $SaleData) {
                        $SaleDataDate = new DateTime($SaleData->DueDate);
        //                $LastSaleDataDate = new DateTime($LastSaledData->DueDate);
                        //$last_payed_date = DateTime($LastSaledData->DueDate);
                        if ($SaleData->CapturedAmountInCents != NULL && ($LastSaledData == NULL || $SaleDataDate > new DateTime($LastSaledData->DueDate))) {
                            $LastSaledData = $SaleData;
                        }
                     }                    
                }
            }
            return null;
        }

        // end of member function update_payment

        function queryOrder($order_key) {
            try {
// Define a url utilizada
                \Gateway\ApiClient::setBaseUrl($GLOBALS['sistem_config']->MUNDIPAGG_BASE_URL);
//    \Gateway\ApiClient::setBaseUrl($GLOBALS['sistem_config']->MUNDIPAGG_BASE_URL);
// Define a chave da loja
                \Gateway\ApiClient::setMerchantKey($GLOBALS['sistem_config']->SYSTEM_MERCHANT_KEY);

//Cria um objeto ApiClient
                $client = new \Gateway\ApiClient();

// Faz a chamada para criação
                $response = $client->searchSaleByOrderKey($order_key);
                return $response;
//                $response = $client->searchSaleByOrderKey("e0c0954a-dbd5-4e79-b513-0769d89bb490");
// Imprime resposta
//                print "<pre>";
//                print json_encode(array('success' => $response->isSuccess(), 'data' => $response->getData()), JSON_PRETTY_PRINT);
//                print "</pre>";
            } catch (\Gateway\One\DataContract\Report\ApiError $error) {
// Imprime json
                print "<pre>";
                print json_encode($error, JSON_PRETTY_PRINT);
                print "</pre>";
            } catch (Exception $ex) {
// Imprime json
                print "<pre>";
                print json_encode($ex, JSON_PRETTY_PRINT);
                print "</pre>";
            }
            return NULL;
        }

        function retry_payment($order_key, $request_key = NULL) {
            try {
// Define a url utilizada
                \Gateway\ApiClient::setBaseUrl($GLOBALS['sistem_config']->MUNDIPAGG_BASE_URL);
//    \Gateway\ApiClient::setBaseUrl($GLOBALS['sistem_config']->MUNDIPAGG_BASE_URL);
// Define a chave da loja
                \Gateway\ApiClient::setMerchantKey($GLOBALS['sistem_config']->SYSTEM_MERCHANT_KEY);

                // Create request object
                $request = new \Gateway\One\DataContract\Request\RetryRequest();

                // Define all request data
                $request->setOrderKey($order_key);
                $request->setRequestKey($request_key);
//                var_dump($order_key);
                // Create new ApiClient object
                $client = new \Gateway\ApiClient();

                // Make the call
                $response = $client->Retry($request);
            } catch (\Gateway\One\DataContract\Report\ApiError $error) {
                $httpStatusCode = $error->errorCollection->ErrorItemCollection[0]->ErrorCode;
                $response = array("message" => $error->errorCollection->ErrorItemCollection[0]->Description);
            } catch (Exception $ex) {
                $httpStatusCode = 500;
                $response = array("message" => "Ocorreu um erro inesperado.");
            } finally {
                return $response;
            }
        }

        function retry_payment_recurrency($order_key, $transaction_key, $cvc = NULL) {
            try {
// Define a url utilizada
                \Gateway\ApiClient::setBaseUrl($GLOBALS['sistem_config']->MUNDIPAGG_BASE_URL);
//    \Gateway\ApiClient::setBaseUrl($GLOBALS['sistem_config']->MUNDIPAGG_BASE_URL);
// Define a chave da loja
                \Gateway\ApiClient::setMerchantKey($GLOBALS['sistem_config']->SYSTEM_MERCHANT_KEY);

                // Create request object
                $request = new \Gateway\One\DataContract\Request\RetryRequest();

                // Create request object
                $request = new \Gateway\One\DataContract\Request\RetryRequest();

                // Define all request data
                $request->setOrderKey($order_key);
                $creditCardTransaction = new \Gateway\One\DataContract\Request\RetryRequestData\RetrySaleCreditCardTransaction();
                if ($cvc) {
                    $creditCardTransaction->setSecurityCode($cvc);
                }
                $creditCardTransaction->setTransactionKey($transaction_key);
                print_r($creditCardTransaction->getData());

                $request->addRetrySaleCreditCardTransactionCollection($creditCardTransaction);

                // Create new ApiClient object
                $client = new \Gateway\ApiClient();

                // Make the call
                $response = $client->Retry($request);
            } catch (\Gateway\One\DataContract\Report\ApiError $error) {
                var_dump($error);
                $httpStatusCode = $error->errorCollection->ErrorItemCollection[0]->ErrorCode;
                $response = array("message" => $error->errorCollection->ErrorItemCollection[0]->Description);
            } catch (Exception $ex) {
                $httpStatusCode = 500;
                $response = array("message" => "Ocorreu um erro inesperado.");
            } finally {
                return $response;
            }
        }

        /**
         * Check whether the $order_key have any payment done
         * @param type $order_key
         * @return boolean
         */
        public function check_client_order_paied($order_key) {
            $result = $this->check_payment($order_key);
            if (is_object($result) && $result->isSuccess()) {
                $data = $result->getData();
                //var_dump($data);
                $SaleDataCollection = $data->SaleDataCollection[0];
                foreach ($SaleDataCollection->CreditCardTransactionDataCollection as $SaleData) {
                    // Get last client payment
                    //$SaleData = $SaleDataCollection->CreditCardTransactionDataCollection[0];
                    $SaleDataDate = new \DateTime($SaleData->DueDate);
                    if ($SaleData->CapturedAmountInCents != NULL) {
                        return TRUE;
                    }
                    //var_dump($SaleData);
                }
            }
            return FALSE;
        }
        
        public function is_ip_hacker(){
            $IP_hackers= array(
                '191.176.169.242', '138.0.85.75', '138.0.85.95', '177.235.130.16', '191.176.171.14', '200.149.30.108', '177.235.130.212', '66.85.185.69',
                '177.235.131.104', '189.92.238.28', '168.228.88.10', '201.86.36.209', '177.37.205.210', '187.66.56.220', '201.34.223.8', '187.19.167.94',
                '138.0.21.188', '168.228.84.1', '138.36.2.18', '201.35.210.135', '189.71.42.124', '138.121.232.245', '151.64.57.146', '191.17.52.46', '189.59.112.125',
                '177.33.7.122', '189.5.107.81', '186.214.241.146', '177.207.99.29', '170.246.230.138', '201.33.40.202', '191.53.19.210', '179.212.90.46', '177.79.7.202',
                '189.111.72.193', '189.76.237.61', '177.189.149.249', '179.223.247.183', '177.35.49.40', '138.94.52.120', '177.104.118.22', '191.176.171.14', '189.40.89.248',
                '189.89.31.89', '177.13.225.38',  '186.213.69.159', '177.95.126.121', '189.26.218.161', '177.193.204.10', '186.194.46.21', '177.53.237.217', '138.219.200.136',
                '177.126.106.103', '179.199.73.251', '191.176.171.14', '179.187.103.14', '177.235.130.16', '177.235.130.16', '177.235.130.16', '177.47.27.207'
                );
            if(in_array($_SERVER['REMOTE_ADDR'],$IP_hackers)){
                die('Error IP: Sua solicitação foi negada. Por favor, contate nosso atendimento');
            }
        }
        
        public function check_recurrency_mundipagg_credit_card($datas, $cnt) {
            $this->is_ip_hacker();
            $payment_data['credit_card_number'] = $datas['credit_card_number'];
            $payment_data['credit_card_name'] = $datas['credit_card_name'];
            $payment_data['credit_card_exp_month'] = $datas['credit_card_exp_month'];
            $payment_data['credit_card_exp_year'] = $datas['credit_card_exp_year'];
            $payment_data['credit_card_cvc'] = $datas['credit_card_cvc'];
            $payment_data['amount_in_cents'] = $datas['amount_in_cents'];
            $payment_data['pay_day'] = $datas['pay_day'];
            $bandeira = $this->detectCardType($payment_data['credit_card_number']);

            if ($bandeira) {
                if ($bandeira == "Visa" || $bandeira == "Mastercard") {
                    //5 Cielo -> 1.5 | 32 -> eRede | 20 -> Stone | 42 -> Cielo 3.0 | 0 -> Auto;        
                    $response = $this->create_recurrency_payment($payment_data, $cnt, 20);

                    if (is_object($response) && $response->isSuccess()) {
                        return $response;
                    } else {
                        $response = $this->create_recurrency_payment($payment_data, $cnt, 42);
                    }
                }
                else if ($bandeira == "Hipercard") {
                    $response = $this->create_recurrency_payment($payment_data, $cnt, 20);
                }
                else {
                    $response = $this->create_recurrency_payment($payment_data, $cnt, 42);
                }
            }
            else {
                $response = array("message" => "Confira seu número de cartão e se está certo entre em contato com o atendimento.");
            }

            return $response;
        }     

        // end of Payment
    }

}

?>