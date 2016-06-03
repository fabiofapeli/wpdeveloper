<?php

/**
 *
 * Plugin Name: Custom Post Ratings
 * Description: Permite aos usuários cadastrados votar nos posts
 * Version: 1.0
 * Author: Kodame
 * Author URI: http://wordpress.kodame.com.br
 * 
 */

class KDM_Ratings
{

    function activation()
    {
    }

    function deactivation()
    {
    }

    function setup()
    {
        add_action( 'wp',                   array( 'KDM_Ratings', 'init' ) );
        add_action( 'wp_ajax_vote',         array( 'KDM_Ratings', 'vote' ) ); //somente usuário logados conseguirão fazer a votação
        //add_action( 'wp_ajax_nopriv_vote',array( 'KDM_Ratings', 'vote' ) ); //Nome do gancho caso o plugin tivesse alguma função offline
        
        add_shortcode( 'ratings',           array( 'KDM_Ratings', 'shortcode' ) );
        
        add_action( 'widgets_init',         array( 'KDM_Ratings', 'widget' ) ); //Através do gancho 'widgets_init' registramos o widget através de metódo widget
        
        // Admin Bar
        add_action( 'admin_menu',           array( 'KDM_Ratings', 'admin_menu' ) );
        
        /*
        Iremos personalizar a barra de ferramentas através de um gancho que irá chamar o método admin_bar
        */
        add_action( 'admin_bar_menu',       array( 'KDM_Ratings', 'admin_bar' ), 250 ); /*250 corresponde a prioridade de execução
        No método add_menus da classe WP_Admin_Bar em wp-inludes/class-wp-admin-bar.php é possível ver as atuais prioridades de cada
        item do menu, para o nosso caso basta incluir um valor superior ao demais 
        */
        
        // WP_Screen
        //Para exibir a opções de tela será realizado um filtro
        add_filter( 'screen_settings',      array( 'KDM_Ratings', 'screen_options' ) );
        
        // Personalização do Dashboard
        add_action( 'wp_dashboard_setup',   array( 'KDM_Ratings', 'dashboard' ) );
        // remove_action( 'welcome_panel',     'wp_welcome_panel' );
        
        // Pointers
        add_action( 'wp_ajax_pointer_hide', array( 'KDM_Ratings', 'pointer_hide' ) ); //Ação será enviada via ajax e ao ser intecptado pelo gancho chamará o método pointer_hide
        add_action( 'admin_enqueue_scripts',array( 'KDM_Ratings', 'admin_scripts' ) ); //Gancho para inserir no Dashboard script referente ao pointer
        
        // oEmbed
        /*
        Abaixo registramos um novo manipulador para incorporãção de conteúdo remoto
        De acordo com a Expressão Regular será captado qualquer conteúdo após a url e enviado para o método embed
        */
        wp_embed_register_handler( 'ted', '#http://www\.ted\.com/(.*)#i', array( 'KDM_Ratings', 'embed' ) );
        
        add_filter( 'embed_defaults',       array( 'KDM_Ratings', 'embed_defaults' ) ); /* Filtro irá chamar método para definição de valores padrão para os vídeos  */

        /*
        Visto que o primeiro carregamento de um vídeo referente a uma nova fonte gera uma certa lentidão, pois o WP primeiro requisita a url do embed e só depois faz a incorporação, podemos então registrar um novo provider referente a fonte a fim de melhorar a performance de carregamento dos vídeos:
        */
        add_action( 'init',                 array( 'KDM_Ratings', 'init_embed' ) );

        /*
        Exibição de formulário de votação através de um shotcode
        add_shortcode('ratings', array('KDM_Ratings','shortcode'));

        Como já visto anteriormente é possível ocultar a barra no lado oculto através de 'show_admin_bar'
Outra forma de ocultar a barra é usando 

        //show_admin_bar(false);

        O usuário poderá ocultar manualmente a barra através do dashboard em Usuários > Barra de ferramentas
        */
       
       remove_action( 'welcome_panel', 'wp_welcome_panel' ); //Remove tela de boas-vindas da tela inicial do dashboard
    }

