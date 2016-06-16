<?php

Route::group(['middleware' => 'web', 'prefix' => 'admin', 'namespace' => 'Bozboz\Enquire\Http\Controllers\Admin', 'before' => 'auth'], function()
{
    Route::resource('enquiry-forms', 'FormAdminController');
    Route::resource('enquiry-form-fields', 'FormFieldAdminController');
    Route::get('enquiry-form-fields/{formId}/{fieldTypeAlias}/create', [
        'as' => 'admin.enquiry-form-field.create',
        'uses' => 'FormFieldAdminController@createForForm'
    ]);

    Route::resource('enquiry-form-submissions', 'FormSubmissionAdminController');
});

Route::group(['middleware' => 'web', 'namespace' => 'Bozboz\Enquire\Http\Controllers'], function()
{
    Route::post('process-enquiry', [
        'as' => 'process-enquiry',
        'uses' => 'FormController@processSubmission'
    ]);
});
