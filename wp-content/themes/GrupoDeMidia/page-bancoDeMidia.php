<?php

/*
Template Name: Banco De Midia 
*/

	//d($_POST);
	//d($_FILEs);
	//d(get_defined_vars());

	//create user 
	
	/*$user_data = array(
        'ID' => '',
        'user_pass' => $_POST['cf3_field_9'],
        'user_login' => $_POST['cf3_field_5'],
        'user_nicename' => $_POST['cf3_field_1'],
        'user_url' => '',
        'user_email' => $_POST['cf3_field_6'],
        'display_name' => $_POST['cf3_field_1'],
        'nickname' => $_POST['cf3_field_1'],
        'first_name' => $_POST['cf3_field_1'],
        'last_name' => $_POST['cf3_field_2'],
        'description' => $_POST['cf3_field_2'],
        'user_registered' => $_POST['cf3_field_7'],
        'role' => get_option('default_role') // Use default role or another role, e.g. 'editor'
    );
    
    $user_id = wp_insert_user( $user_data );*/

    if(isset($_POST['gm_search'])&&!empty($_POST['gm_search'])){
    	//d($_POST['gm_search']);

		global $wpdb;
		$query = "SELECT * FROM wp_usermeta WHERE meta_value";
		$prepared = $wpdb->prepare("SELECT * FROM wp_usermeta WHERE meta_value LIKE  '%%%s%%';", $_POST['gm_search']);
		//$prepared = $wpdb->prepare("SELECT * FROM `wp_usermeta` WHERE `meta_value` LIKE  '%Silvio%' LIMIT '", $_POST['gm_search']);

		//d($prepared);
		$query = $wpdb->get_results($prepared);
		//d($query);
		$ids = array();
		$user_id = '';
		foreach ($query as $key => $item) {
			if(!in_array($item->user_id, $ids)){
				$ids[] = $item->user_id;
				if(strlen($user_id) <= 0) {
					$user_id .= $item->user_id;
				} else {
					$user_id .= ','.$item->user_id;
				}				
			}			
		}

		$prepared = "SELECT * FROM wp_usermeta WHERE user_id IN ($user_id) AND meta_key IN ('first_name','last_name','nickname','description','cargo','gm_image', 'agencia');";
		$query = $wpdb->get_results($prepared);

		$postsMG = array();
		$postsMG_F = array();

		//organize data
		foreach ($ids as $key => $id) {
			foreach ($query as $k => $v) {
				if(($v->user_id == $id)) {
					$postsMG[$id][$v->meta_key] = $v->meta_value;
				}				
			}
		}

		foreach ($postsMG as $k => $post) {
			// filter to remove any user that is not a subscriber and/or has incomplete data
			$userInfo = get_userdata($k);
			if(!$userInfo->caps['subscriber']) {continue;}
			if(empty($post['first_name'])||empty($post['last_name'])||empty($post['agencia'])||empty($post['cargo'])||empty($post['description'])) {continue;}
			$postsMG_F[$k] = $post;
		}
		$numResults = count($postsMG_F);
		if($numResults == 0) {
			$noResults = true;
		}
	}


get_header(); ?>

