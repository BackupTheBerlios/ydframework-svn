<html>

<head>

    <title>{$weblog.title}</title>

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

            <h3>[spam] {t w="new_comment"}<br/>{$item.title}</h3>
            <p><b>{t w="posted_from"}</b><br/>{$comment.userip}</p>
            <p><b>User Agent</b><br/>{$comment.useragent|default:'-'}</p>
            <p><b>Request URI</b><br/>{$comment.userrequrl|default:'-'}</p>
            <p><b>{t w="name"}</b><br/>{$comment.username|default:'-'}</p>
            <p><b>{t w="mail"}</b><br/>{$comment.useremail|default:'-'}</p>
            <p><b>{t w="website"}</b><br/>{$comment.userwebsite|default:'-'}</p>
            <p><b>{t w="created_on"}</b><br/>{$comment.created|date:'%Y/%m/%d %H:%M:%S'}</p>
            <p><b>{t w="weight"}</b><br/>{$comment.comment|strlen}</p>
            <p><b>{t w="comment"}</b><br/>{$comment.comment|htmlentities|default:'-'}</p>

            <h3>$_GET</h3>
            {foreach from=$smarty.get item="item" key="key"}
                <p><b>{$key}</b><br/>{$item}</p>
            {/foreach}

            <h3>$_POST</h3>
            {foreach from=$smarty.post item="item" key="key"}
                <p><b>{$key}</b><br/>{$item}</p>
            {/foreach}

            <h3>$_SERVER</h3>
            {foreach from=$smarty.server item="item" key="key"}
                <p><b>{$key}</b><br/>{$item}</p>
            {/foreach}

            <h3>$_COOKIE</h3>
            {foreach from=$smarty.cookies item="item" key="key"}
                <p><b>{$key}</b><br/>{$item}</p>
            {/foreach}

            <h3>$_ENV</h3>
            {foreach from=$smarty.env item="item" key="key"}
                <p><b>{$key}</b><br/>{$item}</p>
            {/foreach}

            <h3>$_SESSION</h3>
            {foreach from=$smarty.session item="item" key="key"}
                <p><b>{$key}</b><br/>{$item}</p>
            {/foreach}

            &nbsp;

        </div>

        <div id="footer"></div>

    </div>

</body>

</html>

