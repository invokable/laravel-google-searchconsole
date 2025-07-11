<?php

namespace Revolution\Google\SearchConsole\Concerns;

use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Revolution\Google\SearchConsole\Contracts\Factory;
use Revolution\Google\SearchConsole\Traits\WithSearchConsole;

/**
 * We recommend using {@link WithSearchConsole} for consistency with other related Google API packages, but will keep it for compatibility.
 *
 * @deprecated Use {@link WithSearchConsole} instead.
 */
trait SearchConsole
{
    /**
     * @throws BindingResolutionException
     */
    public function searchconsole(): Factory
    {
        $token = $this->tokenForSearchConsole();

        return Container::getInstance()->make(Factory::class)->setAccessToken($token);
    }

    /**
     * Get the Access Token.
     *
     * @deprecated Use {@link WithSearchConsole} instead.
     */
    abstract protected function tokenForSearchConsole(): array|string;
}
