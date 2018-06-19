<?php

namespace Bozboz\Enquire\Forms\Fields;

use Bozboz\Enquire\Submissions\Value;

class FileUpload extends Field
{
    private $filename;
    protected $view = 'enquire::partials.file';

    public function logValue($submission, $input)
    {
        $value = $this->uploadFile($this->getFile($input));

        $value = new Value([
            'label' => $this->label,
            'value' => $value
        ]);
        $value->submission()->associate($submission);
        $value->save();
    }

    public function formatInputForEmail($input)
    {
        $file = $this->getFile($input);

        if ( ! $file) {
            return;
        }

        return link_to(url($this->getStoragePath() . '/' . $this->getFilename($file)));
    }

    protected function getFile($input)
    {
        if ( ! key_exists($this->name, $input)) {
            return false;
        }
        return $input[$this->name];
    }

    protected function getStoragePath()
    {
        return 'uploads/'.str_slug($this->form->name);
    }

    protected function getFilename($file)
    {
        if (isset($this->filename)) {
            return $this->filename;
        } else {
            $this->filename = time() . '-' . str_replace(' ', '-', $file->getClientOriginalName());
            return $this->filename;
        }
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
