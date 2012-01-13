<?php 
/***** 
	This file generates the Plugin Settings page. 
*****/
?>                      

<?php
/************** DEFAULTS *****************/
define(DEFAULT_PPP, 20); 
define(DEFAULT_RAND, 'menu_order');
define(DEFAULT_FILTER_TYPES, 'true');
define(DEFAULT_FILTER_CATS, 'true');
define(DEFAULT_SORT_TYPES, 'true');
define(DEFAULT_SORT_CATS, 'true');
?>

<div class="wrap" id="isotope-wrap"> 

	<form method="post" action="options.php"> 
	
		<?php         
			// delete_option('WPIsotopeSettings');		
		
			settings_fields( 'WPIsotopeSettings' ); 
			$options       = get_option( 'WPIsotopeSettings' );
		
			$types         = $options['type'];
			$layout        = $options['layout'];
			$order         = $options['order'];
			if ($options['ppp']) { $ppp = $options['ppp']; } else { $ppp = DEFAULT_PPP; };
			if ($options['rand']) { $rand = $options['rand']; } else { $rand = DEFAULT_RAND; };
			
			if ($options['filter-type']) { $filter_types = $options['filter-type']; } else { $filter_types = DEFAULT_FILTER_TYPES; };			
			if ($options['filter-cat']) { $filter_cats = $options['filter-cat']; } else { $filter_cats = DEFAULT_FILTER_CATS; };			
			$filter_taxs   = $options['filter-tax'];
			
			if ($options['sort-type']) { $sort_types = $options['sort-type']; } else { $sort_types = DEFAULT_SORT_TYPES; };			
			if ($options['sort-cats']) { $sort_cats = $options['sort-cats']; } else { $sort_cats = DEFAULT_SORT_CATS; };			
		?>
		
		<!-- Here we have the title and instructions for the form. Notice that it has been wrapped in a gettext function for internationalization -->
		<?php screen_icon(); ?>
		<h2>WP Isotope Options</h2>
		
		<h3>Basic Options</h3>
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
					</tr>
					<tr>
						<th class="plugin-title" scope="row">
							<label for="post_types">Posts Per Page</label>
						</th>
						<td>
							<label for="WPIsotopeSettings[ppp]">
								<input id="WPIsotopeSettings[ppp]" name="WPIsotopeSettings[ppp]" type="number" value="<?php echo $ppp; ?>" />
							</label>
						</td>
						<td></td>
					</tr>
					<tr>
						<th class="plugin-title" scope="row">
							<label for="post_types">Shuffle posts?</label>
						</th>
						<td>
							<label for="WPIsotopeSettings[rand][rand]">
								<input id="WPIsotopeSettings[rand][rand]" name="WPIsotopeSettings[rand]" type="radio" value="rand" <?php checked( $rand, 'rand' ); ?> />
								Yes
							</label><br />
							<label for="WPIsotopeSettings[rand][menu_order]">
								<input id="WPIsotopeSettings[rand][menu_order]" name="WPIsotopeSettings[rand]" type="radio" value="menu_order" <?php checked( $rand, 'menu_order' ); ?> />
								No
							</label>								    		
						</td>
						<td></td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<h3>Which filters would you like to include?</h3>
		<div class="inside">
			<table class="form-table wp-list-table widefat plugins">
				<tbody>
					<tr>
						<th class="plugin-title" scope="row">
							<label for="post_types">Filter by content type?</label>
						</th>
						<td>
							<label for="WPIsotopeSettings[filter-type][yes]">
								<input id="WPIsotopeSettings[filter-type][yes]" name="WPIsotopeSettings[filter-type]" type="radio" value="true" <?php checked( $filter_types, 'true' ); ?> />
								Yes
							</label><br />
							<label for="WPIsotopeSettings[filter-type][no]">
								<input id="WPIsotopeSettings[filter-type][no]" name="WPIsotopeSettings[filter-type]" type="radio" value="false" <?php checked( $filter_types, 'false' ); ?> />
								No
							</label>								    		
						</td>
						<td></td>
					</tr>
					<tr>
						<th class="plugin-title" scope="row">
							<label for="taxonomies">Taxonomies</label>
						</th>
						<td>
							<?php $taxonomies = get_taxonomies( array( '_builtin' => false ) );
									$i = 0; 
								  foreach ( $taxonomies  as $taxonomy ) { ?>
								    <label for="WPIsotopeSettings[filter-tax][<?php echo $i; ?>]">
								    	<input 
								    		type  ="checkbox" 
								    		value ="<?php echo $taxonomy; ?>"
								    		id    ="WPIsotopeSettings[filter-tax][<?php echo $i; ?>]" 
								    		name  ="WPIsotopeSettings[filter-tax][<?php echo $i; ?>]" 
								    		<?php echo checked( $filter_taxs[$i], $taxonomy, false ) ?> 
								    		/>
								    		
								    	&nbsp;<?php echo $taxonomy ?>
								    </label><br />
							<?php $i++; } ?>
						</td>
						<td></td>
					</tr>
					<tr>
						<th class="plugin-title" scope="row">
							<label for="post_types">Categories</label>
						</th>
						<td>
							<label for="WPIsotopeSettings[filter-cat][yes]">
								<input id="WPIsotopeSettings[filter-cat][yes]" name="WPIsotopeSettings[filter-cat]" type="radio" value="true" <?php checked( $filter_cats, 'true' ); ?> />
								Yes
							</label><br />
							<label for="WPIsotopeSettings[filter-cat][no]">
								<input id="WPIsotopeSettings[filter-cat][no]" name="WPIsotopeSettings[filter-cat]" type="radio" value="false" <?php checked( $filter_cats, 'false' ); ?> />
								No
							</label>
						</td>
						<td></td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<h3>By what would you like to sort</h3>
		<div class="inside">
			<table class="form-table wp-list-table widefat plugins">
				<tbody>	
					<tr>
						<th class="plugin-title" scope="row">
							<label for="post_types">Content type</label>
						</th>
						<td>
							<label for="WPIsotopeSettings[sort-type][yes]">
								<input id="WPIsotopeSettings[sort-type][yes]" name="WPIsotopeSettings[sort-type]" type="radio" value="true" <?php checked( $sort_types, 'true' ); ?> />
								Yes
							</label><br />
							<label for="WPIsotopeSettings[sort-type][no]">
								<input id="WPIsotopeSettings[sort-type][no]" name="WPIsotopeSettings[sort-type]" type="radio" value="false" <?php checked( $sort_types, 'false' ); ?> />
								No
							</label>								    		
						</td>
						<td></td>
					</tr>					
					<!--<tr>
						<th class="plugin-title" scope="row">
							<label for="taxonomies">Taxonomies</label>
						</th>
						<td>
							<?php //$taxonomies = get_taxonomies( array( '_builtin' => false ) );
							//		$i = 0; 
							//	  foreach ( $taxonomies  as $taxonomy ) { ?>
								    <label for="WPIsotopeSettings[sort-tax][<?php //echo $i; ?>]">
								    	<input 
								    		type  ="checkbox" 
								    		value ="<?php// echo $taxonomy; ?>"
								    		id    ="WPIsotopeSettings[sort-tax][<?php// echo $i; ?>]" 
								    		name  ="WPIsotopeSettings[sort-tax][<?php// echo $i; ?>]" 
								    		<?php// echo checked( $sort_taxs[$i], $taxonomy ) ?> 
								    		/>
								    		
								    	&nbsp;<?php// echo $taxonomy ?>
								    </label><br />
							<?php //$i++; } ?>
						</td>
						<td></td>
					</tr>-->
					<tr>
						<th class="plugin-title" scope="row">
							<label for="post_types">Categories</label>
						</th>
						<td>
							<label for="WPIsotopeSettings[sort-cat][yes]">
								<input id="WPIsotopeSettings[sort-cat][yes]" name="WPIsotopeSettings[sort-cat]" type="radio" value="true" <?php checked( $sort_cats, 'true' ); ?> />
								Yes
							</label><br />
							<label for="WPIsotopeSettings[sort-cat][no]">
								<input id="WPIsotopeSettings[sort-cat][no]" name="WPIsotopeSettings[sort-cat]" type="radio" value="false" <?php checked( $sort_cats, 'false' ); ?> />
								No
							</label>
						</td>
						<td></td>
					</tr>
								
					<?php 
					//	$post_formats = $this->PostFormats();
					//	if ( is_array($post_formats) ) {	
					?><!--
					<tr>
						<th class="plugin-title" scope="row">
							<label for="post_types">Post Formats</label>
						</th>
						<td>
							<label for="WPIsotopeSettings[sort-format][yes]">
								<input id="WPIsotopeSettings[sort-format][yes]" name="WPIsotopeSettings[sort-format]" type="radio" value="true" <?php// checked( $sort_format, 'true' ); ?> />
								Yes
							</label><br />
							<label for="WPIsotopeSettings[sort-format][no]">
								<input id="WPIsotopeSettings[sort-format][no]" name="WPIsotopeSettings[sort-format]" type="radio" value="false" <?php// checked( $sort_format, 'false' ); ?> />
								No
							</label>
						</td>
						<td></td>
					</tr>-->
					<?php// } ?>	
					
				</tbody>
			</table>		
		</div> <!-- end .inside -->
		
		<!-- This is our submit button -->
		<p class="submit">
			<input type="submit" class="button-primary" name="Submit" value="Save Changes" />
		</p>
		
	</form>
	
	

</div>




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
</tr>
-->