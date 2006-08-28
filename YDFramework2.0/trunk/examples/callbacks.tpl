<html>

<head>

    <title>{$YD_FW_NAMEVERS}</title>

</head>

<body>

    <h3>{$title}</h3>

    <p>
        <a href="{$YD_SELF_SCRIPT}">default action</a> |
        <a href="{$YD_SELF_SCRIPT}?do=edit">edit action</a> |
        <a href="{$YD_SELF_SCRIPT}?do=forward">forward action</a> |
        <a href="{$YD_SELF_SCRIPT}?do=redirect">redirect action</a> |
        <a href="index.php">other samples</a>
    </p>
	
	<h3>Browser Info (added by callbacks)</h3>
	<p>
		{$browserinfo}
	</p>

</body>

</html>
