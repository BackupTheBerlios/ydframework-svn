{if $user->rightslevel >= LEVEL_USER }
	<a href="posts.php?do=AddPost&amp;idForum={$forum->id}" class="fff_link1">{t w="forums.createthread"}</a><br /><br />
{else}
	{t w="forums.suscribe"}<br /><br />
{/if}

{if $posts}

  <table class="fff_table" id="listePosts">

    <tr class="fff_title"><th class="fff_tMai">{t w="forums.thread"}</th><th class="fff_tNbe">{t w="forums.answers"}</th><th class="fff_TAut">{t w="global.author"}</th><th class="fff_tNbe">{t w="forums.readings"}</th><th class="fff_tLst">{t w="forums.lastanswer"}</th></tr>

    {foreach from=$posts item=post}
        <tr class="fff_trow0{$post->parity}"><td class="fff_rMai"><a class="fff_link1" href="posts.php?do=viewPost&amp;id={$post->id}">{$post->title}</a></td><td class="fff_rNbe">{$post->nbAnswers}</td><td class="fff_rAut"><a title="{t w="global.profileof"} {$post->loginAuthor}" href="user.php?do=ViewProfil&amp;userLogin={$post->loginAuthor}" class="fff_link2">{$post->loginAuthor}</a></td><td class="fff_rNbe">{$post->nbViews}</td><td class="fff_rLst">{t w="forums.dateprefix"}{$post->dateAnswer|date_format:"%d/%m/%Y à %H:%M:%S"}</td>
        {if $user->rightslevel >= LEVEL_MODERATOR }
			     <td><a href="posts.php?do=DelPost&amp;idPost={$post->id}" class="fff_link1" onclick="return confirm('{t w="forums.confirmdelthread"}');">X</a></td>
		{/if}

        </tr>
    {/foreach}

  </table>
  <table class="fff_table_bottom">
    <tr><td class="fff_left"><a href="forums.php?do=viewForum&amp;id=1{if $forum->limiteBasse != -1 }&amp;from={$forum->limiteBasse}{/if}" class="fff_link1">{if $forum->limiteBasse != -1 }{$forum->pas} {t w="forums.previousthreads"}{/if}</a></td><td class="fff_right"><a href="forums.php?do=viewForum&amp;id=1{if $forum->limiteHaute!=-1}&amp;from={$forum->limiteHaute}{/if}" class="fff_link1">{if $forum->limiteHaute != -1 }{$forum->pas} {t w="forums.nextthreads"}{/if}</a></td></tr>
  </table>
{else}
    <p>{t w="forums.nothread"}</p>
{/if}


{if $user->rightslevel >= LEVEL_ADMIN }
	{t w="forums.yradmin"}
{elseif $user->rightslevel >= LEVEL_MODERATOR }
	{t w="forums.yrcensor"}
{/if}
