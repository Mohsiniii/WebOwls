<?php
class Config
{
	private static $config = array(
		'mysql'		=> array(
			'host'				=> '127.0.0.1',
			'username'		=> 'root',
			'password'		=> '',
			'db'					=> 'wo_raseed'
		),
		'session'	=> array(
			'name'				=> 'WO_RASEED',
			'token_name'	=> 'token'
		)
	);

	private static $configLive = array(
		'mysql'		=> array(
			'host'				=> 'Localhost',
			'username'		=> 'u4hjzntmgykvu',
			'password'		=> 'ancf79nbglrk',
			'db'					=> 'dbra2y6ewgx4un'
		),
		'session'	=> array(
			'name'				=> 'WO_RASEED',
			'token_name'	=> 'token'
		)
	);

	public static function get($path = null)
	{
		if ($path) {
			$value	= (ENVIRONMENT == 'DEPLOYMENT') ? self::$configLive : self::$config;
			$path 	= explode('/', $path);
			foreach ($path as $bit) {
				if (isset($value[$bit])) {
					$value = $value[$bit];
				}
			}
			return $value;
		}
		return false;
	}
}
