<?php

class rssPostWidget extends WP_Widget
{ 
    function rssPostWidget(){
      $widget_ops = array( 'description' => 'Custom rss feed' );
      $control_ops = array( 'width' => 400, 'height' => 300 );
      parent::WP_Widget( false, $name='ET Grupo De Midia - rss', $widget_ops, $control_ops );
    }

  /* Displays the Widget in the front-end */
    function widget( $args, $instance ){
    extract($args);
    $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? 'Grupo De Midia - Gallery' : esc_html( $instance['title'] ) );
    $imagePath = empty( $instance['imagePath'] ) ? '' : esc_url( $instance['imagePath'] );
    $aboutText = empty( $instance['aboutText'] ) ? '' : $instance['aboutText'];

    //$rss = mLoadXml('http://www.blogpulso.com.br/feed/atom/');
    $rss = mLoadXml('http://www.blogpulso.com.br/feed/', True, True);
    //d($rss);
    $posts = $rss->channel->xpath('item');
    echo $before_widget;

    if ( $title )
      echo $before_title . $title . $after_title; ?>
    <!-- Alterar o link abaixo para o caminho correto -->
    <a href="<?php echo get_option('Home'); ?>/pulso/" class="more"><?php _e( 'Mais', 'Lucid' ); ?></a>
    <div class="clearfix rssPulso category-box">
      <ul class="">
      <?php 
        
      ?>
      <?php 
        for ($i=0; $i < 3; $i++) { 
          $author = (string)$posts[$i]->creator;
          $title = (string)$posts[$i]->title;
          $link = (string)$posts[$i]->link;
          $description = (string)$posts[$i]->description;
          $imgfile = ereg_replace(' ', '-', $author);
          $imgfile = toASCII($imgfile);
          $img = "http://www.blogpulso.com.br/wp-content/uploads/2013/02/$imgfile.jpg";
          ?>
        <li class="clearfix">
          
          <a href="<?php echo $link ?>" target="_blank">
            <div class="thumb">
             <?php the_crop_image($img, '&amp;w=60&amp;h=60&amp;zc=1'); ?> 
              <!-- <img src="<?php echo $img ?>" class="category-image" alt="Post com Vídeo" width="60" height="60"> -->
            </div><!-- end .thumb -->
          </a>

          <h3><a href="<?php echo $link ?>" target="_blank"><?php echo $title ?></a></h3>
          <p class='meta-info'><a href="<?php echo $link ?>" target="_blank"><?php the_custom_length($description, 100);?></a></p>
        </li>          
          <?php    
        }
      ?>
      </ul>
      <img src="<?php bloginfo('template_url'); ?>/images/pulse.png" class="pulseImg">
    </div> <!-- end about me section -->
  <?php
    echo $after_widget;
  }

  /*Saves the settings. */
    function update( $new_instance, $old_instance ){
    $instance = $old_instance;
    $instance['title'] = strip_tags( $new_instance['title'] );
    $instance['imagePath'] = esc_url( $new_instance['imagePath'] );
    $instance['aboutText'] = current_user_can('unfiltered_html') ? $new_instance['aboutText'] : stripslashes( wp_filter_post_kses( addslashes($new_instance['aboutText']) ) );

    return $instance;
  }

  /*Creates the form for the widget in the back-end. */
  function form( $instance ){
    //Defaults
    $instance = wp_parse_args( (array) $instance, array( 'title'=>'Blog Pulso •••', 'imagePath'=>'', 'aboutText'=>'' ) );

    $title = esc_attr( $instance['title'] );
    $imagePath = esc_url( $instance['imagePath'] );
    $aboutText = esc_textarea( $instance['aboutText'] );

    # Title
    echo '<p><label for="' . $this->get_field_id('title') . '">' . 'Title:' . '</label><input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . $title . '" /></p>';
    # Image
    echo '<p><label for="' . $this->get_field_id('imagePath') . '">' . 'Image:' . '</label><textarea cols="20" rows="2" class="widefat" id="' . $this->get_field_id('imagePath') . '" name="' . $this->get_field_name('imagePath') . '" >'. $imagePath .'</textarea></p>';
    # About Text
    echo '<p><label for="' . $this->get_field_id('aboutText') . '">' . 'Text:' . '</label><textarea cols="20" rows="5" class="widefat" id="' . $this->get_field_id('aboutText') . '" name="' . $this->get_field_name('aboutText') . '" >'. $aboutText .'</textarea></p>';
  }

}// end AboutMeWidget class

function rssPostWidget() {
  register_widget('rssPostWidget');
}

add_action('widgets_init', 'rssPostWidget');


