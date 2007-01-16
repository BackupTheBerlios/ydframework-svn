{if $userfound }
	<div class="fff_form_container" >
	    {if $form.errors}
	        <div class="error">
	        	<img src="images/caution.gif" /><br />
	            {foreach from=$form.errors item=error}
	                {$error}<br>
	            {/foreach}
	        </div>
	    {/if}
	    <div class="fff_title">
		    {t w="admin.editprofileof"} {$user->login}:<br /><br />
		</div>
	    <form {$form.attribs} class="fff_form">
	    
	        	<table> 
					<tr><td class="fff_left">{$form.userDescription.label}</td><td class="fff_left">{$form.userDescription.html}</td></tr>
					<tr><td class="fff_left">{$form.userGroup.label}</td><td class="fff_left">{$form.userGroup.html}</td></tr>
					<tr><td></td><td class="fff_right">{$form.cmdSubmit.html}</td></tr>
				</table>  
	    
	    </form>
	
	    <br />
	</div>
{else}
	{t w="admin.unkownuser"}
{/if}
