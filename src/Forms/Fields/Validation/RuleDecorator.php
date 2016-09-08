<?php

namespace Bozboz\Enquire\Forms\Fields\Validation;

use Bozboz\Admin\Base\ModelAdminDecorator;

class RuleDecorator extends ModelAdminDecorator
{
    public function __construct(Rule $validation)
    {
        parent::__construct($validation);
    }

    public function getLabel($instance)
    {
        return $instance->rule;
    }

    public function getFields($instance)
    {
        return [];
    }
}
