=== NEOVANTAGE Core ===
Contributors: pixelspress, mohsinrafique
Tags: neovantage, blog, post views, widgets, demo importer
Requires at least: 5.3
Tested up to: 6.9
Requires PHP: 8.0
Stable tag: 2.0.9
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

The official companion plugin for the NEOVANTAGE WordPress blog theme. Adds post view counts, enhanced widgets, demo content, and customizer import/export.

== Description ==

**NEOVANTAGE Core** is the official companion plugin for the [NEOVANTAGE WordPress theme](https://wordpress.org/themes/neovantage/). It extends the theme with features that WordPress best practices recommend delivering as a plugin rather than bundling inside a theme — keeping the theme lightweight and your data portable.

= Features =

**Post View Count**
Automatically tracks and displays how many times each post has been viewed. View counts are shown in post meta and can be used to surface your most popular content. No external service or API key required — counts are stored directly in your WordPress database.

**Enhanced Recent Posts Widget**
A drop-in replacement for WordPress's built-in Recent Posts widget — with post thumbnails, post dates, and clean, theme-matched styling. Add it to any sidebar or footer column from **Appearance → Widgets**.

**Author Page Contact Fields**
Adds Email and Dribbble profile fields to WordPress user profiles. These fields are displayed automatically on author archive pages, giving your writers a professional public profile without a separate plugin.

**System Status Panel**
An at-a-glance admin panel showing your PHP version, WordPress version, server software, active theme version, and database information — everything you need for troubleshooting or sharing with support.

**One-Click Demo Importer**
Import the NEOVANTAGE demo content — posts, pages, menus, widgets, and customizer settings — in a single click. Get from a blank install to a fully configured demo site in under a minute.

**Customizer Export / Import**
Export your entire WordPress Customizer configuration to a file and import it on another site. Perfect for moving your design settings between staging and production, or backing up your customization before a major change.

= Designed to Work Together =

NEOVANTAGE Core is purpose-built for the NEOVANTAGE theme. Every feature is designed, tested, and styled to integrate seamlessly with the theme's layout and design system. It is not a general-purpose plugin and is not supported outside of the NEOVANTAGE theme.

= Automatic Update Notices =

Once installed, NEOVANTAGE Core integrates with WordPress's native update system. When a new version is available, you will see the standard update notice in **Dashboard → Updates**, the Plugins list row, and the admin bar — exactly like any plugin hosted on WordPress.org.

== Installation ==

1. Install and activate the [NEOVANTAGE theme](https://wordpress.org/themes/neovantage/) first.
2. NEOVANTAGE will prompt you to install NEOVANTAGE Core from **Appearance → Install Plugins**. Click **Install** and then **Activate**.
3. Alternatively, upload the `neovantage-core` folder to `/wp-content/plugins/` and activate it from **Plugins → Installed Plugins**.
4. Once active, navigate to the **NEOVANTAGE** admin menu to access the demo importer, system status, and customizer import/export.

== Frequently Asked Questions ==

= Does this plugin work without the NEOVANTAGE theme? =

No. NEOVANTAGE Core is a companion plugin built exclusively for the NEOVANTAGE theme. It depends on theme functions and styling that are only available when the NEOVANTAGE theme is active. Installing it with a different theme will not produce the expected output.

= Where can I find documentation? =

Full documentation is available at [pixelspress.com/documentation](https://pixelspress.com/documentation).

= Is this plugin compatible with PHP 8.4? =

Yes. NEOVANTAGE Core 2.0.6 has been fully tested on PHP 8.0, 8.1, 8.2, 8.3, and 8.4. All deprecated functions have been removed or updated.

= Will my post view counts survive a theme switch? =

Yes. Post view counts are stored as WordPress post meta (`_neovantage_post_views_count`) directly in your database. They are not tied to the theme and will persist through theme switches, plugin deactivations, and WordPress updates.

= Can I import the demo content on an existing site? =

The demo importer is designed for use on a fresh WordPress installation. Running it on an existing site with content may result in duplicate posts, pages, or menu items. Always back up your site before importing demo content.

= How do I export my Customizer settings? =

Go to **NEOVANTAGE → Customizer Export/Import** in your admin menu and click **Export**. This downloads a `.json` file containing all your current Customizer settings. To import on another site, go to the same panel and upload the file.

= Where do I get support? =

Post in the [WordPress.org support forum](https://wordpress.org/support/theme/neovantage/) or visit [pixelspress.com/documentation](https://pixelspress.com/documentation).

== Screenshots ==

1. Post view count displayed in post meta on single post pages.
2. Enhanced Recent Posts widget with thumbnail and date.
3. Author profile page with contact fields.
4. System Status admin panel.
5. One-click demo importer.
6. Customizer Export / Import panel.

== Changelog ==

= 2.0.9 - 11 April, 2026 =
* Fix: Admin header "Installed" version now reads from the NEOVANTAGE theme directory (style.css via wp_get_theme()) instead of the plugin's own NC_VERSION constant — the displayed version now correctly reflects the installed theme version.

= 2.0.8 - 07 April, 2026 =
* Fix: Plugin now boots on plugins_loaded hook instead of directly at file load — resolves _load_textdomain_just_in_time notice introduced in WordPress 6.7.

= 2.0.7 - 07 April, 2026 =
* Fix: Corrected GITHUB_OWNER constant from PixelsPress to mohsin-rafique — wrong owner caused the GitHub Releases API to return 404, silently blocking all update notifications.
* Fix: Updated plugin url and homepage in the updater to the correct GitHub repo (github.com/mohsin-rafique/neovantage-core).
* Fix: Updated doc block API URL reference from PixelsPress/neovantage-core to mohsin-rafique/neovantage-core.
* Distribution: Plugin source code and releases moved to GitHub (github.com/mohsin-rafique/neovantage-core). Download URL updated to GitHub Releases.
* Distribution: Plugin version metadata JSON (plugins-data.json) moved to GitHub raw content.
* Improvement: Plugin URI in plugin header updated to point to GitHub repo.
* Improvement: Plugin description updated to accurately reflect all features.
* Improvement: plugins-data.json added to repo root — single source of truth for version metadata used by the NEOVANTAGE theme's TGM plugin list.
* Bump: Version raised to 2.0.7 in plugin header, and NC_VERSION constants.

= 2.0.6 - 05 April, 2026 =
* Compatibility: Tested up to WordPress 6.9.
* Compatibility: Minimum PHP requirement raised to 8.0; confirmed clean on PHP 8.4.
* Improvement: Plugin now self-manages update notices via Neovantage_Core_Updater — update badges appear in Dashboard → Updates, Plugins list, and admin bar without requiring a theme update.
* Fix: Removed dead mysql_get_server_info() fallback from nc_get_server_database_version() — mysql_* functions were removed in PHP 7.
* Fix: Updated PHP version threshold in System Status from 7.2 to 8.0.
* Fix: Replaced hardcoded latest version string in admin header with a live WordPress.org API lookup cached for 12 hours via transient.
* Fix: Used version_compare() for version comparison instead of loose string comparison.
* Fix: Escaped latest version output in admin header printf.
* Fix: Removed untranslatable pipe separator from esc_html_e() call.
* Fix: Removed defunct Google+ author contact field — Google+ was shut down in 2019.
* Fix: Updated all http:// links in System Status to https://.
* Fix: Updated License URI in plugin header to https://.
* Note: $this->version in Neovantage_Core now reads NC_VERSION constant instead of a hardcoded string.

= 2.0.0 - 04 September, 2019 =
* Compatibility: Full Gutenberg block editor compatibility.

= 1.0.8 - June, 2018 =
* Fix: Strip extra paragraph and break tags from shortcode output.

= 1.0.7 - 25 May, 2018 =
* Fix: Content box Font Awesome icon.

= 1.0.6 - 07 May, 2018 =
* Feature: Post view count tracking and display.

= 1.0.5 - 22 February, 2018 =
* Feature: Additional author profile fields.

= 1.0.4 - 11 November, 2017 =
* Fix: Nonce verification for post format meta boxes.

= 1.0.3 - 17 October, 2017 =
* Update: Revised post format structure.

= 1.0.2 - 09 August, 2017 =
* Feature: Knowledge base functionality.

= 1.0.1 - 20 February, 2017 =
* Feature: Button alignment option.
* Fix: Used antispambot() to escape email output.

= 1.0.0 - 28 August, 2016 =
* Initial release.

== Upgrade Notice ==

= 2.0.6 =
PHP 8.4 compatibility release. Adds native WordPress update notices so you no longer need to visit the theme's plugin page to see available updates. Removes all deprecated PHP code. Upgrade recommended for all users.
