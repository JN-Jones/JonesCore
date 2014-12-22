<?php

Interface JB_Classes_Interfaces_Exportable
{
	// Export multiple objects
	public static function exportMultiple($where='');

	// Export this object
	public function export();

	// Import multiple objects
	public static function importMultiple($data);

	// Import one object
	public static function import($data);
}