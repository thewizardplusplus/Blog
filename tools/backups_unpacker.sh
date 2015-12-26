#!/usr/bin/env bash

################################################################################
# Global variables.
################################################################################
source_path="."
target_path="."
################################################################################

################################################################################
# Options processing.
################################################################################
function ShowHelp() {
	local -r script_name=`basename "$0"`

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
			# so the path passed
			source_path="$option"
			target_path="$option"

			;;
		2)
			source_path="${options[0]}"

			target_path="${options[1]}"
			CreateTarget "$target_path"

			;;
		*)
			ShowError "it was passed too many options"
			;;
	esac
}
################################################################################

################################################################################
# Utils functions.
################################################################################
function FindFiles() {
	local -r path="$1"
	local -r extension="$2"

	find "$path" -maxdepth 1 -name "$extension"
}

function GetTimestamp() {
	local -r path="$1"

	echo "$path" | sed -r "s/.*([[:digit:]]{4}(-[[:digit:]]{2}){5}).*/\1/"
}

function GetDumpName() {
	local -r path="$1"

	local -r timestamp=`GetTimestamp "$path"`
	echo "$target_path/database_dump_$timestamp.xml"
}

function DecodeDump() {
	local -r source_filename="$1"
	local -r target_filename="$2"
	local -r script_path="$3"

	"$script_path/dump_decoder.py" "$source_filename" "$target_filename"
}

function GetScriptPath() {
	dirname "$0"
}
################################################################################

################################################################################
# Dumps processing.
################################################################################
function FindDumps() {
	local -r path="$1"

	FindFiles "$path" "*.xml"
}

function ProcessDump() {
	local -r dump_path="$1"
	local -r script_path="$2"

	echo "Copy and decode dump \"$dump_path\"..."

	local -r new_dump_path=`GetDumpName "$dump_path"`
	DecodeDump "$dump_path" "$new_dump_path" "$script_path"
}

function ProcessDumps() {
	local -r dumps_paths=("$@")

	local -r script_path=`GetScriptPath`
	for dump_path in ${dumps_paths[@]}
	do
		ProcessDump "$dump_path" "$script_path"
	done
}

function FindAndProcessDumps() {
	local -r source_path="$1"

	if [[ "$source_path" != "$target_path" ]]
	then
		local -r dumps=`FindDumps "$source_path"`
		ProcessDumps "$dumps"
	fi
}
################################################################################

################################################################################
# Backups processing.
################################################################################
function FindBackups() {
	local -r path="$1"

	FindFiles "$path" "*.zip"
}

function GetBackupName() {
	local -r backup_path="$1"

	basename "$backup_path" ".zip"
}

function UnpackBackup() {
	local -r backup_path="$1"

	local -r backup_name=`GetBackupName "$backup_path"`
	unzip -p "$backup_path" "$backup_name/database_dump.xml"
}

function SaveDump() {
	local -r dump_name="$1"
	local -r dump_content="$2"

	echo "$dump_content" > "$dump_name"
}

function ProcessBackup() {
	local -r backup_path="$1"
	local -r script_path="$2"

	echo "Unpack backup \"$backup_path\"..."
	local -r database_dump=`UnpackBackup "$backup_path"`

	local -r new_dump_name=`GetDumpName "$backup_path"`
	SaveDump "$new_dump_name" "$database_dump"

	echo "Decode dump \"$new_dump_name\"..."
	DecodeDump "$new_dump_name" "$new_dump_name" "$script_path"
}

function ProcessBackups() {
	local -r backups_paths=("$@")

	local -r script_path=`GetScriptPath`
	for backup_path in ${backups_paths[@]}
	do
		ProcessBackup "$backup_path" "$script_path"
	done
}

function FindAndProcessBackups() {
	local -r source_path="$1"

	local -r backups=`FindBackups "$source_path"`
	ProcessBackups "$backups"
}
################################################################################

################################################################################
# Main code.
################################################################################
ProcessOptions "$@"
FindAndProcessDumps "$source_path"
FindAndProcessBackups "$source_path"
################################################################################
