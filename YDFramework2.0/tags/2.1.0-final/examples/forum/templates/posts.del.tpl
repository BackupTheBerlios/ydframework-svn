<p>
{t w="posts.delandredirect"}
</p>
{literal}
<script language="Javascript">
	<!--
	function redirect() {
{/literal}
			document.location="forums.php?do=viewForum&id={$post->idForum}";
{literal}
	}
{/literal}
	setTimeout("redirect()",1500);
	-->
</script>
<p>
	<a href="forums.php?do=viewForum&id={$post->idForum}" >{t w="global.ifnotredirect"}</a>
</p>
