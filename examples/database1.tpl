<html>

<head>

	<title><?= $YD_FW_NAMEVERS ?></title>

</head>

<body>

	<h3>Testing...</h3>

	<p>Version: <?= $version ?></p>
	<p>Number of queries: <?= $sqlcount ?></p>

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
				<td><?= $row['Id'] ?></td>
				<td><?= $row['User'] ?></td>
				<td><?= $row['Host'] ?></td>
				<td><?= $row['db'] ?>&nbsp;</td>
				<td><?= $row['Command'] ?></td>
				<td><?= $row['Time'] ?></td>
				<td><?= $row['State'] ?>&nbsp;</td>
				<td><?= $row['Info'] ?>&nbsp;</td>
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
				<td><?= $row['Variable_name'] ?></td>
				<td><?= $row['Value'] ?>&nbsp;</td>
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
				<td><?= $row['Variable_name'] ?></td>
				<td><?= $row['Value'] ?>&nbsp;</td>
			</tr>
		<?php } ?>
		</table></p>
	<?php } ?>

	<p>[ <a href="index.php">other samples</a> ]</p>

</body>

</html>
