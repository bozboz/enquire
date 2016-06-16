<?php

namespace Bozboz\Enquire\Submissions;

use Bozboz\Admin\Base\Model;
use Bozboz\Enquire\Forms\FormValidator;

class Submission extends Model
{
	protected $table = 'enquiry_submissions';

	protected $fillable = [
		'form_name'
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