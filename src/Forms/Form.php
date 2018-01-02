<?php

namespace Bozboz\Enquire\Forms;

use Bozboz\Admin\Base\Model;
use Bozboz\Enquire\Forms\Paths\Path;
use Bozboz\Enquire\Forms\Fields\Field;
use Illuminate\Support\Facades\Config;
use Bozboz\Enquire\Forms\FormInterface;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Bozboz\Enquire\Submissions\Submission;
use Bozboz\Enquire\Forms\Fields\Contracts\ReplyTo;

class Form extends Model implements FormInterface
{
	protected $table = 'enquiry_forms';

	protected $fillable = [
		'name',
		'newsletter_signup',
		'recipients',
		'subject',
		'confirmation_message',
		'description',
		'status',
		'page_list',
		'list_id',
		'submit_button_text',
	];

	protected $nullable = [
		'recipients',
		'submit_button_text',
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

	public function getSubject($input)
	{
		if ($this->subject) {
			$placeholders = $this->fields->map(function($field) use ($input) {
				return [
					'value' => key_exists($field->name, $input) ? $input[$field->name] : null,
					'placeholder' => '{{' . $field->label . '}}',
				];
			})->pluck('value', 'placeholder')->prepend($this->name, '{{Form Name}}')->all();

			return str_replace(array_keys($placeholders), $placeholders, $this->subject);
		}
	    return $this->name.' form submission';
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

	public function getDescriptionAttribute()
	{
		if (array_key_exists('description', $this->attributes) && trim(strip_tags($this->attributes['description']))) {
			return $this->attributes['description'];
		}
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

	/**
	 * Get an array containing all form labels that have been
	 * associated with this form.
	 *
	 * @return string[] historic form labels
	 */
	public function getHistoricFormLabels()
	{
		return $this->submissions()
			->select('enquiry_submission_values.label')
			->join('enquiry_submission_values', 'enquiry_submission_values.submission_id', '=', 'enquiry_submissions.id')
			->groupBy('label')
			->orderBy('enquiry_submission_values.created_at', 'ASC')
			->pluck('label')->all();
	}

	public function getReplyToAddress($input)
	{
		return $this->fields->filter(function($field) {
			return $field instanceof ReplyTo;
		})->map(function($field) use ($input) {
			return $field->getReplyToAddress($input);
		})->flatten()->filter();
	}

	public function fields()
	{
		return $this->hasMany(Field::class)->orderBy('sorting');
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
