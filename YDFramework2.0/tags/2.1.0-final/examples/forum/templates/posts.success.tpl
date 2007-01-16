<p>
	{if $editMessage}
		{t w="posts.updateredirect"}
	{else}
		{t w="posts.addredirect"}
	{/if}
</p>
{literal}
<script language="Javascript">
	<!--
	function redirect() {
{/literal}
			document.location="{$YD_SELF_SCRIPT}?do=viewPost&id={$post->id}";
{literal}
	}
{/literal}
	setTimeout("redirect()",1500);
	-->
</script>
<p>
	<a href="{$YD_SELF_SCRIPT}?do=viewPost&amp;id={$post->id}" >{t w="global.ifnotredirect"}</a>
</p>
