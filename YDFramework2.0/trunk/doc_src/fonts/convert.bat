@echo off

set CLASSPATH=%CLASSPATH%;C:\bin\fop\build\fop.jar;C:\bin\fop\lib\xercesImpl.jar;C:\bin\fop\lib\xalan.jar

java org.apache.fop.fonts.apps.TTFReader -enc ansi LSANS.TTF lucida_sans.xml
java org.apache.fop.fonts.apps.TTFReader -enc ansi LSANSD.TTF lucida_sans-bold.xml
java org.apache.fop.fonts.apps.TTFReader -enc ansi LTYPE.TTF lucida_typewriter.xml
java org.apache.fop.fonts.apps.TTFReader -enc ansi LTYPEB.TTF lucida_typewriter-bold.xml
