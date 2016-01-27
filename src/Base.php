<?php
namespace Oml\Zf2LazyForm;

use Zend\Form\Form;
use Zend\ServiceManager\ServiceManager;

use Oml\Zf2LazyForm\ServiceInterface\ServiceProviderInterface;

class Base extends Form implements ServiceProviderInterface
{
    public function __construct(ServiceManager $serviceManager)
    {
        parent::__construct(__NAMESPACE__);

        
    }
}
