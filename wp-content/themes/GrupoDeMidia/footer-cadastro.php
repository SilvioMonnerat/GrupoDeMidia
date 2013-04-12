<?php
	$footer_sidebars = array('footer-area-1','footer-area-2','footer-area-3');
	$any_widget_area_active = is_active_sidebar( $footer_sidebars[0] ) || is_active_sidebar( $footer_sidebars[1] ) || is_active_sidebar( $footer_sidebars[2] );
?>
		
		</div> <!-- end .container -->
	<div id="preFooter" class="container clearfix">
		<!-- <div id="newsletter" class="container clearfix"><?php insert_cform('Newsletter'); ?></div> -->
		<div id="twitter">
			<a class="twitter-timeline" href="https://twitter.com/grupodemidia" data-widget-id="321753539460349954">Tweets by @grupodemidia</a> 
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>


				
		</div>
		<div id="facebook">
			<!-- https://www.facebook.com/photo.php?fbid=270688569658996&set=a.127585053969349.19158.127584683969386&type=1 -->
		    <!-- <script src="https://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php/pt_BR" type="text/javascript"></script> -->

		    <!-- <script type="text/javascript">FB.init("270688569658996");</script>
		    <fb:fan profile_id="270688569658996" connections="6" width="415" height="200" css="" stream="false"></fb:fan> -->
		    <fb:like-box href="http://www.facebook.com/GrupoDeMidiaRJ"  height="349" width="461" show_faces="true" stream="false" header="false"></fb:like-box>

		</div>
		<?php //d($footer_sidebars); ?>

		<div class="container-footer clearfix">
			<?php
				$menuID = 'bottom-menu';
				$footerNav = '';

				if (function_exists('wp_nav_menu')) $footerNav = wp_nav_menu( array( 'theme_location' => 'footer-menu', 'container' => '', 'fallback_cb' => '', 'menu_id' => $menuID, 'menu_class' => 'bottom-nav', 'echo' => false, 'depth' => '1' ) );
				if ($footerNav == '') show_page_menu($menuID);
				else echo($footerNav);
			?>
			<div class="rede-menu">
				<?php
					$social_icons = '';
					$et_rss_url = '' != et_get_option('lucid_rss_url') ? et_get_option('lucid_rss_url') : get_bloginfo('rss2_url');
					if ( 'on' == et_get_option('lucid_show_facebook_icon') ) $social_icons['facebook'] = array('image' => get_bloginfo('template_directory') . '/images/facebook.png', 'url' => et_get_option('lucid_facebook_url'), 'alt' => 'Facebook' );
					if ( 'on' == et_get_option('lucid_show_twitter_icon') ) $social_icons['twitter'] = array('image' => get_bloginfo('template_directory') . '/images/twitter.png', 'url' => et_get_option('lucid_twitter_url'), 'alt' => 'Twitter' );
					if ( 'on' == et_get_option('lucid_show_rss_icon') ) $social_icons['rss'] = array('image' => get_bloginfo('template_directory') . '/images/rss.png', 'url' => $et_rss_url, 'alt' => 'Rss' );
					$social_icons = apply_filters('et_social_icons', $social_icons);
					if ( !empty($social_icons) ) {
						echo '<div id="social-icons">';
						foreach ($social_icons as $icon) {
							echo "<a href='" . esc_url( $icon['url'] ) . "' target='_blank'><img alt='" . esc_attr( $icon['alt'] ) . "' src='" . esc_url( $icon['image'] ) . "' /></a>";
						}
						
					}
				?>
			</div>
		</div> <!-- end .container -->
	</div>
	</div> <!-- end #main-area -->
	<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/jquery.jqtransform.js"></script>
	<script type="text/javascript">jQuery('#li--4').jqTransform({imgPath:'<?php bloginfo('template_url'); ?>/jqtransformplugin/img/'});</script>
	<footer id="main-footer">
	<?php if ( $any_widget_area_active ) { ?>
		<div id="footer-divider"></div>
	<?php } ?>
		<div class="container">
			<div id="footer-widgets" class="clearfix">
				<?php
					if ( $any_widget_area_active ) {
						foreach ( $footer_sidebars as $key => $footer_sidebar ){
							if ( is_active_sidebar( $footer_sidebar ) ) {
								echo '<div class="footer-widget' . (  2 == $key ? ' last' : '' ) . '">';
								dynamic_sidebar( $footer_sidebar );
								echo '</div> <!-- end .footer-widget -->';
							}
						}
					}
				?>
                
			</div> <!-- end #footer-widgets -->	
            
		</div> <!-- end .container -->
		<?php if ( 'on' == et_get_option( 'lucid_728_enable', 'false' ) ){ ?>
			<div id="bottom-advertisment">
				<div class="container">
					<?php 
						if ( ( $lucid_728_adsense = et_get_option('lucid_728_adsense') ) && '' != $lucid_728_adsense ) echo( $lucid_728_adsense );
						else { ?>
						   <a href="<?php echo esc_url(et_get_option('lucid_728_url')); ?>"><img src="<?php echo esc_url(et_get_option('lucid_728_image')); ?>" /></a>
					<?php } ?>
				</div> <!-- end .container -->
			</div>
		<?php } ?>
	</footer> <!-- end #main-footer -->
	
	<div id="footer-bottom">
		<div class="container clearfix">
			<p>Copyright Grupo de Mídia do RJ - Tel 21 3392.1478 - <a href="mailto:contato@grupodemidiarj.com.br" target="_blank">contato@grupodemidiarj.com.br</a></p>
		</div> <!-- end .container -->
	</div> <!-- end #footer-bottom -->
	
	<?php wp_footer(); ?>
</body>
</html>