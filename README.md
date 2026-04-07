<div align="center">

# NEOVANTAGE Core

**The official companion plugin for the [NEOVANTAGE WordPress theme](https://wordpress.org/themes/neovantage/).**

[![Version](https://img.shields.io/badge/version-2.0.8-blue.svg)](https://github.com/mohsin-rafique/neovantage-core/releases)
[![License](https://img.shields.io/badge/license-GPL--2.0%2B-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)
[![WordPress](https://img.shields.io/badge/WordPress-5.3%2B-21759b.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-8.0%2B-777bb3.svg)](https://www.php.net/)
[![Tested up to](https://img.shields.io/badge/tested%20up%20to-WP%206.9-21759b.svg)](https://wordpress.org/)

[Features](#-features) · [Installation](#-installation) · [Requirements](#-requirements) · [Changelog](#-changelog) · [Support](#-support)

</div>

---

## Overview

NEOVANTAGE Core extends the [NEOVANTAGE WordPress theme](https://wordpress.org/themes/neovantage/) with features that WordPress best practices recommend delivering as a plugin — keeping the theme lightweight and your content data fully portable.

> ⚠️ This plugin is designed exclusively for the NEOVANTAGE theme. It will not function correctly with any other theme.

### Why NEOVANTAGE Core?

- **Zero external dependencies** — no API keys, no third-party services
- **Data portable** — all data stored as standard WordPress post meta and options
- **Lightweight** — loads only what the theme needs, nothing more
- **PHP 8.4 ready** — fully tested on PHP 8.0 through 8.4

---

## ✨ Features

### 📊 Post View Count
Automatically tracks and displays how many times each post has been viewed. Counts are stored directly in your WordPress database as post meta — no external service or API key required. Use them to surface your most popular content anywhere in the theme.

### 🗂️ Enhanced Recent Posts Widget
A drop-in replacement for WordPress's built-in Recent Posts widget with post thumbnails, post dates, and clean theme-matched styling. Add it to any sidebar or footer column from **Appearance → Widgets**.

### 👤 Author Profile Contact Fields
Adds Email and Dribbble profile fields to WordPress user profiles. These fields are displayed automatically on author archive pages, giving your writers a professional public profile without a separate plugin.

### 🔧 System Status Panel
An at-a-glance admin panel showing your PHP version, WordPress version, server software, active theme version, and database information — everything needed for troubleshooting or sharing with support.

### 📥 One-Click Demo Importer
Import the NEOVANTAGE demo content — posts, pages, menus, widgets, and Customizer settings — in a single click. Go from a blank install to a fully configured demo site in under a minute.

### 💾 Customizer Export / Import
Export your entire WordPress Customizer configuration to a `.json` file and import it on another site. Perfect for moving design settings between staging and production, or backing up customization before a major change.

---

## 📋 Requirements

| Requirement | Minimum | Recommended |
|---|---|---|
| WordPress | 5.3 | 6.9 |
| PHP | 8.0 | 8.4 |
| Theme | NEOVANTAGE (any version) | NEOVANTAGE 2.0.6+ |

---

## 🚀 Installation

### Option 1 — Via NEOVANTAGE Theme (Recommended)

1. Install and activate the [NEOVANTAGE theme](https://wordpress.org/themes/neovantage/)
2. A notice will appear prompting you to install NEOVANTAGE Core
3. Go to **Appearance → Install Plugins** (or **NEOVANTAGE → Install Plugins**)
4. Click **Install** next to NEOVANTAGE Core, then **Activate**

### Option 2 — Manual Upload

1. Download `neovantage-core.zip` from the [latest release](https://github.com/mohsin-rafique/neovantage-core/releases/latest)
2. In your WordPress admin go to **Plugins → Add New → Upload Plugin**
3. Choose the zip file and click **Install Now**
4. Click **Activate Plugin**

### Option 3 — FTP / File Manager

1. Download and unzip `neovantage-core.zip`
2. Upload the `neovantage-core/` folder to `/wp-content/plugins/`
3. Go to **Plugins → Installed Plugins** and activate **NEOVANTAGE Core**

---

## 🔄 Automatic Updates

Once installed, NEOVANTAGE Core integrates with WordPress's native update system. When a new version is released you will see the standard update notice in:

- **Dashboard → Updates**
- **Plugins** list row
- **Admin bar** badge

No manual downloads needed — update directly from your WordPress admin.

---

## 📖 Usage

### Post View Count
Post views are tracked automatically on every single post page load. The count is stored as `_neovantage_post_views_count` post meta. The theme displays it in post meta areas automatically.

### Demo Importer
1. Go to **NEOVANTAGE → Demo Importer**
2. Click **Import Demo**
3. Wait for the import to complete — menus, widgets, and Customizer settings are all applied automatically

> ⚠️ Run the demo importer on a fresh WordPress installation only. Always back up before importing on an existing site.

### Customizer Export / Import
1. Go to **NEOVANTAGE → Customizer Export/Import**
2. Click **Export** to download your settings as a `.json` file
3. On another site, upload that file using the **Import** button on the same page

---

## 📝 Changelog

### 2.0.8 — 07 April, 2026
- **Fix:** Plugin now boots on `plugins_loaded` hook instead of directly at file load — resolves `_load_textdomain_just_in_time` notice introduced in WordPress 6.7.

### 2.0.7 — 07 April, 2026
- **Fix:** Corrected `GITHUB_OWNER` constant from `PixelsPress` to `mohsin-rafique` — wrong owner caused the GitHub Releases API to return 404, silently blocking all update notifications in Dashboard → Updates, Plugins list, and admin bar.
- **Fix:** Updated plugin `url` and `homepage` in the updater from `pixelspress.com` to the correct GitHub repo (`github.com/mohsin-rafique/neovantage-core`).
- **Fix:** Updated doc block API URL reference from `PixelsPress/neovantage-core` to `mohsin-rafique/neovantage-core`.
- **Distribution:** Plugin source code and releases moved to GitHub (`github.com/mohsin-rafique/neovantage-core`). Download URL updated from `downloads.pixelspress.com` to GitHub Releases.
- **Distribution:** Plugin version metadata JSON (`plugins-data.json`) moved from `downloads.pixelspress.com` to GitHub raw content.
- **Improvement:** Plugin URI in plugin header updated to point to GitHub repo instead of `pixelspress.com`.
- **Improvement:** Plugin description updated to accurately reflect all features.
- **Improvement:** `plugins-data.json` added to repo root — single source of truth for version metadata used by the NEOVANTAGE theme's TGM plugin list.
- **Bump:** Version raised to 2.0.7 in plugin header, `N_VERSION`, and `NC_VERSION` constants.

### 2.0.6 — 05 April, 2026
- **Compatibility:** Tested up to WordPress 6.9
- **Compatibility:** Minimum PHP raised to 8.0; confirmed clean on PHP 8.4
- **Improvement:** Plugin now self-manages update notices via built-in updater — badges appear in Dashboard → Updates, Plugins list, and admin bar
- **Fix:** Removed deprecated `mysql_get_server_info()` fallback (removed in PHP 7)
- **Fix:** Updated PHP version threshold in System Status from 7.2 to 8.0
- **Fix:** Replaced hardcoded version string in admin header with live WordPress.org API lookup (cached 12 hours)
- **Fix:** Used `version_compare()` instead of loose string comparison
- **Fix:** Removed defunct Google+ author contact field (service shut down 2019)
- **Fix:** Updated all `http://` links in System Status to `https://`

### 2.0.0 — 04 September, 2019
- Full Gutenberg block editor compatibility

### 1.0.6 — 07 May, 2018
- Feature: Post view count tracking and display

### 1.0.0 — 28 August, 2016
- Initial release

---

## 🤝 Contributing

Contributions are welcome. Please open an issue first to discuss what you would like to change.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/my-feature`)
3. Commit your changes (`git commit -m 'Add my feature'`)
4. Push to the branch (`git push origin feature/my-feature`)
5. Open a Pull Request

---

## 💬 Support

- **WordPress.org forum:** [wordpress.org/support/theme/neovantage](https://wordpress.org/support/theme/neovantage/)
- **GitHub issues:** [github.com/mohsin-rafique/neovantage-core/issues](https://github.com/mohsin-rafique/neovantage-core/issues)
- **Documentation:** [pixelspress.com/documentation](https://pixelspress.com/documentation)

---

## 📄 License

Licensed under the [GNU General Public License v2.0 or later](https://www.gnu.org/licenses/gpl-2.0.html).

---

<div align="center">

Made with ❤️ by [PixelsPress](https://pixelspress.com) · [mohsin-rafique](https://github.com/mohsin-rafique)

</div>
