@echo off

echo Started making of a new distribution archive.

rem Get the latest revision number
for /f "usebackq" %%f in (`svnlook youngest C:/_SVNRepos/YDF2`) do (set BLDREV=%%f)
echo Latest SVN revision: %BLDREV%

rem Create a directory for the build
set BLDDIR=YDFramework2-bld%BLDREV%
echo Build directory: %BLDDIR%

rem Remove the previous build directory and archive
echo Removing previous build directory: %BLDDIR%
rmdir /Q /S %BLDDIR% > NUL
echo Removing previous build archive: %BLDDIR%.zip
del /Q /S %BLDDIR%.zip > NUL

rem Checkout the current revision
echo Checking out the revision %BLDREV%
svn export -q "file:///C:/_SVNRepos/YDF2/" "%BLDDIR%"

rem Update the documentation
echo Regenerating documentation
cd "%BLDDIR%\YDFramework2\doc"
call make_docs.bat > NUL
cd "..\..\.."
echo Creating changelog files
svn log -v --xml -r %BLDREV%:1 "file:///C:/_SVNRepos/YDF2/" > "%BLDDIR%\YDFramework2\doc\changelog.xml"
call xsltproc --output "%BLDDIR%\YDFramework2\doc\changelog.html" "%BLDDIR%\YDFramework2\doc\svnlog2html.xsl" "%BLDDIR%\YDFramework2\doc\changelog.xml"
del /Q "%BLDDIR%\YDFramework2\doc\docs_api.dxy"
del /Q "%BLDDIR%\YDFramework2\doc\docs_api_footer.html"
del /Q "%BLDDIR%\YDFramework2\doc\make_docs.bat"
del /Q "%BLDDIR%\YDFramework2\doc\RequestProcessing.vsd"
del /Q "%BLDDIR%\YDFramework2\doc\svnlog2html.xsl"
del /Q "%BLDDIR%\YDFramework2\doc\changelog.xml"
xcopy /Q /Y "%BLDDIR%\YDFramework2\doc\changelog.html" "%BLDDIR%.changes.html"

rem Zip up the build to an archive
del /Q /S %BLDDIR%\builds
echo Zipping up the build: %BLDDIR%.zip
zip -9qr "%BLDDIR%.zip" "%BLDDIR%"

rem Remove the rest of the temp files
echo Removing temporary build directory: %BLDDIR%
rmdir /Q /S %BLDDIR%

echo Finished making of a new distribution archive.
