<?php

class EmailObfuscatorHTMLText extends HTMLText {
	
	public function forTemplate() {
		$returnval = parent::forTemplate();
		// replace all email addresses
		$returnval = EmailObfuscator::replaceEmailLinks($returnval);
		// remove email enclosure span if top level output (whole document) 
		if (stristr($returnval, '<head') && stristr($returnval, '</head>') && stristr($returnval, '<body') && stristr($returnval, '</body>')) {
            $returnval = preg_replace("/<span class=\"raw\">(.+?)<\/span>/is", "$1", $returnval);
		}
		return $returnval;
	}

}