    function init()
    {
        if ( is_user_logged_in() ) {
            
           add_filter( 'the_content', array( 'KDM_Ratings', 'stars' ), 1, 1 ); //Insere filtro no conteúdo do post

             //Inserção de estilos e scripts reponsável por formatar o formulário e requisições ajax
            $url = plugins_url( CPR_DIR ) . '/';
            wp_register_script( 'cpr-script', $url . 'js/post-ratings.js', array( 'jquery' ), null ); //cpr-script dependência de Jquery
            wp_register_style( 'cpr-style', $url . 'css/post-ratings.css', array(), null, 'screen' );

            if ( is_single() ) { 
                wp_enqueue_style( 'cpr-style' ); 
                wp_enqueue_script( 'cpr-script' );
            }
        }
    }

    //Método responsável por exibir as estrelas dentro do tema
    function stars( $content, $opt=null )
    {
        if ( is_admin() || !is_single() )
            return $content;

        global $post;
        $value = (float) get_post_meta($post->ID, 'cpr-avg', true);
        $int_value = round( $value );

        $html = '<p class="custom-post-ratings">';
            
        if ( isset( $opt[ 'title' ] ) )
            $html .= $opt[ 'title' ];
        
        $html .= '<span id="post-rating" class="ref-' . $post->ID . '">';

        for ( $i = 1; $i <= 5; $i++ ) {
            $html .= '<span id="star-' . $i . '"';

            if ( $int_value >= $i )
                $html .= ' class="on"';

            $html .= '></span>';
        }

        if ( isset( $opt[ 'avg' ] ) && $opt[ 'avg' ] )
            $html .= ' Média: <em id="cpr-avg">' . number_format( $value, 2, ',', '' ) . '</em>';

        if ( isset( $opt[ 'votes' ] ) && $opt[ 'votes' ] ) {
            $votes = (int) get_post_meta( $post->ID, 'cpr-votes', true );
            $html .= ' Total de votos: <em id="cpr-votes">' . $votes . '</em>';
        }

        $html .= '</span></p>';
        return $html . $content;
    }

    //Método responsável pela votação
    function vote() 
    {
        $rating = (int) $_POST[ 'rating' ];
        $post_id = (int) $_POST[ 'post_id' ];
        $votes = ( isset( $_COOKIE[ 'cpr-votes' ] ) ) ? json_decode( $_COOKIE[ 'cpr-votes' ] ) : array();

        if ( !$rating || !$post_id ) {
            $r = array(
                'error' => true,
                'msg'   => 'Voto não processado...'
            );
        } else if ( in_array( $post_id, $votes ) ) {
            $r = array(
                'error' => true,
                'msg'   => 'Seu voto já foi computado!'
            );
        } else {
            array_push( $votes, $post_id );
            setcookie( 'cpr-votes', json_encode( $votes ), time()+60*60*24, COOKIEPATH, COOKIE_DOMAIN, false );
            
            $count = (int) get_post_meta( $post_id, 'cpr-votes', true );
            $count++;
            
            $total = (int) get_post_meta( $post_id, 'cpr-total', true );
            $total += $rating;
            
            $avg = number_format( $total/$count, 2 );
            //Armazenamento dos dados via metada API
            update_metadata( 'post', $post_id, 'cpr-avg', $avg );
            update_metadata( 'post', $post_id, 'cpr-votes', $count );
            update_metadata( 'post', $post_id, 'cpr-total', $total );

            $r = array(
                'error' => false,
                'msg'   => 'Voto computado com sucesso!',
                'avg'   => $avg,
                'stars' => (string) round( $avg ),
                'votes' => (string) $count
            );
        }
        //Resposta em JSON contendo todos os dados necessários
        echo json_encode( $r );
        die();
    }

    /*
    O shortcode deve ser inserido no post via editor do Admin como exemplos:

    1 - Shortcode sem parâmetros
    [ratings]

    2 - Shortcode com parâmetros
    [ratings votos=1 media=true]

    3 - Shortcode com parâmetros e conteúdo
    [ratings votos=1 media=true] Avaliação [/ratings]
    
    */
    
