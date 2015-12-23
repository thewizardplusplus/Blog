#!/usr/bin/env bash

function ProcessBackup {
	local -r backup_name=$1;
	local -r backup_data=$(unzip -p $backup_name $backup_name/database_dump.sql)
	local -r posts_data=$(echo "$backup_data" | awk '/^INSERT INTO `blog_posts`$/{ echo = 1 } /^$/{echo = 0} echo' | grep "^\s\?('[0-9]\+'")
	echo $posts_data
}

IFS=$'\n'
readonly backup_list=$(find *.zip)
for backup in ${backup_list[@]}
do
	backup_name=$(basename $backup .zip)
	echo "Processing $backup_name..." >&2

	new_backup_name=$(echo $backup_name | sed "s/backup\(.*\)/database_dump\1.sql/")
	ProcessBackup $backup_name > $new_backup_name
done
