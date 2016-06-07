<?php

/**
 *
 * Plugin Name: Login and Register
 * Description: Gerenciamento de usuários e permissões em uma nova área de login e registro.
 * Version: 1.0
 * Author: Kodame
 * Author URI: http://wordpress.kodame.com.br
 * 
 */


class KDM_LR
{

    function activation()
    {
        global $wp_roles; //Na ativação referenciamos as Roles atuais do wordpress
        if ( !isset( $wp_roles->roles[ ROLE_CLIENT ] ) ) { //Verifica se a Role atual esta setada entre eles
            //Definição das Capabilities
            $caps = array( 
                'read' => true, //Permite acessar próprio perfil
                'read_private_posts' => true,// Leitura de conteúdo privado
                'read_private_pages' => true // Leitura de conteúdo privado
            );
            //Inseção a Role dentro do WordPress
            add_role( ROLE_CLIENT, //Identificador da Role 
                    'Cliente', // Nome da role
                    $caps // Capabilities
                    ); 
        }
    }

    function deactivation()
    {
        //Desativação remove a Role
        remove_role( ROLE_CLIENT );
    }
    

    /*
    Para modificar o comportamento do usuário há duas maneiras
    A primeira e modificando a Role, no qual serão alterado dados dentro da tabela wp_options e a segunda é alterando as ações de apenas um dado usuário, nesta serão alterado os dados na tabela de metadados a wp_usermeta.
    */

    function role_update() //Modificando a role (tabela wp_options)
    {
        $c = get_role( ROLE_CLIENT ); //Recupera a role
        if ( is_a( $c, 'WP_Role' ) ) { // Se a Role existir, ou seja, o objeto obtido pertencer a classe WP_Role
            $c->remove_cap( 'read' ); //removendo capabilities, permissão de acesso ao painel
            if ( !$c->has_cap( 'custom-cap' ) ) //Verifica se tem acesso a uma determinada capability
                $c->add_cap( 'custom-cap' ); //Adiciona uma nova capability
        }
    }
    
    function client_update() //Modificando usuário específico (tabela wp_usermeta)
    {
        // $u = wp_get_current_user(); //Recupera um usuário que esta ativo no momento
        $u = new WP_User( 2 ); //recupera usuário passando o id do usuário como parâmetro
        if ( $u->exists() ) { //verifica se foi recuperado um usuário que esta dentro da base de dados                   
            $u->remove_role( 'subscriber' ); //remove role, ou seja, todas as capabilities referente a role serão excluídas do usuário
            $u->add_role( ROLE_CLIENT ); //adiciona role, ou seja, todas as capabilities referente a role serão adicionadas ao usuário
            
            //Adiciona e remove capabilities específicas
            $u->add_cap( 'custom-cap' );
            $u->remove_cap( 'custom-cap' );
            
            $all_caps = $u->get_role_caps(); //recupera todas as capabilities do usuário
            //Com as capabilities em formato de array é possível verificar determinada permissões específicas
            if ( isset( $all_caps[ ROLE_CLIENT ] ) )  //Verifica se determinada role existe
                echo '<p>O usuário é um cliente.</p>';
            
            if ( !$u->has_cap( 'subscriber' ) ) //Também verifica se determinada role existe
                echo '<p>Sem permissão de acesso ao Dashboard!</p>';
                       
            if ( user_can( $u, ROLE_CLIENT ) ) //Também verifica se determinada role existe
                echo '<p>Esse é um dos nossos clientes!</p>';
            
            if ( user_can( $u, 'custom-cap' ) ) //Verifica se determinada capabilite existe
                echo '<p>O cliente pode fazer algo personalizado...</p>';

            //current_user_can('custom-cap') //Verifica se usuário atual possui determinada role ou capability
        }
    }

 
    function setup()
    {  
         //Trabalhando com a API de Reescrita de URLs
        add_filter( 'rewrite_rules_array',  array( 'KDM_LR', 'rules' ) );
        add_filter( 'query_vars',           array( 'KDM_LR', 'vars' ) );
        add_filter( 'template_redirect',    array( 'KDM_LR', 'redirect' ) );
        
        add_filter( 'login_url',            array( 'KDM_LR', 'login_url' ) );  //Chama método no momento em que o formulário de login foi submetido em login.php
        add_filter( 'register_url',         array( 'KDM_LR', 'register_url' ) ); //Chama método no momento em que o formulário de registro foi submetido em register.php
        add_action( 'login_init',           array( 'KDM_LR', 'login_redirect' ) ); //Action para redirecionamento, é acionada antes de iniciar a renderização da tela de login
        
        add_action( 'custom_login',         array( 'KDM_LR', 'authenticate' ) );
        add_action( 'custom_register',      array( 'KDM_LR', 'register' ) );
        
        add_action( 'custom_login',         array( 'KDM_LR', 'logged' ) );
        add_action( 'custom_register',      array( 'KDM_LR', 'logged' ) );
        
        add_action( 'init',                 array( 'KDM_LR', 'init' ) );
    }
    