    function shortcode( $atts, $content ) //Conteúdo 'Avaliação' será exibido como título do formulário
    {
        
        $a = shortcode_atts(
            array(
                'votos' => false,
                'media' => false
            ),
            $atts //atributos passados pelo shortcode
        );
        
       
        $opt = array(
            'title' => $content,
            'avg'   => $a[ 'media' ],
            'votes' => $a[ 'votos' ]
        );
        return self::stars( null, $opt );
        
        //return 'meu formulário shortcode'; //print básico no Post
    }
    //método de registro do widget
    function widget()
    {
        register_widget( 'KDM_Ratings_Widget' );
    }
    
    // Admin Bar
    
    function admin_menu()
    {
        $admin_page = add_menu_page( 'Post Ratings', 'Post Ratings', 'administrator', 'cpr-menu', array( 'KDM_Ratings', 'admin_page' ) );/* Ao incluir
        o item ao menu através de add_menu_page a variável $admin_page receberá o link da página, assim poderemos executar uma função (screen) assim que a 
        página seja carregada através do gancho:
        */
        add_action( 'load-' . $admin_page, array( 'KDM_Ratings', 'screen' ) );
    }
    
    function admin_page()
    {
        echo '<h2>Post Ratings</h2>'
        . '<p>Para inserir o formulário de votação em seus posts, utilize o shortcode '
        . '<b>[ratings votos="" media=""]Título[/ratings]</b> no local pretendido.</p>'
        . '<p>Lembre-se que somente usuários logados terão a permissão de voto.</p>';
    }
    
    // função de callback para personalização da barra   
    function admin_bar()
    {
        global $wp_admin_bar;
        /* 
        var_dump($wp_admin_bar);//Para acessar os ids de todas as opções do qual poderemos tratar basta dar um var_dump na variável global

        Outra forma de descobrir itens que desejamos eliminar basta acessar o código-fonte e identificar o id do elemento

        Opções que serão removidas:
        */
        $wp_admin_bar->remove_node( 'wp-logo' );
        $wp_admin_bar->remove_node( 'comments' );
        $wp_admin_bar->remove_node( 'view-site' );
        $wp_admin_bar->remove_node( 'new-content' );
        

        //Incluir um item a raíz
        $wp_admin_bar->add_node(
            array(
                'id'    => 'cpr-admin-bar', //ID único
                'title' => 'Post Ratings',  //Título da opção
                'href'  => admin_url( 'admin.php?page=cpr-menu' ) //link das opções do plugin
            )
        );

        //É possível inserir um subitem nos itens da raiz
        $wp_admin_bar->add_node(
            array(
                'parent'=> 'cpr-admin-bar', //Item pai da raíz
                'id'    => 'cpr-subitem', //ID único
                'title' => 'Exemplo',
                'href'  => '#'/*,
                'group' => 'nome-do-grupo' //Parâmetro opcional para agrupar itens ao um grupo
                */
            )
        );

        //$wp_admin_bar->add_group Tmb é possível criar grupos, que irão agrupar itens do submenu
    }
    
    // WP_Screen
    //A partir do método abaixo será possível inserir conteúdo na aba de ajuda
    function screen()
    {
        //Aba Ajuda
        $s = get_current_screen();
        //Sub-aba 1
        $s->add_help_tab(
            array(
                'id'        => 'cpr-tab1',
                'title'     => 'Aba #1',
                'content'   => 'Conteúdo da aba #1' //Aceita tags em html
            )
        );
        //Sub-aba 2
        $s->add_help_tab(
            array(
                'id'        => 'cpr-tab2',
                'title'     => 'Aba #2',
                'callback'  => array( 'KDM_Ratings', 'help_tab' ) //Ao invés de incluir diretamente o conteúdo pode se chamar uma função de callback
            )
        );
        
        //Inserção de conteúdo a sidebar da ajuda
        $content = '<p><strong>Guia de ajuda</strong></p><p>Conteúdo de exemplo</p>';
        $s->set_help_sidebar( $content );
    }
    
