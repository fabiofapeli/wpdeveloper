<?php get_header(); 

the_post();
global $post;
$thumb_id = (int) get_post_thumbnail_id();

$images = get_posts( //Recupera todas as imagens anexa ao posts
    array(
        'post_type'         => 'attachment',
        'post_mine_type'    => 'image',
        'post_status'       => 'any',
        'posts_per_page'    => -1,
        'post_parent'       => $post->ID, //De qual post será recupeda as imagens. Caso o argumento seja omitido exibirá todas as imagens da galeria
        'exclude'           => array($thumb_id) // não será exibida a imagem destaca
         )
    );
?>
                <div class="entry photos photos-single">
                    <ul>
                        <?php if (has_post_thumbnail()) { //verificamos se há imagem destacada para listar todas
                           ?>
                        <li>
                            <?php the_post_thumbnail('full'); //imagem destacada ?>
                            <ul class="photos-album">
                                <?php //laço para exibir todas imagens do post
                                foreach($images as $image){
                                list($src, $alt) = get_image_data($image, 'thumbnail'); //função em custom.php irá recuperar alt e src do thumbnail 
                                printf(
                                    '<li><a href="%s" title="%s"><img alt="%s" src="%s"></a></i>',
                                    wp_get_attachment_url( $image->ID), //pega url da imagem full
                                    $image->post_title,
                                    $alt,
                                    $src
                                    );
                                }
                                 ?>
                                
                            </ul>
                        </li>
                        <?php
                        } ?>
                        
                        <li>
                            <h1><?php the_title(); ?></h1>
                            <ul class="photos-info">
                                <li><strong>Date:</strong> 08 January 2014</li>
                                <li><strong>Size:</strong> 1'8"</li>
                                <li><strong>Techinique:</strong> Oil</li>
                                <li><strong>Price:</strong> US$ 45.00</li>
                            </ul>
                            <p><?php the_content(); ?></p>
                        </li>
                    </ul>
                </div>
<?php get_footer(); ?>