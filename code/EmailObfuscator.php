<?php
class EmailObfuscator extends Object{
	
	public static function replaceEmailLinks($input) {
	    
		if (strpos(getenv('REQUEST_URI'), '/admin') !== false) {
			return $input;
		}
		
		$regex = array();
		
		// html mailto links
		$regex[0]  = '/<a\s*'; // Start of anchor tag
		$regex[0] .= '[A-Z0-9+\'" \-_]*?\s*'; // Any attributes or spaces that may or may not exist
		$regex[0] .= 'href=[\'"]+?\s*mailto:(?P<email>\S+)\s*[\'"]+?'; // Grab the link
		$regex[0] .= '\s*[A-Z0-9+\'" \-_]?\s*>\s*'; // Any attributes or spaces that may or may not exist before closing tag
		$regex[0] .= '(?P<linktext>[^<]*)'; // Grab the name
		$regex[0] .= '<\/a>/i'; // Any number of spaces between the closing anchor tag (case insensitive)
		
		// only if not in html value attribute and not preceded by raw-span
		//$regex[1] = '/(?P<email>[A-Z0-9\._%+\-]+@[A-Z0-9\.\-]+\.[A-Z]{2,6})/i'; // Grab the link
		$regex[1] = '/((?<!")(?<!<span class="raw">)(?<![A-Z0-9\._%+\-])([A-Z0-9\._%+\-]+@[A-Z0-9\.\-]+\.[A-Z]{2,8})(?![A-Z]))/i';

		$result = preg_replace_callback($regex, array('EmailObfuscator', 'self::getContactLink'), $input);
		
		return $result;
		
	}
	
	public static function encloseRawEmailLinks($input) {
		return preg_replace('/([A-Z0-9\._%+\-]+@[A-Z0-9\.\-]+\.[A-Z]{2,6})/i', '<span class="raw">${1}</span>', $input);
	}
	
	private static function getContactLink($treffer) {
		if ($treffer) {
			if (!isset($treffer[1])) { //email
				return "";
			}
			$email = $treffer[1];
//			debug::show("email: ".$email);
			$linktext = $treffer[1];
			if (isset($treffer[2])) {
				$linktext = $treffer[2];
			}
//			debug::show("linktext: ".$linktext);
			$class = '';
			if (strpos($linktext, "@") > 0) {
				$class = 'replacetext';
			}
			$title = self::getLinkTitle($linktext);
			$linktext = self::getLinkText($linktext);
			return '<a class="'.$class.'" rel="nofollow" title="'.$title.'" data-eo="'.self::getDataAttr($email).'" href="'.self::getLink().'">'.$linktext.'</a>';
		} else {
			return "";
		}
	}
	
	private static function getLink() {
		
	    if (
	        class_exists('Multisites') 
	        && ($site = Multisites::inst()->getCurrentSite())
	        && $site->hasOneComponent('ContactPage')
	        && ($page = $site->ContactPage())
	        && $page->exists()
	    ) {
	        return Controller::join_links($page->Link());
	    } else if (
	        ($config = SiteConfig::current_site_config())
	        && $config->hasOneComponent('ContactPage')
	        && ($page = $config->ContactPage())
	        && $page->exists()
	    ) {
	        return Controller::join_links($page->Link());
	    }
		return "#";
	}
	
	private static function getDataAttr($email) {
	    
	    $aLink = explode("@", $email);
	    if (sizeof($aLink) == 2) {
	        $email = str_rot13($aLink[0].'+'.str_replace(".", "+", $aLink[1]));
	        return $email;
	    }
	    return "#";
	}
	
	private static function getLinkText($linktext) {
		$default = _t("EmailObfuscator.EMAILVIAFORM", '[E-Mail via form]');
		if (strpos($linktext, "@") > 0) {
			$linktext = $default;
		}
		return $linktext;
	}

	private static function getLinkTitle($linktext) {
		$default = _t("EmailObfuscator.NODISPLAY", 'E-Mail not shown without javascript enabled.');
		if (strpos($linktext, "@") > 0) {
			$linktext = $default;
		} else {
			$linktext = _t("EmailObfuscator.EMAILVIAFORMTO", 'E-Mail via form to').' '.$linktext;
		}
		return $linktext;
	}
	
	public static function unobscure($string) {
		$string = str_rot13($string);
		$string = preg_replace('/\+/i', '@', $string, 1);
		$string = preg_replace('/\+/i', '.', $string);
		return $string;
	}
	
}

