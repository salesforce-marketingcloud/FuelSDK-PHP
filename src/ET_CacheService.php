<?php

namespace FuelSdk;

class ET_CacheService
{
    private $_identifier;
    private $_cacheMinutes = 10;
    private static $cachedSoapUrls;

    public function __construct($clientId, $clientSecret)
    {
        $this->_identifier = $clientId . "-" . $clientSecret;
    }

    public function get()
    {
        $now = time();
        $data = ET_CacheService::$cachedSoapUrls[$this->_identifier];
        if (!$data || !$data->expires || $data->expires < $now) {
            // remove expired data from the array
            unset(ET_CacheService::$cachedSoapUrls[$this->_identifier]);
            return null;
        } else {
            return $data;
        }
    }

    public function write($url)
    {
        $expires = time() + $this->_cacheMinutes * 60;
        $data = new \stdClass();
        $data->expires = $expires;
        $data->url = $url;
        ET_CacheService::$cachedSoapUrls[$this->_identifier] = $data;
    }
}