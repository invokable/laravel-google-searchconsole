<?php

namespace Revolution\Google\SearchConsole\Facades;

use Google\Service\Webmasters\SearchAnalyticsQueryRequest;
use Illuminate\Support\Facades\Facade;
use Revolution\Google\SearchConsole\Contracts\Factory;
use Revolution\Google\SearchConsole\Contracts\Query;
use Revolution\Google\SearchConsole\SearchConsoleClient;

/**
 * @method static Factory setAccessToken(array|string $token)
 * @method static object query(string $url, Query|SearchAnalyticsQueryRequest $query)
 * @method static object listSites(array $optParams = [])
 *
 * @see SearchConsoleClient
 */
class SearchConsole extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return Factory::class;
    }
}
