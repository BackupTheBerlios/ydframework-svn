<html>

<head>

    <title><?= $YD_FW_NAMEVERS ?></title>

</head>

<body>

    <?php if ( $YD_ACTION == 'default' ) { ?>

        <h3>Entry list</h3>

        <p><a href="<?= $YD_SELF_SCRIPT ?>?do=AddEntry">Add a new entry</a></p>

        <?php if ( $entries ) { ?>

            <?php foreach ( $entries as $entry ) { ?>
                <p>
                <b><?= $entry['title'] ?></b>
                [ <a href="<?= $YD_SELF_SCRIPT ?>?do=DeleteEntry&id=<?= $entry['id'] ?>">delete</a> ]
                <br>
                <?= $entry['body'] ?>
                </p>
            <?php } ?>

        <?php } else { ?>
            <p>No entries were found.</p>
        <?php } ?>

    <?php } ?>

    <?php if ( $YD_ACTION == 'addentry' ) { ?>

        <h3>Add entry</h3>

        <?php if ( $form['errors'] ) { ?>
            <p style="color: red"><b>Errors during processing:</b>
            <?php foreach ( $form['errors'] as $error ) { ?>
                <br><?= $error ?>
            <?php } ?>
            </p>
        <?php } ?>

        <form <?= $form['attributes'] ?>>
            <p>
                <?= $form['title']['label'] ?>
                <br>
                <?= $form['title']['html'] ?>
            </p>
            <p>
                <?= $form['body']['label'] ?>
                <br>
                <?= $form['body']['html'] ?>
            </p>
            <p>
                <?= $form['cmdSubmit']['html'] ?>
            </p>
        </form>

    <?php } ?>

</body>

</html>
