@echo off

rem Needs the following software:
rem   - Doxygen
rem   - xsltproc
rem   - fop
rem   - cscript
rem   - tar
rem   - gzip
rem   - sed

:config_settings
set SVN_HOME=svn://svn.berlios.de/ydframework/YDFramework2.0
set BLDTYPE=
set SVN_URL=
set SVN_REV=
set SVN_CMD=
set ARC_NAME=YDFramework-

:check_environment_svn_url
if "%2" == "" goto error_missing_svn_url

:check_environment_build_type
if "%1" == "" goto error_missing_build_type
if "%1" == "--release"  goto setup_buildtype_release
if "%1" == "--snapshot" goto setup_buildtype_snapshot
if "%BLDTYPE%" == "" goto error_wrong_build_type
goto do_export

:build_export
if exist "%ARC_NAME%" ( rmdir /Q /S "%ARC_NAME%" )
echo [task] Exporting SVN repository...
%SVN_CMD%

:build_docs
pushd "%ARC_NAME%"

:build_docs_api
echo [task] Creating API documentation...
cd "doc"
if exist "api" ( rmdir /Q /S api )
mkdir "api"
doxygen "docs_api.dxy"

:builds_docs_userguide
cd "..\doc_src"

:builds_docs_userguide_html
echo [task] Creating HTML version of the user guide...
if exist "html" ( rmdir /Q /S "html" )
mkdir "html"
copy "images\*.gif" "html" > NUL
copy "docbook\images\note.gif" "html" > NUL
copy "docbook\images\warning.gif" "html" > NUL
copy "style.css" "html" > NUL
xsltproc --xinclude --output "html\index.html" "xsl_html.xsl" "_ydframework.xml"

:builds_docs_userguide_pdf
echo [task] Creating PDF version of the user guide...
if exist "pdf" ( rmdir /Q /S "pdf" )
mkdir "pdf"
copy "images\*.gif" "pdf" > NUL
copy "docbook\images\note.gif" "pdf" > NUL
copy "docbook\images\warning.gif" "pdf" > NUL
echo Creating XSL-FO version of the user guide...
xsltproc --xinclude --output "pdf\YDFramework2.fo" "xsl_fo.xsl" "_ydframework.xml"
echo Fixing the font sizes in the XSL-FO file...
cscript //nologo "fixfo.vbs" "pdf\YDFramework2.fo"
echo Creating PDF version of the user guide...
call fop -q -c "fop_config.xml" "pdf\YDFramework2.fo" "pdf\YDFramework2.pdf"
if exist "pdf\YDFramework2.fo" ( "del pdf\YDFramework2.fo" )
del "pdf\*.gif" > NUL

:move_docs
echo [task] Moving documentation to the right location...
cd ..
if exist "doc\userguide" ( rmdir /Q /S "doc\userguide" )
if exist "..\YDFramework2.pdf" ( del /Q "..\YDFramework2.pdf" > NUL )
mkdir "doc\userguide"
move /Y "doc_src\html\*.*" "doc\userguide\"
move /Y "doc_src\pdf\YDFramework2.pdf" "..\YDFramework2.pdf"
rmdir /Q /S "doc_src"

:cleanup_build
echo [task] Cleaning up the tree structure...
del /Q "doc\build_api.bat" > NUL
del /Q "doc\docs_api.dxy" > NUL
del /Q "doc\docs_api_footer.html" > NUL
del /Q "doc\RequestProcessing.vsd" > NUL
del /Q "YDFramework2\images\*.psd" > NUL
del /Q "make_release.bat" > NUL

:create_build_number_file
echo [task] Creating build revision file...
sed "s/'YD_FW_REVISION', 'unknown'/'YD_FW_REVISION', '%SVN_REV%'/g" "YDFramework2\YDF2_init.php" > "YDFramework2\YDF2_init_fixed.php"
del /Q "YDFramework2\YDF2_init.php" > NUL
move /Y "YDFramework2\YDF2_init_fixed.php" "YDFramework2\YDF2_init.php"

:create_weblog_archive
echo [task] Creating archive of the weblog application...
if exist "..\%WARC_NAME%" ( rmdir /Q /S "..\%WARC_NAME%" )
mkdir "..\%WARC_NAME%"
xcopy /E /Y /Q /S "examples\weblog" "..\%WARC_NAME%"
mkdir "..\%WARC_NAME%\include\YDFramework2"
xcopy /E /Y /Q /S "YDFramework2" "..\%WARC_NAME%\include\YDFramework2"
cd "..\%WARC_NAME%"
sed "s/\/\.\.\/\.\.\/\YDFramework2/\/include\/YDFramework2/g" install.php > install_fixed.php
del /Q "install.php" > NUL
move /Y "install_fixed.php" "install.php"
cd "include"
sed "s/\/\.\.\/\.\.\/\.\.\/YDFramework2/\/YDFramework2/g" "YDWeblog_init.php" > "YDWeblog_init_fixed.php"
del /Q "YDWeblog_init.php" > NUL
move /Y "YDWeblog_init_fixed.php" "YDWeblog_init.php"

:create_archive_framework
echo [task] Creating .tar.gz archive of this build...
cd "..\.."
if exist "%ARC_NAME%.tar" ( del /Q "%ARC_NAME%.tar" > NUL )
tar cf "%ARC_NAME%.tar" "%ARC_NAME%"
if exist "%ARC_NAME%.tar.gz" ( del /Q "%ARC_NAME%.tar.gz" > NUL )
gzip -9 "%ARC_NAME%.tar"

:create_archive_weblog
echo [task] Creating .tar.gz archive of this weblog build...
if exist "%WARC_NAME%.tar" ( del /Q "%WARC_NAME%.tar" > NUL )
tar cf "%WARC_NAME%.tar" "%WARC_NAME%"
if exist "%WARC_NAME%.tar.gz" ( del /Q "%WARC_NAME%.tar.gz" > NUL )
gzip -9 "%WARC_NAME%.tar"

:cleanup_build
echo [task] Cleaning up the temporary build files...
if exist "%ARC_NAME%" ( rmdir /Q /S "%ARC_NAME%" )
if exist "%WARC_NAME%" ( rmdir /Q /S "%WARC_NAME%" )

:finish_build
echo [task] Finished the build!
popd
goto end

:setup_buildtype_release
echo [task] Setting up the build...
set BLDTYPE=release
set SVN_URL="%SVN_HOME%/tags/%2"
set ARC_NAME=%ARC_NAME%%2
set WARC_NAME=YDWeblog-%2
echo Build type: %BLDTYPE%
echo SVN URL: %SVN_URL%
set SVN_REV=%2
set SVN_CMD=svn export %SVN_URL% %ARC_NAME%
goto build_export

:setup_buildtype_snapshot
echo [task] Setting up the build...
set BLDTYPE=snapshot
set SVN_URL="%SVN_HOME%/trunk"
set SVN_REV=%2
set ARC_NAME=%ARC_NAME%bld%2
set WARC_NAME=YDWeblog-bld%2
echo Build type: %BLDTYPE%
echo SVN URL: %SVN_URL%
set SVN_CMD=svn export -r "%2" %SVN_URL% "%ARC_NAME%"
goto build_export

:error_missing_build_type
echo ERROR: The build type was not specified.
goto end

:error_wrong_build_type
echo ERROR: The build type "%1" is not supported.
goto end

:error_missing_svn_url
echo ERROR: The SVN path was not given as an argument.
goto end

:end
