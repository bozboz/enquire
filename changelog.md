# Bozboz Forms Package Changelog

## Version 2.1.0 (2016-09-08)

- Change validation on form fields from plain text to tag field
    - Upgrading to this version requires publishing migrations
        ```php artisan vendor:publish --provider="Bozboz\Enquire\Providers\EnquireServiceProvider"```
    

## Version 2.0.0 (2016-08-31)

- Update for admin v2 / L5.2
- Add honeypot spam prevention
