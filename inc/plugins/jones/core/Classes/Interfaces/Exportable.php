<?php

Interface JB_Classes_Interfaces_Exportable
{
	/**
	 * Export multiple objects
	 *
	 * @param string $where
	 *
	 * @return string
	 */
	public static function exportMultiple($where='');

	/**
	 * Export this object
	 *
	 * @return string
	 */
	public function export();

	/**
	 * Import multiple objects
	 *
	 * @param string $data
	 *
	 * @return static[]
	 */
	public static function importMultiple($data);

	/**
	 * Import one object
	 *
	 * @param string $data
	 *
	 * @return static
	 */
	public static function import($data);
}
