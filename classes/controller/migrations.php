<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Migrations extends Controller
{
	/**
	 * Migrations object
	 * @var Migrations
	 */
	protected $_migrations;

	/**
	 * Console object
	 * @var Console
	 */
	protected $_console;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->_console = Console::instance();
		$this->_console->out(Console::format("Kohana Migrations", Console::HEADER));
		$this->_migrations = new Migrations('default', $this->_console);
	}

	/**
	 * Display status
	 */
	public function action_index()
	{
		$this->_print_status();
		$this->_console->out_line();
	}

	/**
	 * Display migrations list & status
	 */
	public function action_status()
	{
		$this->_console->out();
		foreach ($this->_migrations->get_migrations() as $migration) {
			if ($migration->version == $this->_migrations->get_schema_version()) {
				$this->_console->out($migration->version."*\t".Console::format($migration->descr, Console::SUCCESS));
			} else {
				$this->_console->out($migration->version."\t".$migration->descr);
			}
		}
		$this->_console->out();
		$this->_console->out_line();

		return $this->action_index();
	}

	/**
	 * Increase on one or an specified number
	 * @param int $version
	 */
	public function action_up($version = null)
	{
		if ($version == 'all') {
			$version = $this->_migrations->last_schema_version();
		}
		$this->_migrate($version, Migrations::UP);
	}

	/**
	 * Decrease on one or an specified number
	 * @param int $version
	 */
	public function action_down($version = null)
	{
		$this->_migrate($version, Migrations::DOWN);
	}

	protected function _migrate($version, $direction = Migrations::UP)
	{
		if (is_null($version)) {
			$version = (int)$this->_migrations->get_schema_version();

			$version = $direction ? ++$version : --$version;
		} else {
			$version = (int)$version;
		}

		$current_version = $this->_migrations->get_schema_version();
		$last_version = $this->_migrations->last_schema_version();

		$out  = "\tRequested Migration:\t" . $version . PHP_EOL;
		$out .= "\tMigrating:\t\t" . (($direction) ? 'UP' : 'DOWN') . PHP_EOL;
		$this->_console->out($out);

		if ($version > $last_version OR $version < 0) {
			return $this->_console->out(Console::format("\tMigration not found".PHP_EOL, Console::ERROR));
		}

		$this->_console->out_line('-');

		if ( ! $direction) {
			if ($version >= $current_version) {
				return $this->_console->out(Console::format("\tNothing To Do!".PHP_EOL, Console::ERROR));
			} else {
				$this->_migrations->migrate($current_version, $version);
			}
		} else {
			if ($version <= $current_version) {
				return $this->_console->out(Console::format("\tNothing To Do!".PHP_EOL, Console::ERROR));
			} else {
				$this->_migrations->migrate($current_version, $version);
			}
		}

		$this->_console->out_line();
		$this->_print_status();
		$this->_console->out_line();
	}

	protected function _print_status()
	{
		$out = "\tCurrent:\t\t" . $this->_migrations->get_schema_version() . PHP_EOL;
		$out .= "\tLatest:\t\t\t" . $this->_migrations->last_schema_version() . PHP_EOL;

		Console::instance()->out($out);
	}

}
