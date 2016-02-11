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

def parse_options():
    script_name = os.path.basename(__file__)
    return docopt.docopt(__doc__.format(script_name))

if __name__ == '__main__':
    options = parse_options()
    print(options)
