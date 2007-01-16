<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>

  <title>{$title}</title>

  <link href="fffscreen.css" rel="stylesheet" type="text/css" media="all" />

</head>

<body>
<div id="fff_main_head">
		<h3>Friendly Freddy Forum</h3>
		<div id="fff_main_menu">
		  {if $user->rightslevel == LEVEL_ADMIN}
				{if $viewadmin}
					{$menus}
				{else}
					<a href="forums.php?do=ListForums" class="fff_link1">{t w="global.forumlist"}</a> {$menus}
				{/if}
		  {else}
		  	» <a href="forums.php?do=ListForums" class="fff_link1">{t w="global.forumlist"}</a> {$menus}
		  {/if}
		
		</div>	
		
		<div id="fff_main_user">
		
			  {if $user->authentificated==1}
		
				  {t w="global.connectedas"} {$user->login} -
		
				  {if $user->rightslevel == LEVEL_ADMIN}
						{if $viewadmin}
							<a href="forums.php?do=ListForums" class="fff_link1">{t w="global.returnforumlist"}</a> -
						{else}
							<a href="forums.php?do=search" class="fff_link1">{t w="global.search"}</a> - 
							<a href="user.php?do=monprofil" class="fff_link1">{t w="global.profile"}</a> -
							<a href="admin.php" class="fff_link1">{t w="global.admin"}</a> -
						{/if}
				  {else}
				  			<a href="forums.php?do=search" class="fff_link1">{t w="global.search"}</a> - 
				  			<a href="user.php?do=monprofil" class="fff_link1">{t w="global.profile"}</a> -
				  {/if}
		
				   <a href="{$YD_SELF_SCRIPT}?do=logout" class="fff_link1">{t w="global.logout"}</a>
		
			  {else}
			   	  <a href="forums.php?do=search" class="fff_link1">{t w="global.search"}</a> - 
			   	
				  <a href="{$YD_SELF_SCRIPT}?do=login" class="fff_link1">{t w="global.login"}</a> - 
		
				  <a href="user.php?do=suscribe" class="fff_link1">{t w="global.suscribe"}</a>
		
			  {/if}
		
		</div>
		&nbsp;
</div>
<br class="fff_separator" />

{$content}

    <div style="margin-left:auto;margin-right:auto;margin-top:40px;text-align:center"><small>FFF is powered by </small><a href="http://ydframework.berlios.de/" title="Yellow Duck Framework"><img class="fff_img" src="images/ydframework.png"/></div>
</body>
</html>
