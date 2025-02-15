<?php

namespace WPStaging\Framework\Assets;

use WPStaging\Backup\Job\AbstractJob;
use WPStaging\Core\DTO\Settings;
use WPStaging\Core\WPStaging;
use WPStaging\Framework\Filesystem\Scanning\ScanConst;
use WPStaging\Framework\Security\AccessToken;
use WPStaging\Framework\Security\Nonce;
use WPStaging\Framework\Traits\ResourceTrait;
use WPStaging\Framework\SiteInfo;
use WPStaging\Framework\Analytics\AnalyticsConsent;
use WPStaging\Backup\Task\Tasks\JobBackup\DatabaseBackupTask;
use WPStaging\Framework\Facades\Hooks;

class Assets
{
    use ResourceTrait;

    /**
     * Default admin bar background color for staging site
     * @var string
     */
    const DEFAULT_ADMIN_BAR_BG = "#ff8d00";

    private $accessToken;

    private $settings;

    private $analyticsConsent;

    public function __construct(AccessToken $accessToken, Settings $settings, AnalyticsConsent $analyticsConsent)
    {
        $this->accessToken      = $accessToken;
        $this->settings         = $settings;
        $this->analyticsConsent = $analyticsConsent;
    }

    /**
     * Prepend the URL to the assets to the given file
     *
     * @param string $assetsFile optional
     * @return string
     */
    public function getAssetsUrl($assetsFile = '')
    {
        return WPSTG_PLUGIN_URL . "assets/$assetsFile";
    }

    /**
     * Get the version the given file. Use for caching
     *
     * @param string $assetsFile
     * @param string $assetsVersion use WPStaging::getVersion() instead if not given
     * @return string
     */
    public function getAssetsUrlWithVersion($assetsFile, $assetsVersion = '')
    {
        $url = $this->getAssetsUrl($assetsFile);
        $ver = empty($assetsVersion) ? $this->getAssetsVersion($assetsFile, $assetsVersion) : $assetsVersion;
        return $url . '?v=' . $ver;
    }

    /**
     * Prepend the Path to the assets to the given file
     *
     * @param string $assetsFile optional
     * @return string
     */
    public function getAssetsPath($assetsFile = '')
    {
        return WPSTG_PLUGIN_DIR . "assets/$assetsFile";
    }

    /**
     * Get the version the given file. Use for caching
     *
     * @param string $assetsFile
     * @param string $assetsVersion Optional, use WPStaging::getVersion() instead if not given
     * @return string|int
     */
    public function getAssetsVersion($assetsFile, $assetsVersion = '')
    {
        $filename  = $this->getAssetsPath($assetsFile);
        $filemtime = file_exists($filename) ? @filemtime($filename) : false;

        if ($filemtime !== false) {
            return $filemtime;
        } else {
            return $assetsVersion !== '' ? $assetsVersion : WPStaging::getVersion();
        }
    }

