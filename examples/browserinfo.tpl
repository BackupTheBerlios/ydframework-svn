<html>

<head>

    <title><?= $YD_FW_NAMEVERS ?></title>

</head>

<body>

    <h3><?= $title ?></h3>

    Agent: <?= $browser->getAgent() ?>
    <br>
    Browser: <?= $browser->getBrowser() ?>
    <br>
    Version: <?= $browser->getVersion() ?>
    <br>
    Platform: <?= $browser->getPlatform() ?>
    <br>
    Supported .NET runtimes: <?= implode( ', ', $browser->getDotNetRuntimes() ); ?>
    <br>
    Supported languages: <?= implode( ', ', $browser->getBrowserLanguages() ); ?>

    <p>[
        <a href="index.php">other samples</a>
    ]</p>

</body>

</html>
