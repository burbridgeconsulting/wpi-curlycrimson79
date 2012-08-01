<?php
/*
Plugin Name: WP Isotope
Plugin URI: http://chrisburbridge.com/wp-isotope
Description: 
Version: 0.1
Author: Chris Burbridge
Author URI: http://chrisburbridge.com

Copyright 2012  Chris Burbridge  (email : christopherburbridge@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

	
/*
	1.0 Creating the class
	This instantiates our plugin class. It's a good idea to encapsulate your plugin inside a class to avoid naming conflicts. The end user may have many other plugins installed, and if your plugin has the same variable or function names as another, this will cause problems. By putting all your plugin functions inside a class, you avoid this problem entirely, and the only things you need to make sure are unique is the name of your plugin. A good plugin name would be "AcmeBugsBunnyQuotes" a bad plugin name would be "Quotes".
*/

$WPIsotope = new WPIsotope(); 

class WPIsotope {

	/*
		1.1 Defining class variables
		These are class variables. They are values that are used by more than one of the functions below. By defining them here, any function below can access them.
	*/
	
	var $errors       = false;
	var $updated_post = false;
	var $success      = false;
	

	//const WPISO_OPTIONS        = '';
	const ERROR_DUPLICATE_RULE = 1;
	const ERROR_MISSING_FIELDS = 2;
	const ERROR_MYSQL          = 3;
	
	/*
		1.3 The plugin constructor
		This is our plugin constructor. This is what runs first when our plugin gets loaded, so this is a great place to do any intialization that your plugin needs to do before it performs any of its other functions.
		
	*/
		
	function WPIsotope()	{
				
		add_action('admin_menu', array($this,'InsertAdminMenuLink'));
		//add_action('init', array($this,'RouteActions'),2);

		//register_activation_hook(__FILE__,array($this,'Activate'));
		//register_deactivation_hook(__FILE__,array($this,'Deactivate'));
		

		add_action( 'init', array( $this, 'RegisterShortcode' ) );
		add_action( 'init', array( $this, 'GetOptions' ) );

				
		/* Often times, our plugins need to attach new javascript files or css files to our page. WordPress provides a very clean mechanism for doing so that helps prevent us from needlessly including the same file multiple times (which would slow down your page load time). Scroll down to the StylesAndScripts function to see how to properly attach a JavaScript or CSS file. */
		add_action('wp_print_styles',  array($this,'Styles'));
		add_action('wp_print_scripts', array($this,'Scripts'));
		
		//add_action( 'admin_print_footer_scripts', array($this,'AdminPointers') );
		
		//add_filter('post_class', array($this,'PostClass'));
		add_filter('excerpt_length',   array($this,'ExcerptLength'));
		//add_filter('excerpt_more',     array($this,'ExcerptMore'));
		
				
		/* As you can see, filters also occasionally contain variables. This filter lets us add a little link underneath our Plugins name on the Plugins page that links the user to the Plugins settings page. */
		add_filter("plugin_action_links_WPIsotope/WPIsotope.php", array($this, 'InsertSettingsLink')); 
		
		//add_action('admin_notices', array( $this, 'my_admin_notice' ));

		// *** Should add this back when wanted ***
		add_action('wp_ajax_nopriv_isoGetPost', array( $this, 'GetPost' ) );
		add_action('wp_ajax_isoGetPost', array( $this, 'GetPost' ) );

		$this->errors = new WP_Error();
		
	} // end constructor
		
	function Activate() {
	
	}
		
	function Deactivate() {
		// Nothing to see here for now!
	}
	
	
	function InsertAdminMenuLink() {
		
		$page = add_submenu_page( 'options-general.php', __( 'WP Isotope' , 'WPIsotope' ), __( 'WP Isotope' , 'WPIsotope' ), 'edit_plugins', 'WPIsotope', array( $this , 'ConfigPageHtml' ) );
		
		add_action( 'admin_print_scripts-'.$page , array( $this, 'AdminScripts' ) );
		add_action( 'admin_print_styles-'.$page ,  array( $this, 'AdminStyles'  ) );
		
		add_action( 'admin_print_scripts-'.$page , array( $this, 'MetaLinks'    ) );
		add_action( 'admin_notices' , array( $this, 'MetaLinks'    ) );
		
		// WordPress requires us to "register" our plugins settings before we can let the user update them. This is a security feature to prevent unauthorized addition of settings. 
		add_action( 'admin_init' , array( $this, 'RegisterAdminSettings' ) );
				
	}
	
