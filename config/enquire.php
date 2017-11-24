<?php

return [
    'fields' => [
        'checkbox'      => 'enquire::partials.checkbox',
        'dropdown'      => 'enquire::partials.dropdown',
        'phone'         => 'enquire::partials.phone',
        'radio_buttons' => 'enquire::partials.radios',
        'text'          => 'enquire::partials.text',
        'textarea'      => 'enquire::partials.textarea',
        'current-url'   => 'enquire::partials.current-url',
    ],

    'fields_with_options' => [
        'dropdown',
        'radio_buttons',
    ],

    'from_address' => '',

    'mailchimp_api_key' => '',

    'forms_by_path_enabled' => false,
    'newsletter_signup_enabled' => false,
];
