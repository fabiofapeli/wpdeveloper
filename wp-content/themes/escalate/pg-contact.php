<?php 

// Template name: contato

get_header();

$status = 'error';
$error = 0;
$msg['success'][0] = "Mensagem enviada com sucesso...";
$msg['error'][1] = "Preencha todos os campos!";
$msg['error'][2] = "Problemas com o envio da mensagem...";

if ( !empty( $_POST ) ) {
    
    if ( !$_POST[ '_name' ] || !$_POST[ '_email' ] || !$_POST[ '_message' ] ) {
        $error = 1;
    } else {
        $message = sprintf(
            'Mensagem de %s [%s] ' . PHP_EOL . '%s',
            $_POST[ '_name' ],
            $_POST[ '_email' ],
            $_POST[ '_message' ]
        );
        if ( !wp_mail( get_option( 'admin_email' ), 'Formulário de contato', $msg ) )
            $error = 2;
        else
            $status = 'success';
    }
    
}

the_post();

if ( !empty( $_POST ) ) printf('<p class="contact-msg cm-%s">%s</p>',$status,$msg[$status][$error]);
?>
                <div id="content">
                    <form method="post" action="<?php echo get_permalink() ?>">
                        <div class="row half">
                            <div class="6u">
                                <input type="text" class="text" name="_name" placeholder="Name" />
                            </div>
                            <div class="6u">
                                <input type="text" class="text" name="_email" placeholder="Email" />
                            </div>
                        </div>
                        <div class="row half">
                            <div class="12u">
                                <textarea name="_message" placeholder="Message"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="12u"> <input type="submit" value="Send Message" class="button submit"></div>
                        </div>
                    </form>
                </div>
                <!-- end #content -->
        <?php 
        get_sidebar(); 
        get_footer();
        ?>