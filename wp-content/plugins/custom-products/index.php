<?php

/**
 *
 * Plugin Name: Custom Products
 * Description: Gerenciamento de produtos personalizados.
 * Version: 1.0
 * Author: Kodame
 * Author URI: http://wordpress.kodame.com.br
 * 
 */

class KDM_Products
{

    function activation() // Ativação do plugin
    {
        global $wpdb;
        self::setup_table();
        $sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->products}` (
          `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          `title` varchar(128) NOT NULL,
          `price` float unsigned NOT NULL,
          PRIMARY KEY (`id`)
        )";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' ); //localização da função dbDelta
        dbDelta( $sql ); //para criação de tabela é recomendado o uso da função dbDelta, que executará uma série de verificações em relação a instrução sql para que a inserção seja feita de forma correta
    }

    function deactivation() // Desativação do plugin
    {
        global $wpdb;
        self::setup_table();
        $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->products}" ); // Exclusão da tabela
    }
    
    private static function setup_table() // método usado para referenciar a tabela de products, que será executado a cada carregamento do plugin
    {
        global $wpdb;
        $wpdb->products = $wpdb->prefix . 'products'; //atribui tabela a uma variável de acesso público
        array_push( $wpdb->tables, $wpdb->products ); //incluindo a tabela products na listagem de tabelas da classe $wpdb
        //Assim sempre que precisarmos referenciar a tabela poderá ser feito através de $wpdb->products
    }
    
    function setup()
    {
        add_action( 'init', array( 'KDM_Products', 'init' ) );
        
        add_action( 'admin_menu',           array( 'KDM_Products_List_table', 'admin_menu' ) ); //Chamada do método para inserção do menu Produtos
        add_filter( 'set-screen-option',    array( 'KDM_Products_List_table', 'set_option' ), 10, 3 ); //Filtro para adicionar personalizada de paginação de resultados
    }
    
    function init()
    {
        self::setup_table();
        /*
        Métodos de consultas personalizados da classe $wpdb

        Retornar um único valor
        $r  = $wpdb->get_var("SELECT COUNT(id) FROM {$wpdb->posts} WHERE post_type='post'");


        Retorna valores em formato de array
        $r  = $wpdb->get_results("SELECT user_email, display_name FROM {$wpdb->users} ORDER BY display_name");

        Retorna um único regsitro
        $r  = $wpdb->get_row("SELECT user_email, display_name FROM {$wpdb->users} ORDER BY display_name");

        Retorna um único coluna
        $r  = $wpdb->get_col("SELECT user_email, display_name FROM {$wpdb->users} ORDER BY display_name", 1); // 1 Retorna a coluna display_name e 0 para recuperar coluna user_email

        Essas consultas podem ser realizadas utilizando o método $wpdb->prepare que atuará como a função printf ou sprintf do php
        $r  = $wpdb->get_results(
                 $wpdb->prepare("SELECT user_email, display_name FROM {$wpdb->users} WHERE user_email=%s ORDER BY display_name",
                 'contato@cyberway.com.br')
        );
        */

    }
   
}

define( 'LR_DIR',       basename( dirname( __FILE__ ) ) );
define( 'LR_PATH',      WP_PLUGIN_DIR . '/' . LR_DIR . '/' );

require LR_PATH . 'list-table.php'; //arquivo responsável pela listagem de produtos no formato WP_List_Table

register_activation_hook( __FILE__, array( 'KDM_Products', 'activation' ) );
register_deactivation_hook( __FILE__, array( 'KDM_Products', 'deactivation' ) );

add_action( 'plugins_loaded', array( 'KDM_Products', 'setup' ) );