<html>

<head>

    <title><?= $YD_FW_NAMEVERS ?></title>

</head>

<body>

    <?php if ( $YD_ACTION == 'default' ) { ?>
        <h3><?= $url ?></h3>
        <p><a href="<?= $YD_SELF_SCRIPT ?>?do=rss">image list as RSS</a></p>
        <table cellspacing="16" border="0">
        <?php foreach ( $images as $imagerow ) { ?>
            <tr>
                <?php foreach( $imagerow as $image ) { ?>
                    <td width="160" align="center">
                    <?php if ( $image ) { ?>
                        <a href="<?= $YD_SELF_SCRIPT ?>?do=ShowImage&id=<?= $image ?>" target="_blank"><img src="http://www.pbase.com/image/<?= $image ?>/small.jpg" border="1"></a>
                        <a href="<?= $YD_SELF_SCRIPT ?>?do=ShowImage&id=<?= $image ?>" target="_blank"><?= $image ?>.jpg</a>
                    <?php } ?>
                    </td>
                <?php } ?>
            </tr>
        <? } ?>
        </table>
    <?php } ?>

    <?php if ( $YD_ACTION == 'showimage' ) { ?>

        <h3><?= $imageCurrent ?>.jpg</h3>

        <p>[
            <?php if ( $imagePrevious ) { ?>
                <a href="<?= $YD_SELF_SCRIPT ?>?do=ShowImage&id=<?= $imagePrevious ?>">previous</a>
            <?php } else { ?>
                previous
            <?php } ?>
            |
            <a href="<?= $YD_SELF_SCRIPT ?>">overview</a>
            |
            <?php if ( $imageNext ) { ?>
                <a href="<?= $YD_SELF_SCRIPT ?>?do=ShowImage&id=<?= $imageNext ?>">next</a>
            <?php } else { ?>
                next
            <?php } ?>
        ]</p>

        <p><img src="http://www.pbase.com/image/<?= $imageCurrent ?>.jpg" border="1"></p>

    <?php } ?>

    <p>[
        <a href="index.php">other samples</a>
    ]</p>

</body>

</html>
