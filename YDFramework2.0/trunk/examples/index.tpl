
<html>

<head>

    <title>{$YD_FW_NAMEVERS}</title>

    {literal}<style>
        td { border-bottom: 1px solid #FFCC00; }
        P,TD,LI,INPUT,SELECT,TEXTAREA{background-color:#FFE9A6;font:13px/1.3 Verdana}
        BODY{color:#000000;background-color:#FFCC00;font:13px/1.3 Verdana}
        A{color:#993333}
        UL,OL{margin-top:0px;margin-bottom:0px;padding-top:0px;padding-bottom:0px}
        FORM,H1,H2,H3,H4,H5{margin:0px;padding:0px}
        .indent{margin-left:40px}
        .additions{color:#008800}
        .deletions{color:#880000}
        .error{color:#CC3333;font-weight:bold}
        .header{padding:10px;padding-top:0px}
        .comment{background-color:#EEEEEE;padding:10px}
        .commentinfo{color:#AAAAAA}
        .code{background:#FFE9A6;border:solid #888888 2px;color:black;width:99%;height:300px;overflow:scroll;padding:3px;font:10pt "Courier New"}
        .notes{color:darkred}
        .revisioninfo{color:#AAAAAA;padding-bottom:20px}
        .copyright{padding-top:8px;font-size:11px;color:#444444;text-align:right}
        .copyright A{color:#444444}
        .page,.commentform{background-color:#FFE9A6;padding:10px}
        .footer,.commentsheader{background-color:#444444;padding:5px 10px;color:#FFCC00}
        .footer A,.commentsheader A{color:#FFCC00}
    </style>{/literal}

</head>

<body>

    <div class="header">
        <h2>Yellow Duck Framework 2.0</h2>
    </div>

    <div class="page">

    {if $file}

        <h3>source code: {$file}</h3>
        <p>{$source}</p>
        <p><a href="{$YD_SELF_SCRIPT}">go back</a></p>

    {else}

        <h3>{$YD_FW_NAMEVERS}</h3>

        <p><b>Documentation</b></p>

        <p>View the <a href="../doc/api/index.html">API documentation</a> or read the
        <a href="../doc/userguide/index.html">user guide</a>.</p>

        <p>View the <a href="../license.txt">License information</a>.</p>

        <p>For more information, please visit:<br/>
        <a href="{$YD_FW_HOMEPAGE}">{$YD_FW_HOMEPAGE}</a></p>

        <table border="0" width="650" cellpadding="3"  cellspacing="0">
        <tr>
            <td colspan="2"><b>&nbsp;<br/>Writing your first application</b></td>
            <td width="43%"><b>&nbsp;</b></td>
        </tr>
        <tr>
            <td width="7%" rowspan="2" valign="top">&nbsp;</td>
            <td width="50%" valign="top"><a href="firstapp/index.php">Tutorial application</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=firstapp/index.php">firstapp/index.php</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=firstapp/index.tpl">firstapp/index.tpl</a>
            </td>
        </tr>
        <tr>
            <td valign="top"><a href="database/index.php">Tutorial application using a database</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=database/index.php">database/index.php</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=database/index.tpl">database/index.tpl</a>
            </td>
        </tr>
        <tr>
            <td colspan="2"><b>&nbsp;<br/>YDBase: the base class</b></td>
            <td width="43%"><b>&nbsp;</b></td>
        </tr>
        <tr>
            <td valign="top">&nbsp;</td>
            <td valign="top"><a href="base.php">Using the YDBase object</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=base.php">base.php</a>
            </td>
        </tr>
        <tr>
            <td colspan="2"><b>&nbsp;<br/>YDConfig: the global configuration</b></td>
            <td width="43%"><b>&nbsp;</b></td>
        </tr>
        <tr>
            <td valign="top">&nbsp;</td>
            <td valign="top"><a href="config.php">Using the YDConfig object</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=config.php">config.php</a>
            </td>
        </tr>
        <tr>
            <td colspan="2"><b>&nbsp;<br/>How requests are processed</b></td>
            <td width="43%"><b>&nbsp;</b></td>
        </tr>
        <tr>
            <td rowspan="4" valign="top">&nbsp;</td>
            <td valign="top"><a href="sample.php">Defining and using action requests</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=sample.php">sample.php</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=sample.tpl">sample.tpl</a>
            </td>
        </tr>
        <tr>
            <td valign="top"><a href="phpinfo.php">Showing the PHP information</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=phpinfo.php">phpinfo.php</a>
            </td>
        </tr>
        <tr>
            <td valign="top"><a href="extending.php">Extending the YDRequest base class</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=extending.php">extending.php</a>
            </td>
        </tr>
        <tr>
            <td valign="top"><a href="multi_apps/index.php">Multiple applications</a></td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=multi_apps/_includes/YDF2_include.php">multi_apps/_includes/YDF2_include.php</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=multi_apps/index.php">multi_apps/index.php</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=multi_apps/index.tpl">multi_apps/index.tpl</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=multi_apps/std_footer.tpl">multi_apps/std_footer.tpl</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=multi_apps/std_header.tpl">multi_apps/std_header.tpl</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=multi_apps/app1/index.php">multi_apps/app1/index.php</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=multi_apps/app1/index.tpl">multi_apps/app1/index.tpl</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=multi_apps/app2/index.php">multi_apps/app2/index.php</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=multi_apps/app2/index.tpl">multi_apps/app2/index.tpl</a>
            </td>
        </tr>
        <tr>
            <td colspan="2"><b>&nbsp;<br/>Using templates</b></td>
            <td width="43%"><b>&nbsp;</b></td>
        </tr>
        <tr>
            <td rowspan="3" valign="top">&nbsp;</td>
            <td valign="top"><a href="template_smarty.php">Using Smarty templates</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=template_smarty.php">template_smarty.php</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=template_smarty.tpl">template_smarty.tpl</a>
            </td>
        </tr>
        <tr>
            <td valign="top"><a href="template_php.php">Using PHP templates with short open tags</a><br />
                             <a href="template_php.php?do=complete">Using PHP templates without short open tags</a></td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=template_php.php">template_php.php</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=template_php.tpl">template_php.tpl</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=template_php2.tpl">template_php2.tpl</a>
            </td>
        </tr>
        <tr>
            <td valign="top"><a href="cache/index.php">Smarty caching</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=cache/index.php">cache/index.php</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=cache/index.tpl">cache/index.tpl</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=cache/subdir/index.php">cache/subdir/index.php</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=cache/subdir/index.tpl">cache/subdir/index.tpl</a>
            </td>
        </tr>
        <tr>
            <td colspan="2"><b>&nbsp;<br/>Using and validating forms</b></td>
            <td width="43%"><b>&nbsp;</b></td>
        </tr>
        <tr>
            <td rowspan="9" valign="top">&nbsp;</td>
            <td valign="top"><a href="form.php">Form handling and validation</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=form.php">form.php</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=form.tpl">form.tpl</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=form_selector.tpl">form_selector.tpl</a>
            </td>
        </tr>
        <tr>
            <td valign="top"><a href="form2.php">Form handling and validation (new style)</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=form2.php">form.php</a>
            </td>
        </tr>
        <tr>
            <td valign="top"><a href="form_dateselect.php">YDForm using a combination of date elements</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=form_date.php">form_date.php</a>
            </td>
        </tr>
        <tr>
            <td valign="top"><a href="form_get.php">YDForm using GET parameters</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=form_get.php">form_get.php</a>
            </td>
        </tr>
        <tr>
            <td valign="top"><a href="form_rule.php">YDForm using form rules</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=form_rule.php">form_rule.php</a>
            </td>
        </tr>
        <tr>
            <td valign="top"><a href="form_compare.php">YDForm using compare rules</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=form_compare.php">form_compare.php</a>
            </td>
        </tr>
        <tr>
            <td valign="top"><a href="fileupload.php">Handling file uploads</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=fileupload.php">fileupload.php</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=fileupload.tpl">fileupload.tpl</a>
            </td>
        </tr>
        <tr>
            <td valign="top"><a href="form_modified.php">Getting the modified values</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=form_modified.php">form_modified.php</a>
            </td>
        </tr>
        <tr>
            <td valign="top"><a href="validaterules.php">Validation rules</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=validaterules.php">validaterules.php</a>
            </td>
        </tr>
        <tr>
            <td colspan="2"><b>&nbsp;<br/>Using logfiles</b></td>
            <td width="43%"><b>&nbsp;</b></td>
        </tr>
        <tr>
            <td valign="top">&nbsp;</td>
            <td valign="top"><a href="logging.php">Using file logging</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=logging.php">logging.php</a>
            </td>
        </tr>
        <tr>
            <td colspan="2"><b>&nbsp;<br/>Handling authentication</b></td>
            <td width="43%"><b>&nbsp;</b></td>
        </tr>
        <tr>
            <td rowspan="3" valign="top">&nbsp;</td>
            <td valign="top"><a href="login/index.php">Using authentication</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=login/includes/MyLoginRequest.php">login/includes/MyLoginRequest.php</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=login/index.php">login/index.php</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=login/index.tpl">login/index.tpl</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=login/login.tpl">login/login.tpl</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=login/userinfo.php">login/userinfo.php</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=login/userinfo.tpl">login/userinfo.tpl</a>
            </td>
        </tr>
        <tr>
            <td valign="top"><a href="auth_ipcheck.php">Authentication based on IP numbers</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=auth_ipcheck.php">auth_ipcheck.php</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=auth_ipcheck.tpl">auth_ipcheck.tpl</a>
            </td>
        </tr>
        <tr>
            <td valign="top"><a href="http_auth/index.php">Basic HTTP Authentication</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=http_auth/includes/MyLoginRequest.php">http_auth/includes/MyLoginRequest.php</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=http_auth/index.php">http_auth/index.php</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=http_auth/index.tpl">http_auth/index.tpl</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=http_auth/userinfo.php">http_auth/userinfo.php</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=http_auth/userinfo.tpl">http_auth/userinfo.tpl</a>
            </td>
        </tr>
        <tr>
            <td colspan="2"><b>&nbsp;<br/>Connecting to and using databases</b></td>
            <td width="43%"><b>&nbsp;</b></td>
        </tr>
        <tr>
            <td rowspan="9" valign="top">
            <p>&nbsp;</p></td>
            <td valign="top"><a href="database.php">Database connectivity - Native functions</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=database.php">database.php</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=database.tpl">database.tpl</a>
            </td>
        </tr>
        <tr>
            <td valign="top"><a href="database1.php">Database connectivity - YD MySQL library</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=database1.php">database1.php</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=database1.tpl">database1.tpl</a>
            </td>
        </tr>
        <tr>
            <td valign="top"><a href="database2.php">Database connectivity - YD SQLite library</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=database2.php">database2.php</a>
            </td>
        </tr>
        <tr>
            <td valign="top"><a href="database3.php">Database connectivity - YD Oracle library</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=database3.php">database3.php</a>
            </td>
        </tr>
        <tr>
            <td valign="top"><a href="database4.php">Database connectivity - YD PostgreSQL library</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=database4.php">database4.php</a>
            </td>
        </tr>
        <tr>
            <td valign="top"><a href="database_paging.php">Paged recordsets - YD MySQL library</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=database_paging.php">database_paging.php</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=database_paging.tpl">database_paging.tpl</a>
            </td>
        </tr>
        <tr>
            <td valign="top"><a href="array_paging.php">Paged arrays</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=array_paging.php">array_paging.php</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=array_paging.tpl">array_paging.tpl</a>
            </td>
        </tr>
        <tr>
            <td valign="top"><a href="array_paging_with_sorting.php">Paged arrays with sorting</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=array_paging_with_sorting.php">array_paging_with_sorting.php</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=array_paging_with_sorting.tpl">array_paging_with_sorting.tpl</a>
            </td>
        </tr>
        <tr>
            <td valign="top">
                <a href="database_backup.php">Making MySQL database backups</a><br/>
                <a href="database_backup.php?do=restore">Restoring a database backup</a><br/>
            </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=database_backup.php">database_backup.php</a>
            </td>
        </tr>
        <tr>
            <td colspan="2"><b>&nbsp;<br/>Using files, images and directories</b></td>
            <td width="43%"><b>&nbsp;</b></td>
        </tr>
        <tr>
            <td rowspan="5" valign="top">&nbsp;</td>
            <td valign="top"><a href="path.php">YDPath module</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=path.php">path.php</a>
            </td>
        </tr>
        <tr>
            <td valign="top"><a href="fsfile.php">YDFSFile object</a> <br/>
              <a href="fsfile.php?do=download">YDFSFile object - File download</a> <br/>
              <a href="fsfile.php?do=download2">YDFSFile object - File download with changed name</a><br/>
              <a href="fsfile.php?do=downloadinline">YDFSFile object - Inline File download</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=fsfile.php">fsfile.php</a>
            </td>
        </tr>
        <tr>
            <td valign="top"><a href="fsdirectory.php">YDFSDirectory object</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=fsdirectory.php">fsdirectory.php</a>
            </td>
        </tr>
        <tr>
            <td valign="top"><a href="fsdirectory2.php">More examples for the YDFSDirectory object</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=fsdirectory2.php">fsdirectory2.php</a>
            </td>
        </tr>
        <tr>
            <td valign="top"><a href="fsimage.php">YDFSImage object</a> <br/>
              <a href="fsimage.php?do=thumbnail1">YDFSImage object - thumbnail 1</a> <br/>
              <a href="fsimage.php?do=thumbnail2">YDFSImage object - thumbnail 2</a> <br/>
              <a href="fsimage.php?do=thumbnail3">YDFSImage object - thumbnail 3 (no caching)</a> <br/>
              <a href="fsimage.php?do=thumbnailsave">YDFSImage object - thumbnail saving</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=fsimage.php">fsimage.php</a>
            </td>
        </tr>
        <tr>
            <td colspan="2"><b>&nbsp;<br/>Using URLs and downloading data</b></td>
            <td width="43%"><b>&nbsp;</b></td>
        </tr>
        <tr>
            <td rowspan="2" valign="top">&nbsp;</td>
            <td valign="top"><a href="url.php">YDUrl object</a> <br/>
              <a href="url.php?do=image1">YDUrl object - Image 1</a> <br/>
              <a href="url.php?do=headers">YDUrl object - Headers</a> <br/>
              <a href="url.php?do=status">YDUrl object - Status</a> <br/>
              <a href="url.php?do=alter">YDUrl object - Altering URLs</a> <br/>
              <a href="url.php?do=merge">YDUrl object - Merging URLs</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=url.php">url.php</a>
            </td>
        </tr>
        <tr>
            <td valign="top"><a href="pbase.php">Combining YDUrl and YDArrayUtil</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=pbase.php">pbase.php</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=pbase.tpl">pbase.tpl</a>
            </td>
        </tr>
        <tr>
            <td colspan="2"><b>&nbsp;<br/>Using XML/RPC clients and servers</b></td>
            <td width="43%"><b>&nbsp;</b></td>
        </tr>
        <tr>
            <td rowspan="2" valign="top">&nbsp;</td>
            <td valign="top"><a href="xmlrpcclient.php">XML/RPC client</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=xmlrpcclient.php">xmlrpcclient.php</a>
            </td>
        </tr>
        <tr>
            <td valign="top"><a href="xmlrpcserver.php">XML/RPC server</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=xmlrpcserver.php">xmlrpcserver.php</a>
            </td>
        </tr>
        <tr>
            <td colspan="2"><b>&nbsp;<br/>Constructing and sending emails</b></td>
            <td width="43%"><b>&nbsp;</b></td>
        </tr>
        <tr>
            <td valign="top">&nbsp;</td>
            <td valign="top"><a href="email.php">Sending emails</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=email.php">email.php</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=email.tpl">email.tpl</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=email_template.tpl">email_template.tpl</a>
            </td>
        </tr>
        <tr>
            <td colspan="2"><b>&nbsp;<br/>Creating RSS and ATOM feeds</b></td>
            <td width="43%"><b>&nbsp;</b></td>
        </tr>
        <tr>
            <td valign="top">&nbsp;</td>
            <td valign="top"><a href="feedcreator.php">YDFeedCreator object</a> <br/>
              <a href="feedcreator.php?do=rss091">YDFeedCreator object - RSS 0.91 output</a> <br/>
              <a href="feedcreator.php?do=rss10">YDFeedCreator object - RSS 1.0 output</a> <br/>
              <a href="feedcreator.php?do=rss20">YDFeedCreator object - RSS 2.0 output</a> <br/>
              <a href="feedcreator.php?do=atom">YDFeedCreator object - Atom output</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=feedcreator.php">feedcreator.php</a>
            </td>
        </tr>
        <tr>
            <td colspan="2"><b>&nbsp;<br/>YDCart: a shopping cart</b></td>
            <td width="43%"><b>&nbsp;</b></td>
        </tr>
        <tr>
            <td valign="top">&nbsp;</td>
            <td valign="top"><a href="cart.php">YDCart object</a></td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=cart.php">cart.php</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=cart.tpl">cart.tpl</a>
            </td>
        </tr>
        <tr>
            <td colspan="2"><b>&nbsp;<br/>YDLocale: internationalization</b></td>
            <td width="43%"><b>&nbsp;</b></td>
        </tr>
        <tr>
            <td valign="top">&nbsp;</td>
            <td valign="top"><a href="locale.php">Using the YDLocale object</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=locale.php">locale.php</a>
            </td>
        </tr>
        <tr>
            <td colspan="2"><b>&nbsp;<br/>YDDatabaseObject</b></td>
            <td width="43%"><b>&nbsp;</b></td>
        </tr>
        <tr>
            <td valign="top" rowspan="2">&nbsp;</td>
            <td valign="top"><a href="database_object/index.php">YDDatabaseObjects</a></td>
            <td valign="top">step by step tutorial</td>
        </tr>
        <tr>
            <td valign="top"><a href="database_query.php">YDDatabaseQuery class</a></td>
            <td valign="top"><a href="{$YD_SELF_SCRIPT}?do=source&id=database_query.php">database_query.php</a></td>
        </tr>
        <tr>
            <td colspan="2"><b>&nbsp;<br/>YDDate</b></td>
            <td width="43%"><b>&nbsp;</b></td>
        </tr>
        <tr>
            <td valign="top" rowspan="1">&nbsp;</td>
            <td valign="top"><a href="dates.php">Using the YDDate and YDDateUtil classes</a></td>
            <td valign="top"><a href="{$YD_SELF_SCRIPT}?do=source&id=dates.php">dates.php</a></td>
        </tr>
        <tr>
            <td colspan="2"><b>&nbsp;<br/>YDTimer</b></td>
            <td width="43%"><b>&nbsp;</b></td>
        </tr>
        <tr>
            <td valign="top" rowspan="1">&nbsp;</td>
            <td valign="top"><a href="timer.php">Using timers/markers</a> <br/>
              <a href="timer.php?YD_DEBUG=1">Using timers/markers (debug 1)</a> <br/>
              <a href="timer.php?YD_DEBUG=2">Using timers/markers (debug 2)</a> <br/>
              <a href="timer.php?do=standaloneTimer">Using a standalone timers</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=timer.php">timer.php</a>
            </td>
        </tr>
        <tr>
            <td colspan="2"><b>&nbsp;<br/>YDGraph</b></td>
            <td width="43%"><b>&nbsp;</b></td>
        </tr>
        <tr>
            <td valign="top" rowspan="1">&nbsp;</td>
            <td valign="top">
                <a href="graph.php">Sample graph</a> <br/>
                <a href="graph.php?do=demo1">Area chart</a> <br/>
                <a href="graph.php?do=demo2">Area and line chart</a> <br/>
                <a href="graph.php?do=demo3">Bar chart</a> <br/>
                <a href="graph.php?do=demo4">Multiple bar chart</a> <br/>
                <a href="graph.php?do=demo5">Bar and line chart</a> <br/>
                <a href="graph.php?do=demo6">Step and dot chart</a> <br/>
                <a href="graph.php?do=demo7">Line chart</a> <br/>
                <a href="graph.php?do=demo8">Line and dot chart</a> <br/>
                <a href="graph.php?do=demo9">Multiple lines chart</a> <br/>
                <a href="graph.php?do=demo10">Impuls chart</a> <br/>
                <a href="graph.php?do=demo11">Impuls and dots chart</a> <br/>
                <a href="graph.php?do=demo12">Impuls, dots and lines chart</a> <br/>
            </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=graph.php">graph.php</a>
            </td>
        </tr>
        <tr>
          <td colspan="2"><b>&nbsp;<br/>
            YDXMLForm</b></td>
          <td><b>&nbsp;</b></td>
        </tr>
        <tr>
          <td valign="top" rowspan="1">&nbsp;</td>
          <td valign="top"><a href="form_xml.php">Creating forms using XML</a> <br/>
          </td>
          <td valign="top"><a href="{$YD_SELF_SCRIPT}?do=source&id=form_xml.php">form_xml.php</a> </td>
        </tr>
        <tr>
          <td colspan="2"><b>&nbsp;<br/>
            YDAjax</b></td>
          <td><b>&nbsp;</b></td>
        </tr>
        <tr>
          <td valign="top" rowspan="7">&nbsp;</td>
          <td valign="top"><a href="ajax/version.php">Simple ajax example</a><br/>
          </td>
          <td valign="top"><a href="{$YD_SELF_SCRIPT}?do=source&id=ajax/version.php">ajax/version.php</a> </td>
        </tr>
        <tr>
          <td valign="top"><a href="ajax/two_buttons.php">Two buttons with different events </a><br/>
          </td>
          <td valign="top"><a href="{$YD_SELF_SCRIPT}?do=source&id=ajax/two_buttons.php">ajax/two_buttons.php</a> </td>
        </tr>
        <tr>
          <td valign="top"><a href="ajax/calculator.php">Sum calculator </a><br/>
          </td>
          <td valign="top"><a href="{$YD_SELF_SCRIPT}?do=source&id=ajax/calculator.php">ajax/calculator.php</a> </td>
        </tr>
        <tr>
          <td valign="top"><a href="ajax/calculator_dynamic.php">Four operations calculator </a><br/>
          </td>
          <td valign="top"><a href="{$YD_SELF_SCRIPT}?do=source&id=ajax/calculator_dynamic.php">ajax/calculator_dynamic.php</a> </td>
        </tr>
        <tr>
          <td valign="top"><a href="ajax/cars.php">Dependency between select elements</a><br/>
          </td>
          <td valign="top"><a href="{$YD_SELF_SCRIPT}?do=source&id=ajax/cars.php">ajax/cars.php</a> </td>
        </tr>
        <tr>
          <td valign="top"><a href="ajax/date_calculator.php">Date calculator using a datetimeselect</a><br/>
          </td>
          <td valign="top"><a href="{$YD_SELF_SCRIPT}?do=source&id=ajax/date_calculator.php">ajax/date_calculator.php</a> </td>
        </tr>
        <tr>
          <td valign="top"><a href="ajax/form.php">Form submittion and validation</a><br/>
          </td>
          <td valign="top"><a href="{$YD_SELF_SCRIPT}?do=source&id=ajax/form.php">ajax/form.php</a> </td>
        </tr>
        <tr>
            <td colspan="2"><b>&nbsp;<br/>YDXml</b></td>
            <td width="43%"><b>&nbsp;</b></td>
        </tr>
        <tr>
            <td valign="top">&nbsp;</td>
            <td valign="top"><a href="xml_example.php">YDXml - working with XML strings</a> <br/>
              <a href="xml_example.php?do=file">YDXml - working with XML files</a> <br/>
              <a href="xml_example.php?do=url">YDXml - working with XML urls</a> <br/>
              <a href="xml_example.php?do=array">YDXml - working with XML arrays</a> <br/>
              <a href="xml_example.php?do=save">YDXml - saving XML files</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=xml_example.php">xml_example.php</a>
            </td>
        </tr>
        <tr>
            <td colspan="2"><b>&nbsp;<br/>Other classes and modules</b></td>
            <td width="43%"><b>&nbsp;</b></td>
        </tr>
        <tr>
            <td rowspan="13" valign="top">&nbsp;</td>
            <td valign="top"><a href="constants.php">YDFramework2 constants</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=constants.php">constants.php</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=constants.tpl">constants.tpl</a>
            </td>
        </tr>
        <tr>
            <td valign="top"><a href="browserinfo.php">Showing the browser information</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=browserinfo.php">browserinfo.php</a>
            </td>
        </tr>
        <tr>
            <td valign="top"><a href="arrayutil.php">Array utilities</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=arrayutil.php">arrayutil.php</a>
            </td>
        </tr>
        <tr>
            <td valign="top"><a href="bbcode.php">BBCode conversion</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=bbcode.php">bbcode.php</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=bbcode.txt">bbcode.txt</a>
            </td>
        </tr>
        <tr>
            <td valign="top"><a href="stacktrace.php">Using the YDStacktrace function</a> </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=stacktrace.php">stacktrace.php</a>
            </td>
        </tr>
        <tr>
            <td valign="top"><a href="stringutil.php">StringUtil class</a> </td>
            <td valign="top"><a href="{$YD_SELF_SCRIPT}?do=source&id=stringutil.php">stringutil.php</a> </td>
        </tr>
        <tr>
            <td valign="top"><a href="ajax.php">Using AJAX with the {$YD_FW_NAME}</a></td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=ajax.php">ajax.php</a>
                <br/>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=ajax.tpl">ajax.tpl</a>
            </td>
        </tr>
        <tr>
            <td valign="top"><a href="encryption.php">Encryption and decryption</a></td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=encryption.php">encryption.php</a>
            </td>
        </tr>
        <tr>
            <td valign="top"><a href="persistent.php">Using persistent data</a></td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=persistent.php">persistent.php</a>
            </td>
        </tr>
        <tr>
            <td valign="top"><a href="pdfreport.php">Using PDF reports</a></td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=pdfreport.php">pdfreport.php</a>
            </td>
        </tr>
        </table>

    {/if}

    </div>

</body>

</html>