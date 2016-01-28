<?php
namespace Oml\Zf2LazyForm\Form;

use Zend\Form\Form;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Base extends Form implements ServiceLocatorAwareInterface
{
	protected $formElements = array();

	protected $serviceLocator;

	public function __construct($name = null)
	{
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
        $result = array();
        $result['name'] = $element['name'];
        $result['type'] = $element['type'];
        $result['options']['label'] = $element['label'];
        return $result;
    }
}
