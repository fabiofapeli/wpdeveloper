<?php

/**
 *
 * Plugin Name: Custom Notifier
 * Description: Agendar tarefas com o cron do WordPress
 * Version: 1.0
 * Author: Kodame
 * Author URI: http://wordpress.kodame.com.br
 * 
 */

class KDM_Notifier
{
    
    function activation()
    {              
        $when = strtotime( date( '2014-02-07 17:00' ) ); // data que o evendo deverá ocorrer
        $args = array( 
            'topic' => 'amanhã',
            'text'  => 'Amanhã ocorre o evento.'
        );
        wp_schedule_single_event(  // Agendamento de um único evento
                                  $when,
                                 'notifier_event', // id único para ser executado através de uma nova action
                                  $args // Opcional
                                  ); 
        
        $when = strtotime( date( '2014-02-08 17:00' ) );
        wp_schedule_single_event( $when, 'notifier_event' ); // É possível fazer o evento sem a passagem dos parâmetros

        $ts = wp_next_scheduled( 'notifier_comments' ); // Também é possível criar eventos recorrentes
        if ( !$ts ) // Veririfica se ainda não há um evento vinculado a referida tag, caso exista não será necessário fazer o agendamento novamente
            wp_schedule_event( time(), // Momento em que será executado pela primeira vez, no caso de time() no momento de ativação do plugin
                             'daily', // periodicidade que o evento ocorrerá, por padrão o WP oferece também as opções thicedaily e hourly
                              'notifier_comments' // permitirá a criação do gancho que executará a função de callback
                              );
            //Além dos intervalos oferecidos pelo WP (daily, thicedaily e hourly) é possível criar novos intervalos através do gancho 'cron_schedules'
    }

    function deactivation() // Exclusão dos eventos na desativação do plugin
    {
        wp_clear_scheduled_hook( 'notifier_event' );
        $args = array( 
            'topic' => 'amanhã',
            'text'  => 'Amanhã ocorre o evento.'
        );
        wp_clear_scheduled_hook( 'notifier_event', $args ); // Exclui o evento, caso tenha sido passado parâmetros na criação, os mesmos deverão ser passados na exclusão
        
        wp_clear_scheduled_hook( 'notifier_comments' ); 
    }
    
    function setup()
    {
        add_action( 'notifier_event',       array( 'KDM_Notifier', 'event' ) ); // Evento único que chamará o método 'event' após a data ser atingida
        add_action( 'notifier_comments',    array( 'KDM_Notifier', 'comments' ) );
        
        add_filter( 'cron_schedules',       array( 'KDM_Notifier', 'schedules' ) ); // Criação de novos intervalos
        
        add_action( 'init',                 array( 'KDM_Notifier', 'test' ) );
        //Text Domain
        load_plugin_textdomain( 'notifier', // Domínio, identificador único para o ambiente de trdução
                                false, // parâmetro em desuso
                                 CN_DIR . 'lang/' // Diretório onde estarão os arquivos de tradução
                                 );
    }
    
    function event( $args=false )
    {
        if ( !is_array( $args ) ) {
            $args = array( 
                'topic' => 'hoje',
                'text'  => 'O evento é HOJE!'
            );
        }
        file_put_contents( CN_PATH . 'cron.log', $args[ 'text' ], FILE_APPEND );
        // wp_mail( get_option( 'admin_email' ), 'Notificador', $args[ 'text' ] );
    }
    
    function comments() // Notificação cria um log ou uma notificação com a quantidade de comentários que precisão de moderação
    {
        global $wpdb;
        $amount = (int) $wpdb->get_var(
            "SELECT COUNT( comment_ID ) FROM {$wpdb->comments} c "
            . "INNER JOIN {$wpdb->posts} p ON c.comment_post_ID=p.ID "
            . "WHERE c.comment_approved<>'1' AND p.post_status='publish'"
        );
        
        $html = sprintf( 'O site possui %s comentários para moderação.', $amount );
        file_put_contents( CN_PATH . 'cron.log', $html, FILE_APPEND );
        // wp_mail( get_option( 'admin_email' ), 'Notificador', $html );
    }
    
