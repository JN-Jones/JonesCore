<?php

Interface JB_Classes_Interfaces_Storable
{
	/**
	 * Get all objects
	 *
	 * @param string $where
	 * @param array $options
	 *
	 * @return static[]
	 */
	public static function getAll($where='', array $options=array());

	/**
	 * @param string $where
	 *
	 * @return int
	 */
	public static function getNumber($where='');

	/**
	 * Get's the object with that ID
	 *
	 * @param int $id
	 *
	 * @return static|bool
	 */
	public static function getByID($id);

	/**
	 * Saves the current object
	 *
	 * @return bool
	 */
	public function save();

	/**
	 * Delete the current object
	 */
	public function delete();
}
