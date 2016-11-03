<?php

namespace Bozboz\Enquire\Submissions;

use Bozboz\Admin\Base\Model;
use Bozboz\Enquire\Forms\Form;
use Bozboz\Enquire\Forms\FormValidator;

class Submission extends Model
{
	protected $table = 'enquiry_submissions';

	protected $fillable = [
		'form_id'
	];

	public function form()
	{
		return $this->belongsTo(Form::class);
	}

	public function values()
	{
		return $this->hasMany(Value::class);
	}

	public function getFormNameAttribute()
	{
		return $this->form ? $this->form->name : null;
	}

	public function getValidator()
	{
		return new FormValidator;
	}
}