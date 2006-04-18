{if $userfound }
	<div class="fff_title">
		{t w="global.theprofileof"} {$user->login}
	</div>
	<br/>
	<br />
	{$user->description}
	
{else}
	{t w="user.unkownuser"}	
{/if}
<br /><br />
<a title="{t w="global.returnprevious"}" href="#" onclick="javascript:history.back()" class="fff_link1">{t w="global.returnprevious"}</a>
