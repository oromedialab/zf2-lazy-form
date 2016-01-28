<?php

namespace Oml\Zf2LazyForm\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ModuleService implements ServiceLocatorAwareInterface
{
    protected $serviceLocator;

    protected $deepArrayFalse = array('attributes', 'options');

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

    public function lazySet($id)
    {
        $config = $this->config();
        if (!array_key_exists('lazy-set', $config) || !is_array($config['lazy-set'])) {
            throw new \Exception('Config with key "lazy-set" does not exist or incorrect data type given in '.__NAMESPACE__);
        }
        $lazySetConfig = $config['lazy-set'];
        if (!array_key_exists($id, $lazySetConfig)) {
            throw new \Exception('"lazy-set" with id "'.$id.'" does not exist in '.__NAMESPACE__);
        }
        $lazySet = $lazySetConfig[$id];
        foreach ($lazySet as $type => $set) {
            // If set is not array, skip current iteration
            if (!is_array($set)) {
                $lazySet[$type] = $set;
                continue;
            }
            foreach ($set as $index => $attribute) {
                $deepArray = !in_array($type, $this->deepArrayFalse);
                $value = $this->configParser($type, $attribute);
                if (empty($value)) {
                    $value = array();
                }
                if ($deepArray) {
                    $lazySet[$type][$index] = $value;
                } else {
                    $lazySet[$type] = $value;
                }
            }
        }
        return $lazySet;
    }

    /**
     * Return value if exist, else ignore
     */
    protected function configParser($type, $attribute)
    {
        $config = $this->config();
        if (array_key_exists($type, $config)) {
            $value = $config[$type];
            if (array_key_exists($attribute, $value)) {
                return $value[$attribute];
            }
        }
        return false;
    }
}
