<?php

/**
 *
 * Plugin Name: Custom Security
 * Description: Trabalhar as funções de validação do WordPress
 * Version: 1.0
 * Author: Kodame
 * Author URI: http://wordpress.kodame.com.br
 * 
 */


/*
São um conjunto de funções que garante que os dados enviados por um usuário foram realmentes feitos de uma página específica e não por uma fonte desconhecida.
Esse é um tipo de ataque feito por Hackers conhecida como CSRF (do inglês Cross-site request forgery - Falsificação de solicitação entre sites)

*/

class KDM_Security
{
    
    function setup()
    {
        add_action( 'admin_menu', array( 'KDM_Security', 'admin_menu' ) );
    }
    
    function admin_menu()
    {
        add_menu_page( 'Validação', 'Validação', 'administrator', 'cs', array( 'KDM_Security', 'options' ) );
    }
    
    function options()
    {       
        // Nonces em URL
        /*
        Devemos gerar uma variável GET que será a chave de identificação da página através de wp_nonce_url

        $url = wp_nonce_url( 
                admin_url( 'admin.php?page=cs' ), //Url a ser identificada
                 'cs_nonce' // nome da chave
                 );

        $n = $_GET[ '_wpnonce' ]; //recuperamos a variável passada via GET
        
        if ( wp_verify_nonce( $n, 'cs_nonce' ) ) //Verifica se a variável corresponde ao valor da chave
            echo 'Válido!';
        */
        
        // Uso de Nonces em sessions, cookies e arquivos externos
        /*
        if ( !session_id() ) //Verifica se a sessão foi iniciada
            session_start(); //Caso não tenha sido é iniciada uma nova sessão
        
        if ( !isset( $_SESSION[ 'custom_nonce' ] ) ) { //verifica se existe a sessão referente a Nonce
            $_SESSION[ 'custom_nonce' ] = wp_create_nonce( 'cs_nonce' ); // Gera uma nova Nonce
        }
        
        $n = $_SESSION[ 'custom_nonce' ]; //recuperamos a variável passada via GET
        
        if ( wp_verify_nonce( $n, 'cs_nonce' ) ) //Verifica se o Nonce é válido
                echo 'Nonce válido!';

        //Da mesma forma que geramos o Nonce de Session, poderíamos usar o mesmo modelo para gerar através de Cookies ou arquivo externo
        */
        
        // Nonces em campos de formulário
        /*
        Nonces são amplamente utilizadas em formulários a fim de evitar Spams, sobrecarga de servidor e qualquer outro tipo de vunerabilidade.
        A seguir a verificação de um campo Nonce passado via formulário
        */
        //$n = $_POST[ '_nonce' ]; //código desatualizado

        $n = ( isset( $_POST[ 'cs_nonce' ] ) ) ? $_POST[ '_nonce' ] : false;
 
        //if ( wp_verify_nonce( $n, 'cs_nonce' ) ) //código desatualizado
        if ( $n && wp_verify_nonce( $n, 'cs_nonce' ) )
            echo 'Tudo ok...';

        // Verificação da origem de envio dos dados
        /*
        $referer = wp_get_referer(); //recupera a URL de origem dos dados, com isso conseguimos verificar se a mesma pertence ao domínio do site
        $s = 'http://' . $_SERVER[ 'SERVER_NAME' ];
        $pos = strpos( $referer, $s );
        if ( !is_int( $pos ) || $pos > 0 )
            echo 'Origem inválida...';
        else
            echo 'Origem válida';
        */
        
        // Nonces dentro do Dashboard
        /*
        Dentro do Dashboard opte por usar a função check_admin_referer ao invés de wp_verify_nonce ], que garantirá que as requisições estão sendo realmente feitas no Dashboard
        */

        //if ( !check_admin_referer( 'cs_nonce', '_nonce' ) ) //código desatualizado
        if ( $n && !check_admin_referer( 'cs_nonce', '_nonce' ) )
            wp_die( 'Interrompendo o envio das informações...' );
        
        if ( !empty( $_POST ) ) {
            if ( $_POST[ '_str' ] ) {
                echo '<p>Apenas texto: ' . $_POST[ '_str' ] . '</p>';
                
                printf(
                    '<p>Apenas texto: %s</p>',
                    sanitize_text_field( $_POST[ '_str' ] ) //Recupera somente texto, exclui tags, espaços e quebras de linha
                );
            }
            
            if ( $_POST[ '_attr' ] ) {
                echo '<p>Uso de um atributo <a href="#" title="' . $_POST[ '_attr' ] . '">nesse link</a> e nada mais.</p>';
                
                printf(
                    '<p>Uso de um atributo <a href="#" title="%s">nesse link</a> e nada mais.</p>',
                    esc_attr( $_POST[ '_attr' ] ) //Formata conteúdo para ser exibido como um atributo dentro de uma tag html, trata principalmente  as aspas, evitando perca da estrutura html
                );
            }
            
            if ( $_POST[ '_email' ] ) {
                echo '<p>E-mail: ' . $_POST[ '_email' ] . '</p>';
                
                $email = sanitize_email( $_POST[ '_email' ] ); //retorna o email caso seja um endereço válido
                printf(
                    '<p>E-mail: %s</p>',
                    $email
                );
                
                printf(
                    '<p>E-mail: %s</p>',
                    antispambot( $email ) //Codifica o endereço a fim de que o mesmo seja capturado por robots que vasculham email para compor maillings
                );
                
                printf(
                    'Esse e-mail %s é válido!',
                    ( is_email( $email ) ) ? 'sim, ' : 'não' //semelhante a sanitize_email
                );
            }
            
            if ( $_POST[ '_html' ] ) {
                echo '<p>HTML: ' . $_POST[ '_html' ] . '</p>';
                
                printf(
                    '<p>HTML: %s</p>',
                    esc_html( $_POST[ '_html' ] ) //escapa tags para que seja exibida como carácter, evitando sql injections e outros tipos de códigos maliciosos
                );
            }
            
            if ( $_POST[ '_text' ] ) {
                echo '<p>Texto: ' . $_POST[ '_text' ] . '</p>';
                
                $allowed_tags = array( 
                    'b' => array(),
                    'a' => array(
                        'href' => array()
                    ) 
                );
                printf(
                    '<p>Texto: %s</p>',
                    wp_kses( $_POST[ '_text' ], $allowed_tags, array( 'http', 'https' ) ) //Limpa todas tags html, porém permite definir tags que poderão ser aceitas e respectivos atributos e tipo de dados como no caso protocolos
                );
            }
            
            if ( $_POST[ '_editor' ] ) {
                echo '<p>Editor: ' . $_POST[ '_editor' ] . '</p>';
                
                printf(
                    '<p>Editor: %s</p>',
                    wp_strip_all_tags( $_POST[ '_editor' ], true ) //Tratamento de dados enviados pelo editor do WP, inicialmente limpa todas tags html, o segundo parâmetro é opcional, em caso de true tira todos os espaços e quebras de linha
                );
            }
        }
        ?>
        <h2>Validação de dados</h2>
        <form method="post" action="<?php echo admin_url( 'admin.php?page=cs' ); ?>">
            <?php
            //Campo Nonce de formulário
             wp_nonce_field( 'cs_nonce', //Nome da chave
                             '_nonce', //Nome do campo
                              true //parâmetro opcional gera um campo de referência, quando setado como true carrega como informação a página atual, certifica de sempre usar esse parâmetro para garantir que a url de origem seja entregue na url de destino 
                              ); 
             ?>
            <table class="form-table">
                <tr>
                    <th>Apenas texto</th>
                    <td><textarea name="_str" cols="50" rows="5"></textarea></td>
                </tr>
                <tr>
                    <th>Atributo</th>
                    <td><input type="text" name="_attr" /></td>
                </tr>
                <tr>
                    <th>E-mail</th>
                    <td><input type="text" name="_email" /></td>
                </tr>
                <tr>
                    <th>Tag HTML</th>
                    <td><input type="text" name="_html" /></td>
                </tr>
                <tr>
                    <th>Texto</th>
                    <td><textarea name="_text" cols="50" rows="5"></textarea></td>
                </tr>
                <tr>
                    <th>Editor HTML</th>
                    <td><?php wp_editor( '', // Conteúdo inicial
                     '_editor', //Nome do campo
                      array( 'media_buttons' => false ) // Opções para personalização da barra de formatação
                       ); ?></td>
                </tr>
                <tr>
                    <td colspan="2"><?php submit_button(); ?></td>
                </tr>
            </table>
        </form>
        <?php
    }
    
}

add_action( 'plugins_loaded', array( 'KDM_Security', 'setup' ) );