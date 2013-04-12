<?php 
	$featured_slider_class = '';
	if ( 'on' == et_get_option('lucid_slider_auto') ) $featured_slider_class .= ' et_slider_auto et_slider_speed_' . et_get_option('lucid_slider_autospeed');
	if ( 'slide' == et_get_option('lucid_slider_effect') ) $featured_slider_class .= ' et_slider_effect_slide';
?>
<div id="featured_section">
	<div id="featured" class="flexslider<?php echo $featured_slider_class; ?>">
		<ul class="slides">
		<?php
			$info = array();
			$posts_number = empty($instance['posts_number']) ? '' : (int) $instance['posts_number'];
			$featured_cat = et_get_option('lucid_feat_cat');
			$featured_num = et_get_option('lucid_featured_num');

			/*			
			if ( 'false' == et_get_option('lucid_use_pages','false') ) {
				$featured_query = new WP_Query( apply_filters( 'et_featured_post_args', array(
					'posts_per_page' 	=> $featured_num,
					'cat' 				=> get_catId( et_get_option('lucid_feat_posts_cat'))
				) ) );
			} else {
				global $pages_number;
				
				if ( '' != et_get_option('lucid_feat_pages') ) $featured_num = count( et_get_option('lucid_feat_pages') );
				else $featured_num = $pages_number;
				
				$featured_query = new WP_Query(
					apply_filters( 'et_featured_page_args',
						array(	'post_type'			=> 'page',
								'orderby'			=> 'menu_order',
								'order' 			=> 'ASC',
								'post__in' 			=> (array) et_get_option('lucid_feat_pages'),
								'posts_per_page' 	=> (int) $featured_num
							)
					)	
				);
			}*/
			if('false' == et_get_option('lucid_use_pages','false')){
				$featured_query = new WP_Query( apply_filters('et_featured_post_args',array(
					'post_type'	     => 'noticia',
					'posts_per_page' => $featured_num,
					'showpost'       => (int) $posts_number,
					'tax_query' => array(array(
											'taxonomy' => 'noticia_category',
											'field'    => 'slug',
											'terms'    => 'destaque',
											)
									    )
					)));
				}else{
					global $pages_number;
					if ( '' != et_get_option('lucid_feat_pages') ) $featured_num = count( et_get_option('lucid_feat_pages') );
						else $featured_num = $pages_number;
						$featured_query = new WP_Query( apply_filters( 'et_featured_page_args',	array(	
							'post_type'	     => 'noticia',
							'orderby'        => 'menu_order',
							'order'          => 'ASC',
							'post__in'       => (array) et_get_option('lucid_feat_pages'),
							'posts_per_page' => (int) $featured_num,
							)));
				} 
			
			$i = 1;
			$count = 1;
			while ( $featured_query->have_posts() ) : $featured_query->the_post();
				$category = get_the_category();
				
				$info[$i]['number'] = $count++;
				//$info[$i]['date'] = get_the_time( 'd' ) .'<span>' . get_the_time( 'D' ) . '</span>';
				$info[$i]['title'] = ( $custom_title = get_post_meta( $post->ID, 'featured_title', true ) ) && '' != $custom_title ? $custom_title : apply_filters( 'the_title', get_the_title() );
				//$info[$i]['content'] =  get_custom_excerpt(40);
				//$info[$i]['postinfo'] = __( 'Postado em: ', 'Lucid' ) . $category[0]->cat_name;
			?>
				<li class="slide">					
					<a href="<?php echo esc_url( get_permalink() ); ?>">							
						<?php
							$width = apply_filters( 'slider_image_width', 960 );
							$height = apply_filters( 'slider_image_height', 360 );
							$title = get_the_title();
							//$content = get_custom_excerpt(50);
							$thumbnail = get_thumbnail($width,$height,'',$title,$title,false,'Featured');
							$thumb = $thumbnail["thumb"];
							//d($thumb);
							//d($thumbnail["use_timthumb"]);
							//print_thumbnail($thumb, $thumbnail["use_timthumb"], $title, $width, $height, '');
							the_crop_image($thumb, '&amp;w=960&amp;h=360&amp;zc=1');

						    /*$width = 120;
						    $height = 120;
						    $titletext = get_the_title();

						    $thumbnail = get_thumbnail($width,$height,'portfolio',$titletext,$titletext,true,'Portfolio');
						    $thumb = $thumbnail["thumb"];*/ 
						?>
           				<?php //print_thumbnail($thumb, $thumbnail["use_timthumb"], $title, $width, $height, 'Featured'); ?>
						<span class="overlay"></span>
					</a>
				</li> <!-- end .slide -->
		<?php
				$i++;
			endwhile; wp_reset_postdata();
		?>
		</ul>
	</div> <!-- end #featured -->

	<?php if ( $featured_num < 4 ){ ?>
		<div id="switcher-container">
			<ul id="switcher" class="clearfix">
				<?php for ( $i = 1; $i <= $featured_num; $i++ ) { ?>
					<li<?php if ( 1 == $i ) echo ' class="active-slide"'; if ( 3 == $i ) echo ' class="last"'; ?>>
						<div class="switcher-content">
							<span class="post-meta"><?php echo $info[$i]['number']; ?></span>
							<h2><?php echo esc_html( $info[$i]['title'] ); ?></h2>
							<p class="meta-info"><?php //echo esc_html( $info[$i]['postinfo'] ); ?></p>
						</div> <!-- end .switcher-content -->
					</li>
				<?php } ?>
			</ul>
		</div> <!-- end #switcher-container -->
	<?php } ?>
</div> <!-- end #featured_section -->