@echo off

rem Generate the API documentation
rmdir /Q /S api
mkdir api
doxygen docs_api.dxy

rem Generate the changelog files
cd ..\..
echo Creating changelog files...

for /f "" %%f in ( 'svnlook youngest C:/_SVNRepos/YDF2' ) do ( set REV=%%f )
svn log -v -r 1:%REV% > YDFramework2\doc\changelog.txt
svn log -v --xml -r 1:%REV% > YDFramework2\doc\changelog.xml

cd YDFramework2\doc
