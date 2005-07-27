@echo off

rem Yellow Duck Framework version 2.0
rem Copyright (c) copyright 2002-2005 Pieter Claerhout
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
echo Building PDF version of the User Guide
echo.

rem Delete the previous directories
if exist pdf rmdir /Q /S pdf

rem Create the directories
if not exist pdf mkdir pdf

rem Clear the old directories
if exist pdf\YDFramework2.pdf del /Q pdf\YDFramework2.pdf

rem Copy the images
copy images\*.gif pdf > NUL
copy images\*.svg pdf > NUL
copy docbook\images\note.gif pdf > NUL
copy docbook\images\warning.gif pdf > NUL

rem Create outputs
echo Creating YDFramework2.fo
xsltproc --xinclude --output pdf\YDFramework2.fo xsl_fo.xsl _ydframework.xml

rem Fix the FO document
echo Fixing fontsizes
python fixfo.py pdf\YDFramework2.fo
if not "%ERRORLEVEL%" == "0" cscript //nologo fixfo.vbs pdf\YDFramework2.fo

rem Create the PDF
echo Converting YDFramework2.fo to PDF
call fop -q -c fop_config.xml pdf\YDFramework2.fo pdf\YDFramework2.pdf

rem Cleanup
if exist pdf\YDFramework2.fo del pdf\YDFramework2.fo
del pdf\*.gif > NUL
del pdf\*.svg > NUL

rem Open the result
if exist pdf\YDFramework2.pdf start pdf\YDFramework2.pdf

pause