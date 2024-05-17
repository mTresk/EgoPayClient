<?php

namespace mTresk\EgoPayClient;

use SoapVar;

class RegisterOnline
{
    public OrderID $order;

    public Amount $cost;

    public CustomerInfo $customer;

    public OrderInfo $description;

    public SoapVar $postdata;
}