<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title><?php echo @$_GET['title']; ?></title>

	<script language="Javascript">
	<!--
	function CrossBrowserResizeInnerWindowTo(newWidth, newHeight) {
		if (self.innerWidth) {
			frameWidth  = self.innerWidth;
			frameHeight = self.innerHeight;
		} else if (document.documentElement && document.documentElement.clientWidth) {
			frameWidth  = document.documentElement.clientWidth;
			frameHeight = document.documentElement.clientHeight;
		} else if (document.body) {
			frameWidth  = document.body.clientWidth;
			frameHeight = document.body.clientHeight;
		} else {
			return false;
		}
		if (document.layers) {
			newWidth  -= (parent.outerWidth - parent.innerWidth);
			newHeight -= (parent.outerHeight - parent.innerHeight);
		}
		parent.window.resizeBy(newWidth - frameWidth, newHeight - frameHeight);
		return true;
	}
	// -->
	</script>
</head>
<body style="margin: 0px;">
<?php

if (get_magic_quotes_gpc()) {
	$_GET['src'] = stripslashes($_GET['src']);
}
echo '<img src="'.$_GET['src'].'" border="0">';
flush();
if ($imgdata = @getimagesize($_GET['src'])) {
	echo '<script language="Javascript">'."\n";
	echo 'if (((screen.width * 1.1) > '.$imgdata[0].') || ((screen.height * 1.1) > '.$imgdata[1].')) {'."\n";
	echo 'CrossBrowserResizeInnerWindowTo('.$imgdata[0].', '.$imgdata[1].');'."\n";
	echo '} else {'."\n";
	if (!isset($_REQUEST['norespawn'])) {
		echo 'window.open("'.$_SERVER['PHP_SELF'].'?src='.urlencode($_GET['src']).'&title='.urlencode(@$_GET['title']).'&norespawn=1", "respawn", "width="+Math.round(screen.width * 0.9)+",height="+Math.round(screen.height * 0.9)+",resizable=yes,status=no,menubar=no,toolbar=no,scrollbars=yes");'."\n";
		echo 'self.close();'."\n";
	}
	echo '}'."\n";
	echo '</script></body></html>';

} elseif (!isset($_REQUEST['norespawn'])) {

	echo '<script language="Javascript">window.open("'.$_SERVER['PHP_SELF'].'?src='.urlencode($_GET['src']).'&title='.urlencode(@$_GET['title']).'&norespawn=1", "respawn", "width=600,height=400,resizable=yes,status=no,menubar=no,toolbar=no,scrollbars=yes"); self.close();</script>';

}

?>
</body>
</html>