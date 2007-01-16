<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
<head><title></title></head>
<body>
<p><br />
This is a date calculator</p>
<form {$form.attribs}>
<table width="100%"  border="0" cellpadding="3">
  <tr>
    <td width="400" align="center">{$form.currentdate.html}</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="center">{$form.operation.html}</td>
    <td align="left">{$form.mybutton.html}&nbsp;&nbsp;&nbsp;&nbsp;{$form.myspanresult.html}</td>
  </tr>
  <tr>
    <td align="center">{$form.number.html}{$form.type.html}</td>
    <td>&nbsp;</td>
  </tr>
</table>
</form>
</body>
</html>
