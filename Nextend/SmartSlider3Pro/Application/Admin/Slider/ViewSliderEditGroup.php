<?php


namespace Nextend\SmartSlider3Pro\Application\Admin\Slider;


use Nextend\Framework\Acl\Acl;
use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\Hidden;
use Nextend\Framework\Form\Element\Text;
use Nextend\Framework\Form\Element\Text\FieldImage;
use Nextend\Framework\Form\Fieldset\FieldsetRowPlain;
use Nextend\Framework\Form\Form;
use Nextend\Framework\Sanitize;
use Nextend\Framework\View\AbstractView;
use Nextend\SmartSlider3\Application\Admin\Layout\Block\Core\Header\BlockHeader;
use Nextend\SmartSlider3\Application\Admin\Layout\Block\Core\TopBarMain\BlockTopBarMain;
use Nextend\SmartSlider3\Application\Admin\Layout\Block\Forms\Button\BlockButton;
use Nextend\SmartSlider3\Application\Admin\Layout\Block\Forms\Button\BlockButtonBack;
use Nextend\SmartSlider3\Application\Admin\Layout\Block\Forms\Button\BlockButtonPlainIcon;
use Nextend\SmartSlider3\Application\Admin\Layout\Block\Forms\Button\BlockButtonSave;
use Nextend\SmartSlider3\Application\Admin\Layout\Block\Forms\FloatingMenu\BlockFloatingMenu;
use Nextend\SmartSlider3\Application\Admin\Layout\Block\Forms\FloatingMenu\BlockFloatingMenuItem;
use Nextend\SmartSlider3\Application\Admin\Layout\Block\Slider\SliderManager\BlockSliderManager;
use Nextend\SmartSlider3\Application\Admin\Layout\LayoutDefault;
use Nextend\SmartSlider3\Application\Admin\TraitAdminUrl;
use Nextend\SmartSlider3\Form\Element\PublishSlider;

class ViewSliderEditGroup extends AbstractView {

    use TraitAdminUrl;

    protected $groupID = 0;

    protected $slider;

    /**
     * @var BlockHeader
     */
    protected $blockHeader;

    protected $formData = array();

    /**
     * @param array $slider
     */
    public function setSlider($slider) {
        $this->slider = $slider;
    }

    public function getSlider() {
        return $this->slider;
    }

    public function display() {

        $this->layout = new LayoutDefault($this);

        $this->layout->addBreadcrumb(Sanitize::esc_html($this->slider['title']), 'ssi_16 ssi_16--folderclosed');


        $topBar = new BlockTopBarMain($this);

        $buttonSave = new BlockButtonSave($this);
        $buttonSave->addClass('n2_button--inactive');
        $buttonSave->addClass('n2_group_settings_save');
        $topBar->addPrimaryBlock($buttonSave);

        $buttonBack = new BlockButtonBack($this);
        $buttonBack->setUrl($this->getUrlDashboard());
        $buttonBack->addClass('n2_group_settings_back');
        $topBar->addPrimaryBlock($buttonBack);

        $buttonPreview = new BlockButtonPlainIcon($this);
        $buttonPreview->addClass('n2_top_bar_button_icon');
        $buttonPreview->addClass('n2_top_bar_main__preview');
        $buttonPreview->setIcon('ssi_24 ssi_24--preview');
        $buttonPreview->addAttribute('data-n2tip', n2_('Preview'));
        $buttonPreview->setUrl($this->getUrlPreviewIndex($this->slider['id']));
        $topBar->addPrimaryBlock($buttonPreview);

        $this->displayHeader();


        $this->layout->setTopBar($topBar->toHTML());

        $this->layout->addContent($this->render('EditGroup'));


        $this->layout->render();
    }

    protected function displayHeader() {


        $this->blockHeader = new BlockHeader($this);
        $this->blockHeader->setHeading($this->slider['title']);
        $this->blockHeader->setHeadingAfter('ID: ' . $this->slider['id']);

        $this->addHeaderActions();

        $this->layout->addContentBlock($this->blockHeader);
    }

