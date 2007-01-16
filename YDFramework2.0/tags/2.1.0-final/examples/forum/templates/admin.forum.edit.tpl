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
		{t w="admin.forumedit"}
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
