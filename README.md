Lazy Form for Zend Framework 2
=============
Developed by Ibrahim Azhar Armar

Introduction
------------
Did you ever get frustrated by the fact that you have to repeat the same validators, filters, attributes and options over and over again in different forms and elements leading to code duplication and maintenance nightmare? we have read numerous time about [DRY - Don't repeat yourself](https://en.wikipedia.org/wiki/Don't_repeat_yourself) or [OAOO - Once and Only Once](http://c2.com/cgi/wiki?OnceAndOnlyOnce), but do we really follow it?

Zf2LazyForm module is developed to eliminate duplication and encourage reuse. We enhanced the module to support numerous features on top of existing features of zend-form
* Short Syntax
* Reusable Validators, Filters, Attrbutes & Options
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

Example
------------

#### Short Syntax
You can use [short names](http://framework.zend.com/manual/current/en/modules/zend.form.advanced-use-of-forms.html#short-names) offered by ZF2, instead of writing `Zend\Form\Element\Text` for defining form elements you can just type `text`.
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

#### Reusable Validators, Filters, Attrbutes & Options
