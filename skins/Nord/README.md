# Nord — independent MediaWiki skin

MediaWiki 1.46+, SkinMustache, ResourceLoader. No core patch, no build step, no external fonts or trackers.

Copy this directory to `$IP/skins/Nord`, then:
```php
wfLoadSkin( 'Nord' );
$wgDefaultSkin = 'nord';
```

Switch back by changing the default skin or using `?useskin=minerva`.
Sidebar links come from MediaWiki:Sidebar. Article content and fixture templates are not part of the skin.
Light is the default; dark mode is a per-browser preference. Keyboard / focuses search, Escape closes menus.
Mobile navigation, page actions, account tools, categories, edit/history and print remain native MediaWiki.
Nord palette: https://www.nordtheme.com/docs/colors-and-palettes/ .
Code licensed GPL-2.0-or-later.
