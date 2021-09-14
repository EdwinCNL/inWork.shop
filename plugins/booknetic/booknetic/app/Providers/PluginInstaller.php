<?php

namespace BookneticApp\Providers;

class PluginInstaller
{

	private $download_link;

	public function __construct( $download_link )
	{
		$this->download_link = $download_link;
	}

	public function install()
	{
		require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/misc.php';
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

		$upgrader = new \Plugin_Upgrader( new BookneticQuietSkin( ) );

		if ( ! file_exists( WP_PLUGIN_DIR . '/booknetic-saas/init.php' ) )
		{
			$upgrader->install( $this->download_link );
		}

		if ( file_exists( WP_PLUGIN_DIR . '/booknetic-saas/init.php' ) )
		{
			activate_plugin( WP_PLUGIN_DIR . '/booknetic-saas/init.php' );
		}

		return true;
	}

}

