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
	node.data = data.strip()

def process_node(node):
	for child in node.childNodes:
		process_node(child)

	if node.nodeType == node.TEXT_NODE:
		decode_node(node)

def attributes_to_string(node):
	attributes = ''
	for i in range(0, node.attributes.length):
		attribute = node.attributes.item(i)
		attributes += ' {:s}="{:s}"'.format(attribute.name, attribute.value)

	return attributes

def generate_start_tag(node, prefix):
	attributes = attributes_to_string(node)
	return prefix + '<{:s}{:s}>\n'.format(node.tagName, attributes)

def generate_end_tag(node, prefix):
	return prefix + '</{:s}>\n'.format(node.tagName)

def escape(text, prefix, text_prefix):
	text = text.replace(']]>', ']]]><![CDATA[]>')
	return '<![CDATA[\n{2:s}{0:s}\n{1:s}]]>'.format(text, prefix, text_prefix)

def text_node_to_string(node, prefix):
	if not node.data:
		return ''

	text_prefix = ''
	if node.parentNode.tagName != 'text':
		text_prefix = prefix + '\t'

	return prefix + escape(node.data, prefix, text_prefix) + '\n'

def node_to_string(node, prefix = ''):
	result = ''
	if not prefix:
		result += '<?xml version="1.1" encoding="utf-8" ?>\n'

	if node.nodeType != node.TEXT_NODE:
		result += generate_start_tag(node, prefix)
		for child in node.childNodes:
			result += node_to_string(child, prefix + '\t')
		result += generate_end_tag(node, prefix)
	else:
		result += text_node_to_string(node, prefix)

	return result

def write_xml_to_file(content, filename):
	with open(filename, 'w') as target_file:
		target_file.write(content)

def write_xml_to_stdout(content):
	print(content)

def write_xml(content, target):
	if target != "-":
		write_xml_to_file(content, target)
	else:
		write_xml_to_stdout(content)

parameters = parse_parameters()
dom = read_xml(parameters['<dump-file>'])
process_node(dom.documentElement)
content = node_to_string(dom.documentElement)
write_xml(content, parameters['<decoded-dump-file>'])