    //Novas condições para os links logar e registrar
    function rules( $rules )
    {
        $r = array(
            "entrar/?$"     => "index.php?login=1",
            "registrar/?$"  => "index.php?register=1"
        );
        return array_merge( $r, $rules );
    }

    //Cadastro das novas variáveis globais
    function vars( $qv )
    {
        array_push( $qv, 'login' );
        array_push( $qv, 'register' );
        return $qv;
    }

    //Redirecionamento de endereços
    function redirect()
    {
        global $wp_query;
        $file = false;
        $custom = array( 'login', 'register' );
        foreach( $custom as $c ) {
            if ( $wp_query->get( $c ) ) { //Caso um dos dois endereços tenha sido acesso incluí o respectivo arquivo
                $file = $c . '.php';
                break;
            }
        }            
        
        if ( $file ) {
            require LR_PATH . $file;
            exit;
        }
    }
    
    //Método para recuperar url
    function login_url()
    {
        return LR_URL . 'entrar/';
    }
    
    //Método para recuperar url
    function register_url()
    {
        return LR_URL . 'registrar/';
    }
    
    function login_redirect()
    {
        if ( isset( $_GET[ 'action' ] ) && ( $_GET[ 'action' ] == 'logout' ) ) { //verifica se foi acessado link de logout
            wp_logout();
            wp_redirect( wp_login_url() );
            exit;
        }
        require LR_PATH . 'login.php';
        /*
        wp_redirect(wp_login_url()); // Caso queira alterar a url de login
        */
        exit;
    }
    
    function authenticate() //Verifica de login
    {
        if ( empty( $_POST ) || !isset( $_POST[ '_nonce' ] ) )
            return false;
        
        $error = self::check_nonce( 'login' ); //Verificação com Nonce para melhor segurança
        if ( !$error ) {
            global $user; //Pega dados globais de login
            $user = ( isset( $_POST[ '_user' ] ) ) ? sanitize_text_field( $_POST[ '_user' ] ) : '';
            $pass = ( isset( $_POST[ '_pass' ] ) ) ? sanitize_text_field( $_POST[ '_pass' ] ) : '';

            if ( !$user || !$pass ) {
                $error = 'Preencha seu nome e senha para continuar!';                    
            } else {
                $u = wp_signon( //Função para entrada no sistema, recebe credenciais como parâmetro
                    array(
                        'user_login'    => $user,
                        'user_password' => $pass
                    )
                );
                if ( is_wp_error( $u ) ) {
                    $error = 'Dados inválidos';
                    /*
                    Caso se queira mostrar os erros específicos é possível através de get_error_message ou get_error_messages
                    */
                } else {
                    wp_redirect( admin_url( 'profile.php' ) ); //Envia para edição de perfil
                    exit;
                }
            }
        }
        
        if ( $error )
            printf( '<p class="error">%s</p>', $error ); //Imprime erro na tela
    }

    private static function check_nonce( $action )
    {
        $error = false;
         if ( !wp_verify_nonce( $_POST[ '_nonce' ], 'lr-nonce-' . $action ) )
            $error = 'Não foi possível processar sua requisição...';
         
         return $error;
    }
    
