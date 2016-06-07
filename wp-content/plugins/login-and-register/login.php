<?php

/*
Responsável por exibir a tela de login
*/

require LR_PATH . 'header.php';

global $user;

do_action( 'custom_login' ); //Permite verificar se formulário foi submetido

if ( get_option( 'users_can_register' ) ) //Verifica se usuários podem ser registrados, para exibir a opção de registre-se é necessário que esteja marcada a opção Qualquer pessoa pode se registrar em Configurações do Dashboard
    printf(
        '<p>É novo por aqui? <a href="%s" title="Registrar">Faça seu cadastro</a>.</p>',
        wp_registration_url() //Recupera link de registro
    );
        
?>

<form method="post" action="<?php echo wp_login_url(); ?>">
    <?php wp_nonce_field( 'lr-nonce-login', '_nonce' ); ?>
    <div>
        <label>Login</label>
        <input type="text" name="_user" value="<?php echo $user; ?>" />
    </div>
    <div>
        <label>Senha</label>
        <input type="password" name="_pass" />
    </div>
    <div>
        <input type="submit" value="Entrar" />
    </div>
</form>

<?php require LR_PATH . 'footer.php'; ?>