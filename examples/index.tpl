<html>

<head>

	<title>[$YD_FW_NAMEVERS]</title>

</head>

<body>

	[if $file]

		<h3>source code: [$file]</h3>
		[$source]
		<p><a href="[$YD_SELF_SCRIPT]">go back</a></p>
	
	[else]
	
		<h3>[$YD_FW_NAMEVERS]</h3>

		<p>Welcome to the [$YD_FW_NAMEVERS]!</p>

		<p>&nbsp;<br><b>Samples</b></p>

		<table border="1" width="650" cellpadding="3"  cellspacing="0">
		<tr>
			<td><b>Sample</b></td>
			<td width="40%"><b>Source</b></td>
		</tr>
		<tr>
			<td valign="top">
				<a href="phpinfo.php">Showing the PHP information</a>
			</td>
			<td valign="top">
				<a href="[$YD_SELF_SCRIPT]?do=source&id=phpinfo.php">phpinfo.php</a>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<a href="browserinfo.php">Showing the browser information</a>
			</td>
			<td valign="top">
				<a href="[$YD_SELF_SCRIPT]?do=source&id=browserinfo.php">browserinfo.php</a>
				<br>
				<a href="[$YD_SELF_SCRIPT]?do=source&id=browserinfo.tpl">browserinfo.tpl</a>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<a href="arrayutil.php">Array utilities</a>
			</td>
			<td valign="top">
				<a href="[$YD_SELF_SCRIPT]?do=source&id=arrayutil.php">arrayutil.php</a>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<a href="fsfile.php">YDFSFile object</a>
			</td>
			<td valign="top">
				<a href="[$YD_SELF_SCRIPT]?do=source&id=fsfile.php">fsfile.php</a>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<a href="fsdirectory.php">YDFSDirectory object</a>
			</td>
			<td valign="top">
				<a href="[$YD_SELF_SCRIPT]?do=source&id=fsdirectory.php">fsdirectory.php</a>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<a href="fsimage.php">YDFSImage object</a>
				<br>
				<a href="fsimage.php?do=thumbnail1">YDFSImage object - thumbnail 1</a>
				<br>
				<a href="fsimage.php?do=thumbnail2">YDFSImage object - thumbnail 2</a>
				<br>
				<a href="fsimage.php?do=thumbnail3">YDFSImage object - thumbnail 3 (no caching)</a>
				<br>
				<a href="fsimage.php?do=thumbnailsave">YDFSImage object - thumbnail saving</a>
			</td>
			<td valign="top">
				<a href="[$YD_SELF_SCRIPT]?do=source&id=fsimage.php">fsimage.php</a>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<a href="extending.php">Extending the YDRequest base class</a>
			</td>
			<td valign="top">
				<a href="[$YD_SELF_SCRIPT]?do=source&id=extending.php">extending.php</a>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<a href="database.php">Database connectivity - Native functions</a>
				<br>
				<a href="database1.php">Database connectivity - YD MySQL library</a>
				<br>
				<a href="database2.php">Database connectivity - YD SQLite library</a>
				<br>
				<a href="database3.php">Database connectivity - YD Oracle library</a>
				<br>
				<a href="database4.php">Database connectivity - YD PostgreSQL library</a>
			</td>
			<td valign="top">
				<a href="[$YD_SELF_SCRIPT]?do=source&id=database.php">database.php</a>
				<br>
				<a href="[$YD_SELF_SCRIPT]?do=source&id=database.tpl">database.tpl</a>
				<br>
				<a href="[$YD_SELF_SCRIPT]?do=source&id=database1.php">database1.php</a>
				<br>
				<a href="[$YD_SELF_SCRIPT]?do=source&id=database1.tpl">database1.tpl</a>
				<br>
				<a href="[$YD_SELF_SCRIPT]?do=source&id=database2.php">database2.php</a>
				<br>
				<a href="[$YD_SELF_SCRIPT]?do=source&id=database3.php">database3.php</a>
				<br>
				<a href="[$YD_SELF_SCRIPT]?do=source&id=database4.php">database4.php</a>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<a href="form.php">Form handling and validation</a>
			</td>
			<td valign="top">
				<a href="[$YD_SELF_SCRIPT]?do=source&id=form.php">form.php</a>
				<br>
				<a href="[$YD_SELF_SCRIPT]?do=source&id=form.tpl">form.tpl</a>
				<br>
				<a href="[$YD_SELF_SCRIPT]?do=source&id=form_selector.tpl">form_selector.tpl</a>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<a href="form2.php">Form handling and validation (new style)</a>
			</td>
			<td valign="top">
				<a href="[$YD_SELF_SCRIPT]?do=source&id=form2.php">form.php</a>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<a href="sample.php">Defining and using action requests</a>
			</td>
			<td valign="top">
				<a href="[$YD_SELF_SCRIPT]?do=source&id=sample.php">sample.php</a>
				<br>
				<a href="[$YD_SELF_SCRIPT]?do=source&id=sample.tpl">sample.tpl</a>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<a href="url.php">YDUrl object</a>
				<br>
				<a href="url.php?do=image1">YDUrl object - Image 1</a>
				<br>
				<a href="url.php?do=headers">YDUrl object - Headers</a>
				<br>
				<a href="url.php?do=status">YDUrl object - Status</a>
			</td>
			<td valign="top">
				<a href="[$YD_SELF_SCRIPT]?do=source&id=url.php">url.php</a>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<a href="feedcreator.php">YDFeedCreator object</a>
				<br>
				<a href="feedcreator.php?do=rss091">YDFeedCreator object - RSS 0.91 output</a>
				<br>
				<a href="feedcreator.php?do=rss10">YDFeedCreator object - RSS 1.0 output</a>
				<br>
				<a href="feedcreator.php?do=rss20">YDFeedCreator object - RSS 2.0 output</a>
				<br>
				<a href="feedcreator.php?do=atom">YDFeedCreator object - Atom output</a>
			</td>
			<td valign="top">
				<a href="[$YD_SELF_SCRIPT]?do=source&id=feedcreator.php">feedcreator.php</a>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<a href="login/index.php">Using authentication</a>
			</td>
			<td valign="top">
				<a href="[$YD_SELF_SCRIPT]?do=source&id=login/includes/MyLoginRequest.php">login/includes/MyLoginRequest.php</a>
				<br>
				<a href="[$YD_SELF_SCRIPT]?do=source&id=login/index.php">login/index.php</a>
				<br>
				<a href="[$YD_SELF_SCRIPT]?do=source&id=login/index.tpl">login/index.tpl</a>
				<br>
				<a href="[$YD_SELF_SCRIPT]?do=source&id=login/login.tpl">login/login.tpl</a>
				<br>
				<a href="[$YD_SELF_SCRIPT]?do=source&id=login/userinfo.php">login/userinfo.php</a>
				<br>
				<a href="[$YD_SELF_SCRIPT]?do=source&id=login/userinfo.tpl">login/userinfo.tpl</a>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<a href="auth_ipcheck.php">Authentication based on IP numbers</a>
			</td>
			<td valign="top">
				<a href="[$YD_SELF_SCRIPT]?do=source&id=auth_ipcheck.php">auth_ipcheck.php</a>
				<br>
				<a href="[$YD_SELF_SCRIPT]?do=source&id=auth_ipcheck.tpl">auth_ipcheck.tpl</a>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<a href="http_auth/index.php">Basic HTTP Authentication</a>
			</td>
			<td valign="top">
				<a href="[$YD_SELF_SCRIPT]?do=source&id=http_auth/includes/MyLoginRequest.php">http_auth/includes/MyLoginRequest.php</a>
				<br>
				<a href="[$YD_SELF_SCRIPT]?do=source&id=http_auth/index.php">http_auth/index.php</a>
				<br>
				<a href="[$YD_SELF_SCRIPT]?do=source&id=http_auth/index.tpl">http_auth/index.tpl</a>
				<br>
				<a href="[$YD_SELF_SCRIPT]?do=source&id=http_auth/userinfo.php">http_auth/userinfo.php</a>
				<br>
				<a href="[$YD_SELF_SCRIPT]?do=source&id=http_auth/userinfo.tpl">http_auth/userinfo.tpl</a>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<a href="xmlrpcclient.php">XML/RPC client</a>
			</td>
			<td valign="top">
				<a href="[$YD_SELF_SCRIPT]?do=source&id=xmlrpcclient.php">xmlrpcclient.php</a>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<a href="xmlrpcserver.php">XML/RPC server</a>
			</td>
			<td valign="top">
				<a href="[$YD_SELF_SCRIPT]?do=source&id=xmlrpcserver.php">xmlrpcserver.php</a>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<a href="language.php">Language negotiator</a>
			</td>
			<td valign="top">
				<a href="[$YD_SELF_SCRIPT]?do=source&id=language1.php">language.php</a>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<a href="email.php">Sending emails</a>
			</td>
			<td valign="top">
				<a href="[$YD_SELF_SCRIPT]?do=source&id=email.php">email.php</a>
				<br>
				<a href="[$YD_SELF_SCRIPT]?do=source&id=email.tpl">email.tpl</a>
				<br>
				<a href="[$YD_SELF_SCRIPT]?do=source&id=email_template.tpl">email_template.tpl</a>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<a href="bbcode.php">BBCode conversion</a>
			</td>
			<td valign="top">
				<a href="[$YD_SELF_SCRIPT]?do=source&id=bbcode.php">bbcode.php</a>
				<br/>
				<a href="[$YD_SELF_SCRIPT]?do=source&id=bbcode.txt">bbcode.txt</a>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<a href="fileupload.php">Handling file uploads</a>
			</td>
			<td valign="top">
				<a href="[$YD_SELF_SCRIPT]?do=source&id=fileupload.php">fileupload.php</a>
				<br/>
				<a href="[$YD_SELF_SCRIPT]?do=source&id=fileupload.tpl">fileupload.tpl</a>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<a href="constants.php">YDFramework2 constants</a>
			</td>
			<td valign="top">
				<a href="[$YD_SELF_SCRIPT]?do=source&id=constants.php">constants.php</a>
				<br/>
				<a href="[$YD_SELF_SCRIPT]?do=source&id=constants.tpl">constants.tpl</a>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<a href="pbase.php">Combining YDUrl and YDArrayUtil</a>
			</td>
			<td valign="top">
				<a href="[$YD_SELF_SCRIPT]?do=source&id=pbase.php">pbase.php</a>
				<br/>
				<a href="[$YD_SELF_SCRIPT]?do=source&id=pbase.tpl">pbase.tpl</a>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<a href="firstapp/index.php">Tutorial application</a>
			</td>
			<td valign="top">
				<a href="[$YD_SELF_SCRIPT]?do=source&id=firstapp/index.php">firstapp/index.php</a>
				<br/>
				<a href="[$YD_SELF_SCRIPT]?do=source&id=firstapp/index.tpl">firstapp/index.tpl</a>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<a href="database/index.php">Tutorial application using a database</a>
			</td>
			<td valign="top">
				<a href="[$YD_SELF_SCRIPT]?do=source&id=database/index.php">database/index.php</a>
				<br/>
				<a href="[$YD_SELF_SCRIPT]?do=source&id=database/index.tpl">database/index.tpl</a>
			</td>
		</tr>
		</table>

		<p>&nbsp;<br><b>Documentation</b></p>

		<p>View the <a href="../doc/api/index.html">API documentation</a> or read the
		<a href="../doc/userguide/index.html">user guide</a>.</p>
	
	[/if]

</body>

</html>
