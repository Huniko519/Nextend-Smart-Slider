<?php

namespace Nextend\Framework;

use Nextend\Framework\Platform\Platform;

global $allowedentitynames;
/**
 * @var string[] $allowedentitynames Array of KSES allowed HTML entitity names.
 * @since 1.0.0
 */
$allowedentitynames = is_array($allowedentitynames) ? $allowedentitynames : array(
    'nbsp',
    'iexcl',
    'cent',
    'pound',
    'curren',
    'yen',
    'brvbar',
    'sect',
    'uml',
    'copy',
    'ordf',
    'laquo',
    'not',
    'shy',
    'reg',
    'macr',
    'deg',
    'plusmn',
    'acute',
    'micro',
    'para',
    'middot',
    'cedil',
    'ordm',
    'raquo',
    'iquest',
    'Agrave',
    'Aacute',
    'Acirc',
    'Atilde',
    'Auml',
    'Aring',
    'AElig',
    'Ccedil',
    'Egrave',
    'Eacute',
    'Ecirc',
    'Euml',
    'Igrave',
    'Iacute',
    'Icirc',
    'Iuml',
    'ETH',
    'Ntilde',
    'Ograve',
    'Oacute',
    'Ocirc',
    'Otilde',
    'Ouml',
    'times',
    'Oslash',
    'Ugrave',
    'Uacute',
    'Ucirc',
    'Uuml',
    'Yacute',
    'THORN',
    'szlig',
    'agrave',
    'aacute',
    'acirc',
    'atilde',
    'auml',
    'aring',
    'aelig',
    'ccedil',
    'egrave',
    'eacute',
    'ecirc',
    'euml',
    'igrave',
    'iacute',
    'icirc',
    'iuml',
    'eth',
    'ntilde',
    'ograve',
    'oacute',
    'ocirc',
    'otilde',
    'ouml',
    'divide',
    'oslash',
    'ugrave',
    'uacute',
    'ucirc',
    'uuml',
    'yacute',
    'thorn',
    'yuml',
    'quot',
    'amp',
    'lt',
    'gt',
    'apos',
    'OElig',
    'oelig',
    'Scaron',
    'scaron',
    'Yuml',
    'circ',
    'tilde',
    'ensp',
    'emsp',
    'thinsp',
    'zwnj',
    'zwj',
    'lrm',
    'rlm',
    'ndash',
    'mdash',
    'lsquo',
    'rsquo',
    'sbquo',
    'ldquo',
    'rdquo',
    'bdquo',
    'dagger',
    'Dagger',
    'permil',
    'lsaquo',
    'rsaquo',
    'euro',
    'fnof',
    'Alpha',
    'Beta',
    'Gamma',
    'Delta',
    'Epsilon',
    'Zeta',
    'Eta',
    'Theta',
    'Iota',
    'Kappa',
    'Lambda',
    'Mu',
    'Nu',
    'Xi',
    'Omicron',
    'Pi',
    'Rho',
    'Sigma',
    'Tau',
    'Upsilon',
    'Phi',
    'Chi',
    'Psi',
    'Omega',
    'alpha',
    'beta',
    'gamma',
    'delta',
    'epsilon',
    'zeta',
    'eta',
    'theta',
    'iota',
    'kappa',
    'lambda',
    'mu',
    'nu',
    'xi',
    'omicron',
    'pi',
    'rho',
    'sigmaf',
    'sigma',
    'tau',
    'upsilon',
    'phi',
    'chi',
    'psi',
    'omega',
    'thetasym',
    'upsih',
    'piv',
    'bull',
    'hellip',
    'prime',
    'Prime',
    'oline',
    'frasl',
    'weierp',
    'image',
    'real',
    'trade',
    'alefsym',
    'larr',
    'uarr',
    'rarr',
    'darr',
    'harr',
    'crarr',
    'lArr',
    'uArr',
    'rArr',
    'dArr',
    'hArr',
    'forall',
    'part',
    'exist',
    'empty',
    'nabla',
    'isin',
    'notin',
    'ni',
    'prod',
    'sum',
    'minus',
    'lowast',
    'radic',
    'prop',
    'infin',
    'ang',
    'and',
    'or',
    'cap',
    'cup',
    'int',
    'sim',
    'cong',
    'asymp',
    'ne',
    'equiv',
    'le',
    'ge',
    'sub',
    'sup',
    'nsub',
    'sube',
    'supe',
    'oplus',
    'otimes',
    'perp',
    'sdot',
    'lceil',
    'rceil',
    'lfloor',
    'rfloor',
    'lang',
    'rang',
    'loz',
    'spades',
    'clubs',
    'hearts',
    'diams',
    'sup1',
    'sup2',
    'sup3',
    'frac14',
    'frac12',
    'frac34',
    'there4',
);

class Sanitize {

    private static function getCharset() {

        return Platform::getCharset();
    }

