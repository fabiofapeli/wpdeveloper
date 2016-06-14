( function() {
    tinymce.create( 'tinymce.plugins.custombutton', { // Criação de um plugin personalizado
        init: function( ed, url ) {
            ed.addCommand( 'button-alert', function() { // Inserção de um novo botão para o comando de alerta, que será disparado quando clicado e fará a mesma ação da quicktag
                alert( 'Teste de botão' );
            });

            ed.addButton( // Incorporação do botão ao editor
                'custom_button', // Id do botão que foi referenciado tanto no botão quanto no arquivo externo em index.php
                {
                    title: 'Alerta', // Títlo
                    cmd: 'button-alert', // Referência um comando quando o botão for clicado
                    image: url + '/alert.png' // Imagem para o botão
                }
            );
        }
    });
    tinymce.PluginManager.add( 'custom_button', tinymce.plugins.custombutton ); // Incorporação do plugin ao editor visual
})();