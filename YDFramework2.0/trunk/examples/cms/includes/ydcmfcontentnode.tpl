<h2><?php echo( $node->getTranslatedValue( 'title' ) ); ?></h2>

<p>Hello world from template.</p>

<?php if ( sizeof( $languages ) > 1 ) { ?>
<p>
    Languages:
    <?php foreach ( $languages as $l ) { ?>
        <?php if ( $l == $language ) { ?>
            <b><?php echo( $l ); ?></b>
        <? } else { ?>
            <?php echo( $node->getTitleLink( null, $l, $l ) ); ?>
        <? } ?>
    <?php } ?>
</p>
<?php } ?>

<hr size="1" noshade="" />

<p><b>Template:</b> <?php echo( $tplName ); ?></p>

<p><b>Content-Type:</b> <?php echo( $node->getValue( 'content_type' ) ); ?></p>

<?php if ( $node->getChildren() ) { ?>
    <p><b>Children</b></p>
    <ul>
        <?php foreach ( $node->getChildren() as $child ) { ?>
            <li><?php echo( $child->getTitleLink( '' ) ); ?></li>
        <?php } ?>
    </ul>
<?php } ?>

<p><b>Path</b>
<?php foreach ( $node->getPath() as $path ) { ?>
    &raquo; <?php echo( $path->getTitleLink( '' ) ); ?>
<?php } ?>
</p>
