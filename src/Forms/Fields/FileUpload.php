<?php

namespace Bozboz\Enquire\Forms\Fields;

use Bozboz\Enquire\Submissions\Value;

class FileUpload extends Field
{
    protected $view = 'enquire::partials.file';

    public function logValue($submission, $input)
    {
        $value = $this->uploadFile($input[$this->name]);

        $value = new Value([
            'label' => $this->label,
            'value' => $value
        ]);
        $value->submission()->associate($submission);
        $value->save();
    }

    public function formatInputForEmail($input)
    {
        $file = $input[$this->name];

        if ( ! $file) {
            return;
        }

        return link_to(url($this->getStoragePath() . '/' . $this->getFilename($file)));
    }

    protected function getStoragePath()
    {
        return 'uploads/'.str_slug($this->form->name);
    }

    protected function getFilename($file)
    {
        return time() . '-' . str_replace(' ', '-', $file->getClientOriginalName());
    }

    protected function uploadFile($file)
    {
        if ( ! $file) {
            return false;
        }

        $file->move(public_path($this->getStoragePath()), $this->getFilename($file));
        return url($this->getStoragePath() . '/' . $this->getFilename($file));
    }
}
