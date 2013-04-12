<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
	<?php if (et_get_option('lucid_integration_single_top') <> '' && et_get_option('lucid_integrate_singletop_enable') == 'on') echo (et_get_option('lucid_integration_single_top')); ?>
	<?php 
		if(isset($_GET['event'])){

			$id = $_GET['event'];
			$event = new fsEvent($id);
		}
	 ?>
	<h1 class="title"><?php echo $event->subject; ?></h1>

	<article id="post-<?php the_ID(); ?>" <?php post_class('entry clearfix event'); ?>>
		
		<div class="post_content clearfix">
			<?php 
				echo ('<div class="description" >'.wpautop($event->description).'</div>');
				the_content(); 
				//d($event);
			 ?>
			<div class="evento">
				<?php

					if(($event->location)){
						echo "<h3>Local: {$event->location}</h3>";
					} 
				?>

				<?php

					if(($event->date_admin_from && $event->date_admin_to) && ($event->date_admin_from != $event->date_admin_to)){
						echo "<h4>Data: {$event->date_admin_from} - {$event->date_admin_to}</h4>";
					} elseif ($event->date_admin_from) {
						echo "<h4>Data: {$event->date_admin_from}</h4>";
					}
				?>

				<?php
					if($event->time_admin_from && $event->time_admin_to) {
						echo "<h4>Horas: {$event->time_admin_from} - {$event->time_admin_to}</h4>";
					} elseif ($event->time_admin_from) {
						echo "<h4>Horas: {$event->time_admin_from}</h4>";
					}
				?>	
			</div>
			<br /><br /><br />
			<?php include_once('rede-social.php'); ?>

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

	?>
<?php endwhile; // end of the loop. ?>