
{t w="admin.userslist"} <br /><br />

<div style="margin:auto;text-align:center">

  <table class="fff_table" id="listeUsers">

    <tr class="fff_title"><th>Id</th><th>{t w="admin.login"}</th><th>{t w="admin.group"}</th><th>{t w="admin.actions"}</th></tr>
	
	{assign var="parity" value="odd"}
    {foreach from=$users item=user}
        <tr class="fff_trow0{$parity}"><td>{$user->id}</a></td><td><a title="{t w="global.profileof"} {$user->login}" href="user.php?do=ViewProfil&amp;userLogin={$user->login}" class="fff_link2">{$user->login}</a></td><td>{$user->groupname}</td><td><a href="{$YD_SELF_SCRIPT}?do=EditUser&amp;userLogin={$user->login}" class="fff_link1">Editer</a> - <a href="{$YD_SELF_SCRIPT}?do=DelUser&amp;iduser={$user->id}" class="fff_link1" onclick="return confirm('{t w="admin.confirmdeluser"}');">{t w="admin.delete"}</a></td></tr>
    	{if $parity=="odd"}
			{assign var="parity" value="even"}
		{else}
			{assign var="parity" value="odd"}
		{/if}
    {/foreach}
  </table>
</div>
