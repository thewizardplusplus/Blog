<?php

class Constants {
	const DATABASE_HOST =                 'localhost';
	const DATABASE_NAME =                 'blog';
	const DATABASE_USER =                 'root';
	const DATABASE_PASSWORD =             '';
	const COPYRIGHT_START_YEAR =          2013;
	const MAXIMAL_LENGTH_OF_TITLE_FIELD = 255;
	const CUT_TAG_PATTERN =               '/<cut\s*\/>/';
	// 30 days
	const REMEMBER_DURATION_IN_S =        2592000;
	// relatively at /protected/controllers
	const FILES_RELATIVE_PATH =           '/../../files';
	// relatively at /protected/controllers
	const BACKUPS_RELATIVE_PATH =         '/../../backups';
}
