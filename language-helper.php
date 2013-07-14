<?php

/*
Plugin Name: Language Helper
Plugin URI: https://github.com/benhuson/language-helper
Description: Allows you to select the language used for the WordPress frontend and admin.
Version: 0.1
Author: Ben Huson
Author URI: https://github.com/benhuson
License: GPL2
*/

/*
Copyright 2013 Ben Huson

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class LanguageHelper {

	/**
	 * Init
	 *
	 * Set up actions and filters.
	 *
	 * @since  0.1.3
	 */
	function init() {
		add_action( 'plugins_loaded', array( 'LanguageHelper', 'load_plugin_textdomain' ) );
		if ( is_admin() ) {
			add_filter( 'locale', array( 'LanguageHelper', 'change_admin_language' ) );
			add_action( 'admin_init', array( 'LanguageHelper', 'register_settings' ) );
			add_action( 'admin_menu', array( 'LanguageHelper', 'admin_options' ) );
		} else {
			add_filter( 'locale', array( 'LanguageHelper', 'change_public_language' ) );
		}
	}

	/**
	 * Load Plugin Text Domain
	 *
	 * @since  0.1.3
	 */
	function load_plugin_textdomain() {
		load_plugin_textdomain( 'language-helper', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Register Settings
	 *
	 * @since  0.1.3
	 */
	function register_settings() {
    	register_setting( 'wplang_helper_options_group', 'wplang_helper_options', array( 'LanguageHelper', 'validate_options' ) );
	}

	/**
	 * Init Options
	 *
	 * If Peepo options don't exist, add defaults.
	 *
	 * @since  0.1.3
	 */
	function init_options() {
		add_option( 'wplang_helper_options', LanguageHelper::get_options() );
	}

	/**
	 * Validate Options
	 *
	 * Ensure the 'support' option is an array.
	 *
	 * @since  0.1.3
	 *
	 * @param   array  $input  Peepo options array.
	 * @return  array          Options array.
	 */
	function validate_options( $input ) {
		return $input;
	}

	/**
	 * Add Language Helper Menu to Tools
	 *
	 * @since  0.1.3
	 */
	function admin_options() {
		add_management_page( __( 'Language Helper', 'language-helper' ), __( 'Language Helper', 'language-helper' ), 'manage_options', 'wplang_helper_options', array( 'LanguageHelper', 'admin_options_page' ) );
	}

	/**
	 * Add Language Helper Page
	 *
	 * @since  0.1.3
	 */
	function admin_options_page() {
		$updated = isset( $_REQUEST['updated'] ) ? $_REQUEST['updated'] : false;
		?>

		<div class="wrap">
			<?php
			screen_icon();
			echo '<h2>' . __( 'Language Helper', 'language-helper' ) . '</h2>';
			?>

			<?php if ( false !== $updated ) : ?>
				<div><p><strong><?php _e( 'Options saved' ); ?></strong></p></div>
			<?php endif; ?>

			<form method="post" action="options.php">

				<?php $settings = LanguageHelper::get_options(); ?>

				<?php settings_fields( 'wplang_helper_options_group' ); ?>

				<p><?php _e( 'This plugin detects an <tt>.mo</tt> language files installed in your <tt>wp-content/languages</tt> directory.', 'language-helper' ); ?></p>
				<p><?php printf( __( 'The naming convention of the <tt>.mo</tt> files is based on the <a %s>ISO-639</a> language code (e.g. <i>pt</i> for Portuguese) followed by the <a %s>ISO-3166</a> country code (e.g. <i>_PT</i> for Portugal or <i>_BR</i> for Brazil). So, the Brazilian Portuguese file would be called <tt>pt_BR.mo</tt>, and a non-specific Portuges file would be called <tt>pt.mo</tt>.', 'language-helper' ), 'href="http://www.gnu.org/software/gettext/manual/html_chapter/gettext_16.html#Language-Codes" target="_blank"', 'href="http://www.gnu.org/software/gettext/manual/html_chapter/gettext_16.html#Country-Codes" target="_blank"' ); ?></p>

				<h3><?php _e( 'Where can I download .mo files?', 'language-helper' ); ?></h3>
				<p><?php _e( "It's not always easy to track down .mo files but you could try the following links:", 'language-helper' ); ?></p>

				<ul>
					<li><a href="http://svn.automattic.com/wordpress-i18n/" target="_blank"><?php _e( 'WordPress Language File Repository', 'language-helper' ); ?></a></li>
					<li><a href="http://codex.wordpress.org/WordPress_in_Your_Language" target="_blank"><?php _e( 'WordPress in Your Language', 'language-helper' ); ?></a></li>
					<li><a href="http://codex.wordpress.org/Installing_WordPress_in_Your_Language" target="_blank"><?php _e( 'Installing WordPress in Your Language', 'language-helper' ); ?></a></li>
				</ul>

				<p>&nbsp;</p>

				<h3><?php _e( 'Language Settings', 'language-helper' ); ?></h3>

				<?php if ( LanguageHelper::has_installed_languages() ) { ?>

					<table class="form-table">
						<tr valign="top">
							<th scope="row"><label for="wplang_helper_public_language"><?php _e( 'Public Language', 'language-helper' ); ?></label></th>
							<td>
								<select name="wplang_helper_options[public_language]" id="wplang_helper_public_language">
									<option value="">–– <?php _e( 'Default' ); ?> ––</option>';
									<?php echo LanguageHelper::language_menu_options( $settings['public_language'] ); ?>
								</select>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><label for="wplang_helper_admin_language"><?php _e( 'Admin Language', 'language-helper' ); ?></label></th>
							<td>
								<select name="wplang_helper_options[admin_language]" id="wplang_helper_admin_language">
									<option value="">–– <?php _e( 'Default' ); ?> ––</option>';
									<?php echo LanguageHelper::language_menu_options( $settings['admin_language'] ); ?>
								</select>
							</td>
						</tr>
					</table>

					<p class="submit"><input type="submit" value="<?php _e( 'Save Options' ); ?>" class="button-primary" /></p>

				<?php } else { ?>

					<p><?php _e( 'You do not have any language files installed. Please see above.', 'language-helper' ); ?></p>

				<?php } ?>
			</form>

		</div>

		<?php
	}

	/**
	 * Get Public Language Setting
	 *
	 * @since  0.1.3
	 *
	 * @return  string  Language.
	 */
	function public_language() {
		return LanguageHelper::get_option( 'public_language' );
	}

	/**
	 * Get Admin Language Setting
	 *
	 * @since  0.1.3
	 *
	 * @return  string  Language.
	 */
	function admin_language() {
		return LanguageHelper::get_option( 'admin_language' );
	}

	/**
	 * Change Public Language
	 *
	 * Used to filter the default language.
	 *
	 * @since  0.1.3
	 *
	 * @param   string  $locale  Default Language.
	 * @return  string  Language.
	 */
	function change_public_language( $locale ) {
		$lang = LanguageHelper::public_language();
		if ( ! empty( $lang ) )
			return $lang;
		return $locale;
	}

	/**
	 * Change Admin Language
	 *
	 * Used to filter the default language.
	 *
	 * @since  0.1.3
	 *
	 * @param   string  $locale  Default Language.
	 * @return  string  Language.
	 */
	function change_admin_language( $locale ) {
		$lang = LanguageHelper::admin_language();
		if ( ! empty( $lang ) )
			return $lang;
		return $locale;
	}

	/**
	 * Get Options
	 *
	 * Get Peepo options array.
	 *
	 * @since  0.1.3
	 *
	 * @return  array  Options array.
	 */
	function get_options() {
		$default_options = array(
		);
		$options = wp_parse_args( get_option( 'wplang_helper_options', $default_options ), $default_options );
		return $options;
	}

	/**
	 * Get Option
	 *
	 * Get an option from the Peepo options array.
	 *
	 * @since  0.1.3
	 *
	 * @param   string  $option  Peepo option key.
	 * @return  mixed            Option value.
	 */
	function get_option( $option ) {
		$options = LanguageHelper::get_options();
		if ( isset( $options[$option] ) )
			return $options[$option];
		return null;
	}

	/**
	 * Get .mo Files
	 *
	 * Get .mo files from wp-content/languages directory.
	 *
	 * @since  0.1.3
	 *
	 * @return  array  Array of .mo files.
	 */
	function get_mo_files() {
		$mo_files = glob( WP_CONTENT_DIR . '/languages/*.mo' );
		return array_map( 'basename', $mo_files );
	}

	/**
	 * Get Installed Languages
	 *
	 * @since  0.1.3
	 *
	 * @return  array  Array of language codes.
	 */
	function get_installed_languages() {
		$mo_files = LanguageHelper::get_mo_files();
		foreach ( $mo_files as $key => $mo_file ) {
			$mo_file = substr( $mo_file, 0, strlen( $mo_file ) - 3 );
			$file_parts = explode( '-', $mo_file );
			$mo_files[$key] = $file_parts[count( $file_parts ) - 1];
		}
		$mo_files = array_unique( $mo_files );
		return $mo_files;
	}

	/**
	 * Has Installed Languages
	 *
	 * @since  0.1.3
	 *
	 * @return  bool  Has mo files?
	 */
	function has_installed_languages() {
		$mo_files = LanguageHelper::get_installed_languages();
		if ( count( $mo_files ) > 0 )
			return true;
		return false;
	}

	/**
	 * Language Menu Options
	 *
	 * @since  0.1.3
	 *
	 * @param   string  $selected  Selected menu option.
	 * @return  string             HTML menu options.
	 */
	function language_menu_options( $selected = null ) {
		$html = '';
		$options = LanguageHelper::get_installed_languages();
		foreach ( $options as $lang ) {
			$html .= '<option value="' . esc_attr( $lang ) . '" ' . selected( $lang, $selected, false ) . '>' . esc_html( $lang ) . '</option>';
		}
		return $html;
	}

}

LanguageHelper::init();
