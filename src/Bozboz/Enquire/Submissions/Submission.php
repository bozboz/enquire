<?php

namespace Bozboz\Enquire\Submissions;

use Bozboz\Admin\Models\Base;
use Bozboz\Enquire\Forms\FormValidator;

class Submission extends Base
{
	protected $table = 'enquiry_submissions';

	protected $fillable = [
		'form_name',
		'form_id',
	];

	public function values()
	{
		return $this->hasMany(Value::class);
	}

	public function getValidator()
	{
		return new FormValidator;
	}
}
