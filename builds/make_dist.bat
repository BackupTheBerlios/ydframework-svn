@echo off

echo Started making of a new distribution archive.

rem Get the latest revision number
for /f "usebackq" %%f in (`svnlook youngest C:/_SVNRepos/YDF2`) do (set BLDREV=%%f)
echo Latest SVN revision: %BLDREV%

rem Create a directory for the build
set BLDDIR=YDFramework_2.0.0.%BLDREV%
echo Build directory: %BLDDIR%

rem Remove the previous build directory and archive
echo Removing previous build directory: %BLDDIR%
rmdir /Q /S "%BLDDIR%" > NUL
echo Removing previous build archive: %BLDDIR%.tar.gz
del /Q /S "%BLDDIR%.tar.gz" > NUL

rem Checkout the current revision
echo Checking out the revision %BLDREV%
svn export -q "file:///C:/_SVNRepos/YDF2/" "%BLDDIR%"

rem Update the documentation
echo Regenerating documentation
cd "%BLDDIR%\YDFramework2\doc"
call make_docs.bat > NUL
cd "..\..\.."
echo Creating changelog file
svn log -v -r %BLDREV%:2 "file:///C:/_SVNRepos/YDF2/" > "%BLDDIR%\YDFramework2\doc\changelog.txt"
del /Q "%BLDDIR%\YDFramework2\doc\docs_api.dxy"
del /Q "%BLDDIR%\YDFramework2\doc\docs_api_footer.html"
del /Q "%BLDDIR%\YDFramework2\doc\make_docs.bat"
del /Q "%BLDDIR%\YDFramework2\doc\RequestProcessing.vsd"
del /Q "%BLDDIR%\YDFramework2\images\*.psd"
del /Q "%BLDDIR%\YDFramework2\doc\*.gif"
del /Q "%BLDDIR%\YDFramework2\doc\*.doc"
del /Q "%BLDDIR%\YDFramework2\doc\api\doxygen.png"
copy /Y "%BLDDIR%\YDFramework2\doc\changelog.txt" "changelog.txt" > NUL
move "%BLDDIR%\YDFramework2\doc\ydf2_userguide.pdf" "ydf2_userguide.pdf"
move "%BLDDIR%\YDFramework2\doc" "%BLDDIR%\doc" > NUL

rem Compressing the build archive
del /Q /S %BLDDIR%\builds > NUL
echo Compressing the build archive: %BLDDIR%.tar.gz
tar cf "%BLDDIR%.tar" "%BLDDIR%"
gzip -f9q "%BLDDIR%.tar"

rem Remove the rest of the temp files
echo Removing temporary build directory: %BLDDIR%
rmdir /Q /S "%BLDDIR%"

echo Finished making of a new distribution archive.
