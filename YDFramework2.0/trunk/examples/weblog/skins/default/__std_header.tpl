<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head profile="http://gmpg.org/xfn/1">

    <title>{$weblog_title}</title>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="generator" content="{$YD_FW_NAMEVERS}" />

    <link rel="stylesheet" href="{$skin_dir}/default.css" type="text/css" media="screen" />

    <style type="text/css" media="screen">
        body        {literal}{{/literal} background: url("{$image_dir}/kubrickbgcolor.jpg");                                    {literal}}{/literal}
        #page       {literal}{{/literal} background: url("{$image_dir}/kubrickbg.jpg") repeat-y top; border: none;              {literal}}{/literal}
        #header     {literal}{{/literal} background: url("{$image_dir}/kubrickheader.jpg") no-repeat bottom center;             {literal}}{/literal}
        #footer     {literal}{{/literal} background: url("{$image_dir}/kubrickfooter.jpg") no-repeat bottom; border: none;      {literal}}{/literal}
        #header     {literal}{{/literal} margin: 0 !important; margin: 0 0 0 1px; padding: 1px; height: 198px; width: 758px;    {literal}}{/literal}
        #headerimg  {literal}{{/literal} margin: 7px 9px 0; height: 192px; width: 740px;                                        {literal}}{/literal}
        .bbtextarea {literal}{{/literal} width: 650px; height: 160px;                                                           {literal}}{/literal}
    </style>

    <link rel="alternate" type="application/rss+xml"  title="RSS 2.0"  href="{$weblog_link_rss}"  />
    <link rel="alternate" type="text/xml"             title="RSS 0.92"  href="{$weblog_link_rss}"  />
    <link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="{$weblog_link_atom}" />

</head>

<body>

    <div id="page">

        <div id="header">
            <div id="headerimg">
                <h1><a href="{$weblog_link}" title="{$weblog_title}">{$weblog_title}</a></h1>
                <div class="description">
                    {if $weblog_description}{$weblog_description}{else}{t w="another_powered_by"}{/if}
                </div>
            </div>
        </div>

        <hr />
