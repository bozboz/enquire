# Bozboz Forms Package Changelog

## Version 2.4.0 (Future)
- Add jam field for selecting forms on entities
- Add radio field type
- Separate form partial for manual, non view composer usage

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
