<?php
/**
 * Manages module service
 *
 * @author Ibrahin Azhar <azhar@iarmar.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace Oml\Zf2LazyForm\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ModuleService implements ServiceLocatorAwareInterface
{
    /**
     * Instance of service manager
     *
     * @var Zend\ServiceManager\ServiceManager
     */
    protected $serviceLocator;

    /**
     * Set deep array falst to following elements
     *
     * @var array $deepArrayFalse
     */
    protected $deepArrayFalse = array('attributes', 'options');

    /**
     * Method applied from ServiceLocatorAwareInterface, required to inject service locator object
     *
     * @param ServiceLocatorInterface $sl
     * @return $this
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    /**
     * Method applied from ServiceLocatorAwareInterface, required to retreive service locator object
     *
     * @return Zend\ServiceManager\ServiceManager
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Get module config in array format
     *
     * @return array
     */
    public function config()
    {
    	$config = $this->getServiceLocator()->get('config');
    	return $config['oml']['zf2-lazy-form'];
    }

    /**
     * Get default value for given attribute
     *
     * @param string $name
     * @return array
     */
    public function defaultConfig($name)
    {
        $config = $this->config();
        $default = array_key_exists('default', $config) ? $config['default'] : array();
        if (!array_key_exists($name, $default)) {
            return array();
        }
        return $default[$name];
    }

    /**
     * Convert lazy-set format to zend-form compatible array format
     *
     * @param string $id
     * @return array
     */
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
     * Parse config for individual type (attributes, options, validators, filters...)
     *
     * @return string|bool
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