<div id="content-area" class="clearfix">
	<div id="left-area" class="searchResults">
		<?php //get_template_part('includes/breadcrumbs', 'page'); ?>
		<h1 class="title"><?php the_title(); ?></h1>

	<article id="post-<?php the_ID(); ?>" <?php post_class('entry clearfix'); ?>>
		<!-- <div class="contentBox clearfix"> -->
			
				<!-- <div id="et_pt_blog" class="responsive"> -->
					<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

					<?php
						global $wp_embed;
						$thumb = '';
						$et_full_post = get_post_meta( $post->ID, '_et_full_post', true );
						$width = apply_filters('et_blog_image_width',285);
						if ( 'on' == $et_full_post ) $width = apply_filters( 'et_single_fullwidth_image_width', 960 );
						$height = apply_filters('et_blog_image_height',215);
						$classtext = '';
						$titletext = get_the_title();
						$thumbnail = get_thumbnail($width,$height,$classtext,$titletext,$titletext,false,'Singleimage');
						$thumb = $thumbnail["thumb"];
						
						$et_video_url = get_post_meta( $post->ID, '_et_lucid_video_url', true );
					?>
					<?php if ( '' != $thumb && 'on' == et_get_option('lucid_thumbnails') ) { ?>
						<div class="post-thumbnail">
							<?php
								if ( 'video' == get_post_format( $post->ID ) && '' != $et_video_url ){
									$video_embed = $wp_embed->shortcode( '', $et_video_url );

									$video_embed = preg_replace('/<embed /','<embed wmode="transparent" ',$video_embed);
									$video_embed = preg_replace('/<\/object>/','<param name="wmode" value="transparent" /></object>',$video_embed); 
									$video_embed = preg_replace("/height=\"[0-9]*\"/", "height=350", $video_embed);
									$video_embed = preg_replace("/width=\"[0-9]*\"/", "width={$width}", $video_embed);
							
									echo $video_embed;
								} else {
									//print_thumbnail($thumb, $thumbnail["use_timthumb"], $titletext, $width, $height, $classtext);
									the_crop_image($thumb, '&amp;w=630&amp;h=250&amp;zc=1');
								}
							?>
						</div> 	<!-- end .post-thumbnail -->
					<?php } ?>
				<div class="post_content clearfix">
					<?php the_content(); ?>
					<?php endwhile; // end of the loop. ?>
				<!-- </div> -->
			</div>
		<!-- </div> -->
	</article>
		<div id="search" class="clearfix">
			<div id="search-form">
				<!-- <form method="post" id="searchform" action="http://192.168.0.223/wordpress/?page_id=15"> -->
				<form method="post" id="searchform" action="http://grupodemidiarj.com.br/banco-de-midia/">
					<input type="text" placeholder="<?php esc_attr_e('Busca no Banco de Midia', 'Lucid'); ?>" name="gm_search" id="gm_search" />
					<input type="image" src="<?php echo esc_url( get_template_directory_uri() . '/images/search_btn.png' ); ?>" id="searchsubmit" />
				</form>
			</div> 
		</div>
		<?php if($numResults === 0) { ?>
		<div class="entry clearfix">
			<div class="post_content">
				<div id="et_pt_blog" class="responsive">
					<h2>Nenhum resultado correspondente encontrado</h2>
				</div>
			</div>
		</div>		
		<?php } elseif(!empty($postsMG_F)) { ?>
		<div class="entry clearfix">
			<div class="post_content">
				<div id="et_pt_blog" class="responsive">
					<?php
					if($numResults == 1) {
						echo "<h2 class='results'>$numResults midia encontrado</h2>";
					} else {
						echo "<h2 class='results'>$numResults midias encontrados</h2>";
					}
						
					
					foreach ($postsMG_F as $k => $post) {
				?>			
					<div class="et_pt_thumb alignleft">
						<?php 
							$img = get_bloginfo('wpurl').'/wp-content/uploads'.$post['gm_image'];
							the_crop_image( $img, '&amp;w=170&amp;h=125&amp;zc=1'); 
						?>
					</div> <!-- end .thumb -->

					<div class="et_pt_blogentry clearfix">
						<div class="box-post">
							<h2 class="et_pt_title"><?php echo $post['first_name'].' '.$post['last_name']; ?></h2>
							<h3 class="et_pt_title">Agência: <?php echo $post['agencia']; ?></h3>
							<h3 class="et_pt_title">Cargo: <?php echo $post['cargo']; ?></h3>
							<p><?php echo wpautop($post['description']);?></p>
						</div>
					</div> <!-- end .et_pt_blogentry -->
				<?php } ?>
				</div>
			</div>
		</div>		
		<?php } ?>
	</div> <!-- end #left-area -->

	<?php get_sidebar(); ?>
</div> 	<!-- end #content-area -->
	
<?php get_footer('cadastro'); ?>