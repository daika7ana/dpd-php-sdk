# Installation

## Requirements

- PHP 8.3+
- PSR-7, PSR-17, and PSR-18 compatible environment

## Install With Composer

```bash
composer require daika7ana/dpd-php-sdk
```

## Transport Defaults

The SDK ships with a Guzzle-based PSR-18 adapter and PSR-17 factories.
`guzzlehttp/guzzle` is a normal runtime dependency, so `Client::create()` works
after `composer require` without extra setup. You can still pass any PSR-18
client and PSR-17 request/stream factories into `Client::create()` to bypass the
default adapter.

## Next Step

After installation, continue with configuration and client bootstrapping:

- [Configuration](CONFIGURATION.md)
- [Quick Start](QUICKSTART.md)
