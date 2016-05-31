<!DOCTYPE HTML>
<html dir="ltr" lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title><?php echo wp_title( '|', false, 'right' ) . PW_SITE_NAME;?></title>
    <?php wp_head(); ?>
</head>
<body>
<div id="wrapper">
	<div id="header-wrapper">
		<div id="header">
			<div id="logo">
				<h1><?php 
				printf('<a href="%1$s" title="%2$s">%2$s</a>',
				PW_URL,
				PW_SITE_NAME);
				 ?></h1>
				<p><?php bloginfo('description'); ?></p>
			</div>
		</div>
	</div>
		<!-- end #header -->
	<div id="menu">
		<?php    
			$args = array(
				'theme_location' => 'menu-header',
				'walker'		 => new KDM_Menu_Walker()
			);
		
			wp_nav_menu( $args ); ?>
	</div>
	<!-- end #menu -->
	<div id="page">
		<div id="page-bgtop">
			<div id="page-bgbtm">