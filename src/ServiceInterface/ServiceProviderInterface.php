<?php

namespace Oml\Zf2LazyForm\ServiceInterface;

interface ServiceProviderInterface
{
    public function __construct(\Zend\ServiceManager\ServiceManager $serviceManager);
}
