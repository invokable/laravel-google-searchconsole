<?php

namespace Revolution\Google\SearchConsole\Query;

use Google\Service\SearchConsole\SearchAnalyticsQueryRequest;
use Revolution\Google\SearchConsole\Contracts\Query;

abstract class AbstractQuery extends SearchAnalyticsQueryRequest implements Query
{
    /**
     * Google\Model gapiInit().
     */
    protected function gapiInit(): void
    {
        $this->init();
    }

    abstract public function init(): void;
}
