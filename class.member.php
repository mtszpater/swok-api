<?php
class Member {
	public $login;
	public $password;


	function __construct($login, $password)
	{
		$this->login = $login;
		$this->password = $password;
	}


	function string()
	{
		return " $this->login $this->password ";
	}
}
?>