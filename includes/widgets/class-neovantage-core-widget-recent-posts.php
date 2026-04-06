<?php
/**
 * Recent Posts Widget
 *
 * @link       https://pixelspress.com
 * @since      2.0.0
 *
 * @package    Neovantage_Core
 * @subpackage Neovantage_Core/includes
 */

/**
 * Recent Posts Widget.
 *
 * This class display recent posts in widget area.
 *
 * @since      2.0.0
 * @package    Neovantage_Core
 * @subpackage Neovantage_Core/includes
 * @author     PixelsPress <support@pixelspress.com>
 * @copyright  (c) Copyright by PixelsPress
 */
class Neovantage_Core_Widget_Recent_Posts extends WP_Widget {

	/**
	 * The defaults that's responsible for assigning the defaults values of the
	 * recent posts widget.
	 *
	 * @since 2.0.0
	 * @var array contains default values of the recent posts widget.
	 */
	private $defaults;

	/**
	 * Sets up a 'recent posts' widget instance.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'widget_recent_posts',
			'description' => esc_html__( 'A widget for displaying the latest posts', 'neovantage-core' ),
		);
		parent::__construct( 'nc-recent-posts', esc_html__( 'NEOVANTAGE Latest Posts', 'neovantage-core' ), $widget_ops );

		$default_widget_title = esc_html__( 'Latest Posts', 'neovantage-core' );
		$this->defaults       = array(
			'title'          => $default_widget_title,
			'num'            => '5',
			'sort_by'        => '',
			'asc_sort_order' => '',
			'category'       => '',
		);
	}

	/**
	 * Outputs the content for the current TOC widget instance.
	 *
	 * @since 2.0.0
	 *
	 * @global  $wp_query   WP_QUERY
	 *
	 * @param   array $args     Display arguments including 'before_title', 'after_title', 'before_widget', and 'after_widget'.
	 * @param   array $instance   Settings for the current Text widget instance.
	 */
	public function widget( $args, $instance ) {
		extract( $args, EXTR_SKIP );
		$instance = wp_parse_args( $instance, $this->defaults );

		$title             = apply_filters( 'nc_widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$valid_sort_orders = array( 'date', 'title', 'comment_count', 'rand', 'modified' );

		if ( in_array( $instance['sort_by'], $valid_sort_orders ) ) {
			$sort_by    = $instance['sort_by'];
			$sort_order = (bool) $instance['asc_sort_order'] ? 'ASC' : 'DESC';
		} else {
			// By default, display latest first.
			$sort_by    = 'date';
			$sort_order = 'DESC';
		}

		// Setup time/date
		// TODO: is this still needed?
		$post_date = the_date( 'Y-m-d', '', '', false );
		$month_ago = date( 'Y-m-d', mktime( 0, 0, 0, date( 'm' ) - 1, date( 'd' ), date( 'Y' ) ) );
		if ( $post_date > $month_ago ) {
			/* translators: %1$s: month name */
			$post_date = sprintf( esc_html__( '%1$s ago', 'neovantage-core' ), human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) );
		} else {
			$post_date = get_the_date();
		}

		$args = array(
			'orderby'             => $sort_by,
			'order'               => $sort_order,
			'posts_per_page'      => $instance['num'],
			'ignore_sticky_posts' => 1,
			'no_found_rows'       => true,
		);

		$category = $instance['category'];

		if ( ! empty( $category ) ) {
			// tax query.
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'category',
					'field'    => 'term_id',
					'terms'    => $category,
					'operator' => 'IN',
				),
			);
		}

		$wp_query = new WP_Query( $args );

		echo $before_widget; // phpcs:ignore WordPress.Security.EscapeOutput

		if ( $title ) {
			echo $before_title . $title . $after_title; // phpcs:ignore WordPress.Security.EscapeOutput
		}

		if ( $wp_query->have_posts() ) :
			?>
			<ul>
				<?php
				while ( $wp_query->have_posts() ) :
					$wp_query->the_post();
					$post_format = get_post_format();
					?>
					<li class="clearfix">
						<div class="entry-media">
							<div class="blog-img hover-effect">
								<figure>
									<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
										<?php
										if ( ( has_post_thumbnail() ) ) {
											$thumb_output = get_the_post_thumbnail( get_the_ID(), 'recent-posts' );
										} else {
											$n_thumbnail        = get_template_directory_uri() . '/images/thumbnail-80x60.png';
											$n_retina_thumbnail = get_template_directory_uri() . '/images/thumbnail-80x60@2x.png';
											$thumb_output       = '<img src="' . esc_url( $n_thumbnail ) . '" alt="article placeholder" data-at2x="' . esc_url( $n_retina_thumbnail ) . '">';
										}
										echo wp_kses_post( $thumb_output );
										?>
										<span class="hover">
											<?php $post_icon = neovantage_get_post_format_icon(); ?>
											<i class="<?php echo esc_attr( $post_icon['icon'] ); ?>"></i>
										</span>
									</a>
								</figure>
							</div>
						</div>
						<div class="entry-header">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							<span class="post-date"><?php the_date(); ?></span>
						</div>
					</li>
				<?php endwhile; ?>
			</ul>
			<?php
			endif;
			echo $after_widget; // phpcs:ignore WordPress.Security.EscapeOutput
	}

			/**
			 * Outputs the Articles Widget Settings Form.
			 *
			 * @since   2.0.0
			 * @param   array $instance Current settings.
			 * @return  void
			 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		// Category Option, Todo – Delete?
		$args = array(
			'type'         => 'post',
			'child_of'     => 0,
			'parent'       => '',
			'orderby'      => 'name',
			'order'        => 'ASC',
			'hide_empty'   => 1,
			'hierarchical' => 1,
			'exclude'      => '',
			'include'      => '',
			'number'       => '',
			'taxonomy'     => 'category',
			'pad_counts'   => false,
		);

		$categories = get_categories();

		// Store the Values of the Widget in Their Own Variable.
		$title          = wp_strip_all_tags( $instance['title'] );
		$num            = $instance['num'];
		$sort_by        = $instance['sort_by'];
		$asc_sort_order = $instance['asc_sort_order'];
		$category       = $instance['category'];
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_html_e( 'Title', 'neovantage-core' ); ?> :
				<input type="text" class="widefat title" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
			</label>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'num' ) ); ?>">
				<?php esc_html_e( 'Number of Posts to Show', 'neovantage-core' ); ?> :
				<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'num' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'num' ) ); ?>" value="<?php echo absint( $instance['num'] ); ?>" size='3' style="text-align: center;" />
			</label>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>">
				<?php esc_html_e( 'Category', 'neovantage-core' ); ?> :
				<select id="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'category' ) ); ?>" class="widefat">
					<option value="" <?php selected( $instance['category'], '' ); ?>><?php esc_html_e( 'All Categories', 'neovantage-core' ); ?></option>
			<?php foreach ( $categories as $category ) : ?>
						<option value="<?php echo intval( $category->term_id ); ?>"<?php selected( $instance['category'], $category->term_id ); ?>><?php echo esc_attr( $category->name ); ?></option>
					<?php endforeach; ?>
				</select>
			</label>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'sort_by' ) ); ?>">
				<?php esc_html_e( 'Sort By', 'neovantage-core' ); ?> :
				<select id="<?php echo esc_attr( $this->get_field_id( 'sort_by' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'sort_by' ) ); ?>" class="widefat">
					<option value="date"<?php selected( $instance['sort_by'], 'date' ); ?>><?php esc_html_e( 'Date', 'neovantage-core' ); ?></option>
					<option value="title"<?php selected( $instance['sort_by'], 'title' ); ?>><?php esc_html_e( 'Title', 'neovantage-core' ); ?></option>
					<option value="comment_count"<?php selected( $instance['sort_by'], 'comment_count' ); ?>><?php esc_html_e( 'Number of comments', 'neovantage-core' ); ?></option>
					<option value="rand"<?php selected( $instance['sort_by'], 'rand' ); ?>><?php esc_html_e( 'Random', 'neovantage-core' ); ?></option>
					<option value="modified"<?php selected( $instance['sort_by'], 'modified' ); ?>><?php esc_html_e( 'Modified', 'neovantage-core' ); ?></option>
				</select>
			</label>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'asc_sort_order' ) ); ?>">
				<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'asc_sort_order' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'asc_sort_order' ) ); ?>" <?php checked( (bool) $instance['asc_sort_order'], true ); ?> />
				<?php esc_html_e( 'Reverse Sort Order (Ascending)', 'neovantage-core' ); ?>
			</label>
		</p>
				<?php
	}

			/**
			 * Handles updating settings for the current Articles widget instance.
			 *
			 * @since 2.0.0
			 *
			 * @param array $new_instance New settings for this instance as input by the user via
			 *                            WP_Widget::form().
			 * @param array $old_instance Old settings for this instance.
			 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		// Update Widget’s Old Values With the New, Incoming Values.
		$instance['title']          = isset( $new_instance['title'] ) ? wp_strip_all_tags( $new_instance['title'] ) : $this->defaults['title'];
		$instance['category']       = isset( $new_instance['category'] ) ? $new_instance['category'] : $this->defaults['category'];
		$instance['num']            = isset( $new_instance['num'] ) ? intval( $new_instance['num'] ) : $this->defaults['num'];
		$instance['sort_by']        = isset( $new_instance['sort_by'] ) ? $new_instance['sort_by'] : $this->defaults['sort_by'];
		$instance['asc_sort_order'] = isset( $new_instance['asc_sort_order'] ) ? 1 : 0;

		return $instance;
	}
}
new Neovantage_Core_Widget_Recent_Posts();

// end class.
add_action(
	'widgets_init',
	function () {
		register_widget( 'Neovantage_Core_Widget_Recent_Posts' );
	}
);
