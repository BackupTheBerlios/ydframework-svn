<div class="fff_form_container" >
	<div class="fff_title">
		{t w="user.connection"}
	</div>
    {if $form.errors}
    	<div class="error">
	    	<img src="images/caution.gif" /><br /> 
	            {foreach from=$form.errors item=error}
	                {$error}<br>
	            {/foreach}
        </div>
    {/if}

    <form {$form.attribs} class="fff_form">
    		<table> 
				<tr><td class="fff_left">{$form.loginName.label}</td><td class="fff_left">{$form.loginName.html}</td></tr>
				<tr><td class="fff_left">{$form.loginPass.label}</td><td class="fff_left">{$form.loginPass.html}</td></tr>
				<tr><td></td><td class="fff_right">{$form.cmdSubmit.html}</td></tr>
			</table>        
    </form>
</div>
    
