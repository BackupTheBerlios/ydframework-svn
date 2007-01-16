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
	    {t w="user.edityrprofile"}<br /><br />
	</div>
    {if $updated}
    	    {t w="user.updateprofileok"}<br /><br />
    {/if}
    <form {$form.attribs} class="fff_form">
    
        	<table> 
				<tr><td class="fff_left">{$form.profileDescription.label}</td><td class="fff_left">{$form.profileDescription.html}</td></tr>
			</table>  
       
        {t w="user.descprofile"}       
        
        	<table> 
				<tr><td class="fff_left">{$form.profilePass.label}</td><td class="fff_left">{$form.profilePass.html}</td></tr>
				<tr><td class="fff_left">{$form.profilePassUn.label}</td><td class="fff_left">{$form.profilePassUn.html}</td></tr>
				<tr><td class="fff_left">{$form.profilePassDeux.label}</td><td class="fff_left">{$form.profilePassDeux.html}</td></tr>

				<tr><td></td><td class="fff_right">{$form.cmdSubmit.html}</td></tr>
			</table>  



    </form>

    <br />
    <a href="{$YD_SELF_SCRIPT}?do=deleteProfil" class="fff_link1">{t w="user.askdelaccount"}</a>
</div>
