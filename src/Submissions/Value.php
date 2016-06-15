<?php

namespace Bozboz\Enquire\Submissions;

use Illuminate\Database\Eloquent\Model;

class Value extends Model
{
	protected $table = 'enquiry_submission_values';

	protected $fillable = [
		'enquiry_submission_id',
		'label',
		'value'
	];

	public function submission()
	{
		return $this->belongsTo(Submission::class);
	}
}