    /**
     * Checks for invalid UTF8 in a string.
     *
     * @param string $string The text which is to be checked.
     * @param bool   $strip  Optional. Whether to attempt to strip out invalid UTF8. Default is false.
     *
     * @return string The checked text.
     * @since     2.8.0
     *
     * @staticvar bool $is_utf8
     * @staticvar bool $utf8_pcre
     *
     */
    private static function check_invalid_utf8($string, $strip = false) {
        $string = (string)$string;

        if (0 === strlen($string)) {
            return '';
        }

        // Store the site charset as a static to avoid multiple calls to get_option()
        static $is_utf8 = null;
        if (!isset($is_utf8)) {
            $is_utf8 = in_array(self::getCharset(), array(
                'utf8',
                'utf-8',
                'UTF8',
                'UTF-8'
            ));
        }
        if (!$is_utf8) {
            return $string;
        }

        // Check for support for utf8 in the installed PCRE library once and store the result in a static
        static $utf8_pcre = null;
        if (!isset($utf8_pcre)) {
            $utf8_pcre = @preg_match('/^./u', 'a');
        }
        // We can't demand utf8 in the PCRE installation, so just return the string in those cases
        if (!$utf8_pcre) {
            return $string;
        }

        // preg_match fails when it encounters invalid UTF8 in $string
        if (1 === @preg_match('/^./us', $string)) {
            return $string;
        }

        // Attempt to strip the bad chars if requested (not recommended)
        if ($strip && function_exists('iconv')) {
            return iconv('utf-8', 'utf-8', $string);
        }

        return '';
    }

    /**
     * Converts a number of special characters into their HTML entities.
     *
     * Specifically deals with: &, <, >, ", and '.
     *
     * $quote_style can be set to ENT_COMPAT to encode " to
     * &quot;, or ENT_QUOTES to do both. Default is ENT_NOQUOTES where no quotes are encoded.
     *
     * @param string      $string        The text which is to be encoded.
     * @param int|string  $quote_style   Optional. Converts double quotes if set to ENT_COMPAT,
     *                                   both single and double if set to ENT_QUOTES or none if set to ENT_NOQUOTES.
     *                                   Also compatible with old values; converting single quotes if set to 'single',
     *                                   double if set to 'double' or both if otherwise set.
     *                                   Default is ENT_NOQUOTES.
     * @param string|bool $charset       Optional. The character encoding of the string. Default is false.
     * @param bool        $double_encode Optional. Whether to encode existing html entities. Default is false.
     *
     * @return string The encoded text with HTML entities.
     * @since     1.2.2
     * @access    private
     *
     * @staticvar string $_charset
     *
     */
    private static function _specialchars($string, $quote_style = ENT_NOQUOTES, $charset = false, $double_encode = false) {
        $string = (string)$string;

        if (0 === strlen($string)) return '';

        // Don't bother if there are no specialchars - saves some processing
        if (!preg_match('/[&<>"\']/', $string)) return $string;

        // Account for the previous behaviour of the function when the $quote_style is not an accepted value
        if (empty($quote_style)) $quote_style = ENT_NOQUOTES; else if (!in_array($quote_style, array(
            0,
            2,
            3,
            'single',
            'double'
        ), true)) $quote_style = ENT_QUOTES;

        // Store the site charset as a static to avoid multiple calls to wp_load_alloptions()
        if (!$charset) {
            static $_charset = null;
            if (!isset($_charset)) {
                $_charset = self::getCharset();
            }
            $charset = $_charset;
        }

        if (in_array($charset, array(
            'utf8',
            'utf-8',
            'UTF8'
        ))) $charset = 'UTF-8';

        $_quote_style = $quote_style;

        if ($quote_style === 'double') {
            $quote_style  = ENT_COMPAT;
            $_quote_style = ENT_COMPAT;
        } else if ($quote_style === 'single') {
            $quote_style = ENT_NOQUOTES;
        }

        if (!$double_encode) {
            // Guarantee every &entity; is valid, convert &garbage; into &amp;garbage;
            // This is required for PHP < 5.4.0 because ENT_HTML401 flag is unavailable.
            $string = self::kses_normalize_entities($string);
        }

        $string = @htmlspecialchars($string, $quote_style, $charset, $double_encode);

        // Back-compat.
        if ('single' === $_quote_style) $string = str_replace("'", '&#039;', $string);

        return $string;
    }

    /**
     * Converts and fixes HTML entities.
     *
     * This function normalizes HTML entities. It will convert `AT&T` to the correct
     * `AT&amp;T`, `&#00058;` to `&#58;`, `&#XYZZY;` to `&amp;#XYZZY;` and so on.
     *
     * @param string $string Content to normalize entities
     *
     * @return string Content with normalized entities
     * @since 1.0.0
     *
     */
    private static function kses_normalize_entities($string) {
        // Disarm all entities by converting & to &amp;
        $string = str_replace('&', '&amp;', $string);

        // Change back the allowed entities in our entity whitelist
        $string = preg_replace_callback('/&amp;([A-Za-z]{2,8}[0-9]{0,2});/', array(
            self::class,
            'kses_named_entities'
        ), $string);
        $string = preg_replace_callback('/&amp;#(0*[0-9]{1,7});/', array(
            self::class,
            'kses_normalize_entities2'
        ), $string);
        $string = preg_replace_callback('/&amp;#[Xx](0*[0-9A-Fa-f]{1,6});/', array(
            self::class,
            'kses_normalize_entities3'
        ), $string);

        return $string;
    }

