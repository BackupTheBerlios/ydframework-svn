' Yellow Duck Framework version 2.0
' Copyright (C) (c) copyright 2004 Pieter Claerhout
' 
' This library is free software; you can redistribute it and/or
' modify it under the terms of the GNU Lesser General Public
' License as published by the Free Software Foundation; either
' version 2.1 of the License, or (at your option) any later version.
' 
' This library is distributed in the hope that it will be useful,
' but WITHOUT ANY WARRANTY; without even the implied warranty of
' MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
' Lesser General Public License for more details.
' 
' You should have received a copy of the GNU Lesser General Public
' License along with this library; if not, write to the Free Software
' Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA

' Check the command line arguments
if WScript.Arguments.length > 0 then

	' The file references
	file = WScript.Arguments.Item( 0 )

	' Create the filesystemobject
	Set FSO = CreateObject("Scripting.FileSystemObject")

	' Read the data from the file
	Set inObj = FSO.OpenTextFile( file, 1 )
	data = inObj.ReadAll
	inObj.Close

	' Fix the data
	data = Replace( data, "font-size=""24.8832pt""", "font-size=""18pt""" )
	data = Replace( data, "font-size=""18.6624pt""", "font-size=""14pt""" )

	' Write the new output data
	Set outObj = FSO.CreateTextFile( file )
	outObj.write( data )
	outObj.close()

else

	' No arguments
	WScript.Echo( "No arguments specified." )

end if
