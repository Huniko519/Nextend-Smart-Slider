<?php


namespace Nextend\SmartSlider3\Widget;


use Nextend\SmartSlider3\Slider\Slider;

class SliderWidget {

    /** @var AbstractWidgetFrontend[] */
    public $enabledWidgets = array();

    public $widgets = array();

    private $above = array();
    private $aboveHTML = '';
    private $below = array();
    private $belowHTML = '';

    private $positions = array(
        2  => array(
            'side'       => 'both',
            'modifierH'  => 1,
            'modifierV'  => 1,
            'stack'      => 'horizontal',
            'horizontal' => array(
                'side'     => 'left',
                'position' => '0'
            ),

            'vertical' => array(
                'side'     => 'top',
                'position' => '0'
            )
        ),
        3  => array(
            'side'       => 'vertical',
            'modifierH'  => 1,
            'modifierV'  => 1,
            'stack'      => 'vertical',
            'horizontal' => array(
                'side'     => 'left',
                'position' => 'sliderWidth/2-{widgetname}width/2'
            ),

            'vertical' => array(
                'side'     => 'top',
                'position' => '0'
            )
        ),
        4  => array(
            'side'       => 'both',
            'modifierH'  => 1,
            'modifierV'  => 1,
            'stack'      => 'horizontal',
            'horizontal' => array(
                'side'     => 'right',
                'position' => '0'
            ),

            'vertical' => array(
                'side'     => 'top',
                'position' => '0'
            )
        ),
        5  => array(
            'side'       => 'horizontal',
            'modifierH'  => 1,
            'modifierV'  => 1,
            'stack'      => 'horizontal',
            'horizontal' => array(
                'side'     => 'right',
                'position' => 'width'
            ),

            'vertical' => array(
                'side'     => 'top',
                'position' => 'sliderHeight/2-{widgetname}height/2'
            )
        ),
        6  => array(
            'side'       => 'horizontal',
            'modifierH'  => 1,
            'modifierV'  => 1,
            'stack'      => 'horizontal',
            'horizontal' => array(
                'side'     => 'left',
                'position' => '0'
            ),

            'vertical' => array(
                'side'     => 'top',
                'position' => 'sliderHeight/2-{widgetname}height/2'
            )
        ),
        7  => array(
            'side'       => 'horizontal',
            'modifierH'  => 1,
            'modifierV'  => 1,
            'stack'      => 'horizontal',
            'horizontal' => array(
                'side'     => 'right',
                'position' => '0'
            ),

            'vertical' => array(
                'side'     => 'top',
                'position' => 'sliderHeight/2-{widgetname}height/2'
            )
        ),
        8  => array(
            'side'       => 'horizontal',
            'modifierH'  => 1,
            'modifierV'  => 1,
            'stack'      => 'horizontal',
            'horizontal' => array(
                'side'     => 'left',
                'position' => 'width'
            ),

            'vertical' => array(
                'side'     => 'top',
                'position' => 'sliderHeight/2-{widgetname}height/2'
            )
        ),
        9  => array(
            'side'       => 'both',
            'modifierH'  => 1,
            'modifierV'  => 1,
            'stack'      => 'horizontal',
            'horizontal' => array(
                'side'     => 'left',
                'position' => '0'
            ),

            'vertical' => array(
                'side'     => 'bottom',
                'position' => '0'
            )
        ),
        10 => array(
            'side'       => 'vertical',
            'modifierH'  => 1,
            'modifierV'  => 1,
            'stack'      => 'vertical',
            'horizontal' => array(
                'side'     => 'left',
                'position' => 'sliderWidth/2-{widgetname}width/2'
            ),

            'vertical' => array(
                'side'     => 'bottom',
                'position' => '0'
            )
        ),
        11 => array(
            'side'       => 'both',
            'modifierH'  => 1,
            'modifierV'  => 1,
            'stack'      => 'horizontal',
            'horizontal' => array(
                'side'     => 'right',
                'position' => '0'
            ),

            'vertical' => array(
                'side'     => 'bottom',
                'position' => '0'
            )
        )
    );


