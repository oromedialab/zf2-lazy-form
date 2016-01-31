<?php
/**
 * Module config file
 *
 * @author Ibrahin Azhar <azhar@iarmar.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

return [
	'service_manager' => [
        'invokables' => [
            'oml.zf2lazyform' => 'Oml\Zf2LazyForm\Service\ModuleService',
        ],
    ],
	'oml' => [
		'zf2-lazy-form' => [
			'*' => function(\Zend\Form\Form $form) {
			},
			'default' => [
				'placeholder' => []
			],
			'attributes' => [],
			'options' => [],
			'validators' => [],
			'filters' => [],
			'lazy-set' => [],
			'error-messages' => [
			]
		]
	]
];
