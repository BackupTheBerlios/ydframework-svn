<html>

<head>

	<title><?= $YD_FW_NAMEVERS ?></title>

</head>

<body>

	Agent: <?= $browser->getAgent() ?><br>
	Browser: <?= $browser->getBrowser() ?><br>
	Version: <?= $browser->getVersion() ?><br>
	Platform: <?= $browser->getPlatform() ?><br>
	.NET runtimes: <?= implode( ', ', $browser->getDotNetRuntimes() ); ?><br>
	Languages: <?= implode( ', ', $browser->getBrowserLanguages() ); ?>

	<p>[ <a href="index.php">other samples</a> ]</p>

</body>

</html>
