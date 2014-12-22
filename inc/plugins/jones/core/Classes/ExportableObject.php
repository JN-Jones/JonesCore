<?php

abstract class JB_Classes_ExportableObject extends JB_Classes_StorableObject implements JB_Classes_Interfaces_Exportable
{
	// Export multiple objects
	public static function exportMultiple($where='')
	{
		$classes = static::getAll($where);
		$xml = "<objects>\r\n";

		foreach($classes as $class)
		{
			$xml .= $class->export();
			$xml .= "\r\n";
		}

		$xml .= "</objects>";
		return $xml;
	}

	// Export this object
	public function export()
	{
		$class = strtolower(get_called_class());
		$xml = "<object class=\"{$class}\">\r\n";
		foreach($this->data as $key => $attr)
		{
			if($key == "id")
			    continue;

			if(!is_numeric($attr))
			    $attr = "<![CDATA[{$attr}]]>";
			$xml .= "<{$key}>{$attr}</{$key}>\r\n";
		}
		$xml .= "</object>";
		return $xml;
	}

	// Import multiple objects
	public static function importMultiple($data)
	{
		$class = strtolower(get_called_class());
		$data = simplexml_load_string($data);
		$classes = array();

		foreach($data as $obj)
		{
			if((string)$obj['class'] != $class)
			    return false;
			$attrs = $obj->children();
			$datas = array();
			foreach($attrs as $k => $v)
			    $datas[(string)$k] = (string)$v;
			$classes[] = static::create($datas);
		}
		return $classes;
	}

	// Import one object
	public static function import($data)
	{
		$class = strtolower(get_called_class());
		$data = simplexml_load_string($data);

		if((string)$data['class'] != $class)
		    return false;
		$attrs = $data->children();
		$datas = array();
		foreach($attrs as $k => $v)
		    $datas[(string)$k] = (string)$v;
		return static::create($datas);
	}
}