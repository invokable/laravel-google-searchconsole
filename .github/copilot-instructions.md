# GitHub Copilot Onboarding Guide

## Repository Overview

This repository contains **invokable/laravel-google-searchconsole**, a Laravel package that provides a PHP wrapper for the Google Search Console API. The package simplifies integration with Google's Search Console API, allowing Laravel applications to query search analytics data, manage site properties, and analyze website performance in Google Search.

**Primary Purpose**: Enable Laravel developers to easily integrate Google Search Console API functionality into their applications with minimal configuration and maximum flexibility.

**Note**: While the GitHub repository is `invokable/laravel-google-searchconsole`, the Composer package name remains `revolution/laravel-google-searchconsole` for compatibility with existing installations.

## Copilot Environment Restrictions

⚠️ **CRITICAL**: The Copilot firewall causes errors when connecting to `www.googleapis.com`

- Google API calls will fail in the Copilot development environment
- This affects testing and development of Google Search Console functionality
- GitHub Actions and production environments have no such restrictions
- **Workaround**: Use mock data or skip API calls when testing in Copilot environment
- Real API functionality should be tested in GitHub Actions or local development environments

**Note**: All examples and tests should be designed to handle API connectivity failures gracefully when running in restricted environments.

## Key Technologies & Frameworks

- **PHP**: ^8.3 (modern PHP features, typed properties, union types)
- **Laravel**: ^12.0 (Laravel framework integration)
- **Google APIs Client Library**: Via `revolution/laravel-google-sheets` dependency
- **Testing**: PHPUnit with Orchestra Testbench for Laravel package testing
- **Code Quality**: Laravel Pint for code formatting (Laravel preset)
- **Authentication**: OAuth 2.0 and Service Account support

## Project Structure

```
├── .github/                     # GitHub configuration
│   └── workflows/              # CI/CD pipelines
├── docs/                       # Documentation
│   ├── workbench.md            # Workbench development guide
│   ├── trait.md                # Trait usage documentation
│   └── macro.md                # Macro functionality docs
├── src/                        # Main source code
│   ├── Commands/               # Artisan commands
│   │   ├── QueryMakeCommand.php    # Generate query classes
│   │   └── stubs/query.stub        # Template for query classes
│   ├── Concerns/               # Legacy traits (deprecated)
│   │   └── SearchConsole.php       # Legacy model integration trait
│   ├── Traits/                 # Traits
│   │   └── WithSearchConsole.php   # Model integration trait
│   ├── Contracts/              # Interfaces
│   │   ├── Factory.php             # Main service contract
│   │   └── Query.php               # Query interface
│   ├── Facades/                # Laravel facades
│   │   └── SearchConsole.php       # Primary facade
│   ├── Providers/              # Service providers
│   │   └── SearchConsoleServiceProvider.php
│   ├── Query/                  # Query builders
│   │   └── AbstractQuery.php       # Base query class
│   └── SearchConsoleClient.php     # Main client implementation
├── tests/                      # Test suite
│   ├── Feature/                # Feature tests
│   ├── Integration/            # Integration tests
│   └── Search/                 # Example query classes
├── workbench/                  # Orchestra Testbench Workbench
│   ├── app/                    # Workbench Laravel app
│   ├── bootstrap/              # Application bootstrap
│   ├── database/               # Migrations, seeders, factories
│   ├── routes/                 # Workbench routes
│   └── resources/              # Views, assets
├── composer.json               # Package dependencies
├── testbench.yaml.example      # Workbench configuration template
├── pint.json                   # Code style configuration
└── phpunit.xml                 # Test configuration
```

### Important Files

- **SearchConsoleClient.php**: Core client handling API interactions
- **AbstractQuery.php**: Base class for all search queries
- **SearchConsole.php (Facade)**: Main entry point for the package
- **SearchConsoleServiceProvider.php**: Laravel service registration
- **QueryMakeCommand.php**: Artisan command for generating query classes
- **WithSearchConsole.php**: Model integration trait

## Coding Conventions & Best Practices

### Code Style
- Follows **Laravel Pint** with Laravel preset
- PSR-4 autoloading standards
- Unused imports are allowed (configured in pint.json)
- Method chaining for fluent interfaces

### Naming Conventions
- Classes: PascalCase (e.g., `SearchConsoleClient`)
- Methods: camelCase (e.g., `setAccessToken`)
- Properties: camelCase with typed declarations
- Interfaces: Descriptive names in `Contracts/` namespace

### Architecture Patterns
- **Facade Pattern**: `SearchConsole` facade for easy access
- **Service Provider Pattern**: Laravel service container integration
- **Abstract Factory Pattern**: Query creation and management
- **Command Pattern**: Artisan commands for code generation
- **Trait Pattern**: Model integration via `WithSearchConsole` trait

### Authentication Handling
- OAuth 2.0 tokens: `['access_token' => '...', 'refresh_token' => '...']`
- Service Account: JSON key file or credentials array
- Token refresh handled automatically when refresh token is available

## Sample Tasks & Copilot Prompt Guidance

### 1. Creating a New Query Class

**Prompt**: "Create a new query class for getting top pages by impressions in the last 30 days"

**Expected Structure**:
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
        $this->setMetrics(['impressions', 'clicks', 'ctr', 'position']);
        $this->setRowLimit(100);
        $this->setDataState('final');
    }
}
```

### 2. Implementing Model Integration

**Prompt**: "Add SearchConsole functionality to a User model with stored Google tokens"

**Expected Pattern**:
```php
use Revolution\Google\SearchConsole\Traits\WithSearchConsole;

class User extends Model
{
    use WithSearchConsole;
    
