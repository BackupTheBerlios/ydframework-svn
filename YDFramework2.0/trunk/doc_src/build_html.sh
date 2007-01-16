#!/bin/bash

# Yellow Duck Framework version 2.1
# Copyright (c) copyright 2002-2007 Pieter Claerhout
# rem
# This library is free software; you can redistribute it and/or
# modify it under the terms of the GNU Lesser General Public
# License as published by the Free Software Foundation; either
# version 2.1 of the License, or (at your option) any later version.
# rem
# This library is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
# Lesser General Public License for more details.
# rem
# You should have received a copy of the GNU Lesser General Public
# License along with this library; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA

# Print some general info
echo Yellow Duck Framework 2.1
echo Building HTML version of the User Guide
echo

# Delete the previous directories
rm -Rf html

# Create the directories
mkdir html

# Copy the images
cp images/*.gif html
cp docbook/images/note.gif html
cp docbook/images/warning.gif html

# Copy the stylesheet
cp style.css html

# Create outputs
xsltproc --xinclude --output html/index.html xsl_html.xsl _ydframework.xml

# Open the result
open -a Safari html/index.html
