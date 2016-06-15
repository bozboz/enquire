<?php

namespace Admin;

use Bozboz\Admin\Controllers\ModelAdminController;
use Bozboz\Enquire\Forms\FormDecorator;

class FormAdminController extends ModelAdminController
{
	public function __construct(FormDecorator $decorator)
	{
		parent::__construct($decorator);
	}
}
