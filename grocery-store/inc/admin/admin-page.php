<?php
/**
 * grocery-store Admin Class.
 *
 * @author  aThemeart
 * @package grocery-store
 * @since   1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'grocery_store_admin' ) ) :

/**
 * grocery_store_admin Class.
 */
class grocery_store_admin {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'wp_loaded', array( __CLASS__, 'hide_notices' ) );
		add_action( 'load-themes.php', array( $this, 'admin_notice' ) );
	}

	/**
	 * Add admin menu.
	 */
	public function admin_menu() {
		$theme = wp_get_theme( get_template() );

		$page = add_theme_page( esc_attr__( 'Getting Started', 'grocery-store' ) , 
		esc_attr__( 'Getting Started', 'grocery-store' ), 
		'activate_plugins', 
		'welcome', array( $this, 'welcome_screen' ) );
		
		add_action( 'admin_print_styles-' . $page, array( $this, 'enqueue_styles' ) );
	}

	/**
	 * Enqueue styles.
	 */
	public function enqueue_styles() {

		wp_enqueue_style( 'grocery-store-welcome', get_template_directory_uri() . '/inc/admin/welcome.css', array(), '1.0' );
	}

	/**
	 * Add admin notice.
	 */
	public function admin_notice() {
		global $pagenow;

		wp_enqueue_style( 'grocery-store-message', get_template_directory_uri() . '/inc/admin/message.css', array(), '1.0' );

		// Let's bail on theme activation.
		if ( 'themes.php' == $pagenow && isset( $_GET['activated'] ) ) {
			add_action( 'admin_notices', array( $this, 'welcome_notice' ) );
			get_theme_mod( 'grocery_store_admin_notice_welcome', 1 );

		// No option? Let run the notice wizard again..
		} elseif( ! get_option( 'grocery_store_admin_notice_welcome' ) ) {
			add_action( 'admin_notices', array( $this, 'welcome_notice' ) );
		}
	}

	/**
	 * Hide a notice if the GET variable is set.
	 */
	public static function hide_notices() {
		if ( isset( $_GET['grocery-store-hide-notice'] ) && isset( $_GET['_grocery_store_notice_nonce'] ) ) {
			if ( ! wp_verify_nonce( wp_unslash($_GET['_grocery_store_notice_nonce']), 'grocery-store_hide_notices_nonce' ) ) {
				/* translators: %s: plugin name. */
				wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'grocery-store' ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) 
			/* translators: %s: plugin name. */{
				wp_die( esc_html__( 'Cheatin&#8217; huh?', 'grocery-store' ) );
			}

			$hide_notice = sanitize_text_field( wp_unslash( $_GET['grocery-store-hide-notice'] ) );
			get_theme_mod( 'grocery_store_admin_notice_' . $hide_notice, 1 );
		}
	}

	/**
	 * Show welcome notice.
	 */
	public function welcome_notice() {
		?>
		<div id="message" class="updated cresta-message">
			<h2><?php esc_html_e('Congratulations','grocery-store');?> </h2>
			<a class="cresta-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( remove_query_arg( array( 'activated' ), add_query_arg( 'grocery-store-hide-notice', 'welcome' ) ), 'grocery-store_hide_notices_nonce', '_grocery_store_notice_nonce' ) ); ?>"><?php  /* translators: %s: plugin name. */ esc_html_e( 'Dismiss', 'grocery-store' ); ?></a>
			<p><?php printf( /* translators: %s: plugin name. */  esc_html__( 'Thank you for choosing Grocery Store theme ! To fully take advantage of the best our theme can offer please make sure you visit our %1$swelcome page%2$s.', 'grocery-store' ), '<a href="' . esc_url( admin_url( 'themes.php?page=welcome' ) ) . '">', '</a>' ); ?></p>
			<p class="submit">
				<a class="button-secondary" href="<?php echo esc_url( admin_url( 'themes.php?page=welcome' ) ); ?>"><?php esc_html_e( 'Get started with grocery store', 'grocery-store' ); ?></a>
			</p>
		</div>
		<?php
	}

	/**
	 * Intro text/links shown to all about pages.
	 *
	 * @access private
	 */
	private function intro() {
		$theme = wp_get_theme( get_template() );
		?>
		<div class="cresta-theme-info">
				<h1>
					<?php esc_html_e('About', 'grocery-store'); ?>
					<?php echo esc_html( $theme->get( 'Name' )) ." ". esc_html( $theme->get( 'Version' ) ); ?>
				</h1>

			<div class="welcome-description-wrap">
				<div class="about-text"><?php echo esc_html( $theme->display( 'Description' ) ); ?>
				<p class="cresta-actions">
					<a href="<?php echo esc_url( 'https://athemeart.com/downloads/grocerystore-wordpress-woocommerce-theme/' ); ?>" class="button button-secondary" target="_blank"><?php esc_html_e( 'Theme Info', 'grocery-store' ); ?></a>

					<a href="<?php echo esc_url( apply_filters( 'grocery-store_pro_theme_url', 'https://athemeart.com/demo/grocery-store/' ) ); ?>" class="button button-secondary docs" target="_blank"><?php esc_html_e( 'View Demo', 'grocery-store' ); ?></a>

					<a href="<?php echo esc_url( apply_filters( 'grocery-store_pro_theme_url', 'https://athemeart.com/downloads/grocerystore-wordpress-woocommerce-theme/' ) ); ?>" class="button button-primary docs" target="_blank"><?php esc_html_e( 'View PRO version Demo', 'grocery-store' ); ?></a>

					<a href="<?php echo esc_url( apply_filters( 'grocery-store_pro_theme_url', 'https://wordpress.org/support/theme/grocery-store/reviews/#new-post' ) ); ?>" class="button button-secondary docs" target="_blank"><?php esc_html_e( 'Rate this theme', 'grocery-store' ); ?></a>
				</p>
				</div>

				<div class="cresta-screenshot">
					<img src="<?php echo esc_url( get_template_directory_uri() ) . '/screenshot.png'; ?>" />
				</div>
			</div>
		</div>

		<h2 class="nav-tab-wrapper">
			
			<a class="nav-tab <?php if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'free_vs_pro' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'welcome', 'tab' => 'free_vs_pro' ), 'themes.php' ) ) ); ?>">
				<?php esc_html_e( 'Free Vs PRO', 'grocery-store' ); ?>
			</a>
			<a class="nav-tab <?php if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'changelog' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'welcome', 'tab' => 'changelog' ), 'themes.php' ) ) ); ?>">
				<?php esc_html_e( 'Changelog', 'grocery-store' ); ?>
			</a>
            
            
            
            
		</h2>
		<?php
	}

	/**
	 * Welcome screen page.
	 */
	public function welcome_screen() {
			
		$tabs_data = isset( $_GET['tab'] ) ? sanitize_title( wp_unslash($_GET['tab']) ) : '';
		$current_tab = empty( $tabs_data ) ? /* translators: About. */ esc_html('about','grocery-store') : $tabs_data;

		// Look for a {$current_tab}_screen method.
		if ( is_callable( array( $this, $current_tab . '_screen' ) ) ) {
			return $this->{ $current_tab . '_screen' }();
		}

		// Fallback to about screen.
		return $this->about_screen();
	}

	/**
	 * Output the about screen.
	 */
	public function about_screen() {
		$theme = wp_get_theme( get_template() );
		?>
		<div class="wrap about-wrap">

			<?php $this->intro(); ?>

			<div class="changelog point-releases">
				<div class="under-the-hood two-col">
					<div class="col">
						<h4><?php esc_html_e( 'Theme Customizer', 'grocery-store' ); ?></h4>
						<p><?php esc_html_e( 'All Theme Options are available via Customize screen.', 'grocery-store' ) ?></p>
						<p><a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>" class="button button-secondary"><?php /* translators: %s: plugin name. */ esc_html_e( 'Customize', 'grocery-store' ); ?></a></p>
					</div>

					<div class="col">
						<h4><?php esc_html_e( 'Got theme support question?', 'grocery-store' ); ?></h4>
						<p><?php esc_html_e( 'Please put it in our support forum.', 'grocery-store' ) ?></p>
						<p><a target="_blank" href="<?php echo esc_url( 'https://athemeart.com/support/' ); ?>" class="button button-secondary"><?php esc_html_e( 'Support', 'grocery-store' ); ?></a></p>
					</div>

					<div class="col">
						<h4><?php esc_html_e( 'Need more features?', 'grocery-store' ); ?></h4>
						<p><?php esc_html_e( 'Upgrade to PRO version for more exciting features.', 'grocery-store' ) ?></p>
						<p><a target="_blank" href="<?php echo esc_url( 'https://athemeart.com/downloads/grocerystore-wordpress-woocommerce-theme/?ref=welcome' ); ?>" class="button button-secondary"><?php esc_html_e( 'Info about PRO version', 'grocery-store' ); ?></a></p>
					</div>

					
				</div>
			</div>

			<div class="return-to-dashboard cresta">
				<?php if ( current_user_can( 'update_core' ) && isset( $_GET['updated'] ) ) : ?>
					<a href="<?php echo esc_url( self_admin_url( 'update-core.php' ) ); ?>">
						<?php is_multisite() ? esc_html_e( 'Return to Updates', 'grocery-store' ) : esc_html_e( 'Return to Dashboard &rarr; Updates', 'grocery-store' ); ?>
					</a> |
				<?php endif; ?>
				<a href="<?php echo esc_url( self_admin_url() ); ?>"><?php is_blog_admin() ? esc_html_e( 'Go to Dashboard &rarr; Home', 'grocery-store' ) : esc_html_e( 'Go to Dashboard', 'grocery-store' ); ?></a>
			</div>
		</div>
		<?php
	}

		/**
	 * Output the changelog screen.
	 */
	public function changelog_screen() {
		global $wp_filesystem;

		?>
		<div class="wrap about-wrap">

			<?php $this->intro(); ?>

			<p class="about-description"><?php esc_html_e( 'View changelog below:', 'grocery-store' ); ?></p>

			<?php
				$changelog_file = apply_filters( 'grocery-store_changelog_file', get_template_directory() . '/readme.txt' );

				// Check if the changelog file exists and is readable.
				if ( $changelog_file && is_readable( $changelog_file ) ) {
					WP_Filesystem();
					$changelog = $wp_filesystem->get_contents( $changelog_file );
					$changelog_list = $this->parse_changelog( $changelog );

					echo wp_kses_post( $changelog_list );
				}
			?>
		</div>
		<?php
	}

	/**
	 * Parse changelog from readme file.
	 * @param  string $content
	 * @return string
	 */
	private function parse_changelog( $content ) {
		$matches   = null;
		$regexp    = '~==\s*Changelog\s*==(.*)($)~Uis';
		$changelog = '';

		if ( preg_match( $regexp, $content, $matches ) ) {
			$changes = explode( '\r\n', trim( $matches[1] ) );

			$changelog .= '<pre class="changelog">';

			foreach ( $changes as $index => $line ) {
				$changelog .= wp_kses_post( preg_replace( '~(=\s*Version\s*(\d+(?:\.\d+)+)\s*=|$)~Uis', '<span class="title">${1}</span>', $line ) );
			}

			$changelog .= '</pre>';
		}

		return wp_kses_post( $changelog );
	}

	/**
	 * Output the free vs pro screen.
	 */
	public function free_vs_pro_screen() {
		?>
		<div class="wrap about-wrap">

			<?php $this->intro(); ?>

			<p class="about-description"><?php esc_html_e( 'Upgrade to PRO version for more exciting features.', 'grocery-store' ); ?></p>


			<table>
				<thead>
					<tr>
						<th class="table-feature-title"><h4><?php esc_html_e('Features', 'grocery-store'); ?></h4></th>
						<th width="25%"><h4><?php esc_html_e('Grocery Store', 'grocery-store'); ?></h4></th>
						<th width="25%"><h4><?php esc_html_e('Grocery Store PRO', 'grocery-store'); ?></h4></th>
					</tr>
				</thead>
				<tbody>
                <tr>
                  <td><h4><?php esc_html_e('24/7 Priority Support', 'grocery-store'); ?></h4></td>
                  <td><?php esc_html_e('WP forum ( 48 / 5 )', 'grocery-store'); ?></td>
                  <td><?php esc_html_e('Own Ticket, email , Skype &amp; Teamviewer ( 24 / 7 )', 'grocery-store'); ?></td>
                </tr>
                <tr>
                  <td><h4><?php esc_html_e('Theme Customizer', 'grocery-store'); ?> </h4></td>
                  <td><?php esc_html_e('lite features Customizer', 'grocery-store'); ?></td>
                  <td><?php esc_html_e('Theme Options Via codestar framework ( Save $49 )', 'grocery-store'); ?></td>
                </tr>
                <tr>
                  <td><h4><?php esc_html_e('Elementor Page Builder toolkit & Addons', 'grocery-store'); ?></h4></td>
                  <td><?php esc_html_e('Only Elementor plugins', 'grocery-store'); ?></td>
                  <td><?php esc_html_e('Theme Own Addons', 'grocery-store'); ?></td>
                </tr>
                                <tr>
                  <td><h4><?php esc_html_e('Slider Plugins', 'grocery-store'); ?></h4> </td>
                  <td><?php esc_html_e('3rd party plugins', 'grocery-store'); ?></td>
                  <td><?php esc_html_e('Theme Own Slider or 3rd party plugins', 'grocery-store'); ?></td>
                </tr>
                <tr>
                  <td><h4><?php esc_html_e('Advanced WooCommerce Product Search ', 'grocery-store'); ?></h4></td>
                  <td><span class="dashicons dashicons-no"></span><?php esc_html_e('Free Version Only', 'grocery-store'); ?></td>
                  <td><span class="dashicons dashicons-yes"></span><?php esc_html_e('PRO Version ( Save $16 )', 'grocery-store'); ?></td>
                </tr>
                <tr>
                  <td><h4><?php esc_html_e('WooCommerce Modal Fly Cart + Ajax add to cart ', 'grocery-store'); ?></h4></td>
                  <td><span class="dashicons dashicons-no"></span><?php esc_html_e('Free Version Only', 'grocery-store'); ?></td>
                  <td><span class="dashicons dashicons-yes"></span><?php esc_html_e('PRO Version ( Save $13 )', 'grocery-store'); ?></td>
                </tr>
                 <tr>
                  <td><h4><?php esc_html_e('Smart Variation Swatches for WooCommerce Pro ', 'grocery-store'); ?></h4></td>
                  <td><span class="dashicons dashicons-no"></span><?php esc_html_e('Free Version Only', 'grocery-store'); ?></td>
                  <td><span class="dashicons dashicons-yes"></span><?php esc_html_e('PRO Version ( Save $15 )', 'grocery-store'); ?></td>
                </tr>
                
                 <tr>
                  <td><h4><?php esc_html_e('Testimonials Sections', 'grocery-store'); ?></h4></td>
                  <td><span class="dashicons dashicons-no"></span></td>
                  <td><span class="dashicons dashicons-yes"></span></td>
                </tr>
                
               <tr>
                  <td><h4><?php esc_html_e('Pricing Sections', 'grocery-store'); ?></h4></td>
                  <td><span class="dashicons dashicons-no"></span></td>
                  <td><span class="dashicons dashicons-yes"></span></td>
                </tr>
                <tr>
                  <td><h4><?php esc_html_e('Latest Posts Wigets', 'grocery-store'); ?></h4></td>
                  <td><span class="dashicons dashicons-no"></span></td>
                  <td><span class="dashicons dashicons-yes"></span></td>
                </tr>
                
                <tr>
                  <td><h4><?php esc_html_e('Unlimited colors', 'grocery-store'); ?></h4></td>
                  <td><span class="dashicons dashicons-no"></span></td>
                  <td><span class="dashicons dashicons-yes"></span></td>
                </tr>
                <tr>
                  <td><h4><?php esc_html_e('Unlimited Fonts', 'grocery-store'); ?></h4></td>
                  <td><span class="dashicons dashicons-no"></span></td>
                  <td><span class="dashicons dashicons-yes"></span></td>
                </tr>
                
                <tr>
                  <td><h4><?php esc_html_e('Custom Page Template', 'grocery-store'); ?></h4></td>
                  <td><span class="dashicons dashicons-no"></span></td>
                  <td><span class="dashicons dashicons-yes"></span></td>
                </tr>
                
                <tr>
                  <td><h4><?php esc_html_e('Custom Blog Template', 'grocery-store'); ?></h4></td>
                  <td><span class="dashicons dashicons-no"></span></td>
                  <td><span class="dashicons dashicons-yes"></span></td>
                </tr>
                
                <tr>
                  <td><h4><?php esc_html_e('Blog Posts Layout', 'grocery-store'); ?></h4></td>
                  <td><?php esc_html_e('Standard layout', 'grocery-store'); ?></td>
                  <td><?php esc_html_e('Standard,Grids, Standard left, Standard right layout', 'grocery-store'); ?></td>
                </tr>
                <tr>
                  <td><h4><?php esc_html_e('Related Posts ', 'grocery-store'); ?></h4></td>
                  <td><span class="dashicons dashicons-no"></span></td>
                  <td><span class="dashicons dashicons-yes"></span></td>
                </tr>
                <tr>
                  <td><h4><?php esc_html_e('Social Share', 'grocery-store'); ?></h4></td>
                  <td><span class="dashicons dashicons-no"></span></td>
                  <td><span class="dashicons dashicons-yes"></span></td>
                </tr>
               
                <tr>
                  <td><h4><?php esc_html_e('Footer Carditis', 'grocery-store'); ?></h4></td>
                  <td><span class="dashicons dashicons-no"></span></td>
                  <td><span class="dashicons dashicons-yes"></span></td>
                </tr>
                    <tr>
						<td><h4><?php esc_html_e('WooCommerce ', 'grocery-store'); ?></h4></td>
						<td><?php esc_html_e('Basic feature', 'grocery-store'); ?></td>
						<td><?php esc_html_e('Fully customizable feature', 'grocery-store'); ?></td>
					</tr>
                     <tr>
						<td><h4><?php esc_html_e('WooCommerce Custom Plugins', 'grocery-store'); ?></h4></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
                      <tr>
						<td><h4><?php esc_html_e('WooCommerce Customizable Options', 'grocery-store'); ?></h4></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
                     <tr>
						<td><h4><?php esc_html_e('WooCommerce Child Theme', 'grocery-store'); ?></h4></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
                     <tr>
						<td><h4><?php esc_html_e('WooCommerce Layout Options', 'grocery-store'); ?></h4></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
                     <tr>
						<td><h4><?php esc_html_e('You can control overall everything without code', 'grocery-store'); ?></h4></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td class="btn-wrapper">
                        
                        <a href="<?php echo esc_url( apply_filters( 'grocery-store_pro_theme_url', 'https://athemeart.com/downloads/grocerystore-wordpress-woocommerce-theme/' ) ); ?>" class="button button-secondary" target="_blank"><?php esc_html_e( 'More Information', 'grocery-store' ); ?></a>
                    </td>
                </tr>
				</tbody>
			</table>

		</div>
		<?php
	}
	
	
	

}

endif;

return new grocery_store_admin();
