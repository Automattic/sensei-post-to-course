<?php

/**
 * Define the background process that creates lessons.
 *
 * @since      1.0.0
 * @package    Sensei_Post_To_Course
 * @subpackage Sensei_Post_To_Course/admin
 * @author     Automattic
 */

class WP_Lesson_Process extends WP_Background_Process {
	/**
	 * @var string
	 */
	protected $action = 'lesson_process';

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queued item. Return the modified item for further processing
	 * in the next pass through. Or return false to remove the
	 * item from the queue.
	 *
	 * @param array  $item  Queue item to iterate over.
	 *
	 * @return bool         false to remove the item from the queue.
	 */
	protected function task( $item ) {
		$lesson_id    = wp_insert_post( array(
			'post_title'       => $item['post_title'],
			'post_content'     => $item['post_content'],
			'post_excerpt'     => $item['post_excerpt'],
			'post_type'        => 'lesson',
			'meta_input'       => array(
				'_lesson_course' => $item['course_id'],
			),
		) );

		// Lesson could not be created.
		if ( 0 === $lesson_id ) {
			return false;
		}

		// Add featured image.
		$thumbnail_id = get_post_thumbnail_id( $item['post_id'] );

		if ( $thumbnail_id ) {
			set_post_thumbnail( $lesson_id, $thumbnail_id );
		}

		return false;
	}

	/**
	 * Complete
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 */
	protected function complete() {
		parent::complete();
	}
}
