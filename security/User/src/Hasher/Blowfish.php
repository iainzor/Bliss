<?php
namespace User\Hasher;

class Blowfish implements HasherInterface
{
	public function hash($value) 
	{
		if (function_exists("password_hash")) {
			return password_hash($value, PASSWORD_BCRYPT, [
				"cost" => 11
			]);
		} else {
			return crypt($value);
		}
	}

	public function matches($value, $hash) 
	{
		if (function_exists("password_verify")) {
			return password_verify($value, $hash);
		} else {
			return crypt($value, $hash) == $hash;
		}
	}
}