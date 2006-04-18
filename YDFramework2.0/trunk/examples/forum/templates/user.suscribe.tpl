	<div class="fff_form_container" >
		<div class="fff_title">
			{t w="user.suscribeform"}
		</div>
			{if $form.errors}
				<br />
        <div class="fff_error">
            <img src="images/caution.gif" /><br /> 
            {foreach from=$form.errors item=error}
                {$error}<br />
            {/foreach}
        </div>
    	{/if}
		<br />
		<form {$form.attribs} class="fff_form">
			<table> 
				<tr><td class="fff_left">{$form.suscribeLogin.label}</td><td class="fff_left">{$form.suscribeLogin.html}</td></tr>
				<tr><td class="fff_left">{$form.suscribePassUn.label}</td><td class="fff_left">{$form.suscribePassUn.html}</td></tr>
				<tr><td class="fff_left">{$form.suscribePassDeux.label}</td><td class="fff_left">{$form.suscribePassDeux.html}</td></tr>
				<tr><td class="fff_left">{$form.suscribeDescription.label}</td><td class="fff_left">{$form.suscribeDescription.html}</td></tr>
				<tr><td></td><td class="fff_right">{$form.cmdSubmit.html}</td></tr>
			</table>
	    </form>
	</div>
