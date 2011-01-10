<?php defined('SYSPATH') or die('No direct script access.');

class Migrations_Item
{
	private $_version	= NULL;
	private $_filename	= NULL;
	private $_up		= NULL;
	private $_down		= NULL;
	private $_descr		= NULL;
	protected $_loaded	= FALSE;


	public function __construct($version, $filename, $up = NULL, $down = NULL, $descr = NULL)
	{
		$this->_version = $version;
		$this->_filename= $filename;
		$this->_up		= $up;
		$this->_down	= $down;
		$this->_descr	= $descr;

		if ($up or $down) {
			$this->_loaded = TRUE;
		}
	}

	public function __get($name)
	{
		$name = '_'.$name;
		if (isset($this->{$name})) {
			return $this->{$name};
		} else {
			throw new Exception('Undefined property', 100);
		}
	}

	public function __set($name, $value)
	{
		$name = '_'.$name;

		if (isset ($this->{$name})) {
			$this->{$name} = $value;
		} else {
			throw new Exception('Undefined property', 100);
		}
	}
}