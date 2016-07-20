<?php namespace Bozboz\Enquire\Forms\Reports;

use Bozboz\Enquire\Forms\Form;
use Bozboz\Admin\Reports\CSVReport as BaseCSVReport;

class CSVReport extends BaseCSVReport
{
	protected $form;

	public function __construct(Form $form)
	{
		$this->form = $form;
	}

	public function buildCsv()
	{
		$fp = fopen('php://output', 'w');
		$headings = $this->form->getHistoricFormLabels();
		fputcsv($fp, $headings);
		$this->form->submissions()->chunk(200, function($submissions) use ($fp, $headings) {
			foreach ($submissions as $submission) {
				$values = $submission->values->lists('value', 'label');
				$row = $this->getRow($headings, $values);
				fputcsv($fp, $row);
			}
		});

		fclose($fp);
	}

	/*
	 * Match up submission values with the form fields
	 *
	 * @param $headings array
	 * @param $values array
	 *
	 * @return mixed
	 */
	protected function getRow($headings, $values)
	{
		$row = [];
		foreach ($headings as $i => $heading) {
			if ( ! isset($values[$heading])) {
				$row[] = '-';
			} else {
				$row[] = $values[$heading];
			}
		}

		return $row;
	}
}
