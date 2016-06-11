<?php

if( !class_exists( 'WP_List_Table' ) ) //verificamos se a classe existe
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' ); // caso contrário é feito o include do arquivo referente a classe WP_list_table

//extensão da classe
class KDM_Products_List_Table extends WP_List_Table
{

    function __construct()
    {
        //Nome singular e plural do conteúdo atual
        parent::__construct(
            array(
                'singular'  => 'Produto',
                'plural'    => 'Produtos'
            )
        );
    }
    
    function no_items() //É executado caso nenhum itens seja encontrado
    {
        echo 'Nenhum produto cadastrado...';
    }
    
    // Colunas
    
    function get_columns() //Define coluna e rótulos
    {
        $cols = array(
            'cb'    => '<input type="checkbox" />', //Checkbox para ação em lote, responsável por inserir opção de marcar para cada registro
            'title' => 'Produto',
            'price' => 'Preço'
        );
        return $cols;
    }
    
    // Ordenação
    //Colunas passíveis de ordenação
    function get_sortable_columns() 
    {
        $cols = array(
            'title' => array( 'title', false ), // False para primeira ordenação ascendente
            'price' => array( 'price', true ) // False para primeira ordenação descendente
        );
        return $cols;
    }
    
    // Conteúdo

    function column_default( $item, $col_name ) //Retornará o resultado para todas as colunas que não tenho um tipo específico
    {
        return $item[ $col_name ];
    }
    
    /*
    function column_price( $item )
    {
        return 'R$ ' . number_format( $item[ 'price' ], 2, ',', '' );
    } */

     //Caso tenha o método no padrão column_nome-da-coluna esse será o método utilizado como os seguintes:

    function column_cb( $item )
    {
        //Checkbox individual de cada registro
        return sprintf(
            '<input type="checkbox" name="product_id[]" value="%s" />',
            $item[ 'id' ]
        );
    }
    
    function column_title( $item )
    {
        //Inserção do link de edição e exclusão na coluna título do registro
        $actions = array(
            'edit' => sprintf(
                '<a href="?page=%s&action=%s&product_id=%d">Editar</a>',
                'cp-products',
                'edit',
                $item[ 'id' ]
            ),
            'delete' => sprintf(
                '<a href="?page=%s&action=%s&product_id=%d">Excluir</a>',
                'cp-products',
                'delete',
                $item[ 'id' ]
            )
        );
        return sprintf( '%1$s %2$s', $item[ 'title' ], $this->row_actions( $actions ) ); //Método row_actions manterá o layout padrão da classe WP_List_Table
    } 
    
    // Ações em massa

    function get_bulk_actions()
    {
        return array(
            'delete' => 'Excluir'
        );
    }

    function process_bulk_action()
    {
        $action = $this->current_action(); // Recupera ações em massa
        switch ( $action )
        {
            case 'delete': // Caso seja ação de excluir
                $ids = false;
                //Recupera ids escolhidos
                if ( isset( $_POST[ 'product_id' ] ) ) //caso tenha sido passado via post foi feito uma ação em massa
                    $ids = implode( ',', $_POST[ 'product_id' ] ); // lista ids em string separados por vírgula
                else if ( isset( $_GET[ 'product_id' ] ) ) //caso tenha sido passado via get foi feito o click no link individual
                    $ids = $_GET[ 'product_id' ]; // id do registro a ser excluído

                if ( $ids ) {
                    global $wpdb;
                    /*
                    É possível excluir um único registro através da função  $wpdb->delete
                    $wpdb->delete( 
                        $wpdb->products, //Nome da tabela
                        array( 'id' => 1 ), // Verificação do campo chave e valor 
                        array( '%d' ) // Formato do valor do id, no caso %d float, caso seja omitido é assumido como string
                    ); 
                    Como iremos excluir mais de um registro usaremos $wpdb->query
                    */
                    $wpdb->query( "DELETE FROM {$wpdb->products} WHERE id IN ({$ids})" );
                }
                break;
        }
    }
    
    // Consulta SQL

