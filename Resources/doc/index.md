# Installation

Add this line to your `composer.json` file:

```json
{
    "require": {
        "florianv/swap-bundle": "~3.0"
    }
}
```

Update the dependency by running:

```bash
$ php composer.phar update florianv/swap-bundle
```

Enable the Bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Florianv\SwapBundle\FlorianvSwapBundle(),
    );
}
```

# Configuration

## Builtin providers

```yaml
# app/config/config.yml
florianv_swap:
    providers:
        yahoo: ~                           # Yahoo Finance
        google: ~                          # Google Finance  
        fixer: ~                           # Fixer
        webservicex: ~                     # WebserviceX 
        cryptonator: ~                     # Cryptonator 
        russian_central_bank: ~            # Russian Central Bank
        european_central_bank: ~           # European Central Bank
        national_bank_of_romania: ~        # National Bank of Romania
        central_bank_of_czech_republic: ~  # Central Bank of the Czech Republic
        central_bank_of_republic_turkey: ~ # Central Bank of the Republic of Turkey
        open_exchange_rates:               # Open Exchange Rates
            app_id: secret
            enterprise: true 
        currency_layer:                    # currencylayer
            access_key: secret
            enterprise: true 
        xignite:                           # Xignite
            token: secret
```

You can register multiple providers, they will be called in chain. In this example the Yahoo Finance is
the first one and Google Finance is the second one:

```yaml
# app/config/config.yml
florianv_swap:
    providers:
        yahoo: ~
        google: ~
```

## Cache

Currently only some of the [Symfony Cache](https://symfony.com/doc/current/components/cache.html#available-simple-cache-psr-16-classes) adapters are supported.

### Lifetime

You must specify a lifetime for your cache entries:

```yaml
# app/config/config.yml
florianv_swap:
    cache:
        ttl: 3600 # seconds
```

### Cache type

You can use a service id:

```yaml
# app/config/config.yml
florianv_swap:
    cache:
        type: my_cache_service
```

or one of the implemented providers (`array`, `apcu`, `filesystem`)

```yaml
# app/config/config.yml
florianv_swap:
    cache:
        type: apcu
```

# Usage

The Swap service is available in the container:

```php
/** @var \Swap\Swap $swap */
$swap = $this->get('florianv_swap.swap');
```

For more information about how to use it, please consult the [Swap documentation](https://github.com/florianv/swap).
