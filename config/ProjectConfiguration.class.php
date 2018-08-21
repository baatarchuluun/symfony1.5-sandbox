<?php

require_once dirname(__FILE__) . '/../lib/vendor/symfony/lib/autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register();

class ProjectConfiguration extends sfProjectConfiguration
{
    public function setup()
    {
        $this->enablePlugins('sfDoctrinePlugin');
        $this->enablePlugins('sfFormExtraPlugin');
        $this->enablePlugins('sfCKEditorPlugin');
        $this->enablePlugins('sfThumbnailPlugin');
        $this->enablePlugins('sfCaptchaGDPlugin');
        $this->enablePlugins('ImagePlugin');
        $this->enablePlugins('sfJQueryUIPlugin');
    }
}
