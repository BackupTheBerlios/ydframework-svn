<html>

<head>

    <title><?= $YD_FW_NAMEVERS ?></title>

</head>

<body>
    
    <h3>XML/RPC Interface</h3>

    <p>URL: <a href="<?= $xmlRpcUrl ?>"><?= $xmlRpcUrl ?></a></p>

    <p>&nbsp;<br><b>Methods</b></p>

    <table border="0" width="100%" cellpadding="3"  cellspacing="2">
        <?php foreach ( $methods as $method=>$methodInfo ) { ?>
        <tr bgcolor="lightyellow">
            <td><b><?= $method ?></b></td>
            <td>
                Input parameters:
                <?php if ( $methodInfo['paramsIn'] ) { ?>
                    <?= strtolower( implode( ' ', $methodInfo['paramsIn'] ) ) ?>
                <?php } else { ?>
                    none
                <?php } ?>
            </td>
            <td>
                Returns: 
                <?php if ( $methodInfo['paramsOut'] ) { ?>
                    <?= strtolower( $methodInfo['paramsOut'] ) ?>
                <?php } else { ?>
                    none
                <?php } ?>
            </td>
        </tr>
        <?php if ( $methodInfo['help'] ) { ?>
            <tr>
                <td colspan="3">
                    <?= $methodInfo['help'] ?>
                </td>
            </tr>
        <?php } ?>
    <?php } ?>
    </table>

</body>

</html>
