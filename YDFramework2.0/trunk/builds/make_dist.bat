@echo off

echo Started making of a new distribution archive.

rem Get the latest revision number
for /f "usebackq" %%f in (`svnlook youngest C:/_SVNRepos/YDF2/`) do (set BLDREV=%%f)
echo Latest SVN revision: %BLDREV%

rem Create a directory for the build
set BLDDIR=YDFramework-2.0-bld%BLDREV%
echo Build directory: %BLDDIR%

rem Remove the previous build directory and archive
echo Removing previous build directory: %BLDDIR%
rmdir /Q /S "%BLDDIR%" > NUL
echo Removing previous build archive: %BLDDIR%.tar.gz
del /Q /S "%BLDDIR%.tar.gz" > NUL

rem Checkout the current revision
echo Checking out the revision %BLDREV%
svn export -q "file:///C:/_SVNRepos/YDF2/YDFramework2.0/trunk" "%BLDDIR%"

rem Update the documentation
echo Regenerating documentation
cd "%BLDDIR%\doc"
call make_docs.bat > NUL
cd "..\.."
echo Creating changelog file
svn log -v -r %BLDREV%:2 "file:///C:/_SVNRepos/YDF2" > "changelog.txt"
del /Q "%BLDDIR%\doc\docs_api.dxy"
del /Q "%BLDDIR%\doc\docs_api_footer.html"
del /Q "%BLDDIR%\doc\make_docs.bat"
del /Q "%BLDDIR%\doc\RequestProcessing.vsd"
del /Q "%BLDDIR%\YDFramework2\images\*.psd"
del /Q "%BLDDIR%\doc\api\doxygen.png"
del /Q "%BLDDIR%\doc\*.py"

rem Compressing the build archive
del /Q /S %BLDDIR%\builds > NUL
echo Compressing the build archive: %BLDDIR%.tar.gz
tar cf "%BLDDIR%.tar" "%BLDDIR%"
gzip -f9q "%BLDDIR%.tar"

rem Remove the rest of the temp files
echo Removing temporary build directory: %BLDDIR%
rmdir /Q /S "%BLDDIR%"

echo Finished making of a new distribution archive.
