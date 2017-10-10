<?php

namespace Bozboz\Enquire\Forms;

use DateTime, Link;
use Bozboz\Jam\Entities\Entity;
use Bozboz\Jam\Types\EntityList;
use Bozboz\Admin\Fields\TextField;
use Bozboz\Admin\Fields\CheckboxField;
use Bozboz\Admin\Fields\TextareaField;
use Bozboz\Admin\Fields\HTMLEditorField;
use Illuminate\Database\Eloquent\Builder;
use Bozboz\Admin\Base\ModelAdminDecorator;
use Bozboz\Admin\Fields\BelongsToManyField;

class FormDecorator extends ModelAdminDecorator
{
	public function __construct(Form $model)
	{
		parent::__construct($model);
	}

	public function getLabel($instance)
	{
		return $instance->name;
	}

	protected function modifyListingQuery(Builder $query)
	{
		$query->orderBy($this->model->getTable() . '.name')->with(['submissions' => function($query) {
			$query->selectRaw('count(*) as count, max(created_at) as latest_submission, form_id')->groupBy('form_id');
		}]);
	}

	public function getColumns($instance)
	{
		$pageList = $instance->paths->map(function($page) {
			return (string)link_to($page->path, $page->path, ['target' => '_blank']);
		});
		$submissionStats = $instance->submissions->first();
		return array_filter([
			'Name' => $instance->name,
			'Recipients' => implode('<br>', explode(',', $instance->recipients)) ?: '-',
			'Pages' => config('enquire.forms_by_path_enabled') ? $pageList->implode('<br>') : null,
			'Newsletter Signup' => config('enquire.newsletter_signup_enabled')
				? ($instance->newsletter_signup ? '<i class="fa fa-check"></i>' : '')
				: null,
			'Status' => $instance->getAttribute('status') == 1 ? '<i class="fa fa-check"></i>' : '',
			'Submissions' => $submissionStats ? $submissionStats->count : '0',
			'Latest Submission' => $submissionStats && $submissionStats->latest_submission ?
				date('d M Y - H:i', strtotime($submissionStats->latest_submission)) : '-',
		], function($column) {
			return ! is_null($column);
		});
	}

	public function getFields($instance)
	{
		return [
			new TextField(['name' => 'name']),
			new CheckboxField(['name' => 'status']),
			config('enquire.newsletter_signup_enabled') ? new CheckboxField(['name' => 'newsletter_signup']) : null,
			config('enquire.newsletter_signup_enabled') ? new TextField('list_id', [
				'help_text' => 'This is required when setting up a newletter signup form. The ID can be found at the bottom of the setting page for the list in mailchimp.',
			]) : null,
			new TextField([
				'name' => 'recipients',
				'help_text' => 'Comma separated list of email addresses you wish form submissions to be sent to.',
			]),
			config('enquire.forms_by_path_enabled') ? new TextareaField([
				'name' => 'page_list',
				'help_text_title' => 'Paste all the URLs you wish this form to display on separated by a new line.',
				'help_text' => "Wildcards may be be added using an asterisk (*). eg. '/contact/*' would display on any page whos URL began with '/contact/'",
			]) : null,
			new TextField('submit_button_text'),
			new HTMLEditorField('description', [
				'help_text' => 'This text will show above the form.',
			]),
			new HTMLEditorField([
				'name' => 'confirmation_message',
				'help_text' => 'This text will show when the form is submitted.',
			])
		];
	}
}
