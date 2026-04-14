<div align="center">

# NEOVANTAGE Core

**The official companion plugin for the [NEOVANTAGE WordPress theme](https://wordpress.org/themes/neovantage/).**

[![Version](https://img.shields.io/badge/version-2.0.9-blue.svg)](https://github.com/mohsin-rafique/neovantage-core/releases)
[![License](https://img.shields.io/badge/license-GPL--2.0%2B-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)
[![WordPress](https://img.shields.io/badge/WordPress-5.3%2B-21759b.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-8.0%2B-777bb3.svg)](https://www.php.net/)
[![Tested up to](https://img.shields.io/badge/tested%20up%20to-WP%206.9-21759b.svg)](https://wordpress.org/)

[Features](#-features) · [Why Choose It](#-why-choose-it) · [Installation](#-installation) · [Requirements](#-requirements) · [FAQ](#-faq) · [Changelog](#-changelog) · [Hire the Developer](#-hire-the-developer)

</div>

---

## What Is NEOVANTAGE Core?

NEOVANTAGE Core is the official companion plugin for the [NEOVANTAGE WordPress theme](https://wordpress.org/themes/neovantage/). It adds the features that belong in a plugin — post view tracking, enhanced widgets, a one-click demo importer, and Customizer export/import — keeping the theme itself lean, portable, and compliant with WordPress.org guidelines.

> **This plugin is designed exclusively for the NEOVANTAGE theme.** It depends on theme functions and styling that are only present when NEOVANTAGE is active. It will not produce the expected output with any other theme.

---

## ✨ Features

### 📊 Post View Counter

Tracks and displays how many times each post has been viewed — automatically, without configuration. View counts are stored as standard WordPress post meta (`_neovantage_post_views_count`), so they survive theme switches, plugin deactivations, and database migrations.

**No external service. No API key. No bloat.**

### 🗂️ Enhanced Recent Posts Widget

A drop-in upgrade to WordPress's built-in Recent Posts widget. Shows post thumbnails, publication dates, and theme-matched styling — add it to any sidebar or footer column from **Appearance → Widgets**.

### 👤 Author Profile Contact Fields

Adds Email and Dribbble profile fields to WordPress user profiles. Displayed automatically on author archive pages so your writers have a complete, professional presence without installing a separate plugin.

### 🔧 System Status Panel

A single-screen admin panel listing your PHP version, WordPress version, server software, active theme version, and database information. Share a screenshot with support instead of explaining your setup from scratch.

### 📥 One-Click Demo Importer

Imports the full NEOVANTAGE demo — posts, pages, menus, widgets, and all Customizer settings — in a single click. Go from a blank WordPress install to a fully configured demo site in under a minute.

### 💾 Customizer Export / Import

Export your entire WordPress Customizer configuration to a `.json` file. Import it on another site in one step. Perfect for moving settings between staging and production, or backing up your design before a major change.

---

## ✅ Why Choose It

| | NEOVANTAGE Core | Generic alternatives |
|---|---|---|
| **Setup time** | Zero — active on plugin activation | Each feature needs a separate plugin |
| **Theme integration** | Pixel-perfect — built for NEOVANTAGE's layout and CSS | Styling gaps require custom CSS overrides |
| **Post view storage** | Standard WP post meta — fully portable | Some plugins use custom tables or external APIs |
| **Customizer backup** | Native `.json` export/import built in | Requires a third-party migration plugin |
| **Demo content** | One click — menus, widgets, and settings all applied | Manual import, then manual Customizer recreation |
| **Update delivery** | Standard WP update system — no extra steps | N/A |
| **PHP 8.4 support** | Tested clean on PHP 8.0 – 8.4 | Varies by plugin |
| **Code footprint** | Loads only what the theme needs | General-purpose plugins load unused code on every page |
| **Dependencies** | Zero — no APIs, no accounts, no SaaS | Some require API keys or remote services |

---

## 📋 Requirements

| Requirement | Minimum | Recommended |
|-------------|---------|-------------|
| WordPress | 5.3 | 6.9 |
| PHP | 8.0 | 8.4 |
| Theme | NEOVANTAGE (any version) | NEOVANTAGE 2.0.6+ |

---

## 🚀 Installation

### Option 1 — Via the NEOVANTAGE Theme (Recommended)

1. Install and activate the [NEOVANTAGE theme](https://wordpress.org/themes/neovantage/)
2. A notice appears prompting you to install NEOVANTAGE Core
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

Once installed, NEOVANTAGE Core integrates with WordPress's native update system. When a new release is published you will see the standard update notice in:

- **Dashboard → Updates**
- The **Plugins** list row
- The **Admin bar** badge

No manual downloads required — update directly from your WordPress admin, exactly like any plugin hosted on WordPress.org.

---

## 📖 Usage

### Post View Count

Post views are tracked automatically on every single post page load. No configuration required. The theme displays view counts in post meta areas automatically.

To query the count in custom code:

```php
$views = (int) get_post_meta( get_the_ID(), '_neovantage_post_views_count', true );
```

### Demo Importer

1. Go to **NEOVANTAGE → Demo Importer**
2. Click **Import Demo**
3. Wait for the import to complete — menus, widgets, and Customizer settings are all applied automatically

> Run the demo importer on a fresh WordPress installation only. Always back up before importing on an existing site.

### Customizer Export / Import

1. Go to **NEOVANTAGE → Customizer Export/Import**
2. Click **Export** to download your settings as a `.json` file
3. On another site, upload that file using the **Import** button on the same page

---

## ❓ FAQ

**Does this plugin work without the NEOVANTAGE theme?**
No. NEOVANTAGE Core depends on theme functions and styling that are only available when the NEOVANTAGE theme is active. It is not a general-purpose plugin and is not supported outside of NEOVANTAGE.

**Will my post view counts survive a theme switch?**
Yes. View counts are stored as standard WordPress post meta and are not tied to the theme. They persist through theme switches, plugin deactivations, and WordPress updates.

**Can I import demo content on an existing site?**
The demo importer is designed for fresh installations. Running it on an existing site may create duplicate posts, pages, or menus. Always back up first.

**Is this compatible with PHP 8.4?**
Yes. Tested and confirmed clean on PHP 8.0, 8.1, 8.2, 8.3, and 8.4. All deprecated functions have been removed or replaced.

**Where can I get support?**
Post in the [WordPress.org support forum](https://wordpress.org/support/theme/neovantage/) or open an issue on [GitHub](https://github.com/mohsin-rafique/neovantage-core/issues).

---

## 📝 Changelog

### 2.0.9 — 11 April, 2026

- **Fix:** Admin header "Installed" version now reads from the NEOVANTAGE theme directory (`style.css` via `wp_get_theme()`) instead of the plugin's own `NC_VERSION` constant — the displayed version correctly reflects the installed theme version.

### 2.0.8 — 07 April, 2026

- **Fix:** Plugin now boots on `plugins_loaded` hook instead of directly at file load — resolves `_load_textdomain_just_in_time` notice introduced in WordPress 6.7.

### 2.0.7 — 07 April, 2026

- **Fix:** Corrected `GITHUB_OWNER` constant from `PixelsPress` to `mohsin-rafique` — wrong owner caused the GitHub Releases API to return 404, silently blocking all update notifications in Dashboard → Updates, Plugins list, and admin bar.
- **Fix:** Updated plugin `url` and `homepage` in the updater to the correct GitHub repo (`github.com/mohsin-rafique/neovantage-core`).
- **Fix:** Updated doc block API URL reference from `PixelsPress/neovantage-core` to `mohsin-rafique/neovantage-core`.
- **Distribution:** Plugin source code and releases moved to GitHub (`github.com/mohsin-rafique/neovantage-core`). Download URL updated from `downloads.pixelspress.com` to GitHub Releases.
- **Distribution:** Plugin version metadata JSON (`plugins-data.json`) moved from `downloads.pixelspress.com` to GitHub raw content.
- **Improvement:** Plugin URI in plugin header updated to point to GitHub repo instead of `pixelspress.com`.
- **Improvement:** Plugin description updated to accurately reflect all features.
- **Improvement:** `plugins-data.json` added to repo root — single source of truth for version metadata used by the NEOVANTAGE theme's TGM plugin list.
- **Bump:** Version raised to 2.0.7 in plugin header and `NC_VERSION` constants.

### 2.0.6 — 05 April, 2026

- **Compatibility:** Tested up to WordPress 6.9.
- **Compatibility:** Minimum PHP raised to 8.0; confirmed clean on PHP 8.4.
- **Improvement:** Plugin now self-manages update notices via built-in updater — badges appear in Dashboard → Updates, Plugins list, and admin bar.
- **Fix:** Removed deprecated `mysql_get_server_info()` fallback (removed in PHP 7).
- **Fix:** Updated PHP version threshold in System Status from 7.2 to 8.0.
- **Fix:** Replaced hardcoded version string in admin header with live WordPress.org API lookup (cached 12 hours).
- **Fix:** Used `version_compare()` instead of loose string comparison.
- **Fix:** Removed defunct Google+ author contact field (service shut down 2019).
- **Fix:** Updated all `http://` links in System Status to `https://`.

### 2.0.0 — 04 September, 2019

- **Compatibility:** Full Gutenberg block editor compatibility.

### 1.0.8 — June, 2018

- **Fix:** Strip extra paragraph and break tags from shortcode output.

### 1.0.6 — 07 May, 2018

- **Feature:** Post view count tracking and display.

### 1.0.5 — 22 February, 2018

- **Feature:** Additional author profile fields.

### 1.0.4 — 11 November, 2017

- **Fix:** Nonce verification for post format meta boxes.

### 1.0.0 — 28 August, 2016

- Initial release.

---

## 🤝 Contributing

Contributions are welcome. Please open an issue first to discuss what you would like to change.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/my-feature`)
3. Commit your changes (`git commit -m 'Add my feature'`)
4. Push to the branch (`git push origin feature/my-feature`)
5. Open a Pull Request

---

## 💼 Hire the Developer

NEOVANTAGE Core and the NEOVANTAGE theme are built by **Mohsin Rafique**, a WordPress developer and open-source contributor operating as **PixelsPress**.

If this plugin demonstrates the kind of work you are looking for — clean PHP, security-first coding, WordPress best-practice architecture — Mohsin is available for hire.

**What he builds:**
- Custom WordPress themes and companion plugins
- PHP 8.x-compatible WordPress code audits and rewrites
- Performance, security, and WP coding standards reviews
- WordPress plugin and theme development for agencies and product companies

**Get in touch:**
- Website: [pixelspress.com](https://pixelspress.com)
- GitHub: [github.com/mohsin-rafique](https://github.com/mohsin-rafique)
- WordPress.org: [profiles.wordpress.org/mohsinrafique](https://profiles.wordpress.org/mohsinrafique)

---

## 💬 Support

- **WordPress.org forum:** [wordpress.org/support/theme/neovantage](https://wordpress.org/support/theme/neovantage/)
- **GitHub issues:** [github.com/mohsin-rafique/neovantage-core/issues](https://github.com/mohsin-rafique/neovantage-core/issues)
- **Documentation:** [pixelspress.com/documentation](https://pixelspress.com/documentation)

---

## 📄 License

NEOVANTAGE Core — Copyright (C) 2016–2026 [PixelsPress](https://pixelspress.com)

Licensed under the [GNU General Public License v2.0 or later](https://www.gnu.org/licenses/gpl-2.0.html).

This program is free software: you can redistribute it and/or modify it under the terms of the GNU GPL as published by the Free Software Foundation, either version 2 of the License, or (at your option) any later version.

---

<div align="center">

Made with ❤️ by [PixelsPress](https://pixelspress.com) · [mohsin-rafique](https://github.com/mohsin-rafique)

</div>
