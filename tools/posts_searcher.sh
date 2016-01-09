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

function FindDumps() {
	local -r base_path="$1"
	local -r search_query="$2"
	local -r matching_flag="$3"

	if [[ "$matching_flag" == MATCH ]]
	then
		grep -lEi -e "$search_query" "$base_path"/*.xml
	else
		if [[ "$matching_flag" == NOT_MATCH ]]
		then
			grep -LEi -e "$search_query" "$base_path"/*.xml
		fi
	fi
}

function RemoveDumps() {
	local -r dump_list=("$@")

	for dump in ${dump_list[@]}
	do
		rm "$dump"
	done
}

function FindAndRemoveDumps() {
	local -r base_path="$1"
	local -r search_query="$2"

	local -r dump_list=`FindDumps "$base_path" "$search_query" NOT_MATCH`
	RemoveDumps "$dump_list"
}

function OutputDumps() {
	local -r dump_list=("$@")

	echo "$dump_list" | sort -r
}

function FindAndOutputDumps() {
	local -r base_path="$1"
	local -r search_query="$2"

	local -r dump_list=`FindDumps "$base_path" "$search_query" MATCH`
	OutputDumps "$dump_list"
}

ProcessAndValidateOptions "$@"
UnpackBackups "$backups_path" "$tmp_storage_path"
FindAndRemoveDumps "$tmp_storage_path" "$query"
FindAndOutputDumps "$tmp_storage_path" "$query"
