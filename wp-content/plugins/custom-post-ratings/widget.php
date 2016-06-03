<?php

class KDM_Ratings_Widget extends WP_Widget
{
    
    function __construct()
    {
        parent::__construct( 'cpr-widget', 'Post Ratings' ); //método construtor registra um id único para o widget e título
    }

    //Form que será exibido no dashboard para definir as opções de visualização
    function form( $inst ) //Em $inst é recebido os campos referente as opções 
    {
        if ( !isset( $inst[ 'title' ] ) || !isset( $inst[ 'count' ] ) ) {
            $inst = array(
                'title' => '',
                'count' => ''
            );
        }

       // Em get_field_name será gerado um nome automaticamente para os campos do qual será processado pelo WP
        ?>
        <p>
            <label>Título</label>
            <input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $inst[ 'title' ]; ?>" class="widefat" />
        </p>
        <p>
            <label>Quantidade de posts</label>
            <input type="text" name="<?php echo $this->get_field_name( 'count' ); ?>" value="<?php echo $inst[ 'count' ]; ?>" size="5" />
        </p>
        <?php
    }

    /*
    Para realizar a validação dos dados pode ser subscreevida o método update da classe WP_Widget

    function update($new, $old){ //por parâmetro é passado o valor novo e antigo
       return array_merge($new, $old); //Basta fazer as validações necessárias e retornar o array        
    }

    */
    

    //Exibição do conteúdo dentro do tema
    function widget( $args, $inst ) //É recuperado os argumentos da área de widget e os valores instanciados de configuração do widget 
    {                               //Os argumentos são as configurações passadas em register_sidebar em functions
        $q = new WP_Query(
            array(
                'meta_key'              => 'cpr-avg', //Feita a consulta de todos os post que possui a meta_key cpr-avg
                'orderby'               => 'meta_value_num', //Ordenado pela meta-query cpr-avg
                'order'                 => 'DESC',
                'ignore_sticky_posts'   => true, //ignorando posts fixos para que a ordem cpr-avg seja respeitadaa
                'posts_per_page'        => (int) $inst[ 'count' ] //qtd conforme configurada no widget
            )
        );
        echo $args[ 'before_widget' ];
        
        if ( $inst[ 'title' ] ) //Caso o título seja setado nas opções ele é exibido
            echo $args[ 'before_title' ] . $inst[ 'title' ] . $args[ 'after_title' ];
        
        if ( !$q->have_posts() ) { //Caso não tenha tido votação
            echo '<p>Nenhum post foi votado!</p>';
        } else { //caso tenha tido votação é exibido os posts e média
            echo '<ul class="cpr-posts">';
            while ( $q->have_posts() ) {
                $q->the_post();
                global $post;
                printf(
                    '<li><a href="%1$s" title="%2$s">%2$s</a> [%3$s]</li>',
                    get_permalink(),
                    get_the_title(),
                    get_post_meta( $post->ID, 'cpr-avg', true )
                );
            } wp_reset_query();
            echo '</ul>';
        }
        
        echo $args[ 'after_widget' ];
    }
    
}