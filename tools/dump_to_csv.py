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
import csv
import sys

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

def prepare_post(post):
	return {'test 1': '"ololo" 1', 'test 2': 'ololo\n2'}

def prepare_posts(posts):
	return map(prepare_post, posts)

def write_csv_to_writer(writer, posts):
	field_names = ['test 1', 'test 2']
	writer = csv.DictWriter( \
		writer, \
		fieldnames = field_names, \
		lineterminator = '\n')

	writer.writeheader()
	for post in posts:
		writer.writerow(post)

def write_csv_to_file(csv_filename, posts):
	with open(csv_filename, 'w') as csv_file:
		write_csv_to_writer(csv_file, posts)

def write_csv_to_stdout(posts):
	write_csv_to_writer(sys.stdout, posts)

def write_csv(target, posts):
	if target != "-":
		write_csv_to_file(target, posts)
	else:
		write_csv_to_stdout(posts)

parameters = parse_parameters()
dom = read_xml(parameters['<dump-file>'])
posts = find_posts(dom.documentElement)
prepared_posts = prepare_posts(posts)
write_csv(parameters['<csv-file>'], prepared_posts)
