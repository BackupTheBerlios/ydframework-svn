<html>

<head>

	<title>[$YD_FW_NAMEVERS]</title>

</head>

<body>

	[if $YD_ACTION == 'default']

		<h3>[$galTitle]</h3>

		<p>PBase URL: <a href="[$homeUrl]" target="_blank">[$homeUrl]</a></p>

		<table cellpadding="4" cellspacing="0" border="0">
		[foreach from=$galleries item=galleryrow]
			<tr><td colspan="20">&nbsp;</td></tr>
			<tr>
				[foreach from=$galleryrow item=gallery]
					<td width="240" align="center">
					[if $gallery]
						<p>
						<a href="[$YD_SELF_SCRIPT]?do=gallery&gal=[$gallery.id]"><img src="[$gallery.thumbnail]" border="1"></a>
						</p>
						<p>
						<b><a href="[$YD_SELF_SCRIPT]?do=gallery&gal=[$gallery.id]">[$gallery.title]</a></b>
						<br>
						([$gallery.images|@sizeof] images in this gallery)
						</p>
					[/if]
					</td>
				[/foreach]
			</tr>
		[/foreach]
		</table>

	[/if]

	[if $YD_ACTION == 'gallery']

		<h3>
			<a href="[$YD_SELF_SCRIPT]">[$galTitle]</a> &raquo; 
			[$gallery.title]
		</h3>

		<p>PBase URL: <a href="[$gallery.url]" target="_blank">[$gallery.url]</a></p>

		<table cellpadding="4" cellspacing="0" border="0">
		[foreach from=$images item=imagerow]
			<tr><td colspan="20">&nbsp;</td></tr>
			<tr>
				[foreach from=$imagerow item=image]
					<td width="240" align="center">
					[if $image]
						<a href="[$YD_SELF_SCRIPT]?do=image&gal=[$gallery.id]&img=[$image]"><img src="http://www.pbase.com/image/[$image]/small.jpg" border="1"></a>
						<p>
						<b><a href="[$YD_SELF_SCRIPT]?do=image&gal=[$gallery.id]&img=[$image]">[$image].jpg</a></b>
						</p>
					[/if]
					</td>
				[/foreach]
			</tr>
		[/foreach]
		</table>

	[/if]

	[if $YD_ACTION == 'image']

		<h3>
			<a href="[$YD_SELF_SCRIPT]">[$galTitle]</a> &raquo;
			<a href="[$YD_SELF_SCRIPT]?do=gallery&gal=[$gallery.id]">[$gallery.title]</a> &raquo;
			[$imageCurrent].jpg
		</h3>

		<table cellpadding="4" cellspacing="0" border="0">
		<tr>
			<td>
				[if $imagePrevious]
					<a href="[$YD_SELF_SCRIPT]?do=image&gal=[$gallery.id]&img=[$imagePrevious]"><img src="http://www.pbase.com/image/[$imagePrevious]/small.jpg" border="1"></a>
					<br>
					<a href="[$YD_SELF_SCRIPT]?do=image&gal=[$gallery.id]&img=[$imagePrevious]">previous</a>
				[else]
					&nbsp;
				[/if]
			</td>
			<td align="right">
				[if $imageNext]
					<a href="[$YD_SELF_SCRIPT]?do=image&gal=[$gallery.id]&img=[$imageNext]"><img src="http://www.pbase.com/image/[$imageNext]/small.jpg" border="1"></a>
					<br>
					<a href="[$YD_SELF_SCRIPT]?do=image&gal=[$gallery.id]&img=[$imageNext]">next</a>
				[else]
					&nbsp;
				[/if]
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<a href="[$YD_SELF_SCRIPT]?do=gallery&gal=[$gallery.id]"><img src="http://www.pbase.com/image/[$imageCurrent].jpg" border="1"></a>
			<td>
		<tr>
		</table>

	[/if]

	<p>
		<a href="[$YD_SELF_SCRIPT]">all galleries</a>
		|
		<a href="index.php">other samples</a>
	</p>

</body>

</html>
