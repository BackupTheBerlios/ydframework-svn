@echo off

del /Q YDF_*.*
del /Q *.php
for /f "usebackq" %%f in ( `dir /b YDF_*` ) do ( rmdir /Q /S %%f )
