<?php
/**
 * weather actions.
 *
 * @package    Plugins
 * @subpackage weather
 * @author     David Zeller <zellerda01@gmail.com>
 */
class weatherActions extends sfActions
{
    public function executeIndex(sfWebRequest $request)
    {
        $w = new sfWeather('SZXX0022');
        $w->setForecast(5);
        
        $this->w = $w->retrieve();
    }
}
