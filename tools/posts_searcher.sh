#!/usr/bin/env bash

query=""
backups_path="."
tmp_storage_path="."

function ShowHelp() {
	local -r script_name=`basename "$0"`

	echo "Usage:"
	echo -e "\t$script_name -h | --help"
	echo -e "\t$script_name <query> [<backups-path> [<tmp-storage-path>]]"
	echo
	echo "Options:"
	echo -e "\t-h, --help  - show help."
	echo
	echo "Arguments:"
	echo -e "\t<query>             - search query;"
	echo -e "\t<backups-path>      - path to backups [default: .];"
	echo -e "\t<tmp-storage-path>  - path for reformered backups" \
		"[default: equals to <backups-path>]."
}

function ShowError() {
	local -r message="$1"

	echo "Error: $message."
	echo ""
	ShowHelp

	exit 1
}

function CreateTarget() {
	local -r path="$1"

	mkdir -p "$path"
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
			# so the query passed
			query="$option"

			;;
		2)
			query="${options[0]}"
			backups_path="${options[1]}"
			tmp_storage_path="${options[1]}"

			;;
		3)
			query="${options[0]}"
			backups_path="${options[1]}"

			tmp_storage_path="${options[2]}"
			CreateTarget "$tmp_storage_path"

			;;
		*)
			ShowError "it was passed too many options"
			;;
	esac
}

function ValidateOptions() {
	if [[ "$query" == "" ]]
	then
		ShowError "<query> can't be empty"
	fi
}

function ProcessAndValidateOptions() {
	local -r options=("$@")

	ProcessOptions "${options[@]}"
	ValidateOptions
}

function GetScriptPath() {
	dirname "$0"
}

function UnpackBackups() {
	local -r source_path="$1"
	local -r target_path="$2"

	local -r script_path=`GetScriptPath`
	"$script_path/backups_unpacker.sh" "$source_path" "$target_path"
}

function FindDumpsMatchedQuery() {
	local -r base_path="$1"
	local -r query="$2"

	grep -lEi -e "$query" "$base_path"/*.xml
}

ProcessAndValidateOptions "$@"
# UnpackBackups "$backups_path" "$tmp_storage_path"
readonly dumps=`FindDumpsMatchedQuery "$tmp_storage_path" "$query"`
echo "$dumps"
