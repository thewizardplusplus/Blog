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
import xml.dom.minidom
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

def collect_posts_from_dump(path):
    posts = read_posts(path)
    print(posts)
    return {path: 'test'}

def collect_posts(dumps):
    all_posts = {}
    for dump in dumps:
        posts = collect_posts_from_dump(dump)
        all_posts.update(posts)

    return all_posts

if __name__ == '__main__':
    options = parse_options()
    create_target(options['<target-path>'])
    dumps = find_dumps(options['<source-path>'])
    posts = collect_posts(dumps)
    print(posts)
