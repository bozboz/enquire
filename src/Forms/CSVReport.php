<?php

namespace Bozboz\Enquire\Forms;

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
        fputcsv($fp, array_merge(['Date'], $headings));
        $this->form->submissions()->chunk(200, function($submissions) use ($fp, $headings) {
            foreach ($submissions as $submission) {
                $row = $this->getRow($headings, $submission);
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
    protected function getRow($headings, $submission)
    {
        $values = $submission->values->pluck('value', 'label');

        $row = [$submission->created_at->format('d M Y - H:i')];
        foreach ($headings as $i => $heading) {
            $row[] = $values->get($heading) ?: '-';
        }

        return $row;
    }
}
