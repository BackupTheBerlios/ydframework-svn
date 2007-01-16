@echo off

rem Yellow Duck Framework version 2.1
rem Copyright (c) copyright 2002-2007 Pieter Claerhout
rem
rem This library is free software; you can redistribute it and/or
rem modify it under the terms of the GNU Lesser General Public
rem License as published by the Free Software Foundation; either
rem version 2.1 of the License, or (at your option) any later version.
rem
rem This library is distributed in the hope that it will be useful,
rem but WITHOUT ANY WARRANTY; without even the implied warranty of
rem MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
rem Lesser General Public License for more details.
rem
rem You should have received a copy of the GNU Lesser General Public
rem License along with this library; if not, write to the Free Software
rem Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA

rem Print some general info
echo Yellow Duck Framework 2.0
echo Building HTML version of the User Guide
echo.

rem Delete the previous directories
if exist html rmdir /Q /S html

rem Create the directories
if not exist html mkdir html

rem Clear the old directories
del /Q html\*.* > NUL

rem Copy the images
copy images\*.gif html > NUL
copy docbook\images\note.gif html > NUL
copy docbook\images\warning.gif html > NUL

rem Copy the stylesheet
copy style.css html > NUL

rem Create outputs
xsltproc --xinclude --output html\index.html xsl_html.xsl _ydframework.xml

rem Open the result
if exist html\index.html start html\index.html

pause