    function register() //Verificação de registro do usuário
    {
        if ( empty( $_POST ) || !isset( $_POST[ '_nonce' ] ) ) 
            return false;
        
        global $values;  //Pega dados globais de registro
        $error = self::check_nonce( 'register' ); //Verificação de Nonce
        if ( !$error ) {
            $blank = false;
            $keys = array_keys( $values );
            foreach ( $keys as $k ) {
                $values[ $k ] = ( isset( $_POST[ '_' . $k ]   ) ) ? sanitize_text_field( $_POST[ '_' . $k ] ) : '';
                if ( !$values[ $k ] ) {
                    $blank = true;
                    break;
                }
            }

            if ( $blank ) {
                $error = 'Preencha todos os campos para continuar!';
            } else if ( !is_email( $values[ 'email' ] ) ) {
                $error = 'E-mail inválido!';
            } else if ( $values[ 'pass1' ] !== $values[ 'pass2' ] ) {
                $error = 'As senhas não conferem!';
            } else {
                $u = wp_insert_user( //Insere usuário já com a nova role
                    array(
                        'user_email'    => $values[ 'email' ],
                        'user_login'    => $values[ 'user' ],
                        'user_pass'     => $values[ 'pass1' ],
                        'role'          => ROLE_CLIENT //nova role
                    )
                );
                if ( is_wp_error( $u ) ) {
                    $error = $u->get_error_message();
                } else if ( !is_int( $u ) ) {
                    $error = 'Não foi possível realizar o registro...';
                } else {
                    wp_mail( $values[ 'email' ], 'Bem-vindo', 'Obrigado por se cadastrar em nosso site!' ); //Função do WP para envio de emails, porém a função esta em modo Pluggable, ou seja, será sobreposta por um método interno no plugin
                    printf(
                        '<p class="success">Cadastro realizado com sucesso! <a href="%s" title="Entrar">Entrar</a></p>',
                        wp_login_url()
                    );
                    exit;
                }
            }
        }
        
        if ( $error )
            printf( '<p class="error">%s</p>', $error );
    }
    
    function init()
    {
        $version = '1.0';
        if ( get_option( 'lr-rules' ) !== $version ) {
            update_option( 'lr-rules', $version );
            flush_rewrite_rules();
        }
    }
    
    function logged()
    {
        if ( is_user_logged_in() ) { //Verifica se usuário esta logado
            if ( current_user_can( 'read' ) ) { //verifica se usuário possui determinada permissão
                wp_redirect( admin_url( 'profile.php' ) );
                exit;
            } else {
                wp_die( 'Você está logado!' );
            }
        }
    }
    
}

/*
Funções pluggable são aquelas que o WP permite que seu comportamento possa ser sobreto por algum método do programador.
As funções pluggable estão localizadas na pasta wp-includes/pluggable.php
*/

if ( !function_exists( 'wp_mail' ) ) { //É obrigatório verificar se a função já foi sobreposto por outro plugin 
    
    function wp_mail( $to, $subject, $message ) //Declara nova rotina para a função
    {
        return ( @mail( $to, $subject, $message ) );        
    }

}

function custom_title( $sep='|' )
{
    $screen = ( get_query_var( 'login' ) ) ? 'Entrar' : 'Registrar';
    printf( 
        '%s %s %s',
        get_bloginfo( 'name' ),
        $sep,
        $screen
    );
}

define( 'LR_URL',       site_url() . '/' );

define( 'LR_DIR',       basename( dirname( __FILE__ ) ) );
define( 'LR_PATH',      WP_PLUGIN_DIR . '/' . LR_DIR . '/' );

define( 'ROLE_CLIENT',  'client' ); //1º Criação de uma constante para a Role

define( 'LR_PATH_URL',  plugins_url( LR_DIR ) . '/' );

register_activation_hook( __FILE__, array( 'KDM_LR', 'activation' ) );
register_deactivation_hook( __FILE__, array( 'KDM_LR', 'deactivation' ) );

add_action( 'plugins_loaded', array( 'KDM_LR', 'setup' ) );