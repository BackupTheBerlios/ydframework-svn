<html>

<head>

    <title><?= $YD_FW_NAMEVERS ?></title>

    <script language="JavaScript">

        function addItem( item ) {
            window.opener.AddText( '<?= $YD_GET['field'] ?>', '[<?= $YD_GET['tag'] ?>]', item, '[/<?= $YD_GET['tag'] ?>]' );
            window.close()
            return false;
        }

    </script>

</head>

<body>

    <h3>Select image for <?= $YD_GET['field'] ?></h3>
    
    <p>Tag: <?= $YD_GET['tag'] ?></p>

    <?php if ( sizeof( $items ) > 0 ) { ?>
    
        <?php foreach ( $items as $item ) { ?>

            <a href="javascript:void( addItem( '<?= addslashes( $item->getBasename() ) ?>' ) )"><?= $item->getBasename() ?></a><br>

        <?php } ?>

    <?php } else { ?>

        <p>No item(s) available yet.</p>

    <?php } ?>

</body>

</html>
