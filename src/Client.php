<?php

namespace mTresk\EgoPayClient;

use SoapClient;
use SoapFault;
use SoapParam;

class Client extends SoapClient
{
    /**
     * @throws SoapFault
     */
    public function registerOnline($mixed = null)
    {
        return $this->__soapCall("register_online", $this->makeSoapParams($mixed));
    }

    /**
     * @throws SoapFault
     */
    public function getStatus($mixed = null)
    {
        return $this->__soapCall("get_status", $this->makeSoapParams($mixed));
    }
    
    protected function makeSoapParams($mixed): array
    {
        $args = [];
        foreach ($mixed as $key => $val) {
            $args[] = new SoapParam($val, $key);
        }
        return $args;
    }
}