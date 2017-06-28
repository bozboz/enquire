<?php

namespace Bozboz\Enquire\Exceptions;

use Exception;

class FormException extends Exception
{
    public static function notFound($id)
    {
        return new static("Unable to find form with id {$id}");
    }

    public static function noSignup()
    {
        return new static("Attempting to use newletter signup with no implementation");
    }
}
