<?php

namespace Admin;

use Bozboz\Admin\Controllers\ModelAdminController;
use Bozboz\Admin\Reports\Report;
use Bozboz\Enquire\Submissions\SubmissionDecorator;

class FormSubmissionAdminController extends ModelAdminController
{
	public function __construct(SubmissionDecorator $decorator)
	{
		parent::__construct($decorator);
	}

	public function index()
	{
		$report = new Report($this->decorator);
		$report->overrideView('enquire::admin.submissions.overview');
		return $report->render(array('controller' => get_class($this)));
	}
}
