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
			{t w="forums.searchonforum"}
		</div>
		<br />
		<form {$form.attribs} class="fff_form">
			<table> 
				<tr><td class="fff_left">{$form.searchKeys.label}</td><td class="fff_left">{$form.searchKeys.html}</td></tr>
				<tr><td class="fff_left">{$form.searchForum.label}</td><td class="fff_left">{$form.searchForum.html}</td></tr>
				<tr><td></td><td class="fff_right">{$form.cmdSubmit.html}</td></tr>
			</table>
	    </form>
	</div>
	{if $searched}
		<br />
		{if $nbposts >0 }
			{t w="forums.startresult"}{$nbposts}{t w="forums.endresult"}<br />

			<table class="fff_table" id="listePosts">
			    <tr class="fff_title"><th class="fff_tMai">{t w="forums.thread"}</th><th class="fff_tNbe">{t w="forums.answers"}</th><th class="fff_TAut">{t w="global.author"}</th><th class="fff_tNbe">{t w="forums.readings"}</th><th class="fff_tLst">{t w="forums.lastanswer"}</th></tr>
			{foreach from=$posts item=post}
		        <tr class="fff_trow0{$post->parity}"><td class="fff_rMai"><a class="fff_link1" href="posts.php?do=viewPost&amp;id={$post->id}">{$post->title}</a></td><td class="fff_rNbe">{$post->nbAnswers}</td><td class="fff_rAut">{$post->loginAuthor}</td><td class="fff_rNbe">{$post->nbViews}</td><td class="fff_rLst">{t w="forums.dateprefix"}{$post->dateAnswer|date_format:"%d/%m/%Y à %H:%M:%S"}</td>
		        </tr>
		    {/foreach}
			</table>
		{else}
			{t w="forums.noresult"}
		{/if}
	{/if}
