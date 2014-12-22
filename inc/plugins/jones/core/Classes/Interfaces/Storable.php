<?php

Interface JB_Classes_Interfaces_Storable
{
	// Get all objects
	public static function getAll($where='', $options=array());

	public static function getNumber($where='');

	// Get's the object with that ID
	public static function getByID($id);

	// Saves the current object
	public function save();

	// Delete the current object
	public function delete();
}