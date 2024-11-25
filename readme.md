# Silverstripe Email Obfuscator

[![Version](http://img.shields.io/packagist/v/innoweb/silverstripe-email-obfuscator.svg?style=flat-square)](https://packagist.org/packages/innoweb/silverstripe-email-obfuscator)
[![License](http://img.shields.io/packagist/l/innoweb/silverstripe-email-obfuscator.svg?style=flat-square)](license.md)

## Overview

Obfuscates all email addresses displayed on the site. Email addresses are revealed using Javascript.

Thanks to [Plato](https://github.com/PlatoCreative/silverstripe-email-obfuscator) for some inspiration.

## Requirements

* Silverstripe CMS 5.x

Note: this version is compatible with SilverStripe 5. For SilverStripe 4, please see the [2 release line](https://github.com/xini/silverstripe-email-obfuscator/tree/2).

## Installation

Install the module using composer:
```
composer require innoweb/silverstripe-email-obfuscator dev-master
```
and run dev/build.

## Usage

All email addresses on a page get obfuscated by a middleware and then rewritten back to email links via Javascript.

If you need to skip obfuscation of certain links, you can add the `skip-email-obfuscation` class to their tag.

This is useful for e.g. Mastodon links that are falsly recognised as emails:

```html
<a href="https://mastodon.social/@sminnee@mastodon.nz" class="skip-email-obfuscation">Sam Minnée on Mastodon</a>

```

## License

BSD 3-Clause License, see [License](license.md)
