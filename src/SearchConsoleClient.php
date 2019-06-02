<?php

namespace Revolution\Google\SearchConsole;

use Google_Service_Webmasters;
use PulkitJalan\Google\Client;
use Illuminate\Container\Container;
use Illuminate\Support\Traits\Macroable;
use Revolution\Google\SearchConsole\Contracts\Factory;

class SearchConsoleClient implements Factory
{
    use Concerns\SearchAnalytics;
    use Concerns\Sites;
    use Macroable;

    /**
     * @var Google_Service_Webmasters
     */
    protected $service;

    /**
     * @param  Google_Service_Webmasters|\Google_Service  $service
     * @return $this
     */
    public function setService($service)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * @return Google_Service_Webmasters
     */
    public function getService(): Google_Service_Webmasters
    {
        return $this->service;
    }

    /**
     * set access_token and set new service
     *
     * @param  string|array  $token
     * @return $this
     * @throws \Exception
     */
    public function setAccessToken($token)
    {
        /**
         * @var Client $google
         */
        $google = Container::getInstance()->make(Client::class);

        $google->getCache()->clear();

        $google->setAccessToken($token);

        if (isset($token['refresh_token']) and $google->isAccessTokenExpired()) {
            $google->fetchAccessTokenWithRefreshToken();
        }

        return $this->setService($google->make('Webmasters'));
    }

    /**
     * @return array
     */
    public function getAccessToken()
    {
        return $this->getService()->getClient()->getAccessToken();
    }
}
