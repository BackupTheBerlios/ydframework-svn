<html>

<head>

    <title><?= $YD_FW_NAMEVERS ?></title>

</head>

<body>

    <?php if ( $YD_ACTION == 'test' ) { ?>

        <h3>Testing <?php $YD_GET['id'] ?></h3>

        <?php if ( $error ) { ?>
            <p style="color: red"><b>ERROR: <?= $error ?></b></p>
        <?php } else { ?>

            <p>Connected succesfully to database alias <b><?= $YD_GET['id'] ?></b>!</p>

            <?php if ( $processList ) { ?>
                <p><b>show processlist</b></p>
                <p><table border="1" cellspacing="0" cellpadding="4">
                <tr>
                    <td><b>id</b></td>
                    <td><b>user</b></td>
                    <td><b>host</b></td>
                    <td><b>db</b></td>
                    <td><b>command</b></td>
                    <td><b>time</b></td>
                    <td><b>state</b></td>
                    <td><b>info</b></td>
                </tr>
                <?php foreach ( $processList as $row ) { ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['user'] ?></td>
                        <td><?= $row['host'] ?></td>
                        <td><?= $row['db'] ?>&nbsp;</td>
                        <td><?= $row['command'] ?></td>
                        <td><?= $row['time'] ?></td>
                        <td><?= $row['state'] ?>&nbsp;</td>
                        <td><?= $row['info'] ?>&nbsp;</td>
                    </tr>
                <?php } ?>
                </table></p>
            <?php } ?>

            <?php if ( $status ) { ?>
                <p><b>show status</b></p>
                <p><table border="1" cellspacing="0" cellpadding="4">
                <tr>
                    <td><b>name</b></td>
                    <td><b>value</b></td>
                </tr>
                <?php foreach ( $status as $row ) { ?>
                    <tr>
                        <td><?= $row['variable_name'] ?></td>
                        <td><?= $row['value'] ?>&nbsp;</td>
                    </tr>
                <?php } ?>
                </table></p>
            <?php } ?>

            <?php if ( $variables ) { ?>
                <p><b>show variables</b></p>
                <p><table border="1" cellspacing="0" cellpadding="4">
                <tr>
                    <td><b>name</b></td>
                    <td><b>value</b></td>
                </tr>
                <?php foreach ( $variables as $row ) { ?>
                    <tr>
                        <td><?= $row['variable_name'] ?></td>
                        <td><?= $row['value'] ?>&nbsp;</td>
                    </tr>
                <?php } ?>
                </table></p>
            <?php } ?>

        <?php } ?>

        <p>
            Number of database connections: <?= $YD_DB_CONN_CNT ?>
            <br>
            Number of database queries: <?= $YD_DB_SQLQ_CNT ?>
        </p>

        <p>[
            <a href="<?= $YD_SELF_SCRIPT ?>">go back</a>
            |
            <a href="index.php">other samples</a>
        ]</p>

    <?php } else { ?>

        <h3>Database aliasses</h3>

        <?php if ( $dbAliasses ) { ?>
            <table border="1" width="650" cellpadding="3"  cellspacing="0">
            <tr>
                <td width="200"><b>Alias</b></td>
                <td width="400"><b>Database URL</b></td>
                <td width="50"><b>Actions</b></td>
            </tr>
            <?php foreach ( $dbAliasses as $dbAlias => $dbUrl ) { ?>
            <tr>
                <td><?= $dbAlias ?></td>
                <td><?= $dbUrl ?></td>
                <td>[ <a href="<?= $YD_SELF_SCRIPT ?>?<?= $YD_ACTION_PARAM ?>=test&id=<?= $dbAlias ?>">test</a> ]</td>
            </tr>
            <?php } ?>
            </table>
        <?php } else { ?>
            <p>No database aliasses defined.</p>
        <?php } ?>

        <p>[
            <a href="<?= $YD_SELF_SCRIPT ?>">refresh</a>
            |
            <a href="index.php">other samples</a>
        ]</p>

    <?php } ?>

</body>

</html>
