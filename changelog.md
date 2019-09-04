# Bozboz Forms Package Changelog

## Version 3.1.0 (2019-09-04)
- Add multiple checkbox field
- Update validation field help text

## Version 3.0.2 (2018-06-19)
- Fix FileUpload filename issue

## Version 3.0.1 (2018-01-22)
- Fix email field options

## Version 3.0.0 (2018-01-22)
- Smarter field models
- Add validation to prevent duplicate field names on a single form

## Version 2.11.2 (2017-10-31)
- Fix checkbox view

## Version 2.11.1 (2017-10-25)
- Fix permission on form fields button on forms listing

## Version 2.11.0 (2017-10-10)
- Allow page list and newsletter signup options to be toggled

## Version 2.10.1 (2017-09-25)
- Exclude inactive forms in jam field relation

## Version 2.10.0 (2017-08-31)
- Remove sr-only class from labels

## Version 2.9.1 (2017-08-25)
- Make submission controller property on form controller protected not private
- Rearrange permissions to make FormAdminController easier to extend
- Fix missing argument when invoking canDuplicate from duplicateForm method

## Version 2.9.0 (2017-08-23)
- Add ability to duplicate existing forms

## Version 2.8.0 (2017-07-11)
- Add more help text to jam form field

## Version 2.7.0 (2017-06-28)
- Throw exception rather than abort if form not found in process method


## Version 2.6.0 (2017-06-22)
- Add some helpful links and info to the jam form field

## Version 2.5.0 (2017-06-05)
- Allow submissions that have failed the token check to skip honeypot time validation

## Version 2.4.0 (2017-05-04)
- Add jam field for selecting forms on entities
- Add radio field type
- Separate form partial for manual, non view composer usage
- Change save and exit to save and create fields on forms with no fields
- Add current url field type

## Version 2.3.6 (2017-02-08)
- Fix validation for files

## Version 2.3.5 (2017-01-27)
- Fix form create screen
- Allow separate form and field permissions

## Version 2.3.4 (2017-01-26)
- Strip empty WYSIWYG content from description

## Version 2.3.3 (2017-01-03)
- Require admin v2.* in composer.json

## Version 2.3.2 (2017-01-03)
- Strip spaces out of uploaded file names

## Version 2.3.1 (2016-12-23)
- Allow submit button text to be changed in the CMS

## Version 2.3.0 (2016-12-21)
- Update success message so multiple forms can exist on a single page
- Link submissions to forms with a foreign key
- Add form filter to submissions report
- Add reporting

## Version 2.2.4 (2016-11-02)
- Use correct blade escape syntax in email view

## Version 2.2.3 (2016-11-02)
- Fix uploading of files
- Prepend timestamp to uploaded files

## Version 2.2.2 (2016-11-02)

- Separate view composer in to its own method on service provider
- Send better failure messages and pass on info from mailchimp to user
- Use default site "from" address when sending email

## Version 2.2.1 (2016-10-06)

- Add partial for phone field type

## Version 2.2.0 (2016-10-05)

- Set up mailchimp integration

## Version 2.1.1 (2016-09-16)

- Set up permissions properly for forms and fields

## Version 2.1.0 (2016-09-08)

- Change validation on form fields from plain text to tag field
    - Upgrading to this version requires publishing migrations
        ```php artisan vendor:publish --provider="Bozboz\Enquire\Providers\EnquireServiceProvider"```


## Version 2.0.0 (2016-08-31)

- Update for admin v2 / L5.2
- Add honeypot spam prevention