	function MetaLinks($page) {
		//Load the custom button API
		include_once 'screen-meta-links.php';
			
		//echo "B";	
			 
		add_screen_meta_links(
			'isotope-example',         //Link ID. Should be unique.  
			'Isotope Example',         //Link text.
			'http://example.com/',     //URL
			'settings_page_WPIsotope', //Where to show the link. 
			'<p>To embed Isotope on a post or page, use this shortcode <code>[wpisotope]</code></p>
			 <p>To embed Isotope in a theme template, use this code <code>&lt;?php echo do_shortcode( "[wpisotope]" ); ?&gt;</code></p>'
		); 
	
	}
	
	function AdminPointers() {
		$pointer_content = '<p>To embed Isotope on a post or page, use this shortcode <code>[wpisotope]</code><br /></p>';
		$pointer_content .= '<p>To embed Isotope in a theme template, use this code: <code>&lt;?php echo do_shortcode( "[wpisotope]" ); ?&gt;</code></p>';
		?>
		<script>
			//<![CDATA[
			jQuery(document).ready( function($) {
				$('#icon-options-general + h2').pointer({
					content: '<?php echo $pointer_content; ?>',
					position: 'left',
					close: function() {
						// Once the close button is hit
					}
				}).pointer('open');
			});
			//]]>
		</script>
		<?php
	}
	
	function RegisterAdminSettings() {
		register_setting( 'WPIsotopeSettings', 'WPIsotopeSettings', array( $this, 'SaveSettings' ) );
	}
	
	function SaveSettings($input) {
					
		$valid                    = array();
		
		$valid[ 'type'   ]        = ( isset( $input[ 'type' ] ) )          ? $input[ 'type' ]          : '';
		$valid[ 'layout' ]        = ( isset( $input[ 'layout' ] ) )        ? $input[ 'layout' ]        : 'masonry';
		$valid[ 'ppp'    ]        = ( isset( $input[ 'ppp' ] ) )           ? $input[ 'ppp' ]           : '';
		$valid[ 'rand'  ]         = ( isset( $input[ 'rand' ] ) )          ? $input[ 'rand' ]          : '';
		
		$valid[ 'filter-type' ]   = ( isset( $input[ 'filter-type' ] ) )   ? $input[ 'filter-type' ]   : '';
		$valid[ 'filter-tax' ]    = ( isset( $input[ 'filter-tax' ] ) )    ? $input[ 'filter-tax' ]    : '';
		$valid[ 'filter-cat' ]    = ( isset( $input[ 'filter-cat' ] ) )    ? $input[ 'filter-cat' ]    : '';
		$valid[ 'filter-format' ] = ( isset( $input[ 'filter-format' ] ) ) ? $input[ 'filter-format' ] : '';
		
		$valid[ 'sort-type' ]     = ( isset( $input[ 'sort-type' ] ) )     ? $input[ 'sort-type' ]     : '';
		$valid[ 'sort-tax' ]      = ( isset( $input[ 'sort-tax' ] ) )      ? $input[ 'sort-tax' ]      : '';
		$valid[ 'sort-cat' ]      = ( isset( $input[ 'sort-cat' ] ) )      ? $input[ 'sort-cat' ]      : '';
		$valid[ 'sort-format' ]   = ( isset( $input[ 'sort-format' ] ) )   ? $input[ 'sort-format' ]   : '';
				
		return $valid;
	}
	

		
	function ConfigPageHtml() {
		$content = '';
		ob_start(); // This function begins output buffering, this means that any echo or output that is generated after this function will be captured by php instead of being sent directly to the user.
			require_once('html/config.php'); // This function includes my configuration page html. Open the file html/config.php to see how to format a configuration page to save options.
			$content = ob_get_contents(); // This function takes all the html retreived from html/config.php and stores it in the variable $content
		ob_end_clean(); // This function ends the output buffering
		
		echo $content; // Now I simply echo the content out to the user
	
	}
	

