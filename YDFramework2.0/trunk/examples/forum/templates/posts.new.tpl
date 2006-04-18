    {if $form.errors}
        <div class="error">
            <img src="images/caution.gif" /><br /> 
            {foreach from=$form.errors item=error}
                {$error}<br>
            {/foreach}
        </div>
    {/if}
	{if $editMessage}
	 {t w="posts.messageedit"}
	{else}
	 {t w="posts.pleasesearch"}
	{/if}
	<br />
<div class="fff_form_container">
    <form {$form.attribs} class="fff_form" >
        <div>
            <span width="300px">{$form.postTitle.label}</span><br />{$form.postTitle.html}
        </div>
        <div>
            <span width="300px">{$form.postContent.label}</span><br />{$form.postContent.html}
        </div>
        <div>
            <a href="#" onclick="javascript:history.back();" class="fff_link1">{t w="global.back"}</a> {$form.cmdSubmit.html}
        </div>
    </form>
</div>
