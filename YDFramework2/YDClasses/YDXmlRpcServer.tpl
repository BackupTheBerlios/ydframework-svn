<html>

<head>

    <title><?= $YD_FW_NAMEVERS ?></title>

</head>

<body>
    
    <h3>XML/RPC Interface</h3>

    <p>URL: <a href="<?= $xmlRpcUrl ?>"><?= $xmlRpcUrl ?></a></p>

    <p>&nbsp;<br><b>XML-RPC interface methods</b></p>

    <table border="0" width="100%" cellpadding="3"  cellspacing="0">
        <?php $bgcolor=true ?>
        <?php foreach ( $methods as $method=>$methodInfo ) { ?>
            <?php if ( $bgcolor ) { ?>
                <tr bgcolor="<?= $rowcolor ?>">
            <?php } else { ?>
                <tr>
            <?php } ?>
                <td><b>
                    <?php if ( $methodInfo['paramsIn'] ) { ?>
                        <?= $method ?>(
                        <?= strtolower( implode( ' ', $methodInfo['paramsIn'] ) ) ?>
                        )
                    <?php } else { ?>
                        <?= $method ?>()
                    <?php } ?>
                </b></td>
                <td align="right">
                    returns: 
                    <?php if ( $methodInfo['paramsOut'] ) { ?>
                        <?= strtolower( $methodInfo['paramsOut'] ) ?>
                    <?php } else { ?>
                        none
                    <?php } ?>
                </td>
            </tr>
            <?php if ( $bgcolor ) { ?>
                <tr bgcolor="<?= $rowcolor ?>">
            <?php } else { ?>
                <tr>
            <?php } ?>
                <td colspan="2">
                    <?php if ( $methodInfo['help'] ) { ?>
                        <?= $methodInfo['help'] ?>
                    <?php } else { ?>
                        No help available.
                    <?php } ?>
                </td>
            </tr>
            <?php
                if ( $bgcolor ) {
                    $bgcolor = false;
                } else {
                    $bgcolor = true;
                } 
            ?>
        <?php } ?>
        <?php if ( $bgcolor ) { ?>
            <tr bgcolor="<?= $rowcolor ?>"><td height="1" colspan="2"></td></tr>
        <?php } ?>
    </table>

    <p>&nbsp;<br><b>XML-RPC interface capabilities</b></p>

    <table border="0" width="100%" cellpadding="3"  cellspacing="0">
        <?php $bgcolor=true ?>
        <?php foreach ( $capabilities as $key=>$info ) { ?>
            <?php if ( $bgcolor ) { ?>
                <tr bgcolor="<?= $rowcolor ?>">
            <?php } else { ?>
                <tr>
            <?php } ?>
                <td><?= $key ?></td>
                <td><a href="<?= $info['specUrl'] ?>" target="_blank"><?= $info['specUrl'] ?></a></td>
                <td align="right">version <?= $info['specVersion'] ?></td>
            </tr>
            <?php
                if ( $bgcolor ) {
                    $bgcolor = false;
                } else {
                    $bgcolor = true;
                } 
            ?>
        <?php } ?>
        <?php if ( $bgcolor ) { ?>
                <tr bgcolor="<?= $rowcolor ?>"><td height="1" colspan="3"></td></tr>
        <?php } ?>
    </table>

</body>

</html>
