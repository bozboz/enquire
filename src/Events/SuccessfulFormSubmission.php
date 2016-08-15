<?php

namespace Bozboz\Enquire\Events;

use Bozboz\Enquire\Forms\FormInterface;
use Illuminate\Queue\SerializesModels;

class SuccessfulFormSubmission
{
    use SerializesModels;

    public $form;

    public $input;

    public $recipients;

    /**
     * Create a new event instance
     *
     * @param FormInterface $form
     * @param array         $input
     * @param array         $recipients
     */
    public function __construct(FormInterface $form, $input, $recipients)
    {
        $this->form = $form;
        $this->input = $input;
        $this->recipients = $recipients;
    }
}
