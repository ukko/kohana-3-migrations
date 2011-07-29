<?php defined('SYSPATH') or die('No direct script access.');

class Migrations
{
	const DOWN	= 0;
	const UP	= 1;

	protected $config;
	protected $group;

	/**
	 * Console object
	 *
	 * @var Console
	 */
	protected $_console		= NULL;

	/**
	 * Array with Migrations_Item
	 *
	 * @var array
	 */
	protected $_migrations	= NULL;


	/**
	 * Constructor method
	 *
	 * @param type $group
	 * @return bool
	 */
	public function __construct($group = 'default', $console = NULL)
	{
		$this->_console			= $console;
		$this->config			= Kohana::$config->load('migrations');
		$this->group			= $group;
		$this->config['path']	= $this->config['path'][$group];
		$this->config['info']	= $this->config['path'] . $this->config['info'] . '/';
	}

	/**
	 * Return current number version or set 0
	 *
	 * @return int N$thisumber schema version
	 */
	public function get_schema_version()
	{
		if ( ! is_dir($this->config['path'])) {
			mkdir($this->config['path']);
		}

		if ( ! is_dir($this->config['info'])) {
			mkdir($this->config['info']);
		}

		if ( ! file_exists($this->config['info'] . 'version')) {
			$fversion = fopen($this->config['info'] . 'version', 'w');
			fwrite($fversion, '0');
			fclose($fversion);
			return 0;
		} else {
			$fversion = fopen($this->config['info'] . 'version', 'r');
			$version = fread($fversion, 11);
			fclose($fversion);
			return (int) $version;
		}
		return 0;
	}

	/**
	 * Set current number version
	 *
	 * @param int $version
	 * @return void
	 */
	public function set_schema_version($version)
	{
		$fversion = fopen($this->config['info'] . 'version', 'w');
		fwrite($fversion, $version);
		fclose($fversion);
	}

	/**
	 * Get last nubmer version
	 *
	 * @return int
	 */
	public function last_schema_version()
	{
		if (is_null($this->_migrations)) {
			$this->_migrations = $this->get_migrations();
		}

		return $this->_migrations[count($this->_migrations)]->version;
	}

	/**
	 * Return array with migrations sorted by direction
	 *
	 * @return array
	 */
	public function get_migrations()
	{
		$migrations = scandir($this->config['path']);
		$items		= array();

		foreach ($migrations as $file) {
			list ($name, $ext) = explode('.', $file);

			if (strtolower($ext) == 'sql') {
				$version = intval($name);
				$items[$version] = $this->_get_migrations_content($file, $version);
			}
		}
		return $items;
	}

	/**
	 *
	 * @param type $from
	 * @param type $to
	 */
	public function migrate($from, $to)
	{
		$migrations = $this->get_migrations();
		if ($from < $to) {
			foreach ($migrations as $index => $migration)
				if ($index > $from and $index <= $to) {
					try {
						$this->_console->out($this->run_migration($migration, self::UP));
						$this->set_schema_version($index);
					} catch (Exception $e) {
						$error = "Error running migration ".$index." UP: ".$e->getMessage().PHP_EOL;
						$this->_console->out(Console::format($error, Console::ERROR));
					}
				}
		} else {
			for ($index = $from; $index > $to; $index--) {
				try {
					$this->_console->out($this->run_migration($migrations[$index], self::DOWN));
					$this->set_schema_version($index - 1);
				} catch (Exception $e) {
					$error = "Error running migration ".$index." DOWN: ".$e->getMessage().PHP_EOL;
					$this->_console->out(Console::format($error, Console::ERROR));
				}
			}
		}
	}

	/**
	 * Run exequte RAW SQL query
	 *
	 * @param Migrations_Item	$migration
	 * @param int				$direction	self::UP || self::DOWN
	 * @return type
	 */
	public function run_migration($migration, $direction = self::UP)
	{
		$queries = explode(';', ($direction == self::UP) ? $migration->up : $migration->down);

		$db = Database::instance($this->group);

		foreach ($queries as $query) {
			$query = trim($query);
			if (empty($query)) {
				continue;
			}
			$db->query(Database::UPDATE, $query, false);
		}

		return "Migrated: ".Console::format($migration->filename, Console::SUCCESS)." ".$migration->descr;
	}

	/**
	 * Fill data Migrations_Item
	 *
	 * @param string	$file
	 * @param int		$version
	 * @return Migrations_Item
	 */
	private function _get_migrations_content($file, $version)
	{
		$lines	= explode(PHP_EOL, file_get_contents($this->config['path'] . $file));
		$flag	= NULL;
		$descr	= '';
		$up		= '';
		$down	= '';

		foreach ($lines as $line) {
			// Comments
			if (substr($line, 0, 2) == '--') {
				$comment = trim(substr($line, 2));
				if (stristr($comment, 'UP')) {
					$flag = self::UP;
				}elseif (stristr($comment, 'DOWN')) {
					$flag = self::DOWN;
				} elseif ($flag === NULL) {
					$descr .= $comment . PHP_EOL;
				}
			// SQL query
			} else {
				if ($flag === self::UP) {
					$up	.= $line . PHP_EOL;
				} elseif ($flag === self::DOWN) {
					$down .= $line . PHP_EOL;
				}
			}
		}
		return new Migrations_Item($version, $file, $up, $down, $descr);
	}

}
