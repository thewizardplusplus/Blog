<?php

return array(
	'name' => 'Хроники завоевания мира...',
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
				'posts/<tag:.+>' => 'post/list',
				'posts' => 'post/list',
				'post/<id:\d+>-<title:.+>' => 'post/view',
				'post/<id:\d+>' => 'post/view',
				'create' => 'post/create',
				'post/<id:\d+>/update' => 'post/update',
				'post/<id:\d+>/delete' => 'post/delete',
				'files' => 'site/files',
				'parameters' => 'parameters/update'
			)
		),
		'db' => array(
			'connectionString' => 'mysql:host=localhost;dbname=blog',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '',
			'charset' => 'utf8',
			'tablePrefix' => 'blog_'
		),
		'log' => array(
			'class'=>'CLogRouter',
			'routes' => array(
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
