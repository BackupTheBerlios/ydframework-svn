<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>{$_title}</title>
{foreach from=$_css_link item="css"}
<link rel="stylesheet" type="text/css" href="{$css}" media="all" />
{/foreach}
<style type="text/css" media="all">
<!--
{literal}

body {
    width: 700px;
    margin: 18px;
    padding: 0px;
}

#header {
    
}

#header p.description {
    
}

#contents {
    
}

#footer {
    margin: 30px 0px;
} 

body, p, .normal, li, td, th, span {
    font-size: 10pt;
    line-height: 13pt;
}

form {
    padding: 0; 
    margin: 0;
}

p {
    margin: 0px 0px 10px 0px
}

a {
    color: #417690;
    text-decoration: underline;
    font-weight: normal;
    cursor: pointer;
}

select, input, textarea, body, p, .normal, li, td {
    font-family: "Lucida Sans Unicode", "Lucida Grande", "Lucida Sans", Verdana, Arial, Sans-Serif;
}

.disabled {
    color: gray;
}

.error {
    color: black;
    background-color: rgb(255, 223, 223);
    padding: 10px;
    border-bottom: 1px solid rgb(230, 162, 162);
}

.alert {
    color: black;
    background-color: #CAE1FF;
    padding: 10px;
    border-bottom: 1px solid #8DB6CD;
}

.subline {
    color: gray;
    font-size: 8pt;
}

input, textarea, select {
    margin:2px;
    padding:2px;
    border-right:1px solid #ccc;
    border-bottom:1px solid #ccc;
    border-left:2px solid #999;
    border-top:2px solid #999;
    background: #FFF;
    font-size: 10pt;
}

td.checkbox input, td.radio input {
    border: none;
}

td.radio input {
    margin-right: 5px;
    margin-left: 0px;
    vertical-align: middle;
}

input.submit {
    background: white;
    padding: 2px 10px;
    font-weight: bold;
    border-left:1px solid #ccc;
    border-top:1px solid #ccc;
    border-right:2px solid #999;
    border-bottom:2px solid #999;
}

input.button {
    padding: 2px 10px;
    background: white;
    border-left:1px solid #ccc;
    border-top:1px solid #ccc;
    border-right:2px solid #999;
    border-bottom:2px solid #999;
}

textarea {
    width: 700px;
    height: 200px;
}

td.text input {
    width: 400px;
}

td.left, td.right, td.span, td.textarea {
    border-bottom: 1px solid #DDDDDD;
    text-align: left;
    padding: 4px 0px 4px 4px;
}

h3 {
    border-bottom: 1px solid #DDDDDD;
    border-top: 1px solid #DDDDDD;
    padding: 4px;
    text-align: left;
    font-weight: bold;
    font-size: 11pt;
}

h2 {
    text-align: left;
    color: #417690;
    margin: 0px;
    padding: 0px 0px 10px 0px;
}

h1 {
    font-size: 18pt;
    margin: 0px;
    padding: 0px 0px 10px 0px;
}

td.div {
    border-bottom: 1px solid #DDDDDD;
    border-top: 1px solid #DDDDDD;
    font-weight: bold;
    font-size: 10pt;
    background: #F4F4F4;
    padding: 4px;
}

td.span {
    padding-bottom: 8px;
    border: 0;
}


.sep {
    width: 700px;
    border-bottom: 1px solid #DDDDDD;
    height: 1px;
    line-height: 0;
}

#header .sep {
    border-bottom: 5px solid #DDDDDD;
}

pre, p.code {
    margin: 0px 0 10px 0px;
    padding: 5px 10px;
    border-left: 5px solid #DDDDDD;
    font-size: 8pt;
    line-height: 12pt;
    font-family: courier;
}

pre, p.code {
    width: 700px;
    margin: 15px 0 15px 0;
}

ul, ol {
    margin: 5px 10px 10px 15px;
    padding: 5px 10px 10px 15px;
}
li {
    margin: 0px 0px 2px 2px;
    padding: 0px 0px 2px 2px;
}
{/literal}
{foreach from=$_css item="css"}

{$css}
   
{/foreach}
-->
</style>
{foreach from=$_js_link item="js"}
<script src="{$js}"></script>
{/foreach}
{if $_js}
<script type="text/javascript" language="JavaScript">
<!--
{foreach from=$_js item="js"}

{$js}
   
{/foreach}
-->
</script>
{/if}
</head>

<body>

<div id="header">

    <h1>{$_title}</h1>
    {if $_description}<p class="description">{$_description}</p>{/if}
    
    <p class="sep"></p>

</div>

<div id="contents">

{if $_errors}
<p class="error">
{foreach from=$_errors item="error"}
&middot; {$error} <br />
{/foreach}
</p>
{/if}

{foreach from=$_items item="item"}
    {if $item.type == 'header'}<h2{$item.attributes}>{$item.info}</h2>{/if}
    {if $item.type == 'text'}<p{$item.attributes}>{$item.info|nl2br}</p>{/if}
    {if $item.type == 'alert'}<p{$item.attributes}>{$item.info|nl2br}</p>{/if}
    {if $item.type == 'pre'}<pre{$item.attributes}>{$item.info}</pre>{/if}
    {if $item.type == 'list'}<ul>{foreach from=$item.info item="li"}<li{$item.attributes}>{$li}</li>{/foreach}</ul>{/if}
    {if $item.type == 'num_list'}<ol>{foreach from=$item.info item="li"}<li{$item.attributes}>{$li}</li>{/foreach}</ol>{/if}
    {if $item.type == 'php'}<p>{$item.info}</p>{/if}
    {if $item.type == 'separator'}<p class="sep"></p>{/if}
    {if $item.type == 'image'}<p><img src="{$item.info}"{$item.attributes} /></p>{/if}
    {if $item.type == 'link'}<p><a{$item.attributes}>{$item.info}</a></p>{/if}
    {if $item.type == 'button'}<input type="button" value="{$item.info}"{$item.attributes} />{/if}
    {if $item.type == 'html'}{$item.info}{/if}
{/foreach}

{if $_show_form}
    {$_form}
{/if}

</div>

<div id="footer" class="subline">
    Powered by <a href="{$YD_FW_HOMEPAGE}" class="subline" target="_blank">{$YD_FW_NAME}</a>
    <br />
    {$YD_FW_COPYRIGHT}
</div>

</body>
</html>