    /**
     * Callback for kses_normalize_entities() regular expression.
     *
     * This function only accepts valid named entity references, which are finite,
     * case-sensitive, and highly scrutinized by HTML and XML validators.
     *
     * @param array  $matches preg_replace_callback() matches array
     *
     * @return string Correctly encoded entity
     * @since 3.0.0
     *
     * @global array $allowedentitynames
     *
     */
    public static function kses_named_entities($matches) {
        global $allowedentitynames;

        if (empty($matches[1])) return '';

        $i = $matches[1];

        return (!in_array($i, $allowedentitynames)) ? "&amp;$i;" : "&$i;";
    }

    /**
     * Callback for kses_normalize_entities() regular expression.
     *
     * This function helps kses_normalize_entities() to only accept 16-bit
     * values and nothing more for `&#number;` entities.
     *
     * @access private
     *
     * @param array $matches preg_replace_callback() matches array
     *
     * @return string Correctly encoded entity
     * @since  1.0.0
     *
     */
    public static function kses_normalize_entities2($matches) {
        if (empty($matches[1])) return '';

        $i = $matches[1];
        if (self::valid_unicode($i)) {
            $i = str_pad(ltrim($i, '0'), 3, '0', STR_PAD_LEFT);
            $i = "&#$i;";
        } else {
            $i = "&amp;#$i;";
        }

        return $i;
    }

    /**
     * Callback for kses_normalize_entities() for regular expression.
     *
     * This function helps kses_normalize_entities() to only accept valid Unicode
     * numeric entities in hex form.
     *
     * @access private
     *
     * @param array $matches preg_replace_callback() matches array
     *
     * @return string Correctly encoded entity
     */
    public static function kses_normalize_entities3($matches) {
        if (empty($matches[1])) return '';

        $hexchars = $matches[1];

        return (!self::valid_unicode(hexdec($hexchars))) ? "&amp;#x$hexchars;" : '&#x' . ltrim($hexchars, '0') . ';';
    }

    /**
     * Helper function to determine if a Unicode value is valid.
     *
     * @param int $i Unicode value
     *
     * @return bool True if the value was a valid Unicode number
     */
    private static function valid_unicode($i) {
        return ($i == 0x9 || $i == 0xa || $i == 0xd || ($i >= 0x20 && $i <= 0xd7ff) || ($i >= 0xe000 && $i <= 0xfffd) || ($i >= 0x10000 && $i <= 0x10ffff));
    }

    /**
     * Escape single quotes, htmlspecialchar " < > &, and fix line endings.
     *
     * Escapes text strings for echoing in JS. It is intended to be used for inline JS
     * (in a tag attribute, for example onclick="..."). Note that the strings have to
     * be in single quotes. The {@see 'js_escape'} filter is also applied here.
     *
     * @param string $text The text to be escaped.
     *
     * @return string Escaped text.
     * @since 2.8.0
     *
     */
    public static function esc_js($text) {
        $safe_text = self::check_invalid_utf8($text);
        $safe_text = self::_specialchars($safe_text, ENT_COMPAT);
        $safe_text = preg_replace('/&#(x)?0*(?(1)27|39);?/i', "'", stripslashes($safe_text));
        $safe_text = str_replace("\r", '', $safe_text);
        $safe_text = str_replace("\n", '\\n', addslashes($safe_text));

        return $safe_text;
    }

    /**
     * Escaping for HTML blocks.
     *
     * @param string $text
     *
     * @return string
     * @since 2.8.0
     *
     */
    public static function esc_html($text) {
        $safe_text = self::check_invalid_utf8($text);
        $safe_text = self::_specialchars($safe_text, ENT_QUOTES);

        return $safe_text;
    }

    /**
     * Escaping for HTML attributes.
     *
     * @param string $text
     *
     * @return string
     * @since 2.8.0
     *
     */
    public static function esc_attr($text) {
        $safe_text = self::check_invalid_utf8($text);
        $safe_text = self::_specialchars($safe_text, ENT_QUOTES);

        return $safe_text;
    }

    /**
     * Escaping for textarea values.
     *
     * @param string $text
     *
     * @return string
     * @since 3.1.0
     *
     */
    public static function esc_textarea($text) {
        $safe_text = htmlspecialchars($text, ENT_QUOTES, self::getCharset());

        return $safe_text;
    }

    public static function esc_css_value($text) {
        $safe_text = self::check_invalid_utf8($text);

        return preg_replace_callback('/<\/style.*?>/i', array(
            self::class,
            'esc_css_value_callback'
        ), $safe_text);
    }

    public static function esc_css_value_callback($a) {
        return self::esc_html($a[0]);
    }
}