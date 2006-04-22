<?xml version="1.0" standalone="yes" ?>
<DBMODEL Version="4.0">
<SETTINGS>
<GLOBALSETTINGS ModelName="YDCM scheme" IDModel="0" IDVersion="0" VersionStr="1.0.0.0" Comments="" UseVersionHistroy="1" AutoIncVersion="1" DatabaseType="MySQL" ZoomFac="73.00" XPos="25" YPos="329" DefaultDataType="5" DefaultTablePrefix="0" DefSaveDBConn="" DefSyncDBConn="" DefQueryDBConn="" Printer="" HPageCount="4.0" PageAspectRatio="1.440892512336408" PageOrientation="1" PageFormat="A4 (210x297 mm, 8.26x11.7 inches)" SelectedPages="" UsePositionGrid="1" PositionGridX="20" PositionGridY="20" TableNameInRefs="0" DefaultTableType="0" ActivateRefDefForNewRelations="1" FKPrefix="" FKPostfix="" CreateFKRefDefIndex="0" DBQuoteCharacter="`" CreateSQLforLinkedObjects="0" DefModelFont="Tahoma" CanvasWidth="4096" CanvasHeight="2842" />
<DATATYPEGROUPS>
<DATATYPEGROUP Name="Numeric Types" Icon="1" />
<DATATYPEGROUP Name="Date and Time Types" Icon="2" />
<DATATYPEGROUP Name="String Types" Icon="3" />
<DATATYPEGROUP Name="Blob and Text Types" Icon="4" />
<DATATYPEGROUP Name="User defined Types" Icon="5" />
<DATATYPEGROUP Name="Geographic Types" Icon="6" />
</DATATYPEGROUPS>
<DATATYPES>
<DATATYPE ID="1" IDGroup="0" TypeName="TINYINT" Description="A very small integer. The signed range is -128 to 127. The unsigned range is 0 to 255." ParamCount="1" OptionCount="2" ParamRequired="0" EditParamsAsString="0" SynonymGroup="0" PhysicalMapping="0" PhysicalTypeName="" >
<PARAMS>
<PARAM Name="length" />
</PARAMS>
<OPTIONS>
<OPTION Name="UNSIGNED" Default="1" />
<OPTION Name="ZEROFILL" Default="0" />
</OPTIONS>
</DATATYPE>
<DATATYPE ID="2" IDGroup="0" TypeName="SMALLINT" Description="A small integer. The signed range is -32768 to 32767. The unsigned range is 0 to 65535." ParamCount="1" OptionCount="2" ParamRequired="0" EditParamsAsString="0" SynonymGroup="0" PhysicalMapping="0" PhysicalTypeName="" >
<PARAMS>
<PARAM Name="length" />
</PARAMS>
<OPTIONS>
<OPTION Name="UNSIGNED" Default="1" />
<OPTION Name="ZEROFILL" Default="0" />
</OPTIONS>
</DATATYPE>
<DATATYPE ID="3" IDGroup="0" TypeName="MEDIUMINT" Description="A medium-size integer. The signed range is -8388608 to 8388607. The unsigned range is 0 to 16777215." ParamCount="1" OptionCount="2" ParamRequired="0" EditParamsAsString="0" SynonymGroup="0" PhysicalMapping="0" PhysicalTypeName="" >
<PARAMS>
<PARAM Name="length" />
</PARAMS>
<OPTIONS>
<OPTION Name="UNSIGNED" Default="1" />
<OPTION Name="ZEROFILL" Default="0" />
</OPTIONS>
</DATATYPE>
<DATATYPE ID="4" IDGroup="0" TypeName="INT" Description="A normal-size integer. The signed range is -2147483648 to 2147483647. The unsigned range is 0 to 4294967295." ParamCount="1" OptionCount="2" ParamRequired="0" EditParamsAsString="0" SynonymGroup="1" PhysicalMapping="0" PhysicalTypeName="" >
<PARAMS>
<PARAM Name="length" />
</PARAMS>
<OPTIONS>
<OPTION Name="UNSIGNED" Default="0" />
<OPTION Name="ZEROFILL" Default="0" />
</OPTIONS>
</DATATYPE>
<DATATYPE ID="5" IDGroup="0" TypeName="INTEGER" Description="A normal-size integer. The signed range is -2147483648 to 2147483647. The unsigned range is 0 to 4294967295." ParamCount="1" OptionCount="2" ParamRequired="0" EditParamsAsString="0" SynonymGroup="1" PhysicalMapping="0" PhysicalTypeName="" >
<PARAMS>
<PARAM Name="length" />
</PARAMS>
<OPTIONS>
<OPTION Name="UNSIGNED" Default="1" />
<OPTION Name="ZEROFILL" Default="0" />
</OPTIONS>
</DATATYPE>
<DATATYPE ID="6" IDGroup="0" TypeName="BIGINT" Description="A large integer. The signed range is -9223372036854775808 to 9223372036854775807. The unsigned range is 0 to 18446744073709551615." ParamCount="1" OptionCount="2" ParamRequired="0" EditParamsAsString="0" SynonymGroup="0" PhysicalMapping="0" PhysicalTypeName="" >
<PARAMS>
<PARAM Name="length" />
</PARAMS>
<OPTIONS>
<OPTION Name="UNSIGNED" Default="0" />
<OPTION Name="ZEROFILL" Default="0" />
</OPTIONS>
</DATATYPE>
<DATATYPE ID="7" IDGroup="0" TypeName="FLOAT" Description="A small (single-precision) floating-point number. Cannot be unsigned. Allowable values are -3.402823466E+38 to -1.175494351E-38, 0, and 1.175494351E-38 to 3.402823466E+38." ParamCount="1" OptionCount="1" ParamRequired="1" EditParamsAsString="0" SynonymGroup="0" PhysicalMapping="0" PhysicalTypeName="" >
<PARAMS>
<PARAM Name="precision" />
</PARAMS>
<OPTIONS>
<OPTION Name="ZEROFILL" Default="0" />
</OPTIONS>
</DATATYPE>
<DATATYPE ID="8" IDGroup="0" TypeName="FLOAT" Description="A small (single-precision) floating-point number. Cannot be unsigned. Allowable values are -3.402823466E+38 to -1.175494351E-38, 0, and 1.175494351E-38 to 3.402823466E+38." ParamCount="2" OptionCount="1" ParamRequired="0" EditParamsAsString="0" SynonymGroup="0" PhysicalMapping="0" PhysicalTypeName="" >
<PARAMS>
<PARAM Name="length" />
<PARAM Name="decimals" />
</PARAMS>
<OPTIONS>
<OPTION Name="ZEROFILL" Default="0" />
</OPTIONS>
</DATATYPE>
<DATATYPE ID="9" IDGroup="0" TypeName="DOUBLE" Description="A normal-size (double-precision) floating-point number. Cannot be unsigned. Allowable values are -1.7976931348623157E+308 to -2.2250738585072014E-308, 0, and 2.2250738585072014E-308 to 1.7976931348623157E+308." ParamCount="2" OptionCount="1" ParamRequired="0" EditParamsAsString="0" SynonymGroup="2" PhysicalMapping="0" PhysicalTypeName="" >
<PARAMS>
<PARAM Name="length" />
<PARAM Name="decimals" />
</PARAMS>
<OPTIONS>
<OPTION Name="ZEROFILL" Default="0" />
</OPTIONS>
</DATATYPE>
<DATATYPE ID="10" IDGroup="0" TypeName="DOUBLE PRECISION" Description="This is a synonym for DOUBLE." ParamCount="2" OptionCount="1" ParamRequired="0" EditParamsAsString="0" SynonymGroup="2" PhysicalMapping="0" PhysicalTypeName="" >
<PARAMS>
<PARAM Name="length" />
<PARAM Name="decimals" />
</PARAMS>
<OPTIONS>
<OPTION Name="ZEROFILL" Default="0" />
</OPTIONS>
</DATATYPE>
<DATATYPE ID="11" IDGroup="0" TypeName="REAL" Description="This is a synonym for DOUBLE." ParamCount="2" OptionCount="1" ParamRequired="0" EditParamsAsString="0" SynonymGroup="2" PhysicalMapping="0" PhysicalTypeName="" >
<PARAMS>
<PARAM Name="length" />
<PARAM Name="decimals" />
</PARAMS>
<OPTIONS>
<OPTION Name="ZEROFILL" Default="0" />
</OPTIONS>
</DATATYPE>
<DATATYPE ID="12" IDGroup="0" TypeName="DECIMAL" Description="An unpacked floating-point number. Cannot be unsigned. Behaves like a CHAR column." ParamCount="2" OptionCount="1" ParamRequired="0" EditParamsAsString="0" SynonymGroup="3" PhysicalMapping="0" PhysicalTypeName="" >
<PARAMS>
<PARAM Name="length" />
<PARAM Name="decimals" />
</PARAMS>
<OPTIONS>
<OPTION Name="ZEROFILL" Default="0" />
</OPTIONS>
</DATATYPE>
<DATATYPE ID="13" IDGroup="0" TypeName="NUMERIC" Description="This is a synonym for DECIMAL." ParamCount="2" OptionCount="1" ParamRequired="1" EditParamsAsString="0" SynonymGroup="3" PhysicalMapping="0" PhysicalTypeName="" >
<PARAMS>
<PARAM Name="length" />
<PARAM Name="decimals" />
</PARAMS>
<OPTIONS>
<OPTION Name="ZEROFILL" Default="0" />
</OPTIONS>
</DATATYPE>
<DATATYPE ID="14" IDGroup="1" TypeName="DATE" Description="A date. The supported range is \a1000-01-01\a to \a9999-12-31\a." ParamCount="0" OptionCount="0" ParamRequired="0" EditParamsAsString="0" SynonymGroup="0" PhysicalMapping="0" PhysicalTypeName="" >
</DATATYPE>
<DATATYPE ID="15" IDGroup="1" TypeName="DATETIME" Description="A date and time combination. The supported range is \a1000-01-01 00:00:00\a to \a9999-12-31 23:59:59\a." ParamCount="0" OptionCount="0" ParamRequired="0" EditParamsAsString="0" SynonymGroup="0" PhysicalMapping="0" PhysicalTypeName="" >
</DATATYPE>
<DATATYPE ID="16" IDGroup="1" TypeName="TIMESTAMP" Description="A timestamp. The range is \a1970-01-01 00:00:00\a to sometime in the year 2037. The length can be 14 (or missing), 12, 10, 8, 6, 4, or 2 representing YYYYMMDDHHMMSS, ... , YYYYMMDD, ... , YY formats." ParamCount="1" OptionCount="0" ParamRequired="0" EditParamsAsString="0" SynonymGroup="0" PhysicalMapping="0" PhysicalTypeName="" >
<PARAMS>
<PARAM Name="length" />
</PARAMS>
</DATATYPE>
<DATATYPE ID="17" IDGroup="1" TypeName="TIME" Description="A time. The range is \a-838:59:59\a to \a838:59:59\a." ParamCount="0" OptionCount="0" ParamRequired="0" EditParamsAsString="0" SynonymGroup="0" PhysicalMapping="0" PhysicalTypeName="" >
</DATATYPE>
<DATATYPE ID="18" IDGroup="1" TypeName="YEAR" Description="A year in 2- or 4-digit format (default is 4-digit)." ParamCount="1" OptionCount="0" ParamRequired="0" EditParamsAsString="0" SynonymGroup="0" PhysicalMapping="0" PhysicalTypeName="" >
<PARAMS>
<PARAM Name="length" />
</PARAMS>
</DATATYPE>
<DATATYPE ID="19" IDGroup="2" TypeName="CHAR" Description="A fixed-length string (1 to 255 characters) that is always right-padded with spaces to the specified length when stored. values are sorted and compared in case-insensitive fashion according to the default character set unless the BINARY keyword is given." ParamCount="1" OptionCount="1" ParamRequired="1" EditParamsAsString="0" SynonymGroup="0" PhysicalMapping="0" PhysicalTypeName="" >
<PARAMS>
<PARAM Name="length" />
</PARAMS>
<OPTIONS>
<OPTION Name="BINARY" Default="0" />
</OPTIONS>
</DATATYPE>
<DATATYPE ID="20" IDGroup="2" TypeName="VARCHAR" Description="A variable-length string (1 to 255 characters). Values are sorted and compared in case-sensitive fashion unless the BINARY keyword is given." ParamCount="1" OptionCount="1" ParamRequired="1" EditParamsAsString="0" SynonymGroup="0" PhysicalMapping="0" PhysicalTypeName="" >
<PARAMS>
<PARAM Name="length" />
</PARAMS>
<OPTIONS>
<OPTION Name="BINARY" Default="0" />
</OPTIONS>
</DATATYPE>
<DATATYPE ID="21" IDGroup="2" TypeName="BIT" Description="This is a synonym for CHAR(1)." ParamCount="0" OptionCount="0" ParamRequired="0" EditParamsAsString="0" SynonymGroup="0" PhysicalMapping="0" PhysicalTypeName="" >
</DATATYPE>
<DATATYPE ID="22" IDGroup="2" TypeName="BOOL" Description="This is a synonym for CHAR(1)." ParamCount="0" OptionCount="0" ParamRequired="0" EditParamsAsString="0" SynonymGroup="0" PhysicalMapping="0" PhysicalTypeName="" >
</DATATYPE>
<DATATYPE ID="23" IDGroup="3" TypeName="TINYBLOB" Description="A column maximum length of 255 (2^8 - 1) characters. Values are sorted and compared in case-sensitive fashion." ParamCount="0" OptionCount="0" ParamRequired="0" EditParamsAsString="0" SynonymGroup="0" PhysicalMapping="0" PhysicalTypeName="" >
</DATATYPE>
<DATATYPE ID="24" IDGroup="3" TypeName="BLOB" Description="A column maximum length of 65535 (2^16 - 1) characters. Values are sorted and compared in case-sensitive fashion." ParamCount="0" OptionCount="0" ParamRequired="0" EditParamsAsString="0" SynonymGroup="0" PhysicalMapping="0" PhysicalTypeName="" >
</DATATYPE>
<DATATYPE ID="25" IDGroup="3" TypeName="MEDIUMBLOB" Description="A column maximum length of 16777215 (2^24 - 1) characters. Values are sorted and compared in case-sensitive fashion." ParamCount="0" OptionCount="0" ParamRequired="0" EditParamsAsString="0" SynonymGroup="0" PhysicalMapping="0" PhysicalTypeName="" >
</DATATYPE>
<DATATYPE ID="26" IDGroup="3" TypeName="LONGBLOB" Description="A column maximum length of 4294967295 (2^32 - 1) characters. Values are sorted and compared in case-sensitive fashion." ParamCount="0" OptionCount="0" ParamRequired="0" EditParamsAsString="0" SynonymGroup="0" PhysicalMapping="0" PhysicalTypeName="" >
</DATATYPE>
<DATATYPE ID="27" IDGroup="3" TypeName="TINYTEXT" Description="A column maximum length of 255 (2^8 - 1) characters." ParamCount="0" OptionCount="0" ParamRequired="0" EditParamsAsString="0" SynonymGroup="0" PhysicalMapping="0" PhysicalTypeName="" >
</DATATYPE>
<DATATYPE ID="28" IDGroup="3" TypeName="TEXT" Description="A column maximum length of 65535 (2^16 - 1) characters." ParamCount="0" OptionCount="0" ParamRequired="0" EditParamsAsString="0" SynonymGroup="0" PhysicalMapping="0" PhysicalTypeName="" >
</DATATYPE>
<DATATYPE ID="29" IDGroup="3" TypeName="MEDIUMTEXT" Description="A column maximum length of 16777215 (2^24 - 1) characters." ParamCount="0" OptionCount="0" ParamRequired="0" EditParamsAsString="0" SynonymGroup="0" PhysicalMapping="0" PhysicalTypeName="" >
</DATATYPE>
<DATATYPE ID="30" IDGroup="3" TypeName="LONGTEXT" Description="A column maximum length of 4294967295 (2^32 - 1) characters." ParamCount="0" OptionCount="0" ParamRequired="0" EditParamsAsString="0" SynonymGroup="0" PhysicalMapping="0" PhysicalTypeName="" >
</DATATYPE>
<DATATYPE ID="31" IDGroup="3" TypeName="ENUM" Description="An enumeration. A string object that can have only one value, chosen from the list of values." ParamCount="1" OptionCount="0" ParamRequired="1" EditParamsAsString="1" SynonymGroup="0" PhysicalMapping="0" PhysicalTypeName="" >
<PARAMS>
<PARAM Name="values" />
</PARAMS>
</DATATYPE>
<DATATYPE ID="32" IDGroup="3" TypeName="SET" Description="A set. A string object that can have zero or more values, each of which must be chosen from the list of values." ParamCount="1" OptionCount="0" ParamRequired="1" EditParamsAsString="1" SynonymGroup="0" PhysicalMapping="0" PhysicalTypeName="" >
<PARAMS>
<PARAM Name="values" />
</PARAMS>
</DATATYPE>
<DATATYPE ID="33" IDGroup="4" TypeName="Varchar(20)" Description="" ParamCount="0" OptionCount="1" ParamRequired="0" EditParamsAsString="0" SynonymGroup="0" PhysicalMapping="0" PhysicalTypeName="" >
<OPTIONS>
<OPTION Name="BINARY" Default="0" />
</OPTIONS>
</DATATYPE>
<DATATYPE ID="34" IDGroup="4" TypeName="Varchar(45)" Description="" ParamCount="0" OptionCount="1" ParamRequired="0" EditParamsAsString="0" SynonymGroup="0" PhysicalMapping="0" PhysicalTypeName="" >
<OPTIONS>
<OPTION Name="BINARY" Default="0" />
</OPTIONS>
</DATATYPE>
<DATATYPE ID="35" IDGroup="4" TypeName="Varchar(255)" Description="" ParamCount="0" OptionCount="1" ParamRequired="0" EditParamsAsString="0" SynonymGroup="0" PhysicalMapping="0" PhysicalTypeName="" >
<OPTIONS>
<OPTION Name="BINARY" Default="0" />
</OPTIONS>
</DATATYPE>
<DATATYPE ID="36" IDGroup="5" TypeName="GEOMETRY" Description="Geographic Datatype" ParamCount="0" OptionCount="0" ParamRequired="0" EditParamsAsString="0" SynonymGroup="0" PhysicalMapping="0" PhysicalTypeName="" >
</DATATYPE>
<DATATYPE ID="38" IDGroup="5" TypeName="LINESTRING" Description="Geographic Datatype" ParamCount="0" OptionCount="0" ParamRequired="0" EditParamsAsString="0" SynonymGroup="0" PhysicalMapping="0" PhysicalTypeName="" >
</DATATYPE>
<DATATYPE ID="39" IDGroup="5" TypeName="POLYGON" Description="Geographic Datatype" ParamCount="0" OptionCount="0" ParamRequired="0" EditParamsAsString="0" SynonymGroup="0" PhysicalMapping="0" PhysicalTypeName="" >
</DATATYPE>
<DATATYPE ID="40" IDGroup="5" TypeName="MULTIPOINT" Description="Geographic Datatype" ParamCount="0" OptionCount="0" ParamRequired="0" EditParamsAsString="0" SynonymGroup="0" PhysicalMapping="0" PhysicalTypeName="" >
</DATATYPE>
<DATATYPE ID="41" IDGroup="5" TypeName="MULTILINESTRING" Description="Geographic Datatype" ParamCount="0" OptionCount="0" ParamRequired="0" EditParamsAsString="0" SynonymGroup="0" PhysicalMapping="0" PhysicalTypeName="" >
</DATATYPE>
<DATATYPE ID="42" IDGroup="5" TypeName="MULTIPOLYGON" Description="Geographic Datatype" ParamCount="0" OptionCount="0" ParamRequired="0" EditParamsAsString="0" SynonymGroup="0" PhysicalMapping="0" PhysicalTypeName="" >
</DATATYPE>
<DATATYPE ID="43" IDGroup="5" TypeName="GEOMETRYCOLLECTION" Description="Geographic Datatype" ParamCount="0" OptionCount="0" ParamRequired="0" EditParamsAsString="0" SynonymGroup="0" PhysicalMapping="0" PhysicalTypeName="" >
</DATATYPE>
</DATATYPES>
<COMMON_DATATYPES>
<COMMON_DATATYPE ID="5" />
<COMMON_DATATYPE ID="8" />
<COMMON_DATATYPE ID="20" />
<COMMON_DATATYPE ID="15" />
<COMMON_DATATYPE ID="22" />
<COMMON_DATATYPE ID="28" />
<COMMON_DATATYPE ID="26" />
<COMMON_DATATYPE ID="33" />
<COMMON_DATATYPE ID="34" />
<COMMON_DATATYPE ID="35" />
</COMMON_DATATYPES>
<TABLEPREFIXES>
<TABLEPREFIX Name="Default (no prefix)" />
</TABLEPREFIXES>
<REGIONCOLORS>
<REGIONCOLOR Color="Red=#FFEEEC" />
<REGIONCOLOR Color="Yellow=#FEFDED" />
<REGIONCOLOR Color="Green=#EAFFE5" />
<REGIONCOLOR Color="Cyan=#ECFDFF" />
<REGIONCOLOR Color="Blue=#F0F1FE" />
<REGIONCOLOR Color="Magenta=#FFEBFA" />
</REGIONCOLORS>
<POSITIONMARKERS>
<POSITIONMARKER ZoomFac="-1.0" X="0" Y="0" />
<POSITIONMARKER ZoomFac="-1.0" X="0" Y="0" />
<POSITIONMARKER ZoomFac="-1.0" X="0" Y="0" />
<POSITIONMARKER ZoomFac="-1.0" X="0" Y="0" />
<POSITIONMARKER ZoomFac="-1.0" X="0" Y="0" />
<POSITIONMARKER ZoomFac="-1.0" X="0" Y="0" />
<POSITIONMARKER ZoomFac="-1.0" X="0" Y="0" />
<POSITIONMARKER ZoomFac="-1.0" X="0" Y="0" />
<POSITIONMARKER ZoomFac="-1.0" X="0" Y="0" />
<POSITIONMARKER ZoomFac="-1.0" X="0" Y="0" />
<POSITIONMARKER ZoomFac="-1.0" X="0" Y="0" />
</POSITIONMARKERS>
</SETTINGS>
<METADATA>
<REGIONS>
</REGIONS>
<TABLES>
<TABLE ID="1158" Tablename="YDCMAudit" PrevTableName="admin_log" XPos="700" YPos="260" TableType="0" TablePrefix="0" nmTable="0" Temporary="0" UseStandardInserts="0" StandardInserts="\n" TableOptions="DelayKeyTblUpdates=0\nPackKeys=0\nRowChecksum=0\nRowFormat=0\nUseRaid=0\nRaidType=0\n" Comments="" Collapsed="0" IsLinkedObject="0" IDLinkedModel="-1" Obj_id_Linked="-1" OrderPos="2" >
<COLUMNS>
<COLUMN ID="1169" ColName="YDCMAudit_id" PrevColName="id" Pos="1" idDatatype="5" DatatypeParams="" Width="0" Prec="0" PrimaryKey="1" NotNull="1" AutoInc="1" IsForeignKey="0" DefaultValue="" Comments="autoincrement action">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1171" ColName="user_id" PrevColName="username" Pos="3" idDatatype="5" DatatypeParams="(10)" Width="0" Prec="0" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="1" DefaultValue="" Comments="user id">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1170" ColName="date" PrevColName="timestamp" Pos="2" idDatatype="15" DatatypeParams="" Width="0" Prec="0" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="date of this action">
<OPTIONSELECTED>
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1172" ColName="object" PrevColName="action" Pos="4" idDatatype="20" DatatypeParams="(255)" Width="0" Prec="0" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="action object">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1174" ColName="object_action" PrevColName="itemname" Pos="6" idDatatype="20" DatatypeParams="(255)" Width="0" Prec="0" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="action name of the above object">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1173" ColName="object_id" PrevColName="itemid" Pos="5" idDatatype="5" DatatypeParams="" Width="0" Prec="0" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="action parameter">
<OPTIONSELECTED>
<OPTIONSELECT Value="1" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
</COLUMNS>
<RELATIONS_END>
<RELATION_END ID="1333" />
</RELATIONS_END>
<INDICES>
<INDEX ID="1176" IndexName="PRIMARY" IndexKind="0" FKRefDef_Obj_id="-1">
<INDEXCOLUMNS>
<INDEXCOLUMN idColumn="1169" LengthParam="0" />
</INDEXCOLUMNS>
</INDEX>
</INDICES>
</TABLE>
<TABLE ID="1160" Tablename="YDCMPage" PrevTableName="component_page" XPos="360" YPos="800" TableType="0" TablePrefix="0" nmTable="0" Temporary="0" UseStandardInserts="0" StandardInserts="\n" TableOptions="DelayKeyTblUpdates=0\nPackKeys=0\nRowChecksum=0\nRowFormat=0\nUseRaid=0\nRaidType=0\n" Comments="" Collapsed="0" IsLinkedObject="0" IDLinkedModel="-1" Obj_id_Linked="-1" OrderPos="4" >
<COLUMNS>
<COLUMN ID="1315" ColName="component_id" PrevColName="" Pos="10" idDatatype="5" DatatypeParams="" Width="-1" Prec="-1" PrimaryKey="1" NotNull="1" AutoInc="1" IsForeignKey="0" DefaultValue="" Comments="page id">
<OPTIONSELECTED>
<OPTIONSELECT Value="1" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1424" ColName="content_id" PrevColName="" Pos="1" idDatatype="5" DatatypeParams="(10)" Width="0" Prec="0" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="1" DefaultValue="" Comments="component code">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1187" ColName="language_id" PrevColName="language" Pos="8" idDatatype="20" DatatypeParams="(255)" Width="0" Prec="0" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="1" DefaultValue="" Comments="language code">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1297" ColName="current_version" PrevColName="version" Pos="9" idDatatype="1" DatatypeParams="(1)" Width="-1" Prec="-1" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="this is the current version of this page">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1293" ColName="title" PrevColName="" Pos="8" idDatatype="20" DatatypeParams="(255)" Width="-1" Prec="-1" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="page title">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1181" ColName="html" PrevColName="content" Pos="2" idDatatype="28" DatatypeParams="" Width="0" Prec="0" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="page html">
<OPTIONSELECTED>
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1440" ColName="xhtml" PrevColName="" Pos="11" idDatatype="28" DatatypeParams="" Width="-1" Prec="-1" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="optional page xhtml">
<OPTIONSELECTED>
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1182" ColName="template_pack" PrevColName="" Pos="3" idDatatype="1" DatatypeParams="(1)" Width="0" Prec="0" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="template pack used">
<OPTIONSELECTED>
<OPTIONSELECT Value="1" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1183" ColName="template" PrevColName="" Pos="4" idDatatype="20" DatatypeParams="(255)" Width="0" Prec="0" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="template name">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1184" ColName="metatags" PrevColName="" Pos="5" idDatatype="1" DatatypeParams="(1)" Width="0" Prec="0" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="metatags are used. 1 true, 0 false">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1185" ColName="description" PrevColName="" Pos="6" idDatatype="28" DatatypeParams="" Width="0" Prec="0" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="metatag description">
<OPTIONSELECTED>
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1186" ColName="keywords" PrevColName="" Pos="7" idDatatype="28" DatatypeParams="" Width="0" Prec="0" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="metatag keywords">
<OPTIONSELECTED>
</OPTIONSELECTED>
</COLUMN>
</COLUMNS>
<RELATIONS_END>
<RELATION_END ID="1331" />
<RELATION_END ID="1340" />
</RELATIONS_END>
<INDICES>
<INDEX ID="1327" IndexName="PRIMARY" IndexKind="0" FKRefDef_Obj_id="-1">
<INDEXCOLUMNS>
<INDEXCOLUMN idColumn="1315" LengthParam="0" />
</INDEXCOLUMNS>
</INDEX>
</INDICES>
</TABLE>
<TABLE ID="1161" Tablename="YDCMConfiguration" PrevTableName="configuration" XPos="1420" YPos="60" TableType="0" TablePrefix="0" nmTable="0" Temporary="0" UseStandardInserts="0" StandardInserts="INSERT INTO configuration(name, value)\nVALUES(\aname\a, \a\A.$site_name.\A\a);\nINSERT INTO configuration(name, value)\nVALUES(\aadministrator_id\a, \a1\a);\nINSERT INTO configuration(name, value)\nVALUES(\aserver_type\a, \a0\a);\nINSERT INTO configuration(name, value)\nVALUES(\agmt\a, \a0\a);\nINSERT INTO configuration(name, value)\nVALUES(\acharset\a, \aiso-8859-15\a);\nINSERT INTO configuration(name, value)\nVALUES(\astate\a, \a1\a);\nINSERT INTO configuration(name, value)\nVALUES(\aoffline_msg\a, \aPortal indisponivel\a);\nINSERT INTO configuration(name, value)\nVALUES(\aerror_msg\a, \aOcorreu um erro\a);\nINSERT INTO configuration(name, value)\nVALUES(\avisits_total\a, \a0\a);\nINSERT INTO configuration(name, value)\nVALUES(\avisits_current\a, \a0\a);\nINSERT INTO configuration(name, value)\nVALUES(\apack\a, \adefault\a);\nINSERT INTO configuration(name, value)\nVALUES(\ainstalation_date\a, \a123123123\a);\nINSERT INTO configuration(name, value)\nVALUES(\aemail_server\a, \a0\a);\nINSERT INTO configuration(name, value)\nVALUES(\aemail_from\a, \a\a);\nINSERT INTO configuration(name, value)\nVALUES(\aemail_host\a, \alocalhost\a);\nINSERT INTO configuration(name, value)\nVALUES(\aemail_port\a, \a25\a);\nINSERT INTO configuration(name, value)\nVALUES(\aemail_user\a, \a\a);\nINSERT INTO configuration(name, value)\nVALUES(\aemail_pass\a, \a\a);\nINSERT INTO configuration(name, value)\nVALUES(\aemail_priority\a, \a1\a);\nINSERT INTO configuration(name, value)\nVALUES(\aemail_helo\a, \a\a);\n" TableOptions="DelayKeyTblUpdates=0\nPackKeys=0\nRowChecksum=0\nRowFormat=0\nUseRaid=0\nRaidType=0\n" Comments="" Collapsed="0" IsLinkedObject="0" IDLinkedModel="-1" Obj_id_Linked="-1" OrderPos="5" >
<COLUMNS>
<COLUMN ID="1188" ColName="name" PrevColName="" Pos="1" idDatatype="20" DatatypeParams="(50)" Width="0" Prec="0" PrimaryKey="1" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="variable name">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1189" ColName="value" PrevColName="" Pos="2" idDatatype="20" DatatypeParams="(255)" Width="0" Prec="0" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="value">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
</COLUMNS>
<INDICES>
<INDEX ID="1281" IndexName="PRIMARY" IndexKind="0" FKRefDef_Obj_id="-1">
<INDEXCOLUMNS>
<INDEXCOLUMN idColumn="1188" LengthParam="0" />
</INDEXCOLUMNS>
</INDEX>
<INDEX ID="1190" IndexName="name" IndexKind="1" FKRefDef_Obj_id="-1">
<INDEXCOLUMNS>
<INDEXCOLUMN idColumn="1188" LengthParam="0" />
</INDEXCOLUMNS>
</INDEX>
</INDICES>
</TABLE>
<TABLE ID="1162" Tablename="YDCMTree" PrevTableName="content" XPos="40" YPos="820" TableType="0" TablePrefix="0" nmTable="0" Temporary="0" UseStandardInserts="0" StandardInserts="INSERT INTO content(id, parent, path, pathname, position, type, children, reference, state, services, author, date, access, search, published, date_start, date_end, published_after, published_after_notify)\nVALUES(2, 1, \a1\a, \a/\a, 1, \amenu\a, 0, \astandard\a, 1, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0);\nINSERT INTO content(id, parent, path, pathname, position, type, children, reference, state, services, author, date, access, search, published, date_start, date_end, published_after, published_after_notify)\nVALUES(3, 1, \a1\a, \a/\a, 9999999, \amenu_archive\a, 0, \aarchive\a, 1, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0);\n" TableOptions="DelayKeyTblUpdates=0\nPackKeys=0\nRowChecksum=0\nRowFormat=0\nUseRaid=0\nRaidType=0\n" Comments="" Collapsed="0" IsLinkedObject="0" IDLinkedModel="-1" Obj_id_Linked="-1" OrderPos="6" >
<COLUMNS>
<COLUMN ID="1191" ColName="content_id" PrevColName="id" Pos="1" idDatatype="5" DatatypeParams="(10)" Width="0" Prec="0" PrimaryKey="1" NotNull="1" AutoInc="1" IsForeignKey="0" DefaultValue="" Comments="Autoincrement id">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1192" ColName="parent_id" PrevColName="parent" Pos="2" idDatatype="5" DatatypeParams="(10)" Width="0" Prec="0" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="Parent node id">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1418" ColName="nleft" PrevColName="" Pos="18" idDatatype="5" DatatypeParams="" Width="-1" Prec="-1" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="0" Comments="">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1419" ColName="nright" PrevColName="" Pos="19" idDatatype="5" DatatypeParams="" Width="-1" Prec="-1" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="0" Comments="">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1420" ColName="nlevel" PrevColName="" Pos="20" idDatatype="5" DatatypeParams="" Width="-1" Prec="-1" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="0" Comments="">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1195" ColName="position" PrevColName="" Pos="5" idDatatype="5" DatatypeParams="(10)" Width="0" Prec="0" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="0" DefaultValue="1" Comments="position related to brothers">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1196" ColName="type" PrevColName="" Pos="6" idDatatype="20" DatatypeParams="(20)" Width="0" Prec="0" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="component type name. eg YDCMPage">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1198" ColName="reference" PrevColName="" Pos="8" idDatatype="20" DatatypeParams="(100)" Width="0" Prec="0" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="internal name">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1199" ColName="state" PrevColName="" Pos="9" idDatatype="1" DatatypeParams="(1)" Width="0" Prec="0" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="state code">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1203" ColName="access" PrevColName="" Pos="13" idDatatype="1" DatatypeParams="(2)" Width="0" Prec="0" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="1" Comments="access code. 0 private, 1 public">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1204" ColName="searcheable" PrevColName="search" Pos="14" idDatatype="1" DatatypeParams="(1)" Width="0" Prec="0" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="component will be afected by visitors searches">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1206" ColName="published_date_start" PrevColName="date_start" Pos="16" idDatatype="5" DatatypeParams="(20)" Width="0" Prec="0" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="schedule start date (used when state is schedule)">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1207" ColName="published_date_end" PrevColName="date_end" Pos="17" idDatatype="5" DatatypeParams="(20)" Width="0" Prec="0" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="schedule end date (used when state is schedule)">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
</COLUMNS>
<RELATIONS_START>
<RELATION_START ID="1340" />
<RELATION_START ID="1341" />
<RELATION_START ID="1437" />
<RELATION_START ID="1526" />
<RELATION_START ID="1535" />
<RELATION_START ID="1572" />
</RELATIONS_START>
<INDICES>
<INDEX ID="1210" IndexName="PRIMARY" IndexKind="0" FKRefDef_Obj_id="-1">
<INDEXCOLUMNS>
<INDEXCOLUMN idColumn="1191" LengthParam="0" />
</INDEXCOLUMNS>
</INDEX>
</INDICES>
</TABLE>
<TABLE ID="1163" Tablename="YDCMLink" PrevTableName="content_language" XPos="360" YPos="1060" TableType="0" TablePrefix="0" nmTable="0" Temporary="0" UseStandardInserts="0" StandardInserts="\n" TableOptions="DelayKeyTblUpdates=0\nPackKeys=0\nRowChecksum=0\nRowFormat=0\nUseRaid=0\nRaidType=0\n" Comments="" Collapsed="0" IsLinkedObject="0" IDLinkedModel="-1" Obj_id_Linked="-1" OrderPos="7" >
<COLUMNS>
<COLUMN ID="1318" ColName="component_id" PrevColName="" Pos="5" idDatatype="5" DatatypeParams="" Width="-1" Prec="-1" PrimaryKey="1" NotNull="1" AutoInc="1" IsForeignKey="0" DefaultValue="" Comments="">
<OPTIONSELECTED>
<OPTIONSELECT Value="1" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1425" ColName="content_id" PrevColName="" Pos="1" idDatatype="5" DatatypeParams="(10)" Width="0" Prec="0" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="1" DefaultValue="" Comments="">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1213" ColName="language_id" PrevColName="language" Pos="3" idDatatype="20" DatatypeParams="(255)" Width="0" Prec="0" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="1" DefaultValue="" Comments="language code">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1212" ColName="title" PrevColName="" Pos="2" idDatatype="20" DatatypeParams="(200)" Width="0" Prec="0" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="link title">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1295" ColName="url" PrevColName="" Pos="3" idDatatype="20" DatatypeParams="(255)" Width="-1" Prec="-1" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="link url">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
</COLUMNS>
<RELATIONS_END>
<RELATION_END ID="1332" />
<RELATION_END ID="1341" />
</RELATIONS_END>
<INDICES>
<INDEX ID="1325" IndexName="PRIMARY" IndexKind="0" FKRefDef_Obj_id="-1">
<INDEXCOLUMNS>
<INDEXCOLUMN idColumn="1318" LengthParam="0" />
</INDEXCOLUMNS>
</INDEX>
</INDICES>
</TABLE>
<TABLE ID="1164" Tablename="YDCMConfiguration_files" PrevTableName="files" XPos="1420" YPos="160" TableType="0" TablePrefix="0" nmTable="0" Temporary="0" UseStandardInserts="0" StandardInserts="INSERT INTO files(name, value)\nVALUES(\amaxfilesize_mb\a, \a5\a);\nINSERT INTO files(name, value)\nVALUES(\aimage\a, \ajpeg, gif, png, ico, psd, bmp\a);\nINSERT INTO files(name, value)\nVALUES(\atext\a, \ahtm, html, xml, js, css, doc, pdf, xls, ppt, txt\a);\nINSERT INTO files(name, value)\nVALUES(\avideo\a, \ampg, mpeg, avi, wmv\a);\nINSERT INTO files(name, value)\nVALUES(\aaudio\a, \amp3, wav, ogg, mid\a);\nINSERT INTO files(name, value)\nVALUES(\acompress\a, \azip, rar, gz, tgz\a);\nINSERT INTO files(name, value)\nVALUES(\aother\a, \aswf\a);\n" TableOptions="DelayKeyTblUpdates=0\nPackKeys=0\nRowChecksum=0\nRowFormat=0\nUseRaid=0\nRaidType=0\n" Comments="" Collapsed="0" IsLinkedObject="0" IDLinkedModel="-1" Obj_id_Linked="-1" OrderPos="8" >
<COLUMNS>
<COLUMN ID="1214" ColName="name" PrevColName="" Pos="1" idDatatype="20" DatatypeParams="(50)" Width="0" Prec="0" PrimaryKey="1" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1215" ColName="value" PrevColName="" Pos="2" idDatatype="20" DatatypeParams="(255)" Width="0" Prec="0" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
</COLUMNS>
<INDICES>
<INDEX ID="1282" IndexName="PRIMARY" IndexKind="0" FKRefDef_Obj_id="-1">
<INDEXCOLUMNS>
<INDEXCOLUMN idColumn="1214" LengthParam="0" />
</INDEXCOLUMNS>
</INDEX>
<INDEX ID="1216" IndexName="name" IndexKind="1" FKRefDef_Obj_id="-1">
<INDEXCOLUMNS>
<INDEXCOLUMN idColumn="1214" LengthParam="0" />
</INDEXCOLUMNS>
</INDEX>
</INDICES>
</TABLE>
<TABLE ID="1165" Tablename="YDCMLanguages" PrevTableName="languages" XPos="700" YPos="920" TableType="0" TablePrefix="0" nmTable="0" Temporary="0" UseStandardInserts="0" StandardInserts="INSERT INTO languages(interface, name, lang, active, default_lang)\nVALUES(\aadmin\a, \aen\a, \aEnglish\a, 1, 1);\nINSERT INTO languages(interface, name, lang, active, default_lang)\nVALUES(\aadmin\a, \apt\a, \aPortugu\234s\a, 1, 0);\nINSERT INTO languages(interface, name, lang, active, default_lang)\nVALUES(\aclient\a, \aen\a, \aEnglish\a, 1, 1);\nINSERT INTO languages(interface, name, lang, active, default_lang)\nVALUES(\aclient\a, \apt\a, \aPortugu\234s\a, 1, 0);\n" TableOptions="DelayKeyTblUpdates=0\nPackKeys=0\nRowChecksum=0\nRowFormat=0\nUseRaid=0\nRaidType=0\n" Comments="" Collapsed="0" IsLinkedObject="0" IDLinkedModel="-1" Obj_id_Linked="-1" OrderPos="9" >
<COLUMNS>
<COLUMN ID="1219" ColName="language_id" PrevColName="lang" Pos="3" idDatatype="20" DatatypeParams="(255)" Width="0" Prec="0" PrimaryKey="1" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="language code">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1218" ColName="name" PrevColName="" Pos="2" idDatatype="20" DatatypeParams="(50)" Width="0" Prec="0" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="language name">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1220" ColName="active" PrevColName="" Pos="4" idDatatype="1" DatatypeParams="(1)" Width="0" Prec="0" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="is active. 1 true, 0 false">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1221" ColName="visitors_default" PrevColName="default_lang" Pos="5" idDatatype="1" DatatypeParams="(1)" Width="0" Prec="0" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="this is the default language for visitors side. 1 true, 0 false">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
</COLUMNS>
<RELATIONS_START>
<RELATION_START ID="1331" />
<RELATION_START ID="1332" />
<RELATION_START ID="1366" />
<RELATION_START ID="1527" />
<RELATION_START ID="1536" />
<RELATION_START ID="1573" />
</RELATIONS_START>
<INDICES>
<INDEX ID="1290" IndexName="PRIMARY" IndexKind="0" FKRefDef_Obj_id="-1">
<INDEXCOLUMNS>
<INDEXCOLUMN idColumn="1219" LengthParam="0" />
</INDEXCOLUMNS>
</INDEX>
</INDICES>
</TABLE>
<TABLE ID="1166" Tablename="YDCMVisits" PrevTableName="site_log" XPos="1120" YPos="60" TableType="0" TablePrefix="0" nmTable="0" Temporary="0" UseStandardInserts="0" StandardInserts="\n" TableOptions="DelayKeyTblUpdates=0\nPackKeys=0\nRowChecksum=0\nRowFormat=0\nUseRaid=0\nRaidType=0\n" Comments="" Collapsed="0" IsLinkedObject="0" IDLinkedModel="-1" Obj_id_Linked="-1" OrderPos="10" >
<COLUMNS>
<COLUMN ID="1222" ColName="visit_id" PrevColName="id" Pos="1" idDatatype="5" DatatypeParams="" Width="0" Prec="0" PrimaryKey="1" NotNull="1" AutoInc="1" IsForeignKey="0" DefaultValue="" Comments="autoincrement id">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1224" ColName="date" PrevColName="timestamp" Pos="3" idDatatype="15" DatatypeParams="" Width="0" Prec="0" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="visit date">
<OPTIONSELECTED>
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1228" ColName="browser" PrevColName="" Pos="7" idDatatype="20" DatatypeParams="(255)" Width="0" Prec="0" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="browser info">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1229" ColName="platform" PrevColName="" Pos="8" idDatatype="20" DatatypeParams="(255)" Width="0" Prec="0" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="user platform">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
</COLUMNS>
<INDICES>
<INDEX ID="1230" IndexName="PRIMARY" IndexKind="0" FKRefDef_Obj_id="-1">
<INDEXCOLUMNS>
<INDEXCOLUMN idColumn="1222" LengthParam="0" />
</INDEXCOLUMNS>
</INDEX>
</INDICES>
</TABLE>
<TABLE ID="1167" Tablename="YDCMSearches" PrevTableName="site_searches" XPos="940" YPos="60" TableType="0" TablePrefix="0" nmTable="0" Temporary="0" UseStandardInserts="0" StandardInserts="\n" TableOptions="DelayKeyTblUpdates=0\nPackKeys=0\nRowChecksum=0\nRowFormat=0\nUseRaid=0\nRaidType=0\n" Comments="" Collapsed="0" IsLinkedObject="0" IDLinkedModel="-1" Obj_id_Linked="-1" OrderPos="11" >
<COLUMNS>
<COLUMN ID="1231" ColName="search_id" PrevColName="id" Pos="1" idDatatype="5" DatatypeParams="(10)" Width="0" Prec="0" PrimaryKey="1" NotNull="1" AutoInc="1" IsForeignKey="0" DefaultValue="" Comments="autoincrement id">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1232" ColName="date" PrevColName="timestamp" Pos="2" idDatatype="15" DatatypeParams="" Width="0" Prec="0" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="search date">
<OPTIONSELECTED>
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1236" ColName="word" PrevColName="" Pos="6" idDatatype="20" DatatypeParams="(255)" Width="0" Prec="0" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="word used on search">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
</COLUMNS>
<INDICES>
<INDEX ID="1237" IndexName="PRIMARY" IndexKind="0" FKRefDef_Obj_id="-1">
<INDEXCOLUMNS>
<INDEXCOLUMN idColumn="1231" LengthParam="0" />
</INDEXCOLUMNS>
</INDEX>
</INDICES>
</TABLE>
<TABLE ID="1168" Tablename="YDCMUsers" PrevTableName="users" XPos="900" YPos="480" TableType="0" TablePrefix="0" nmTable="0" Temporary="0" UseStandardInserts="0" StandardInserts="INSERT INTO users(id, parent, path, position, children, master, username, password, name, email, other, language, state, login_start, login_end, created, created_user, login_counter, login_last, login_current, personalconfig_access, content_access, content_create, content_edit, content_approve, content_move, content_copy, content_block, content_delete, content_custom, visits_access, searches_access, downloads_access, information_access, database_access, audit_access, files_access, files_configuration, users_access, configuration_general_access, configuration_languages_access)\nVALUES(1, 0, \a0\a, 1, 0, 0, \a21232f297a57a5a\a, \a21232f297a57a5a743894a0e4a801fc3\a, \apH cms - Super administrator\a, \a \a, \a \a, \aEnglish\a, 1, 0, 0, 0, 1, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);\n" TableOptions="DelayKeyTblUpdates=0\nPackKeys=0\nRowChecksum=0\nRowFormat=0\nUseRaid=0\nRaidType=0\n" Comments="" Collapsed="0" IsLinkedObject="0" IDLinkedModel="-1" Obj_id_Linked="-1" OrderPos="12" >
<COLUMNS>
<COLUMN ID="1238" ColName="user_id" PrevColName="id" Pos="1" idDatatype="5" DatatypeParams="(10)" Width="0" Prec="0" PrimaryKey="1" NotNull="1" AutoInc="1" IsForeignKey="0" DefaultValue="" Comments="autoincrement user id">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1239" ColName="parent_id" PrevColName="parent" Pos="2" idDatatype="5" DatatypeParams="(10)" Width="0" Prec="0" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="optional parent of this user">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1240" ColName="nleft" PrevColName="path" Pos="3" idDatatype="5" DatatypeParams="" Width="0" Prec="0" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="optional nleft">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1241" ColName="nright" PrevColName="position" Pos="4" idDatatype="5" DatatypeParams="" Width="0" Prec="0" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="optional nright">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1242" ColName="nlevel" PrevColName="children" Pos="5" idDatatype="5" DatatypeParams="" Width="0" Prec="0" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="optional nlevel">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1244" ColName="username" PrevColName="" Pos="7" idDatatype="20" DatatypeParams="(100)" Width="0" Prec="0" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1245" ColName="password" PrevColName="" Pos="8" idDatatype="20" DatatypeParams="(100)" Width="0" Prec="0" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1246" ColName="name" PrevColName="" Pos="9" idDatatype="20" DatatypeParams="(255)" Width="0" Prec="0" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1247" ColName="email" PrevColName="" Pos="10" idDatatype="20" DatatypeParams="(255)" Width="0" Prec="0" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1248" ColName="other" PrevColName="" Pos="11" idDatatype="28" DatatypeParams="" Width="0" Prec="0" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="other user informations">
<OPTIONSELECTED>
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1249" ColName="language_id" PrevColName="language" Pos="12" idDatatype="20" DatatypeParams="(255)" Width="0" Prec="0" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="1" DefaultValue="" Comments="language code for administration">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1250" ColName="state" PrevColName="" Pos="13" idDatatype="1" DatatypeParams="(1)" Width="0" Prec="0" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="user state">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1251" ColName="login_start" PrevColName="" Pos="14" idDatatype="15" DatatypeParams="" Width="0" Prec="0" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="when state is schedule this is the start date">
<OPTIONSELECTED>
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1252" ColName="login_end" PrevColName="" Pos="15" idDatatype="15" DatatypeParams="" Width="0" Prec="0" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="when state is schedule this is the end date">
<OPTIONSELECTED>
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1255" ColName="login_counter" PrevColName="" Pos="18" idDatatype="5" DatatypeParams="(11)" Width="0" Prec="0" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="login counter">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1256" ColName="login_last" PrevColName="" Pos="19" idDatatype="15" DatatypeParams="" Width="0" Prec="0" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="last login date">
<OPTIONSELECTED>
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1257" ColName="login_current" PrevColName="" Pos="20" idDatatype="15" DatatypeParams="" Width="0" Prec="0" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="date of current session">
<OPTIONSELECTED>
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1254" ColName="created_user" PrevColName="" Pos="17" idDatatype="5" DatatypeParams="(20)" Width="0" Prec="0" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="id  of user that created this user">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1253" ColName="created_date" PrevColName="created" Pos="16" idDatatype="15" DatatypeParams="" Width="0" Prec="0" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="creation date">
<OPTIONSELECTED>
</OPTIONSELECTED>
</COLUMN>
</COLUMNS>
<RELATIONS_START>
<RELATION_START ID="1333" />
<RELATION_START ID="1383" />
<RELATION_START ID="1410" />
<RELATION_START ID="1436" />
</RELATIONS_START>
<RELATIONS_END>
<RELATION_END ID="1366" />
</RELATIONS_END>
<INDICES>
<INDEX ID="1279" IndexName="PRIMARY" IndexKind="0" FKRefDef_Obj_id="-1">
<INDEXCOLUMNS>
<INDEXCOLUMN idColumn="1238" LengthParam="0" />
</INDEXCOLUMNS>
</INDEX>
</INDICES>
</TABLE>
<TABLE ID="1367" Tablename="YDCMHelpdesk_posts" PrevTableName="Table_11" XPos="740" YPos="1540" TableType="0" TablePrefix="0" nmTable="0" Temporary="0" UseStandardInserts="0" StandardInserts="\n" TableOptions="DelayKeyTblUpdates=0\nPackKeys=0\nRowChecksum=0\nRowFormat=0\nUseRaid=0\nRaidType=0\n" Comments="" Collapsed="0" IsLinkedObject="0" IDLinkedModel="-1" Obj_id_Linked="-1" OrderPos="20" >
<COLUMNS>
<COLUMN ID="1581" ColName="post_id" PrevColName="" Pos="13" idDatatype="5" DatatypeParams="" Width="-1" Prec="-1" PrimaryKey="1" NotNull="1" AutoInc="1" IsForeignKey="0" DefaultValue="" Comments="">
<OPTIONSELECTED>
<OPTIONSELECT Value="1" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1369" ColName="component_id" PrevColName="ticket_id" Pos="0" idDatatype="5" DatatypeParams="" Width="-1" Prec="-1" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="1" DefaultValue="" Comments="">
<OPTIONSELECTED>
<OPTIONSELECT Value="1" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1371" ColName="user_id" PrevColName="" Pos="1" idDatatype="5" DatatypeParams="(10)" Width="-1" Prec="-1" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="1" DefaultValue="" Comments="">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1372" ColName="subject" PrevColName="" Pos="2" idDatatype="28" DatatypeParams="" Width="-1" Prec="-1" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="">
<OPTIONSELECTED>
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1373" ColName="localization" PrevColName="" Pos="3" idDatatype="28" DatatypeParams="" Width="-1" Prec="-1" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="">
<OPTIONSELECTED>
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1374" ColName="urgency_id" PrevColName="urgency" Pos="4" idDatatype="1" DatatypeParams="(1)" Width="-1" Prec="-1" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="1" DefaultValue="0" Comments="">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1375" ColName="state_id" PrevColName="state" Pos="5" idDatatype="1" DatatypeParams="(1)" Width="-1" Prec="-1" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="1" DefaultValue="1" Comments="">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1376" ColName="text" PrevColName="" Pos="6" idDatatype="28" DatatypeParams="" Width="-1" Prec="-1" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="">
<OPTIONSELECTED>
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1377" ColName="created_in" PrevColName="" Pos="7" idDatatype="15" DatatypeParams="" Width="-1" Prec="-1" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="">
<OPTIONSELECTED>
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1378" ColName="reported_in" PrevColName="" Pos="8" idDatatype="15" DatatypeParams="" Width="-1" Prec="-1" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="">
<OPTIONSELECTED>
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1379" ColName="reported_by" PrevColName="" Pos="9" idDatatype="20" DatatypeParams="(255)" Width="-1" Prec="-1" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1380" ColName="reported_to_in" PrevColName="" Pos="10" idDatatype="15" DatatypeParams="" Width="-1" Prec="-1" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="">
<OPTIONSELECTED>
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1381" ColName="reported_to" PrevColName="" Pos="11" idDatatype="20" DatatypeParams="(255)" Width="-1" Prec="-1" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1382" ColName="reported_to_local" PrevColName="" Pos="12" idDatatype="20" DatatypeParams="(255)" Width="-1" Prec="-1" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
</COLUMNS>
<RELATIONS_START>
<RELATION_START ID="1409" />
</RELATIONS_START>
<RELATIONS_END>
<RELATION_END ID="1383" />
<RELATION_END ID="1392" />
<RELATION_END ID="1413" />
<RELATION_END ID="1575" />
</RELATIONS_END>
<INDICES>
<INDEX ID="1582" IndexName="PRIMARY" IndexKind="0" FKRefDef_Obj_id="-1">
<INDEXCOLUMNS>
<INDEXCOLUMN idColumn="1581" LengthParam="0" />
</INDEXCOLUMNS>
</INDEX>
</INDICES>
</TABLE>
<TABLE ID="1384" Tablename="YDCMHelpdesk_state" PrevTableName="Table_12" XPos="1080" YPos="1760" TableType="0" TablePrefix="0" nmTable="0" Temporary="0" UseStandardInserts="0" StandardInserts="\n" TableOptions="DelayKeyTblUpdates=0\nPackKeys=0\nRowChecksum=0\nRowFormat=0\nUseRaid=0\nRaidType=0\n" Comments="" Collapsed="0" IsLinkedObject="0" IDLinkedModel="-1" Obj_id_Linked="-1" OrderPos="22" >
<COLUMNS>
<COLUMN ID="1386" ColName="state_id" PrevColName="" Pos="0" idDatatype="1" DatatypeParams="(1)" Width="-1" Prec="-1" PrimaryKey="1" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1388" ColName="description" PrevColName="state_name" Pos="1" idDatatype="20" DatatypeParams="(255)" Width="-1" Prec="-1" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
</COLUMNS>
<RELATIONS_START>
<RELATION_START ID="1392" />
</RELATIONS_START>
<INDICES>
<INDEX ID="1387" IndexName="PRIMARY" IndexKind="0" FKRefDef_Obj_id="-1">
<INDEXCOLUMNS>
<INDEXCOLUMN idColumn="1386" LengthParam="0" />
</INDEXCOLUMNS>
</INDEX>
</INDICES>
</TABLE>
<TABLE ID="1393" Tablename="YDCMHelpdesk_urgency" PrevTableName="Table_13" XPos="1080" YPos="1680" TableType="0" TablePrefix="0" nmTable="0" Temporary="0" UseStandardInserts="0" StandardInserts="\n" TableOptions="DelayKeyTblUpdates=0\nPackKeys=0\nRowChecksum=0\nRowFormat=0\nUseRaid=0\nRaidType=0\n" Comments="" Collapsed="0" IsLinkedObject="0" IDLinkedModel="-1" Obj_id_Linked="-1" OrderPos="24" >
<COLUMNS>
<COLUMN ID="1395" ColName="urgency_id" PrevColName="" Pos="0" idDatatype="1" DatatypeParams="(1)" Width="-1" Prec="-1" PrimaryKey="1" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1397" ColName="description" PrevColName="" Pos="1" idDatatype="20" DatatypeParams="(255)" Width="-1" Prec="-1" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1416" ColName="color" PrevColName="" Pos="2" idDatatype="20" DatatypeParams="(45)" Width="-1" Prec="-1" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
</COLUMNS>
<RELATIONS_START>
<RELATION_START ID="1413" />
</RELATIONS_START>
<INDICES>
<INDEX ID="1396" IndexName="PRIMARY" IndexKind="0" FKRefDef_Obj_id="-1">
<INDEXCOLUMNS>
<INDEXCOLUMN idColumn="1395" LengthParam="0" />
</INDEXCOLUMNS>
</INDEX>
</INDICES>
</TABLE>
<TABLE ID="1401" Tablename="YDCMHelpdesk_response" PrevTableName="Table_14" XPos="1080" YPos="1560" TableType="0" TablePrefix="0" nmTable="0" Temporary="0" UseStandardInserts="0" StandardInserts="\n" TableOptions="DelayKeyTblUpdates=0\nPackKeys=0\nRowChecksum=0\nRowFormat=0\nUseRaid=0\nRaidType=0\n" Comments="" Collapsed="0" IsLinkedObject="0" IDLinkedModel="-1" Obj_id_Linked="-1" OrderPos="26" >
<COLUMNS>
<COLUMN ID="1403" ColName="response_id" PrevColName="" Pos="0" idDatatype="5" DatatypeParams="" Width="-1" Prec="-1" PrimaryKey="1" NotNull="1" AutoInc="1" IsForeignKey="0" DefaultValue="" Comments="">
<OPTIONSELECTED>
<OPTIONSELECT Value="1" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1584" ColName="post_id" PrevColName="" Pos="4" idDatatype="5" DatatypeParams="" Width="-1" Prec="-1" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="1" DefaultValue="" Comments="">
<OPTIONSELECTED>
<OPTIONSELECT Value="1" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1406" ColName="user_id" PrevColName="" Pos="2" idDatatype="5" DatatypeParams="(10)" Width="-1" Prec="-1" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="1" DefaultValue="" Comments="">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1407" ColName="date" PrevColName="" Pos="3" idDatatype="15" DatatypeParams="" Width="-1" Prec="-1" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="">
<OPTIONSELECTED>
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1408" ColName="description" PrevColName="" Pos="4" idDatatype="28" DatatypeParams="" Width="-1" Prec="-1" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="">
<OPTIONSELECTED>
</OPTIONSELECTED>
</COLUMN>
</COLUMNS>
<RELATIONS_END>
<RELATION_END ID="1409" />
<RELATION_END ID="1410" />
</RELATIONS_END>
<INDICES>
<INDEX ID="1404" IndexName="PRIMARY" IndexKind="0" FKRefDef_Obj_id="-1">
<INDEXCOLUMNS>
<INDEXCOLUMN idColumn="1403" LengthParam="0" />
</INDEXCOLUMNS>
</INDEX>
</INDICES>
</TABLE>
<TABLE ID="1430" Tablename="YDCMLocks" PrevTableName="Table_15" XPos="40" YPos="620" TableType="0" TablePrefix="0" nmTable="0" Temporary="0" UseStandardInserts="0" StandardInserts="\n" TableOptions="DelayKeyTblUpdates=0\nPackKeys=0\nRowChecksum=0\nRowFormat=0\nUseRaid=0\nRaidType=0\n" Comments="" Collapsed="0" IsLinkedObject="0" IDLinkedModel="-1" Obj_id_Linked="-1" OrderPos="27" >
<COLUMNS>
<COLUMN ID="1578" ColName="lock_id" PrevColName="" Pos="2" idDatatype="5" DatatypeParams="" Width="-1" Prec="-1" PrimaryKey="1" NotNull="1" AutoInc="1" IsForeignKey="0" DefaultValue="" Comments="lock ID">
<OPTIONSELECTED>
<OPTIONSELECT Value="1" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1432" ColName="content_id" PrevColName="" Pos="0" idDatatype="5" DatatypeParams="(10)" Width="-1" Prec="-1" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="1" DefaultValue="" Comments="component code">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1434" ColName="user_id" PrevColName="" Pos="1" idDatatype="5" DatatypeParams="(10)" Width="-1" Prec="-1" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="1" DefaultValue="" Comments="user id that lock component">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
</COLUMNS>
<RELATIONS_END>
<RELATION_END ID="1436" />
<RELATION_END ID="1437" />
</RELATIONS_END>
<INDICES>
<INDEX ID="1579" IndexName="PRIMARY" IndexKind="0" FKRefDef_Obj_id="-1">
<INDEXCOLUMNS>
<INDEXCOLUMN idColumn="1578" LengthParam="0" />
</INDEXCOLUMNS>
</INDEX>
</INDICES>
</TABLE>
<TABLE ID="1519" Tablename="YDCMMenu" PrevTableName="Table_21" XPos="360" YPos="680" TableType="0" TablePrefix="0" nmTable="0" Temporary="0" UseStandardInserts="0" StandardInserts="\n" TableOptions="DelayKeyTblUpdates=0\nPackKeys=0\nRowChecksum=0\nRowFormat=0\nUseRaid=0\nRaidType=0\n" Comments="" Collapsed="0" IsLinkedObject="0" IDLinkedModel="-1" Obj_id_Linked="-1" OrderPos="31" >
<COLUMNS>
<COLUMN ID="1521" ColName="component_id" PrevColName="" Pos="0" idDatatype="5" DatatypeParams="" Width="-1" Prec="-1" PrimaryKey="1" NotNull="1" AutoInc="1" IsForeignKey="0" DefaultValue="" Comments="">
<OPTIONSELECTED>
<OPTIONSELECT Value="1" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1523" ColName="content_id" PrevColName="" Pos="1" idDatatype="5" DatatypeParams="(10)" Width="-1" Prec="-1" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="1" DefaultValue="" Comments="">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1524" ColName="language_id" PrevColName="" Pos="2" idDatatype="20" DatatypeParams="(255)" Width="-1" Prec="-1" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="1" DefaultValue="" Comments="language code">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1525" ColName="title" PrevColName="" Pos="3" idDatatype="20" DatatypeParams="(255)" Width="-1" Prec="-1" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="menu title">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
</COLUMNS>
<RELATIONS_END>
<RELATION_END ID="1526" />
<RELATION_END ID="1527" />
</RELATIONS_END>
<INDICES>
<INDEX ID="1522" IndexName="PRIMARY" IndexKind="0" FKRefDef_Obj_id="-1">
<INDEXCOLUMNS>
<INDEXCOLUMN idColumn="1521" LengthParam="0" />
</INDEXCOLUMNS>
</INDEX>
</INDICES>
</TABLE>
<TABLE ID="1528" Tablename="YDCMGuestbook" PrevTableName="Table_22" XPos="360" YPos="1200" TableType="0" TablePrefix="0" nmTable="0" Temporary="0" UseStandardInserts="0" StandardInserts="\n" TableOptions="DelayKeyTblUpdates=0\nPackKeys=0\nRowChecksum=0\nRowFormat=0\nUseRaid=0\nRaidType=0\n" Comments="" Collapsed="0" IsLinkedObject="0" IDLinkedModel="-1" Obj_id_Linked="-1" OrderPos="34" >
<COLUMNS>
<COLUMN ID="1530" ColName="component_id" PrevColName="" Pos="0" idDatatype="5" DatatypeParams="" Width="-1" Prec="-1" PrimaryKey="1" NotNull="1" AutoInc="1" IsForeignKey="0" DefaultValue="" Comments="">
<OPTIONSELECTED>
<OPTIONSELECT Value="1" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1532" ColName="content_id" PrevColName="" Pos="1" idDatatype="5" DatatypeParams="(10)" Width="-1" Prec="-1" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="1" DefaultValue="" Comments="">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1533" ColName="language_id" PrevColName="" Pos="2" idDatatype="20" DatatypeParams="(255)" Width="-1" Prec="-1" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="1" DefaultValue="" Comments="language code">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1534" ColName="title" PrevColName="" Pos="3" idDatatype="20" DatatypeParams="(255)" Width="-1" Prec="-1" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="guestbook name">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
</COLUMNS>
<RELATIONS_START>
<RELATION_START ID="1547" />
</RELATIONS_START>
<RELATIONS_END>
<RELATION_END ID="1535" />
<RELATION_END ID="1536" />
</RELATIONS_END>
<INDICES>
<INDEX ID="1531" IndexName="PRIMARY" IndexKind="0" FKRefDef_Obj_id="-1">
<INDEXCOLUMNS>
<INDEXCOLUMN idColumn="1530" LengthParam="0" />
</INDEXCOLUMNS>
</INDEX>
</INDICES>
</TABLE>
<TABLE ID="1537" Tablename="YDCMGuestbook_posts" PrevTableName="Table_23" XPos="640" YPos="1320" TableType="0" TablePrefix="0" nmTable="0" Temporary="0" UseStandardInserts="0" StandardInserts="\n" TableOptions="DelayKeyTblUpdates=0\nPackKeys=0\nRowChecksum=0\nRowFormat=0\nUseRaid=0\nRaidType=0\n" Comments="" Collapsed="0" IsLinkedObject="0" IDLinkedModel="-1" Obj_id_Linked="-1" OrderPos="37" >
<COLUMNS>
<COLUMN ID="1539" ColName="post_id" PrevColName="" Pos="0" idDatatype="5" DatatypeParams="" Width="-1" Prec="-1" PrimaryKey="1" NotNull="1" AutoInc="1" IsForeignKey="0" DefaultValue="" Comments="autoincrement post id">
<OPTIONSELECTED>
<OPTIONSELECT Value="1" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1541" ColName="component_id" PrevColName="" Pos="1" idDatatype="5" DatatypeParams="" Width="-1" Prec="-1" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="1" DefaultValue="" Comments="guestbook id">
<OPTIONSELECTED>
<OPTIONSELECT Value="1" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1542" ColName="date" PrevColName="" Pos="2" idDatatype="15" DatatypeParams="" Width="-1" Prec="-1" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="post date">
<OPTIONSELECTED>
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1543" ColName="name" PrevColName="" Pos="3" idDatatype="20" DatatypeParams="(255)" Width="-1" Prec="-1" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="post user name">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1544" ColName="email" PrevColName="" Pos="4" idDatatype="20" DatatypeParams="(255)" Width="-1" Prec="-1" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="email of poster">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1545" ColName="message" PrevColName="" Pos="5" idDatatype="28" DatatypeParams="" Width="-1" Prec="-1" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="">
<OPTIONSELECTED>
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1546" ColName="active" PrevColName="" Pos="6" idDatatype="1" DatatypeParams="(1)" Width="-1" Prec="-1" PrimaryKey="0" NotNull="1" AutoInc="0" IsForeignKey="0" DefaultValue="1" Comments="post is active and can be seen after creation. 1 yes, 0 no">
<OPTIONSELECTED>
<OPTIONSELECT Value="1" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
</COLUMNS>
<RELATIONS_END>
<RELATION_END ID="1547" />
</RELATIONS_END>
<INDICES>
<INDEX ID="1540" IndexName="PRIMARY" IndexKind="0" FKRefDef_Obj_id="-1">
<INDEXCOLUMNS>
<INDEXCOLUMN idColumn="1539" LengthParam="0" />
</INDEXCOLUMNS>
</INDEX>
</INDICES>
</TABLE>
<TABLE ID="1565" Tablename="YDCMHelpdesk" PrevTableName="Table_20" XPos="360" YPos="1520" TableType="0" TablePrefix="0" nmTable="0" Temporary="0" UseStandardInserts="0" StandardInserts="\n" TableOptions="DelayKeyTblUpdates=0\nPackKeys=0\nRowChecksum=0\nRowFormat=0\nUseRaid=0\nRaidType=0\n" Comments="" Collapsed="0" IsLinkedObject="0" IDLinkedModel="-1" Obj_id_Linked="-1" OrderPos="39" >
<COLUMNS>
<COLUMN ID="1567" ColName="component_id" PrevColName="" Pos="0" idDatatype="5" DatatypeParams="" Width="-1" Prec="-1" PrimaryKey="1" NotNull="1" AutoInc="1" IsForeignKey="0" DefaultValue="" Comments="">
<OPTIONSELECTED>
<OPTIONSELECT Value="1" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1569" ColName="content_id" PrevColName="" Pos="1" idDatatype="5" DatatypeParams="(10)" Width="-1" Prec="-1" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="1" DefaultValue="" Comments="">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1570" ColName="language_id" PrevColName="" Pos="2" idDatatype="20" DatatypeParams="(255)" Width="-1" Prec="-1" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="1" DefaultValue="" Comments="">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
<COLUMN ID="1571" ColName="title" PrevColName="" Pos="3" idDatatype="20" DatatypeParams="(255)" Width="-1" Prec="-1" PrimaryKey="0" NotNull="0" AutoInc="0" IsForeignKey="0" DefaultValue="" Comments="help desk title">
<OPTIONSELECTED>
<OPTIONSELECT Value="0" />
</OPTIONSELECTED>
</COLUMN>
</COLUMNS>
<RELATIONS_START>
<RELATION_START ID="1575" />
</RELATIONS_START>
<RELATIONS_END>
<RELATION_END ID="1572" />
<RELATION_END ID="1573" />
</RELATIONS_END>
<INDICES>
<INDEX ID="1568" IndexName="PRIMARY" IndexKind="0" FKRefDef_Obj_id="-1">
<INDEXCOLUMNS>
<INDEXCOLUMN idColumn="1567" LengthParam="0" />
</INDEXCOLUMNS>
</INDEX>
</INDICES>
</TABLE>
</TABLES>
<RELATIONS>
<RELATION ID="1331" RelationName="language_id" Kind="2" SrcTable="1165" DestTable="1160" FKFields="language_id=language_id\n" FKFieldsComments="\n" relDirection="4" MidOffset="0" OptionalStart="0" OptionalEnd="0" CaptionOffsetX="0" CaptionOffsetY="0" StartIntervalOffsetX="0" StartIntervalOffsetY="0" EndIntervalOffsetX="0" EndIntervalOffsetY="0" CreateRefDef="1" Invisible="0" RefDef="Matching=0\nOnDelete=3\nOnUpdate=3\n" Comments="" FKRefDefIndex_Obj_id="-1" Splitted="0" IsLinkedObject="0" IDLinkedModel="-1" Obj_id_Linked="-1" OrderPos="12" />
<RELATION ID="1332" RelationName="language_id" Kind="2" SrcTable="1165" DestTable="1163" FKFields="language_id=language_id\n" FKFieldsComments="\n" relDirection="4" MidOffset="0" OptionalStart="0" OptionalEnd="0" CaptionOffsetX="0" CaptionOffsetY="0" StartIntervalOffsetX="0" StartIntervalOffsetY="0" EndIntervalOffsetX="0" EndIntervalOffsetY="0" CreateRefDef="1" Invisible="0" RefDef="Matching=0\nOnDelete=3\nOnUpdate=3\n" Comments="" FKRefDefIndex_Obj_id="-1" Splitted="0" IsLinkedObject="0" IDLinkedModel="-1" Obj_id_Linked="-1" OrderPos="13" />
<RELATION ID="1333" RelationName="user_id" Kind="2" SrcTable="1168" DestTable="1158" FKFields="user_id=user_id\n" FKFieldsComments="\n" relDirection="1" MidOffset="0" OptionalStart="0" OptionalEnd="0" CaptionOffsetX="0" CaptionOffsetY="0" StartIntervalOffsetX="0" StartIntervalOffsetY="0" EndIntervalOffsetX="0" EndIntervalOffsetY="0" CreateRefDef="1" Invisible="0" RefDef="Matching=0\nOnDelete=3\nOnUpdate=3\n" Comments="" FKRefDefIndex_Obj_id="-1" Splitted="0" IsLinkedObject="0" IDLinkedModel="-1" Obj_id_Linked="-1" OrderPos="14" />
<RELATION ID="1340" RelationName="content_id" Kind="2" SrcTable="1162" DestTable="1160" FKFields="content_id=content_id\n" FKFieldsComments="\n" relDirection="2" MidOffset="0" OptionalStart="0" OptionalEnd="0" CaptionOffsetX="0" CaptionOffsetY="0" StartIntervalOffsetX="0" StartIntervalOffsetY="0" EndIntervalOffsetX="0" EndIntervalOffsetY="0" CreateRefDef="1" Invisible="0" RefDef="Matching=0\nOnDelete=3\nOnUpdate=3\n" Comments="" FKRefDefIndex_Obj_id="-1" Splitted="0" IsLinkedObject="0" IDLinkedModel="-1" Obj_id_Linked="-1" OrderPos="15" />
<RELATION ID="1341" RelationName="content_id" Kind="2" SrcTable="1162" DestTable="1163" FKFields="content_id=content_id\n" FKFieldsComments="\n" relDirection="2" MidOffset="11" OptionalStart="0" OptionalEnd="0" CaptionOffsetX="0" CaptionOffsetY="0" StartIntervalOffsetX="0" StartIntervalOffsetY="0" EndIntervalOffsetX="0" EndIntervalOffsetY="0" CreateRefDef="1" Invisible="0" RefDef="Matching=0\nOnDelete=3\nOnUpdate=3\n" Comments="" FKRefDefIndex_Obj_id="-1" Splitted="0" IsLinkedObject="0" IDLinkedModel="-1" Obj_id_Linked="-1" OrderPos="16" />
<RELATION ID="1366" RelationName="language_id" Kind="2" SrcTable="1165" DestTable="1168" FKFields="language_id=language_id\n" FKFieldsComments="\n" relDirection="1" MidOffset="0" OptionalStart="0" OptionalEnd="0" CaptionOffsetX="0" CaptionOffsetY="0" StartIntervalOffsetX="0" StartIntervalOffsetY="0" EndIntervalOffsetX="0" EndIntervalOffsetY="0" CreateRefDef="1" Invisible="0" RefDef="Matching=0\nOnDelete=3\nOnUpdate=3\n" Comments="" FKRefDefIndex_Obj_id="-1" Splitted="0" IsLinkedObject="0" IDLinkedModel="-1" Obj_id_Linked="-1" OrderPos="19" />
<RELATION ID="1383" RelationName="user_id" Kind="2" SrcTable="1168" DestTable="1367" FKFields="user_id=user_id\n" FKFieldsComments="\n" relDirection="3" MidOffset="4" OptionalStart="0" OptionalEnd="0" CaptionOffsetX="0" CaptionOffsetY="0" StartIntervalOffsetX="0" StartIntervalOffsetY="0" EndIntervalOffsetX="0" EndIntervalOffsetY="0" CreateRefDef="1" Invisible="0" RefDef="Matching=0\nOnDelete=3\nOnUpdate=3\n" Comments="" FKRefDefIndex_Obj_id="-1" Splitted="0" IsLinkedObject="0" IDLinkedModel="-1" Obj_id_Linked="-1" OrderPos="21" />
<RELATION ID="1392" RelationName="state_id" Kind="2" SrcTable="1384" DestTable="1367" FKFields="state_id=state_id\n" FKFieldsComments="\n" relDirection="4" MidOffset="0" OptionalStart="0" OptionalEnd="0" CaptionOffsetX="0" CaptionOffsetY="0" StartIntervalOffsetX="0" StartIntervalOffsetY="0" EndIntervalOffsetX="0" EndIntervalOffsetY="0" CreateRefDef="1" Invisible="0" RefDef="Matching=0\nOnDelete=3\nOnUpdate=3\n" Comments="" FKRefDefIndex_Obj_id="-1" Splitted="0" IsLinkedObject="0" IDLinkedModel="-1" Obj_id_Linked="-1" OrderPos="23" />
<RELATION ID="1409" RelationName="post_id" Kind="2" SrcTable="1367" DestTable="1401" FKFields="post_id=post_id\n" FKFieldsComments="\n" relDirection="2" MidOffset="0" OptionalStart="0" OptionalEnd="0" CaptionOffsetX="0" CaptionOffsetY="0" StartIntervalOffsetX="0" StartIntervalOffsetY="0" EndIntervalOffsetX="0" EndIntervalOffsetY="0" CreateRefDef="1" Invisible="0" RefDef="Matching=0\nOnDelete=3\nOnUpdate=3\n" Comments="" FKRefDefIndex_Obj_id="-1" Splitted="0" IsLinkedObject="0" IDLinkedModel="-1" Obj_id_Linked="-1" OrderPos="27" />
<RELATION ID="1410" RelationName="user_id" Kind="2" SrcTable="1168" DestTable="1401" FKFields="user_id=user_id\n" FKFieldsComments="\n" relDirection="3" MidOffset="0" OptionalStart="0" OptionalEnd="0" CaptionOffsetX="0" CaptionOffsetY="0" StartIntervalOffsetX="0" StartIntervalOffsetY="0" EndIntervalOffsetX="0" EndIntervalOffsetY="0" CreateRefDef="1" Invisible="0" RefDef="Matching=0\nOnDelete=3\nOnUpdate=3\n" Comments="" FKRefDefIndex_Obj_id="-1" Splitted="0" IsLinkedObject="0" IDLinkedModel="-1" Obj_id_Linked="-1" OrderPos="28" />
<RELATION ID="1413" RelationName="urgency_id" Kind="2" SrcTable="1393" DestTable="1367" FKFields="urgency_id=urgency_id\n" FKFieldsComments="\n" relDirection="4" MidOffset="0" OptionalStart="0" OptionalEnd="0" CaptionOffsetX="0" CaptionOffsetY="0" StartIntervalOffsetX="0" StartIntervalOffsetY="0" EndIntervalOffsetX="0" EndIntervalOffsetY="0" CreateRefDef="1" Invisible="0" RefDef="Matching=0\nOnDelete=3\nOnUpdate=3\n" Comments="" FKRefDefIndex_Obj_id="-1" Splitted="0" IsLinkedObject="0" IDLinkedModel="-1" Obj_id_Linked="-1" OrderPos="28" />
<RELATION ID="1436" RelationName="user_id" Kind="2" SrcTable="1168" DestTable="1430" FKFields="user_id=user_id\n" FKFieldsComments="\n" relDirection="4" MidOffset="0" OptionalStart="0" OptionalEnd="0" CaptionOffsetX="0" CaptionOffsetY="0" StartIntervalOffsetX="0" StartIntervalOffsetY="0" EndIntervalOffsetX="0" EndIntervalOffsetY="0" CreateRefDef="1" Invisible="0" RefDef="Matching=0\nOnDelete=3\nOnUpdate=3\n" Comments="" FKRefDefIndex_Obj_id="-1" Splitted="0" IsLinkedObject="0" IDLinkedModel="-1" Obj_id_Linked="-1" OrderPos="28" />
<RELATION ID="1437" RelationName="content_id" Kind="2" SrcTable="1162" DestTable="1430" FKFields="content_id=content_id\n" FKFieldsComments="\n" relDirection="1" MidOffset="0" OptionalStart="0" OptionalEnd="0" CaptionOffsetX="0" CaptionOffsetY="0" StartIntervalOffsetX="0" StartIntervalOffsetY="0" EndIntervalOffsetX="0" EndIntervalOffsetY="0" CreateRefDef="1" Invisible="0" RefDef="Matching=0\nOnDelete=3\nOnUpdate=3\n" Comments="" FKRefDefIndex_Obj_id="-1" Splitted="0" IsLinkedObject="0" IDLinkedModel="-1" Obj_id_Linked="-1" OrderPos="29" />
<RELATION ID="1526" RelationName="content_id" Kind="2" SrcTable="1162" DestTable="1519" FKFields="content_id=content_id\n" FKFieldsComments="\n" relDirection="2" MidOffset="0" OptionalStart="0" OptionalEnd="0" CaptionOffsetX="0" CaptionOffsetY="0" StartIntervalOffsetX="0" StartIntervalOffsetY="0" EndIntervalOffsetX="0" EndIntervalOffsetY="0" CreateRefDef="1" Invisible="0" RefDef="Matching=0\nOnDelete=3\nOnUpdate=3\n" Comments="" FKRefDefIndex_Obj_id="-1" Splitted="0" IsLinkedObject="0" IDLinkedModel="-1" Obj_id_Linked="-1" OrderPos="32" />
<RELATION ID="1527" RelationName="language_id" Kind="2" SrcTable="1165" DestTable="1519" FKFields="language_id=language_id\n" FKFieldsComments="\n" relDirection="4" MidOffset="18" OptionalStart="0" OptionalEnd="0" CaptionOffsetX="0" CaptionOffsetY="0" StartIntervalOffsetX="0" StartIntervalOffsetY="0" EndIntervalOffsetX="0" EndIntervalOffsetY="0" CreateRefDef="1" Invisible="0" RefDef="Matching=0\nOnDelete=3\nOnUpdate=3\n" Comments="" FKRefDefIndex_Obj_id="-1" Splitted="0" IsLinkedObject="0" IDLinkedModel="-1" Obj_id_Linked="-1" OrderPos="33" />
<RELATION ID="1535" RelationName="content_id" Kind="2" SrcTable="1162" DestTable="1528" FKFields="content_id=content_id\n" FKFieldsComments="\n" relDirection="2" MidOffset="-15" OptionalStart="0" OptionalEnd="0" CaptionOffsetX="0" CaptionOffsetY="0" StartIntervalOffsetX="0" StartIntervalOffsetY="0" EndIntervalOffsetX="0" EndIntervalOffsetY="0" CreateRefDef="1" Invisible="0" RefDef="Matching=0\nOnDelete=3\nOnUpdate=3\n" Comments="" FKRefDefIndex_Obj_id="-1" Splitted="0" IsLinkedObject="0" IDLinkedModel="-1" Obj_id_Linked="-1" OrderPos="35" />
<RELATION ID="1536" RelationName="language_id" Kind="2" SrcTable="1165" DestTable="1528" FKFields="language_id=language_id\n" FKFieldsComments="\n" relDirection="4" MidOffset="14" OptionalStart="0" OptionalEnd="0" CaptionOffsetX="28" CaptionOffsetY="-48" StartIntervalOffsetX="0" StartIntervalOffsetY="0" EndIntervalOffsetX="0" EndIntervalOffsetY="0" CreateRefDef="1" Invisible="0" RefDef="Matching=0\nOnDelete=3\nOnUpdate=3\n" Comments="" FKRefDefIndex_Obj_id="-1" Splitted="0" IsLinkedObject="0" IDLinkedModel="-1" Obj_id_Linked="-1" OrderPos="36" />
<RELATION ID="1547" RelationName="component_id" Kind="2" SrcTable="1528" DestTable="1537" FKFields="component_id=component_id\n" FKFieldsComments="\n" relDirection="2" MidOffset="-4" OptionalStart="0" OptionalEnd="0" CaptionOffsetX="0" CaptionOffsetY="0" StartIntervalOffsetX="0" StartIntervalOffsetY="0" EndIntervalOffsetX="0" EndIntervalOffsetY="0" CreateRefDef="1" Invisible="0" RefDef="Matching=0\nOnDelete=3\nOnUpdate=3\n" Comments="" FKRefDefIndex_Obj_id="-1" Splitted="0" IsLinkedObject="0" IDLinkedModel="-1" Obj_id_Linked="-1" OrderPos="38" />
<RELATION ID="1572" RelationName="content_id" Kind="2" SrcTable="1162" DestTable="1565" FKFields="content_id=content_id\n" FKFieldsComments="\n" relDirection="3" MidOffset="110" OptionalStart="0" OptionalEnd="0" CaptionOffsetX="0" CaptionOffsetY="0" StartIntervalOffsetX="0" StartIntervalOffsetY="0" EndIntervalOffsetX="0" EndIntervalOffsetY="0" CreateRefDef="1" Invisible="0" RefDef="Matching=0\nOnDelete=3\nOnUpdate=3\n" Comments="" FKRefDefIndex_Obj_id="-1" Splitted="0" IsLinkedObject="0" IDLinkedModel="-1" Obj_id_Linked="-1" OrderPos="40" />
<RELATION ID="1573" RelationName="language_id" Kind="2" SrcTable="1165" DestTable="1565" FKFields="language_id=language_id\n" FKFieldsComments="\n" relDirection="3" MidOffset="236" OptionalStart="0" OptionalEnd="0" CaptionOffsetX="0" CaptionOffsetY="0" StartIntervalOffsetX="0" StartIntervalOffsetY="0" EndIntervalOffsetX="0" EndIntervalOffsetY="0" CreateRefDef="1" Invisible="0" RefDef="Matching=0\nOnDelete=3\nOnUpdate=3\n" Comments="" FKRefDefIndex_Obj_id="-1" Splitted="0" IsLinkedObject="0" IDLinkedModel="-1" Obj_id_Linked="-1" OrderPos="41" />
<RELATION ID="1575" RelationName="component_id" Kind="2" SrcTable="1565" DestTable="1367" FKFields="component_id=component_id\n" FKFieldsComments="\n" relDirection="2" MidOffset="0" OptionalStart="0" OptionalEnd="0" CaptionOffsetX="0" CaptionOffsetY="0" StartIntervalOffsetX="0" StartIntervalOffsetY="0" EndIntervalOffsetX="0" EndIntervalOffsetY="0" CreateRefDef="1" Invisible="0" RefDef="Matching=0\nOnDelete=3\nOnUpdate=3\n" Comments="" FKRefDefIndex_Obj_id="-1" Splitted="0" IsLinkedObject="0" IDLinkedModel="-1" Obj_id_Linked="-1" OrderPos="41" />
</RELATIONS>
<NOTES>
</NOTES>
<IMAGES>
</IMAGES>
</METADATA>
<PLUGINDATA>
<PLUGINDATARECORDS>
</PLUGINDATARECORDS>
</PLUGINDATA>
<QUERYDATA>
<QUERYRECORDS>
</QUERYRECORDS>
</QUERYDATA>
<LINKEDMODELS>
</LINKEDMODELS>
</DBMODEL>
