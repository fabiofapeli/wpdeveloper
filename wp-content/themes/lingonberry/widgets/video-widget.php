<?php 

// Video widget for Lingonberry WordPress theme

class lingonberry_video_widget extends WP_Widget {

	function lingonberry_video_widget() {
		parent::WP_Widget(false, $name = 'Video widget', array('description' => __('Displays a video of your choosing.', 'lingonberry') ));	
	}
	
	function widget($args, $instance) {
	
		// Outputs the content of the widget
		extract($args); // Make before_widget, etc available.
		
		$widget_title = apply_filters('widget_title', $instance['widget_title']);
		$video_url = $instance['video_url'];
		
		echo $before_widget;
		
		
		if (!empty($widget_title)) {
		
			echo $before_title . $widget_title . $after_title;
			
		} ?>
			
			<?php if (strpos($video_url,'youtube') !== false) : ?>
			
				<?php $url = $video_url;
				preg_match(
				        '/[\\?\\&]v=([^\\?\\&]+)/',
				        $url,
				        $matches
				    );
				$id = $matches[1];
				 
				echo '<iframe width="640" height="400" src="http://www.youtube.com/embed/' . $id . '?autoplay=0" frameborder="0" allowfullscreen></iframe>' ?>
				
			<?php elseif (strpos($video_url,'vimeo') !== false) : ?>
			
				<?php $url = $video_url;
				preg_match(
				        '/(\d+)/',
				        $url,
				        $matches
				    );
				$id = $matches[1];
				 
				echo '<iframe width="640" height="300" src="http://player.vimeo.com/video/' . $id . '" frameborder="0" allowfullscreen></iframe>' ?>
				
			<?php elseif (strpos($video_url,'.mp4') !== false) : ?>
				
				<video controls>
				
					<source src="<?php echo $video_url ?>" type="video/mp4">
					<?php _e('Your browser does not support the video tag.','lingonberry'); ?>
					
				</video>
					
			<?php endif; ?>
							
		<?php echo $after_widget; 
	}
	
	
	function update($new_instance, $old_instance) {
	
		//update and save the widget
		return $new_instance;
		
	}
	
	function form($instance) {
	
		// Get the options into variables, escaping html characters on the way
		$widget_title = $instance['widget_title'];
		$video_url = $instance['video_url'];
		?>
		
		<p>
			<label for="<?php echo $this->get_field_id('widget_title'); ?>"><?php  _e('Title', 'lingonberry'); ?>:
			<input id="<?php echo $this->get_field_id('widget_title'); ?>" name="<?php echo $this->get_field_name('widget_title'); ?>" type="text" class="widefat" value="<?php echo $widget_title; ?>" /></label>
		</p>
		
				
		<p>
			<label for="<?php echo $this->get_field_id('video_url'); ?>"><?php  _e('Video URL', 'lingonberry'); ?>:
			<input id="<?php echo $this->get_field_id('video_url'); ?>" name="<?php echo $this->get_field_name('video_url'); ?>" type="text" class="widefat" value="<?php echo $video_url; ?>" /></label>
		</p>
						
		<?php
	}
}
register_widget('lingonberry_video_widget'); ?>