<?php
/*
 * MundiAPILib
 *
 * This file was automatically generated by APIMATIC v2.0 ( https://apimatic.io ).
 */

namespace MundiAPILib\Models;

use JsonSerializable;

/**
 * Response object for getting an order item
 */
class GetOrderItemResponse implements JsonSerializable
{
    /**
     * @todo Write general description for this property
     * @required
     * @var integer $amount public property
     */
    public $amount;

    /**
     * @todo Write general description for this property
     * @required
     * @var string $description public property
     */
    public $description;

    /**
     * @todo Write general description for this property
     * @required
     * @var integer $quantity public property
     */
    public $quantity;

    /**
     * Seller data
     * @maps GetSellerResponse
     * @var GetSellerResponse|null $getSellerResponse public property
     */
    public $getSellerResponse;

    /**
     * Constructor to set initial or default values of member properties
     * @param integer           $amount            Initialization value for $this->amount
     * @param string            $description       Initialization value for $this->description
     * @param integer           $quantity          Initialization value for $this->quantity
     * @param GetSellerResponse $getSellerResponse Initialization value for $this->getSellerResponse
     */
    public function __construct()
    {
        if (4 == func_num_args()) {
            $this->amount            = func_get_arg(0);
            $this->description       = func_get_arg(1);
            $this->quantity          = func_get_arg(2);
            $this->getSellerResponse = func_get_arg(3);
        }
    }


    /**
     * Encode this object to JSON
     */
    public function jsonSerialize()
    {
        $json = array();
        $json['amount']            = $this->amount;
        $json['description']       = $this->description;
        $json['quantity']          = $this->quantity;
        $json['GetSellerResponse'] = $this->getSellerResponse;

        return $json;
    }
}
