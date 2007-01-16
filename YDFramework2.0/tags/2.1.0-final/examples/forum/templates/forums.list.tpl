{if $forums}

  <table class="fff_table" id="listeforums">

    <tr class="fff_title"><th class="fff_tMai">{t w="forums.forums"}</th><th class="fff_tNbe">{t w="forums.threads"}</th><th class="tNbe">{t w="forums.messages"}</th></tr>

    {foreach from=$forums item=forum}
        <tr class="fff_trow0{$forum->parity}"><td class="fff_rMai"><a class="fff_link1" href="forums.php?do=viewForum&amp;id={$forum->id}&amp;from=0">{$forum->name}</a></td><td class="fff_rNbe">{$forum->nbSubjects}</td><td class="fff_rNbe">{$forum->nbPosts}</td></tr>
    {/foreach}

  </table>

{else}

    <p>{t w="forums.noforum"}</p>

{/if}

