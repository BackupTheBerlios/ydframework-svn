@echo off

del /Q YDF_*.*
del /Q *.php
del /Q YDFramework2_log.xml
for /f "usebackq" %%f in ( `dir /b YDF_*` ) do ( rmdir /Q /S %%f )
rmdir /Q /S cache
