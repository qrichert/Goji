<?php

	namespace Goji\Core;

	use PDO;
	use Exception;

	/**
	 * Class DataBase
	 *
	 * @package Goji\Core
	 */
	class DataBase extends PDO {

		/* <ATTRIBUTES> */

		private $m_dataBaseID;

		/* <CONSTANTS> */

		const CONFIG_FILE = '../config/database.json';

		/**
		 * DataBase constructor.
		 *
		 * Loads config file (/config/database.json) and creates new PDO from it.
		 *
		 * The first database appearing in the config and that works is selected.
		 * You could have a production, test and local one for example.
		 *
		 * To have multiple databases, do it like this (in config file):
		 *
		 * ```json
		 * {
		 *      "production": {
		 *          "prefix": "mysql",
		 *          "host": "hostname",
		 *          "dbname": "databasename",
		 *          "port": 3306,
		 *          "username": "username",
		 *          "password": "userpassword"
		 *      },
		 *      "localhost": {
		 *          "prefix": "mysql",
		 *          "host": "hostname",
		 *          "dbname": "databasename",
		 *          "username": "username",
		 *          "password": "userpassword"
		 *      }
		 * }
		 * ```
		 *
		 * The database identification name is entirely up to you (here we have 'production' and 'localhost').
		 * You'll be able to access the selected one via DataBase::getdataBaseID();
		 *
		 * Usable parameters are prefix, host, port, dbname, unix_socket, charset, username, password.
		 * If a parameter is missing or null it will be ignored.
		 *
		 * @param string $configFile (optional) default = DataBase::CONFIG_FILE
		 * @throws \Exception
		 */
		public function __construct($configFile = self::CONFIG_FILE) {

			$this->m_dataBaseID = null;

			$config = ConfigurationLoader::loadFileToArray($configFile);

			$connectionSuccessful = false;
			$lastException = null;

			// For each given database, extract config & try to connect
			foreach ($config as $dataBaseID => $databaseConfig) {

				$prefix = '';
				$dsn = array();
				$username = '';
				$password = '';

				// Extract configuration
				foreach ($databaseConfig as $parameter => $value) {

					// null
					if (!isset($value))
						continue;

					// Look for prefix

					if ($parameter == 'prefix') {
						$prefix = strval($value);
						continue;
					}

					// Look for username and password, not part of DSN

					if ($parameter == 'username') {
						$username = strval($value);
						continue;
					}

					if ($parameter == 'password') {
						$password = strval($value);
						continue;
					}

					// If not username or password, it is part of DSN

					// 3306 -> "3306"
					if (is_numeric($value))
						$value = strval($value);

					// "host" => "localhost" -> "host=localhost"
					if (is_string($value))
						$dsn[] = $parameter . '=' . $value;
				}

				// "host=localhost", "dbname=dbname" -> "host=localhost;dbname=dbname"
				$dsn = implode(';', $dsn);

				// "mysql" . ":" . "host=localhost;dbname=dbname" -> "mysql:host=localhost;dbname=dbname"
				if (!empty($prefix))
					$dsn = $prefix . ':' . $dsn;

				// Connect to database
				try {

					// Call PDO::__construct
					parent::__construct($dsn, $username, $password);

				} catch (Exception $e) {

					$lastException = $e;
					continue;
				}

				// We found the right one, now we save it and exit
				$connectionSuccessful = true;
				$this->m_dataBaseID = $dataBaseID;
				break;
			}

			// If every connection has failed, we want to see the error if possible
			if (!$connectionSuccessful && isset($lastException))
				throw $lastException;
		}

		/**
		 * Returns ID as defined in configuration file.
		 *
		 * @return string
		 */
		public function getDataBaseID() {
			return $this->m_dataBaseID;
		}
	}