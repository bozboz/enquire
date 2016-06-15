<?php

namespace Bozboz\Enquire\Forms\Paths;

use Bozboz\Enquire\Forms\Form;
use Illuminate\Database\Eloquent\Model;

class Path extends Model
{
	protected $table = 'enquiry_form_paths';

	protected $fillable = [
		'form_id',
		'path'
	];

	public function form()
	{
		return $this->belongsTo(Form::class);
	}
}