    /**
     * @action admin_enqueue_scripts 100 1
     * @action wp_enqueue_scripts 100 1
     */
    public function enqueueElements($hook)
    {
        $this->loadGlobalAssets($hook);

        // Load this css file on frontend and backend on all pages if current site is a staging site
        if ((new SiteInfo())->isStagingSite()) {
            wp_register_style('wpstg-admin-bar', false);
            wp_enqueue_style('wpstg-admin-bar');
            wp_add_inline_style('wpstg-admin-bar', $this->getStagingAdminBarColor());
        }

        // Load feedback form js file on page plugins.php in free version or in free dev version
        if (!WPStaging::isPro() && $this->isPluginsPage()) {
            $asset = 'js/dist/wpstg-admin-plugins.min.js';
            if ($this->isDebugOrDevMode()) {
                $asset = 'js/dist/wpstg-admin-plugins.js';
            }

            wp_enqueue_script(
                "wpstg-admin-script",
                $this->getAssetsUrl($asset),
                ["jquery"],
                $this->getAssetsVersion($asset),
                false
            );

            $asset = 'css/dist/wpstg-admin-feedback.min.css';
            if ($this->isDebugOrDevMode()) {
                $asset = 'css/dist/wpstg-admin-feedback.css';
            }

            wp_enqueue_style(
                "wpstg-admin-feedback",
                $this->getAssetsUrl($asset),
                [],
                $this->getAssetsVersion($asset)
            );
        }

        // Load js file on page plugins.php for pro version
        if (WPStaging::isPro() && is_admin()) {
            $asset = 'js/dist/pro/wpstg-admin-all-pages.min.js';
            if ($this->isDebugOrDevMode()) {
                $asset = 'js/dist/pro/wpstg-admin-all-pages.js';
            }

            wp_enqueue_script(
                "wpstg-admin-all-pages-script",
                $this->getAssetsUrl($asset),
                ["jquery"],
                $this->getAssetsVersion($asset),
                false
            );

            $asset = 'css/dist/wpstg-admin-all-pages.min.css';
            if ($this->isDebugOrDevMode()) {
                $asset = 'css/dist/wpstg-admin-all-pages.css';
            }

            wp_enqueue_style(
                "wpstg-admin-all-pages-style",
                $this->getAssetsUrl($asset),
                [],
                $this->getAssetsVersion($asset)
            );
        }

        // Load below assets only on wp staging admin pages
        if ($this->isNotWPStagingAdminPage($hook)) {
            return;
        }

        // Load admin js files
        $asset = 'js/dist/wpstg.js';
        wp_enqueue_script(
            "wpstg-common",
            $this->getAssetsUrl($asset),
            ["jquery"],
            $this->getAssetsVersion($asset),
            false
        );

        // Load admin js files
        $asset = 'js/dist/wpstg-admin.min.js';
        if ($this->isDebugOrDevMode()) {
            $asset = 'js/dist/wpstg-admin.js';
        }

        wp_enqueue_script(
            "wpstg-admin-script",
            $this->getAssetsUrl($asset),
            ["wpstg-common", "wpstg-admin-notyf", "wpstg-admin-sweetalerts"],
            $this->getAssetsVersion($asset),
            false
        );

        // Sweet Alert
        $asset = 'js/dist/wpstg-sweetalert2.min.js';
        if ($this->isDebugOrDevMode()) {
            $asset = 'js/dist/wpstg-sweetalert2.js';
        }

        wp_enqueue_script(
            'wpstg-admin-sweetalerts',
            $this->getAssetsUrl($asset),
            [],
            $this->getAssetsVersion($asset),
            true
        );

        $asset = 'css/dist/wpstg-sweetalert2.min.css';
        if ($this->isDebugOrDevMode()) {
            $asset = 'css/dist/wpstg-sweetalert2.css';
        }

        wp_enqueue_style(
            'wpstg-admin-sweetalerts',
            $this->getAssetsUrl($asset),
            [],
            $this->getAssetsVersion($asset)
        );

        // Notyf Toast Notification
        $asset = 'js/vendor/notyf.min.js';
        wp_enqueue_script(
            'wpstg-admin-notyf',
            $this->getAssetsUrl($asset),
            [],
            $this->getAssetsVersion($asset),
            true
        );

        $asset = 'css/vendor/notyf.min.css';
        wp_enqueue_style(
            'wpstg-admin-notyf',
            $this->getAssetsUrl($asset),
            [],
            $this->getAssetsVersion($asset)
        );

        // Internal hook to enqueue backup scripts, used by the backup addon
        do_action('wpstg_enqueue_backup_scripts', $this->isDebugOrDevMode());

        // Load admin js pro files
        if (defined('WPSTGPRO_VERSION')) {
            $asset = 'js/dist/pro/wpstg-admin-pro.min.js';
            if ($this->isDebugOrDevMode()) {
                $asset = 'js/dist/pro/wpstg-admin-pro.js';
            }

            wp_enqueue_script(
                "wpstg-admin-pro-script",
                $this->getAssetsUrl($asset),
                ["jquery", "wpstg-admin-notyf", "wpstg-admin-sweetalerts"],
                $this->getAssetsVersion($asset),
                false
            );
        }

        // Load admin css files
        $asset = 'css/dist/wpstg-admin.min.css';
        if ($this->isDebugOrDevMode()) {
            $asset = 'css/dist/wpstg-admin.css';
        }

        wp_enqueue_style(
            "wpstg-admin",
            $this->getAssetsUrl($asset),
            [],
            $this->getAssetsVersion($asset)
        );

        $wpstgConfig = [
            "delayReq"               => 0,
            // TODO: move directorySeparator to consts?
            "settings"               => (object)[
                "directorySeparator" => ScanConst::DIRECTORIES_SEPARATOR
            ],
            "tblprefix"              => WPStaging::getTablePrefix(),
            "isMultisite"            => is_multisite(),
            AccessToken::REQUEST_KEY => (string)$this->accessToken->getToken() ?: (string)$this->accessToken->generateNewToken(),
            'nonce'                  => wp_create_nonce(Nonce::WPSTG_NONCE),
            'assetsUrl'              => $this->getAssetsUrl(),
            'ajaxUrl'                => admin_url('admin-ajax.php'),
            'wpstgIcon'              => $this->getAssetsUrl('img/wpstg-loader.gif'),
            'maxUploadChunkSize'     => $this->getMaxUploadChunkSize(),
            'backupDBExtension'      => DatabaseBackupTask::PART_IDENTIFIER . '.' . DatabaseBackupTask::FILE_FORMAT,
            'analyticsConsentAllow'  => esc_url($this->analyticsConsent->getConsentLink(true)),
            'analyticsConsentDeny'   => esc_url($this->analyticsConsent->getConsentLink(false)),
            'isPro'                  => WPStaging::isPro(),
            'maxFailedRetries'       => Hooks::applyFilters(AbstractJob::TEST_FILTER_MAXIMUM_RETRIES, 10),
            // TODO: handle i18n translations through Class/Service Provider?
            'i18n'                   => [
                'dbConnectionSuccess'   => esc_html__('Database connection successful', 'wp-staging'),
                'dbConnectionFailed'    => esc_html__('Database connection failed', 'wp-staging'),
                'somethingWentWrong'    => esc_html__('Something went wrong.', 'wp-staging'),
                'noRestoreFileFound'    => esc_html__('No backup file found.', 'wp-staging'),
                'selectFileToRestore'   => esc_html__('Select backup file to restore.', 'wp-staging'),
                'cloneResetComplete'    => esc_html__('Reset Complete!', 'wp-staging'),
                'cloneUpdateComplete'   => esc_html__('Update Complete!', 'wp-staging'),
                'success'               => esc_html__('Success', 'wp-staging'),
                'resetClone'            => esc_html__('Reset Staging Site', 'wp-staging'),
                'showLogs'              => esc_html__('Show Logs', 'wp-staging'),
                'hideLogs'              => esc_html__('Hide Logs', 'wp-staging'),
                'noTableSelected'       => esc_html__('No table selected', 'wp-staging'),
                'tablesSelected'        => esc_html__('{d} tables(s) selected', 'wp-staging'),
                'noFileSelected'        => esc_html__('No file selected', 'wp-staging'),
                'filesSelected'         => esc_html__('{t} theme(s), {p} plugin(s) selected', 'wp-staging'),
                'wpstg_cloning'         => [
                    'title' => esc_html__('Staging Site Created Successfully!', 'wp-staging'),
                    'body'  => esc_html__('You can access it from here:', 'wp-staging'),
                ],
                'wpstg_update'          => [
                    'title' => esc_html__('Staging Site Updated Successfully!', 'wp-staging'),
                    'body'  => esc_html__('You can access it from here:', 'wp-staging'),
                ],
                'wpstg_push_processing' => [
                    'title' => esc_html__('Staging Site Pushed Successfully!', 'wp-staging'),
                    'body'  => esc_html__('Now delete the theme and the website cache if the website does not look as expected! ', 'wp-staging'),
                ],
                'wpstg_reset'           => [
                    'title' => esc_html__('Staging Site Reset Successfully!', 'wp-staging'),
                    'body'  => esc_html__('You can access it from here:', 'wp-staging'),
                ],
                'wpstg_delete_clone'    => [
                    'title' => esc_html__('Staging Site Deleted Successfully!', 'wp-staging'),
                ],
            ],
        ];

        // We need some wpstgConfig vars in the wpstg.js file (loaded with wpstg-common scripts) as well
        wp_localize_script("wpstg-common", "wpstg", $wpstgConfig);
    }

