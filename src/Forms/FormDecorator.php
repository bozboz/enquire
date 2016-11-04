<?php

namespace Bozboz\Enquire\Forms;

use DateTime, Link;
use Bozboz\Admin\Fields\TextField;
use Bozboz\Admin\Fields\TextareaField;
use Bozboz\Admin\Fields\HTMLEditorField;
use Bozboz\Admin\Fields\CheckboxField;
use Bozboz\Admin\Fields\BelongsToManyField;
use Bozboz\Admin\Base\ModelAdminDecorator;
use Illuminate\Database\Eloquent\Builder;

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
		return [
			'Name' => $instance->name,
			'Recipients' => $instance->recipients,
			'Pages' => $pageList->implode('<br>'),
			'Newsletter Signup' => $instance->newsletter_signup ? '<i class="fa fa-check"></i>' : '',
			'Status' => $instance->getAttribute('status') == 1 ? '<i class="fa fa-check"></i>' : '',
			'Submissions' => $submissionStats->count,
			'Latest Submission' => $submissionStats->latest_submission ?
				date('d M Y - H:i', strtotime($submissionStats->latest_submission)) : '-',
		];
	}

	public function getFields($instance)
	{
		return [
			new TextField(['name' => 'name']),
			new CheckboxField(['name' => 'status']),
			new CheckboxField(['name' => 'newsletter_signup']),
			new TextField('list_id', [
				'help_text' => 'This is required when setting up a newletter signup form. The ID can be found at the bottom of the setting page for the list in mailchimp.',
			]),
			new TextField([
				'name' => 'recipients',
				'help_text' => 'Comma separated list of email addresses you wish form submissions to be sent to.',
			]),
			new TextareaField([
				'name' => 'page_list',
				'help_text_title' => 'Paste all the URLs you wish this form to display on separated by a new line.',
				'help_text' => "Wildcards maybe be added using an asterisk (*). eg. '/contact/*' would display on any page whos URL began with '/contact/'",
			]),
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
