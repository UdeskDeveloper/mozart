<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bridge\WordPress;

/**
 * Class Theme
 *
 * @package Mozart\Bridge\WordPress
 */
class Theme
{
	/**
	 * @var string
	 */
	private $theme = '';

	/**
	 * @var string
	 */
	private $themeRoot = '';

    /**
     * @var array
     */
    private $themeDirectories = array();
	/**
	 * @var array
	 */
	private $options;

    /**
     * @param array $themeDirectories
     * @param array $options
     */
    public function __construct(array $themeDirectories, array $options = array())
	{
		$this->themeDirectories = $themeDirectories;
		$this->options = $options;
	}

    /**
     * @return mixed|void
     */
    public function getTemplateDirectoryPath()
	{
		$theme_root = $this->getThemeRoot( $this->theme );
		$template_dir = "$theme_root/{$this->theme}";

		return apply_filters( 'template_directory', $template_dir, $this->theme, $theme_root );
	}

	/**
	 * Retrieve path to themes directory.
	 * Does not have trailing slash.
	 *
	 * @param string $stylesheet_or_template The stylesheet or template name of the theme
	 *
	 * @return string Theme path.
	 */
	public function getThemeRoot($stylesheet_or_template = false)
	{
		if ($stylesheet_or_template && $this->themeRoot = $this->getRawThemeRoot( $stylesheet_or_template )) {
			// Always prepend WP_CONTENT_DIR unless the root currently registered as a theme directory.
			// This gives relative theme roots the benefit of the doubt when things go haywire.
			if (!in_array( $this->themeRoot, $this->themeDirectories )) {
				$this->themeRoot = WP_CONTENT_DIR . $this->themeRoot;
			}
		} else {
			$this->themeRoot = WP_CONTENT_DIR . '/themes';
		}

		return apply_filters( 'theme_root', $this->themeRoot );
	}

	/**
	 * @param string $themeRoot
	 */
	public function setThemeRoot($themeRoot)
	{
		$this->themeRoot = $themeRoot;
	}

	/**
	 * Get the raw theme root relative to the content directory with no filters applied.
	 *
	 * @param string $stylesheet_or_template The stylesheet or template name of the theme
	 * @param bool   $skip_cache             Optional. Whether to skip the cache. Defaults to false, meaning the cache
	 *                                       is used.
	 *
	 * @return string Theme root
	 */
	public function getRawThemeRoot($stylesheet_or_template, $skip_cache = false)
	{
		if (count( $this->themeDirectories ) <= 1) {
			return '/themes';
		}

		$theme_root = false;

		// If requesting the root for the current theme, consult options to avoid calling get_theme_roots()
		if (!$skip_cache) {
			if (get_option( 'stylesheet' ) == $stylesheet_or_template) {
				$theme_root = get_option( 'stylesheet_root' );
			} elseif (get_option( 'template' ) == $stylesheet_or_template) {
				$theme_root = get_option( 'template_root' );
			}
		}

		if (empty( $theme_root )) {
			$theme_roots = $this->getThemeRoots();
			if (false === empty( $theme_roots[$stylesheet_or_template] )) {
				$theme_root = $theme_roots[$stylesheet_or_template];
			}
		}

		return $theme_root;
	}

	/**
	 * Retrieve theme roots.
	 *
	 * @return array|string An array of theme roots keyed by template/stylesheet or a single theme root if all themes
	 *                      have the same root.
	 */
	public function getThemeRoots()
	{
		if (count( $this->themeDirectories ) <= 1) {
			return '/themes';
		}

		$theme_roots = get_site_transient( 'theme_roots' );
		if (false === $theme_roots) {
			$this->searchThemeDirectories( true ); // Regenerate the transient.
			$theme_roots = get_site_transient( 'theme_roots' );
		}

		return $theme_roots;
	}

