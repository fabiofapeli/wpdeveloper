<?php

/**
 *
 * Plugin Name: Custom Editor
 * Description: Personalizar o editor
 * Version: 1.0
 * Author: Kodame
 * Author URI: http://wordpress.kodame.com.br
 * 
 */

add_action( 'plugins_loaded', 'ce_setup' );

function ce_setup()
{
    add_action( 'admin_print_footer_scripts',   'ce_quicktags' ); 
    // Para personalização do editor visual a inserção de um novo botão passa por dois filtros
    add_filter( 'mce_buttons',          'register_button' );
    add_filter( 'mce_external_plugins', 'add_tinymce_plugin' );
}

function ce_quicktags()
{
    if ( wp_script_is( 'quicktags' ) ) {  // verificamos se a quicktag esta incorparada a página atual
    ?>
    <script type="text/javascript">
    QTags.addButton( 'ce_pre', // id único
                     'pre', // Rótulo
                      '<pre lang="php">', // Texto de abertura
                       '</pre>', // texto de fechamento
                        'q', // tecla de acesso
                         'Texto pré-formatado', // Título 
                          130 // posicionamento, para verificar as posições atuais podemos ir em wp-includes/js/quicktags.js
                     );
    // Botão incoporará funções específicas
    QTags.addButton( 'ce_alert', 'alerta', custom_fn, // Funçõa de callback
                     false, false, 'Alerta' );
    function custom_fn() {
        alert( 'Teste de botão' );
    }
    </script>   
    <?php
    } 
}

function register_button( $buttons ) // Recebe todos os botões por parâmetro
{
    array_push( $buttons, 'custom_button' ); // Incorpora novo botão
    return $buttons; // Retornamos os botões com o novo botão incorporado
}

function add_tinymce_plugin( $plugins ) // Inserção de um novo plugin
{
    $plugins[ 'custom_button' ] = plugin_dir_url( __FILE__ ) . 'ce.js'; // Mesmo id 'custom_button' do botão personalizado e fará referência a um arquivo js externo
    return $plugins;
}