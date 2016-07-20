<?php

namespace Bozboz\Enquire\Forms;

use DB;
use Bozboz\Admin\Models\Base;
use Bozboz\Enquire\Forms\Fields\Field;
use Bozboz\Enquire\Forms\FormInterface;
use Bozboz\Enquire\Forms\Paths\Path;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;

class Form extends Base implements FormInterface
{
	protected $table = 'enquiry_forms';

	protected $fillable = [
		'name',
		'newsletter_signup',
		'recipients',
		'confirmation_message',
		'status',
		'page_list'
	];

	protected $nullable = [
		'recipients'
	];

	public static function boot()
	{
		static::saved(function($form) {
			$form->paths->each(function($path) {
				$path->delete();
			});
			$newPaths = array_filter(array_map('trim', explode("\n", \Input::get('page_list'))));
			foreach ($newPaths as $pathString) {
				$path = new Path([
					'path' => $form->cleanPath($pathString),
					'form_id' => $form->id
				]);
				$path->save();
			}
		});
	}

	public function submissions()
	{
		return $this->hasMany('Bozboz\Enquire\Submissions\Submission');
	}

	public function scopeActive($query)
	{
		$query->where('status', 1);
	}

	public function scopeForPath($query, $path)
	{
		$path = $this->cleanPath($path);
		$query->whereHas('paths', function($query) use ($path) {
			$query->where('path', $path);
		});
	}

	protected function cleanPath($path)
	{
		return '/'.preg_replace('(^/|^'.Request::root().'/)', '', $path);
	}

	public function getPageListAttribute()
	{
		if ($this->paths) {
			return implode("\n", $this->paths->lists('path'));
		}
	}

	public function setPageListAttribute($pageList) {}

	public function getHtmlIdAttribute()
	{
		return str_replace(' ', '', snake_case($this->name));
	}

	public function getFileInputs()
	{
		$fileInputs = [];
		foreach ($this->fields as $field) {
			if (array_search($field->input_type, Config::get('enquire::fields')) == 'file_upload') {
				$fileInputs[] = $field;
			}
		}
		return $fileInputs;
	}

	public function fields()
	{
		return $this->hasMany(Field::class);
	}

	public function paths()
	{
		return $this->hasMany(Path::class);
	}

	public function getValidator()
	{
		return new FormValidator;
	}

	public function getHistoricFormLabels()
	{
		return DB::table('enquiry_submissions')
			->select('enquiry_submission_values.label')
			->join('enquiry_submission_values', 'enquiry_submission_values.submission_id', '=', 'enquiry_submissions.id')
			->where('form_id', '=', $this->id)
			->groupBy('label')
			->orderBy('enquiry_submission_values.created_at', 'ASC')
			->lists('label');
	}

}
