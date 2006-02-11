<html>

<head>

    <title><?= YD_FW_NAMEVERS ?></title>

</head>

<body>

    <?= YD_FW_NAMEVERS ?>

    <p><b>Using an object</b></p>
    <ul>
        <li><?= $browser->agent ?></li>
        <li><?= $browser->browser ?></li>
        <li><?= $browser->version ?></li>
        <li><?= $browser->platform ?></li>
        <li><?= YDDebugUtil::dump( $browser->dotnet ) ?></li>
        <li><?= YDDebugUtil::dump( $browser->getBrowserLanguages() ) ?></li>
        <li><?= $browser->getLanguage() ?></li>
    </ul>

</body>

</html>
