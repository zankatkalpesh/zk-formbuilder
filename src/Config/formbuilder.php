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
                'class' => 'my-3 d-flex justify-content-end',
            ],
            'actions' => [
                'frm-submit' => [
                    'tag' => 'button',
                    'class' => 'btn btn-primary',
                    'attributes' => [
                        'type' => 'submit',
                    ],
                    'label' => 'Submit',
                ],
                'frm-reset' => [
                    'tag' => 'button',
                    'class' => 'btn btn-secondary',
                    'attributes' => [
                        'type' => 'reset',
                    ],
                    'label' => 'Reset',
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
            'after' => function ($elm) {
                if ($elm->hasViewOnly() || empty($elm->getFiles())) {
                    return null;
                }
                $files = [];
                foreach ($elm->getFiles() as $file) {
                    $files[] = '<li class="file-item">
                        <a href="' . $file . '" target="_blank">' . basename($file) . '</a>
                    </li>';
                }
                return '<div class="frm-view-only type-file mt-2" data-view-after="true">
                            <ul class="list-unstyled">' . implode('', $files) . '</ul>
                        </div>';
            },
        ],
        'date' => [
            'modifyValue' => function ($value, $elm) {
                if ($elm->hasViewOnly()) {
                    return $value ? date('d-m-Y', strtotime($value)) : null;
                }
                return $value ? date('Y-m-d', strtotime($value)) : null;
            },
        ],
        'hidden' => [
            'wrapper' => [
                'class' => 'd-none',
            ]
        ],
        'checkboxgroup' => [
            'wrapper' => [
                'class' => 'col-md-4 {errorClass}',
            ],
        ],
        'radiogroup' => [
            'wrapper' => [
                'class' => 'col-md-4 {errorClass}',
            ],
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
                'class' => 'col-md-12 mb-3 card d-block form-group group-{name}',
            ],
            'fieldWrapper' => [
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
                'class' => 'mb-3 form-multiple-row row',
                'attributes' => [
                    'data-row-prefix' => '{rowPrefix}',
                ],
            ],
            'fieldWrapper' => [
                'class' => 'form-multiple-fields col-md-11',
                'children' => [
                    'class' => 'row',
                ],
            ],
            'action' => [
                'add' => [
                    'position' => 'before',
                    'attributes' => [
                        'class' => 'btn btn-primary btn-md ms-2',
                        'type' => 'button',
                    ],
                    'text' => '',
                    'before' => '<i class="bi bi-plus-circle"></i>',
                ],
                'remove' => [
                    'position' => 'after',
                    'attributes' => [
                        'class' => 'btn btn-danger btn-md',
                        'type' => 'button',
                    ],
                    'text' => '',
                    'before' => '<i class="bi bi-trash"></i>',
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
                'class' => 'form-tab tab-{name}',
            ],
            'tabWrapper' => [
                'tag' => 'ul',
                'class' => 'nav nav-tabs'
            ],
            'itemWrapper' => [
                'activeClass' => 'active',
                'tag' => 'li',
                'class' => 'nav-item',
                'attributes' => [
                    'role' => 'presentation'
                ],
                'children' => [
                    'tag' => 'a',
                    'class' => 'nav-link {activeClass}',
                    'attributes' => [
                        'href' => '#{id}-{tabKey}',
                        'id' => 'tab-{id}-{tabKey}',
                        'data-bs-toggle="tab" data-bs-target="#{id}-{tabKey}-tab-pane"'
                    ],
                ]
            ],
            'contentWrapper' => [
                'class' => 'tab-content'
            ],
            'panelWrapper' => [
                'activeClass' => 'active show',
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
