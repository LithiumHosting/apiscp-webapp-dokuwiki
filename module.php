<?php

use LithiumHosting\WebApps\DokuWiki\Handler;
use Module\Support\Webapps;
use Module\Support\Webapps\PhpWrapper;
use Module\Support\Webapps\DatabaseGenerator;
use Module\Support\Webapps\VersionFetcher\Github;
use Opcenter\Auth\Password;
use Opcenter\Map;
use Opcenter\Provisioning\ConfigurationWriter;
use Opcenter\SiteConfiguration;
use Opcenter\Versioning;

/**
 * DokuWiki management
 *
 * @package core
 */
class DokuWiki_Module extends Webapps
{
	const REGISTERED_HANDLER_KEY = 'webapps.dokuwiki';
	const APP_NAME = Handler::NAME;
	const DEFAULT_VERSION_LOCK = 'minor';

	protected $aclList = [
		'min' => [
			'data',
			'lib/plugins',
			'lib/tpl',
			'conf/local.php', //for the installer and for subsequent web configuration to work.
			'conf/local.php.bak',
			'conf/users.auth.php', //for the ACL web configuration and usermanager to work.
			'conf/acl.auth.php', //for the ACL web configuration and usermanager to work.
			'conf/plugins.local.php', //for the Extension Manager
			'conf/plugins.local.php.bak',
		],
		'max' => [
			'data',
			'lib/plugins',
			'lib/tpl',
		],
	];

	protected const CLEANUP_FILES = [
		'COPYING',
		'README',
		'SECURITY.md'
	];

	/**
	 * Install DokuWiki into a pre-existing location
	 *
	 * @param string $hostname domain or subdomain to install DokuWiki
	 * @param string $path     optional path under hostname
	 * @param array  $opts     additional install options
	 * @return bool
	 */
	public function install(string $hostname, string $path = '', array $opts = []): bool
	{
		if (!version_compare($this->php_version(), '7', '>=')) {
			return error('DokuWiki requires PHP7.2 or higher, PHP8+ is recommended');
		}

		if (!($docroot = $this->getDocumentRoot($hostname, $path))) {
			return error("failed to normalize path for `%s'", $hostname);
		}

		if (!$this->parseInstallOptions($opts, $hostname, $path)) {
			return false;
		}

		$args['version'] = $opts['version'];


		/**
		 * Download - https://github.com/splitbrain/dokuwiki/tarball/stable
		 * Extract
		 * Cleanup
		 * Write conf/local.php
		 * Write conf/users.auth.php
		 * Write conf/acl.auth.php
		 */


		$oldex = Error_Reporter::exception_upgrade(Error_Reporter::E_ERROR);
		$approot = $this->getAppRoot($hostname, $path);
//		try {
//			$this->downloadVersion($approot, $args['version']);
//		}


	}

	/**
	 * Get all available DokuWiki versions
	 *
	 * @return array
	 */
	public function get_versions(): array
	{
		$versions = $this->_getVersions();

		return array_column(array_filter($versions, static function ($meta) {
			return false === strpos($meta['version'], 'A');
		}), 'version');
	}

	/**
	 * Get all current major versions
	 *
	 * @return array
	 */
	private function _getVersions(): array
	{
		$key = 'dokuwiki.versions';
		$cache = Cache_Super_Global::spawn();
//		if (false !== ($ver = $cache->get($key))) {
//			return (array)$ver;
//		}
		$versions = (new Github)->setMode('tags')->fetch('splitbrain/dokuwiki');

		$cache->set($key, $versions, 43200);

		return $versions;
	}

	public function get_version(string $hostname, string $path = ''): ?string
	{
		return '';
		// TODO: Implement get_version() method.
	}

	/**
	 * Location is a valid DokuWiki install
	 *
	 * @param string $hostname or $docroot
	 * @param string $path
	 * @return bool
	 */
	public function valid(string $hostname, string $path = ''): bool
	{
		if ($hostname[0] === '/') {
			$approot = $hostname;
		} else {
			$approot = $this->getAppRoot($hostname, $path);
			if (!$approot) {
				return false;
			}
		}
		return $this->file_exists($approot . '/doku.php');
	}
}