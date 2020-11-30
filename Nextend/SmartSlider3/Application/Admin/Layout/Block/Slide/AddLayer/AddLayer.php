<?php

namespace Nextend\SmartSlider3\Application\Admin\Layout\Block\Slide\AddLayer;

use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Application\Admin\Layout\Block\Core\FreeNeedMore\BlockFreeNeedMore;
use Nextend\SmartSlider3\Application\Admin\Layout\Block\Forms\Button\BlockButtonIconCode;
use Nextend\SmartSlider3\Application\Admin\Layout\Block\Forms\Button\BlockButtonPlainIcon;

/**
 * @var $this BlockAddLayer
 */
?>
<div class="n2_add_layer">
    <div class="n2_add_layer__bar">

        <div class="n2_add_layer__bar_top">
            <?php
            $buttonAddLayer = new BlockButtonIconCode($this);
            $buttonAddLayer->addClass('n2_add_layer__bar_button');
            $buttonAddLayer->addClass('n2_add_layer__bar_button_add');
            $buttonAddLayer->setIcon('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"><path fill="currentColor" d="M13 3a1 1 0 0 1 1 1v6h6a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-6v6a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-6H4a1 1 0 0 1-1-1v-2a1 1 0 0 1 1-1h6V4a1 1 0 0 1 1-1h2z"/></svg>');
            $buttonAddLayer->setGreen();
            $buttonAddLayer->addAttribute('data-n2tip', n2_('Add Layer'));
            $buttonAddLayer->display();

            $this->displayAddShortcut('heading', 'ssi_24 ssi_24--heading', n2_('Heading'));
            $this->displayAddShortcut('text', 'ssi_24 ssi_24--text', n2_('Text'));
            $this->displayAddShortcut('image', 'ssi_24 ssi_24--image', n2_('Image'));
            $this->displayAddShortcut('button', 'ssi_24 ssi_24--button', n2_('Button'));
            $this->displayAddShortcut('structure-2col', 'ssi_24 ssi_24--col2', n2_('Row'));
            ?>
        </div>
        <div class="n2_add_layer__bar_bottom">
            <?php
            $buttonPlay = new BlockButtonPlainIcon($this);
            $buttonPlay->addClass('n2_timeline_control_play_pause');
            $buttonPlay->addClass('n2_add_layer__bar_button');
            $buttonPlay->setIcon('ssi_24 ssi_24--play');
            $buttonPlay->addAttribute('data-n2tip', n2_('Play animations'));
            $buttonPlay->addAttribute('data-n2tipv', -20);
            $buttonPlay->display();

            $buttonTimeline = new BlockButtonPlainIcon($this);
            $buttonTimeline->addClass('n2_add_layer__bar_button');
            $buttonTimeline->addClass('n2_slide_editor_timeline_toggle n2_slide_editor_timeline_toggle--show');
            $buttonTimeline->setIcon('ssi_24 ssi_24--timeline');
            $buttonTimeline->addAttribute('data-n2tip', n2_('Timeline'));
            $buttonTimeline->addAttribute('data-n2tipv', -20);
            $buttonTimeline->display();
        
            ?>
        </div>
    </div>
    <div class="n2_add_layer__more n2_form--dark">
        <div class="n2_add_layer__more_tab_buttons">
            <div class="n2_add_layer__more_tab_button" data-related-tab="layers">
                <div class="n2_add_layer__more_tab_button_icon">
                    <i class="ssi_24 ssi_24--layers"></i>
                </div>
                <div class="n2_add_layer__more_tab_button_label">
                    <?php echo n2_('Layers'); ?>
                </div>
            </div>
            <div class="n2_add_layer__more_tab_button" data-related-tab="library">
                <div class="n2_add_layer__more_tab_button_icon">
                    <i class="ssi_24 ssi_24--smart"></i>
                </div>
                <div class="n2_add_layer__more_tab_button_label">
                    <?php echo n2_('Library'); ?>
                </div>
            </div>
        </div>
        <div class="n2_add_layer__more_tab" data-tab="layers">
            <div class="n2_add_layer__more_layers">
                <?php
                foreach ($this->getGroups() AS $groupLabel => $boxes):
                    ?>
                    <div class="n2_add_layer_group">
                        <div class="n2_add_layer_group__label">
                            <?php echo $groupLabel; ?>
                        </div>
                        <div class="n2_add_layer_group__content">
                            <?php
                            foreach ($boxes AS $box):
                                echo Html::openTag('div', array(
                                        'class' => 'n2_add_layer_box'
                                    ) + $box['attributes']);
                                ?>
                                <div class="n2_add_layer_box__icon">
                                    <i class="<?php echo $box['icon'] ?>"></i>
                                </div>
                                <div class="n2_add_layer_box__label_wrap">
                                    <div class="n2_add_layer_box__label">
                                        <?php echo $box['label']; ?>
                                    </div>
                                </div>
                                <?php
                                echo Html::closeTag('div');
                            endforeach;
                            ?>
                        </div>
                    </div>
                <?php
                endforeach;
                ?>
                <?php
                ?>
            </div>
            <div class="n2_add_layer__more_position n2_add_layer_position" data-position="default">
                <div class="n2_add_layer_position__label n2_add_layer_position__default_label">
                    <?php n2_e('Default'); ?>
                </div>
                <div class="n2_add_layer_position__switch_container">
                    <div class="n2_add_layer_position__switch">
                        <div class="n2_add_layer_position__switch_dot">

                        </div>
                    </div>
                </div>
                <div class=" n2_add_layer_position__label n2_add_layer_position__absolute_label">
                    <?php n2_e('Absolute'); ?>
                </div>
            </div>
        </div>
        <div class="n2_add_layer__more_tab n2_add_layer_library" data-tab="library">

        </div>
    </div>
</div>
