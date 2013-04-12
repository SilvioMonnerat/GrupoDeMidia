<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
	<?php if (et_get_option('lucid_integration_single_top') <> '' && et_get_option('lucid_integrate_singletop_enable') == 'on') echo (et_get_option('lucid_integration_single_top')); ?>
	
	<article id="post-<?php the_ID(); ?>" <?php post_class('entry clearfix'); ?>>
		<h3 class="title"><?php the_title(); ?></h3>
		<?php
			global $wp_embed;
			$thumb = '';
			$et_full_post = get_post_meta( $post->ID, '_et_full_post', true );
			$width = apply_filters('et_blog_image_width',630);
				if ( 'on' == $et_full_post ) $width = apply_filters( 'et_single_fullwidth_image_width', 960 );
			$height = apply_filters('et_blog_image_height',250);
			$classtext = '';
			$titletext = get_the_title();
			$thumbnail = get_thumbnail($width,$height,$classtext,$titletext,$titletext,false,'Singleimage');
			$thumb = $thumbnail["thumb"];
			
			$et_video_url = get_post_meta( $post->ID, '_et_lucid_video_url', true );
		?>
		<?php if ( '' != $thumb && 'on' == et_get_option('lucid_thumbnails') ) { ?>
			<div class="post-thumbnail">
				<?php
					/*if( 'video' == get_post_format( $post->ID ) && '' != $et_video_url ) {
						$is_video = true;
					}*/
					
					if ( 'video' == get_post_format( $post->ID ) && '' != $et_video_url ){
						$video_embed = $wp_embed->shortcode( '', $et_video_url );

						$video_embed = preg_replace('/<embed /','<embed wmode="transparent" ',$video_embed);
						$video_embed = preg_replace('/<\/object>/','<param name="wmode" value="transparent" /></object>',$video_embed); 
						$video_embed = preg_replace("/height=\"[0-9]*\"/", "height=250", $video_embed);
						$video_embed = preg_replace("/width=\"[0-9]*\"/", "width={$width}", $video_embed);
				
						echo $video_embed;
					} else {
						print_thumbnail($thumb, $thumbnail["use_timthumb"], $titletext, $width, $height, $classtext);
					}
				?>
			</div> 	<!-- end .post-thumbnail -->
		<?php } ?>
		
		<div class="post_content clearfix">
			
			<?php the_content(); ?>				
			<?php wp_link_pages(array('before' => '<p><strong>'.esc_attr__('Pages','Lucid').':</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
			<?php edit_post_link(esc_attr__('Editar está página','Lucid')); ?>

		</div> 	<!-- end .post_content -->

	</article> <!-- end .entry -->
	
	<?php if (et_get_option('lucid_integration_single_bottom') <> '' && et_get_option('lucid_integrate_singlebottom_enable') == 'on') echo(et_get_option('lucid_integration_single_bottom')); ?>
		
	<?php 
		if ( et_get_option('lucid_468_enable') == 'on' ){
			if ( et_get_option('lucid_468_adsense') <> '' ) echo( et_get_option('lucid_468_adsense') );
			else { ?>
			   <a href="<?php echo esc_url(et_get_option('lucid_468_url')); ?>"><img src="<?php echo esc_url(et_get_option('lucid_468_image')); ?>" alt="468 ad" class="foursixeight" /></a>
	<?php 	}    
		}
	?>
	<div class="fb-comments" data-href="<?php the_permalink(); ?>" data-width="500" data-num-posts="5"></div>
	<?php 
		//if ( 'on' == et_get_option('lucid_show_postcomments') ) comments_template('', true);
		//echo do_shortcode('[fbcomments]');
	?>
<?php endwhile; // end of the loop. ?>