<html>

<head>

    <title><?= $YD_FW_NAMEVERS ?></title>

</head>

<body>

    <h3>YDEmail test page</h3>

    <?php if ( $formValid ) { ?>

        <p>The email to <b><?= $form['email']['value'] ?></b> was sent
        successfully!</p>

    <?php } else { ?>
    
        <?php if ( $form['errors'] ) { ?>
            <p style="color: red"><b>Errors during processing:</b>
            <?php foreach ( $form['errors'] as $error ) { ?>
                <br><?= $error ?>
            <?php } ?>
            </p>
        <?php } ?>

        <form <?= $form['attributes'] ?>>
            <p>
                <?= $form['email']['label'] ?>
                <br>
                <?= $form['email']['html'] ?>
            </p>
            <p>
                <?= $form['cmdSubmit']['html'] ?>
            </p>
        </form>

    <?php } ?>

    <p>[
        <a href="<?= $YD_SELF_SCRIPT ?>">try again</a>
        |
        <a href="index.php">other samples</a>
    ]</p>

</body>

</html>