    function schedules( $s ) // Função recebe como parâmetros os intervalos padrão do WP
    {
        $s[ 'weekly' ] = array(
            'interval'  => 60*60*24*7, // Tempo de intervalo
            'display'   => 'Semanalmente' // Descrição
        );
        return $s; // Retorna novo intervalo
        // Para verificar se essa nova periodicidade foi adicionada ao WP podemos usar var_dump( wp_get_schedules() );
    }
    
    function test() // Método chamado através da action 'init' e será executado na parte pública do site
    {
        if ( is_admin() )
            return true;

        // É recomendável que o texto utilizados nas strings seja em Inglês que é o padrão usado em projetos open-souce        

        // Opções que serão traduzidas
        // Funções básicas para tradução do conteúdo
        echo __( //Retorna a tradução da string de acordo com idioma definido pelo wordpress
                'Translatable text ', // String a ser traduzida
                 'notifier' // Text domain
                  ) . '<br />';

        echo _n( // Permite definir um...  
            'Singular', // valor no singular
             'Plural', //  valor no plural
              1, // Valor numérico que irá definir se o texto retornado será no plural ou singular
               'notifier' // Text domain
               ) . '<br />';

        // Ambas funções __() e _n() possui variações _x() e _nx() respectivamente que terá traduções específica para determinado contexto, x se refere a esse contexto
        
        echo _x( 'Text', 'button', 'notifier' ) . '<br />'; // Tradução quando contexto for um botão
        echo _x( 'Text', 'header', 'notifier' ) . '<br />'; // Tradução quando contexto for cabeçalho
        echo _nx( 'Singular', 'Plural', 2, 'reference', 'notifier' ) . '<br />';
        
        // As funções também podem ganhar a função de impressão na tela automática com a inclusão da letra e
        _e( 'Example', 'notifier' ); // mesmo resultado de echo __( 'Example', 'notifier' );
        _ex( 'Example', 'test only', 'notifier' ); // mesmo resultado de echo _x( 'Example', 'test only', 'notifier' );
        
         // echo 'Testando o plugin para agendamento de tarefas através do CRON do WordPress.';
        /*
         Tipos de periodicidade agendadas
        var_dump( wp_get_schedules() );
        */

        /*
        Tipo de agendamento para o gancho específico
        var_dump( wp_get_schedule( 'notifier_comments' ) );
        */

        /*
        Próxima ocorrência de um evento
        $ns = wp_next_scheduled( 'notifier_comments' );
        var_dump( date( 'd/m/Y H:i:s', $ns ) );
        */
        
        /*
        Remover agendamento de uma ocorrência do evento
        $ns = wp_next_scheduled( 'notifier_comments' );
        A diferença para wp_clear_scheduled_hook é que wp_next_scheduled apagará apenas uma ocorrência específica
       */

        /* 
        Remover ocorrência específica do evento 
        wp_unschedule_event( $ns, // timestamp do evento
                             'notifier_comments' // tag referida
                            );

        Também é possível excluir o evento e re-inserir como novas especificações
         wp_schedule_event( time(), 'hourly', 'notifier_comments' );
        */
        
        exit;
    }
    
}

define( 'CN_DIR',   basename( dirname( __FILE__ ) ) . '/' ); // Diretório de instalação do plugin
define( 'CN_PATH',  WP_PLUGIN_DIR . '/' . CPR_DIR . '/' );

register_activation_hook(   __FILE__, array( 'KDM_Notifier', 'activation'   ) );
register_deactivation_hook( __FILE__, array( 'KDM_Notifier', 'deactivation' ) );

add_action( 'plugins_loaded', array( 'KDM_Notifier', 'setup' ) );