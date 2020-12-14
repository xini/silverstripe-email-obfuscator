<?php

namespace Innoweb\EmailObfuscator\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\View\Requirements;
use SilverStripe\View\ThemeResourceLoader;

class PageControllerExtension extends Extension
{
    public function onAfterInit()
    {
		Requirements::javascript(
			'innoweb/silverstripe-email-obfuscator: client/dist/javascript/email-obfuscator.js',
			['defer' => true]
		);
    }
}