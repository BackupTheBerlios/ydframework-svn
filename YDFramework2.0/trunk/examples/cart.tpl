<html>

<head>

	<title>{$YD_FW_NAMEVERS}</title>

</head>

<body>

	<h3>{$title}</h3>

	{if $result}
		<p style="color: #990000">{$result}</p>
	{/if}

	{if $cart_isempty }

	<p>Cart is empty.</p>

	{else}

	<p>
		The cart has {$cart_count} product(s).
		<a href="{$YD_SELF_SCRIPT}?do=empty">Empty the cart</a>
	</p>

	<table width="500" border="1" cellpadding="5" cellspacing="0" bordercolor="#CCCCCC">
		<tr>
			<td><b>ID</b></td>
			<td><b>Name</b></td>
			<td><b>Quantity</b></td>
			<td><b>Total</b></td>
		</tr>
		{foreach from=$cart_item key="id" item="detail"}
			<tr>
				<td>{$id}</td>
				<td>{$detail.name}</td>
				<td>{$detail.count}</td>
				<td>{$detail.total}</td>
			</tr>
		{/foreach}
		<tr>
			<td colspan="3" align="right"><b>Total:</b></td>
			<td>{$cart_total}</td>
		</tr>
	</table>

	{/if}

	<h3>Products</h3>

	{foreach from=$product key="id" item="detail"}
		<p>
			{$id}. <b>{$detail.name}</b> - $ {$detail.price} - {$detail.quantity} items available
			<br />
			<a href="{$YD_SELF_SCRIPT}?do=add&id={$id}">add one</a> | 
			<a href="{$YD_SELF_SCRIPT}?do=add&id={$id}&quantity=2">add two</a> | 
			<a href="{$YD_SELF_SCRIPT}?do=add&id={$id}&quantity=3">add three</a> | 
			<a href="{$YD_SELF_SCRIPT}?do=rem&id={$id}&quantity=1">remove one</a> | 
			<a href="{$YD_SELF_SCRIPT}?do=rem&id={$id}">remove all</a> | 
			<a href="{$YD_SELF_SCRIPT}?do=modify&id={$id}&quantity=1">set to one</a> |
			<a href="{$YD_SELF_SCRIPT}?do=modify&id={$id}&quantity=4">set to four</a> |
			<a href="{$YD_SELF_SCRIPT}?do=modify&id={$id}&quantity=0">set to none </a> 
		</p>
	{/foreach}

	<p>
		<a href="index.php">other samples</a>
	</p>

</body>

</html>
