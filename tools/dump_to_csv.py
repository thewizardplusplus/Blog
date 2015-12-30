#!/usr/bin/env python

"""
Usage:
  {0:s} -h | --help
  {0:s} <dump-file> [<csv-file>]

Options:
  -h, --help  - show help.
"""

import re
import docopt
import os.path
import xml.dom.minidom
import base64
import slugify
import csv
import sys

cut_tag_pattern = re.compile('<cut\s*\/>')

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

def get_subnode(node, subnode_tag):
	return node.getElementsByTagName(subnode_tag)[0]

def decode_text(text):
	data = base64.b64decode(text)
	data = data.decode('string_escape')
	return data.strip()

def get_node_text(nodes):
	text = ''
	for node in nodes.childNodes:
		if node.nodeType == node.TEXT_NODE:
			text += decode_text(node.data)

	return text

def extract_excerpt(text):
	excerpt = cut_tag_pattern.split(text)[0]
	return excerpt.strip()

def extract_content(text):
	return cut_tag_pattern.sub('', text)

def get_post_tags(post):
	tags = get_node_text(get_subnode(post, 'tags'))
	return '|'.join(map(lambda tag: tag.strip(), tags.split(',')))

def get_attribute(node, attribute_name):
	return node.attributes[attribute_name].value

def prepare_post(post):
	title = get_node_text(get_subnode(post, 'title'))
	text = get_node_text(get_subnode(post, 'text'))
	return { \
		'Title': title, \
		'Slug': slugify.slugify(title), \
		'Excerpt': extract_excerpt(text), \
		'Content': extract_content(text), \
		'Tags': get_post_tags(post), \
		'Created date': get_attribute(post, 'create-time'), \
		'Updated date': get_attribute(post, 'modify-time')}

def prepare_posts(posts):
	return map(prepare_post, posts)

def write_csv_to_writer(writer, posts):
	field_names = [ \
		'Title', \
		'Slug', \
		'Excerpt', \
		'Content', \
		'Tags', \
		'Created date', \
		'Updated date']
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
