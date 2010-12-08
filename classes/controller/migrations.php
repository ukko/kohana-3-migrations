<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Migrations extends Controller
{
	const DELIMITER = "===================================================================\n\n";
	const ERROR		= 0;
	const SUCCESS	= 1;


	public function __construct()
	{
		// Command line access ONLY
		if ('cli' != PHP_SAPI) {
			die('oops');
			url::redirect('/');
		}
		$this->stdout = fopen('php://stdout', 'w');
		$this->out("\n=======================[ Kohana Migrations ]=======================\n\n");
		$this->migrations = new Migrations();
	}

	public function __destruct()
	{
		fclose($this->stdout);
		exit;
	}

	public function out($line = "\n")
	{
		fwrite($this->stdout, $line);
		fflush($this->stdout);
	}

	/**
	 * Display status
	 */
	public function action_index()
	{
		$this->_print_status();
		$this->out();
		$this->out(self::DELIMITER);
	}

	/**
	 * Alias for display status
	 * @return type
	 */
	public function action_status()
	{
		return $this->action_index();
	}

	/**
	 * Increase on one or an specified number
	 * @param int $version
	 */
	public function action_up($version = null)
	{
		$this->_migrate($version);
	}

	/**
	 * Decrease on one or an specified number
	 * @param int $version
	 */
	public function action_down($version = null)
	{
		$this->_migrate($version, true);
	}

	protected function _migrate($version, $down = false)
	{
		if (is_null($version)) {
			$version = $this->migrations->get_schema_version();

			$version = $down ? --$version : ++$version;
		}

		$current_version = $this->migrations->get_schema_version();
		$last_version = $this->migrations->last_schema_version();

		$direction = ( $down ) ? 'DOWN' : 'UP';

		$this->_print_status('Migrate');

		$out = PHP_EOL . PHP_EOL . self::DELIMITER . PHP_EOL;
		$out .= "  Requested Migration: $version" . PHP_EOL . "            Migrating: $direction";
		$out .= PHP_EOL . self::DELIMITER;
		$this->out($out);

		if ($version > $last_version OR $version < 0) {
			return $this->out($this->color("\tMigration not found", self::ERROR) . PHP_EOL . PHP_EOL . self::DELIMITER);
		}


		if ($down) {
			if ($version >= $current_version) {
				$this->out($this->color("  Nothing To Do!", self::ERROR));
			} else {
				$this->migrations->migrate($this, $current_version, $version);
			}
		} else {
			if ($version <= $current_version) {
				$this->out($this->color("  Nothing To Do!", self::ERROR));
			} else {
				$this->migrations->migrate($this, $current_version, $version);
			}
		}
		$this->out(PHP_EOL . PHP_EOL . self::DELIMITER);
		$this->_print_status();
		$this->out(PHP_EOL . self::DELIMITER);
	}

	protected function color($text, $type)
	{
		if ($type == self::ERROR) {
			return "\033[01;38;5;160m" . $text . "\033[39m";
		} elseif ($type == self::SUCCESS) {

		}
	}


	protected function _print_status()
	{
		$current_version = $this->migrations->get_schema_version();
		$last_version = $this->migrations->last_schema_version();
		$out = "    Current Migration: $current_version" . PHP_EOL;
		$out .= "     Latest Migration: $last_version" . PHP_EOL;
		$this->out($out);
	}

}
