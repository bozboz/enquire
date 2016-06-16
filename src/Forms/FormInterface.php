<?php

namespace Bozboz\Enquire\Forms;

interface FormInterface
{
	public function scopeActive($query);

	public function scopeForPath($query, $path);

	public function getPageListAttribute();

	public function setPageListAttribute($pageList);

	public function getHtmlIdAttribute();

	public function getFileInputs();

	public function fields();

	public function paths();
}