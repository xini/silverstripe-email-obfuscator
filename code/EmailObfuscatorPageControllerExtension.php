<?php

class EmailObfuscatorPageControllerExtension extends Extension {

    public function onAfterInit() {
        Requirements::javascript(THIRDPARTY_DIR . '/jquery/jquery.js');
        Requirements::javascript('emailobfuscator/javascript/emailobfuscator.js');
	}

}
