<?php 

	//get $_POST

	if(isset($_POST['gm_search'])){

		$query = 'SELECT * FROM  `wp_usermeta` WHERE  `meta_value` LIKE  '%sil%' LIMIT 0 , 30';

		global $wpdb;
		$wpdb->query($wpdb->prepare("SELECT * FROM `wp_usermeta` WHERE `meta_value` LIKE  '%%%s%%' LIMIT ', $_POST['gm_search']));
	}