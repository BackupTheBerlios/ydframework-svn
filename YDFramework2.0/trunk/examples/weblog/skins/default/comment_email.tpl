<html>

<head>

    <title>{$weblog.title}</title>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="generator" content="{$YD_FW_NAMEVERS}" />

    <link rel="stylesheet" href="{$skin_dir}/default.css" type="text/css" media="screen" />

    <style type="text/css" media="screen">
        body        {literal}{{/literal} background: url("{$image_dir}/kubrickbgcolor.jpg");                                 {literal}}{/literal}
        #page       {literal}{{/literal} background: url("{$image_dir}/kubrickbgwide.jpg") repeat-y top; border: none;       {literal}}{/literal}
        #header     {literal}{{/literal} background: url("{$image_dir}/kubrickheader.jpg") no-repeat top center;             {literal}}{/literal}
        #footer     {literal}{{/literal} background: url("{$image_dir}/kubrickfooter.jpg") no-repeat bottom; border: none;   {literal}}{/literal}
        #header     {literal}{{/literal} margin: 0 !important; margin: 0; padding: 1px; height: 0px; width: 758px;           {literal}}{/literal}
        #headerimg  {literal}{{/literal} margin: 0; height: 0px; width: 740px;                                               {literal}}{/literal}
        .bbtextarea {literal}{{/literal} width: 650px; height: 160px;                                                        {literal}}{/literal}
    </style>

</head>

<body>

    <div id="page">

        <div id="header"></div>

        <div id="content" class="narrowcolumn">

            <h3>{t w="new_comment"}: {$item.title}</h3>

            <p>
                <b>{t w="weblog"}</b>: <a href="{$weblog_link}">{$weblog_title}</a><br/>
                <b>{t w="item_title"}</b>: <a href="{$item|@link_item}">{$item.title}</a><br/>
            </p>

            <p>{$eml_comment.username} {t w="wrote"}:</p>

            <blockquote><i>{$eml_comment.comment|bbcode}</i></blockquote>

            &nbsp;

        </div>

        <div id="footer"></div>

    </div>

</body>

</html>

