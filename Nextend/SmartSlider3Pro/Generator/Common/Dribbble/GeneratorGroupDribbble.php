<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\Dribbble;

use Nextend\SmartSlider3\Generator\AbstractGeneratorGroup;
use Nextend\SmartSlider3Pro\Generator\Common\Dribbble\Sources\DribbbleProject;
use Nextend\SmartSlider3Pro\Generator\Common\Dribbble\Sources\DribbbleShots;

class GeneratorGroupDribbble extends AbstractGeneratorGroup {

    protected $name = 'dribbble';

    protected $needConfiguration = true;

    protected $isDeprecated = true;

    public function __construct() {
        parent::__construct();

        $this->configuration = new ConfigurationDribbble($this);
    }

    public function getLabel() {
        return 'Dribbble';
    }

    public function getDescription() {
        return sprintf(n2_('Creates slides from %1$s.'), 'Dribble');
    }

    protected function loadSources() {

        new DribbbleShots($this, 'shots', 'Shots');
        new DribbbleProject($this, 'project', 'Project');
    }
}