    private function addHeaderActions() {

        $accessEdit   = Acl::canDo('smartslider_edit', $this);
        $accessDelete = Acl::canDo('smartslider_delete', $this);

        if ($accessEdit || $accessDelete) {

            $sliderid = $this->slider['id'];

            $actionsMenu = new BlockFloatingMenu($this);

            $actions = new BlockButton($this);
            $actions->setBig();
            $actions->setLabel(n2_('Actions'));
            $actions->setIcon('ssi_16 ssi_16--buttonarrow');
            $actionsMenu->setButton($actions);


            if ($accessEdit) {

                $item = new BlockFloatingMenuItem($this);
                $item->setLabel(n2_('Clear cache'));
                $item->setIcon('ssi_16 ssi_16--reset');
                $item->setUrl($this->getUrlSliderClearCache($sliderid));
                $actionsMenu->addMenuItem($item);

                $item = new BlockFloatingMenuItem($this);
                $item->setLabel(sprintf(n2_('Export %1$s as HTML'), n2_('Group')));
                $item->setIcon('ssi_16 ssi_16--download');
                $item->setUrl($this->getUrlSliderExportHtml($sliderid));
                $actionsMenu->addMenuItem($item);

                $item = new BlockFloatingMenuItem($this);
                $item->setLabel(n2_('Export'));
                $item->setIcon('ssi_16 ssi_16--download');
                $item->setUrl($this->getUrlSliderExport($sliderid));
                $actionsMenu->addMenuItem($item);
            


                $item = new BlockFloatingMenuItem($this);
                $item->setLabel(n2_('Duplicate'));
                $item->setIcon('ssi_16 ssi_16--duplicate');
                $item->setUrl($this->getUrlSliderDuplicate($sliderid, $this->groupID));
                $actionsMenu->addMenuItem($item);
            }

            if ($accessDelete) {

                $item = new BlockFloatingMenuItem($this);
                $item->setRed();
                $item->setLabel(n2_('Move to trash'));
                $item->setIcon('ssi_16 ssi_16--delete');
                $item->setUrl($this->getUrlSliderMoveToTrash($sliderid, $this->groupID));
                $actionsMenu->addMenuItem($item);
            }

            $this->blockHeader->addAction($actionsMenu->toHTML());
        }
    }

    public function renderSliderManager() {

        $sliderManager = new BlockSliderManager($this->layout);
        $sliderManager->setGroupID($this->slider['id']);
        $sliderManager->display();
    }


    public function renderForm() {

        $slider = $this->slider;

        $data = json_decode($slider['params'], true);
        if ($data == null) $data = array();
        $data['title']     = $slider['title'];
        $data['type']      = $slider['type'];
        $data['thumbnail'] = $slider['thumbnail'];
        $data['alias']     = isset($slider['alias']) ? $slider['alias'] : '';

        $this->editGroupForm($data);

        $this->formData = $data;
    }

    private function editGroupForm($data = array()) {

        $form = new Form($this, 'slider');
        $form->set('class', 'nextend-smart-slider-admin');

        $form->loadArray($data);

        $table = new ContainerTable($form->getContainer(), 'publish', n2_('Publish'));
        $row   = new FieldsetRowPlain($table, 'publish');
        new PublishSlider($row);


        $table = new ContainerTable($form->getContainer(), 'group', n2_('General'));

        $row1 = $table->createRow('row-1');

        new Text($row1, 'title', n2_('Name'), n2_('Group'), array(
            'style' => 'width:400px;'
        ));

        new Text($row1, 'alias', n2_('Alias'), '', array(
            'style' => 'width:200px;'
        ));

        new FieldImage($row1, 'thumbnail', n2_('Thumbnail'));

        new Hidden($row1, 'type', 'group');


        echo $form->render();
    }

    /**
     * @return array
     */
    public function getFormData() {
        return $this->formData;
    }
}