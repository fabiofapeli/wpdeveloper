<!DOCTYPE HTML>
<html dir="ltr" lang="pt-BR">
<head>
    <meta charset="UTF-8" />
        <title>Hyacinth</title>
        <meta name="keywords" content="" />
        <meta name="description" content="" />
        <?php wp_head();?>
    </head>
    <body>
        <div id="header-wrapper">
            <div id="header" class="container">
                <div id="logo">
                    <h1><?php 
                        printf('<a href="%1$s" title="%2$s">%2$s</a>', PW_URL, PW_SITE_NAME)
                     ?></h1>
                    <span><?php bloginfo('description') ?></span>
                </div>
                <div id="menu">
                    <?php wp_nav_menu() ?>
                </div>
            </div>
        </div>