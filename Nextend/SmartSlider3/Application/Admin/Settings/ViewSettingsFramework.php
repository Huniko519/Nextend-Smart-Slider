<?php


namespace Nextend\SmartSlider3\Application\Admin\Settings;


use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\OnOff;
use Nextend\Framework\Form\Element\Select;
use Nextend\Framework\Form\Element\Text;
use Nextend\Framework\Form\Element\Token;
use Nextend\Framework\Form\Form;
use Nextend\Framework\Settings;

class ViewSettingsFramework extends AbstractViewSettings {

    protected $active = 'framework';

    public function display() {

        parent::display();

        $this->layout->addBreadcrumb(n2_('Framework'), '');

        $this->layout->addContent($this->render('Framework'));

        $this->layout->render();
    }

    public function renderForm() {

        $values = Settings::getAll();

        $form = new Form($this, 'global');
        $form->loadArray($values);


        $table = new ContainerTable($form->getContainer(), 'framework', n2_('Framework'));

        $row1 = $table->createRow('framework-1');

        new Token($row1);
        new OnOff($row1, 'protocol-relative', n2_('Use protocol-relative URL'), 1, array(
            'tipLabel'       => n2_('Use protocol-relative URL'),
            'tipDescription' => n2_('Loads the URLs without a http or https protocol.')
        ));

        new OnOff($row1, 'force-english-backend', n2_('English UI'), 0, array(
            'tipLabel'       => n2_('English UI'),
            'tipDescription' => n2_('You can keep using Smart Slider 3 in English, even if your backend isn\'t in English.')
        ));

        new OnOff($row1, 'frontend-accessibility', n2_('Improved frontend accessibility'), 1, array(
            'tipLabel'       => n2_('Improved frontend accessibility'),
            'tipDescription' => n2_('Keeps the clicked element (like a button) in focus unless the focus is changed by clicking away.')
        ));


        $table = new ContainerTable($form->getContainer(), 'javascript', 'JavaScript');

        $row1 = $table->createRow('javascript-1');

        new OnOff($row1, 'jquery', n2_('Load jQuery on frontend'), 1);
        new OnOff($row1, 'gsap', n2_('Load GSAP on frontend'), 1);
    
        new OnOff($row1, 'async', n2_('Async'), 0);
        new OnOff($row1, 'combine-js', n2_('Combine'), 0);
        new Text($row1, 'scriptattributes', n2_('Script attributes'), '');

        new Select($row1, 'javascript-inline', n2_('Slider\'s inline JavaScript'), 'head', array(
            'options' => array(
                'head' => n2_('Head'),
                'body' => n2_('Into the slider')
            )
        ));


        $table = new ContainerTable($form->getContainer(), 'css', 'CSS');

        $row1 = $table->createRow('css-1');

        new Select($row1, 'css-mode', n2_('CSS mode'), 'normal', array(
            'options' => array(
                'normal' => n2_('Inline'),
                'inline' => n2_('Inline at head'),
                'async'  => n2_('Async'),
            )
        ));
        new OnOff($row1, 'icon-fa', n2_('Load Font Awesome 4'), 1);
    

        $table = new ContainerTable($form->getContainer(), 'requests', n2_('API requests'));

        $row1 = $table->createRow('requests-1');

        new OnOff($row1, 'curl', sprintf(n2_x('Use %s', 'Curl'), 'Curl'), 1);

        new OnOff($row1, 'curl-clean-proxy', sprintf(n2_x('Clean %s', 'curl proxy'), 'curl proxy'), 0);


        $form->render();
    }
}