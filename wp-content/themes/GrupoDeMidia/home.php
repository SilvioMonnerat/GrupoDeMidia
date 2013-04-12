<?php get_header(); ?>

<?php if ( 'on' == et_get_option('lucid_featured', 'on') && is_home() ) get_template_part( 'includes/featured', 'home' ); ?>

<?php
	$recent_sidebars = array('recent-area-1','recent-area-2','recent-area-3');
	if ( is_active_sidebar( $recent_sidebars[0] ) || is_active_sidebar( $recent_sidebars[1] ) || is_active_sidebar( $recent_sidebars[2] ) ) {
		echo '<div id="recent-categories" class="clearfix">';
		foreach ( $recent_sidebars as $key => $recent_sidebar ){
			if ( is_active_sidebar( $recent_sidebar ) ) {
				echo '<div class="recent-category' . (  2 == $key ? ' last' : '' ) . '">';
				dynamic_sidebar( $recent_sidebar );
				echo '</div> <!-- end .recent-category -->';
			}
		}
		echo '</div> <!-- end #recent-categories -->';
	}
?>

<div id="content-area" class="clearfix">
	<div id="left-area">
	<?php 
		if ( 'on' == et_get_option('lucid_video_slider_home','on') ){ 

		?>
		<?php
			$video_postsnum = (int) et_get_option( 'lucid_video_postsnum', 4 );
			$video_posts_query = new WP_Query( apply_filters( 'et_video_post_args', array(
						'post_type' => 'video',
						/*'tax_query' => array(
							array(
								'taxonomy' => 'post_format',
								'field' => 'slug',
								'terms' => 'post-format-video'
							)
						),*/
						'showposts' => $video_postsnum
					) 
				)
			); 
		?>
		
		<?php if ( $video_posts_query->have_posts() ){ ?>	
			<div id="recent-videos">
				<h3 class="main-title"><?php esc_html_e( 'Vídeos •••', 'Lucid' ); ?></h3>
				<!-- Alterar o link abaixo para o caminho correto -->
				<a href="<?php echo get_option('Home'); ?>/videos/" class="more"><?php esc_html_e( 'Mais', 'Lucid' ); ?></a>
				<div id="video-slider-section">
					<div id="video-content" class="flexslider">
						<ul class="slides">
							<?php 
								$video_info = array();
								$i = 0;
								global $wp_embed;
							?>

							<?php while ( $video_posts_query->have_posts() ) : $video_posts_query->the_post(); ?>
								<?php 
									$thumb = '';
									$width = apply_filters('et_video_image_width',136);
									$height = apply_filters('et_video_image_height',77);
									$classtext = '';
									$titletext = get_the_title();
									$thumbnail = get_thumbnail($width,$height,$classtext,$titletext,$titletext,false,'Videoimage');
									
									$video_info[$i]['thumb'] = $thumbnail["thumb"];
									$video_info[$i]['thumbnail'] = $thumbnail["use_timthumb"];
									$video_info[$i]['titletext'] = $titletext;
									$video_info[$i]['width'] = $width;
									$video_info[$i]['height'] = $height;
									
									$et_video_url = get_post_meta( $post->ID, '_et_lucid_video_url', true );

									$et_video_url = addParamToURL($et_video_url, "enablejsapi=1")
								?>
								<li>
								<?php 
									$video_embed = apply_filters( 'the_content', $wp_embed->shortcode( '', $et_video_url ) );

									$video_embed = preg_replace('/<embed /','<embed wmode="transparent" ',$video_embed);
									$video_embed = preg_replace('/<\/object>/','<param name="wmode" value="transparent" /></object>',$video_embed); 
									$video_embed = preg_replace("/height=\"[0-9]*\"/", "height={$height}", $video_embed);
									$video_embed = preg_replace("/width=\"[0-9]*\"/", "width={$width}", $video_embed);
							
									echo $video_embed;
								?>
								</li>
								<?php $i++; ?>
							<?php endwhile; ?>
							<?php wp_reset_postdata();; ?>
						</ul> <!-- end .slides -->
					</div> <!-- end #video-content -->
				
				<?php if ( $video_postsnum < 5 ) { ?>
					<ul id="video-switcher" class="clearfix">
						<?php for ( $i = 0; $i < count( $video_info ); $i++ ) { ?>
							<li<?php if ( 0 == $i ) echo ' class="active_video"'; ?>>
								<div class="video_image">
									<?php print_thumbnail($video_info[$i]['thumb'], $video_info[$i]['thumbnail'], $video_info[$i]['titletext'], $video_info[$i]['width'], $video_info[$i]['height'], ''); ?>
									<span class="et_video_play"></span>
								</div> <!-- end .video_image -->
							</li>
						<?php } ?>
					</ul>
				<?php } ?>
				</div> <!-- end #video-slider-section -->
			</div> <!-- end #recent-videos -->
		<?php } ?>
	<?php } ?>
		
	</div> <!-- end #left-area -->
	
	<?php get_sidebar('home'); ?>
</div> <!-- end #content-area -->
	
<?php get_footer(); ?>