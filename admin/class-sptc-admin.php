<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://automattic.com
 * @since      1.0.0
 *
 * @package    Sensei_Post_To_Course
 * @subpackage Sensei_Post_To_Course/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Sensei_Post_To_Course
 * @subpackage Sensei_Post_To_Course/admin
 * @author     Automattic
 */
class Sensei_Post_To_Course_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Instance of the lesson processing class.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      WP_Lesson_Process    $process_lessons    Instance of the lesson processing class.
	 */
	protected $process_lessons;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version           The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name     = $plugin_name;
		$this->version         = $version;
		$this->process_lessons = new WP_Lesson_Process();
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Sensei_Post_To_Course_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Sensei_Post_To_Course_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/sptc-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since 1.1.0
	 */
	public function enqueue_scripts() {
		$screen = get_current_screen();

		if ( 'tools_page_sensei-post-to-course' === $screen->id ) {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/sptc-admin.js', array(), $this->version );
		}
	}

	/**
	 * Add "Post to Course Creator" sub-menu under the "Tools" menu.
	 *
	 * @since    1.0.0
	 */
	public function register_menu() {
		$hook_suffix = add_submenu_page(
			'tools.php',
			__( 'Post to Course Creator', 'sensei-post-to-course' ),
			__( 'Post to Course Creator', 'sensei-post-to-course' ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'render_admin_ui' )
		);
	}

	/**
	 * Render the page for the sub-menu.
	 *
	 * @since    1.0.0
	 */
	public function render_admin_ui() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		include_once( 'partials/sptc-admin-display.php' );
	}

	/**
	 * Register the plugin's settings.
	 *
	 * @since    1.0.0
	 */
	public function register_settings() {
		register_setting( $this->plugin_name, 'sptc_settings', array( $this, 'create_course_and_lessons' ) );
	}

	/**
	 * Create the course and its lessons.
	 *
	 * @since    1.0.0
	 * @param    array    $input       Input data.
	 * @return   bool                  false to indicate that the data should not be saved.
	 */
	public function create_course_and_lessons( $input = array() ) {
		if ( ! current_user_can( 'create_courses' ) || empty( $input ) ) {
			return false;
		}

		// Create the course.
		$course_name = isset( $input['course_name'] ) ? trim( $input['course_name'] ) : '';
		$post_type   = isset( $input['post_type'] ) ? trim( $input['post_type'] ) : 'post';
		$category_id = 'post' === $post_type && isset( $input['category_id'] ) ? intval( $input['category_id'] ) : -1;
		$course_id   = $this->create_course( $course_name );

		if ( 0 === $course_id ) {
			add_settings_error(
				'sptc_settings',
				'post-to-course',
				__( 'Course could not be created.', 'sensei-post-to-course' ),
				'error'
			);

			return false;
		}

		// Create the lessons.
		$this->create_lessons( $course_id, $post_type, $category_id );

		add_settings_error(
			'sptc_settings',
			'post-to-course',
			sprintf(
				__( 'Course <a href="%1$s">%2$s</a> was created successfully!', 'sensei-post-to-course' ),
				get_edit_post_link( $course_id ),
				$course_name
			),
			'success'
		);

		return false;
	}

	/**
	 * Create the course.
	 *
	 * @since    1.0.0
	 * @param    string    $course_name       Name of the course.
	 * @return   int                          Course ID on success. The value 0 on failure.
	 */
	private function create_course( $course_name ) {
		if ( empty( $course_name ) ) {
			return 0;
		}

		$content = '';

		// Add course blocks if the site is configured to use blocks.
		if ( function_exists( 'register_block_type' ) && ! $this->is_classic_editor_plugin_active() ) {
			$content = '<!-- wp:paragraph {"placeholder":"Course description, objectives, and overview.","className":"sensei-content-description"} -->
				<p class="sensei-content-description"></p>
				<!-- /wp:paragraph -->

				<!-- wp:sensei-lms/button-take-course -->
				<div class="wp-block-sensei-lms-button-take-course is-style-default wp-block-sensei-button wp-block-button has-text-align-left">
				<button class="wp-block-button__link">Take Course</button></div>
				<!-- /wp:sensei-lms/button-take-course -->

				<!-- wp:sensei-lms/course-progress /-->

				<!-- wp:sensei-lms/course-outline /-->';
		}

		$course_id = wp_insert_post( array(
			'post_content' => $content,
			'post_title'   => $course_name,
			'post_type'    => 'course',
		) );

		return $course_id;
	}

	/**
	 * Create the lessons.
	 *
	 * @since 1.0.0
	 * @param int    $course_id       Course ID.
	 * @param string $post_type       Post type.
	 * @param int    $category_id     Category ID.
	 */
	private function create_lessons( $course_id, $post_type, $category_id ) {
		$post_args = array(
			'numberposts' => -1,
			'order'       => 'ASC',
			'post_type'   => $post_type,
		);

		if ( -1 !== $category_id ) {
			$post_args['category'] = $category_id;
		}

		$posts = get_posts( $post_args );

		// Create the lessons in a background process.
		foreach ( $posts as $post ) {
			$item                 = array();
			$item['course_id']    = $course_id;
			$item['post_id']      = $post->ID;
			$item['post_title']   = $post->post_title;
			$item['post_content'] = $post->post_content;
			$item['post_excerpt'] = $post->post_excerpt;

			$this->process_lessons->push_to_queue( $item );
		}

		$this->process_lessons->save()->dispatch();
	}

	/**
	 * Check if Classic Editor plugin is active.
	 *
	 * @since  1.1.0
	 * @return bool  True if the Classic Editor plugin is active.
	 */
	private function is_classic_editor_plugin_active() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		if ( is_plugin_active( 'classic-editor/classic-editor.php' ) ) {
			return true;
		}

		return false;
	}
}
