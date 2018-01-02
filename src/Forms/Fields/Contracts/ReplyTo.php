<?php

namespace Bozboz\Enquire\Forms\Fields\Contracts;

interface ReplyTo
{
    /**
     * @param  array $input
     * @return array/collection of email addresses
     */
    public function getReplyToAddress($input);
}
