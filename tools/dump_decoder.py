#!/usr/bin/env python

"""
Usage:
  {0:s} -h | --help
  {0:s} <dump-file> [<decoded-dump-file>]

Options:
  -h, --help  - show help.
"""

import docopt
import os.path

def parse_options():
	script_name = os.path.basename(__file__)
	return docopt.docopt(__doc__.format(script_name))

def process_options(options):
	if options["<decoded-dump-file>"] == None:
		decoded_dump_file = os.path.basename(options["<dump-file>"])
		options["<decoded-dump-file>"] = decoded_dump_file

def parse_parameters():
	options = parse_options()
	process_options(options)

	return options

parameters = parse_parameters()
print(parameters)
