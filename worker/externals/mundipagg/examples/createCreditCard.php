<?php

require_once(dirname(__FILE__) . '/../init.php');
require_once(dirname(__FILE__) . '/../../../class/system_config.php');

try {
    // Define a url utilizada
    \Gateway\ApiClient::setBaseUrl("https://sandbox.mundipaggone.com");
//    \Gateway\ApiClient::setBaseUrl("https://api.mundipaggone.com");
//    \Gateway\ApiClient::setBaseUrl("https://checkout.mundipagg.com");
//    \Gateway\ApiClient::setBaseUrl("https://api-dashboard.mundipagg.com");
//    \Gateway\ApiClient::setBaseUrl("https://callcenter.mundipagg.com");
//    \Gateway\ApiClient::setBaseUrl("https://www.mundipagg.com");
//    \Gateway\ApiClient::setBaseUrl("https://209.134.53.73/");

    \Gateway\ApiClient::
    setEnvironment(\Gateway\One\DataContract\Enum\ApiEnvironmentEnum::PRODUCTION);

//    \MundiPagg\ApiClient::
//    setMerchantKey("54409272-781D-4470-BFA3-C58A5A005B49");

    // Define a chave da loja
//    \Gateway\ApiClient::setMerchantKey("85328786-8BA6-420F-9948-5352F5A183EB");
    \Gateway\ApiClient::setMerchantKey(\leads\cls\system_config::SYSTEM_MERCHANT_KEY);

    // Cria objeto requisição
    $request = new \Gateway\One\DataContract\Request\CreateInstantBuyDataRequest();

    $request
            ->setCreditCardBrand(\Gateway\One\DataContract\Enum\CreditCardBrandEnum::MASTERCARD)
            ->setBuyerKey("6bdce42e-a018-41e2-aa1f-c779b832ad64")
            ->setCreditCardNumber("4111111111111111")
            ->setExpMonth(10)
            ->setExpYear(22)
            ->setHolderName("LUKE SKYWALKER")
            ->setSecurityCode("999")
            ->setIsOneDollarAuthEnabled(false)
            ->getBillingAddress()
            ->setStreet("Mos Eisley Cantina")
            ->setNumber("123")
            ->setComplement("")
            ->setDistrict("Mos Eisley")
            ->setCity("Tatooine")
            ->setState("RJ")
            ->setZipCode("20001000")
            ->setCountry(\Gateway\One\DataContract\Enum\CountryEnum::BRAZIL);

    //Cria um objeto ApiClient
    $client = new Gateway\ApiClient();

    // Faz a chamada para criação
    $response = $client->createCreditCard($request);


    // Imprime resposta
    print "<pre>";
    print json_encode($response->getData(), JSON_PRETTY_PRINT);
    print "</pre>";
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
