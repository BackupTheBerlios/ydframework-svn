<html>

<head>

	<title><?= $YD_FW_NAMEVERS ?></title>

</head>

<body>

	<?php if ( $YD_ACTION == 'default' ) { ?>

		<h3><?= $galTitle ?></h3>

		<p>PBase URL: <a href="<?= $homeUrl ?>" target="_blank"><?= $homeUrl ?></a></p>

		<table cellpadding="4" cellspacing="0" border="0">
		<?php foreach ( $galleries as $galleryrow ) { ?>
			<tr><td colspan="20">&nbsp;</td></tr>
			<tr>
				<?php foreach ( $galleryrow as $gallery ) { ?>
					<td width="240" align="center">
					<?php if ( $gallery ) { ?>
						<p>
						<a href="<?= $YD_SELF_SCRIPT ?>?do=gallery&gal=<?= $gallery['id'] ?>"><img src="<?= $gallery['thumbnail'] ?>" border="1"></a>
						</p>
						<p>
						<b><a href="<?= $YD_SELF_SCRIPT ?>?do=gallery&gal=<?= $gallery['id'] ?>"><?= $gallery['title'] ?></a></b>
						<br>
						(<?= sizeof( $gallery['images'] ) ?> images in this gallery)
						</p>
					<?php } ?>
					</td>
				<?php } ?>
			</tr>
		<? } ?>
		</table>

	<?php } ?>

	<?php if ( $YD_ACTION == 'gallery' ) { ?>

		<h3>
			<a href="<?= $YD_SELF_SCRIPT ?>"><?= $galTitle ?></a> &raquo; 
			<?= $gallery['title'] ?>
		</h3>

		<p>PBase URL: <a href="<?= $gallery['url'] ?>" target="_blank"><?= $gallery['url'] ?></a></p>

		<table cellpadding="4" cellspacing="0" border="0">
		<?php foreach ( $images as $imagerow ) { ?>
			<tr><td colspan="20">&nbsp;</td></tr>
			<tr>
				<?php foreach ( $imagerow as $image ) { ?>
					<td width="240" align="center">
					<?php if ( $image ) { ?>
						<a href="<?= $YD_SELF_SCRIPT ?>?do=image&gal=<?= $gallery['id'] ?>&img=<?= $image ?>"><img src="http://www.pbase.com/image/<?= $image ?>/small.jpg" border="1"></a>
						<p>
						<b><a href="<?= $YD_SELF_SCRIPT ?>?do=image&gal=<?= $gallery['id'] ?>&img=<?= $image ?>"><?= $image ?>.jpg</a></b>
						</p>
					<?php } ?>
					</td>
				<?php } ?>
			</tr>
		<? } ?>
		</table>
	<?php } ?>

	<?php if ( $YD_ACTION == 'image' ) { ?>

		<h3>
			<a href="<?= $YD_SELF_SCRIPT ?>"><?= $galTitle ?></a> &raquo;
			<a href="<?= $YD_SELF_SCRIPT ?>?do=gallery&gal=<?= $gallery['id'] ?>"><?= $gallery['title'] ?></a> &raquo;
			<?= $imageCurrent ?>.jpg
		</h3>

		<table cellpadding="4" cellspacing="0" border="0">
		<tr>
			<td>
				<?php if ( $imagePrevious ) { ?>
					<a href="<?= $YD_SELF_SCRIPT ?>?do=image&gal=<?= $gallery['id'] ?>&img=<?= $imagePrevious ?>"><img src="http://www.pbase.com/image/<?= $imagePrevious ?>/small.jpg" border="1"></a>
					<br>
					<a href="<?= $YD_SELF_SCRIPT ?>?do=image&gal=<?= $gallery['id'] ?>&img=<?= $imagePrevious ?>">previous</a>
				<?php } else { ?>
					&nbsp;
				<?php } ?>
			</td>
			<td align="right">
				<?php if ( $imageNext ) { ?>
					<a href="<?= $YD_SELF_SCRIPT ?>?do=image&gal=<?= $gallery['id'] ?>&img=<?= $imageNext ?>"><img src="http://www.pbase.com/image/<?= $imageNext ?>/small.jpg" border="1"></a>
					<br>
					<a href="<?= $YD_SELF_SCRIPT ?>?do=image&gal=<?= $gallery['id'] ?>&img=<?= $imageNext ?>">next</a>
				<?php } else { ?>
					&nbsp;
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<a href="<?= $YD_SELF_SCRIPT ?>?do=gallery&gal=<?= $gallery['id'] ?>"><img src="http://www.pbase.com/image/<?= $imageCurrent ?>.jpg" border="1"></a>
			<td>
		<tr>
		</table>

	<?php } ?>

	<p>[
		<a href="<?= $YD_SELF_SCRIPT ?>">all galleries</a>
		|
		<a href="index.php">other samples</a>
	]</p>

</body>

</html>
