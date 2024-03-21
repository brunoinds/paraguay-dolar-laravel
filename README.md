# PHP Paraguay Dolar Currency Exchange

A simple PHP library for exchanging currencies based on bcp.gov.py

## Installation

Install via Composer:

```bash
composer require brunoinds/paraguay-dolar-laravel
```

## Usage

The `Exchange` class provides methods for exchanging between PYG and USD:

```php
use Brunoinds\ParaguayDolarLaravel\Exchange;
use Brunoinds\ParaguayDolarLaravel\Enums\Currency;

// Get current exchange rate
$result = Exchange::now()->convert(Currency::USD, 1)->to(Currency::PYG);

// Get historical exchange rate 
$date = new DateTime('2023-12-10');
$result = Exchange::on($date)
                ->convert(Currency::USD, 1)
                ->to(Currency::PYG);
echo $result // 0.27

```

The `Currency` enum provides constants for the supported currencies:

```php
use Brunoinds\ParaguayDolarLaravel\Enums\Currency;

Currency::USD;
Currency::PYG;
```

## Testing

Unit tests are located in the `tests` directory. Run tests with:

```
composer test
```

## Contributing

Pull requests welcome!

## License

MIT License

## Powered by:
- [API bcp.gov.py](https://www.bcp.gov.py/webapps/web/cotizacion/monedas)

Let me know if you would like any sections expanded or have any other feedback!