<?php

namespace Tests\Feature;

use Google\Service\SearchConsole as SearchConsoleService;
use Mockery as m;
use Revolution\Google\Client\GoogleApiClient;
use Revolution\Google\SearchConsole\Facades\SearchConsole;
use Revolution\Google\SearchConsole\SearchConsoleClient;
use Tests\TestCase;

class SearchTest extends TestCase
{
    protected GoogleApiClient $google;

    protected function setUp(): void
    {
        parent::setUp();

        $this->google = m::mock(GoogleApiClient::class);
        app()->instance(GoogleApiClient::class, $this->google);
    }

    protected function tearDown(): void
    {
        m::close();

        parent::tearDown();
    }

    public function test_instance()
    {
        $sc = new SearchConsoleClient;

        $this->assertInstanceOf(SearchConsoleClient::class, $sc);
    }

    public function test_service()
    {
        $this->google->shouldReceive('make')->once()->andReturns(m::mock(SearchConsoleService::class));

        SearchConsole::setService($this->google->make('SearchConsole'));

        $this->assertInstanceOf(SearchConsoleService::class, SearchConsole::getService());
    }

    public function test_set_access_token()
    {
        $this->google->shouldReceive('getCache->clear')->once();
        $this->google->shouldReceive('setAccessToken')->once();
        $this->google->shouldReceive('isAccessTokenExpired')->once()->andReturns(true);
        $this->google->shouldReceive('fetchAccessTokenWithRefreshToken')->once();
        $this->google->shouldReceive('make')->once()->andReturns(m::mock(SearchConsoleService::class));

        $sc = SearchConsole::setAccessToken([
            'access_token' => 'test',
            'refresh_token' => 'test',
        ]);

        $this->assertInstanceOf(SearchConsoleService::class, $sc->getService());
    }

    public function test_get_access_token()
    {
        $sc = m::mock(SearchConsoleClient::class)->makePartial();
        $sc->shouldReceive('getService->getClient->getAccessToken')->andReturn(['token' => 'test']);

        $token = $sc->getAccessToken();

        $this->assertSame(['token' => 'test'], $token);
    }
}
