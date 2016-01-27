<?php

return [
	'oml' => [
		'zf2-lazy-form' => [
			'apply_this_for_all_form' => [],
			'form-id-1' => [
				'form-method' => 'post',
				'elements' => [
					'first_name' => [
						'type' => 'Zend\Form\Element\Textarea',
						'label' => 'First Name'
					]
				],
				'attributes' => [],
				'options' => [],
				'validators' => [
					'not_empty' => [
						'name' => 'Zend\Validator\NotEmpty'
					]
				],
				'filters' => [
					'strip_tags' => ['name' => 'StripTags']
                    'string_trim' => ['name' => 'StringTrim']
				],
				'filter_set' => [
					'string_and_string' => ['strip_tags', 'string_trim']
				],
				'validator_set' => [],
				'element_validators' => [],
				'element_attributes' => [],
				'element_filters' => [],
				'element_options' => []
			]
		]
	]
];
