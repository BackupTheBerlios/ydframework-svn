<html>

<head>

	<title><?= $YD_FW_NAMEVERS ?></title>

</head>

<body>

	<h3><?= $title ?></h3>

	<?php if ( $YD_ACTION == 'default' ) { ?>

		<p><a href="<?= $YD_SELF_SCRIPT ?>?do=AddNote">Add a new note</a></p>

		<?php if ( $entries ) { ?>

			<?php foreach ( $entries as $entry ) { ?>
				<p>
				<b><?= $entry['NoteTitle'] ?></b>
				[ <a href="<?= $YD_SELF_SCRIPT ?>?do=EditNote&id=<?= $entry['NoteID'] ?>">edit</a> | 
				<a href="<?= $YD_SELF_SCRIPT ?>?do=DeleteNote&id=<?= $entry['NoteID'] ?>">delete</a> ]
				<br>
				<?= $entry['NoteContents'] ?>
				</p>
			<?php } ?>

		<?php } else { ?>
			<p>No notes were found.</p>
		<?php } ?>

	<?php } ?>

	<?php if ( $YD_ACTION == 'addnote' || $YD_ACTION == 'editnote' ) { ?>
		<?= $form ?>
	<?php } ?>

</body>

</html>
