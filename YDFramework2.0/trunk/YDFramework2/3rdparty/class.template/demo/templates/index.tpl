<% config_load file="test.conf" %>
<% assign name="title" value="foo" %>
<% include file="header.tpl"%>
<PRE>

<% assign name="foo" value="up" %>
<% assign name="bar" value=0 %>
01: <% $lala %> (// Array)
02: <% $lala[up] %> (// first entry)
03: <% $lala[$foo] %> (// first entry)
04: <% $lala[#blah#] %> (// first entry)
05: <% #snah# %> (// 0)
06: <% #bold# %> (// Array)
07: <% #bold[0]# %> (// up)
08: <% #bold[$bar]# %> (// up)
09: <% #bold[#snah#]# %> (// up)
10: <%* a comment <%* inside a comment *%> *%>

<%* bold and title are read from the config file *%>
<% if #bold# %><b><% /if %>
<%* capitalize the first letters of each word of the title *%>
Title: <% #title#|capitalize %>
<% if #bold# %></b><% /if %>

<% literal %>this is a block of literal text<% /literal %>

The current date and time is <% $_TPL[NOW]|date:"Y-m-d H:i:s" %>

The value of global assigned variable $SCRIPT_NAME is <% $_TPL[SERVER][SCRIPT_NAME] %>

Example of accessing server environment variable SERVER_NAME: <% $_TPL[SERVER][SERVER_NAME] %>

The value of <% ldelim %> $Name <% rdelim %> is <b><% $Name %></b>

variable modifier example of <% ldelim %> $Name|upper <% rdelim %>

<b><% $Name|upper %></b>


An example of a foreach loop:

<% foreach value=value from=$FirstName %>
	<% $value %>
<% foreachelse %>
	none
<% /foreach %>

An example of foreach looped key values:

<% foreach value=value from=$contacts %>
	phone: <% $value[phone] %><br>
	fax: <% $value[fax] %><br>
	cell: <% $value[cell] %><br>
<% /foreach %>
<p>

testing strip tags
<% strip %>
<table border=0>
	<tr>
		<td>
			<A HREF="<% $_TPL[SERVER][SCRIPT_NAME] %>">
			<font color="red">This is a  test     </font>
			</A>
		</td>
	</tr>
</table>
<% /strip %>

</PRE>

<% include file="footer.tpl" %>
