# Installation

## Requirements

- PHP 8.3+
- PSR-7, PSR-17, and PSR-18 compatible environment

## Install With Composer

```bash
composer require daika7ana/dpd-php-sdk
```

## Transport Defaults

The SDK ships with a Guzzle-based PSR-18 adapter by default. If your application already standardizes on a different PSR-18 client or PSR-17 factories, pass them into `Client::create()` instead of replacing the rest of the SDK surface.

## Next Step

After installation, continue with configuration and client bootstrapping:

- [Configuration](CONFIGURATION.md)
- [Quick Start](QUICKSTART.md)
