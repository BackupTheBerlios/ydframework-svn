@echo off

rem Delete the previous directories
rmdir /Q /S html
rmdir /Q /S pdf

rem Create the directories
mkdir html
mkdir pdf

rem Clear the old directories
del /Q html\*.*
del /Q pdf\YDFramework2.pdf

rem Copy the images
copy images\*.gif html
copy docbook\images\note.gif html
copy docbook\images\warning.gif html
copy images\*.gif pdf
copy docbook\images\note.gif pdf
copy docbook\images\warning.gif pdf

rem Copy the stylesheet
copy style.css html

rem Create outputs
xsltproc --output html\index.html xsl_html.xsl _ydframework.xml
xsltproc --output pdf\YDFramework2.fo xsl_fo.xsl _ydframework.xml

rem Fix the FO document
python fixfo.py pdf\YDFramework2.fo

rem Create the PDF
set PATH=%PATH%;C:\bin\fop
call fop -q pdf\YDFramework2.fo pdf\YDFramework2.pdf

rem Cleanup
del pdf\YDFramework2.fo
del pdf\*.gif
