<h2><?php echo( $node->getTranslatedValue( 'title' ) ); ?></h2>

<p>Hello world from template.</p>

<?php if ( sizeof( $languages ) > 1 ) { ?>
<p>
    <b>Languages:</b>
    <?php foreach ( $languages as $l => $fl ) { ?>
        <?php if ( $l == $language ) { ?>
            [<?php echo( $l ); ?>]
        <? } else { ?>
            <?php echo( $node->getTitleLink( null, $fl, $l ) ); ?>
        <? } ?>
    <?php } ?>
</p>
<?php } ?>

<?php if ( sizeof( $node->getPossibleActions() ) > 1 ) { ?>
<p>
    <b>Actions:</b>
    <?php foreach ( $node->getPossibleActions() as $a ) { ?>
        <?php if ( $a == $YD_MODULE_ACTION ) { ?>
            [<?php echo( $a ); ?>]
        <? } else { ?>
            <?php echo( $node->getTitleLink( $a, $language, $a ) ); ?>
        <? } ?>
    <?php } ?>
</p>
<?php } ?>

<?php if ( $YD_MODULE_ACTION == 'custom' ) { ?>
    <hr size="1" noshade="" />
    <b>MySQL version:</b> <?php echo( $mysql_version ); ?>
<?php } ?>

<?php if ( $YD_MODULE_ACTION == 'form' ) { ?>
    <hr size="1" noshade="" />
    <b>Using forms</b>
    <?php echo( $form ); ?>
<?php } ?>

<hr size="1" noshade="" />

<p><b>Template:</b> <?php echo( $tplName ); ?></p>

<p><b>Content-Type:</b> <?php echo( $node->getValue( 'content_type' ) ); ?></p>

<p><b>Action:</b> <?php echo( $YD_MODULE_ACTION ); ?></p>

<p><b>Parameters:</b> <?php YDDebugUtil::dump( $_GET ); ?></p>

<?php if ( $node->getChildren() ) { ?>
    <p><b>Children</b></p>
    <ul>
        <?php foreach ( $node->getChildren( 'created DESC, title_' . $language . ' DESC' ) as $child ) { ?>
            <li><?php echo( $child->getTitleLink( '' ) ); ?></li>
        <?php } ?>
    </ul>
<?php } ?>

<p><b>Path</b>
<?php foreach ( $node->getPath() as $path ) { ?>
    &raquo; <?php echo( $path->getTitleLink( '' ) ); ?>
<?php } ?>
</p>
