@echo off

del /Q YDF_*.*
for /f "usebackq" %%f in ( `dir /b YDF_*` ) do ( rmdir /Q /S %%f )
