<?php

class frontendConfiguration extends sfApplicationConfiguration
{

    public function configure()
    {
        $this->dispatcher->connect('application.throw_exception', array('AppTools', 'handleException'));
    }

}
