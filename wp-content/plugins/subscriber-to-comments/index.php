<?php
/*
Plugin Name: Subscriber 2 comments
Description: Permite aos usuários subscrever comentários
*/

function plugin_setup(){
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . 'fpl_subscribertocomments';

	$sql = "CREATE TABLE $table_name (
		post_ID int(7) NOT NULL,
		user_ID int(7) NOT NULL) $charset_collate;";

	$wpdb->query($sql);
}


function plugin_clean(){
	global $wpdb;
	$table_name = $wpdb->prefix . 'fpl_subscribertocomments';
	$wpdb->query("DROP TABLE $table_name");
}

class FPL_Subscriber{
	static $field_name = 'fpl_Subscriber';

	function init(){
		add_action('comment_form', array('FPL_Subscriber','show_form'));
		add_action('comment_post', array('FPL_Subscriber','check_subscriber'),10,2);
		add_filter('wp_mail_content_type', create_function('','return "text/html";'));
		add_action('init', array('FPL_Subscriber','unsubscribe'));
	}


	function show_form(){
		global $wpdb;
		global $post;
		global $current_user;
		$table_name = $wpdb->prefix . 'fpl_subscribertocomments';
		$resultado=$wpdb->get_results("SELECT * FROM $table_name WHERE post_ID = '$post->ID' AND user_ID='$current_user->ID'");
		if($wpdb->num_rows>0){
		echo '<br /><a href="?unsubscribe=1&p='.$post->ID.'">Clique aqui para cancelar a notificação de comentários relacionados a este post</a>';
		}
		else{
			 echo '<br /><label><input type="checkbox" name="'. self::$field_name
		.'" value="1" >Desejo receber notificações sobre comentários relacionados a
		esse post. </label>';
		}
		
	}

	function check_subscriber($comment_id, $approved ){
		$comment = get_comment($comment_id);
		if(isset($_POST[self::$field_name])){
			global $wpdb;
			$table_name = $wpdb->prefix . 'fpl_subscribertocomments';
			$wpdb->query("INSERT INTO $table_name(post_ID,user_ID) VALUES('$comment->comment_post_ID','$comment->user_id')");
		}
		if($approved){
			self::notify($comment);
		}
	}

	function notify($comment){
		$post_id = $comment->comment_post_ID;
		$user_id = $comment->user_id;
		global $wpdb;
		$permalink = get_permalink($post_id);
		$post_title = get_the_title($post_id);
		$message = 'Novo comentário para '.$post_title.'. <a href="'.$permalink.'">Clique aqui</a> para visualizar. <br /><br />
		<a href="'.$permalink.'&unsubscribe=1">Cancelar subscrição</a>';
		$table_user = $wpdb->prefix . 'users';
		$table_subscriber = $wpdb->prefix . 'fpl_subscribertocomments';
		$sql = "SELECT user_email FROM $table_user u INNER JOIN $table_subscriber s ON u.ID=s.user_ID WHERE s.post_ID = '$post_id' AND s.user_ID!='$user_id'";
		$posts=$wpdb->get_results($sql);
		foreach ($posts as $p) {
				wp_mail($p->user_email,'Notificação de comentários',$message);
				//$wpdb->insert( 'wp_mail', array( 'destinatario' => $p->user_email, 'assunto' => 'Notificação de comentários', 'mensagem' =>$message) );
		}

	}

	function unsubscribe(){
		if(isset($_GET['unsubscribe']) and $_GET['unsubscribe']==1){
			global $wpdb;
			global $post;
			global $current_user;
			$table_name = $wpdb->prefix . 'fpl_subscribertocomments';	
			$wpdb->query("DELETE FROM $table_name WHERE post_ID = '$post->ID' AND user_ID='$current_user->ID'");
		}
	}

}

add_action('plugins_loaded',array('FPL_Subscriber','init'));

register_activation_hook(__FILE__,'plugin_setup');
register_deactivation_hook(__FILE__,'plugin_clean');
