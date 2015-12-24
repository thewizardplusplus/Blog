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
import xml.dom.minidom
import base64
import sys

def parse_options():
	script_name = os.path.basename(__file__)
	return docopt.docopt(__doc__.format(script_name))

def process_options(options):
	if options['<decoded-dump-file>'] == None:
		decoded_dump_file = os.path.basename(options['<dump-file>'])
		options['<decoded-dump-file>'] = decoded_dump_file

def parse_parameters():
	options = parse_options()
	process_options(options)

	return options

def read_xml(filename):
	return xml.dom.minidom.parse(filename)

def decode_node(node):
	data = base64.b64decode(node.data)
	data = data.decode('string_escape')
	data = data.strip()
	if node.parentNode.tagName == 'text':
		data = '\n%s\n\t\t' % data

	node.data = data

def process_node(node):
	for child in node.childNodes:
		process_node(child)

	if node.nodeType == node.TEXT_NODE:
		decode_node(node)

def write_xml_to_file(dom, filename):
	with open(filename, 'w') as target_file:
		dom.writexml(target_file, encoding = 'utf-8')

def write_xml_to_stdout(dom):
	dom.writexml(sys.stdout, encoding = 'utf-8')

def write_xml(dom, target):
	if target != "-":
		write_xml_to_file(dom, target)
	else:
		write_xml_to_stdout(dom)

parameters = parse_parameters()
dom = read_xml(parameters['<dump-file>'])
process_node(dom.documentElement)
write_xml(dom, parameters['<decoded-dump-file>'])
