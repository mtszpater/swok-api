<?php
class DatabaseManager {
	private $connection = null;

	public function __construct($host = 'localhost', $port = '5432', $dbname = 'springbootdb', $user = 'ja', $password = '' ){
		$this->connection = pg_connect("host=".$host." port=".$port." dbname=".$dbname." user=".$user." password=".$password."");
	}

	public function has_perm($user_login, $user_password){

		$query = pg_query($this->connection, "select * from member WHERE 	login='$user_login' AND password='$user_password';");

		if( pg_num_rows($query) == 1) return true; else return false;
	}

}
?>