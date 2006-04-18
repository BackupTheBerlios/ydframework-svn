<br />
<table class="fff_table" id="listePosts">
	<tr class="fff_title">
	 <th>{t w="global.author"}</th>
	 <th class="fff_rMessage">{t w="posts.messagehead"}</th>
	</tr>
		
    <tr class="fff_trow0even">
	 <td rowspan="2">
	    <a title="{t w="global.profileof"} {$post->loginAuthor}" href="user.php?do=ViewProfil&amp;userLogin={$post->loginAuthor}" class="fff_link2">{$post->loginAuthor}</a>
	 </td>
	 <td class="fff_rMessage">
	  <span class="fff_title">{$post->title} : </span><br /><br />
	  {$post->content}
	  
	  {if $post->idEditor != ""}
	  	<br /><br />
	  	<span class="fff_edited">{t w="posts.editedon"} {$post->dateEdited|date_format:"%d/%m/%Y  %H:%M:%S"} {t w="global.by"} {$post->loginEditor}.</span>
	  {/if}
	 </td>
	</tr>
	<tr class="fff_trow0even">
	 <td class="fff_rMessage_infos">
	 	{t w="posts.postedon"} {$post->datePost|date_format:"%d/%m/%Y  %H:%M:%S"}
	 	{if $user->rightslevel >= LEVEL_MODERATOR } par {$post->ipAuthor} <a class="fff_link2" href="posts.php?do=EditPost&amp;idPost={$post->id}">{t w="posts.editthemessage"}</a>
	 	{elseif $user->id == $post->idAuthor } <a class="fff_link2" href="posts.php?do=EditPost&amp;idPost={$post->id}">{t w="posts.editmymessage"}</a>
	 	{/if}
	 </td>
	</tr>
	
{assign var="parity" value="odd"}
{foreach from=$post->answers item=ans}
    <tr class="fff_trow0{$parity}">
	 <td rowspan="2">
		 <a title="{t w="global.profileof"} {$ans->loginAuthor}" href="user.php?do=ViewProfil&amp;userLogin={$ans->loginAuthor}" class="fff_link2">{$ans->loginAuthor}</a>
	 </td>
	 <td class="fff_rMessage">
	  <span class="fff_title">{$ans->title} : </span><br /><br />
	  {$ans->content}
	  {if $ans->idEditor != ""}
	  	<br /><br />
	  	<span class="fff_edited">{t w="posts.editedon"} {$ans->dateEdited|date_format:"%d/%m/%Y à %H:%M:%S"} {t w="global.by"} {$ans->loginEditor}.</span>
	  {/if}
	 </td>
	</tr>
	<tr class="fff_trow0{$parity}">
	 <td class="fff_rMessage_infos">
	 	 {t w="posts.postedon"} {$ans->datePost|date_format:"%d/%m/%Y %H:%M:%S"}
	 	{if $user->rightslevel >= LEVEL_MODERATOR } par {$ans->ipAuthor} <a class="fff_link2" href="posts.php?do=EditPost&amp;idPost={$ans->id}">{t w="posts.editthemessage"}</a>
	 	{elseif $user->id == $ans->idAuthor } <a class="fff_link2" href="posts.php?do=EditPost&amp;idPost={$ans->id}">{t w="posts.editmymessage"}</a>
	 	{/if}
	 </td>
	</tr>
	
	{if $parity=="odd"}
		{assign var="parity" value="even"}
	{else}
		{assign var="parity" value="odd"}
	{/if}
{/foreach}
	<tr>
	 <td colspan="2" align="right">
		{if $user->rightslevel >= LEVEL_MODERATOR }
			<a href="posts.php?do=DelPost&amp;idPost={$post->id}" class="fff_link1" onclick="return confirm('{t w="posts.confirmdelthread"}');">{t w="posts.delete"}</a>
		{/if}
	  <a class="fff_link1" href="posts.php?do=Repondre&amp;idForum={$post->idForum}&amp;idPostParent={$post->id}">{t w="posts.answer"}</a>
	 </td>
	</tr>
</table>
