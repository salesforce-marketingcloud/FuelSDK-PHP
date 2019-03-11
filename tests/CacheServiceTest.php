<?php
namespace FuelSdk {

    /**
     * Overwrites the time() function in order to be able to control it from the tests
     * Returns the Test\CacheServiceTest::$now value if set or the default time() value, otherwise.
     * @return int
     */
    function time()
    {
        return Test\CacheServiceTest::$now ?: \time();
    }
}

namespace FuelSdk\Test {

    use FuelSdk\ET_CacheService;

     /**
     * @covers \FuelSdk\ET_CacheService
     */
    final class CacheServiceTest extends \PHPUnit_Framework_TestCase
    {
        public static $now;
        private $currentTime;
        const CACHE_TIME_IN_SECONDS = 10 * 60;
        const CLIENT_ID_1 = 'id1';
        const CLIENT_SECRET_1 = 'secret1';
        const SOAP_URL_1 = 'http://soap1.asmx';
        const CLIENT_ID_2 = 'id2';
        const CLIENT_SECRET_2 = 'secret2';
        const SOAP_URL_2 = 'http://soap2.asmx';

        public function setup()
        {
            $this->currentTime = time();
            CacheServiceTest::$now = $this->currentTime;
        }

        public function testWhenNewInstanceIsCreatedGetReturnsNull()
        {
            // Arrange
            $sut = new ET_CacheService(self::CLIENT_ID_1, self::CLIENT_SECRET_1);
            // Act
            $cachedValue = $sut->get();
            // Assert
            $this->assertNull($cachedValue);
        }

        public function testAnUrlIsCachedFor10Minutes()
        {
            // Arrange
            $sut = new ET_CacheService(self::CLIENT_ID_1, self::CLIENT_SECRET_1);
            $sut->write(self::SOAP_URL_1);
            // Act
            $cachedValue = $sut->get();
            // Assert
            $this->assertEquals(self::SOAP_URL_1, $cachedValue->url);
            $this->assertEquals($this->currentTime + self::CACHE_TIME_IN_SECONDS, $cachedValue->expires);
        }

        public function testGettingACachedValueInTheCacheWindowWillReturnTheCachedValueCorrectly()
        {
            // Arrange
            $sut = new ET_CacheService(self::CLIENT_ID_1, self::CLIENT_SECRET_1);
            $sut->write(self::SOAP_URL_1);
            // Act
            // simulate time passes, but we are still in the cache window
            CacheServiceTest::$now += 60;
            $cachedValue = $sut->get();
            // Assert
            $this->assertEquals(self::SOAP_URL_1, $cachedValue->url);
            $this->assertEquals($this->currentTime + self::CACHE_TIME_IN_SECONDS, $cachedValue->expires);
        }

        public function testANewInstanceWithTheSameClientIdAndSecretWillGetThePreviouslyCachedValue()
        {
            // Arrange
            $sut = new ET_CacheService(self::CLIENT_ID_1, self::CLIENT_SECRET_1);

            // Act
            $sut->write(self::SOAP_URL_1);
            $previouslyCachedValue = $sut->get();
            // simulate a new instance with the same client id and secret is created after 10 seconds
            CacheServiceTest::$now += 10;
            $sut2 = new ET_CacheService(self::CLIENT_ID_1, self::CLIENT_SECRET_1);
            $newInstanceCachedValue = $sut2->get();

            // Assert
            $this->assertEquals($previouslyCachedValue->url, $newInstanceCachedValue->url);
            $this->assertEquals($previouslyCachedValue->expires, $newInstanceCachedValue->expires);
        }

        public function testGettingAnExpiredCachedValueReturnsNull()
        {
            // Arrange
            $sut = new ET_CacheService(self::CLIENT_ID_1, self::CLIENT_SECRET_1);
            $sut->write(self::SOAP_URL_1);
            // Act
            // simulate cache expires
            CacheServiceTest::$now += self::CACHE_TIME_IN_SECONDS + 1;
            $cachedValue = $sut->get();
            // Assert
            $this->assertNull($cachedValue);
        }

        public function testSettingAnExpiredCachedValueReturnsTheCorrectSetValue()
        {
            // Arrange
            $sut = new ET_CacheService(self::CLIENT_ID_1, self::CLIENT_SECRET_1);
            $sut->write(self::SOAP_URL_1);

            // Act
            // simulate cache expires
            CacheServiceTest::$now += self::CACHE_TIME_IN_SECONDS + 1;
            $this->currentTime = CacheServiceTest::$now;

            $sut->write(self::SOAP_URL_1);
            $cachedValue = $sut->get();

            // Assert
            $this->assertEquals(self::SOAP_URL_1, $cachedValue->url);
            $this->assertEquals($this->currentTime + self::CACHE_TIME_IN_SECONDS, $cachedValue->expires);
        }

        public function testMultipleETCacheServiceInstancesWillSetTheirCachedDataCorrectly()
        {
            // Arrange
            $sut1 = new ET_CacheService(self::CLIENT_ID_1, self::CLIENT_SECRET_1);
            $sut1->write(self::SOAP_URL_1);
            // simulate a new instance is created after 10 seconds
            CacheServiceTest::$now += 10;
            $sut2 = new ET_CacheService(self::CLIENT_ID_2, self::CLIENT_SECRET_2);
            $sut2->write(self::SOAP_URL_2);
            // Act
            $cachedValue1 = $sut1->get();
            $cachedValue2 = $sut2->get();
            // Assert
            $this->assertEquals(self::SOAP_URL_1, $cachedValue1->url);
            $this->assertEquals($this->currentTime + self::CACHE_TIME_IN_SECONDS, $cachedValue1->expires);
            $this->assertEquals(self::SOAP_URL_2, $cachedValue2->url);
            $this->assertEquals(CacheServiceTest::$now + self::CACHE_TIME_IN_SECONDS, $cachedValue2->expires);
        }

        public function tearDown()
        {
            CacheServiceTest::$now = null;
        }
    }
}
