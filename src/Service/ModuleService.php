<?php

namespace Oml\Zf2LazyForm\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ModuleService implements ServiceLocatorAwareInterface
{
    protected $serviceLocator;

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function config()
    {
    	$config = $this->getServiceLocator()->get('config');
    	return $config['oml']['zf2-lazy-form'];
    }
}
