<?php

/**
 * Adicionamos uma acção no inicio do carregamento do WordPress
 * através da função add_action( 'init' )
 */
add_action( 'init', 'create_post_type_curso' );

/**
 * Esta é a função que é chamada pelo add_action()
 */
function create_post_type_curso() {

    /**
     * Labels customizados para o tipo de post
     * 
     */
    $labels = array(
	    'name'                 => _x('Cursos', 'post type general name'),
	    'singular_name'        => _x('Cursos', 'post type singular name'),
	    'add_new'              => _x('Adicionar Novo', 'curso'),
	    'add_new_item'         => __('Adicionar Novo Curso'),
	    'edit_item'            => __('Editar Cursos'),
	    'new_item'             => __('Novo Curso'),
	    'all_items'            => __('Cursos e Eventos'),
	    'view_item'            => __('Vizualizar Cursos'),
	    'search_items'         => __('Pesquisar por Cursos'),
	    'not_found'            => __('Nenhum Cursos encontrado'),
	    'not_found_in_trash'   => __('Nenhum Cursos encontrado na lixeira'),
	    'parent_item_colon'    => '',
	    'menu_name'            => 'Cursos e Eventos'
    );
    
    /**
     * Registamos o tipo de post curso através desta função
     * passando-lhe os labels e parâmetros de controlo.
     */
    register_post_type( 'curso', array(
	    'labels'               => $labels,
	    'public'               => true,
	    'publicly_queryable'   => true,
	    'show_ui'              => true,
	    'show_in_menu'         => true,
	    'has_archive'          => 'cursos',
	    'rewrite'              => array('slug' 
	    	                   => 'cursos','with_front' 
	    	                   => false,),
	    'capability_type'      => 'post',
	    'has_archive'          => true,
	    'hierarchical'         => false,
	    'menu_position'        => 7,
	    'supports'             => array('title','editor','author','thumbnail','excerpt','comments')
	    )
    );
    
    /**
     * Registamos a categoria de curso para o tipo de post curso
     */
    register_taxonomy( 'curso_category', array( 'curso' ), array(
        'hierarchical'         => true,
        'label'                => __( 'Curso Category' ),
        'labels'               => array( // Labels customizadas
	    'name'                 => _x( 'Categories', 'taxonomy general name' ),
	    'singular_name'        => _x( 'Category', 'taxonomy singular name' ),
	    'search_items'         => __( 'Search Categories' ),
	    'all_items'            => __( 'All Categories' ),
	    'parent_item'          => __( 'Parent Category' ),
	    'parent_item_colon'    => __( 'Parent Category:' ),
	    'edit_item'            => __( 'Edit Category' ),
	    'update_item'          => __( 'Update Category' ),
	    'add_new_item'         => __( 'Add New Category' ),
	    'new_item_name'        => __( 'New Category Name' ),
	    'menu_name'            => __( 'Categorias' ),
	),
        'show_ui'              => true,
        'show_in_tag_cloud'    => true,
        'query_var'            => true,
        'rewrite'              => array('slug'
                               => 'cursos/categories','with_front' 
                               => false,),
        )
    );
    
    /** 
     * Esta função associa tipos de categorias com tipos de posts.
     * Aqui associamos as tags ao tipo de post curso.
     */
    register_taxonomy_for_object_type( 'tags', 'curso' );
    
}
