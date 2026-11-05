<?php
/**
 * @package ruijinen-block-patterns
 * @author mgn
 * @license GPL-2.0+
 */

namespace Ruijinen\Pattern\Master\App\Setup;

class TextDomain {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'load_textdomain_mofile', array( $this, 'load_textdomain_mofile' ), 10, 2 );
		load_plugin_textdomain( RJE_BP_TEXTDOMAIN, false, dirname( RJE_BP_PLUGIN_BASENAME ) . '/languages' );
	}

	/**
	 * When local .mo file exists, load this.
	 *
	 * @param string $mofile Path to the MO file.
	 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
	 * @return string
	 */
	public function load_textdomain_mofile( $mofile, $domain ) {
		if ( RJE_BP_TEXTDOMAIN === $domain ) {
			$local_mofile = RJE_BP_PLUGIN_PATH . 'languages/' . basename( $mofile );
			if ( file_exists( $local_mofile ) ) {
				return $local_mofile;
			}
		}
		return $mofile;
	}
}
