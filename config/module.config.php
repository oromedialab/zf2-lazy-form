<?php

return [
	'service_manager' => [
        'invokables' => [
            'oml.zf2lazyform' => 'Oml\Zf2LazyForm\Service\ModuleService',
        ],
    ],
	'oml' => [
		'zf2-lazy-form' => [
			'*' => [
				'method' => 'post'
			],
			'attributes' => [
				'submit-btn' => [
					'type' => 'submit'
				]
			],
			'options' => [

			],
			'validators' => [
				'not_empty' => ['name' => 'NotEmpty']
			],
			'filters' => [
				'strip_tags' => ['name' => 'StripTags'],
                'string_trim' => ['name' => 'StringTrim']
			],
			'lazy-set' => [
				1 => [
					'validators' => ['not_empty'],
					'filters' => ['strip_tags', 'string_trim']
				]
			]
		]
	]
];
