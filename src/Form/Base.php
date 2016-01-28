<?php
namespace Oml\Zf2LazyForm\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Hydrator\ClassMethods;

class Base extends Form implements ServiceLocatorAwareInterface
{
	protected $formElements = array();

	protected $serviceLocator;

	public function __construct($name = null)
	{
        $this->inputFilter  = new InputFilter();
        $this->inputFactory = new InputFactory();
        $this->setHydrator(new ClassMethods(true));
        $this->setBindOnValidate(false);
		parent::__construct(null);
	}

    public function init()
    {
    	$sm = $this->getServiceLocator();
    	$config = $sm->get('oml.zf2lazyform')->config();
        foreach ($this->getFormElements() as $element) {
            $this->add($element);
        }
        return $this;
    }

    public function addFormElement(array $element)
    {
    	$this->formElements[] = $element;
    }

    public function setServiceLocator(ServiceLocatorInterface $sl)
	{
		$this->serviceLocator = $sl;
	}

	public function getServiceLocator()
	{
		return $this->serviceLocator->getServiceLocator();
	}

    public function getFormElements()
    {
        $formElements = array();
        foreach ($this->formElements as $element) {
            $formElements[] = $this->formatFormElement($element);
        }
        return $formElements;
    }

    protected function formatFormElement($element)
    {
        $sm = $this->getServiceLocator();
        $result = array();
        if (array_key_exists('lazy-set', $element)) {
            $result = $sm->get('oml.zf2lazyform')->lazySet($element['lazy-set']);
        }
        $result['name'] = $element['name'];
        $result['type'] = $element['type'];
        $result['options']['label'] = $element['label'];
        return $result;
    }

    protected function addInputFilter()
    {
        $sm = $this->getServiceLocator();
        $config = $sm->get('oml.zf2lazyform')->config();
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
                }
            }
            if (array_key_exists('type', $element)) {
                unset($element['type']);
            }
            $this->inputFilter->add($this->inputFactory->createInput($element));
        }
        return $this->inputFilter;
    }

    public function getInputFilter()
    {
        if (!$this->filter) {
            $this->filter = $this->addInputFilter();
        }
        return $this->filter;
    }
}
