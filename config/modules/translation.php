<?php
// Translation runtime; back up the database and run update.php when enabling on a new installation.
wfLoadExtension( 'UniversalLanguageSelector' );
wfLoadExtension( 'Translate' );
wfLoadExtension( 'ParserFunctions' );
wfLoadExtension( 'InputBox' );
wfLoadExtension( 'TemplateData' );
wfLoadExtension( 'NordNotes', dirname( __DIR__, 2 ) . '/extensions/NordNotes/extension.json' );
$wgGroupPermissions['sysop']['pagetranslation'] = true;
$wgGroupPermissions['sysop']['translate'] = true;
$wgGroupPermissions['sysop']['translate-manage'] = true;
$wgGroupPermissions['sysop']['translate-groupreview'] = true;
$wgGroupPermissions['sysop']['translate-messagereview'] = true;
$wgGroupPermissions['sysop']['pagelang'] = true;
$wgPageLanguageUseDB = true;
$wgTranslatePageTranslationULS = false;
$wgTranslateWorkflowStates = [
    'new' => [ 'color' => '81a1c1' ],
    'translated' => [ 'color' => '88c0d0' ],
    'proofreading' => [ 'color' => 'ebcb8b' ],
    'reviewed' => [ 'color' => 'a3be8c' ],
];

// Keep the local interface in the configured site language instead of browser detection.
$wgULSLanguageDetection = false;
