<?php

add_action( 'after_setup_theme', 'custom_setup' );

function custom_setup()
{
    add_action( 'customize_register', 'customizer' ); // Primeiro inserimos uma action referente a área de personalização
}

function customizer( $c ) // Função de callback receberá a classe $wp_customize
{
    $c->add_section( // Inserção de uma Section
        'ct-section', // Id único
        array( // Atributos
            'title'     => 'Seção personalizada',
            'priority'  => 30,
        )
    );

    $c->add_setting( // Setting será uma das opões que poderemos personalizar
        'ct-option', // Id da setting
        array(
            'default'   => 'Valor padrão', // Valor inicial do campo
            'type'      => 'option' // Formato que será armazenado no banco de dados
        )
    );

    // Para exibir as opções precisamos inserir um novo controle

         $c->add_control(
        new WP_Customize_Color_Control( // Primeiro parâmetro será a instância da classe WP_Customize_Control
            $c, // Construtor recebe a intância da classe $wp_customize
            'ct-option-val', // Identificador único
            array(
                'label'     => 'Váriavel', // Rótulo
                'section'   => 'ct-section', // Section da qual faz parte
                'settings'  => 'ct-option', // Setting de referência
            )
        )
    );

    // Podemos verificar as alterações na tabela wp_options

    //Podemos também trabalhar com as opções no formato serializado, basta inserir o nome da opções em formato de array

    $c->add_setting(
        'ct-opt[a]', //nome da opção em formato de array
        array(
            'type' => 'option'
        )
    );
    $c->add_setting(
        'ct-opt[b]',
        array(
            'type' => 'option'
        )
    );

       $c->add_control(
        new WP_Customize_Control(
            $c,
            'ct-opt-a',
            array(
                'label'     => 'Texto 1',
                'section'   => 'ct-section',
                'settings'  => 'ct-opt[a]', 
            )
        )
    );

       $c->add_control(
        new WP_Customize_Control(
            $c,
            'ct-opt-b',
            array(
                'label'     => 'Texto 2',
                'section'   => 'ct-section',
                'settings'  => 'ct-opt[b]', 
            )
        )
    );

    
    // Na tabela wp_options poderemos verificar os valores serializados

    // Como estamos trabalhando com a personalização do tema, vamos optar por trabalhar com a theme_mod (Theme Modification)

    $c->add_setting(
        'ct-text',
        array(
            'type' => 'theme_mod' // ao invés de informar option devemos informar 'theme_mod'
        )
    );

   $c->add_control(
        new WP_Customize_Control(
            $c,
            'ct-text-c',
            array(
                'label'     => 'Texto',
                'section'   => 'ct-section',
                'settings'  => 'ct-text', 
            )
        )
    );

    /*
    $c->add_setting( 'ct-test' ); Também podemos ocultar o tipo que o WP assumirá que se trata de uma Theme Modification

    Ao verificar a tabela wp-options podemos vericar que os dados serão salvo de forma serializada na option_name theme_mods_custom-theme 

    var_dump(get_theme_mods()); // Com a função get_theme_mods recuperamos todas as personalizações que o tema possui

    var_dump(get_theme_mod('ct-test')); // Caso queira recuperar uma modificação específica usamos get_theme_mod() passando o id da modification

    Através de set_theme_mod podemos atribuir uma nova configuração oo tema em tempo de execução

    set_theme_mod('info', // Nome da váriavel
                'Valor da config' // valor da variável
                );

    Também é possível excluir uma modificação com:

    remove_theme_mod('info');

    Caso queira remover todas as modificaçoes use:

    remove_theme_mods();

    Além de campo de texto podemos criar diferente tipo de controles
    */

    $c->add_setting( 'set-file' );
    $c->add_setting( 'set-cor' );
    $c->add_setting( 'set-imagem' );
    
    $c->add_control(
        new WP_Customize_Upload_Control( // WP_Customize_Upload_Control para upload de arquivos
            $c,
            'ct-file',
            array(
                'label'     => 'Arquivo',
                'section'   => 'ct-section',
                'settings'  => 'set-file',
            )
        )
    );

      $c->add_control(
        new WP_Customize_Color_Control( // WP_Customize_Color_Control para controle de cor
            $c, 
            'ct-cor',
            array(
                'label'     => 'Alguma cor',
                'section'   => 'ct-section',
                'settings'  => 'set-cor', 
            )
        )
    );
    $c->add_control(
        new WP_Customize_Image_Control( // WP_Customize_Image_Control para edição de imagem
            $c,
            'ct-imagem',
            array(
                'label'     => 'Imagem',
                'section'   => 'ct-section',
                'settings'  => 'set-imagem',
            )
        )
    );

   // var_dump(get_theme_mods());
    /*
    Exibirá algo do tipo

    array(5) {
      [0]=>
      bool(false)
      ["nav_menu_locations"]=>
      array(1) {
        ["menu-header"]=>
        int(2)
      }
      ["set-file"]=>
      string(73) "http://localhost:8080/wordpress/wp-content/uploads/2016/05/Lighthouse.jpg"
      ["set-cor"]=>
      string(7) "#3d1272"
      ["set-imagem"]=>
      string(69) "http://localhost:8080/wordpress/wp-content/uploads/2016/05/Tulips.jpg"
    }


    */

}