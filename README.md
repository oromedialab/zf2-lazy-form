Lazy Form for Zend Framework 2
=============
Developed and Maintained by Ibrahim Azhar Armar

[![Gitter](https://badges.gitter.im/oromedialab/zf2-lazy-form.svg)](https://gitter.im/oromedialab/zf2-lazy-form?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)

Introduction
------------
Did you ever get frustrated by the fact that you have to repeat the same validators, filters, attributes and options over and over again in different forms and elements leading to code duplication and maintenance nightmare? we have read numerous time about [DRY - Don't repeat yourself](https://en.wikipedia.org/wiki/Don't_repeat_yourself) or [OAOO - Once and Only Once](http://c2.com/cgi/wiki?OnceAndOnlyOnce), but do we really follow it?

Zf2LazyForm module is developed to eliminate duplication and encourage reuse. We enhanced the module to support numerous features on top of existing features of zend-form
* [Short Syntax](https://github.com/oromedialab/zf2-lazy-form#short-syntax)
* [Configurable Validators, Filters, Attrbutes & Options](https://github.com/oromedialab/zf2-lazy-form#configurable-validators-filters-attrbutes--options)
* [Lazy Set](https://github.com/oromedialab/zf2-lazy-form#lazy-set)
* [Placeholders](https://github.com/oromedialab/zf2-lazy-form#placeholders)
* [Global Form Elements and Attributes](https://github.com/oromedialab/zf2-lazy-form#global-form-elements-and-attributes)
* [Configurable Error Messages](#) - @TODO

Installation
------------

#### Install using composer
```
composer require oromedialab/zf2-lazy-form dev-master
```

#### Install using GIT clone
```
git clone https://github.com/oromedialab/zf2-lazy-form.git
```

#### Enable Zf2 Module
Enable the module by adding `Oml\Zf2LazyForm` in your `config/application.config.php` file.

Important Instruction
------------
Form must be initialized using FormElementManager, lets see an example 
```php
// Correct approach
$sm = $this->getServiceLocator();
$form = $sm->get('FormElementManager')->get('User\Form\Create');

// Incorrect approach
$sm = $this->getServiceLocator();
$form = new User\Form\Create();
```

Example
------------

#### Short Syntax
Let's consider the below example to define form element using short syntax
```php
use Oml\Zf2LazyForm\Form\Base;

class MyForm extends Base
{
	public function __construct($name = null)
	{
		parent::__construct(null);

		$this->addFormElement(['name' => 'first_name', 'label' => 'First name', 'type' => 'text']);
		$this->addFormElement(['name' => 'last_name', 'label' => 'Last name', 'type' => 'text']);
	}
}
```
When an element is defined using `addFormElement()` by default empty input filters are injected, you don't have to worry about defining input filters separately. To be precise you never define input filters in form again, instead you define it in the config file and reuse it across forms and elements, we'll see an example of this below

You can also use [short names](http://framework.zend.com/manual/current/en/modules/zend.form.advanced-use-of-forms.html#short-names) offered by ZF2, instead of writing `Zend\Form\Element\Text` for defining form elements, you can just type `text`, same goes for rest of elements

#### Configurable Validators, Filters, Attrbutes & Options
Define validators, filters, attributes and options in config file to reuse it across forms and elements. the syntax is same as what you use in zend-form

```php
return [
	'oml' => [
		'zf2-lazy-form' => [
			'validators' => [
				'not-empty' => ['name' => 'NotEmpty'],
				'string-length' => [
	                'name'    => 'StringLength',
	                'options' => array(
	                    'encoding' => 'UTF-8',
	                    'min' => 2,
	                    'max' => 255
	                )
				]
			],
			'filters' => [
				'strip-tags' => ['name' => 'StripTags'],
	            'string-trim' => ['name' => 'StringTrim']
			]
			'attributes' => [
				'submit-btn' => [
					'type' => 'submit',
					'class' => 'submit-btn'
				]
			],
			'options' => [
				'label-option' => [
					'label_attributes' => [
		                'class' => 'col-sm-2 font_16'
		            ]
				]
			]
		]
	]
];
```

#### Lazy Set
Once configuration is defined, it can be reused using lazy-set

```php
return [
	'oml' => [
		'zf2-lazy-form' => [
			'lazy-set' => [
				1 => [
					'validators' => ['not-empty', 'string-length'],
					'filters' => ['strip-tags', 'string-trim'],
					'attributes' => ['submit-btn'],
					'options' => ['label-option']
				],
				2 => [
					'attributes' => ['submit-btn'],
					'filters' => false
				]
			]
		]
	]
];
```

To use lazy set in your form element, you need to define it in each element, refer the example below where we apply `lazy-set = 1` to an element

```php
$this->addFormElement(['name' => 'first_name', 'label' => 'First name', 'type' => 'text', 'lazy-set' => 1]);
```

In some cases you may want to disable filters, you can do it by using `filters => false`, refer the below example where we apply `lazy-set = 2` which has an element with `filters => false`

```php
$this->addFormElement(['name' => 'submit', 'label' => 'Submit', 'type' => 'button', 'lazy-set' => 2]);
```

#### Placeholders
In many instances you may want to define different validation values for a given validator. Lets consider `StringLength` where it makes sense to have a default minimum and maximum length for all form elements, however for specific element we may want to overwrite it with specific values, this is where `Placeholders` comes to our rescue, lets see some example

```php
return [
	'oml' => [
		'zf2-lazy-form' => [
			'validators' => [
				'not_empty' => ['name' => 'NotEmpty'],
				'string_length' => [
                    'name'    => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => ':min',
                        'max' => ':max',
                    )
				]
			]
		]
	]
];
```
The defined placeholder `:min` and `:max` in above configuration can be replaced on 3 level
* Global
* Form
* Element

Replace placeholder value on a global level
```php
// Apply global placeholder
return [
	'oml' => [
		'zf2-lazy-form' => [
			'default' => [
				'placeholder' => [
					':min' => 2,
					':max' => 200
				]
			]
		]
	]
];
```

Replace placeholder value on a form level
```php
use Oml\Zf2LazyForm\Form\Base;

class MyForm extends Base
{
	public function __construct($name = null)
	{
		parent::__construct(null);

		// Overwrite :min and :max value for this form
		$this->setPlaceholderParameter(':min', 20);
		$this->setPlaceholderParameter(':max', 500);

		$this->addFormElement(['name' => 'first_name', 'label' => 'First name', 'type' => 'text', 'lazy-set' => 1]);
	}
}
```

Replace placeholder value per element
```php
use Oml\Zf2LazyForm\Form\Base;

class MyForm extends Base
{
	public function __construct($name = null)
	{
		parent::__construct(null);

		// Overwrite :min and :max value for first name
		$this->setPlaceholderParameter(':min', 20, 'first_name');
		$this->setPlaceholderParameter(':max', 500, 'first_name');

		$this->addFormElement(['name' => 'first_name', 'label' => 'First name', 'type' => 'text', 'lazy-set' => 1]);
	}
}
```

#### Global Form Elements and Attributes
Most often we use common elements in forms such as, all forms must have a submit button, a csrf token must be included, it must contain specific class names, or bind hydator etc. this can be done easily using closure in your config file

```php
return [
	'oml' => [
		'zf2-lazy-form' => [
			'*' => function(\Zend\Form\Form $form) {
				// Apply form attribute
				$form->setAttribute('class', 'form-horizontal form');
				// Add an element in the form
				$form->addFormElement(['name' => 'submit', 'label' => 'Submit', 'type' => 'button', 'lazy-set' => 2]);
				// Set hydrator
				$form->setHydrator(new \Zend\Stdlib\Hydrator\ClassMethods(true));
			},
		]
	]
];
```
An instance of `Zend\Form` is injected by default when you define `$config['oml']['zf2-lazy-form'][*]` with closure, this allows you to modify or add elements to the form on a global level, you can also use `addFormElement()` or other available module functions here

Options
------------
Available Options in Config File :

- `$config['oml']['zf2-lazy-form']['*'] = function(\Zend\Form\Form $form){}` - Global elements and attributes
- `$config['oml']['zf2-lazy-form']['default']['placeholder']` - Default values for placeholder
- `$config['oml']['zf2-lazy-form']['attributes']` - Form element attributes
- `$config['oml']['zf2-lazy-form']['options']` - Form element options
- `$config['oml']['zf2-lazy-form']['validators']` - Form element validators
- `$config['oml']['zf2-lazy-form']['filters']` - Form element filters
- `$config['oml']['zf2-lazy-form']['lazy-set']` - Lazy set for reusable elements

Available Options in Form Class Extending `Oml\Zf2LazyForm\Form\Base` :
- `addFormElement(array $params)` - Accepts name, type, label and lazy-set
- `setPlaceholderParameter($name, $value, $elementName = null)` = Replace placeholder value for form or element

Feel free to use native zend-form functions parallelly with this module if the function offered by this module does not suffice your need. it is designed to avoid conflict with existing `Zend\Form` functionality, hence allowing you to use `add()` or `addFormElement()` together in your form

