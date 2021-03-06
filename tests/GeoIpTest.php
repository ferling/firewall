<?php

namespace PragmaRX\Firewall\Tests;

use PragmaRX\Firewall\Vendor\Laravel\Facade as Firewall;

class GeoIpTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        Firewall::updateGeoIp();
    }

    public function test_get_country()
    {
        $this->assertEquals('us', Firewall::getCountryFromIp('8.8.8.8'));

        $this->assertEquals('br', Firewall::getCountryFromIp('200.222.0.24'));
    }

    public function test_block_per_country()
    {
        Firewall::blacklist('country:us');

        $this->assertTrue(Firewall::isBlacklisted('8.8.8.8'));

        $this->assertFalse(Firewall::isWhitelisted('8.8.8.8'));
    }

    public function test_make_country()
    {
        $this->assertEquals('country:br', Firewall::makeCountryFromString('br'));

        $this->assertEquals('country:br', Firewall::makeCountryFromString('country:br'));

        $this->assertEquals('country:br', Firewall::makeCountryFromString('200.222.0.21'));
    }

    public function test_country_ip_listing()
    {
        Firewall::blacklist('8.8.8.7');
        Firewall::blacklist('8.8.8.8');
        Firewall::blacklist('8.8.8.9');

        Firewall::blacklist('200.222.0.21');
        Firewall::blacklist('200.222.0.22');

        $this->assertCount(2, Firewall::allByCountry('br'));

        $this->assertCount(3, Firewall::allByCountry('us'));
    }

    public function test_country_is_valid()
    {
        $this->assertTrue(Firewall::validCountry('country:us'));

        $this->assertTrue(Firewall::validCountry('country:br'));

        $this->assertFalse(Firewall::validCountry('country:xx'));
    }

    public function test_country_cidr()
    {
        Firewall::blacklist('country:us');

        $this->assertTrue(Firewall::isBlacklisted('8.8.8.0/24'));
    }
}
