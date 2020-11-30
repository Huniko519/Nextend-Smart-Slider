<?php


namespace Nextend\Framework\WordPress;


use Nextend\Framework\Asset\AssetManager;
use Nextend\Framework\Pattern\SingletonTrait;
use Nextend\WordPress\OutputBuffer;

class AssetInjector {

    use SingletonTrait;

    protected $js = '';
    protected $css = '';

    /**
     * @var OutputBuffer
     */
    protected $outputBuffer;

    private $headTokens = array();

    protected function init() {

        $this->outputBuffer = OutputBuffer::getInstance();
        if (defined('SMART_SLIDER_OB_START') && SMART_SLIDER_OB_START >= 0) {
            $this->outputBuffer->setExtraObStart(SMART_SLIDER_OB_START);
        }

        $this->addInjectCSSComment();

        add_filter('wordpress_prepare_output', array(
            $this,
            'prepareOutput'
        ));
    }

    public function prepareOutput($buffer) {
        static $once = false;
        if (!$once) {
            $once = true;
            $this->finalizeCssJs();

            if (!empty($this->css)) {
                if (strpos($buffer, '<!--n2css-->') !== false) {
                    $buffer = str_replace('<!--n2css-->', $this->css, $buffer);

                    $this->css = '';
                } else {
                    $parts = preg_split('/<\/head[\s]*>/i', $buffer, 2);
                    // There might be no head and it would result a notice.
                    if (count($parts) == 2) {
                        list($head, $body) = $parts;
                        /**
                         * We must tokenize the HTML comments in the head to prepare for condition CSS/scripts
                         * Eg.: <!--[if lt IE 9]><link rel='stylesheet' href='ie8.css?ver=1.0' type='text/css' media='all' /> <![endif]-->
                         */
                        $head = preg_replace_callback('/<!--.*?-->/s', array(
                            $this,
                            'tokenizeHead'
                        ), $head);


                        $head = preg_replace_callback('/<noscript>.*?<\/noscript>/s', array(
                            $this,
                            'tokenizeHead'
                        ), $head);

                        /**
                         * Find the first <script> tag with src attribute
                         */
                        $pattern = '/<script[^>]+src=[\'"][^>"\']*[\'"]/si';
                        if (preg_match($pattern, $head, $matches)) {

                            $splitBy = $matches[0];

                            $headParts = preg_split($pattern, $head, 2);

                            /**
                             * Find the last stylesheet before the first script
                             */
                            if (preg_match_all('/<link[^>]*rel=[\'"]stylesheet[\'"][^>]*>/si', $headParts[0], $matches, PREG_SET_ORDER)) {
                                /**
                                 * If there is a match we insert our stylesheet after that.
                                 */
                                $match          = array_pop($matches);
                                $lastStylesheet = $match[0];

                                $headParts[0] = str_replace($lastStylesheet, $lastStylesheet . $this->css, $headParts[0]);

                                $this->css = '';
                            } else {
                                /**
                                 * No stylesheet found, so  we insert our stylesheet before the first <script>.
                                 */
                                $headParts[0] .= $this->css;

                                $this->css = '';
                            }

                            $head = implode($splitBy, $headParts);

                            /**
                             * Restore HTML comments
                             */
                            $head = preg_replace_callback('/<!--TOKEN([0-9]+)-->/', array(
                                $this,
                                'restoreHeadTokens'
                            ), $head);

                            $buffer = $head . '</head>' . $body;
                        }
                    }
                }
            }

            if ($this->css != '' || $this->js != '') {
                $parts = preg_split('/<\/head[\s]*>/', $buffer, 2);

                return implode($this->css . $this->js . '</head>', $parts);
            }
        }

        return $buffer;
    }

    public function tokenizeHead($matches) {

        $index = count($this->headTokens);

        $this->headTokens[$index] = $matches[0];

        return '<!--TOKEN' . $index . '-->';
    }

    public function restoreHeadTokens($matches) {

        return $this->headTokens[$matches[1]];
    }

    private function finalizeCssJs() {
        static $finalized = false;
        if (!$finalized) {
            $finalized = true;

            if (class_exists('\\Nextend\\Framework\\Asset\\AssetManager', false)) {
                $this->css = AssetManager::getCSS();
                $this->js  = AssetManager::getJs();
            }
        }

        return true;
    }

    public function addInjectCSSComment() {

        remove_action('wp_print_scripts', array(
            $this,
            'injectCSSComment'
        ));
    }

    public function removeInjectCSSComment() {

        add_action('wp_print_scripts', array(
            $this,
            'injectCSSComment'
        ));
    }

    public function injectCSSComment() {
        static $once;
        if (!$once) {
            echo "<!--n2css-->";
            $once = true;
        }
    }
}