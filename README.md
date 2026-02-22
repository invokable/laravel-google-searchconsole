# Google SearchConsole API for Laravel

[![Maintainability](https://qlty.sh/badges/9fb434a4-1cd3-4203-af92-efca4ca98a81/maintainability.svg)](https://qlty.sh/gh/invokable/projects/laravel-google-searchconsole)
[![Code Coverage](https://qlty.sh/badges/9fb434a4-1cd3-4203-af92-efca4ca98a81/test_coverage.svg)](https://qlty.sh/gh/invokable/projects/laravel-google-searchconsole)
[![Ask DeepWiki](https://deepwiki.com/badge.svg)](https://deepwiki.com/invokable/laravel-google-searchconsole)

https://developers.google.com/webmaster-tools

## Overview

This Laravel package provides a comprehensive PHP wrapper for the Google Search Console API, enabling you to seamlessly integrate Search Console functionality into your Laravel applications. With this package, you can:

- **Query Search Analytics Data**: Retrieve search performance metrics including impressions, clicks, CTR, and average position
- **Analyze Website Performance**: Get detailed insights about your website's performance in Google Search results
- **Filter by Dimensions**: Query data by page, query, country, device, and more
- **Manage Site Properties**: List and manage your Search Console properties
- **Flexible Authentication**: Support for both OAuth 2.0 (user-based) and Service Account (server-to-server) authentication
- **Laravel Integration**: Built specifically for Laravel with facades, service providers, and Artisan commands

The package leverages the powerful `revolution/laravel-google-sheets` dependency for Google API client management and authentication, providing automatic token refresh and robust error handling.

**Perfect for**: SEO tools, analytics dashboards, automated reporting, website monitoring, and any application that needs to access Google Search Console data programmatically.

## Requirements
- PHP >= 8.3
- Laravel >= 12.0

## Installation

```bash
composer require revolution/laravel-google-searchconsole

php artisan vendor:publish --tag="google-config"
```

### Uninstall
```
composer remove revolution/laravel-google-searchconsole
```

## Sample project

https://github.com/invokable/search-console-project

## Configuration

### Authentication Methods

This package supports two authentication methods for accessing the Google Search Console API:

- **OAuth 2.0**: Recommended for user-based access when you need to access data on behalf of individual Google users
- **Service Account**: Ideal for server-to-server applications with automated access to specific properties
- **Note**: API Key authentication is NOT supported by Google Search Console API

### Prerequisites

1. Go to the [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one
3. Enable the **Google Search Console API**
4. Create credentials based on your chosen authentication method

## OAuth 2.0 Setup (using Socialite)

For OAuth authentication, this package works seamlessly with Laravel Socialite's official Google driver.

### 1. Install Laravel Socialite

```bash
composer require laravel/socialite
```

### 2. Create OAuth 2.0 Credentials

In Google Cloud Console:
1. Go to "Credentials" → "Create Credentials" → "OAuth 2.0 Client IDs"
2. Set application type to "Web application"
3. Add your authorized redirect URIs (e.g., `https://yourapp.com/auth/google/callback`)

### 3. Configure OAuth Settings

Add to `config/services.php`:

```php
'google' => [
    'client_id'     => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect'      => env('GOOGLE_REDIRECT_URI'),
],
```

Add to `config/google.php`:

```php
'client_id'        => env('GOOGLE_CLIENT_ID'),
'client_secret'    => env('GOOGLE_CLIENT_SECRET'),
'redirect_uri'     => env('GOOGLE_REDIRECT_URI'),
'scopes'           => [\Google\Service\Webmasters::WEBMASTERS],
'access_type'      => 'offline',
'prompt'           => 'consent select_account',
```

### 4. Environment Variables for OAuth

```env
GOOGLE_CLIENT_ID=your_oauth_client_id
GOOGLE_CLIENT_SECRET=your_oauth_client_secret
GOOGLE_REDIRECT_URI=https://yourapp.com/auth/google/callback
```

## Service Account Setup

Service Accounts provide server-to-server authentication without user interaction. The Service Account email must be added as an owner or user in Search Console for each property you want to access.

### 1. Create Service Account Credentials

In Google Cloud Console:
1. Go to "Credentials" → "Create Credentials" → "Service Account"
2. Fill in the service account details
3. Create and download the JSON key file

### 2. Configure Service Account

The package automatically uses Service Account authentication when configured through the `laravel-google-sheets` dependency. Place your service account JSON file in your Laravel storage directory.

Even for service accounts, scopes must be set in `config/google.php`:

```php
'scopes' => [\Google\Service\Webmasters::WEBMASTERS],
```

### 3. Environment Variables for Service Account

#### Option 1: Using File Path

```env
GOOGLE_SERVICE_ENABLED=true
GOOGLE_SERVICE_ACCOUNT_JSON_LOCATION=/path/to/service-account.json
```

#### Option 2: Using JSON String

`GOOGLE_SERVICE_ACCOUNT_JSON_LOCATION` can be an array other than a file path, so you can set it as a JSON string in `.env` and decode it to an array in `config/google.php`.

```php
// config/google.php
'service' => [
    'file' => json_decode(env('GOOGLE_SERVICE_ACCOUNT_JSON_LOCATION', ''), true),
],
```

```env
// .env
GOOGLE_SERVICE_ACCOUNT_JSON_LOCATION='{"type": "service_account", "project_id": "your-project", "private_key_id": "...", "private_key": "...", "client_email": "...", "client_id": "...", "auth_uri": "...", "token_uri": "...", "auth_provider_x509_cert_url": "...", "client_x509_cert_url": "..."}'
```

This method is particularly well-suited for GitHub Actions deployment, as it allows the entire service account credentials to be stored as a single secret.

### 4. Add Service Account to Search Console

1. Go to [Google Search Console](https://search.google.com/search-console)
2. Select your property
3. Go to Settings → Users and permissions
4. Add your service account email as a user with "Full" permissions

## Creating Custom Queries

All query classes extend `Revolution\Google\SearchConsole\Query\AbstractQuery`, which is a subclass of `Google\Service\Webmasters\SearchAnalyticsQueryRequest`. This provides access to all Google Search Console API query parameters.

### Generate Query Classes

Use the Artisan command to create new query classes in `app/Search`:

```bash
php artisan make:search:query YourQueryName
```

### Query Structure

Each query class must implement the `init()` method where you define your query parameters:

```php
<?php

namespace App\Search;

use Revolution\Google\SearchConsole\Query\AbstractQuery;

class YourQueryName extends AbstractQuery
{
    public function init(): void
    {
        // Define your query parameters here
    }
}
```

### Available Query Parameters

You can use any of the following methods in your `init()` method:

#### Date Range
```php
$this->setStartDate('2024-01-01');                    // Start date (YYYY-MM-DD)
$this->setEndDate('2024-01-31');                      // End date (YYYY-MM-DD)
$this->setStartDate(now()->subDays(30)->toDateString()); // Dynamic dates
```

#### Dimensions (group results by)
```php
$this->setDimensions(['query']);           // Group by search queries
$this->setDimensions(['page']);            // Group by pages
$this->setDimensions(['country']);         // Group by countries
$this->setDimensions(['device']);          // Group by device types
$this->setDimensions(['searchAppearance']); // Group by search appearance
$this->setDimensions(['query', 'page']);   // Multiple dimensions
```

#### Filters
```php
// Filter by specific queries
$this->setDimensionFilterGroups([
    [
        'filters' => [
            [
                'dimension' => 'query',
                'operator' => 'contains',
                'expression' => 'laravel'
            ]
        ]
    ]
]);

// Filter by specific pages
$this->setDimensionFilterGroups([
    [
        'filters' => [
            [
                'dimension' => 'page',
                'operator' => 'equals',
                'expression' => 'https://example.com/page'
            ]
        ]
    ]
]);
```

#### Additional Options
```php
$this->setRowLimit(100);                   // Limit results (max 25,000)
$this->setStartRow(0);                     // Pagination offset
$this->setAggregationType(['auto']);       // Aggregation type
$this->setDataState('final');              // 'final' or 'fresh' data
```

### Example Query Classes

#### Top Pages Query
```php
<?php

namespace App\Search;

use Revolution\Google\SearchConsole\Query\AbstractQuery;

class TopPagesQuery extends AbstractQuery
{
    public function init(): void
    {
        $this->setStartDate(now()->subDays(30)->toDateString());
        $this->setEndDate(now()->toDateString());
        $this->setDimensions(['page']);
        $this->setRowLimit(50);
        $this->setDataState('final');
    }
}
```

#### Mobile Search Queries
```php
<?php

namespace App\Search;

use Revolution\Google\SearchConsole\Query\AbstractQuery;

class MobileQueriesQuery extends AbstractQuery
{
    public function init(): void
    {
        $this->setStartDate(now()->subDays(7)->toDateString());
        $this->setEndDate(now()->toDateString());
        $this->setDimensions(['query']);
        
        // Filter for mobile devices only
        $this->setDimensionFilterGroups([
            [
                'filters' => [
                    [
                        'dimension' => 'device',
                        'operator' => 'equals',
                        'expression' => 'mobile'
                    ]
                ]
            ]
        ]);
        
        $this->setRowLimit(100);
    }
}
```

#### Country Performance Query
```php
<?php

namespace App\Search;

use Revolution\Google\SearchConsole\Query\AbstractQuery;

class CountryPerformanceQuery extends AbstractQuery
{
    public function init(): void
    {
        $this->setStartDate(now()->subMonths(3)->toDateString());
        $this->setEndDate(now()->toDateString());
        $this->setDimensions(['country', 'query']);
        $this->setRowLimit(200);
        $this->setAggregationType(['auto']);
    }
}
```

## Basic Usage

### Using OAuth 2.0 Authentication

When using OAuth, you'll need to obtain access tokens through the Socialite authentication flow, then use `setAccessToken()` to authenticate your requests.

#### 1. OAuth Authentication Flow (Controller Example)

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Revolution\Google\SearchConsole\Facades\SearchConsole;
use App\Search\TopPagesQuery;

class GoogleSearchConsoleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->scopes(config('google.scopes'))
            ->with([
                'access_type'     => config('google.access_type'),
                'prompt' => config('google.prompt'),
            ])
            ->redirect();
    }
    
    public function handleGoogleCallback()
    {
        $user = Socialite::driver('google')->user();
        
        // Store tokens in your database
        auth()->user()->update([
            'google_access_token' => $user->token,
            'google_refresh_token' => $user->refreshToken,
        ]);
        
        return redirect()->route('dashboard');
    }
    
    public function getSearchConsoleData(Request $request)
    {
        $token = [
            'access_token' => auth()->user()->google_access_token,
            'refresh_token' => auth()->user()->google_refresh_token,
        ];
        
        // Create and execute query
        $query = new TopPagesQuery();
        $result = SearchConsole::setAccessToken($token)
                              ->query('https://example.com/', $query);
        
        return response()->json($result);
    }
}
```

#### 2. Using OAuth with Model Integration

You can use the `WithSearchConsole` trait to integrate Search Console functionality directly into your models:

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Revolution\Google\SearchConsole\Traits\WithSearchConsole;

class User extends Authenticatable
{
    use WithSearchConsole;
    
    protected $fillable = [
        'name', 'email', 'google_access_token', 'google_refresh_token'
    ];
    
    protected function tokenForSearchConsole(): array
    {
        return [
            'access_token' => $this->google_access_token,
            'refresh_token' => $this->google_refresh_token,
        ];
    }
}
```

Then use it in your application:

```php
use App\Search\TopPagesQuery;

$user = User::find(1);
$query = new TopPagesQuery();

// The searchconsole() method automatically handles authentication
$result = $user->searchconsole()->query('https://example.com/', $query);
$sites = $user->searchconsole()->listSites();
```

### Using Service Account Authentication

With Service Account authentication, the package automatically handles authentication through the GoogleApiClient included in `laravel-google-sheets`. No manual token management is required.

#### Service Account Usage Example

```php
<?php

namespace App\Services;

use Revolution\Google\SearchConsole\Facades\SearchConsole;
use App\Search\TopPagesQuery;
use App\Search\MobileQueriesQuery;
use App\Search\CountryPerformanceQuery;

class SearchConsoleService
{
    public function getTopPages(string $siteUrl): object
    {
        $query = new TopPagesQuery();
        
        // Service Account authentication is automatic
        return SearchConsole::query($siteUrl, $query);
    }
    
    public function getMobileQueries(string $siteUrl): object
    {
        $query = new MobileQueriesQuery();
        return SearchConsole::query($siteUrl, $query);
    }
    
    public function getCountryPerformance(string $siteUrl): object
    {
        $query = new CountryPerformanceQuery();
        return SearchConsole::query($siteUrl, $query);
    }
    
    public function getAllSites(): object
    {
        return SearchConsole::listSites();
    }
    
    public function getSiteInfo(string $siteUrl): object
    {
        return SearchConsole::listSites(['siteUrl' => $siteUrl]);
    }
}
```

#### Using Service Account in Commands

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Revolution\Google\SearchConsole\Facades\SearchConsole;
use App\Search\TopPagesQuery;

class FetchSearchConsoleData extends Command
{
    protected $signature = 'searchconsole:fetch {site_url}';
    protected $description = 'Fetch Search Console data for a site';
    
    public function handle()
    {
        $siteUrl = $this->argument('site_url');
        $query = new TopPagesQuery();
        
        try {
            $result = SearchConsole::query($siteUrl, $query);
            
            $this->info("Found {$result->rowCount} results:");
            
            foreach ($result->rows as $row) {
                $this->line("Page: {$row->keys[0]}");
                $this->line("  Clicks: {$row->clicks}");
                $this->line("  Impressions: {$row->impressions}");
                $this->line("  CTR: " . round($row->ctr * 100, 2) . "%");
                $this->line("  Position: " . round($row->position, 1));
                $this->line("");
            }
        } catch (\Exception $e) {
            $this->error("Error fetching data: " . $e->getMessage());
        }
    }
}
```

### Quick Reference

#### Available Facade Methods

```php
use Revolution\Google\SearchConsole\Facades\SearchConsole;

// Set access token (OAuth only)
SearchConsole::setAccessToken($token);

// Execute a query
SearchConsole::query($siteUrl, $queryObject);

// List all sites
SearchConsole::listSites();

// List sites with parameters
SearchConsole::listSites(['siteUrl' => 'https://example.com/']);
```

#### Response Structure

The API returns objects with the following structure:

```php
$result = SearchConsole::query($siteUrl, $query);

// Access the data
echo $result->rowCount;           // Number of results
echo $result->responseAggregationType; // Aggregation type used

foreach ($result->rows as $row) {
    print_r($row->keys);          // Array of dimension values
    echo $row->clicks;            // Number of clicks
    echo $row->impressions;       // Number of impressions
    echo $row->ctr;              // Click-through rate (0-1)
    echo $row->position;         // Average position (1+)
}
```

#### Laravel Helper Functions for Response Handling

The response objects work seamlessly with Laravel's helper functions, making data manipulation more convenient:

**Using `data_get()` for Safe Property Access**

```php
$result = SearchConsole::query($siteUrl, $query);

// Safely access properties with default values
$rowCount = data_get($result, 'rowCount', 0);
$aggregationType = data_get($result, 'responseAggregationType', 'unknown');

// Access nested properties safely
foreach ($result->rows as $row) {
    $clicks = data_get($row, 'clicks', 0);
    $impressions = data_get($row, 'impressions', 0);
    $ctr = data_get($row, 'ctr', 0.0);
    $position = data_get($row, 'position', 0.0);
    
    // Handle optional or nested properties
    $firstKey = data_get($row, 'keys.0', 'N/A');
}
```

**Using `collect()` for Enhanced Data Processing**

```php
$result = SearchConsole::query($siteUrl, $query);

// Convert rows to a Laravel Collection for powerful data manipulation
$rows = collect($result->rows);

// Filter rows with high CTR
$highCtrRows = $rows->filter(fn($row) => $row->ctr > 0.05);

// Sort by impressions (descending)
$sortedRows = $rows->sortByDesc('impressions');

// Get top 10 pages by clicks
$topPages = $rows->sortByDesc('clicks')->take(10);

// Transform data structure
$pageData = $rows->map(function ($row) {
    return [
        'page' => data_get($row, 'keys.0', 'Unknown'),
        'metrics' => [
            'clicks' => $row->clicks,
            'impressions' => $row->impressions,
            'ctr' => round($row->ctr * 100, 2) . '%',
            'position' => round($row->position, 1),
        ]
    ];
});

// Group by device type (if querying by device dimension)
$deviceGroups = $rows->groupBy(fn($row) => data_get($row, 'keys.0', 'unknown'));

// Calculate totals
$totalClicks = $rows->sum('clicks');
$totalImpressions = $rows->sum('impressions');
$averagePosition = $rows->average('position');
```

## LICENSE
MIT
