<?php 
/***** 
	This file generates the Plugin Settings page. 
*****/
?>

<div class="wrap" id="isotope-wrap"> 

	<form method="post" action="options.php"> 
	
		<?php
			settings_fields( 'WPIsotopeSettings' ); 
			$options = get_option( 'WPIsotopeSettings' );
		
			$types   = $options[ 'type'   ];
			$taxes   = $options[ 'tax'    ];
			$cats    = $options[ 'cat'    ];
			$format  = $options[ 'format' ];
			$layout  = $options[ 'layout' ];
					
		?>
		
		<pre>
		<?php print_r($options); ?>
		</pre>
		
		<!-- Here we have the title and instructions for the form. Notice that it has been wrapped in a gettext function for internationalization -->
		<?php screen_icon(); ?>
		<h2>WP Isotope Options</h2>
		
		<h3>Which content types, filters and sort options would you like to include?</h3>
		<div class="inside">
			<table class="form-table wp-list-table widefat plugins">
				<tbody>
					<tr>
						<th class="plugin-title" scope="row">
							<label for="post_types">Content to include</label>
						</th>
						<td>
							<?php $post_types = get_post_types( array( 'public' => true ), 'names', 'and' ); 
									$i = 0;
								  foreach ( $post_types  as $post_type ) { ?>
								    <label for="WPIsotopeSettings[type][<?php echo $i; ?>]">
								    	<input 
								    		type  ="checkbox" 
								    		value ="<?php echo $post_type; ?>" 
								    		id    ="WPIsotopeSettings[type][<?php echo $i; ?>]" 
								    		name  ="WPIsotopeSettings[type][<?php echo $i; ?>]" 
								    		<?php echo checked( $types[$i], $post_type, false ) ?> 
								    		/>
								    		
								    	&nbsp;<?php echo $post_type ?>
								    </label><br />
							<?php $i++; } ?>
						</td>
						<td>Would you like to include any of these post types?</td>
					</tr>
					<tr>
						<th scope="row" colspan="3"><h4>Filters</h4></th>
					</tr>
					<tr>
						<th class="plugin-title" scope="row">
							<label for="taxonomies">Taxonomies</label>
						</th>
						<td>
							<?php $taxonomies = get_taxonomies( array( '_builtin' => false ) );
									$i = 0; 
								  foreach ( $taxonomies  as $taxonomy ) { ?>
								    <label for="WPIsotopeSettings[tax][<?php echo $i; ?>]">
								    	<input 
								    		type  ="checkbox" 
								    		value ="<?php echo $taxonomy; ?>"
								    		id    ="WPIsotopeSettings[tax][<?php echo $i; ?>]" 
								    		name  ="WPIsotopeSettings[tax][<?php echo $i; ?>]" 
								    		<?php echo checked( $taxes[$i], $taxonomy, false ) ?> 
								    		/>
								    		
								    	&nbsp;<?php echo $taxonomy ?>
								    </label><br />
							<?php $i++; } ?>
						</td>
						<td>Would you like to Filter or Sort by taxonomy?</td>
					</tr>
					<tr>
						<th class="plugin-title" scope="row">
							<label for="post_types">Categories</label>
						</th>
						<td>
							<?php $categories = get_categories();
									$i = 0;
								  foreach ( $categories  as $category ) { ?>
								    <label for="WPIsotopeSettings[cat][<?php echo $i; ?>]">
								    	<input 
								    		type  ="checkbox" 
								    		value ="<?php echo $category->name; ?>"
								    		id    ="WPIsotopeSettings[cat][<?php echo $i; ?>]" 
								    		name  ="WPIsotopeSettings[cat][<?php echo $i; ?>]" 
								    		<?php echo checked( $cats[$i], $category->name, false ) ?> 
								    		/>
								    		
								    	&nbsp;<?php echo $category->name; ?>
								    </label><br />
							<?php $i++; } ?>
						</td>
						<td>Would you like to Filter or Sort by category?</td>
					</tr>
					<?php 
						$post_formats = $this->PostFormats();
						if ( is_array($post_formats) ) {	
					?>
					<tr>
						<th class="plugin-title" scope="row">
							<label for="post_types">Post Formats</label>
						</th>
						<td>
							<?php 
								$i = 0;
									foreach ( $post_formats  as $post_format ) { ?>
								    <label for="WPIsotopeSettings[format][<?php echo $i; ?>]">
								    	<input 
								    		type  ="checkbox"
								    		value ="<?php echo $post_format; ?>" 
								    		id    ="WPIsotopeSettings[format][<?php echo $i; ?>]" 
								    		name  ="WPIsotopeSettings[format][<?php echo $i; ?>]" 
								    		<?php echo checked( $format[$i], $post_format, false ) ?> 
								    		/>
								    		
								    	&nbsp;<?php echo $post_format; ?>
								    </label><br />
							<?php $i++; } ?>
						</td>
						<td>Would you like to Filter or Sort by post format?</td>
					</tr>
					<?php } ?>	
					<!--
					<tr>
						<th scope="row"><label for="WPIsotopeSettings[layout]">Layout Modes</label></th>
						<td>
							<select name="WPIsotopeSettings[layout]" id="layout">
								<option value="cellsbycolumn"     <?php //selected( $layout, 'cellsbycolumn'      ); ?>>cellsByColumn</option>
								<option value="cellsbyrow"        <?php //selected( $layout, 'cellsbyrow'         ); ?>>cellsByRow</option>
								<option value="fitcolumns"        <?php //selected( $layout, 'fitcolumns'         ); ?>>fitColumns</option>
								<option value="fitrows"           <?php //selected( $layout, 'fitrows'            ); ?>>fitRows</option>
								<option value="masonry"           <?php //selected( $layout, 'masonry'            ); ?>>masonry</option>
								<option value="masonryhorizontal" <?php //selected( $layout, 'masonryhorizontal'  ); ?>>masonryHorizontal</option>
								<option value="straightacross"    <?php //selected( $layout, 'straightacross'     ); ?>>straightAcross</option>
								<option value="straightdown"      <?php //selected( $layout, 'straightdown'       ); ?>>straightDown</option>
							</select>
						</td>
						<td>Which Layout Mode would you like to use?</td>
					</tr>
					-->
				</tbody>
			</table>		
		</div> <!-- end .inside -->
		
		<!-- This is our submit button -->
		<p class="submit">
			<input type="submit" class="button-primary" name="Submit" value="Save Changes" />
		</p>
		
	</form>
	
	

</div>

