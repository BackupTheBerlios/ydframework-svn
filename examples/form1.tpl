<html>

<head>

    <title><?= $YD_FW_NAMEVERS ?></title>

    <style>
        .bbtextarea {
            width: 650px;
            height: 240px;
        }
        a {
            color: darkred;
            text-decoration: none;
        }
        .bbtoolbar {
            background-color: #DEDEDE;
            padding: 2px;
        }
    </style>

</head>

<body>

    <h3><?= $title ?></h3>

    <?php if ( $formValid ) { ?>

        <p>Welcome to <b><?= $form['name']['value'] ?></b>!</p>

        <p>Description: <?= $form['desc']['value_html'] ?></p>
        <p>Description2: <?= $form['desc2']['value_html'] ?></p>

        <p>Date <?= $form['date']['value']['d'] ?>/<?= $form['date']['value']['M'] ?>/<?= $form['date']['value']['Y'] ?></p>

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
                <?= $form['desc2']['label'] ?>
                <br>
                <?= $form['desc2']['html'] ?>
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
