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
				'placeholder' => [
					':min' => 10,
					':max' => 200
				]
			],
			'attributes' => [
				'submit_btn' => [
					'type' => 'submit'
				]
			],
			'options' => [],
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
			],
			'filters' => [
				'strip_tags' => ['name' => 'StripTags'],
                'string_trim' => ['name' => 'StringTrim']
			],
			'lazy-set' => [
				1 => [
					'validators' => ['not_empty', 'string_length'],
					'filters' => ['strip_tags', 'string_trim']
				],
				2 => [
					'attributes' => ['submit_btn'],
					'filters' => false
				]
			]
		]
	]
];
