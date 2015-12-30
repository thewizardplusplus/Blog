#!/usr/bin/env python

"""
Usage:
  {0:s} -h | --help
  {0:s} <dump-file> [<csv-file>]

Options:
  -h, --help  - show help.
"""

import docopt
import os.path
import xml.dom.minidom

def parse_options():
	script_name = os.path.basename(__file__)
	return docopt.docopt(__doc__.format(script_name))

def process_options(options):
	if options['<csv-file>'] == None:
		csv_file = os.path.basename(options['<dump-file>'])
		csv_file = os.path.splitext(csv_file)[0]
		options['<csv-file>'] = csv_file + '.csv'

def parse_parameters():
	options = parse_options()
	process_options(options)

	return options

def read_xml(filename):
	return xml.dom.minidom.parse(filename)

def find_posts(node):
	return node.getElementsByTagName('post')

parameters = parse_parameters()
dom = read_xml(parameters['<dump-file>'])
posts = find_posts(dom.documentElement)
print(posts)
