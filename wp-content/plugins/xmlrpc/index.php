<?php

/**
 *
 * Plugin Name: Remote Control
 * Description: Como utilizar XML-RPC
 * Version: 1.0
 * Author: Kodame
 * Author URI: http://wordpress.kodame.com.br
 *
 */

//add_action( 'plugins_loaded', 'rc_setup' );

function rc_setup()
{
    add_action( 'init',             'rc_init' );
    
    add_filter( 'xmlrpc_methods',   'rc_methods' ); // Filtro para inclusão de métodos personalizados
}

function rc_init()
{
    if ( is_admin() ) // Lado público do site
        return true;
    
   // $r = xmlrpc_request( 'demo.sayHello' ); // Método do servidor sayHello
    
   // $r = xmlrpc_request( 'wp.getPageTemplates' ); // Visualização do modelo default de determinado método  
    
   // $r = xmlrpc_request( 'wp.getPost', array( 2 ) ); // Recuperação de um post específico com id 2


    // Além dos métodos de consulta podemos fazer modificações no servidor remoto, através de métodos de inserção, remoção e atualização
    // Inserção de uma nova página    
    $args = array(
        'title'         => 'Teste de XML-RPC',
        'description'   => 'Texto de conteúdo do post',
        'excerpt'       => 'Um breve resumo',
        'post_type'     => 'page'
    );


    $r = xmlrpc_request( 'metaWeblog.newPost', $args ); 
    
    printf( '<pre>%s</pre>', esc_html( $r ) ); // Exibição do valor retornado
    exit;
}

function xmlrpc_request( $method, $p=array() ) // Configurações de acesso ao servidor
{
    $url = 'http://wpdeveloper.com.br/xmlrpc.php'; // URL de destino
    $data = array(
        'user' => 'xmlrpc', // usuário
        'pass' => 'xmlrpc' // senha
    );
    
    $params = array( 0, $data[ 'user' ], $data[ 'pass' ] );
    ( count( $p ) == 1 ) ? $params = array_merge( $params, $p ) : array_push( $params, $p );

    $request = xmlrpc_encode_request( $method, $params ); // Preparamos o envio da requisição passando o método e parâmetros codificados no formato xmlrpc
    
    $args = array(
        'sslverify' => false,
        'timeout'   => 5,
        'body'      => $request
    );
    $r = wp_remote_post( $url, $args ); // Requisição do método via HTTP
    if ( is_array( $r ) && isset( $r[ 'body' ] ) )
        return $r[ 'body' ]; // Verificação da resposta
    else if ( is_wp_error( $r ) )
        return $r->get_error_message(); // Caso de erro exibe mensagem
   
}

function rc_methods( $methods ) // Novo método personalizado
{
    $methods[ 'customMethod' ] = 'rc_custom_method';
    
    // unset( $methods[ 'demo.sayHello' ] );
    // unset( $methods[ 'demo.addTwoNumbers' ] );
     
    return $methods;   
}

function rc_custom_method( $args ) // Função de callback para o novo método rc_methods, será possível inserir qualquer comportamento
{
    if ( !isset( $args[ 'test' ] ) )
        return false;
    
    // Rotinas do método personalizado
    return $args[ 'msg' ]; // Resposta de retorno
}
