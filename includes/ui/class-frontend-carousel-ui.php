<?php
/**
 * WPBookList Carousel UI Class
 *
 * @author   Jake Evans
 * @category Carousel UI
 * @package  Includes/UI
 * @version  1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WPBookList_Carousel_UI', false ) ) :
/**
 * WPBookList_Carousel_UI Class.
 */
class WPBookList_Carousel_UI {

	public $table = '';
	public $speed = 5000;
	public $titlecount = 3;
	public $transition = 'rotateleft';
	public $coverheight = 170;
	public $coverwidth = 103;

	public $books_array = array();
	public $html_output = '';
	public $display_options_actual = array();
	public $display_options_table = '';


	public function __construct($atts_array) {

		$this->table = $atts_array['table'];
		$this->speed = $atts_array['speed'];
		$this->titlecount = $atts_array['titlecount'];
		$this->transition = $atts_array['transition'];
		$this->coverwidth = $atts_array['coverwidth'];
		$this->coverheight = $atts_array['coverheight'];
		$this->coverheight = $atts_array['coverheight'];
		$this->action = $atts_array['action'];
		$this->build_book_table();
		$this->get_books();
		$this->get_display_options();
		$this->output_html();
	}

	private function build_book_table(){
		global $wpdb;
		if($this->table != $wpdb->prefix.'wpbooklist_jre_saved_book_log'){
			$this->table = $wpdb->prefix.'wpbooklist_jre_'.$this->table;
		}
	}

	private function get_books(){
		global $wpdb;
		$this->books_array = $wpdb->get_results("SELECT * FROM $this->table");
	}

	private function get_display_options(){
		global $wpdb;
		// Building display options table
		if($this->table == $wpdb->prefix.'wpbooklist_jre_saved_book_log'){
			$this->display_options_table = $wpdb->prefix.'wpbooklist_jre_user_options';
		} else {
			$temp = explode('_', $this->table);
			$temp = array_pop($temp);
			$this->display_options_table = $wpdb->prefix.'wpbooklist_jre_settings_'.strtolower($temp);
		}

		// Getting all display options
		$this->display_options_actual = $wpdb->get_row($wpdb->prepare("SELECT * FROM $this->display_options_table WHERE ID = %d", 1));
	}

