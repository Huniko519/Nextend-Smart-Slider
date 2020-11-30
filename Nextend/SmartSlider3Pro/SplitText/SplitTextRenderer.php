<?php

namespace Nextend\SmartSlider3Pro\SplitText;

use Nextend\Framework\Asset\Css\Css;
use Nextend\Framework\Misc\Base64;
use Nextend\Framework\Model\Section;
use Nextend\Framework\Settings;

class SplitTextRenderer {

    public static $pre = '';
    public static $sets = array();
    public static $animations = array();

    /**
     * @var Animation
     */
    public static $animation;

    public static $mode;

    public static function preLoad($animationId) {
        if (intval($animationId) > 0) {
            $animation = Section::getById($animationId, 'animation');
            if ($animation) {
                self::$sets[] = $animation['referencekey'];
            }
        }
    }

    public static function render($animation, $mode, $group, $pre = '') {

        $cssData = self::_render($animation, $mode, $pre);
        if ($cssData) {
            Css::addCode($cssData[1], $group);

            return $cssData[0];
        }

        return '';
    }

    public static function _render($animation, $mode, $pre = '') {
        self::$pre = $pre;
        if (intval($animation) > 0) {
            // Linked
            $animation = Section::getById($animation, 'animation');
            if ($animation) {
                if (is_string($animation['value'])) {

                    $decoded = $animation['value'];
                    if ($decoded[0] != '{') {
                        $decoded = Base64::decode($decoded);
                    }

                    $value = json_decode($decoded, true);
                } else {
                    $value = $animation['value'];
                }
                $selector = 'n2-animation-' . $animation['id'] . '-' . $mode;

                self::$sets[] = $animation['referencekey'];

                if (!isset(self::$animations[$animation['id']])) {
                    self::$animations[$animation['id']] = array(
                        $mode
                    );
                } else if (!in_array($mode, self::$animations[$animation['id']])) {
                    self::$animations[$animation['id']][] = $mode;
                }

                return array(
                    $selector . ' ',
                    self::renderStyle($mode, $pre, $selector, $value['data'])
                );
            }
        } else if ($animation != '') {
            $decoded = $animation;
            if ($decoded[0] != '{') {
                $decoded = Base64::decode($decoded);
            } else {
                $animation = Base64::encode($decoded);
            }

            $value = json_decode($decoded, true);
            if ($value) {
                $selector = 'n2-animation-' . md5($animation) . '-' . $mode;

                return array(
                    $selector . ' ',
                    self::renderStyle($mode, $pre, $selector, $value['data'])
                );
            }
        }

        return false;
    }

    private static function renderStyle($mode, $pre, $selector, $tabs) {
        $search  = array(
            '@pre',
            '@selector'
        );
        $replace = array(
            $pre,
            '.' . $selector
        );
        $tabs[0] = array_merge(array(
            'backgroundcolor' => 'ffffff00',
            'opacity'         => 100,
            'padding'         => '0|*|0|*|0|*|0|*|px',
            'boxshadow'       => '0|*|0|*|0|*|0|*|000000ff',
            'border'          => '0|*|solid|*|000000ff',
            'borderradius'    => '0',
            'extra'           => '',
        ), $tabs[0]);
        foreach ($tabs AS $k => $tab) {
            $search[]  = '@tab' . $k;
            $replace[] = self::$animation->animation($tab);
        }

        $template = '';
        foreach (self::$mode[$mode]['selectors'] AS $s => $animation) {
            if (!in_array($animation, $search) || !empty($replace[array_search($animation, $search)])) {
                $template .= $s . "{" . $animation . "}";
            }
        }

        return str_replace($search, $replace, $template);
    }
}


$frontendAccessibility = intval(Settings::get('frontend-accessibility', 1));

