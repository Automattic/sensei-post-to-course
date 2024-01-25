<?php

/**
 * Provide an admin area view for the plugin.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://automattic.com
 * @since      1.0.0
 *
 * @package    Sensei_Post_To_Course
 * @subpackage Sensei_Post_To_Course/admin/partials
 */
?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<form action="options.php" method="post">
		<?php
			settings_fields( $this->plugin_name );
			settings_errors( 'sptc_settings' );
		?>

		<p class="sptc-description">
			<?php esc_html_e( 'Use this tool to create a course from posts or from a custom post type.', 'sensei-post-to-course' ) ?>
		</p>

		<div class="sptc-setting">
			<div class="sptc-setting-label">
				<label for="course">
					<?php esc_html_e( 'Course Name', 'sensei-post-to-course' ); ?>:
				</label>
			</div>

			<div class="sptc-setting-value">
				<input id="sptc-course" type="text" name="sptc_settings[course_name]" class="regular-text text-input">
				<p class="sptc-setting-description">
					<?php esc_html_e( 'A new course with this name will be created.', 'sensei-post-to-course' ); ?>
				</p>
			</div>
		</div>

		<div class="sptc-setting">
			<div class="sptc-setting-label">
				<label for="sptc-post-type">
					<?php esc_html_e( 'Post Type', 'sensei-post-to-course' ); ?>:
				</label>
			</div>

			<div class="sptc-setting-value">
				<?php
					$post_types = get_post_types(
						[
							'public'   => true,
							'_builtin' => false,
						],
						'objects'
					);

					echo '<select id="sptc-post-type" name="sptc_settings[post_type]">';
					echo '<option value="post">' . __( 'Posts', 'sensei-post-to-course' ) . '</option>';

					foreach ( $post_types as $post_type ) {
						// Don't include Sensei post types.
						if ( in_array( $post_type->name, [ 'course', 'lesson', 'quiz', 'sensei_message', 'sensei_email' ] ) ) {
							continue;
						}

						echo '<option value="' . esc_attr( $post_type->name ) . '">' . esc_html( $post_type->label ) . '</option>';
					}

					echo '</select>';
				?>
				<p class="sptc-setting-description">
					<?php esc_html_e( 'Courses will be created from this post type.', 'sensei-post-to-course' ); ?>
				</p>
			</div>
		</div>

		<div class="sptc-setting sptc-category">
			<div class="sptc-setting-label">
				<label for="category">
					<?php esc_html_e( 'Category', 'sensei-post-to-course' ); ?>:
				</label>
			</div>

			<div class="sptc-setting-value">
				<?php
					wp_dropdown_categories( array(
						'selected' => -1,
						'name' => 'sptc_settings[category_id]',
						'show_option_none' => __( 'None', 'sensei-post-to-course' ),
					) );
				?>
				<p class="sptc-setting-description">
					<?php esc_html_e( 'New lessons will be created for every post with this category. If no category is selected, new lessons will be created for all posts.', 'sensei-post-to-course' ); ?>
				</p>
			</div>
		</div>

		<?php submit_button(); ?>
	</form>
</div>
