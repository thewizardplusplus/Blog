#!/usr/bin/env python

import base64
import HTMLParser
import sys
import xml.dom.minidom

def process(node, html_parser):
	for child in node.childNodes:
		process(child, html_parser)

	if node.nodeType == node.TEXT_NODE:
		data = node.data
		data = base64.b64decode(data)
		data = data.decode('string_escape')
		data = data.decode('utf-8')
		data = data.strip()
		data = html_parser.unescape(data)
		if node.parentNode.tagName == 'text':
			data = '\n%s\n\t\t' % data

		node.data = data

dom = xml.dom.minidom.parse(sys.argv[1])

html_parser = HTMLParser.HTMLParser()
process(dom.documentElement, html_parser)

with open(sys.argv[2], 'w') as target_file:
	content = dom.toprettyxml(encoding = 'utf-8')

	lines = content.split('\n')
	# lines = map(lambda line: line.rstrip(), lines)
	# lines = filter(lambda line: line, lines)

	target_file.write('\n'.join(lines))
