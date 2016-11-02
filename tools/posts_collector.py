#!/usr/bin/env python
# -*- coding: utf-8 -*-

"""
Usage:
  {0:s} -h | --help
  {0:s} <source-path> <target-path>

Options:
  -h, --help  - show help.
"""

from __future__ import print_function
import os
import docopt
import re
import xml.dom.minidom
import base64
import sys

def parse_options():
    script_name = os.path.basename(__file__)
    return docopt.docopt(__doc__.format(script_name))

def create_target(path):
    if not os.path.exists(path):
        os.makedirs(path)

def filter_dumps(files):
    dumps = []
    for filename in files:
        _, extension = os.path.splitext(filename)
        if extension != '.xml':
            continue

        dumps.append(filename)

    return dumps

def make_dumps_paths(root, dumps):
    paths = []
    for dump in dumps:
        path = os.path.join(root, dump)
        paths.append(path)

    return paths

def find_dumps(path):
    all_paths = []
    for root, _, files in os.walk(path):
        dumps = filter_dumps(files)
        paths = make_dumps_paths(root, dumps)
        all_paths += paths

    return all_paths

def extract_timestamp(dump):
    match = re.search(r'\d{4}(?:-\d{2}){5}', dump)
    return match.group(0)

def sort_dumps(dumps):
    return sorted(dumps, key=extract_timestamp)

def read_posts_unsafe(dump):
    dom = xml.dom.minidom.parse(dump)
    return dom.getElementsByTagName('post')

def read_posts(dump):
    posts = []
    try:
        posts = read_posts_unsafe(dump)
    except xml.parsers.expat.ExpatError as error:
        sys.stderr.write(
            'Warning: the dump "{:s}" has a invalid markup ({:s}).\n'.format(
                dump,
                error
            )
        )

    return posts

def get_first_subnode(node, tag):
    return node.getElementsByTagName(tag)[0]

def decode_text(text):
    data = base64.b64decode(text)
    data = data.decode('string_escape')
    return data.strip()

def get_node_text(node):
    text = ''
    for child in node.childNodes:
        if \
            child.nodeType == xml.dom.Node.TEXT_NODE \
            or child.nodeType == xml.dom.Node.CDATA_SECTION_NODE \
        :
            text += decode_text(child.data)

    return text

def get_first_subnode_text(node, tag):
    return get_node_text(get_first_subnode(node, tag))

def transform_post(post):
    title = get_first_subnode_text(post, 'title')
    text = get_first_subnode_text(post, 'text')
    return title, text

def transform_posts(posts):
    new_posts = {}
    for post in posts:
        title, text = transform_post(post)
        new_posts[title] = text

    return new_posts

def collect_posts_from_dump(dump):
    posts = read_posts(dump)
    return transform_posts(posts)

def collect_posts(dumps):
    all_posts = {}
    for dump in dumps:
        posts = collect_posts_from_dump(dump)
        all_posts.update(posts)

    return all_posts

def save_post(path, text):
    with open(path, 'w') as post:
        post.write(text)

def make_post_path(target, title):
    filename = '{:s}.md'.format(title)
    return os.path.join(target, filename)

def save_posts(target, posts):
    create_target(target)
    for title, text in posts.iteritems():
        path = make_post_path(target, title)
        save_post(path, text)

if __name__ == '__main__':
    options = parse_options()
    dumps = find_dumps(options['<source-path>'])
    dumps = sort_dumps(dumps)
    posts = collect_posts(dumps)
    save_posts(options['<target-path>'], posts)
