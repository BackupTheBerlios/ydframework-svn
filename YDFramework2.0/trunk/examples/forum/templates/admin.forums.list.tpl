<div class="fff_title">
	{t w="admin.manageforums"}
</div>
<br />
{t w="admin.forumorder"}

{if $forums}
  <table class="fff_table" id="listeforums">

    <tr class="fff_title"><th class="fff_tMai">{t w="admin.forums"}</th><th class="fff_tNbe">{t w="admin.weights"}</th><th class="fff_tNbe">{t w="admin.subjects"}</th><th class="tNbe">{t w="admin.operations"}</th></tr>

    {foreach from=$forums item=forum}
        <tr class="fff_trow0{$forum->parity}"><td class="fff_rMai">{$forum->name}</a></td><td class="fff_rNbe">{$forum->order}</td><td class="fff_rNbe">{$forum->nbSubjects}</td><td class="fff_rNbe"><a href="admin.php?do=EditForum&amp;idforum={$forum->id}" class="fff_link1">{t w="admin.edit"}</a> - <a href="admin.php?do=DelForum&amp;idforum={$forum->id}" class="fff_link1" onclick="return confirm('{t w="admin.confirmdelforum"}');">{t w="admin.delete"}</a></td></tr>
    {/foreach}

  </table>
{else}
    <p>{t w="admin.noforums"}</p>
{/if}

<br />
{if $form.errors}
    <div class="fff_error">
        <img src="images/caution.gif" /><br /> 
        {foreach from=$form.errors item=error}
            {$error}<br />
        {/foreach}
    </div>
{/if}
<div class="fff_form_container" >
	<div class="fff_title">
	{t w="admin.forumcreate"}
	</div>
	<br />
	<form {$form.attribs} class="fff_form">
		<table> 
			<tr><td class="fff_left">{$form.forumTitle.label}</td><td class="fff_left">{$form.forumTitle.html}</td></tr>
			<tr><td class="fff_left">{$form.forumPoids.label}</td><td class="fff_left">{$form.forumPoids.html}</td></tr>
			<tr><td></td><td class="fff_right">{$form.cmdSubmit.html}</td></tr>
		</table>
    </form>
</div>
