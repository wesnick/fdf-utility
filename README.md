FDF Utility Library for PHP
===========================

[![CI](https://github.com/wesnick/fdf-utility/actions/workflows/ci.yaml/badge.svg)](https://github.com/wesnick/fdf-utility/actions/workflows/ci.yaml)
[![Coding Standards](https://github.com/wesnick/fdf-utility/actions/workflows/coding-standards.yaml/badge.svg)](https://github.com/wesnick/fdf-utility/actions/workflows/coding-standards.yaml)
[![Static Analysis](https://github.com/wesnick/fdf-utility/actions/workflows/static-analysis.yaml/badge.svg)](https://github.com/wesnick/fdf-utility/actions/workflows/static-analysis.yaml)

This library produces FDF files for PDF Forms.

Left to finish

- Add additional validation and tests for invalid configurations.
- Implement signature field support.

## Console Usage

- Generate CSV Export from PDF

```shell
bin/fdf wesnick:fdf:csv-export /path/to/my/pdf.pdf path/to/my/csv/csv --pdftk=/path/to/pdftk
```

- Generate Example Filled PDF

```shell
bin/fdf wesnick:fdf:example-pdf /path/to/my/emtpy-pdf.pdf path/to/my/filled-pdf.pdf --pdftk=/path/to/pdftk
```

You can also use the library's components directly. The class `PdfForm` is a useful reference point.

## Installation

Use composer.

```shell
composer require wesnick/fdf-utility
```

## Requirements

- PHP 8.1 or higher
- Symfony Process (^6.4 or 7.2.*)

> [!TIP]
> For PHP versions between 7.1 and 8.0 use
> the [release v0.6.0](https://github.com/wesnick/fdf-utility/releases/tag/v0.6.0)

> [!TIP]
> For PHP versions lower than 7.1 use the [release v0.5.0](https://github.com/wesnick/fdf-utility/releases/tag/v0.5.0)

> [!TIP]
> For Symfony versions between >=3.3 and <4.4 use
> the [release v0.5.0](https://github.com/wesnick/fdf-utility/releases/tag/v0.5.0)

> [!TIP]
> For Symfony versions between >=2.3 and <3.3 use
> the [release v0.4.3](https://github.com/wesnick/fdf-utility/releases/tag/v0.4.3)

## Acknowledgements

Much of the code for creating FDF files is based on Sid Steward's PDF work -- http://www.pdflabs.com
The test pdf form is borrowed from active_pdftk, as well as some ideas about how to handle pdftk field dumps.

## Contributing

Fork and issue a Pull Request.

## Running the Tests

```shell
./vendor/bin/phpunit
```

## License

Released under the MIT License. See the bundled LICENSE file for details.
