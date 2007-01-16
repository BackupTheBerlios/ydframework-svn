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

:cleanup_build
echo [task] Cleaning up the tree structure...
del /Q "doc\build_api.bat" > NUL
del /Q "doc\docs_api.dxy" > NUL
del /Q "doc\docs_api_footer.html" > NUL
del /Q "doc\RequestProcessing.vsd" > NUL
del /Q "YDFramework2\images\*.psd" > NUL
del /Q "scripts\*.bat" > NUL
rmdir /Q /S "scripts"

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
cd ..\..

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
