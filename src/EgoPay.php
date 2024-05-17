<?php

namespace mTresk\EgoPayClient;

use Exception;
use SoapClient;
use SoapFault;
use SoapVar;

class EgoPay
{
    private SoapClient $client;
    private string $shopId;
    private string $urlOk;
    private string $urlFault;

    const DEFAULT_CURRENCY = 'RUB';
    const DEFAULT_LANGUAGE = 'ru';
    const DEFAULT_CARD_TYPE = 'VI';
    const DEFAULT_DESCRIPTION = 'Оплата услуги или товара';

    /**
     * @throws SoapFault
     */
    public function __construct($shopId, $login, $password, $location, $uri, $urlOk, $urlFault)
    {
        $this->client = new Client(null, [
            'location' => $location,
            'uri' => $uri,
            'login' => $login,
            'password' => $password,
            'trace' => 1,
            'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
            'connection_timeout' => 12
        ]);

        $this->shopId = $shopId;
        $this->urlOk = $urlOk;
        $this->urlFault = $urlFault;
    }

    /**
     * @throws SoapFault
     * @throws Exception
     */
    public function register($payment): string
    {
        $request = $this->buildRequest($payment);

        $info = $this->client->registerOnline($request);

        if (!empty($info->redirect_url) && !empty($info->session)) {
            return $info->redirect_url . '?session=' . $info->session;
        } else {
            throw new Exception('Invalid response received from EgoPay service.');
        }
    }

    /**
     * @throws SoapFault
     */
    public function getStatus(string $orderNumber)
    {
        $order = new OrderID();
        $order->shop_id = $this->shopId;
        $order->number = $orderNumber;

        $status = new GetStatus();
        $status->order = $order;

        return $this->client->getStatus($status);
    }

    private function buildRequest($payment): RegisterOnline
    {
        $orderAmount = $payment['amount'];

        $itemTypeName = 'service';
        $itemHost = '';
        $itemPNR = $payment['order_number'];

        $order = new OrderID();
        $order->shop_id = $this->shopId;
        $order->number = $payment['order_number'];

        $cost = new Amount();
        $cost->amount = $orderAmount;
        $cost->currency = self::DEFAULT_CURRENCY;

        $customer = new CustomerInfo();
        $customer->id = $payment['customer_id'] ?? '';
        $customer->name = $payment['customer_name'] ?? '';
        $customer->phone = $payment['customer_phone'] ?? '';
        $customer->email = $payment['customer_email'] ?? '';

        $description = new OrderInfo();
        $description->paytype = 'card';

        $itemCost = new Amount();
        $itemCost->amount = $orderAmount;
        $itemCost->currency = self::DEFAULT_CURRENCY;

        $item = new OrderItem();
        $item->typename = $itemTypeName;
        $item->number = $itemPNR;
        $item->amount = $itemCost;
        $item->host = $itemHost;
        $item->descr = self::DEFAULT_DESCRIPTION;

        $items = new SoapVar([$item], SOAP_ENC_OBJECT);
        $description->items = $items;

        $language = new PostEntry();
        $language->name = 'Language';
        $language->value = self::DEFAULT_LANGUAGE;

        $cardtype = new PostEntry();
        $cardtype->name = 'ChosenCardType';
        $cardtype->value = self::DEFAULT_CARD_TYPE;

        $returnUrlOk = new PostEntry();
        $returnUrlOk->name = 'ReturnURLOk';
        $returnUrlOk->value = $this->urlOk;

        $returnUrlFault = new PostEntry();
        $returnUrlFault->name = 'ReturnURLFault';
        $returnUrlFault->value = $this->urlFault;

        $request = new RegisterOnline();
        $request->order = $order;
        $request->cost = $cost;
        $request->customer = $customer;
        $request->description = $description;

        $postdata = new SoapVar([$language, $cardtype, $returnUrlOk, $returnUrlFault], SOAP_ENC_OBJECT);

        $request->postdata = $postdata;

        return $request;
    }
}
