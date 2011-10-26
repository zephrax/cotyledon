<?php

$config = array ();

$config['domains'] = array (

	'cotyledon' => array (
		'environment' => ENV_DEVELOPMENT,
		'use_db' => false,
		'db' => array (
			'type' => 'mysql',
			'server' => 'localhost',
			'database' => 'mysql',
			'username' => 'root',
			'password' => '',
		),
		'core_leaves' => array (
			'components' => array ( 'all' )
		),
		'routes' => array (
			'' => array('Main', '')
		)
	),
	
	'cotyledondb' => array (
		'environment' => ENV_PRODUCTION,
		'use_db' => true,
		'type' => 'mysql',
		'server' => 'localhost',
		'database' => 'db',
		'username' => 'db',
		'password' => 'db',
	),
);
