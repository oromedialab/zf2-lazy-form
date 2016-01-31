Lazy Form for Zend Framework 2
=============
Developed by Ibrahim Azhar Armar

Introduction
------------
Did you ever get frustrated by the fact that you have to repeat the same validators, filters, attributes and options over and over again in different forms and elements leading to code duplication and maintenance nightmare? we have read numerous time about [DRY - Don't repeat yourself](https://en.wikipedia.org/wiki/Don't_repeat_yourself) or [OAOO - Once and Only Once](http://c2.com/cgi/wiki?OnceAndOnlyOnce), but do we really follow it?

Zf2LazyForm module is developed to eliminate duplication and encourage reuse. We enhanced the module to support numerous features on top of existing features of zend-form
* Short Syntax
* Configurable Validators, Filters, Attrbutes & Options
* Reuse using Lazy Set
* Refined Error Messages
* Placeholders
* Global Form Elements

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
You can use [short names](http://framework.zend.com/manual/current/en/modules/zend.form.advanced-use-of-forms.html#short-names) offered by ZF2, instead of writing `Zend\Form\Element\Text` for defining form elements, you can just type `text`, same goes for rest of elements, let's consider the below example to define form element using short syntax
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

#### Reuse using Lazy Set
Once configuration is defined, it can be reused using lazy-set

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

To use lazy set in your form element, you need to define it in each element, refer the example below where we apply `lazy-set = 1` to an element

```php
$this->addFormElement(['name' => 'first_name', 'label' => 'First name', 'type' => 'text', 'lazy-set' => 1]);
```

In some cases you may want to disable filters, you can do it by using `filters => false`, refer the below example where we apply `lazy-set = 2` which has an element with `filters => false`

```php
$this->addFormElement(['name' => 'submit', 'label' => 'Submit', 'type' => 'button', 'lazy-set' => 2]);
```


