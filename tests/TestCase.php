<?php

namespace Tests;

use Google\Service\Webmasters;
use Illuminate\Foundation\Application;
use Revolution\Google\Client\Facades\Google;
use Revolution\Google\Client\Providers\GoogleServiceProvider;
use Revolution\Google\SearchConsole\Facades\SearchConsole;
use Revolution\Google\SearchConsole\Providers\SearchConsoleServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            SearchConsoleServiceProvider::class,
            GoogleServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Photos' => SearchConsole::class,
            'Google' => Google::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Configure Google service account authentication for integration tests
        $serviceAccountJson = env('GOOGLE_SERVICE_ACCOUNT_JSON_LOCATION');

        if ($serviceAccountJson) {
            // Check if it's a JSON string or file path
            if (is_string($serviceAccountJson) && (str_starts_with($serviceAccountJson, '{') || str_starts_with($serviceAccountJson, '['))) {
                // It's a JSON string, decode it
                $credentials = json_decode($serviceAccountJson, true);
                if (json_last_error() === JSON_ERROR_NONE && isset($credentials['client_email'])) {
                    // Valid service account JSON structure
                    $app['config']->set('google.service.enable', true);
                    $app['config']->set('google.service.file', $credentials);

                    // Set required scopes for Search Console
                    $app['config']->set('google.scopes', [Webmasters::WEBMASTERS]);

                    // Set dummy OAuth values to prevent errors (not used with service account)
                    $app['config']->set('google.client_id', 'dummy');
                    $app['config']->set('google.client_secret', 'dummy');
                    $app['config']->set('google.redirect_uri', 'http://localhost');
                }
            } else {
                // Treat as file path - check if file exists and has valid content
                if (file_exists($serviceAccountJson)) {
                    $fileContent = file_get_contents($serviceAccountJson);
                    $credentials = json_decode($fileContent, true);
                    if (json_last_error() === JSON_ERROR_NONE && isset($credentials['client_email'])) {
                        $app['config']->set('google.service.enable', true);
                        $app['config']->set('google.service.file', $serviceAccountJson);

                        // Set required scopes for Search Console
                        $app['config']->set('google.scopes', [Webmasters::WEBMASTERS]);

                        // Set dummy OAuth values to prevent errors (not used with service account)
                        $app['config']->set('google.client_id', 'dummy');
                        $app['config']->set('google.client_secret', 'dummy');
                        $app['config']->set('google.redirect_uri', 'http://localhost');
                    }
                }
            }
        }
    }
}