    function help_tab()
    {
        echo 'Conteúdo da aba #2';
    }
    
     //A partir do método abaixo será possível inserir conteúdo na aba opções de tela
    function screen_options()
    {
        /*
        É possível incluir campos de formulário  e tratar diretamenta no metódo e realizar as alterações referente a opção escolhida
        if(isset($_POST[nome-do-campo])){

        }
        */
        return 'Minhas opções';
    }
    
    // Personalização do Dashboard

    function dashboard()
    {
        /*
       
        
        wp_add_dashboard_widget( 'cpr-dashboard', //id único
                                 'Post Ratings', //título
                                  array( 'KDM_Ratings', 'metabox' ), //função de callback
                                  array( 'KDM_Ratings', 'metabox_save' ) 
                            );

        É possível excluir os metabox existentes na tela inicial através de remove_meta_box
       

        remove_meta_box( 'dashboard_quick_press', 
            'dashboard', //Opcional quando referente a tela inicial
             'side' //(Opcional)Contexto ou coluna de posicionamento );
        );

        remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
        remove_meta_box( 'dashboard_right_now', null, null );
        remove_meta_box( 'dashboard_activity', null, null );
        
        outra forma de remover os metabox e através da seguinte variável global global $wp_meta_box
        Essa variável armazena em formato de array todos os metabox e podemos apagá-los através de unset
        
        global $wp_meta_box;
        unset($wp_meta_box);

        ou ainda através de: 
        */
         global $wp_meta_boxes;
        $wp_meta_boxes[ 'dashboard' ] = array(); //limpa array

        /*
        Para incluir um novo metabox podemos recorrer a função add_meta_box
        

        add_meta_box( 'cpr-dashboard', //id único
                                 'Post Ratings', //título
                                  array( 'KDM_Ratings', 'metabox' ), //função de callback
                                 'dashboard',
                                 'normal',// Ao invés de 'normal' podemos alterar o posicionamento para:
                                 // side - 2º coluna
                                 // column3 - 3º coluna
                                 'high' // podemos também definir a prioridade com core para incluir no final da pilha de metabox
                            );

        
        Uma alternativa a add_meta_box e a função wp_add_dashboard_widget que permite uma segundo função de callback para tratamento do submit

        */
       
        wp_add_dashboard_widget( 'cpr-dashboard', //id único
                                 'Post Ratings', //título
                                array( 'KDM_Ratings', 'metabox' ), //1º função de callback
                                array( 'KDM_Ratings', 'metabox_save' ) //2º função de callback: exibe link configurar e permite tratar dados de formulário
                                );


    }
    
    function metabox()
    {
        echo 'Durante a edição de seus conteúdos lembre-se de inserir o shortcode de votação.';
    }
    
    function metabox_save()
    {
        if ( !empty( $_POST ) ) {
            var_dump($_POST);
            exit;
            // custom actions
            //Através da Option API é possível salvar as opções referente ao widget
        }
        
        //Não há necessidade incluir tag form ou botão de submit

        echo '<p><label>Opção personalizada</label> '
         . '<input type="text" name="_text" /></p>';
    }
    
    // Pointers
    
    function admin_scripts()
    {
        wp_enqueue_style( 'wp-pointer' ); //Recuperar formatação do pointer
        wp_enqueue_script( 'wp-pointer' ); //Script responsável por posicionar e ação de fechar
        
        //iremos imprimir os scripts no rodapé do Dashboard:
        add_action( 'admin_print_footer_scripts', array( 'KDM_Ratings', 'custom_script' ) );
    }
    
