<?php

namespace LithiumHosting\WebApps\DokuWiki;

use Module\Support\Webapps\App\Type\Unknown\Handler as Unknown;

class Handler extends Unknown
{
	const NAME       = 'DokuWiki';
	const ADMIN_PATH = "";
	const LINK       = 'https://dokuwiki.org';

	const DEFAULT_FORTIFICATION = 'max';
	const FEAT_ALLOW_SSL        = true;
	const FEAT_RECOVERY         = false;

	public function display(): bool
	{
		return version_compare($this->php_version(), '7', '>=');
	}

	public function hasUpdate(): bool
	{
		return false;
	}
}