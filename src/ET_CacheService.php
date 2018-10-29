<?php

namespace FuelSdk;


class ET_CacheService
{

    private $_identifier;
    private $_filePath = '../.cache';
    private $_cacheMinutes = 10;

    public function __construct($clientId, $clientSecret)
    {
        $this->_identifier = $clientId . "-" . $clientSecret;
    }

    public function get()
    {
        $cache = $this->_getOrCreateFile();
        $data = $cache->{$this->_identifier};
        $now = time();
        if (!$data || !$data->expires || $data->expires < $now) {
            return null;
        } else {
            return $data;
        }
    }

    public function write($url)
    {
        $expires = time() + $this->_cacheMinutes * 60;
        $cache = $this->_getOrCreateFile();
        $data = new \stdClass();
        $data->expires = $expires;
        $data->url = $url;
        $cache->{$this->_identifier} = $data;
        $this->_writeFile($cache);
    }

    public function clear()
    {
        $cache = $this->_getOrCreateFile();
        unset($cache->{$this->_identifier});
        $this->_writeFile($cache);
    }

    private function _getOrCreateFile()
    {
        if (file_exists($this->_filePath)) {
            return $this->_readFile();
        } else {
            $data = new \stdClass();
            $this->_writeFile($data);
            return $data;
        }
    }

    private function _writeFile($contents)
    {
        file_put_contents($this->_filePath, json_encode($contents));
    }

    private function _readFile()
    {
        return json_decode(file_get_contents($this->_filePath));
    }

}