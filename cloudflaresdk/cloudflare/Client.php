<?php
namespace cloudflaresdk\cloudflare;

use cloudflaresdk\cloudflare\Operation;
class Client
{
	private static $user;
	private static $myDomains = array();
	private static $operations;
	private static $selected = null;
	
	function __construct($token, $email)
	{
		self::$user = array('tkn'=>$token, 'email'=>$email);

	}
  public function select($domain){
  		return new Operation($domain, self::$user);
  }
}
