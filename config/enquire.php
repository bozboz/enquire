<?php

return [
    'fields' => [
        'checkbox'      => 'enquire::partials.checkbox',
        'checkboxes'    => 'enquire::partials.checkboxes',
        'dropdown'      => 'enquire::partials.dropdown',
        'email'         => 'enquire::partials.email',
        'file_upload'   => 'enquire::partials.file',
        'phone'         => 'enquire::partials.phone',
        'radio_buttons' => 'enquire::partials.radios',
        'text'          => 'enquire::partials.text',
        'textarea'      => 'enquire::partials.textarea',
        'current-url'   => 'enquire::partials.current-url',
    ],

    'fields_with_options' => [
        'dropdown',
        'radio_buttons',
        'checkboxes',
    ],

    'from_address' => '',

    'mailchimp_api_key' => '',
    'mailchimp_double_opt_in' => true,

    'forms_by_path_enabled' => false,
    'newsletter_signup_enabled' => false,
    'allow_multiple_forms_per_page' => false,
];
