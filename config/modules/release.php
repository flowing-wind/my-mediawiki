<?php
// FuuraikoWiki 1.0.0 runtime. Accounts, credentials and URLs stay in private LocalSettings.php.
$wgLanguageCode = 'en';
$wgEnableUploads = true;
$wgFileExtensions[] = 'svg';
$wgSVGConverter = 'ImageMagick';
$wgSVGConverterPath = '/usr/bin';
wfLoadExtension( 'Cite' );
wfLoadExtension( 'Math' );
$wgMathValidModes = [ 'native' ];
$wgDefaultUserOptions['math'] = 'native';
require __DIR__ . '/site-branding.php';
require __DIR__ . '/site-policy.php';
require __DIR__ . '/visual-editor.php';
require __DIR__ . '/translation.php';
require __DIR__ . '/category-sorting.php';
