{if $currentAction == 'show'}
    <h1>Admin Home</h1>
    {foreach from=$adminMenu item="menuItem"}
        <p>
            {if $menuItem.url}
                <a href="{$menuItem.url}"><b>{$menuItem.title}</b></a>
            {else}
                <b>{$menuItem.title}</b>
            {/if}
            {foreach from=$menuItem.children item="menuSubItem"}
                <br/>
                {if $menuSubItem.url}
                    <a href="{$menuSubItem.url}">{$menuSubItem.title}</a>
                {else}
                    {$menuSubItem.title}
                {/if}
            {/foreach}
        </p>
    {/foreach}
{/if}

{if $currentAction == 'modules'}
    <h1>Modules</h1>
    <h2>Core Modules</h2>
    <ul>
        {foreach from=$modules item=module}
            {if $module->isRequired()}
                <li><p>
                    <b>{$module->name} {t w="version"} {$module->version}</b>
                    <br/>
                    {t w="created_by"} {$module->authorName}, {$module->authorUrl|url}
                    <br/>
                    {$module->description}
                </p></li>
            {/if}
        {/foreach}
    </ul>
    <h2>Optional Modules</h2>
    <ul>
        {foreach from=$modules item=module}
            {if not $module->isRequired()}
                <li><p>
                    <b>{$module->name} {t w="version"} {$module->version}</b>
                    <br/>
                    {t w="created_by"} {$module->authorName}, {$module->authorUrl|url}
                    <br/>
                    {$module->description}
                </p></li>
            {/if}
        {/foreach}
    </ul>
{/if}

{if $currentAction == 'settings'}
    <h1>Settings</h1>
{/if}
