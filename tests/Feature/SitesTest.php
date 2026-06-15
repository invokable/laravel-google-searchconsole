<?php

namespace Tests\Feature;

use Google\Service\SearchConsole;
use Google\Service\SearchConsole\Resource\Sites;
use Mockery as m;
use Revolution\Google\SearchConsole\SearchConsoleClient;
use Tests\TestCase;

class SitesTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();

        parent::tearDown();
    }

    public function test_list_sites()
    {
        $sc = m::mock(SearchConsoleClient::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $sc->shouldReceive('serviceSites->listSites->toSimpleObject')->andReturn((object) ['test' => 'test']);

        $sites = $sc->listSites([]);

        $this->assertObjectHasProperty('test', $sites);
    }

    public function test_service_sites()
    {
        $service = m::mock(SearchConsole::class);
        $service->sites = m::mock(Sites::class);

        $photos = m::mock(SearchConsoleClient::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $sites = $photos->setService($service)->serviceSites();

        $this->assertInstanceOf(Sites::class, $sites);
    }
}
