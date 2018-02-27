<?php

class MysqlConnection
{
	public $connection;
	public $const;

	public function connection()
	{
		if(!$this->connection) {
			$this->connection = new mysqli(Constants::DB_HOST, Constants::DB_USERNAME, Constants::DB_PASS, Constants::DB_NAME);
			$this->connection->set_charset('utf8');
		}

		if($this->connection === false) {
			return mysqli_connect_error();
		}

		return $this->connection;
	}

	public function query($query)
	{
		$result = mysqli_query($this->connection, $query);
		return $result;
	}
}

