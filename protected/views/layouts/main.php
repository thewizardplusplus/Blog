<?php
	/* @var $this CController */

	Yii::app()->getClientScript()->registerCoreScript('jquery');
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/purl.js'),
		CClientScript::POS_HEAD
	);
	Yii::app()->getClientScript()->registerScriptFile(
		CHtml::asset('scripts/tag_cloud.js'),
		CClientScript::POS_HEAD
	);
	if (!Yii::app()->user->isGuest) {
		Yii::app()->getClientScript()->registerScriptFile(
			CHtml::asset('scripts/backuping.js'),
			CClientScript::POS_HEAD
		);
	}

	$with_aside = ($this->route == 'post/list' or $this->route == 'post/view');

	$copyright_years = Constants::COPYRIGHT_START_YEAR;
	$current_year = date('Y');
	if ($current_year > Constants::COPYRIGHT_START_YEAR) {
		$copyright_years .= '-' . $current_year;
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset = "utf-8" />
		<meta name = "viewport" content = "width=device-width" />
		<link
			rel = "icon"
			type = "image/png"
			href = "<?= Yii::app()->request->baseUrl ?>/images/logo.png" />

		<link rel = "stylesheet" href = "<?php echo Yii::app()->request->
			baseUrl; ?>/bootstrap/css/bootstrap.min.css" />
		<link rel = "stylesheet" href = "<?php echo Yii::app()->request->
			baseUrl; ?>/jquery-ui/css/theme/jquery-ui.min.css" />
		<link rel = "stylesheet" href = "<?php echo Yii::app()->request->
			baseUrl; ?>/styles/blog.css" />

		<title><?php echo CHtml::encode($this->pageTitle); ?></title>

		<script src = "<?php echo Yii::app()->request->baseUrl;
			?>/bootstrap/js/bootstrap.min.js"></script>
		<script src = "<?php echo Yii::app()->request->baseUrl;
			?>/jquery-ui/js/jquery-ui.min.js"></script>
		<script src = "<?php echo Yii::app()->request->baseUrl;
			?>/scripts/ace/ace.js"></script>
	</head>
	<body
		<?= !Yii::app()->user->isGuest
			? 'style = "padding-top: 50px;"'
			: '' ?>>
		<?php if (!Yii::app()->user->isGuest) { ?>
			<nav class = "navbar navbar-default navbar-fixed-top navbar-inverse">
				<section class = "container">
					<div class = "navbar-header">
						<button
							class = "navbar-toggle"
							data-toggle = "collapse"
							data-target = "#navbar-collapse">
							<span class = "icon-bar"></span>
							<span class = "icon-bar"></span>
							<span class = "icon-bar"></span>
						</button>
						<a
							class = "navbar-brand"
							href = "<?= Yii::app()->homeUrl ?>">
							<?= Yii::app()->name ?>
						</a>
					</div>

					<div
						id = "navbar-collapse"
						class = "collapse navbar-collapse">
						<a
							class = "btn btn-primary navbar-btn navbar-left"
							href = "<?= $this->createUrl('post/create') ?>">
							<span class = "glyphicon glyphicon-plus"></span>
							Новый пост
						</a>
						<?php $this->widget(
							'zii.widgets.CMenu',
							array(
								'items' => array(
									array(
										'label' => 'Файлы',
										'url' => array('file/list')
									),
									array(
										'label' => 'Бекапы',
										'url' => array('backup/list')
									)
								),
								'htmlOptions' => array(
									'class' => 'nav navbar-nav'
								)
							)
						); ?>
						<button
							class = "btn btn-primary navbar-btn navbar-left create-backup-button"
							data-create-backup-url = "<?= $this->createUrl('backup/new') ?>"
							data-get-log-url = "<?= $this->createUrl('backup/log') ?>">
							<img
								src = "<?= Yii::app()->request->baseUrl ?>/images/processing-icon.gif"
								alt = "..." />
							<span class = "glyphicon glyphicon-compressed">
							</span>
							<span>Бекап</span>
						</button>
						<?php $this->widget(
							'zii.widgets.CMenu',
							array(
								'items' => array(
									array(
										'label' => 'Параметры',
										'url' => array('parameters/update')
									)
								),
								'htmlOptions' => array(
									'class' => 'nav navbar-nav'
								)
							)
						); ?>
						<?= CHtml::beginForm(
							$this->createUrl('site/logout'),
							'post',
							array('class' => 'navbar-form navbar-right')
						) ?>
							<?= CHtml::htmlButton(
								'<span class = "glyphicon glyphicon-log-out">'
									. '</span> Выход',
								array(
									'class' => 'btn btn-primary',
									'type' => 'submit'
								)
							) ?>
						<?= CHtml::endForm() ?>
					</div>
				</section>
			</nav>
		<?php } ?>

		<section class = "container">
			<?php if (Yii::app()->user->isGuest) { ?>
				<header class = "page-header">
					<h1>
						<img
							src = "<?= Yii::app()->request->baseUrl ?>/images/logo.png"
							alt = "logo" />
						<a href = "<?= $this->createUrl('post/list') ?>">
							Хроники завоевания мира...
						</a>
					</h1>
				</header>
			<?php } ?>

			<?php if ($with_aside) { ?>
			<div class = "row">
				<div class = "col-md-9">
			<?php } ?>
			<?php echo $content; ?>
			<?php if ($with_aside) { ?>
				</div>
				<div class = "col-md-3">
					<div
						class = "panel panel-default clearfix tag-cloud"
						data-update-url = "<?= $this->createUrl(
							'post/tagList'
						) ?>"
						data-tag-url = "<?= $this->createUrl(
							'post/list'
						) ?>">
					</div>

					<div class = "hidden-xs hidden-sm">
						<a
							class = "twitter-timeline"
							href = "https://twitter.com/thewizardpp"
							height = "400"
							data-widget-id = "482893293462622209"
							data-chrome = "nofooter">
						</a>
						<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
					</div>

					<div class = "hidden-xs hidden-sm">
						<script
							src = "http://octocard.in/o.js"
							data-name = "thewizardplusplus"
							data-modules = "base,repos"
							data-reposNum = "-1"
							data-theme = "azzura-black">
						</script>
					</div>
				</div>
			</div>
			<?php } ?>

			<footer>
				<hr />
				<p class = "without-bottom-margin">
					<?= Yii::app()->name ?>, <?= Constants::APP_VERSION ?>
				</p>
				<p class = "unimportant-text without-bottom-margin">
					&copy; thewizardplusplus, <?= $copyright_years ?>
				</p>
				<p class = "unimportant-text">
					Текст и изображения доступны по лицензии CC-BY, если
					не указано иное.
				</p>
			</footer>
		</section>
	</body>
</html>
