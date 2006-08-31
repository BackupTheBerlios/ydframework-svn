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
            <th colspan="3" class="adminRowLG">&raquo; {t w="cfg_db_settings"}</th>
        </tr>
        <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
            <td class="adminRowL" width="300">{t w="cfg_db_host"}</td>
            <td class="adminRowL" width="400">{$config.db_host}</td>
        </tr>
        <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
            <td class="adminRowL">{t w="cfg_db_name"}</td>
            <td class="adminRowL">{$config.db_name}</td>
        </tr>
        <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
            <td class="adminRowL">{t w="cfg_db_user"}</td>
            <td class="adminRowL">{$config.db_user}</td>
        </tr>
        <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
            <td class="adminRowL">{t w="cfg_db_pass"}</td>
            <td class="adminRowL">{$config.db_pass|default:'-'}</td>
        </tr>
        <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
            <td class="adminRowL">{t w="cfg_db_prefix"}</td>
            <td class="adminRowL">{$config.db_prefix|default:'-'}</td>
        </tr>
    </table>
    <table width="700" cellspacing="0" cellpadding="0" border="0">
        <tr><td colspan="3">&nbsp;</td></tr>
        <tr>
            <th colspan="3" class="adminRowLG">&raquo; {t w="cfg_weblog_settings"}</th>
        </tr>
        <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
            <td class="adminRowL" width="300">{t w="cfg_weblog_title"}</td>
            <td class="adminRowL" width="400">{$config.weblog_title}</td>
        </tr>
        <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
            <td class="adminRowL">{t w="cfg_weblog_description"}</td>
            <td class="adminRowL">{$config.weblog_description|default:'-'}</td>
        </tr>
        <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
            <td class="adminRowL">{t w="cfg_weblog_entries_fp"}</td>
            <td class="adminRowL">{$config.weblog_entries_fp}</td>
        </tr>
        <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
            <td class="adminRowL">{t w="cfg_weblog_skin"}</td>
            <td class="adminRowL">{$config.weblog_skin}</td>
        </tr>
        <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
            <td class="adminRowL">{t w="cfg_weblog_language"}</td>
            <td class="adminRowL">{$config.weblog_language}</td>
        </tr>
        <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
            <td class="adminRowL">{t w="cfg_friendly_urls"}</td>
            <td class="adminRowL">{$config.friendly_urls}</td>
        </tr>
        <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
            <td class="adminRowL">{t w="cfg_include_debug_info"}</td>
            <td class="adminRowL">{$config.include_debug_info}</td>
        </tr>
        <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
            <td class="adminRowL">{t w="cfg_keep_stats"}</td>
            <td class="adminRowL">{$config.keep_stats}</td>
        </tr>
        <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
            <td class="adminRowL">{t w="cfg_weblog_google_analytics"}</td>
            <td class="adminRowL">{$config.google_analytics|default:'-'}</td>
        </tr>
    </table>
    <table width="700" cellspacing="0" cellpadding="0" border="0">
        <tr><td colspan="3">&nbsp;</td></tr>
        <tr>
            <th colspan="3" class="adminRowLG">&raquo; {t w="cfg_default_item_settings"}</th>
        </tr>
        <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
            <td class="adminRowL" width="300">{t w="is_draft"}</td>
            <td class="adminRowL" width="400">{$config.dflt_is_draft}</td>
        </tr>
    </table>
    <table width="700" cellspacing="0" cellpadding="0" border="0">
        <tr><td colspan="3">&nbsp;</td></tr>
        <tr>
            <th colspan="3" class="adminRowLG">&raquo; {t w="cfg_cache"}</th>
        </tr>
        <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
            <td class="adminRowL" width="300">{t w="cfg_use_cache_comment"}</td>
            <td class="adminRowL" width="400">{$config.use_cache}</td>
        </tr>
    </table>
    <table width="700" cellspacing="0" cellpadding="0" border="0">
        <tr><td colspan="3">&nbsp;</td></tr>
        <tr>
            <th colspan="3" class="adminRowLG">&raquo; {t w="cfg_notification"}</th>
        </tr>
        <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
            <td class="adminRowL" width="300">{t w="cfg_notification_email_comment"}</td>
            <td class="adminRowL" width="400">{$config.email_new_comment}</td>
        </tr>
        <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
            <td class="adminRowL" width="300">{t w="cfg_notification_email_item"}</td>
            <td class="adminRowL" width="400">{$config.email_new_item}</td>
        </tr>
    </table>
    <table width="700" cellspacing="0" cellpadding="0" border="0">
        <tr><td colspan="3">&nbsp;</td></tr>
        <tr>
            <th colspan="3" class="adminRowLG">&raquo; {t w="cfg_rss"}</th>
        </tr>
        <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
            <td class="adminRowL" width="300">{t w="cfg_rss_max_syndicated_items"}</td>
            <td class="adminRowL" width="400">{$config.max_syndicated_items}</td>
        </tr>
    </table>
    <table width="700" cellspacing="0" cellpadding="0" border="0">
        <tr><td colspan="3">&nbsp;</td></tr>
        <tr>
            <th colspan="3" class="adminRowLG">&raquo; {t w="cfg_spam_protection"}</th>
        </tr>
        <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
            <td class="adminRowL" width="300">{t w="cfg_blocked_ips"}</td>
            <td class="adminRowL" width="400">{$config.blocked_ips|default:'-'|replace:', ':'<br/>'}</td>
        </tr>
    </table>
    <p><input type="button" class="button" onClick="window.location='{$YD_SELF_SCRIPT}?do=edit';" value="{t w="change_config"}" />
{/if}

