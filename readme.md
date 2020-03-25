# SilverStripe Email Obfuscator

[![Version](http://img.shields.io/packagist/v/innoweb/silverstripe-email-obfuscator.svg?style=flat-square)](https://packagist.org/packages/innoweb/silverstripe-email-obfuscator)
[![License](http://img.shields.io/packagist/l/innoweb/silverstripe-email-obfuscator.svg?style=flat-square)](license.md)

## Overview

Obfuscates all email addresses displayed on the site. Email addresses are revealed using Javascript. The links falls back to a global contact form when no Javascript is available.

## Requirements

* SilverStripe CMS ^3.1

## Installation

Install the module using composer:
```
composer require innoweb/silverstripe-email-obfuscator dev-master
```
and run dev/build.

## Configuration

The module adds a new tab to the SiteConfig in the CMS where the default contact form for the fallback can be configured. 

### MultiSites support

The module supports the [multisites module](https://github.com/silverstripe-australia/silverstripe-multisites) and by default adds the config options to the Sites.

## License

BSD 3-Clause License, see [License](license.md)
