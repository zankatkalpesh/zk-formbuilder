<?php

return [
    /*
    |--------------------------------------------------------------------------
    */
    'path' => 'App\\Forms',
    //
    'form' => [
        // 'prefix' => '{name}',
        'class' => 'form-{name}',
        'method' => 'POST',
        'action' => '',
        'wrapper' => [
            'class' => 'container'
        ],
        'buttons' => [
            'wrapper' => [
                'tag' => 'div',
                'class' => 'mb-3 d-flex justify-content-end',
            ],
            // 'actionConfig' => [],
            'actions' => [
                'submit' => [
                    'tag' => 'button',
                    'class' => 'btn btn-primary',
                    'attributes' => [
                        'type' => 'submit',
                    ],
                    'label' => 'Submit',
                ]
            ]
        ],
    ],
    //
    'field' => [
        'wrapper' => [
            'tag' => 'div',
            'class' => 'form-group mb-3 {errorClass}',
            'errorClass' => 'has-error',
        ],
        'label' => [
            'class' => 'form-label',
        ],
        'input' => [
            'class' => 'form-control {errorClass}',
            'errorClass' => 'is-invalid',
        ],
        'attributes' => [
            'placeholder' => 'Enter your {label}',
        ],
        'error' => [
            'class' => 'invalid-feedback',
            'id' => '{id}-error',
        ],
    ],
    //
    'type' =>  [
        'file' => [
            // 'upload' => function ($el, $entity, $files) {
            //     dd('upload main', $el, $entity, $files);
            // },
            // 'fetch' => function ($el, $entity, $data) {
            //     dd('fetch main', $this, $entity, $data);
            // },
            // 'display' => function ($el, $data) {
            //     dd('display main', $el);
            // },
        ],
        'hidden' => [
            'wrapper' => [
                'class' => 'd-none',
            ]
        ],
        'checkboxgroup' => [
            'wrapper' => [
                'class' => 'mb-3',
            ]
        ],
        'radiogroup' => [
            'wrapper' => [
                'class' => 'mb-3',
            ]
        ],
        'checkbox' => [
            'wrapper' => [
                'class' => 'form-check',
            ],
            'class' => 'form-check-input {errorClass}',
        ],
        'radio' => [
            'wrapper' => [
                'class' => 'form-check',
            ],
            'class' => 'form-check-input {errorClass}',
        ],
        'select' => [
            'class' => 'form-select {errorClass}',
        ],
        'group' => [
            'wrapper' => [
                'tag' => 'div',
                'class' => 'form-group mb-3 group-{name}',
            ],
            'fieldWrapper' => [
                'tag' => 'div',
                'class' => 'form-group-fields'
            ],
            'label' => [
                'attributes' => [
                    'for' => false
                ]
            ]
        ],
        'multiple' => [
            'label' => [
                'attributes' => [
                    'for' => false
                ]
            ],
            'wrapper' => [
                'tag' => 'div',
                'class' => 'form-multiple multiple-{name}',
                'attributes' => [
                    'data-prefix' => '{prefix}',
                    'data-min-row' => '{minRow}',
                    'data-max-row' => '{maxRow}',
                ],
            ],
            'rowWrapper' => [
                'tag' => 'div',
                'class' => 'form-multiple-row',
                'attributes' => [
                    'data-row-prefix' => '{rowPrefix}',
                ],
            ],
            'action' => [
                'add' => [
                    'position' => 'before',
                    'tag' => 'button',
                    'attributes' => [
                        'class' => 'btn btn-primary btn-sm',
                        'type' => 'button',
                        'data-add-prefix' => '{prefix}',
                        'data-action' => 'add-row',
                    ],
                    'label' => 'Add',
                ],
                'remove' => [
                    'position' => 'after',
                    'tag' => 'button',
                    'attributes' => [
                        'class' => 'btn btn-danger btn-sm',
                        'type' => 'button',
                        'data-action' => 'remove-row',
                        'data-row-key' => '{rowKey}',
                        'data-remove-prefix' => '{rowPrefix}',
                    ],
                    'label' => 'Remove',
                ]
            ]
        ],
        'tab' => [
            'label' => [
                'attributes' => [
                    'for' => false
                ]
            ],
            'wrapper' => [
                'tag' => 'div',
                'class' => 'form-tab tab-{name}',
            ],
            'tabWrapper' => [
                'tag' => 'ul',
                'class' => 'nav nav-tabs'
            ],
            'itemWrapper' => [
                'activeClass' => 'active',
                'tag' => [
                    'name' => 'li',
                    'class' => 'nav-item',
                    'attributes' => [
                        'role' => 'presentation'
                    ],
                    'tag' => [
                        'name' => 'a',
                        'class' => 'nav-link {activeClass}',
                        'attributes' => [
                            'href' => '#{id}-{tabKey}',
                            'id' => 'tab-{id}-{tabKey}',
                            'data-bs-toggle="tab" data-bs-target="#{id}-{tabKey}-tab-pane"'
                        ],
                    ]
                ]
            ],
            'contentWrapper' => [
                'tag' => 'div',
                'class' => 'tab-content'
            ],
            'panelWrapper' => [
                'activeClass' => 'active show',
                'tag' => 'div',
                'class' => 'tab-pane fade {activeClass}',
                'attributes' => [
                    'id' => '{id}-{tabKey}-tab-pane',
                ],
            ],
        ]
    ],
    // 
    'messages' => [],
    //
    'elements' => []
];