	/**
	 * Search all registered theme directories for complete and valid themes.
	 *
	 * @param bool $force Optional. Whether to force a new directory scan. Defaults to false.
	 *
	 * @return array Valid themes found
	 */
	public function searchThemeDirectories($force = false)
	{
		if (empty( $this->themeDirectories )) {
			return false;
		}

		static $found_themes;
		if (!$force && isset( $found_themes )) {
			return $found_themes;
		}

		$found_themes = array();

		// Set up maybe-relative, maybe-absolute array of theme directories.
		// We always want to return absolute, but we need to cache relative
		// to use in get_theme_root().
		foreach ($this->themeDirectories as $theme_root) {
			if (0 === strpos( $theme_root, WP_CONTENT_DIR )) {
				$relative_theme_roots[str_replace( WP_CONTENT_DIR, '', $theme_root )] = $theme_root;
			} else {
				$relative_theme_roots[$theme_root] = $theme_root;
			}
		}

		/**
		 * Filter whether to get the cache of the registered theme directories.
		 *
		 * @param bool   $cache_expiration Whether to get the cache of the theme directories. Default false.
		 * @param string $cache_directory  Directory to be searched for the cache.
		 */
		if ($cache_expiration = apply_filters( 'wp_cache_themes_persistently', false, 'search_theme_directories' )) {
			$cached_roots = get_site_transient( 'theme_roots' );
			if (is_array( $cached_roots )) {
				foreach ($cached_roots as $theme_dir => $theme_root) {
					// A cached theme root is no longer around, so skip it.
					if (!isset( $relative_theme_roots[$theme_root] )) {
						continue;
					}
					$found_themes[$theme_dir] = array(
						'theme_file' => $theme_dir . '/style.css',
						'theme_root' => $relative_theme_roots[$theme_root], // Convert relative to absolute.
					);
				}

				return $found_themes;
			}
			if (!is_int( $cache_expiration )) {
				$cache_expiration = 1800;
			} // half hour
		} else {
			$cache_expiration = 1800; // half hour
		}

		/* Loop the registered theme directories and extract all themes */
		foreach ($this->themeDirectories as $theme_root) {

			// Start with directories in the root of the current theme directory.
			$dirs = @ scandir( $theme_root );
			if (!$dirs) {
				trigger_error( "$theme_root is not readable", E_USER_NOTICE );
				continue;
			}
			foreach ($dirs as $dir) {
				if (!is_dir( $theme_root . '/' . $dir ) || $dir[0] == '.' || $dir == 'CVS') {
					continue;
				}
				if (file_exists( $theme_root . '/' . $dir . '/style.css' )) {
					// wp-content/themes/a-single-theme
					// wp-content/themes is $theme_root, a-single-theme is $dir
					$found_themes[$dir] = array(
						'theme_file' => $dir . '/style.css',
						'theme_root' => $theme_root,
					);
				} else {
					$found_theme = false;
					// wp-content/themes/a-folder-of-themes/*
					// wp-content/themes is $theme_root, a-folder-of-themes is $dir, then themes are $sub_dirs
					$sub_dirs = @ scandir( $theme_root . '/' . $dir );
					if (!$sub_dirs) {
						trigger_error( "$theme_root/$dir is not readable", E_USER_NOTICE );
						continue;
					}
					foreach ($sub_dirs as $sub_dir) {
						if (!is_dir( $theme_root . '/' . $dir . '/' . $sub_dir ) || $dir[0] == '.' || $dir == 'CVS') {
							continue;
						}
						if (!file_exists( $theme_root . '/' . $dir . '/' . $sub_dir . '/style.css' )) {
							continue;
						}
						$found_themes[$dir . '/' . $sub_dir] = array(
							'theme_file' => $dir . '/' . $sub_dir . '/style.css',
							'theme_root' => $theme_root,
						);
						$found_theme = true;
					}
					// Never mind the above, it's just a theme missing a style.css.
					// Return it; WP_Theme will catch the error.
					if (!$found_theme) {
						$found_themes[$dir] = array(
							'theme_file' => $dir . '/style.css',
							'theme_root' => $theme_root,
						);
					}
				}
			}
		}

		asort( $found_themes );

		$theme_roots = array();
		$relative_theme_roots = array_flip( $relative_theme_roots );

		foreach ($found_themes as $theme_dir => $theme_data) {
			$theme_roots[$theme_dir] = $relative_theme_roots[$theme_data['theme_root']]; // Convert absolute to relative.
		}

		if ($theme_roots != get_site_transient( 'theme_roots' )) {
			set_site_transient( 'theme_roots', $theme_roots, $cache_expiration );
		}

		return $found_themes;
	}

    /**
     * @return string
     */
    public function getTemplate()
	{
		return $this->theme;
	}

	/**
	 * @return string
	 */
	public function getTheme()
	{
		if (true === empty( $this->theme )) {
			$this->theme = apply_filters( 'template', get_option( 'template' ) );
		}

		return $this->theme;
	}

	/**
	 * @param string $theme
	 */
	public function setTheme($theme)
	{
		$this->theme = $theme;
	}

	/**
	 * @return mixed
	 */
	public function getThemeDirectories()
	{
		return $this->themeDirectories;
	}

	/**
	 * @param mixed $themeDirectories
	 */
	public function setThemeDirectories($themeDirectories)
	{
		$this->themeDirectories = $themeDirectories;
	}

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }
} 