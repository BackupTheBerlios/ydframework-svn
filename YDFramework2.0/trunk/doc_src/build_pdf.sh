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
echo Building PDF version of the User Guide
echo

# Delete the previous directories
rm -Rf pdf

# Create the directories
mkdir pdf

# Copy the images
cp images/*.gif pdf
cp images/*.svg pdf
cp docbook/images/note.gif pdf
cp docbook/images/warning.gif pdf

# Create outputs
echo Creating YDFramework2.fo
xsltproc --xinclude --output pdf/YDFramework2.fo xsl_fo.xsl _ydframework.xml

# Fix the FO document
echo Fixing fontsizes
python fixfo.py pdf/YDFramework2.fo

# Create the PDF
echo Converting YDFramework2.fo to PDF
fop -q -c fop_config.xml pdf/YDFramework2.fo pdf/YDFramework2.pdf

# Cleanup
rm -f pdf/YDFramework2.fo
rm -f pdf/*.gif
rm -f pdf/*.svg

# Open the result
open -a Preview pdf/YDFramework2.pdf

pause