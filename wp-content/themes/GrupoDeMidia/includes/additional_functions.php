<?php 

/* Meta boxes */

function lucid_settings(){
	add_meta_box("et_post_meta", "ET Settings", "lucid_display_options", "post", "normal", "high");
	add_meta_box("et_post_meta", "ET Settings", "lucid_display_options", "video", "normal", "high");
	add_meta_box("et_post_meta", "ET Settings", "lucid_display_options", "page", "normal", "high");
}
add_action("admin_init", "lucid_settings");

function lucid_display_options($callback_args) {
	global $post, $themename;
			
	$et_fs_video = get_post_meta( $post->ID, '_et_lucid_video_url', true );
	$et_left_sidebar = get_post_meta( $post->ID, '_et_left_sidebar', true );
	$et_full_post = get_post_meta( $post->ID, '_et_full_post', true );
	
	wp_nonce_field( basename( __FILE__ ), 'et_settings_nonce' );
?>

	<div class="et_fs_setting" style="margin: 17px 0 22px 5px;">
		<label for="et_left_sidebar" class="selectit"><input name="et_left_sidebar" type="checkbox" id="et_left_sidebar" <?php checked( $et_left_sidebar, 'on' ); ?>> Mostrar barra lateral à esquerda</label>
	</div>
	
	<?php if ( 'page' == $callback_args->post_type ) return; ?>
	
	<div class="et_fs_setting" style="margin: 17px 0 22px 5px;">
		<label for="et_full_post" class="selectit"><input name="et_full_post" type="checkbox" id="et_full_post" <?php checked( $et_full_post, 'on' ); ?>> Esconder Sidebar</label>
	</div>
	
	<div id="et_custom_settings" style="margin: 17px 0 17px 5px;">				
		<h4 style="font-size: 13px;"><?php esc_html_e('Video Post Format Settings: ',$themename); ?></h4>
		
		<div class="et_fs_setting" style="margin: 13px 0 26px 4px;">
			<label for="et_fs_video" style="color: #000; font-weight: bold;"> <?php esc_html_e('Video url:',$themename); ?> </label>
			<input type="text" style="width: 30em;" value="<?php echo esc_url($et_fs_video); ?>" id="et_fs_video" name="et_fs_video" size="67" />
			<br />
			<small style="position: relative; top: 8px;"><?php esc_html_e('Você pode colocar link do Youtube ou vídeo aqui.', $themename); ?> ex: <code><?php echo htmlspecialchars("http://www.youtube.com/watch?v=WkuHbkaieZ4");?></code></small>
		</div>
	</div> <!-- #et_custom_settings -->
	
	<?php
}

add_action( 'save_post', 'lucid_save_details', 10, 2 );
function lucid_save_details( $post_id, $post ){
	global $pagenow;
	if ( 'post.php' != $pagenow ) return $post_id;
		
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
		return $post_id;

	$post_type = get_post_type_object( $post->post_type );
	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;
		
	if ( !isset( $_POST['et_settings_nonce'] ) || !wp_verify_nonce( $_POST['et_settings_nonce'], basename( __FILE__ ) ) )
        return $post_id;
		
	if ( isset($_POST["et_fs_video"]) ) update_post_meta( $post_id, "_et_lucid_video_url", esc_url_raw( $_POST["et_fs_video"] ) );
	
	if ( isset( $_POST["et_left_sidebar"] ) ) update_post_meta( $post_id, "_et_left_sidebar", 'on' );
	else delete_post_meta( $post_id, "_et_left_sidebar" );
	
	if ( isset( $_POST["et_full_post"] ) ) update_post_meta( $post_id, "_et_full_post", 'on' );
	else delete_post_meta( $post_id, "_et_full_post" );
} ?>