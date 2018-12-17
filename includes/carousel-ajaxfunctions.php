<?php





/*
 * Below is a carousel ajax function and callback, 
 * complete with console.logs and echos to verify functionality
 */

/*
// For adding a book from the admin dashboard
add_action( 'admin_footer', 'wpbooklist_carousel_action_javascript' );
add_action( 'wp_ajax_wpbooklist_carousel_action', 'wpbooklist_carousel_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_carousel_action', 'wpbooklist_carousel_action_callback' );


function wpbooklist_carousel_action_javascript() { 
	?>
  	<script type="text/javascript" >
  	"use strict";
  	jQuery(document).ready(function($) {
	  	$("#wpbooklist-admin-addbook-button").click(function(event){

		  	var data = {
				'action': 'wpbooklist_carousel_action',
				'security': '<?php echo wp_create_nonce( "wpbooklist_carousel_action_callback" ); ?>',
			};
			console.log(data);

	     	var request = $.ajax({
			    url: ajaxurl,
			    type: "POST",
			    data:data,
			    timeout: 0,
			    success: function(response) {
			    	console.log(response);
			    },
				error: function(jqXHR, textStatus, errorThrown) {
					console.log(errorThrown);
		            console.log(textStatus);
		            console.log(jqXHR);
				}
			});

			event.preventDefault ? event.preventDefault() : event.returnValue = false;
	  	});
	});
	</script>
	<?php
}

// Callback function for creating backups
function wpbooklist_carousel_action_callback(){
	global $wpdb;
	check_ajax_referer( 'wpbooklist_carousel_action_callback', 'security' );
	//$var1 = filter_var($_POST['var'],FILTER_SANITIZE_STRING);
	//$var2 = filter_var($_POST['var'],FILTER_SANITIZE_NUMBER_INT);
	echo 'hi';
	wp_die();
}*/
?>