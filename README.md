FDF Utility Library for PHP
===========================

[![Build Status](https://travis-ci.org/wesnick/fdf-utility.png)](https://travis-ci.org/wesnick/fdf-utility)

This library produces FDF files for PDF Forms.

Left to finish
- add additional validation and tests for invalid configurations
- implement signature field support


## Usage

Console Usage -
- Generate CSV Export from PDF
```
$ /path/to/fdf-utility/bin/fdf wesnick:fdf:csv-export /path/to/my/pdf.pdf path/to/my/csv/csv --pdftk=/path/to/pdftk
```

- Generate Example Filled PDF
```
$ /path/to/fdf-utility/bin/fdf wesnick:fdf:example-pdf /path/to/my/emtpy-pdf.pdf path/to/my/filled-pdf.pdf --pdftk=/path/to/pdftk
```

You can also use the lirbary's components directly.  The class PdfForm is a useful reference point.


## Installation

Use composer.

```
composer require wesnick/fdf-utility
```

## Requirements

- PHP 5.5.9 / PHP 7.0 or higher
- Symfony Process (>=3.3 or >=4.0)
- Symfony Console (dev, >=3.3 or >=4.0)

For Symfony versions between >=2.3 and <3.3 use the [release v0.4.3](https://github.com/wesnick/fdf-utility/releases/tag/v0.4.3)

## Acknowledgements

Much of the code for creating FDF files is based on Sid Steward's PDF work -- http://www.pdflabs.com
The test pdf form is borrowed from active_pdftk, as well as some ideas about how to handle pdftk field dumps

## Contributing

Fork and issue a Pull Request.

## Running the Tests

```
$ phpunit
```

## License

Released under the MIT License. See the bundled LICENSE file for details.
