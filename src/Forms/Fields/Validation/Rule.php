<?php

namespace Bozboz\Enquire\Forms\Fields\Validation;

use Bozboz\Admin\Base\Model;
use Bozboz\Enquire\Forms\Fields\Field;

class Rule extends Model
{
    protected $table = 'enquiry_form_validation';

    protected $fillable = ['rule'];
}
