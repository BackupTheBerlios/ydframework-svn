@echo off

rem Skip API docs if needed
if "%1" == "skipapi" goto docs

rem Generate the API documentation
rmdir /Q /S api
mkdir api
doxygen docs_api.dxy

rem Generate the actual documentation
:docs
call aurigadoc -html -XML ydf2.xml -OUT ydf2.html
call aurigadoc -pdf  -XML ydf2.xml -OUT ydf2.pdf
