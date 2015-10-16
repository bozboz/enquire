<?php

namespace Bozboz\Enquire\Forms;

use Illuminate\Support\Facades\Request;

class FormRepository implements FormRepositoryInterface
{
	private $form;

	public function __construct(FormInterface $form)
	{
		$this->form = $form;
	}

	public function find($id)
	{
		return $this->form->with('fields')->find($id);
	}

	public function getForPath(string $path)
	{
		return $htis->form->forPath($path)->with('fields')->get();
	}

	public function getForCurrentPath()
	{
		return $this->getForPath(Request::path());
	}
}