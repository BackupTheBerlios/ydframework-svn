<html>

<head>

    <title><?= $YD_FW_NAMEVERS ?></title>

</head>

<body>

    <h3><?= $title ?></h3>

    <?php if ( $formValid ) { ?>
    
        <p>Welcome to <b><?= $form['name']['value'] ?></b>!</p>

        <p>Description: <?= $form['desc']['value'] ?></p>

        <p>Date <?= $form['date']['value']['d'][0] ?>/<?= $form['date']['value']['M'][0] ?>/<?= $form['date']['value']['Y'][0] ?></p>

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
                <?= $form['name']['label'] ?>
                <br>
                <?= $form['name']['html'] ?>
            </p>
            <p>
                <?= $form['desc']['label'] ?>
                <br>
                <?= $form['desc']['html'] ?>
            </p>
            <p>
                <?= $form['date']['label'] ?>
                <br>
                <?= $form['date']['html'] ?>
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
