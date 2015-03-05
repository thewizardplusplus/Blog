<?php

require_once('Constants.php');

return array(
	'name' => 'Wizard Blog',
	'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
	'defaultController' => 'post/list',
	'language' => 'ru',
	'preload' => array('log'),
	'import' => array(
		'application.models.*',
		'application.components.*',
		'application.config.Constants'
	),
	'components' => array(
		'user' => array('allowAutoLogin' => true),
		'urlManager' => array(
			'urlFormat' => 'path',
			'showScriptName' => FALSE,
			'rules' => array(
				'login' => 'site/login',
				'logout' => 'site/logout',
				'posts' => 'post/list',
				'post/<id:\d+>-<title:.+>' => 'post/view',
				'post/<id:\d+>' => 'post/view',
				'post/new' => 'post/create',
				'post/<id:\d+>/update' => 'post/update',
				'post/<id:\d+>/delete' => 'post/delete',
				'files' => 'file/list',
				'files/rename' => 'file/rename',
				'files/delete' => 'file/remove',
				'parameters' => 'parameters/update',
				'backups' => 'backup/list',
				'backups/new' => 'backup/create'
			)
		),
		'db' => array(
			'connectionString' => 'mysql:host=' . Constants::DATABASE_HOST .
				';dbname=' . Constants::DATABASE_NAME,
			'emulatePrepare' => true,
			'username' => Constants::DATABASE_USER,
			'password' => Constants::DATABASE_PASSWORD,
			'charset' => 'utf8',
			'tablePrefix' => 'blog_'
		),
		'log' => array(
			'class'=>'CLogRouter',
			'routes' => array(
				array(
					'class' => 'CFileLogRoute',
					'logFile' => 'backups.log',
					'levels' => 'info',
					'categories' => 'backups'
				),
				array(
					'class' => 'CFileLogRoute',
					'levels' => 'trace, info, warning, error'
				),
				array(
					'class' => 'CWebLogRoute',
					'levels' => 'trace, info, warning, error'
				)
			),
		),
		'errorHandler' => array('errorAction' => 'site/error')
	)
);
