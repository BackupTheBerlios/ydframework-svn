@echo off

rem Skip API docs if needed
if "%1" == "skipapi" goto docs

rem Generate the API documentation
rmdir /Q /S api
mkdir api
doxygen docs_api.dxy

rem Generate the actual documentation
:docs

rmdir /Q /S userguide
mkdir userguide
call aurigadoc -dhtml -XML ydf2.xml -OUT userguide
call aurigadoc -html -XML ydf2.xml -OUT userguide\ydf2_userguide.html
copy style.css userguide\style.css
copy userguide\admain.html userguide\index.html
copy *.gif userguide\*.gif

call aurigadoc -pdf  -XML ydf2.xml -OUT ydf2_userguide.pdf
