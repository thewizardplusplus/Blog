#!/usr/bin/env bash

source_path="."
target_path="."

function ShowHelp() {
	local -r script_name=$(basename "$0")

	echo "Usage:"
	echo -e "\t$script_name -h | --help"
	echo -e "\t$script_name [<source-path> [<target-path>]]"
	echo
	echo "Options:"
	echo -e "\t-h, --help  - show help."
	echo
	echo "Arguments:"
	echo -e "\t<source-path>  - path to backups [default: .];"
	echo -e "\t<target-path>  - path for reformered backups" \
		"[default: equals to <source-path>]."
}

function ShowError() {
	local -r message="$1"

	echo "Error: $message."
	echo ""
	ShowHelp

	exit 1
}

function ProcessOption() {
	local -r option="$1"

	case "$option" in
		-h|--help)
			ShowHelp
			exit

			;;
	esac
}

function ProcessOptions() {
	local -r options=("$@")

	local -r number_of_options=${#options[@]}
	case $number_of_options in
		0)
			;;
		1)
			local -r option="$1"
			ProcessOption "$option"

			# if the script isn't yet complete by ProcessOption(),
			# so the path passed
			source_path="$option"
			target_path="$option"

			;;
		2)
			source_path="${options[0]}"
			target_path="${options[1]}"

			;;
		*)
			ShowError "it was passed too many options"
			;;
	esac
}

function CreateTarget() {
	local -r path="$1"

	mkdir -p "$path"
}

function FindFiles() {
	local -r path="$1"
	local -r extension="$2"

	find "$path" -maxdepth 1 -name "$extension"
}

function FindDumps() {
	local -r path="$1"

	FindFiles "$path" "*.xml"
}

function CopyDump() {
	local -r dump_path="$1"

	echo "Copy dump \"$dump_path\"..."
	cp "$dump_path" "$target_path"
}

function CopyDumps() {
	local -r dumps_paths=("$@")

	for dump_path in ${dumps_paths[@]}
	do
		CopyDump "$dump_path"
	done
}

function FindBackups() {
	local -r path="$1"

	FindFiles "$path" "*.zip"
}

function GetName() {
	local -r backup_path="$1"

	basename "$backup_path" ".zip"
}

function UnpackBackup() {
	local -r backup_path="$1"

	local -r backup_name=`GetName "$backup_path"`
	unzip -p "$backup_path" "$backup_name/database_dump.xml"
}

function GetTimestamp() {
	local -r backup_path="$1"

	local -r backup_name=`GetName "$backup_path"`
	echo "$backup_name" | sed "s/backup_//"
}

function MakeDumpName() {
	local -r backup_path="$1"

	local -r timestamp=`GetTimestamp "$backup_path"`
	echo "$target_path/database_dump_$timestamp.xml"
}

function SaveDump() {
	local -r dump_name="$1"
	local -r dump_content="$2"

	echo "$dump_content" > "$dump_name"
}

function ProcessBackup() {
	local -r backup_path="$1"

	echo "Process backup \"$backup_path\"..."
	local -r database_dump=`UnpackBackup "$backup_path"`

	local -r new_dump_name=`MakeDumpName "$backup_path"`
	SaveDump "$new_dump_name" "$database_dump"
}

function ProcessBackups() {
	local -r backups_paths=("$@")

	for backup_path in ${backups_paths[@]}
	do
		ProcessBackup "$backup_path"
	done
}

ProcessOptions "$@"
CreateTarget "$target_path"

if [[ "$source_path" != "$target_path" ]]
then
	readonly dumps=`FindDumps "$source_path"`
	CopyDumps "$dumps"
fi

readonly backups=`FindBackups "$source_path"`
ProcessBackups "$backups"