    function custom_script()
    {
        $u = wp_get_current_user(); //Recupera o usuário que esta logado
        $hide = get_user_meta( $u->ID, 'pointer-hide', true ); //Verifica se existe um metadado 'pointer-hide'
        if ( !$hide ) { //Caso não exista indica que o usuário não fechou pointer e será exibido
            $content  = '<h3>Precisa de ajuda?</h3>'; //Título
            $content .= '<p>Acesse a tela de suporte do plugin para maiores detalhes</p>';
            ?>
            <script type="text/javascript">
            jQuery( document ).ready( function($){
                $( '#cpr-dashboard' ).pointer({ //Atribuímos o pointer ao metadado recém criado '#cpr-dashboard'
                    content: '<?php echo $content; ?>',
                    position: {
                        edge: 'top', //posicionamento da seta
                        align: 'center' //posicionamento da caixa em relação ao elemento
                    },
                    close: function(){ //Método close será acionado assim que o link 'dispensar' for clicado 
                        $.post( //requisição ajax
                            'admin-ajax.php',
                            {
                                action: 'pointer_hide' //Enviará a ação 'pointer_hide'
                            }
                        );
                    }
                }).pointer( 'open' );
            });
            </script>
            <?php
        }
    }
    
    function pointer_hide()
    {
        $u = wp_get_current_user(); //recupera o usuário atual
        update_user_meta( $u->ID, 'pointer-hide', true ); //Atribui um metadado ao pointer para que não seja exibido mais
        exit;
    }
    
    // oEmbed
    
    function embed( $m ) //Recebe todas as ocorrências dada pela Expressão Regular
    {
        /*
        Poderia ser feita a incorporação de forma manual através do script embed disponível pela fonte.
        return sprintf('<iframe src="https://embed-ssl.ted.com/talks/%s" width="640" height="360" frameborder="0" scrolling="no" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>',
        $m[1]
        ) 

        Porém essa forma seria bem trabalhosa para inserir as principais fontes de conteúdo em vídeo e muito menos recomendável já que API oEmbed do WP
        oferece uma forma universal para isso como visto a seguir:
         */

        require_once( ABSPATH . WPINC . '/class-oembed.php' ); //Arquivo já possui todas as funções necessárias para esse tipo de manipulação
        $oembed = _wp_oembed_get_object();
        return $oembed->get_html( $m[0] );

    }
    
    function embed_defaults()
    {
        return array(
            'width' => 712,
            'height'=> 534
        );
    }
    
    function init_embed()
    {
        wp_oembed_add_provider( '#http://(www.)?ted\.com/.*#i',
                                 'http://www.ted.com/talks/oembed.{format}' //url do embed do site
                                 //Os sites disponibilizam a url embed para facilitar a incorporação por outros sites
                                 , true //confirma que esta sendo passado uma ER como parâmetro
                                 );
        /*
        Caso queira manipular outro valores pela oEmbed é possível: 
        $url = 'http://www.ted.com/talks/tim_berners_lee_on_the_next_web.html';
        require_once( ABSPATH . WPINC . '/class-oembed.php' );
        $oembed = _wp_oembed_get_object();
        
        $provider = 'http://www.ted.com/talks/oembed.json'; //além de json pode ser acessado em xml
        $provider = add_query_arg( 'url', $url, $provider ); 
        $provider = add_query_arg( 'width', 480, $provider );
        $provider = add_query_arg( 'height', 360, $provider );

        var_dump( $oembed->fetch( $url ) ); //recupera apenas o código de incorporação

        var_dump( $oembed->fetch( $provider, $url ) ); //recupera todas as variáveis do Embed
        
        var_dump( wp_oembed_get( $url, array( 'width' => 480, 'height' => 360 ) ) ); // Passando parâmetros
        exit;
        */
    }
    
}

define( 'CPR_DIR',   basename( dirname( __FILE__ ) ) );
define( 'CPR_PATH',  WP_PLUGIN_DIR . '/' . CPR_DIR . '/' );

require_once( CPR_PATH . 'widget.php' ); //include referente ao widget

// register_activation_hook( __FILE__, array( 'KDM_Ratings', 'activation' ) );
// register_deactivation_hook( __FILE__, array( 'KDM_Ratings', 'deactivation' ) );

add_action( 'plugins_loaded', array( 'KDM_Ratings', 'setup' ) );

?>