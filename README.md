# Genesis Shortcode UI

* Contributors: [David Decker](https://github.com/deckerweb), [contributors](https://github.com/deckerweb/genesis-shortcode-ui/graphs/contributors)
* Tags: shortcode, updated, last updated, date, time, item, post type, custom post types, post, element
* Requires at least: 4.1.0
* Tested up to: 4.6.x
* Stable tag: master
* Donate link: [http://ddwb.me/9s](http://ddwb.me/9s)
* License: GPL-2.0+
* License URI: [http://www.opensource.org/licenses/gpl-license.php](http://www.opensource.org/licenses/gpl-license.php)

Enhance the default Shortcodes of the Genesis Framework with a Shortcode UI powered by the Shortcake plugin.


## Description:

Very comfortably enter the 20 Genesis default Shortcodes in the WordPress Editor via a visual User Interface powered by the Shortcake plugin. The Genesis default Shortcodes were never easier before!

*Backstory:* Since I figured out the Shortcake plugin (aka "Shortcode UI") is a really nice and genius project it was clear I had to do something for Genesis with it. So this is the first result! :-)


## Features:

* Visual Shortcode interface - totally simple and easy! :)
* Developer friendly: customize or extend via filters
* Fully internationalized and translateable! -- German translations already packaged!
* Developed with security in mind: proper WordPress coding standards and security functions - escape all the things! :)


## Plugin Installation:

**Requirements/ Prerequisites**
* [WordPress v4.1.0 or higher](https://wordpress.org/download/)
* [Genesis Framework v2.3.1 or higher](http://deckerweb.de/go/genesis/)
* ["Shortcake" plugin (Shortcode UI) v0.6.2 or higher](https://wordpress.org/plugins/shortcode-ui/screenshots/)

**Manual Upload**
* Download current .zip archive from master branch here, URL: [https://github.com/deckerweb/genesis-shortcode-ui/archive/master.zip](https://github.com/deckerweb/genesis-shortcode-ui/archive/master.zip)
* Unzip the package, then **rename the folder to `genesis-shortcode-ui`**, then upload renamed folder via FTP to your WordPress plugin directory
* Activate the plugin

**Via "GitHub Updater" Plugin** *(recommended!)*

* Install & activate the "GitHub Updater" plugin, get from here: [https://github.com/afragen/github-updater](https://github.com/afragen/github-updater)
* Recommended: set your API Token in the plugin's settings
* Go to "Settings > GitHub Updater > Install Plugin"
* Paste the GitHub URL `https://github.com/deckerweb/genesis-shortcode-ui` in the "Plugin URI" field (branch "master" is pre-set), then hit the "Install Plugin" button there
* Install & activate the plugin

**Updates**
* Are done via the plugin "GitHub Updater" (see above) - leveraging the default WordPress update system!
* Setting your GitHub API Token is recommended! :)
* It's so easy and seamless you won't find any better solution for this ;-)


## Usage - 1) Basics:

* In the edit screen for Posts, Pages, Custom Post types click the button "Add Media" (above the editor window) and then on the left click "Add Post Element"
* This will give you a nice overview list of all available Shortcodes: just choose any of the Genesis ones and change the attributes if necessary
* Then embed it to your post content and you're done!
* Once you want to edit an existing Shortcode, inserted before, just hover over its line in the content editor and you'll get presented with an edit icon (pencil icon): just click it and change the attributes.


## Usage - 2) Advanced:

* Shortcode UI is automatically available where the Visual Editor of WordPress is available including the "Add Media" button
* That means you can use the Shortcodes UI in Page Builders, Widgets with the Visual Editor etc.
* Example Page Builder plugin: ["SiteOrigin Page Builder" (via WordPress.org)](https://wordpress.org/plugins/siteorigin-panels/screenshots/)
* Example Widget plugin: ["Visual Text Editor" (via WordPress.org)](https://wordpress.org/plugins/visual-text-editor/screenshots/)


## Usage - 3) Limitations:

* The "Back to Top" Shortcode from Genesis was left out, intentionally, as it makes no sense to use this XHTML/ HTML 4 feature any longer!
* Also, the attributes `relative_depth` from the Shortcodes "Post Date" and "Post Modified Date" were left out as they make not much sense in an UI aimed at end users/ beginner users.
* Otherwise, the originally "hidden" attribute `url` attribute in Shortcode "Footer Genesis Link" was included as it makes total sense especially for affiliate links! :)


## Plugin Filters (Developers) - 1) Basics:

* `gsui_filter_genesis_label` --> modify the label "Genesis" to anything you want (presented in the Shortcode overview list)
* `gsui_filter_genesis_logo` --> modify the image path for the Genesis logo - absolute path is needed!
* `gsui_filter_shortcode_ui_args_{shortcode_tag}` --> filter arguments for one Shortcode tag for Shortcode UI (Shortcake plugin)
* `gsui_filter_genesis_shortcodes_ui_args` --> filter arguments for any/all Shortcodes for Shortcode UI (Shortcake plugin)


## Plugin Filters - 2) Example

To exclude one of the Genesis default Shortcodes from appearing in the UI, just use this code snippet from **[this Gist https://gist.github.com/deckerweb/65bf8c1c38f95cffcb9e2e2dda344345](https://gist.github.com/deckerweb/65bf8c1c38f95cffcb9e2e2dda344345)**

Just uncommend all those lines with the Shortcode tags you don't want to see in the UI.

In the filter `gsui_filter_genesis_shortcodes_ui_args` place the Genesis Shortcode tag together with the prefix `sp_`, like so:

```
$genesis_shortcodes[ 'sp_footer_studiopress_link' ] = 0;
```


## Translations:

* Used textdomain: `genesis-shortcode-ui`
* Default `.pot` file included
* German translations included (`de_DE`)
* Plugin's own path for translations: `wp-content/plugins/genesis-shortcode-ui/languages/genesis-shortcode-ui-de_DE.mo`
* *Recommended:* Global WordPress lang dir path for translations: `wp-content/languages/plugins/genesis-shortcode-ui-de_DE.mo` ---> *NOTE: if this file/path exists it will be loaded at higher priority than the plugin path! This is the recommended path & way to store your translations as it is update-safe and allows for custom translations!*
* Recommended translation tools: *Poedit Pro v1.8+* or *WordPress Plugin "Loco Translate"* or *your IDE/ Code Editor* or *old WordPress "Codestyling Localization"* (for the brave who know what they are doing :) )


## Changelog:

See plugin file [CHANGES.md here](https://github.com/deckerweb/genesis-shortcode-ui/blob/master/CHANGES.md)

Copyright (c) 2016 David Decker - DECKERWEB.de
