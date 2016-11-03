<?php

namespace Bozboz\Enquire\Forms;

use Bozboz\Admin\Base\Model;
use Bozboz\Enquire\Forms\Fields\Field;
use Bozboz\Enquire\Forms\FormInterface;
use Bozboz\Enquire\Forms\Paths\Path;
use Bozboz\Enquire\Submissions\Submission;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class Form extends Model implements FormInterface
{
	protected $table = 'enquiry_forms';

	protected $fillable = [
		'name',
		'newsletter_signup',
		'recipients',
		'confirmation_message',
		'description',
		'status',
		'page_list',
		'list_id',
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
			$newPaths = array_filter(array_map('trim', explode("\n", \Request::get('page_list'))));
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
		return $this->hasMany(Submission::class);
	}

	public function wasSubmitted()
	{
		return Session::get($this->getSessionHandle());
	}

	public function getSessionHandle()
	{
		return "form-" . e($this->name);
	}

	public function scopeActive($query)
	{
		$query->where('status', 1);
	}

	public function scopeForPath($query, $path)
	{
		$path = $this->cleanPath($path);
		$query->select('enquiry_forms.*')
			->join(
				'enquiry_form_paths',
				'enquiry_form_paths.form_id', '=', 'enquiry_forms.id'
			)->whereRaw(sprintf("'%s' like replace(`enquiry_form_paths`.`path`, '*', '%%')", $path))
			->orderByRaw('length(`enquiry_form_paths`.`path`) desc')
			->limit(1);
	}

	protected function cleanPath($path)
	{
		return '/'.preg_replace('(^/|^'.Request::root().'/)', '', $path);
	}

	public function getPageListAttribute()
	{
		if ($this->paths) {
			return $this->paths->pluck('path')->implode("\n");
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
			if (array_search($field->input_type, Config::get('enquire.fields')) == 'file_upload') {
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
}