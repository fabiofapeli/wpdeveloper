<div class="comments">    
                    <h2>Comentários</h2>
                    <?php if(!comments_open()){
                        echo '<p>Comentários fechados!</p>';
                    }else{ ?>
                    <ul class="comment-list">
                        <?php 
                        wp_list_comments('callback=custom_comments');
                        ?>
                    </ul>
                    <?php 
                     }
                     ?>
                    <div class="commentform" id="responder">
                        <?php
                            //incluir id='responder'
                        ?>
                        <h3>Enviar comentário</h3>
                        <p class="comment-reply-cancel">
                            <a rel="nofollow" id="cancel-comment-reply-link" href="#responder" style="display: none;" class="btn btn-orange">Cancelar Resposta</a>
                        </p>
                        <form action="<?php echo PW_URL ?>wp-comments-post.php" method="post" id="commentform">
                            <ul>
                                <?php if(is_user_logged_in()){
                                    $u = wp_get_current_user();
                                    printf('<li>logado como <a href="%s">%s</a>, <a href="%s">Desconectar</a></li>',
                                    admin_url('profile.php'),
                                    $u->display->name,
                                    wp_logout_url(get_permalink())
                                    );
                                }else{
                                 ?>
                                <li>
                                    <input id="author" name="author" placeholder="Nome" type="text" class="text" />
                                </li>
                                <li>
                                    <input id="email" name="email" placeholder="Email" type="text" class="text" />
                                </li>
                                <li>
                                    <input name="url" placeholder="Website (Opcional)" type="text" class="text" />
                                </li>
                                <?php 
                                }
                                 ?>
                                <li>
                                    <textarea name="comment" rows="4" cols="48" placeholder="Comentário"></textarea>
                                </li>
                                <li>
                                    <button type="submit" class="button submit">Enviar</button>
                                </li>
                            </ul>
                            <input name="comment_post_ID" value="<?php global $post; echo $post->ID ?>" id="comment_post_ID" type="hidden">
                            <input name="comment_parent" id="comment_parent" value="<?php echo (isset($_GET['replytocom']) ? $_GET['replytocom']: 0); ?>" type="hidden">
                        </form>
                </div>
</div>