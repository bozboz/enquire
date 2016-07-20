<?php

namespace Admin;

use Bozboz\Admin\Controllers\ModelAdminController;
use Bozboz\Admin\Reports\Report;
use Bozboz\Enquire\Forms\Form;
use Bozboz\Enquire\Forms\FormDecorator;
use Bozboz\Enquire\Forms\Reports\CSVReport;

class FormAdminController extends ModelAdminController
{
	public function __construct(FormDecorator $decorator)
	{
		parent::__construct($decorator);
	}

	public function getListingReport()
	{
		return new Report($this->decorator, 'enquire::admin.overview');
	}

	public function downloadCsv(Form $form)
	{
		$report = new CSVReport($form);

		return $report->render();
	}
}