    /**
     * Load js vars globally but NOT on wp staging admin pages
     * @return void
     */
    private function loadGlobalAssets($pageSlug)
    {
        if (!$this->isNotWPStagingAdminPage($pageSlug)) {
            return;
        }

        wp_enqueue_script('wpstg-global', $this->getAssetsUrl('js/dist/wpstg-blank-loader.js'), [], [], false);

        $vars = [
            'nonce' => wp_create_nonce(Nonce::WPSTG_NONCE),
        ];

        wp_localize_script("wpstg-global", "wpstg", $vars);
    }

    /**
     * @return int The max upload size for a file.
     */
    protected function getMaxUploadChunkSize()
    {
        $lowerLimit = 64 * KB_IN_BYTES;
        $upperLimit = 16 * MB_IN_BYTES;

        $maxPostSize       = wp_convert_hr_to_bytes(ini_get('post_max_size'));
        $uploadMaxFileSize = wp_convert_hr_to_bytes(ini_get('upload_max_filesize'));

        // The real limit, read from the PHP context.
        $limit = min($maxPostSize, $uploadMaxFileSize) * 0.90;

        // Do not allow going over upper limit.
        $limit = min($limit, $upperLimit);

        // Do not allow going under lower limit.
        $limit = max($lowerLimit, $limit);

        return (int)$limit;
    }

