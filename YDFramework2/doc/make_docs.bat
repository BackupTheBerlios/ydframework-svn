@echo off

rem Generate the API documentation
rmdir /Q /S api
mkdir api
doxygen docs_api.dxy

rem Generate the changelog files
rem cd ..\..
rem echo Creating changelog files...

rem for /f "" %%f in ( 'svnlook youngest C:/_SVNRepos/YDF2' ) do ( set REV=%%f )
rem svn log -v -r 1:%REV% > YDFramework2\doc\changelog.txt
rem svn log -v --xml -r 1:%REV% > YDFramework2\doc\changelog.xml

rem cd YDFramework2\doc
