<?php

return [
    /*
    |--------------------------------------------------------------------------
    */
    'path' => 'App\\Forms',
    //
    'form' => [
        // 'prefix' => '{name}',
        'class' => 'form-{name} row',
        'method' => 'POST',
        'action' => '',
        'wrapper' => [],
        'buttons' => [
            'wrapper' => [
                'tag' => 'div',
                'class' => 'my-3 d-flex justify-content-end',
            ],
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
            'class' => 'mb-2 col-md-4 {errorClass}',
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
            'placeholder' => 'form.input.placeholder',
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
                'class' => 'col-md-4',
            ],
            'itemWrapper' => [
                'class' => 'form-check',
            ],
        ],
        'radiogroup' => [
            'wrapper' => [
                'class' => 'col-md-4',
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
                'class' => 'col-md-12 mb-3 card d-block form-group group-{name}',
            ],
            'fieldWrapper' => [
                'tag' => 'div',
                'class' => 'form-group-fields row px-2'
            ],
            'label' => [
                'class' => 'fs-5 mt-2 border-bottom border-secondary form-label form-group-label',
                'attributes' => [
                    'for' => false
                ]
            ]
        ],
        'multiple' => [
            'label' => [
                'class' => 'fs-5 mt-2 border-bottom border-secondary form-label form-multiple-label',
                'attributes' => [
                    'for' => false
                ]
            ],
            'wrapper' => [
                'class' => 'col-md-12 mb-3 card d-block form-multiple multiple-{name}',
                'attributes' => [
                    // 'data-prefix' => '{prefix}',
                    'data-min-row' => '{minRow}',
                    'data-max-row' => '{maxRow}',
                ],
            ],
            'contentWrapper' => [
                'class' => 'form-multiple-content-{name}',
                'attributes' => [
                    'data-prefix' => '{prefix}',
                ],
            ],
            'rowWrapper' => [
                'tag' => 'div',
                'class' => 'mb-3 form-multiple-row row',
                'attributes' => [
                    'data-row-prefix' => '{rowPrefix}',
                ],
            ],
            'fieldWrapper' => [
                'tag' => [
                    'class' => 'form-multiple-fields col-md-11',
                    'tag' => [
                        'class' => 'row',
                    ]
                ]
            ],
            'action' => [
                'add' => [
                    'position' => 'before',
                    'attributes' => [
                        'class' => 'btn btn-primary btn-md ms-2',
                        'type' => 'button',
                        'data-add-prefix' => '{prefix}',
                        'data-action' => 'add-row',
                    ],
                    'label' => 'Add',
                ],
                'remove' => [
                    'position' => 'after',
                    'attributes' => [
                        'class' => 'btn btn-danger btn-md',
                        'type' => 'button',
                        'data-action' => 'remove-row',
                        'data-row-key' => '{rowKey}',
                        'data-remove-prefix' => '{rowPrefix}',
                    ],
                    'label' => '<i class="fas fa-trash"></i>',
                    'wrapper' => [
                        'class' => 'form-multiple-remove col-md-1 text-md-center',
                    ]
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
