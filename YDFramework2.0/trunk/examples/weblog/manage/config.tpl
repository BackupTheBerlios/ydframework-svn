{include file="__mng_header.tpl"}

<p class="title">{t w="h_global_settings"} &raquo; {t w="a_settings"}</p>

{if $form.errors}
    <p><table width="700" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <th class="adminRowELG">{t w="err_general"}</th>
        </tr>
        <tr>
            <td class="adminRowEL">
                {foreach from=$form.errors item="error"}
                    {$error}<br/>
                {/foreach}
            </td>
        </tr>
    </table></p>
{/if}

{if $YD_ACTION == 'default'}
    <table width="700" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <th colspan="3" class="adminRowLG">&raquo; {t w="cfg_db_settings"}</td>
        </tr>
        <tr>
            <td class="adminRowL" width="300">{t w="cfg_db_host"}</td>
            <td class="adminRowL" width="400">{$config.db_host}</td>
        </tr>
        <tr>
            <td class="adminRowL">{t w="cfg_db_name"}</td>
            <td class="adminRowL">{$config.db_name}</td>
        </tr>
        <tr>
            <td class="adminRowL">{t w="cfg_db_user"}</td>
            <td class="adminRowL">{$config.db_user}</td>
        </tr>
        <tr>
            <td class="adminRowL">{t w="cfg_db_pass"}</td>
            <td class="adminRowL">{$config.db_pass|default:'&nbsp;'}</td>
        </tr>
        <tr>
            <td class="adminRowL">{t w="cfg_db_prefix"}</td>
            <td class="adminRowL">{$config.db_prefix|default:'&nbsp;'}</td>
        </tr>
    </table>
    <table width="700" cellspacing="0" cellpadding="0" border="0">
        <tr><td colspan="3">&nbsp;</td></tr>
        <tr>
            <th colspan="3" class="adminRowLG">&raquo; {t w="cfg_weblog_settings"}</td>
        </tr>
        <tr>
            <td class="adminRowL" width="300">{t w="cfg_weblog_title"}</td>
            <td class="adminRowL" width="400">{$config.weblog_title}</td>
        </tr>
        <tr>
            <td class="adminRowL">{t w="cfg_weblog_description"}</td>
            <td class="adminRowL">{$config.weblog_description}</td>
        </tr>
        <tr>
            <td class="adminRowL">{t w="cfg_weblog_entries_fp"}</td>
            <td class="adminRowL">{$config.weblog_entries_fp}</td>
        </tr>
        <tr>
            <td class="adminRowL">{t w="cfg_weblog_skin"}</td>
            <td class="adminRowL">{$config.weblog_skin}</td>
        </tr>
        <tr>
            <td class="adminRowL">{t w="cfg_weblog_language"}</td>
            <td class="adminRowL">{$config.weblog_language}</td>
        </tr>
    </table>
    <table width="700" cellspacing="0" cellpadding="0" border="0">
        <tr><td colspan="3">&nbsp;</td></tr>
        <tr>
            <th colspan="3" class="adminRowLG">&raquo; {t w="cfg_notification"}</td>
        </tr>
        <tr>
            <td class="adminRowL" width="300">{t w="cfg_notification_email_comment"}</td>
            <td class="adminRowL" width="400">{$config.email_new_comment}</td>
        </tr>
    </table>
    <table width="700" cellspacing="0" cellpadding="0" border="0">
        <tr><td colspan="3">&nbsp;</td></tr>
        <tr>
            <th colspan="3" class="adminRowLG">&raquo; {t w="cfg_rss"}</td>
        </tr>
        <tr>
            <td class="adminRowL" width="300">{t w="cfg_rss_max_syndicated_items"}</td>
            <td class="adminRowL" width="400">{$config.max_syndicated_items}</td>
        </tr>
    </table>
    <p><a href="{$YD_SELF_SCRIPT}?do=edit">{t w="change_config"}</a></p>
{/if}

{if $YD_ACTION == 'edit'}
    {$form.tag}

        <table width="700" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <th colspan="3" class="adminRowLG">&raquo; {t w="cfg_db_settings"}</td>
            </tr>
            <tr>
                <td class="adminRowL" width="300">{$form.db_host.label_html}</td>
                <td class="adminRowL" width="400">{$form.db_host.html}</td>
            </tr>
            <tr>
                <td class="adminRowL">{$form.db_name.label_html}</td>
                <td class="adminRowL">{$form.db_name.html}</td>
            </tr>
            <tr>
                <td class="adminRowL">{$form.db_user.label_html}</td>
                <td class="adminRowL">{$form.db_user.html}</td>
            </tr>
            <tr>
                <td class="adminRowL">{$form.db_pass.label_html}</td>
                <td class="adminRowL">{$form.db_pass.html}</td>
            </tr>
            <tr>
                <td class="adminRowL">{$form.db_prefix.label_html}</td>
                <td class="adminRowL">{$form.db_prefix.html}</td>
            </tr>
        </table>
        <table width="700" cellspacing="0" cellpadding="0" border="0">
            <tr><td colspan="3">&nbsp;</td></tr>
            <tr>
                <th colspan="3" class="adminRowLG">&raquo; {t w="cfg_weblog_settings"}</td>
            </tr>
            <tr>
                <td class="adminRowL" width="300">{$form.weblog_title.label_html}</td>
                <td class="adminRowL" width="400">{$form.weblog_title.html}</td>
            </tr>
            <tr>
                <td class="adminRowL">{$form.weblog_description.label_html}</td>
                <td class="adminRowL">{$form.weblog_description.html}</td>
            </tr>
            <tr>
                <td class="adminRowL">{$form.weblog_entries_fp.label_html}</td>
                <td class="adminRowL">{$form.weblog_entries_fp.html}</td>
            </tr>
            <tr>
                <td class="adminRowL">{$form.weblog_skin.label_html}</td>
                <td class="adminRowL">{$form.weblog_skin.html}</td>
            </tr>
            <tr>
                <td class="adminRowL">{$form.weblog_language.label_html}</td>
                <td class="adminRowL">{$form.weblog_language.html}</td>
            </tr>
        </table>
        <table width="700" cellspacing="0" cellpadding="0" border="0">
            <tr><td colspan="3">&nbsp;</td></tr>
            <tr>
                <th colspan="3" class="adminRowLG">&raquo; {t w="cfg_notification"}</td>
            </tr>
            <tr>
                <td class="adminRowL" width="300">{$form.email_new_comment.label_html}</td>
                <td class="adminRowL" width="400">{$form.email_new_comment.html}</td>
            </tr>
        </table>
        <table width="700" cellspacing="0" cellpadding="0" border="0">
            <tr><td colspan="3">&nbsp;</td></tr>
            <tr>
                <th colspan="3" class="adminRowLG">&raquo; {t w="cfg_rss"}</td>
            </tr>
            <tr>
                <td class="adminRowL" width="300">{$form.max_syndicated_items.label_html}</td>
                <td class="adminRowL" width="400">{$form.max_syndicated_items.html}</td>
            </tr>
        </table>
        <p>{$form._cmdSubmit.html}</p>
    {$form.endtag}
{/if}

{include file="__mng_footer.tpl"}