    function prepare_items() // este método irá preparar todos os resultados de forma que a classe WP_List_Table consiga pegar os resultados e exibir de acordo com os dados de ordenação, paginação e demais itens da classe 
    {
        global $wpdb;
        $columns  = $this->get_columns(); // Recupera as colunas especificadas em get_columns
        $hidden   = get_hidden_columns( get_current_screen() ); // através de WP_Screen pega também as colunas que estão oculta em Opção de Tela para que não sejam exibid
        $sortable = $this->get_sortable_columns(); // Recuperação das colunas passíveis de ordenação
        $this->_column_headers = array( $columns, $hidden, $sortable );

        $this->process_bulk_action(); //Antes de recuperarmos os resultados precisamos executar as ações em massa

        // conteúdo do banco de dados
        $query = "SELECT * FROM {$wpdb->products}"; // Início da query de lista de produtos

        if ( isset( $_POST[ 's' ] ) ) { // Verifica se campo de pesquisa foi submetido
            $q = sanitize_text_field( $_POST[ 's' ] );
            $query .= ' WHERE title LIKE "%'.$q.'%"'; // Condição de pesquisa por tipo
        }

        // Verifica se ordenação foi passado por parâmetro
        $orderby = !empty( $_GET[ 'orderby' ] ) ? esc_attr( $_GET[ 'orderby' ] ) : 'title'; // Caso não tenha sido passado será feita a ordenação pelo campo title       
        $order = !empty( $_GET[ 'order' ] ) ? sanitize_sql_orderby( $_GET[ 'order' ] ) : 'ASC'; // Caso não tenha sido passado será feita o tipo de ordenação Asc   
	
        $query.= " ORDER BY {$orderby} {$order}"; // Conclusão da query

        // Páginação dos resultados
        $items_total = $wpdb->query( $query );
        $items_per_page = $this->get_items_per_page( 'products_per_page' );
        
        $paged = !empty( $_GET[ 'paged' ] ) ? (int) $_GET[ 'paged' ] : 1;
        if( !$paged )
            $paged = 1;

        $pages = ceil( $items_total/$items_per_page );
        $offset = ( $paged-1 ) * $items_per_page;
        $query .= sprintf(
            ' LIMIT %d, %d',
            (int) $offset,
            (int) $items_per_page
        );

        // Método próprio da class WP_Lista_table com dados da paginação
        $this->set_pagination_args(
            array(
                'total_pages'   => $pages,
                'total_items'   => $items_total,
                'per_page'      => $items_per_page,
            )
        );

        $this->items = $wpdb->get_results( $query, ARRAY_A ); // Atribuição de itens da consulta para a query personalizada. Optamos para que os dados sejam tratados como array associativo em ARRAY_A, caso seja omitido os dados serão retornados como objeto
    }
    
    // Exibição dos resultados
    // Após todo a verificação a respeito da consulta, ações em massa e exibição de conteúdo em cada coluna será necessário exibir a tabela na página personalizada
    function render()
    {
        global $products;
        $action = $products->current_action();
        if ( in_array( $action, array( 'insert', 'edit' ) ) ) { // Verificamos se o tipo de ação solicitada e inserir ou editar
            $products->form(); // Exibe formulário de edição ou inclusão de produto
        } else {
            // Caso não tenha sido apenas verificamos se ação foi exclusão e exibimos mensagem
            if ( $action == 'delete' ) {
                $msg = 'Registros excluídos';
                if ( isset( $_GET[ 'product' ] ) ) $msg = str_replace( 'os', 'o', $msg );
                echo '<div class="updated"><p>'.$msg.' com sucesso!</p></div>';
            }

            global $products; // pega objeto produto que a instancia a classe
            echo '<div class="wrap">'
                . '<h2>Lista de Produtos '
                . '<a href="admin.php?page=cp-products&action=insert" class="add-new-h2">Adicionar Novo</a>'
                . '</h2>'; // Exibe título da página e link de adicionar novo

            //Caso tenha sido feita a consulta permitimos que o usuário limpe a consulta    
            $search = ( isset( $_POST[ 's' ] ) ) ? sanitize_text_field( $_POST[ 's' ] ) : false;
            if ( $search ) {
                printf(
                    '<p><span>Resultados da pesquisa por "%s"</span> <a href="%s">Limpar Busca</a></p>',
                    $search,
                    $_SERVER[ 'REQUEST_URI' ]
                );
            }

            $products->prepare_items(); // Com a classe que extende WP_List_Table prepara-se os itens
            //Inserção do formulário de busca
            echo '<form method="post">';

            $products->search_box( // Formulário de busca
                      'Pesquisar', // Rótulo
                     'custom-search' // Id único
                      );
            $products->display(); // Exibição em tela
            echo '</form></div>';
        }
    }
    