SplitTextRenderer::$mode = array(
    '0'              => array(
        'id'            => '0',
        'label'         => n2_('Single'),
        'tabs'          => array(
            n2_('Text')
        ),
        'renderOptions' => array(
            'combined' => false
        ),
        'preview'       => '<div class="{styleClassName}">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div>',
        'selectors'     => array(
            '@pre@selector' => '@tab'
        )
    ),
    'simple'         => array(
        'id'            => 'simple',
        'label'         => n2_('Simple'),
        'tabs'          => array(
            n2_('Normal')
        ),
        'renderOptions' => array(
            'combined' => true
        ),
        'preview'       => '<div class="{styleClassName}" style="width: 200px; height:100px;"></div>',
        'selectors'     => array(
            '@pre@selector' => '@tab0'
        )
    ),
    'box'            => array(
        'id'            => 'box',
        'label'         => n2_('Box'),
        'tabs'          => array(
            n2_('Normal'),
            n2_('Hover')
        ),
        'renderOptions' => array(
            'combined' => true
        ),
        'preview'       => '<div class="{styleClassName}" style="width: 200px; height:100px;"></div>',
        'selectors'     => array(
            '@pre@selector'       => '@tab0',
            '@pre@selector:HOVER' => '@tab1'
        )
    ),
    'button'         => array(
        'id'            => 'button',
        'label'         => n2_('Button'),
        'tabs'          => array(
            n2_('Normal'),
            n2_('Hover')
        ),
        'renderOptions' => array(
            'combined' => true
        ),
        'preview'       => '<div><a style="display:inline-block; margin:20px;" class="{styleClassName}" href="#" onclick="return false;">Button</a></div>',
        'selectors'     => $frontendAccessibility ? array(
            '@pre@selector'                                                  => '@tab0',
            '@pre@selector:Hover, @pre@selector:ACTIVE, @pre@selector:FOCUS' => '@tab1'
        ) : array(
            '@pre@selector, @pre@selector:FOCUS'        => '@tab0',
            '@pre@selector:Hover, @pre@selector:ACTIVE' => '@tab1'
        )
    ),
    'heading'        => array(
        'id'            => 'heading',
        'label'         => n2_('Heading'),
        'tabs'          => array(
            n2_('Normal'),
            n2_('Hover')
        ),
        'renderOptions' => array(
            'combined' => true
        ),
        'preview'       => '<div class="{styleClassName}">Heading</div>',
        'selectors'     => $frontendAccessibility ? array(
            '@pre@selector'                                                  => '@tab0',
            '@pre@selector:Hover, @pre@selector:ACTIVE, @pre@selector:FOCUS' => '@tab1'
        ) : array(
            '@pre@selector, @pre@selector:FOCUS'        => '@tab0',
            '@pre@selector:Hover, @pre@selector:ACTIVE' => '@tab1'
        )
    ),
    'heading-active' => array(
        'id'            => 'heading-active',
        'label'         => n2_('Heading active'),
        'tabs'          => array(
            n2_('Normal'),
            n2_('Active')
        ),
        'renderOptions' => array(
            'combined' => true
        ),
        'preview'       => '<div class="{styleClassName}">Heading</div>',
        'selectors'     => array(
            '@pre@selector'           => '@tab0',
            '@pre@selector.n2-active' => '@tab1'
        )
    ),
    'dot'            => array(
        'id'            => 'dot',
        'label'         => n2_('Dot'),
        'tabs'          => array(
            n2_('Normal'),
            n2_('Active')
        ),
        'renderOptions' => array(
            'combined' => true
        ),
        'preview'       => '<div><div class="{styleClassName}" style="display: inline-block; margin: 3px;"></div><div class="{styleClassName} n2-active" style="display: inline-block; margin: 3px;"></div><div class="{styleClassName}" style="display: inline-block; margin: 3px;"></div></div>',
        'selectors'     => array(
            '@pre@selector'                                => '@tab0',
            '@pre@selector.n2-active, @pre@selector:HOVER' => '@tab1'
        )
    ),
    'highlight'      => array(
        'id'            => 'highlight',
        'label'         => n2_('Highlight'),
        'tabs'          => array(
            n2_('Normal'),
            n2_('Highlight'),
            n2_('Hover')
        ),
        'renderOptions' => array(
            'combined' => true
        ),
        'preview'       => '<div class="{fontClassName}">' . n2_('Button') . '</div>',
        'selectors'     => $frontendAccessibility ? array(
            '@pre@selector'                                                                                                  => '@tab0',
            '@pre@selector .n2-highlighted'                                                                                  => '@tab1',
            '@pre@selector .n2-highlighted:HOVER, @pre@selector .n2-highlighted:ACTIVE, @pre@selector .n2-highlighted:FOCUS' => '@tab2'
        ) : array(
            '@pre@selector'                                                             => '@tab0',
            '@pre@selector .n2-highlighted, @pre@selector .n2-highlighted:FOCUS'        => '@tab1',
            '@pre@selector .n2-highlighted:HOVER, @pre@selector .n2-highlighted:ACTIVE' => '@tab2'
        )
    ),
);