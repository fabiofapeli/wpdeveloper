<!DOCTYPE HTML>
<html dir="ltr" lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title><?php custom_title(); ?></title>
    <link type="image/x-icon" rel="icon" href="<?php echo LR_PATH_URL; ?>favicon.ico" />
    <link rel="stylesheet" type="text/css" href="<?php echo LR_PATH_URL; ?>style.css" />
    <?php do_action( 'custom_head' ); //Permite inserir novas ações para alterar o comportamento da tela sem alterar o plugin ?>
</head>

<body>
    <div class="wrap">