<html>

<head>

    <title><?= $YD_FW_NAMEVERS ?></title>

</head>

<body>

    <h3><?= $url ?></h3>

    <table cellspacing="16" border="0">
    <?php foreach ( $images as $imagerow ) { ?>
        <tr>
            <?php foreach( $imagerow as $image ) { ?>
                <td width="160" align="center">
                <?php if ( $image ) { ?>
                    <a href="<?= $YD_SELF_SCRIPT ?>?do=show&id=<?= $image ?>" target="_blank"><img src="http://www.pbase.com/image/<?= $image ?>/small.jpg" border="1"></a>
                    <a href="<?= $YD_SELF_SCRIPT ?>?do=show&id=<?= $image ?>" target="_blank"><?= $image ?>.jpg</a>
                <?php } ?>
                </td>
            <?php } ?>
        </tr>
    <? } ?>
    </table>

    <p>[
        <a href="index.php">other samples</a>
    ]</p>

</body>

</html>
