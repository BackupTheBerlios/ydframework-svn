    {if $confirmation}
        <p style="color: green">
						{t w="user.delaccount"}
        </p>
    {else}
    	<p style="color: red">
			{t w="user.delaccountconfirm"}
			<a href="{$YD_SELF_SCRIPT}?do=deleteProfil&amp;confirm=true" class="fff_link1">{t w="global.yes"}</a> - <a href="{$YD_SELF_SCRIPT}?do=monProfil" class="fff_link1">{t w="global.no"}</a>
        </p>
    {/if}