    /**
     * Load css and js files only on wp staging admin pages
     *
     * @param $page string slug of the current page
     *
     * @return bool
     */
    private function isNotWPStagingAdminPage($page)
    {
        if (defined('WPSTGPRO_VERSION')) {
            $availablePages = [
                "toplevel_page_wpstg_clone",
                "toplevel_page_wpstg_backup",
                "wp-staging-pro_page_wpstg_clone",
                "wp-staging-pro_page_wpstg_backup",
                "wp-staging-pro_page_wpstg-settings",
                "wp-staging-pro_page_wpstg-tools",
                "wp-staging-pro_page_wpstg-license",
                "wp-staging-pro_page_wpstg-restorer",
            ];
        } else {
            $availablePages = [
                "toplevel_page_wpstg_clone",
                "toplevel_page_wpstg_backup",
                "wp-staging_page_wpstg_clone",
                "wp-staging_page_wpstg_backup",
                "wp-staging_page_wpstg-settings",
                "wp-staging_page_wpstg-tools",
                "wp-staging_page_wpstg-welcome",
            ];
        }

        return !in_array($page, $availablePages) || !is_admin();
    }

    /**
     * Remove heartbeat api and user login check
     *
     * @action admin_enqueue_scripts 100 1
     * @see AssetServiceProvider.php
     *
     * @param bool $hook
     */
    public function removeWPCoreJs($hook)
    {
        if ($this->isNotWPStagingAdminPage($hook)) {
            return;
        }

        // Disable user login status check
        // Todo: Can we remove this now that we have AccessToken?
        remove_action('admin_enqueue_scripts', 'wp_auth_check_load');

        // Disable heartbeat check for cloning and pushing
        wp_deregister_script('heartbeat');
    }

    /**
     * Check if current page is plugins.php
     * @global array $pagenow
     * @return bool
     */
    private function isPluginsPage()
    {
        global $pagenow;

        return ($pagenow === 'plugins.php');
    }

    /**
     * @return string
     */
    public function getStagingAdminBarColor()
    {
        $barColor = $this->settings->getAdminBarColor();
        if (!preg_match("/#([a-f0-9]{3}){1,2}\b/i", $barColor)) {
            $barColor = self::DEFAULT_ADMIN_BAR_BG;
        }

        return "#wpadminbar { background-color: {$barColor} !important; }";
    }

    /**
     * Check whether app is in debug mode or in dev mode
     *
     * @return bool
     */
    private function isDebugOrDevMode()
    {
        return ($this->settings->isDebugMode() || (defined('WPSTG_DEV') && WPSTG_DEV === true) || (defined('WPSTG_DEBUG') && WPSTG_DEBUG === true));
    }
}
