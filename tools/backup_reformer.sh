#!/usr/bin/env bash

source_path="."
target_path="."

function ShowHelp() {
	local -r script_name=$(basename $0)

	echo "Usage:"
	echo -e "\t$script_name -h | --help"
	echo -e "\t$script_name [<source-path> [<target-path>]]"
	echo
	echo "Options:"
	echo -e "\t-h, --help  - show help."
	echo
	echo "Arguments:"
	echo -e "\t<source-path>  - path to backups [default: .];"
	echo -e "\t<target-path>  - path for reformered backups " \
		"[default: equals to <source-path>]."
}

function ShowError() {
	local -r message=$1

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

function FindBackups() {
	local -r path=$1

	find "$path" -maxdepth 1 -name "*.zip"
}

function ProcessBackup() {
	local -r backup_path="$1"

	echo "Processing backup \"$backup_path\"..."
}

function ProcessBackups() {
	local -r backups_paths=("$@")

	for backup_path in ${backups_paths[@]}
	do
		ProcessBackup "$backup_path"
	done
}

ProcessOptions "$@"

readonly backups=`FindBackups "$source_path"`
ProcessBackups "$backups"
