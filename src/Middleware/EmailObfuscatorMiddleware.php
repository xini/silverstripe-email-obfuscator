<?php

namespace Innoweb\EmailObfuscator\Middleware;

use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Control\Middleware\HTTPMiddleware;
use SilverStripe\Core\Config\Configurable;
use Masterminds\HTML5;

class EmailObfuscatorMiddleware implements HTTPMiddleware
{
    use Configurable;

	/**
	 * @var string generic email regex
	 */
    private static $email_regex = '/[A-Z0-9\._%+\-]+@[A-Z0-9\.\-]+\.[A-Z]{2,8}/i';

	/**
	 * @var string email regex for plaintext, excluding values in html attribues
	 */
	private static $email_regex_plaintext = '/((?<!["])(?<![^ <>])([A-Z0-9\._%+\-]+@[A-Z0-9\.\-]+\.[A-Z]{2,8})(?![A-Z]))/i';

    /**
     * Obfuscate all email addreses contained in response body.
     * Emails in <head> are encoded using ASCII & HEX, emails in the <body> use JS to un-obfuscate
     *
     * @param HTTPRequest $request
     * @param callable $delegate
     * @return HTTPResponse
     */
    public function process(HTTPRequest $request, callable $delegate)
    {
        /** @var HTTPResponse $response */
        $response = $delegate($request);
        if (!$response) {
            return null;
        }

        if (
            $request->routeParams()['Controller'] != 'SilverStripe\Admin\AdminRootController'
            && $request->routeParams()['Controller'] != '%$SilverStripe\GraphQL\Controller.admin'
			&& strpos(strtolower($response->getHeader('content-type')), 'text/html') !== false
        ) {

            $html = $response->getBody();

            // replace email links usig DOMDocument parser
            $html5 = new HTML5();
            $dom = $html5->loadHTML($html ?? '');
            $links = $dom->getElementsByTagName('a');
            foreach ($links as $link) {
                if ($link->hasAttribute('href')
					&& (!$link->hasAttribute('class') || !str_contains($link->getAttribute('class'), 'skip-email-obfuscation'))
					&& preg_match($this->config()->email_regex, $link->getAttribute('href'), $matches)
				) {
                    $email = $matches[0];
                    $link->setAttribute('href', '#');
                    $link->setAttribute('rel', 'nofollow');
                    $link->setAttribute('data-eo', $this->getDataAttr($email));
                    if ($link->hasAttribute('title')) {
                        $link->setAttribute('title', $this->getLinkTitle($link->getAttribute('title')));
                    } else {
                        $link->setAttribute('title', $this->getLinkTitle($link->textContent));
                    }
                    if (preg_match($this->config()->email_regex, $link->textContent)) {
                        $link->setAttribute('data-eo-text', 'true');
                    }
                    $link->textContent = $this->getLinkText($link->textContent);
                }
            }
            $html = $html5->saveHTML($dom);

            // manual replacement using regex
            if (strpos($html, '</head>') !== false) {
                list($head, $body) = explode('</head>', $html);
                $parts = array(
                    // encode email contained in head with ASCII/HEX method
                    $head => $this->obfuscateEmailSimple($head),
                    // obfuscate plaintext email addresses for JS method
                    $body => $this->obfuscateEmailForJavascript($body)
                );
                $html = implode('</head>', $parts);
            } else {
                // obfuscate plaintext email addresses for JS method
                $html = $this->obfuscateEmailForJavascript($html);
            }

            $response->setBody($html);
        }

        return $response;
    }

    /**
     * Obfuscate all matching emails using the ACII & HEX method
     * @param string
     * @return string
     */
    private function obfuscateEmailSimple($html)
    {
        if (preg_match_all($this->config()->email_regex, $html, $matches)) {
            $searchstring = $matches[0];
            for ($i=0; $i < count($searchstring); $i++) {
                $html = str_replace(
                    $searchstring[$i],
                    $this->encodeASCIIHEX($searchstring[$i]),
                    $html
                );
            }
        }
        return $html;
    }

    /**
     * Obscure email address.
     *
     * @param string The email address
     * @return string The encoded (ASCII & hexadecimal) email address
     */
    private function encodeASCIIHEX($originalString)
    {
        $encodedString = '';
        $nowCodeString = '';
        $originalLength = strlen($originalString);
        for ($i = 0; $i < $originalLength; $i++) {
            $encodeMode = ($i % 2 == 0) ? 1 : 2; // Switch encoding odd/even
            switch ($encodeMode) {
                case 1: // Decimal code
                    $nowCodeString = '&#' . ord($originalString[$i]) . ';';
                    break;
                case 2: // Hexadecimal code
                    $nowCodeString = '&#x' . dechex(ord($originalString[$i])) . ';';
                    break;
                default:
                    return 'ERROR: wrong encoding mode.';
            }
            $encodedString .= $nowCodeString;
        }
        return $encodedString;
    }

    /*
     * Obfuscate all matching emails to be un-obfuscated using javascript
     * @param string
     * @return string
     */
    private function obfuscateEmailForJavascript($html)
    {
        $regex = array();

        // plaintext, only if not in html attribute
        $regex = $this->config()->email_regex_plaintext;

        $result = preg_replace_callback($regex, self::class . '::getReplacement', $html);

        return $result;
    }

    private function getReplacement($matches) {
        if ($matches) {
            if (!isset($matches[1])) { //email
                return "";
            }
            $email = $matches[1];
            $linktext = $matches[1];
            if (isset($matches[2])) {
                $linktext = $matches[2];
            }
            $textFlag = '';
            if (preg_match($this->config()->email_regex, $linktext)) {
                $textFlag = ' data-eo-text="true"';
            }
            $title = $this->getLinkTitle($linktext);
            $linktext = $this->getLinkText($linktext);
            $data = $this->getDataAttr($email);
            return '<a href="#" rel="nofollow" title="'.$title.'" data-eo="'.$data.'"'.$textFlag.'>'.$linktext.'</a>';
        } else {
            return "";
        }
    }

    private function getDataAttr($email) {

        $aLink = explode("@", $email);
        if (sizeof($aLink) == 2) {
            $email = str_rot13($aLink[0].'#'.str_replace(".", "#", $aLink[1]));
            return $email;
        }
        return "";
    }

    private function getLinkText($linktext) {
        $default = _t(__CLASS__ . ".NotDisplayed", '[E-Mail not displayed]');
        if (preg_match($this->config()->email_regex, $linktext)) {
            $linktext = $default;
        }
        return $linktext;
    }

    private function getLinkTitle($linktext) {
        $default = _t(__CLASS__ . ".NotDisplayedWithoutJavascript", 'E-Mail not displayed without javascript.');
        if (preg_match($this->config()->email_regex, $linktext)) {
            $linktext = $default;
        }
        return $linktext;
    }

}
