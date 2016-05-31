<?php 

get_header(); 

the_post();
global $post;
$thumb_id = (int) get_post_thumbnail_id();

$meta = get_post_meta( $post->ID, 'add-info', true );
$meta_labels = array(
    'date' => 'Data de criação',
    'size'  => 'Tamanho',
    'techs' => 'Técnicas',
    'price' => 'Preço'
    );

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
                            <?php the_post_thumbnail('photo-full'); //imagem destacada ?>
                            <ul class="photos-album">
                                <?php //laço para exibir todas imagens do post
                                foreach($images as $image){
                                list($src, $alt) = get_image_data($image, 'photo-thumb'); //função em custom.php irá recuperar alt e src do thumbnail 
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
                            <?php 
                            
                            if(is_array($meta)){
                                echo '<ul class="photos-info">';
                                foreach ($meta as $k => $v) {
                                    $price = ($k=='price') ? number_format($v, 2, ',', '') : $v ;
                                    printf('<li><strong>%s:</strong> %s</li>',
                                        $meta_labels[$k],
                                        $price);
                                }
                                echo '</ul>';
                            }
                             ?>
                            <p><?php the_content(); ?></p>
                        </li>
                    </ul>
                </div>
<?php get_footer(); ?>