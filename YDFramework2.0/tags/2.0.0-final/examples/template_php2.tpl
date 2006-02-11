<html>

<head>

    <title><?php echo YD_FW_NAMEVERS; ?></title>

</head>

<body>

    <?php echo YD_FW_NAMEVERS; ?>

    <p><b>Using an object</b></p>
    <ul>
        <li><?php echo $browser->agent; ?></li>
        <li><?php echo $browser->browser; ?></li>
        <li><?php echo $browser->version; ?></li>
        <li><?php echo $browser->platform; ?></li>
        <li><?php echo YDDebugUtil::dump( $browser->dotnet ); ?></li>
        <li><?php echo YDDebugUtil::dump( $browser->getBrowserLanguages() ); ?></li>
        <li><?php echo $browser->getLanguage(); ?></li>
    </ul>

</body>

</html>