{if $YD_ACTION == 'edit'}
    {$form.tag}

        <table width="700" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <th class="adminRowLG">&raquo; {t w="cfg_db_settings"}</th>
                <th class="adminRowLGR">&raquo; <a href="{$YD_SELF_SCRIPT}"><b>{t w="back"}</b></a></th>
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
                <th colspan="3" class="adminRowLG">&raquo; {t w="cfg_weblog_settings"}</th>
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
            <tr>
                <td class="adminRowL">{$form.friendly_urls.label_html}</td>
                <td class="adminRowL">{$form.friendly_urls.html}</td>
            </tr>
            <tr>
                <td class="adminRowL">{$form.include_debug_info.label_html}</td>
                <td class="adminRowL">{$form.include_debug_info.html}</td>
            </tr>
            <tr>
                <td class="adminRowL">{$form.keep_stats.label_html}</td>
                <td class="adminRowL">{$form.keep_stats.html}</td>
            </tr>
            <tr>
                <td class="adminRowL">{$form.google_analytics.label_html}</td>
                <td class="adminRowL">{$form.google_analytics.html}</td>
            </tr>
        </table>
        <table width="700" cellspacing="0" cellpadding="0" border="0">
            <tr><td colspan="3">&nbsp;</td></tr>
            <tr>
                <th colspan="3" class="adminRowLG">&raquo; {t w="cfg_default_item_settings"}</th>
            </tr>
            <tr>
                <td class="adminRowL" width="300">{$form.dflt_allow_comments.label_html}</td>
                <td class="adminRowL" width="400">{$form.dflt_allow_comments.html}</td>
            </tr>
            <tr>
                <td class="adminRowL" width="300">{$form.auto_close_items.label_html}</td>
                <td class="adminRowL" width="400">{$form.auto_close_items.html}</td>
            </tr>
            <tr>
                <td class="adminRowL" width="300">{$form.dflt_is_draft.label_html}</td>
                <td class="adminRowL" width="400">{$form.dflt_is_draft.html}</td>
            </tr>
        </table>
        <table width="700" cellspacing="0" cellpadding="0" border="0">
            <tr><td colspan="3">&nbsp;</td></tr>
            <tr>
                <th colspan="3" class="adminRowLG">&raquo; {t w="cfg_cache"}</th>
            </tr>
            <tr>
                <td class="adminRowL" width="300">{$form.use_cache.label_html}</td>
                <td class="adminRowL" width="400">{$form.use_cache.html}</td>
            </tr>
        </table>
        <table width="700" cellspacing="0" cellpadding="0" border="0">
            <tr><td colspan="3">&nbsp;</td></tr>
            <tr>
                <th colspan="3" class="adminRowLG">&raquo; {t w="cfg_notification"}</th>
            </tr>
            <tr>
                <td class="adminRowL" width="300">{$form.email_new_comment.label_html}</td>
                <td class="adminRowL" width="400">{$form.email_new_comment.html}</td>
            </tr>
            <tr>
                <td class="adminRowL" width="300">{$form.email_new_item.label_html}</td>
                <td class="adminRowL" width="400">{$form.email_new_item.html}</td>
            </tr>
        </table>
        <table width="700" cellspacing="0" cellpadding="0" border="0">
            <tr><td colspan="3">&nbsp;</td></tr>
            <tr>
                <th colspan="3" class="adminRowLG">&raquo; {t w="cfg_rss"}</th>
            </tr>
            <tr>
                <td class="adminRowL" width="300">{$form.max_syndicated_items.label_html}</td>
                <td class="adminRowL" width="400">{$form.max_syndicated_items.html}</td>
            </tr>
        </table>
        <table width="700" cellspacing="0" cellpadding="0" border="0">
            <tr><td colspan="3">&nbsp;</td></tr>
            <tr>
                <th colspan="3" class="adminRowLG">&raquo; {t w="cfg_spam_protection"}</th>
            </tr>
            <tr>
                <td class="adminRowL" width="300">{$form.blocked_ips.label_html}</td>
                <td class="adminRowL" width="400">{$form.blocked_ips.html}</td>
            </tr>
        </table>
        <p>{$form._cmdSubmit.html}</p>
    {$form.endtag}
{/if}

{include file="__mng_footer.tpl"}