	function AdminScripts() {
		wp_enqueue_script( 'WPIsotope_Admin', plugin_dir_url( __FILE__ ) . '/js/admin.js', array('jquery') );
		wp_enqueue_script( 'wp-pointer' );
	}
	
	function AdminStyles() {
		wp_enqueue_style( 'WPIsotope_Admin', plugin_dir_url( __FILE__ ) . '/css/admin.css' );
		wp_enqueue_style( 'wp-pointer' );
	}


	function InsertSettingsLink($links) {
		$settings_link = '<a href="options-general.php?page=WPIsotope">'.__('Settings','WPIsotope').'</a>'; 
		array_unshift( $links, $settings_link ); 
		return $links; 
	}
	
	
	function Styles() {		
		wp_enqueue_style( 'WPIsotope_css', plugin_dir_url( __FILE__ ) . 'css/isotope.css' );
		// wp_enqueue_style( 'WPIsotope_css_custom', plugin_dir_url( __FILE__ ) . 'css/isotope-custom.css' );
	}
	
	function Scripts() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'WPIsotope_js', plugin_dir_url( __FILE__ ) . 'js/jquery.isotope.min.js', array('jquery'), '1.4.110906' );
	}
	
	function GetPost( $id ) {
		global $wp_rewrite;
		
		$id       = $_POST['id'];
		$post     = get_post( $id );
		
		// Again, the Images thing here is very Lakshmi-specific.
		$images = $this->GetGalleryImages( $id, 'coalition-full' );
		
		$content  = wpautop( $images . $post->post_content );
		$content  = do_shortcode( $content );
		$response = json_encode( array( 'success' => true, 'content' => $content ) );

// $handle = fopen("/Users/chrisburbridge/Sites/awakemedia/wp-content/plugins/wp-isotope/log.txt", "w");
// fwrite($handle, $response);
// fclose($handle);

	    // response output
	    header( "Content-Type: application/json" );
	    echo $response;
	 
	    // IMPORTANT: don't forget to "exit"
	    exit;
	}
	
	function GetOptions() {
		return get_option( 'WPIsotopeSettings' );
	}
	
	function PostFormats() {
		if ( current_theme_supports( 'post-formats' ) ) {
			$post_formats = get_theme_support( 'post-formats' );
		
			if ( is_array( $post_formats[0] ) ) {
				return $post_formats[0];
			}
			else {
				return;
			}
		} else {
			return;
		}
	}
		
	function PostClass() {
		global $post;
		
		foreach ( ( get_the_category( $post->ID ) ) as $category ) {
			$classes[] = $category->category_nicename;
		}
		
		foreach ( ( get_taxonomies( array( '_builtin' => false ) ) ) as $tax ) {
			foreach ( ( get_the_terms( $post->ID, $tax ) ) as $term ) {
				$classes[] = $term->slug;
			}
		}
			
		return $classes;
	}
	
	function ReadMore() {
		return "<a class='iso-read-more' href='" . get_permalink() . "'>Permalink</a>";
	}
		
	function ExcerptLength($length) {
		return 20;
	}	
                                    
	function GetBuyButton($id, $position) {
		return $buy_button;
	}

	function ExcerptMore($charlength) {
		$excerpt = get_the_excerpt();      
		
		$charlength++;
		if( strlen( $excerpt ) > $charlength ) {
			$subex   = substr( $excerpt, 0, $charlength-5 );
			$exwords = explode( " ",$subex );
			$excut   = -( strlen( $exwords[ count( $exwords ) - 1 ] ) );
			if( $excut < 0 ) {
				return substr( $subex, 0, $excut ) . "</div>";
			} else {    
				return $subex . "</div>";
			}
		} else {
			return $excerpt . "</div>";
		}
	}

	function Pagination($range = 4) {
	
		global $wp_query;
		
		if ( !isset($max_page) ) 
			$max_page = $wp_query->max_num_pages;
			
		$paged       = get_query_var( 'paged' );
		
		if ( $max_page > 1 ) {
		
			if ( $paged == 0 || $paged == '' ) 
				$paged = 1;
				
				for ( $i = $max_page; $i >= $max_page; $i-- ) {
					if ( $paged < $max_page ) {
						//$pagination .= "Page = " . $paged;
						$pagination .= "<div class='next-posts'><a href='" . get_pagenum_link($paged+1) ."'>next</a></div>";
					}
				}
				if ( $paged > 1 )
					$pagination .=  "<div class='prev-posts'><a href='" . get_pagenum_link($paged-1) ."'>previous</a></div>";
	
		}
		
		return $pagination;
		
	}
	
	function RegisterShortcode() {
		add_shortcode( 'wpisotope', array( $this, 'RunShortcode' ) );
	}
	
	function GetGalleryImages( $post_id, $size ) {
		$images =& get_children( 'post_type=attachment&post_mime_type=image&post_parent=' . $post_id );
		$counter=0;
		$result = "<div class='images-wrapper-{$size} images-wrapper'>";
		$result .= '<div class="images">';
		foreach( (array) $images as $attachment_id => $attachment )
		{
		   $counter++;
			 $image = wp_get_attachment_image_src( $attachment_id, $size );
			 $result .= "<img src='{$image[0]}' />";
		}
		$result .= '</div>';
		$result .= '<div class="pager"></div>';
		$result .= '</div>';
		return $result;
	}

	function RunShortcode( $attr ) {
		
		global $wp_query;

		$page_id       = $wp_query->get_queried_object_id();
		$options       = $this->GetOptions();
		$output        = '';
		$types         = ( isset( $attr['type'] ) )      ? $attr['type']      : ( ( $options['type'] )        ? $options['type'] : 'post' );
		//$types         = ( $types == 'all' )             ? array_values(get_post_types( array( 'public' => true ), 'names', 'and' )) : $types;
		$types         = ( !is_array($types) && $types != 'any' ) ? array($types)      : $types;	
		$ppp           = ( isset( $options[ 'ppp' ] ) )  ? $options[ 'ppp' ]  : get_option('posts_per_page');
		$rand          = ( isset( $options[ 'rand' ] ) ) ? $options[ 'rand' ] : 'false';
		
		$taxonomies    = get_taxonomies( array( '_builtin' => false ) );
		$post_formats  = $this->PostFormats();
		
		$filter_types  = $options[ 'filter-type' ];
		$filter_cats   = $options[ 'filter-cat'  ];
		$filter_taxs   = ( isset( $attr['tax'] ) )       ? isset( $attr['tax'] )      : $options[ 'filter-tax'  ];
		$filter_taxs   = ( !is_array($filter_taxs) )     ? explode(',', $attr['tax']) : $filter_taxs;
		$filter_format = $options[ 'filter-format' ];
		
		$taxonomiez    = get_terms( $filter_taxs );
									
		$sort_types    = $options[ 'sort-type'   ];
		$sort_taxs     = $options[ 'sort-tax'    ];
		$sort_cats     = $options[ 'sort-cat'    ];
		$sort_format   = $options[ 'sort-format' ];
				
		// setup isotope query
		$paged = ( get_query_var('page') ) ? get_query_var('page') : 1;
					
		query_posts(
			array(
				'post_type'      => $types,
				'orderby'        => $rand,
				'posts_per_page' => $ppp,
				'paged'          => get_query_var( 'page' ),
				'post__not_in'   => array($page_id)
			)
		);
		
		if ( have_posts() ) : 

			$types = ( $types == 'any' ) ? array_values(get_post_types( array( 'public' => true ), 'names', 'and' )) : 		
			$post_type = $types[0];
			
			// *** Here, the following should REALLY be an option: ***
			$filter_prepend_text = 'Browse by Category: ';
		
			// Setup all the filters
			if ($attr["show_filters"] != 'false') {
				if ( $filter_types != 'false' || 
				   ( isset($filter_cats) && $filter_cats != 'false' && $filter_cats != false ) || 
				   ( isset($filter_taxs) && $filter_taxs != 'false' && $filter_taxs != false || 
				   ( isset($filter_format) && $filter_format != 'false' && $filter_format != false ) ) ) {
						$output .= '<ul class="filters cf show-all"><li class="filter-prepend">' . $filter_prepend_text . '</li>' .
							' <li><a href="#" data-filter="*">Show All</a></li>';

					if ( $filter_types != 'false' ) {			
						foreach ( $types as $type ) {
							$output .= '<li><a href="#" data-filter=".type-' . $type . '">' . $type . '</a></li>';
						}
					}			
					if ( isset($filter_cats) && $filter_cats != 'false' && $filter_cats != false ) {
						foreach ( ( get_categories() ) as $category ) {
							if ($category->name != 'Uncategorized') {
								$output .= '<li><a href="#" data-filter=".category-' . $category->slug . '">' . 
									$category->name . '</a></li>';
							}
						}
					}
					// *** Filter taxonomies has a bug ***
					if ( isset($filter_taxs) && $filter_taxs != 'false' && $filter_taxs != false ) {
						foreach ( $taxonomiez as $tax ) {
							$output .= '<li><a href="#" data-filter=".taxonomy-' . $tax->slug . '">' . $tax->slug . '</a></li>';
						}
					}

					// **** ONLY for Lakshmi! ****
					$output .= '<li class="search">' . get_search_form(false) . '<span class="icon"></span></li>';
					
					$output .= '</ul>';
				}
			}  
			
			// Setup the sort options
			if ( $sort_types  != 'false' || $sort_cats   != 'false' ) {
				$output    .= 'Sort:
					<ul class="sort cf">';
				if ( $sort_types  != 'false' ) {
					$output .= '<li><a href="#post_type">Post Type</a></li>';
				}
				if ( $sort_cats   != 'false' ) {
					$output .= '<li><a href="#category">Category</a></li>';
				}
				$output    .= '</ul>';
			}
			
			
			$output    .= "<section id='iso-container'>";
		
			while ( have_posts() ) : the_post();
				
				$content = apply_filters( 'the_content', get_the_content() );
				$content = str_replace(']]>', ']]&gt;', $content);
				$cat     = get_the_category();
				$perma   = get_permalink();
				$id      = get_the_ID();
				$author  = get_the_author_meta('ID');
				$avatar  = get_avatar( $author, '32' );
				$author  = get_the_author_meta( 'display_name' );
				$link    = get_author_posts_url( $author );
				$comment = get_comments_number();

			
				$klasses =  get_post_class( 'item' );
								
				$output .= "<div id='post-" . $id . "' data-id='" . $id . "' class='";
				foreach ( $klasses as $klass ) { 
					$output .= $klass . ' ';
				};
				
				if ( $filter_taxs ) {
					foreach ( $taxonomies as $tax ) {
						$terms   = get_the_terms( $post->ID, $tax );
						
						if ( $terms ) {						
							foreach ( $terms as $term ) {
								$output .= 'taxonomy-' . $term->slug . ' ';
							}
						}
					}
				}
				
				$output .= "' data-post_type='" . get_post_type()    . "' ";
				$output .= "' data-category='" . $cat[0]->name  . "' ";				
				$output .= ">"; // end open post div

				// *** More Lakshmi stuff ***
				if ($post_type == 'coalition' or $post_type == 'portfolio') {
					global $post;
					$output .= $this->GetGalleryImages($post->ID, 'coalition-thumb');
				} else {
					if ( current_theme_supports( 'post-thumbnails' ) ) {       
						// *** Should probably change back ***
						// $output .= "<div class='iso-thumb'>" . get_the_post_thumbnail( $post->ID, 'thumbnail' ) . "</div>";
						$output .= "<div class='iso-thumb'>" . get_the_post_thumbnail( $post->ID, 'medium' ) . "</div>";
					}                         
				}
				
				/**
				 * @param taxonomy_labels 
				 * Provide a valid taxonomy for this object type.
				 */
				
				$output .= "<div class='title-wrap'>";
				
				$output .= "<h2 class='title'>" . get_the_title() . "</h2>";

				// *** For Lakshmi only! ***
				if ($post_type == 'portfolio') {
					if (get_field('pfo_client')) {
						$output .= "<h2 class='client'>Client: " . get_field('pfo_client') . "</h2>";
					}
					if (get_field('pfo_author')->post_title) {
						$output .= "<h2 class='author'>Created By: " . get_field('pfo_author')->post_title . "</h2>";
					}
				} else if ($post_type == 'coalition') {
					
				}

				if ($attr['taxonomy_labels']) {
					$tax_type = $attr['taxonomy_labels'];
					$taxonomy_objects = wp_get_post_terms( get_the_id(), $tax_type );
					$output .= "<p class='categories'>";
					// require_once('do_dump.php');
					// do_dump($taxonomy_objects);
					foreach ($taxonomy_objects as $to) {
						$output .= "{$to->name} ";
					}
					$output .= "</p>";
				}

				$output .= "</div> <!-- title wrap -->";
				                                                               
				// *** Here we will definitely want to change back ... ***
				// $output .= "<div class='excerpt'>" . $this->ExcerptMore(90); 
				
				$output .= "<div class='content'><br /><a href='" . $perma . "'>read more</a></div>";

				$output .= "<span class='iso-close'><img src='" . plugin_dir_url( __FILE__ ) . "i/close.png'/></span>";
				
				// *** Footer stuff that might be there or not ***
				// $output .= "<div class='iso-meta iso-footer'>";
				// $output .= $avatar;
				// $output .= "<span class='iso-author'>From <a href='" . $link . "' >" . $author . "</a></span> ";
				// $output .= "<div class='iso-comments'>" . $comment . " comments </div>";
				// $output .= "</div>";
				
				$output .= "</div>";	// end post div	
			endwhile; 
			
			$output    .= "<div class='navigation cf'>";
			$output    .= $this->Pagination();
			$output    .= "</div>";
			
			$output    .= "</section>";
			$output    .= "
			<script>
				(function($) {
					$(function() {
										
						var container = $('#iso-container');

						container.isotope({
							// options
							itemSelector   : '.item',
							layoutMode     : 'masonry',
							masonry : {
								columnWidth : 275
							},
							getSortData    : {
								post_type   : function ( elem ) {
									return elem.data('post_type');
								},
								category    : function ( elem ) {
									return elem.data('category');
								},
								format      : function ( elem ) {
									return elem.data('format');
								},
								symbol : function ( elem ) {
									return elem.find('.symbol').text();
								}
							}							
						});
						
						
						$('.filters a').click(function(){
							var selector = $(this).data('filter');
							container.isotope({ filter: selector });
							return false;
						});
						
						$('.sort a').click(function(event){
							// event.stopPropagation()
							var sortName = $(this).attr('href').slice(1);
							container.isotope({ sortBy : sortName });
							return false;
						});
						
						$('.isotope-item').on( 'click', function(event) {
							// event.stopPropagation()
							
							var that    = $(this);
							var id      = that.data('id');
							var content = that.find('.content');
							
							$('.isotope-item').not(that).removeClass('big');
							
							if ( !that.hasClass('big') && that.data( 'click' ) !== 'off' ) {
								
								that.data( 'click', 'off' ).append('<div id=loading style=\"background: url(" . plugin_dir_url( __FILE__ ) . "i/loading.gif) no-repeat 50% 50% transparent;\" />');
								
								$.post( '/wp-admin/admin-ajax.php', { action: 'isoGetPost', id: id }, function(data) {
									$('#loading').remove();
									content.html(data.content);
																		
									that.addClass('big');
									container.isotope('reLayout');
									that.data( 'click', 'on' );
									
									// Add cycling code for large images (for Lakshmi) here
									// This is pretty much the same cycling code as for the small boxes, but
									// that code can go in the theme directory, as part of the theme behavior
									that.find('.images-wrapper-coalition-full').each(function() {
											var slideshow = jQuery(this).find('.images')
											slideshow.cycle({
												autostop: 1,
												// autostopCount: 1
												pager: jQuery(this).find('.pager'),
												// allowPagerClickBubble: false
											})
									})
									
								});
								
							} else {
								that.removeClass('big');
								container.isotope('reLayout');
								that.data( 'click', 'on' );
							}
							
										      });  
						
					});
				})(jQuery);
			</script>";
			
		endif; 
		
		return $output;
	}
	
}