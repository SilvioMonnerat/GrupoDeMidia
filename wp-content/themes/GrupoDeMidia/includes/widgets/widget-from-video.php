<?php class ETRecentFromWidget3 extends WP_Widget
{
    function ETRecentFromWidget3(){
		$widget_ops = array('description' => 'Displays recent posts from any category');
		$control_ops = array('width' => 400, 'height' => 300);
		parent::WP_Widget(false,$name='ET Vídeo Widget',$widget_ops,$control_ops);
    }

  /* Displays the Widget in the front-end */
    function widget($args, $instance){
		extract($args);
		$title = apply_filters('widget_title', empty($instance['title']) ? 'Vídeos ••• ' : $instance['title']);
		$posts_number = empty($instance['posts_number']) ? '' : (int) $instance['posts_number'];
		$blog_category = empty($instance['blog_category']) ? '' : (int) $instance['blog_category'];

		echo $before_widget;

		if ( $title )
		echo $before_title . $title . $after_title;
?>		<!-- Alterar o link abaixo para o caminho correto -->
		<a href="<?php echo get_option('Home'); ?>/videos/" class="more"><?php _e( 'Mais', 'Lucid' ); ?></a>
		<ul class="category-box noticaPost">
			<?php
			$j = 1;
			$recent_from_query = new WP_Query( apply_filters( 'et_recent_from_args', array(
				'post_type' => 'video',
				'showposts' => (int) $posts_number
				/*'cat' => $blog_category*/
			) ) );
			if ($recent_from_query->have_posts()) : while ($recent_from_query->have_posts()) : $recent_from_query->the_post(); ?>
				<li class="clearfix<?php if ( $j % 2 == 0 ) echo ' recent_even'; ?>">
					<?php
						$thumb = '';
						$width = 60;
						$height = 60;
						$classtext = 'category-image';
						$titletext = get_the_title();
						$thumbnail = get_thumbnail($width,$height,$classtext,$titletext,$titletext,false,'Recent');
						$thumb = $thumbnail["thumb"];
					?>
					<?php if ( '' != $thumb ){ ?>
						<div class="thumb">
							<a href="<?php the_permalink(); ?>">
								<?php 
									//print_thumbnail($thumb, $thumbnail["use_timthumb"], $titletext, $width, $height, $classtext);
									the_crop_image($thumb, '&amp;w=60&amp;h=60&amp;zc=1');
								?>
							</a>
						</div> 	<!-- end .thumb -->
					<?php } ?>
					<h3><a href="<?php the_permalink(); ?>"><?php the_custom_length(get_the_title(), 35); ?></a></h3>
					<p class="meta-info"><a href="<?php the_permalink(); ?>"><?php the_custom_excerpt(75); ?></a></p>
				</li>
			<?php
				$j++;
			endwhile; endif; wp_reset_postdata(); ?>
		</ul>
<?php
		echo $after_widget;
	}

  /*Saves the settings. */
    function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['title'] = stripslashes($new_instance['title']);
		$instance['posts_number'] = (int) $new_instance['posts_number'];
		$instance['blog_category'] = (int) $new_instance['blog_category'];

		return $instance;
	}

  /*Creates the form for the widget in the back-end. */
    function form($instance){
		//Defaults
		$instance = wp_parse_args( (array) $instance, array('title'=>'Vídeos ••• ', 'posts_number'=>'3', 'blog_category'=>'') );

		$title = esc_attr($instance['title']);
		$posts_number = (int) $instance['posts_number'];
		$blog_category = (int) $instance['blog_category'];

		# Title
		echo '<p><label for="' . $this->get_field_id('title') . '">' . 'Title:' . '</label><input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . $title . '" /></p>';
		# Number Of Posts
		echo '<p><label for="' . $this->get_field_id('posts_number') . '">' . 'Números de posts:' . '</label><input class="widefat" id="' . $this->get_field_id('posts_number') . '" name="' . $this->get_field_name('posts_number') . '" type="text" value="' . $posts_number . '" /></p>';
		# Category ?>
		<?php 
			$cats_array = get_categories('hide_empty=0');
		?>
		<p>
			<label for="<?php echo $this->get_field_id('blog_category'); ?>">Category</label>
			<select name="<?php echo $this->get_field_name('blog_category'); ?>" id="<?php echo $this->get_field_id('blog_category'); ?>" class="widefat">
				<?php foreach( $cats_array as $category ) { ?>
					<option value="<?php echo $category->cat_ID; ?>"<?php selected( $instance['blog_category'], $category->cat_ID ); ?>><?php echo $category->cat_name; ?></option>
				<?php } ?>
			</select>
		</p> 
		<?php
	}

}// end ETRecentFromWidget class

function ETRecentFromWidget3Init() {
  register_widget('ETRecentFromWidget3');
}

add_action('widgets_init', 'ETRecentFromWidget3Init');