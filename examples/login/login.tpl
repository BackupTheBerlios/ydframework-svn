<html>

<head>

	<title>Authentication sample</title>

</head>

<body>

	<h3>Welcome to the login screen.</h3>
	
	<p>You can login as the user <b>pieter</b> with the password <b>kermit</b>.</p>

	<?php if ( $form['errors'] ) { ?>
		<p style="color: red">
			<?php foreach ( $form['errors'] as $error ) { ?>
				<?= $error ?><br>
			<?php } ?>
		</p>
	<?php } ?>

	<form <?= $form['attribs'] ?>>
		<p>
			<?= $form['loginName']['label'] ?>
			<br>
			<?= $form['loginName']['html'] ?>
		</p>
		<p>
			<?= $form['loginPass']['label'] ?>
			<br>
			<?= $form['loginPass']['html'] ?>
		</p>
		<p>
			<?= $form['cmdSubmit']['html'] ?>
		</p>
	</form>

</body>

</html>