    /**
     * @param $slider Slider
     */
    public function __construct($slider) {

        $params = $slider->params;

        $widgetGroups = WidgetGroupFactory::getGroups();

        foreach ($widgetGroups AS $groupName => $group) {
            $isEnabled = false;
            if ($params->has('widget-' . $group->getName() . '-enabled')) {
                $isEnabled = $params->get('widget-' . $group->getName() . '-enabled', 0);
            } else {
                $oldValue = $params->get('widget' . $groupName);
                if ($oldValue != 'disabled' && $oldValue != '') {
                    $isEnabled = true;
                }
            }

            if ($isEnabled) {
                $widget = $group->getWidget($params->get('widget' . $groupName));
                if ($widget) {
                    $this->enabledWidgets[$groupName] = $widget->createFrontend($this);
                }
            }
        }

        $positions = array();
        foreach ($this->enabledWidgets AS $widget) {
            $params->fillDefault($widget->getDefaults());

            $positions += $widget->getPositions($params);
        }

        $this->makePositions($positions, $params);

        foreach ($this->enabledWidgets AS $widgetName => $widget) {

            $rendered = $widget->render($slider, $slider->elementId, $params);
            if (is_array($rendered)) {
                $this->widgets = array_merge($this->widgets, $rendered);
            } else {
                $this->widgets[$widgetName] = $rendered;
            }
        }
        foreach ($this->above AS $name) {
            $this->aboveHTML .= $this->widgets[$name] . "\n";
            unset($this->widgets[$name]);
        }
        foreach ($this->below AS $name) {
            $this->belowHTML .= $this->widgets[$name] . "\n";
            unset($this->widgets[$name]);
        }
    }

    function echoAbove() {
        echo $this->aboveHTML;
    }

    function echoBelow() {
        echo $this->belowHTML;
    }

    function echoOnce($k) {
        if (isset($this->widgets[$k])) {
            echo $this->widgets[$k];
            unset($this->widgets[$k]);
        }
    }

    function echoOne($k) {
        if (isset($this->widgets[$k])) {
            echo $this->widgets[$k];
        }
    }

    function echoRemainder() {
        foreach ($this->widgets AS $v) {
            echo $v . "\n";
        }
    }

    function makePositions($positions, &$params) {
        $priority = array(
            array(),
            array(),
            array(),
            array()
        );
        foreach ($positions AS $k => $v) {
            list($key, $name) = $v;
            if ($params->get($key . 'mode') == 'simple') {
                $priority[intval($params->get($key . 'stack', 1)) - 1][] = array(
                    $k => $positions[$k]
                );
            } else {
                unset($positions[$k]);
            }
        }

        foreach ($priority AS $current) {
            foreach ($current AS $positions) {
                foreach ($positions AS $k => $v) {
                    $this->makePositionByIndex($params, $v[0], $v[1]);
                }
            }
        }
    }

    function makePositionByIndex(&$params, $key, $name) {

        $values = array();

        $area = intval($params->get($key . 'area'));
        if ($area == 1) {
            $this->above[] = $name;
            $params->set($key . 'mode', 'above');

            return;
        } else if ($area == 12) {
            $this->below[] = $name;
            $params->set($key . 'mode', 'below');

            return;
        }

        $position = $this->positions[$area];

        $values['horizontal']          = $position['horizontal']['side'];
        $values['horizontal-position'] = str_replace('{widgetname}', $name, $position['horizontal']['position']);
        $values['horizontal-unit']     = 'px';

        $values['vertical']          = $position['vertical']['side'];
        $values['vertical-position'] = str_replace('{widgetname}', $name, $position['vertical']['position']);
        $values['vertical-unit']     = 'px';

        $offset = intval($params->get($key . 'offset', 0));

        if ($offset != 0 && ($position['side'] == 'vertical' || $position['side'] == 'both')) {
            $values['vertical-position'] .= "+" . $position['modifierV'] * $offset;
        }

        if ($offset != 0 && ($position['side'] == 'horizontal' || $position['side'] == 'both')) {
            $values['horizontal-position'] .= "+" . $position['modifierH'] * $offset;
        }

        if ($position['stack'] == 'vertical') {
            if ($offset > 0) {
                $calc = "({$name}height > 0 ? {$name}height+{$offset} : 0)";
            } else {
                $calc = "{$name}height";
            }
            if ($position['modifierV'] != 1) {
                $calc = $position['modifierV'] . "*{$calc}";
            }
            $this->positions[$area]['vertical']['position'] .= '+' . $calc;
        }

        if ($position['stack'] == 'horizontal') {
            if ($offset > 0) {
                $calc = "({$name}width > 0 ? {$name}width+{$offset} : 0)";
            } else {
                $calc = "{$name}width";
            }
            if ($position['modifierH'] != 1) {
                $calc = $position['modifierH'] . "*{$calc}";
            }
            $this->positions[$area]['horizontal']['position'] .= '+' . $calc;
        }

        foreach ($values AS $k => $v) {
            $params->set($key . $k, $v);
        }
    }
}