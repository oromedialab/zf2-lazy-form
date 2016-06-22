<?php

/**
 * Base form to handle the functions of module
 *
 * @author Ibrahim Azhar <azhar@iarmar.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace Oml\Zf2LazyForm\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class Base extends Form implements ServiceLocatorAwareInterface
{
    /**
     * Form elements in array format 
     *
     * @var array $formElements
     */
	protected $formElements = array();

    protected $formElementOptions = array();

    /**
     * Remove form elements from the list of array
     *
     * @var array $removeElements
     */
    protected $removeElements = array();

    /**
     * User defined placeholder parameters
     *
     * @var array
     */
    protected $placeholderParameters = array();

    /**
     * Instance of service manager
     *
     * @var Zend\ServiceManager\ServiceManager
     */
	protected $serviceLocator;

    /**
     * Default method for form
     *
     * @var const DEFAULT_METHOD
     */
    const DEFAULT_METHOD = 'POST';

    /**
     * ModuleService Identifier
     *
     * @var string
     */
    protected $moduleService = 'Oml\Zf2LazyForm\Service\ModuleService';

    /**
     * Intiialize form with default attributes
     *
     * @param string $name
     */
	final public function __construct($name = null)
	{
        parent::__construct(null);
        $this->inputFilter  = new InputFilter();
        $this->inputFactory = new InputFactory();
        $this->setAttribute('method', self::DEFAULT_METHOD);
	}

    /**
     * Zend\Form\Form::init() method is used instead of constructed to add form elements
     * Form must be initialized using FormElementManager
     *
     * @return $this
     */
    final public function init()
    {
        $sm = $this->getServiceLocator();
        $config = $sm->get($this->moduleService)->config();
        if (array_key_exists('*', $config)) {
            // Inject form instance if * is closure
            if (is_object($config['*']) && $config['*'] instanceof \Closure) {
                $form = $config['*']($this);
            }
        }
        $this->initialize();
        // Initialize form elements
        foreach ($this->getFormElements() as $element) {
            $elementOptions = array();
            if (array_key_exists($element['name'], $this->formElementOptions)) {
                $elementOptions = $this->formElementOptions[$element['name']];
            }
            // Add id attribute for each element
            if (!array_key_exists('attributes', $element)) {
                $element['attributes'] = array();
                $element['attributes'] ['id'] = $element['name'];
            } else {
                $element['attributes'] ['id'] = $element['name'];
            }
            $this->add($element, $elementOptions);
        }
        return $this;
    }

    /**
     * Form initialazation method
     */
    public function initialize()
    {
        return $this;
    }

    /**
     * Add form element using short syntax
     *
     * @param array $element
     * @return $this
     */
    public function addFormElement(array $element, $options = array())
    {
        if (array_key_exists($element['name'], $this->formElements)) {
            throw new \Exception('Form element "'.$element['name'].'" is already added, use replaceFormElement() or removeElement() instead');
        }
    	$this->formElements[$element['name']] = $element;
        if (!empty($options) && is_array($options)) {
            $this->formElementOptions[$element['name']] = $options;
        }
        return $this;
    }

    /**
     * Remove form element
     *
     * @param array $element
     * @return $this
     */
    public function removeFormElement($name)
    {
        $this->removeElements[] = $name;
        return $this;
    }

    /**
     * Replace existing form element, this methid is same as addFormElement with two difference
     *   1. This does not throw an error if element with same name exist
     *   2. Using removeFormElement() and addFormElement() for an element in the same form does not work, to remove confusion this element is introduced
     *
     * @param array $element
     * @return $this
     */
    public function replaceFormElement(array $element, $options = array())
    {
        $this->formElements[$element['name']] = $element;
        if (!empty($options) && is_array($options)) {
            $this->formElementOptions[$element['name']] = $options;
        }
        return $this;
    }

    /**
     * Method applied from ServiceLocatorAwareInterface, required to inject service locator object
     *
     * @param ServiceLocatorInterface $sl
     * @return $this
     */
    public function setServiceLocator(ServiceLocatorInterface $sl)
	{
		$this->serviceLocator = $sl;
        return $this;
	}

    /**
     * Method applied from ServiceLocatorAwareInterface, required to retreive service locator object
     *
     * @return Zend\ServiceManager\ServiceManager
     */
	public function getServiceLocator()
	{
		return $this->serviceLocator ? $this->serviceLocator->getServiceLocator() : null;
	}

    /**
     * Get form elements in array format with values merged from placeholder
     *
     * @return array $formElements
     */
    public function getFormElements()
    {
        $sm = $this->getServiceLocator();
        $formElements = array();
        $placeholders = $this->getPlaceholderParameters();
        $replacePlaceholderWithValues = array();
        foreach ($this->formElements as $element) {
            // Remove form element if it is available in the remove list
            if (in_array($element['name'], $this->removeElements)) {
                continue;
            }
            $formElement = $this->formatFormElement($element);
            // Replace placeholder with default values
            $replacePlaceholderWithValues = $sm->get($this->moduleService)->defaultConfig('placeholder');
            // Replace placeholder with global form values
            if (!empty($placeholders) && is_array($placeholders)) {
                // Replace placeholder with global values
                if (array_key_exists('global', $placeholders) &&  !empty($placeholders['global']) &&  is_array($placeholders['global'])) {
                    $replacePlaceholderWithValues = array_merge($replacePlaceholderWithValues, $placeholders['global']);
                }
                // Replace placeholder with element form values
                if (array_key_exists('element', $placeholders) &&  !empty($placeholders['element']) &&  is_array($placeholders['element'])) {
                    if (array_key_exists($element['name'], $placeholders['element'])) {
                        $replacePlaceholderWithValues = array_merge($replacePlaceholderWithValues, $placeholders['element'][$element['name']]);
                    }
                }
            }
            $formElement = $this->searchAndReplacePlaceHolders($formElement, $replacePlaceholderWithValues);
            $formElements[] = $formElement;
        }
        return $formElements;
    }

    /**
     * Format form element by replacing keywords with values recognized by zend-form
     *
     * @param array $element
     * @return array $result
     */
    protected function formatFormElement(array $element)
    {
        $sm = $this->getServiceLocator();
        $result = array();
        if (array_key_exists('lazy-set', $element)) {
            $result = $sm->get($this->moduleService)->lazySet($element['lazy-set']);
        }
        $result['name'] = $element['name'];
        $result['type'] = $element['type'];
        if (array_key_exists('label', $element)) {
            $result['options']['label'] = $element['label'];
        }
        return $result;
    }

    /**
     * Add input filter to form elements
     * - Skip filters if value is set to false
     * - If NotEmpty validator is available, add required element with value true, else set required value to false
     * - Remove type from element to avoid conflict with input filter
     *
     * @return Zend\InputFilter\InputFilter
     */
    protected function addInputFilter()
    {
        $sm = $this->getServiceLocator();
        if (empty($sm)) {
            throw new \Exception('Form must be initialized using FormElementManager, refer document for more information');
        }
        $config = $sm->get($this->moduleService)->config();
        foreach ($this->getFormElements() as $element) {
            // If element by name filters exist with value false, do not apply the filter for the element.
            if (array_key_exists('filters', $element) && false === $element['filters']) {
                continue;
            }
            // If validator by name not empty exist then set required to true, else. Set required to false
            $element['required'] = false;
            if (array_key_exists('validators', $element) && is_array($element['validators'])) {
                foreach ($element['validators'] as $validator) {
                    if (in_array($validator['name'], array('NotEmpty'))) {
                        $element['required'] = true;
                    }
                    // Allow validation on empty values for Callback validator
                    if (in_array($validator['name'], array('Callback'))) {
                        $callBackValidatorExist = true;
                        $element['allow_empty'] = true;
                        $element['continue_if_empty'] = true;
                    }
                }
            }
            if (array_key_exists('type', $element)) {
                unset($element['type']);
            }
            $this->inputFilter->add($this->inputFactory->createInput($element));
        }
        return $this->inputFilter;
    }

    /**
     * Get instance of input filter
     *
     * @return Zend\InputFilter\InputFilter
     */
    public function getInputFilter()
    {
        if (!$this->filter) {
            $this->filter = $this->addInputFilter();
        }
        return $this->filter;
    }

    /**
     * Replace place holder with given value in validators, filters, attributes or options
     *
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function setPlaceholderParameter($name, $value, $elementName = null)
    {
        if (null != $elementName) {
            $this->placeholderParameters['element'][$elementName][$name] = $value;
        } else {
            $this->placeholderParameters['global'][$name] = $value;
        }
        return $this;
    }

    /**
     * Get all placeholder parameters
     *
     * @return array
     */
    public function getPlaceholderParameters()
    {
        return $this->placeholderParameters;
    }

    /**
     * Search and replace occurences of place holders in an element
     *
     * @param array $element
     * @param array $placeholders
     * @return array
     */
    protected function searchAndReplacePlaceHolders(array $element, array $placeHolders)
    {
        array_walk_recursive($element, function(&$v, $k, $ph){
            if (is_string($v) && array_key_exists($v, $ph)) {
                $v = $ph[$v];
            }
        }, $placeHolders);
        return $element;
    }
}
