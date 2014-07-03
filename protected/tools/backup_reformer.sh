#!/bin/bash

function ProcessBackup {
	local -r backup_name=$1;

	echo -e '\tPrepare data...' >&2
	local -r backup_data=$(unzip -p $backup_name $backup_name/database_dump.sql)
	local -r posts_data=$(echo "$backup_data" | awk '/^INSERT INTO `blog_posts`$/{ echo = 1 } /^$/{echo = 0} echo' | grep "^\s\?('[0-9]\+'")
	local posts_count=$(echo "$posts_data" | wc -l)

	echo '<?xml version = "1.0" encoding = "utf-8" ?>'
	echo '<blog>'

	local post_index=1
	for post in ${posts_data[@]}
	do
		local progress=$((100 * post_index / posts_count))
		local post_index=$((post_index + 1))
		echo -e "\t\tProcessing point ($progress%)..." >&2

		local create_time=$(echo "$post" | awk -F "', '" '{ print $(NF - 3); }' | sed "s/ /T/")
		local modify_time=$(echo "$post" | awk -F "', '" '{ print $(NF - 2) }' | sed "s/ /T/")
		local published=$(echo "$post" | awk -F "', '" '{ print $(NF); }' | sed 's/0.*/false/;s/1.*/true/')
		echo -e "\t<post create-time = \"$create_time\" modify-time = \"$modify_time\" published = \"$published\">"

		local title=$(echo "$post" | awk -F "', '" '{ print $2; }' | base64 -w 0)
		echo -e "\t\t<title>$title</title>"

		local text=$(echo "$post" | awk -F "', '" '{ print $3; }' | base64 -w 0)
		echo -e "\t\t<text>$text</text>"

		local tags=$(echo "$post" | awk -F "', '" '{ print $(NF - 1); }' | base64 -w 0)
		echo -e "\t\t<tags>$tags</tags>"

		echo -e '\t</post>'
	done

	echo '</blog>'
}

IFS=$'\n'
readonly backup_list=$(find *.zip)
for backup in ${backup_list[@]}
do
	backup_name=$(basename $backup .zip)
	echo "Processing $backup_name..." >&2

	new_backup_name=$(echo $backup_name | sed "s/backup\(.*\)/database_dump\1.xml/")
	ProcessBackup $backup_name > $new_backup_name
done
