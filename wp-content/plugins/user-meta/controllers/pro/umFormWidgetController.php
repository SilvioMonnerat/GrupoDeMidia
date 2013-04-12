<?php


if( !class_exists( 'umFormWidget' ) ) :
class umFormWidget extends WP_Widget {
    
    /**
     * Register widget with WordPress.
     */
    public function __construct() {  
        global $userMeta;
        
        parent::__construct(
        	'umForm', // Base ID
        	__( 'User Meta Registration/Profile Form', $userMeta->name ), // Name
        	array( 'description' => __( 'Show user registration or profile form as widget', $userMeta->name ) ) // Args
        );         
      
    }
    
    /**
     * Front-end display of widget.
     * @see WP_Widget::widget()
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {
        global $userMeta;            
        extract( $args );
        
        //$userMeta->dump($args);
        
        $title = is_user_logged_in() ? @$instance[ 'user_title' ] : @$instance[ 'guest_title' ];        
        $title = apply_filters( 'widget_title', $title );
                
        echo $before_widget;
        if ( ! empty( $title ) )
        	echo $before_title . $title . $after_title;
                       
        echo $userMeta->userUpdateRegisterProcess( @$instance['action_type'], @$instance['form_name'] );
               
        echo $after_widget;
    }
    
    /**
     * Sanitize widget form values as they are saved.
     * @see WP_Widget::update()
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        
        return array_map( 'strip_tags', $new_instance );
        
        /*
        $instance = array();
        $instance[ 'guest_title' ]  = strip_tags( $new_instance[ 'guest_title' ] );
        $instance[ 'user_title' ]   = strip_tags( $new_instance[ 'user_title' ] );
        $instance[ 'hide_from_login_page' ]   = strip_tags( $new_instance[ 'hide_from_login_page' ] );
        
        return $instance;
        */                   
    }
    
    /**
     * Back-end widget form.
     * @see WP_Widget::form()
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {
        global $userMeta;
        
        $guest_title    = isset( $instance[ 'guest_title' ] )   ? $instance[ 'guest_title' ]    : null;
        $user_title     = isset( $instance[ 'user_title' ] )    ? $instance[ 'user_title' ]     : null;       
        $action_type    = isset( $instance[ 'action_type' ] )   ? $instance[ 'action_type' ]    : null;
        $form_name      = isset( $instance[ 'form_name' ] )     ? $instance[ 'form_name' ]      : null;
        
        $formsList = array();
        $formsList[] = null;
        $forms = $userMeta->getData( 'forms' );  
        if( is_array( $forms ) ){
            foreach( $forms as $key => $val )
                $formsList[] = $key;
        }
        
        
        echo $userMeta->createInput( $this->get_field_name( 'guest_title' ), 'text', array(
            'value'     => esc_attr( $guest_title ),
            'label'     => __( 'Guest Title:', $userMeta->name ),
            'id'        => $this->get_field_id( 'guest_title' ),
            'class'     => 'widefat',
            'after'     => '<i>' . __( 'Only guest user can see this title', $userMeta->name ) . '</i>',
            'enclose'   => 'p'
        ) );
        
        echo $userMeta->createInput( $this->get_field_name( 'user_title' ), 'text', array(
            'value'     => esc_attr( $user_title ),
            'label'     => __( 'Logged-In User Title:', $userMeta->name ),
            'id'        => $this->get_field_id( 'user_title' ),
            'class'     => 'widefat',
            'after'     => '<i>' . __( 'Only logged-in user can see this title', $userMeta->name ) . '</i>',
            'enclose'   => 'p'
        ) ); 
        
        echo $userMeta->createInput( $this->get_field_name( 'action_type' ), 'select', array(
            'value'     => esc_attr( $action_type ),
            'label'     => __( 'Action Type:', $userMeta->name ),
            'id'        => $this->get_field_id( 'action_type' ),
            'class'     => 'widefat',
            'enclose'   => 'p'
        ), array( '' ,'profile', 'registration', 'both', 'none' ) );         
        
        echo $userMeta->createInput( $this->get_field_name( 'form_name' ), 'select', array(
            'value'     => esc_attr( $form_name ),
            'label'     => __( 'Form Name:', $userMeta->name ),
            'id'        => $this->get_field_id( 'form_name' ),
            'class'     => 'widefat',
            'enclose'   => 'p'
        ), $formsList );                  
                           
    }    
}
endif;

// register umLoginWidget widget
add_action( 'widgets_init', create_function( '', 'register_widget( "umFormWidget" );' ) );
?>