# Islandora REST Client

PHP library for building clients for Islandora's REST interface.

Still in early development, please stay tuned.

## Requirements

* On the target Islandora instance
  * [Islandora REST](https://github.com/discoverygarden/islandora_rest)
  * [Islandora REST Authen](https://github.com/mjordan/islandora_rest_authen)
  * Optionally, [Islandora REST Extras](https://github.com/mjordan/islandora_rest_extras) (see "Generating DC XML" below for more information).
* On the system where the script is run
  * PHP 5.5.0 or higher.
  * [Composer](https://getcomposer.org)

## Installation

1. `git clone https://github.com/mjordan/islandora_rest_client.git`
1. `cd islandora_rest_client`
1. `php composer.phar install` (or equivalent on your system, e.g., `./composer install`)

## Example

## Maintainer

* [Mark Jordan](https://github.com/mjordan)

## Development and feedback

Still in very early development. Once it's past the proof of concept stage, I'd be happy to take PRs, etc.

## License

The Unlicense
