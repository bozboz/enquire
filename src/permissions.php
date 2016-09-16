<?php

$permissions->define([

    'view_enquire_forms'   => 'Bozboz\Permissions\Rules\GlobalRule',
    'create_enquire_forms' => 'Bozboz\Permissions\Rules\ModelRule',
    'edit_enquire_forms'   => 'Bozboz\Permissions\Rules\ModelRule',
    'delete_enquire_forms' => 'Bozboz\Permissions\Rules\ModelRule',

    'view_enquire_submissions'   => 'Bozboz\Permissions\Rules\GlobalRule',
    'create_enquire_submissions' => 'Bozboz\Permissions\Rules\ModelRule',
    'edit_enquire_submissions'   => 'Bozboz\Permissions\Rules\ModelRule',
    'delete_enquire_submissions' => 'Bozboz\Permissions\Rules\ModelRule',

]);
