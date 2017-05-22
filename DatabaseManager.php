<?php
include_once "DatabaseOperation.php";

class DatabaseManager implements DatabaseOperation {
	private $connection = null;

	public function __construct($host = 'localhost', $port = '5432', $dbname = 'springbootdb', $user = 'ja', $password = '' ){
		$this->connection = pg_connect("host=".$host." port=".$port." dbname=".$dbname." user=".$user." password=".$password."");
	}

    /**
     * @return bool
     */

    public function userExists($user_login, $user_password){

		$query = pg_query($this->connection, "select * from member WHERE login='$user_login' AND password='$user_password';");

        return pg_num_rows($query) == 1 ? true : false;
	}

	public function isAdmin($user_login, $user_password){

        $query = pg_query($this->connection, "select * from member WHERE login='$user_login' AND password='$user_password' AND admin='1';");

        return pg_num_rows($query) == 1 ? true : false;

    }

	public function registerUser($user_login, $user_password){

        return pg_query($this->connection, "insert into member (login, password) VALUES ('$user_login', '$user_password')") ? true : false;

    }

    public function isLoginBusy($user_login){
        $query = pg_query($this->connection, "select * from member WHERE login='$user_login'");

        return pg_num_rows($query) == 1 ? true : false;
    }

    function __destruct()
    {
        pg_close($this->connection);
    }

}
?>