<?php

namespace PrCy\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class YandexMoneyAccount implements ResourceOwnerInterface
{
    /**
     * @var array
     */
    protected $response;

    const STATUS_ANONYMOUS  = 'anonymous';
    const STATUS_NAMED      = 'named';
    const STATUS_IDENTIFIED = 'identified';

    const TYPE_PERSONAL     = 'personal';
    const TYPE_PROFESSIONAL = 'professional';

    const CARD_TYPE_VISA            = 'VISA';
    const CARD_TYPE_MASTERCARD      = 'MasterCard';
    const CARD_TYPE_AMERICANEXPRESS = 'AmericanExpress';
    const CARD_TYPE_JCB             = 'JCB';

    /**
     * @param array $response
     */
    public function __construct(array $response)
    {
        $this->response = $response;
    }

    public function getId()
    {
        return $this->response['account'];
    }

    /**
     * Get account balance.
     *
     * @return string
     */
    public function getBalance()
    {
        return $this->response['balance'];
    }

    /**
     * Get account currency.
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->response['currency'];
    }

    /**
     * Get account status.
     *
     * @return string
     */
    public function getAccountStatus()
    {
        return $this->response['account_status'];
    }

    /**
     * Get account type.
     *
     * @return string
     */
    public function getAccountType()
    {
        return $this->response['account_type'];
    }

    /**
     * Get account avatar.
     *
     * @return object
     */
    public function getAvatar()
    {
        return $this->response['avatar'];
    }

    /**
     * Get account balance details.
     *
     * @return object
     */
    public function getBalanceDetails()
    {
        return isset($this->response['balance_details']) ? $this->response['balance_details'] : null;
    }

    /**
     * Get account cards linked.
     *
     * @return array
     */
    public function getCardsLinked()
    {
        return isset($this->response['cards_linked']) ? $this->response['cards_linked'] : [];
    }


    /**
     * Get account data as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->response;
    }
}
