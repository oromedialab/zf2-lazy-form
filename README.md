Zend Framework 2 - Lazy form for lazy ass
=============
Do you feel lazy while using Zend Form? welcome to the league! we get bored repeating the same thing over and over again in different places, it's time to change this, we developed this module to reuse all possible sets of form elements by using a simple config file, sounds easy isn't it? lets see some example, but first let's install the module in your ZF2 application, you can install using following options.

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

Now that you have installed and enabled some module, lets try creating a simple form element

```php
use Oml\Zf2LazyForm\Form\Base;

class MyForm extends Base
{
	public function __construct($name = null)
	{
		parent::__construct(null);

		$this->addFormElement(['name' => 'name', 'label' => 'Name', 'type' => 'text']);
	}
}
```

That's it, it added a form element of type text in your form.


#### Features
	- Short syntax using short names.
	- Reusable and configurable validators, filters, attrbutes, options.