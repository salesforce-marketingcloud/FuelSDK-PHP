<?php

use \ExactTarget\ET_SDKUtils;
use \PHPUnit_Framework_TestCase;

class AutoloadTest extends PHPUnit_Framework_TestCase
{
    public function testPsr0()
    {
        $this->assertTrue(
            strpos(ET_SDKUtils::getSDKVersion(), 'FuelSDK-PHP-v') !== false
        );
    }
}
