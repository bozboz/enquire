<?php

namespace Bozboz\Enquire\Forms;

interface FormRepositoryInterface
{
	public function __construct(FormInterface $form);

	/**
	 * Fetch form by id
	 */
	public function find($id);

	/**
	 * Fetch form for current url
	 */
	public function getForCurrentPath();

	/**
	 * Fetch form for specific url
	 */
	public function getForPath($path);
}