	private function output_html(){

		$string  = '<div data-table="'.$this->table.'" data-speed="'.$this->speed.'" data-titlecount="'.$this->titlecount.'" data-transition="'.$this->transition.'" data-coverwidth="'.$this->coverwidth.'" data-coverheight="'.$this->coverheight.'" data-table="'.$this->table.'"" class="wpbooklist-carousel-shortcode-atts-div-class" id="wpbooklist-carousel-shortcode-atts-div"></div>';

		// Add the nav arrows
		$string = $string.'<div id="wpbooklist-carousel-flex-container"><div class="wpbooklist-carousel-nav-div" id="wpbooklist-carousel-nav-div-left"><img class="wpbooklist-carousel-nav-arrow" id="wpbooklist-carousel-nav-left" src="'.CAROUSEL_ROOT_IMG_URL.'leftbutton.png'.'" /></div>';


		$string = $string.'<div id="wpbooklist_carousel_main_display_div" class="wpbooklist_carousel_main_display_div_class">';

		if($this->books_array == null || sizeof($this->books_array) == 0){
			$this->html_output = '<div>Uh-oh! You haven\'t added any books to the \' \' Library!</div>';
			return;
		}

		foreach ($this->books_array as $key => $book) {
			$string = $string.'<div class="wpbooklist_carousel_entry_div">
		                <p style="display:none;" id="wpbooklist-hidden-isbn1">'.$book->isbn.'</p>
		                <div class="wpbooklist_carousel_inner_main_display_div">';

		                $page_url = get_permalink($book->page_yes);
		                $post_url = get_permalink($book->post_yes);

		                if($this->action == 'page'){
		                	if($page_url != null && $page_url != '' && $page_url != undefined){
		                    	$string = $string.'<a href="'.$page_url.'"><img style="width:'.$this->coverwidth.'px; height:'.$this->coverheight.'px;" class="wpbooklist_cover_image_class" data-bookid="'.$book->ID.'" data-booktable="'.$this->table.'" id="wpbooklist_carousel_cover_image" src="'.$book->image.'" style="opacity: 1;"><span class="wpbooklist-carousel-title-span-class" id="wpbooklist-carousel-title-span-id-'.$key.'">'.$book->title.'</span></a>';
			                } else {
			                	$string = $string.'<img style="width:'.$this->coverwidth.'px; height:'.$this->coverheight.'px;" class="wpbooklist_cover_image_class wpbooklist-show-book-colorbox" data-bookid="'.$book->ID.'" data-booktable="'.$this->table.'" id="wpbooklist_carousel_cover_image" src="'.$book->image.'" style="opacity: 1;"><span class="wpbooklist-carousel-title-span-class" id="wpbooklist-carousel-title-span-id-'.$key.'">'.$book->title.'</span>';
			                }
			            }

		                if($this->action == 'post'){
		                	if($post_url != null && $post_url != '' && $post_url != undefined){
		                    $string = $string.'<a href="'.$post_url.'"><img style="width:'.$this->coverwidth.'px; height:'.$this->coverheight.'px;" class="wpbooklist_cover_image_class" data-bookid="'.$book->ID.'" data-booktable="'.$this->table.'" id="wpbooklist_carousel_cover_image" src="'.$book->image.'" style="opacity: 1;"><span class="wpbooklist-carousel-title-span-class" id="wpbooklist-carousel-title-span-id-'.$key.'">'.$book->title.'</span></a>';
			                } else {
			                	$string = $string.'<img style="width:'.$this->coverwidth.'px; height:'.$this->coverheight.'px;" class="wpbooklist_cover_image_class wpbooklist-show-book-colorbox" data-bookid="'.$book->ID.'" data-booktable="'.$this->table.'" id="wpbooklist_carousel_cover_image" src="'.$book->image.'" style="opacity: 1;"><span class="wpbooklist-carousel-title-span-class" id="wpbooklist-carousel-title-span-id-'.$key.'">'.$book->title.'</span>';
			                }
			            }

		                if($this->action == 'colorbox'){
		                    $string = $string.'<img style="width:'.$this->coverwidth.'px; height:'.$this->coverheight.'px;" class="wpbooklist_cover_image_class wpbooklist-show-book-colorbox" data-bookid="'.$book->ID.'" data-booktable="'.$this->table.'" id="wpbooklist_carousel_cover_image" src="'.$book->image.'" style="opacity: 1;"><span style="width:'.((int)$this->coverwidth-10).'px;" class="wpbooklist-carousel-title-span-class" id="wpbooklist-carousel-title-span-id-'.$key.'">'.$book->title.'</span>';
		                }

		                if($this->action == '' || $this->action == null || $this->action == 'undefined'){
		                    $string = $string.'<img style="width:'.$this->coverwidth.'px; height:'.$this->coverheight.'px;" class="wpbooklist_cover_image_class wpbooklist-show-book-colorbox" data-bookid="'.$book->ID.'" data-booktable="'.$this->table.'" id="wpbooklist_carousel_cover_image" src="'.$book->image.'" style="opacity: 1;"><span class="wpbooklist-carousel-title-span-class" id="wpbooklist-carousel-title-span-id-'.$key.'">'.$book->title.'</span>';
		                }


		                $string = $string.'<span class="hidden_id_title">'.$book->ID.'</span>
		                    <p class="wpbooklist_saved_title_link wpbooklist-show-book-colorbox" data-bookid="'.$book->ID.'" data-booktable="'.$this->table.'" id="wpbooklist_carousel_saved_title_link">'.$book->title.'<span class="hidden_id_title">'.$book->ID.'</span>
		                    </p><div class="wpbooklist-library-frontend-purchase-div">';

		                    $sales_array = array($book->author_url,$book->price);
		                    if($this->display_options_actual->enablepurchase == 1 && ($book->price != null && $book->price != '') && $this->display_options_actual->hidefrontendbuyprice != 1){
			                    if(has_filter('wpbooklist_append_to_frontend_library_price_purchase')) {
									$string = $string.apply_filters('wpbooklist_append_to_frontend_library_price_purchase', $sales_array);
								}
							}

							if($this->display_options_actual->enablepurchase == 1 && $book->author_url != '' && $this->display_options_actual->hidefrontendbuyimg != 1){
			                    if(has_filter('wpbooklist_append_to_frontend_library_image_purchase')) {
									$string = $string.apply_filters('wpbooklist_append_to_frontend_library_image_purchase', $sales_array);
								}
							}



		                    $string = $string.'</div></div></div>';
		}

		$this->html_output = $string.'</div><div class="wpbooklist-carousel-nav-div" id="wpbooklist-carousel-nav-div-right"><img class="wpbooklist-carousel-nav-arrow" id="wpbooklist-carousel-nav-right" src="'.CAROUSEL_ROOT_IMG_URL.'rightbutton.png'.'" /></div></div>';

	}

}


endif;