    //Método que exibirá o formulário e ações de inserção e edição
    function form()
    {
        global $wpdb;

        if ( !isset( $_GET[ 'product_id' ] ) ) { // Caso id não tinha sido setado indica que deverá ser feito um novo registro
            $id = 0;
            $action = 'insert';
            $action_label = 'Adicionar';
        } else { // Caso tenha sido passado o id a ação será de edição
            $id = (int) $_GET[ 'product_id' ];
            $action = "edit&product_id={$id}";
            $action_label = 'Alterar';
        }
        // Envio dos dados
        if ( !empty( $_POST ) && isset( $_POST[ '_cp_nonce' ] ) ) {
            //Recuperamos título e preço
            $title = sanitize_text_field( $_POST[ '_title' ] ); 
            $price = (float) sanitize_text_field( str_replace( ',', '.', $_POST[ '_price' ] ) );
            if ( !wp_verify_nonce( $_POST[ '_cp_nonce' ], 'cp-form' ) ) { // Verificação de Nonce
                $error = 'Não foi possível processar sua requisição...';
            } else if ( !$title || !$price ) { // Validação dos dados
                $error = 'Preencha todos os campos!';
            } else {
                if ( !$id ) { // Novo produto
                    // Para inclusão podemos usar $wpdb->query, porém optaremos pela função específica $wpdb->insert
                    $wpdb->insert(
                        $wpdb->products, // Nome da tabela
                        array( // Array associativo com campos
                            'title' => $title,
                            'price' => $price
                        ),
                        array( // formato de cada campo
                            '%s', // string
                            '%f' // float
                        )
                    );
                    $msg = 'Produto inserido com sucesso!';
                } else { // Edição de produto
                    // Para edição podemos usar $wpdb->query, porém optaremos pela função específica $wpdb->update
                    $wpdb->update(
                        $wpdb->products,  // Nome da tabela
                        array( // Array associativo com campos
                            'title' => $title,
                            'price' => $price
                        ),
                        array( // Campos de comparação, cláusula WHERE
                            'id' => $id
                        ), 
                        array( // formato dos campos
                            '%s',
                            '%f'
                        ),
                        array( // formato das comparações da cláusula WHERE
                            '%d'
                        )
                    );
                    $msg = 'Produto alterado com sucesso!';
                }
            }
            
            if ( !isset( $error ) ) {
                $msg_class = 'updated'; // Mensagem de sucesso
            } else {
                $msg = $error;
                $msg_class = 'error'; // Mensagem de erro
            }
        }
        
        $v = array(); 
        if ( $id ) { // Caso tenha o id será feita a consulta dos dados
            $v = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT title, price FROM {$wpdb->products} WHERE id=%d",
                    $id
                ), ARRAY_A // Retorna dados em formato de array associativo
            );
        }
        
        if ( !isset( $v[ 'title' ] ) || !isset( $v[ 'price' ] ) ) { // Caso não tenha sido recuperado os dados, ou seja, será inserido um novo item será gerado um array com dados vazio para title e price
            $v = array(
                'title' => '',
                'price' => ''
            );
        } ?>
        <div class="wrap">
            <h2>
                Produtos - <?php echo $action_label; ?>
                <a href="admin.php?page=cp-products" class="add-new-h2">Voltar</a>
            </h2>
            
            <?php
            if ( isset( $msg ) && isset( $msg_class ) ) {
                printf(
                    '<div class="%s"><p>%s</p></div>',
                    $msg_class,
                    $msg
                );
            }
            ?>
            
            <form action="admin.php?page=cp-products&action=<?php echo $action; ?>" method="post">
                <?php wp_nonce_field( 'cp-form', '_cp_nonce' ); // Tratamento com nonce ?>
                <table class="form-table">
                    <tr>
                        <th>Produto</th>
                        <td><input type="text" value="<?php echo $v[ 'title' ]; ?>" name="_title" size="30" /></td>
                    </tr>
                    <tr>
                        <th>Preço</th>
                        <td><input type="text" value="<?php echo $v[ 'price' ]; ?>" name="_price" size="10" /></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><?php submit_button(); ?></td>
                    </tr>
                </table>
            </form>
        </div>
        <?php
    }

    // Página administrativa
    //Método responsável pela inserção do menu Produtos
    function admin_menu()
    {
        $hook = add_menu_page( 'Produtos', 'Produtos', 'administrator', 'cp-products', array( 'KDM_Products_List_table', 'render' ) ); // Chama o método render para exibição dos resultados
        add_action( "load-$hook", array( 'KDM_Products_List_table', 'add_options' ) ); // Assim que for carregado a página será acionado ao método add_options
        
        add_submenu_page( 'cp-products', 'Adicionar novo', 'Adicionar novo', 'administrator', 'cp-products-form', array( 'KDM_Products_List_table', 'form' ) ); // Inclusão do submenu adicionar novo que chamará o método form da classe KDM_Products_List_table
    }

    function add_options()
    {
        global $products;
        $products = new KDM_Products_List_table(); //Instância de novo objeto da classe
        
        //Opções de página que são realizadas através da classe WP_Screen
        $option = 'per_page';
        $args = array(
            'label'     => 'Produtos',
            'option'    => 'products_per_page',
            'default'   => 10
        );
        add_screen_option( $option, $args ); //Nova opção para página products_per_page, que irá controlar a páginação de resultados, para essa opção funcionar deverá ser adicionado um filtro 'set-screen-option' como foi feito em index.php 
    }

    function set_option( $status, $option, $value ) //$status quando passado no argumento sempre será false, $option o nome da opção e $value valor padrão da opção
    {
        if ( $option == 'products_per_page' ) //Caso seja a opção 'products_per_page'
            $status = $value; // atribui o valor que foi definido em add_screen_option, adiciona um valor personalizado para cada usuário na tabela _usersmeta
        
        return $status; //Caso de false o WP não executará nenhuma alteração
    }

}