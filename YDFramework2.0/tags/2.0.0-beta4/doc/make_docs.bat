@echo off

rem Generate the API documentation
rmdir /Q /S api
mkdir api
doxygen docs_api.dxy
