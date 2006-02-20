{include file="__std_header.tpl"}

{assign var="max_img_size" value="450"}

{capture assign="browsebar"}
    {if $image->previous or $image->next}
        <div class="navigation">
            {if $image->previous}
                <div class="alignleft">&laquo; <a href="{$YD_SELF_SCRIPT}?id={$image->previous->relative_path}">{t w="previous"}</a></div>
            {/if}
            {if $image->next}
                <div class="alignright"><a href="{$YD_SELF_SCRIPT}?id={$image->next->relative_path}">{t w="next"}</a> &raquo;</div>
            {/if}
        </div>
    {/if}
{/capture}

<style type="text/css" media="screen">
    #page {literal}{{/literal} background: url("{$image_dir}/kubrickbgwide.jpg") repeat-y top; border: none; {literal}}{/literal}
</style>

<div id="content" class="widecolumn">

    {$browsebar}

    <div class="post">

        <h2 id="post-{$item.id}"><a href="{$item|@link_item}">{$item.title}</a> &raquo; {$image->getBasenameNoExt()}</h2>

        <div class="entrytext">

            <p align="center">
            {if $image->getWidth() > $max_img_size}
                <iframe src="{$uploads_dir}/{$image->relative_path}" width="{$max_img_size}" height="{$image->getHeight()+20}" marginwidth="0" marginheight="0"></iframe>
            {else}
                <img src="{$uploads_dir}/{$image->relative_path}">
            {/if}
            </p>

            {$browsebar}

            <p class="postmetadata alt">
                <small>
                    {t w="image"}: {$image->getBaseName()}
                    ({t w="image"} {$image->num} {t w="of"} {$image->total_images})
                    <br/>
                    {t w="item"}: <a href="{$item|@link_item}">{$item.title}</a>
                    {if $image->exif}
                        <br/>
                        EXIF: {$image->exif.Model}
                        {$image->exif.ExposureTime|replace:' sec':'s'}
                        {$image->exif.ApertureValue|replace:' ':'/'} {t w="at"}
                        {$image->exif.FocalLength|replace:' ':''}
                        ISO-{$image->exif.ISOSpeedRatings}
                    {/if}
                </small>
            </p>

            <p align="center"><a href="{$item|@link_item_comment}">{$item|@text_num_comments:true}</a></p>

        </div>

    </div>

</div>

{include file="__std_footer.tpl"}
