<html>

<head>

	<title>[$YD_FW_NAMEVERS]</title>

</head>

<body>

	Agent: [$browser->getAgent()]<br>
	Browser: [$browser->getBrowser()]<br>
	Version: [$browser->getVersion()]<br>
	Platform: [$browser->getPlatform()]<br>
	.NET runtimes: [$browser->getDotNetRuntimes()|@implode]<br>
	Languages: [$browser->getBrowserLanguages()|@implode]

	<p><a href="index.php">other samples</a></p>

</body>

</html>
