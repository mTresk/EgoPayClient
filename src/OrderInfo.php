<?php

namespace mTresk\EgoPayClient;

use SoapVar;

class OrderInfo
{
    public SoapVar $items;

    public string $paytype;
}