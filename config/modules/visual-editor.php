<?php
// VisualEditor bundled with MediaWiki 1.46; uses the bundled Parsoid service.
wfLoadExtension( 'VisualEditor' );
$wgVisualEditorUseSingleEditTab = false;
$wgVisualEditorSkinToolbarScrollOffset['nord'] = 79;

// Enable visual editing for authored help and project-policy pages.
$wgVisualEditorAvailableNamespaces['Help'] = true;
$wgVisualEditorAvailableNamespaces['Project'] = true;