    protected function tokenForSearchConsole(): array|string
    {
        return [
            'access_token' => $this->google_access_token,
            'refresh_token' => $this->google_refresh_token,
        ];
    }
}
```

### 3. Service Usage Examples

**Prompt**: "Show how to use the SearchConsole facade to query analytics data"

**Expected Usage**:
```php
use Revolution\Google\SearchConsole\Facades\SearchConsole;

// Set token and query
$result = SearchConsole::setAccessToken($token)
                      ->query('https://example.com/', new TopPagesQuery());

// List available sites
$sites = SearchConsole::setAccessToken($token)->listSites();
```

### 4. Testing Patterns

**Prompt**: "Create a test for a custom query class"

**Expected Test Structure**:
```php
use Tests\TestCase;
use Revolution\Google\SearchConsole\Facades\SearchConsole;

class CustomQueryTest extends TestCase
{
    public function test_custom_query_initialization()
    {
        $query = new CustomQuery();
        
        $this->assertInstanceOf(AbstractQuery::class, $query);
        // Test specific query parameters
    }
}
```

### Effective Copilot Prompts

1. **Be Specific**: "Create a query for mobile traffic data from Search Console API"
2. **Include Context**: "Following the AbstractQuery pattern, create a query that..."
3. **Mention Dependencies**: "Using the SearchConsole facade, implement a method that..."
4. **Reference Patterns**: "Like the existing SampleQuery, create a new query for..."

## Repository-Specific Guidelines

### Authentication Considerations
- Always handle token expiration with refresh tokens
- Service account emails must be added to Search Console properties
- API keys are NOT supported by Google Search Console API

### Query Development
- All queries must extend `AbstractQuery`
- Implement the `init()` method for query configuration
- Use appropriate date ranges and dimensions
- Consider API rate limits and data freshness

### Error Handling
- Google API errors are typically thrown as exceptions
- Handle authentication failures gracefully
- Validate URL parameters for Search Console properties

### Performance Best Practices
- Cache Google API responses when appropriate
- Batch multiple queries when possible
- Use appropriate row limits to avoid large responses
- Consider using data state 'final' vs 'fresh' based on needs

## Workbench Development Environment

This package uses **Orchestra Testbench Workbench** for development, providing a complete Laravel application environment for testing and demonstrating package functionality.

### What is Workbench?

Workbench creates a complete Laravel application structure within your package, enabling you to:

- **Test your package in a real Laravel application context**
- **Develop and preview package functionality interactively**
- **Create demo applications and examples**
- **Build comprehensive test suites with realistic scenarios**
- **Serve your package with a full Laravel application for development**

### Key Workbench Features

1. **Complete Laravel Application**: Full Laravel app structure in `workbench/` directory
2. **Interactive Development**: Serve the workbench with `composer serve`
3. **Package Integration**: Your package is automatically loaded and available
4. **Database Support**: Migrations, seeders, and factories for test data
5. **Configuration Management**: `testbench.yaml` for environment-specific settings

### Workbench Configuration

Configuration is managed through `testbench.yaml` (not committed to repository):

```bash
# Copy the example configuration
cp testbench.yaml.example testbench.yaml

# Customize for your development environment
```

### Common Workbench Commands

```bash
# Build the workbench application
composer build

# Serve the workbench (includes automatic building)
composer serve

# Clear workbench cache and rebuild
composer clear && composer build

# Access workbench Laravel app
# Available at http://localhost:8000 when serving
```

### Workbench Best Practices

- Keep workbench code separate from package code
- Use workbench for integration testing and demonstrations
- Create realistic test scenarios in workbench controllers
- Utilize workbench seeders for comprehensive test data
- Document workbench setup for contributors

### Development Workflow with Workbench

1. **Develop**: Write your package code in `src/`
2. **Configure**: Update `testbench.yaml` with required providers and settings
3. **Build**: Run `composer build` to create workbench application
4. **Test**: Use workbench routes and controllers to test functionality
5. **Iterate**: Make changes and rebuild as needed

*See `docs/workbench.md` for comprehensive workbench documentation and advanced usage patterns.*

## Common Copilot Workflows

### Generating Query Classes
1. Use the artisan command: `php artisan make:search:query QueryName`
2. Implement the `init()` method with appropriate parameters
3. Test the query with mock data or real API calls

### Extending Functionality
1. Follow existing patterns in the codebase
2. Use the SearchConsole facade for consistent API access
3. Implement proper error handling and validation
4. Add corresponding tests
5. Test functionality using workbench for integration testing

### Testing with Workbench
1. Build workbench: `composer build`
2. Serve workbench: `composer serve`
3. Create demo routes in `workbench/routes/web.php`
4. Test package functionality interactively
5. Use workbench for comprehensive integration testing

### Debugging API Issues
1. Check token validity and permissions
2. Verify Search Console property ownership
3. Review API quotas and limits
4. Validate URL formats and parameters

## Development Commands

```bash
# Install dependencies
composer install

# Run code style checks
vendor/bin/pint --test

# Fix code style issues
vendor/bin/pint

# Run tests
vendor/bin/phpunit
# Or use the composer script
composer test

# Generate query class
php artisan make:search:query YourQueryName

# Workbench commands
composer build           # Build workbench application
composer serve           # Serve workbench (includes building)
composer clear           # Clear workbench cache
```

## Contributing Guidelines

1. Follow Laravel Pint formatting standards
2. Write comprehensive tests for new features
3. Use meaningful commit messages
4. Ensure backward compatibility
5. Update documentation for public API changes
6. Test with both OAuth and Service Account authentication methods

---

*This guide helps GitHub Copilot understand the repository structure, patterns, and best practices for contributing to the Laravel Google SearchConsole package in the `invokable/laravel-google-searchconsole` repository.*
