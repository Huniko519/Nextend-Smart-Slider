<?php


namespace Nextend\SmartSlider3\Application\Admin\Sliders\Pro;

/**
 * @var $this ViewSlidersActivate
 */
?>

<div class="n2_page_activate">
    <div class="n2_page_activate__heading">
        <?php n2_e('Activate Smart Slider 3 Pro'); ?>
    </div>
    <div class="n2_page_activate__subheading">
        <?php n2_e('Register Smart Slider 3 Pro on this domain to enable auto update, slider templates and slide library.'); ?>
    </div>
    <div class="n2_page_activate__video">
        <div class="n2_page_activate__video_placeholder"></div>
        <iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/8t5p1Xxysfw?rel=0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
    <div class="n2_page_activate__buttons">
        <div class="n2_page_activate__button_dont_show">
            <a href="<?php echo $this->getUrlDashboard(); ?>"><?php n2_e('Go to dashboard'); ?></a>
        </div>
        <div class="n2_page_activate__button_dashboard">
            <a href="<?php echo $this->getUrlDashboard(); ?>" onclick="N2Classes.License.get().startActivation().done((function(){window.location=this.getAttribute('href');}).bind(this));return false;"><?php n2_e('Activate'); ?></a>
        </div>
    </div>
</div>
