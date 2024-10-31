<?php 
/*
Plugin Name: Scattered Polaroids Image Gallery
Plugin URI: http://www.bluelevel.in/plugins/SPIGallery
Description: This plugin adds a Scattered Polaroids Image Gallery your site. Just enter the shortcode [SPIGallery] in any post or page editor and voila! 
Author: Bluelevel 
Author URI: http://www.bluelevel.in
version: 1.0
*/

define( 'SPIG_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

//Calling the files needed to get the Gallery working
function SPIG_files(){
    
    wp_register_style('gallery', plugins_url('/css/gallery-styles.css', __FILE__), true);
    wp_register_style('fontawesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css', true);
    wp_enqueue_style('gallery');
    wp_enqueue_style('fontawesome');
    
    wp_enqueue_script('mordenizr', plugins_url('/js/modernizr.min.js', __FILE__), true);
    wp_enqueue_script('classie', plugins_url('/js/classie.js', __FILE__), true);
    wp_enqueue_script('gallery', plugins_url('/js/photostack.js', __FILE__), true);
}
add_action('after_setup_theme', 'SPIG_files');

//Calling the files needed to get the settings page working
function SPIG_admin_files(){
	 
		//Wordpress's own Color Picker - Saves Space!
		wp_enqueue_style( 'wp-color-picker' ); 
         
        // Include our custom jQuery file with WordPress Color Picker dependency
        wp_enqueue_script( 'cpscript', plugins_url( '/js/col-pick-script.js', __FILE__ ), array( 'wp-color-picker' ), false, true ); 
    
}
add_action('admin_init', 'SPIG_admin_files');

function SPIG_custom_post(){
    
    register_post_type('SPIGallery', array(
        'public' => true,
        'label' => 'Scattered Polaroid Image Gallery',
        'menu_icon' => 'dashicons-format-gallery',
        'labels' => array(
            'name' => 'Added Images',
            'add_new' => 'Add new Image',
            'singular_name' => 'Scattered Polaroid Image Gallery',
        ),
        'supports' => array('title', 'editor', 'thumbnail'),
    ));
    
}
add_action('init', 'SPIG_custom_post');

add_action( 'admin_menu', 'SPIG_add_admin_menu' );
add_action( 'admin_init', 'SPIG_settings_init' );


function SPIG_add_admin_menu(  ) { 

	add_menu_page( 'Scattered Polaroid Image Gallery', 'Scattered Polaroid Image Gallery', 'manage_options', 'SPIGallery_options_page', 'SPIG_options_page', 'dashicons-images-alt' );

}


function SPIG_settings_init(  ) { 

	register_setting( 'pluginPage', 'SPIG_settings' );

	add_settings_section(
		'SPIG_pluginPage_section', 
		__( 'Choose the colors you like for this plugin.', 'wordpress' ), 
		'SPIG_settings_section_callback', 
		'pluginPage'
	);

	add_settings_field( 
		'SPIG_text_field_0', 
		__( 'Choose the Background Color for the Gallery section.', 'wordpress' ), 
		'SPIG_text_field_0_render', 
		'pluginPage', 
		'SPIG_pluginPage_section' 
	);

	add_settings_field( 
		'SPIG_text_field_1', 
		__( 'Choose the Background Color for the Polaroid images.', 'wordpress' ), 
		'SPIG_text_field_1_render', 
		'pluginPage', 
		'SPIG_pluginPage_section' 
	);

	add_settings_field( 
		'SPIG_text_field_2', 
		__( 'Choose the Text Color for the Polaroid images.', 'wordpress' ), 
		'SPIG_text_field_2_render', 
		'pluginPage', 
		'SPIG_pluginPage_section' 
	);



}


function SPIG_text_field_0_render(  ) { 

	$options = get_option( 'SPIG_settings' );
	?>
	<input type='text' class="color-field" name='SPIG_settings[SPIG_text_field_0]' value='<?php echo $options['SPIG_text_field_0']; ?>'>
	<?php

}


function SPIG_text_field_1_render(  ) { 

	$options = get_option( 'SPIG_settings' );
	?>
	<input type='text' class="color-field" name='SPIG_settings[SPIG_text_field_1]' value='<?php echo $options['SPIG_text_field_1']; ?>'>
	<?php

}

function SPIG_text_field_2_render(  ) { 

	$options = get_option( 'SPIG_settings' );
	?>
	<input type='text' class="color-field" name='SPIG_settings[SPIG_text_field_2]' value='<?php echo $options['SPIG_text_field_2']; ?>'>
	<?php

}


function SPIG_settings_section_callback(  ) { 

	echo __( '', 'wordpress' );

}


function SPIG_options_page(  ) { 

	?>
	<form action='options.php' method='post'>
		
		<h2><i class="dashicons dashicons-images-alt"></i>  Scattered Polaroid Image Gallery</h2>
		
		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>
		
	</form>
	<?php

}


add_theme_support( 'post-thumbnails'); // Adding it for posts
add_image_size( 'SPIGsmall', 240, 240, true ); // Post thumbnails, hard crop mode


//Adding the shortcode to display the Gallery
function SPIG_shortcode($atts){   ?>
 <section id="photostack" class="photostack photostack-start">
     <div>
  <?php 
    global $post;
    $args = array( 'posts_per_page' => -1, 'post_type'=> 'SPIGallery' );
    $myposts = get_posts( $args );  
    foreach( $myposts as $post ) : setup_postdata($post); 
    $portfolio_thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'SPIGsmall');  
	$content = get_the_content();
	$options = get_option( 'SPIG_settings' );
     ?>
					<figure>
						<img src="<?php echo $portfolio_thumb[0]; ?>" width="240" height="240" alt="Image"/>
						<figcaption>
							<h2 class="photostack-title"><?php the_title(); ?></h2>
							<div class="photostack-back">
								<?php echo substr($content, 0, 180); ?>
							</div>
						</figcaption>
					</figure>
    <?php endforeach;?>
				</div>
                <script>
                    new Photostack( document.getElementById( 'photostack' ), {
                        callback : function( item ) {
                            //console.log(item)
                        }
                    } );
                </script>
			</section>
<style>
	.js .photostack {
		background: <?php echo $options[SPIG_text_field_0]; ?>;
}
	.js .photostack img{
		height: 240px!important;
}
	.js .photostack figure {
		background: <?php echo $options[SPIG_text_field_1]; ?>;
		color: <?php echo $options[SPIG_text_field_2]; ?>;
		height: 56%;
    -webkit-box-shadow: 50px 45px 20px #ddd;
    -moz-box-shadow: 50px 45px 20px #ddd;
    box-shadow: 50px 45px 20px #ddd; 
}
	.js .photostack figure .photostack-back {
		background: <?php echo $options[SPIG_text_field_1]; ?>;
		color: <?php echo $options[SPIG_text_field_2]; ?>
}
	.js .photostack figure h2.photostack-title {
		color: <?php echo $options[SPIG_text_field_2]; ?>;
		margin-top: 11px;
}
</style>
<?php
}
add_shortcode('SPIGallery', 'SPIG_shortcode');

	add_action( 'init', 'my_buttons' );
	function my_buttons() {
		add_filter( "mce_external_plugins", "my_add_buttons" );
		add_filter( 'mce_buttons', 'my_register_buttons' );
	}
	function my_add_buttons( $plugin_array ) {
		$plugin_array['SPIG'] = SPIG_PLUGIN_URL . '/tinymce/mce-button.js';
		return $plugin_array;
	}
	function my_register_buttons( $buttons ) {
		array_push( $buttons, 'mybutton' );
		return $buttons;
	}
	
	
	
?>