<?php
// Site content policy. Include after the installer-generated rights settings.
// This does not change MediaWiki, extension or skin software licenses.
$wgRightsPage = 'FuuraikoWiki:Copyrights';
$wgRightsUrl = 'https://creativecommons.org/licenses/by-nc/4.0/';
$wgRightsText = 'CC BY-NC 4.0';
$wgRightsIcon = '';

$wgHooks['SkinAddFooterLinks'][] = static function ( $skin, $key, &$items ) {
    if ( $skin->getSkinName() !== 'nord' ) {
        return;
    }
    if ( $key === 'info' ) {
        // The colophon already provides the license link; retain last-modified metadata.
        $items['copyright'] = '<span class="nord-footer-omit" hidden></span>';
        return;
    }
    if ( $key !== 'places' ) {
        return;
    }
    // Replace the default group with the requested navigation order.
    $items['privacy'] = $items['about'] = $items['disclaimers'] = '<span class="nord-footer-omit" hidden></span>';
    $items['dashboard'] = MediaWiki\Html\Html::element( 'a', [
        'href' => 'https://flowing-wind.space', 'target' => '_blank', 'rel' => 'noopener'
    ], 'Dashboard' );
    $items['about-site'] = MediaWiki\Html\Html::element( 'a', [
        'href' => MediaWiki\Title\Title::newFromText( 'FuuraikoWiki:About' )->getLocalURL()
    ], 'About' );
    $items['rss'] = MediaWiki\Html\Html::element( 'a', [
        'href' => MediaWiki\SpecialPage\SpecialPage::getTitleFor( 'Recentchanges' )->getLocalURL( [ 'feed' => 'rss' ] ),
        'type' => 'application/rss+xml', 'title' => 'Subscribe to wiki recent changes'
    ], 'RSS' );
    foreach ( [
        'copyrights' => [ 'FuuraikoWiki:Copyrights', 'Copyrights' ],
        'disclaimer-policy' => [ 'FuuraikoWiki:Disclaimers', 'Disclaimers' ],
        'privacy-policy' => [ 'FuuraikoWiki:Privacy policy', 'Privacy policy' ],
    ] as $id => [ $title, $label ] ) {
        $items[$id] = MediaWiki\Html\Html::element( 'a', [
            'href' => MediaWiki\Title\Title::newFromText( $title )->getLocalURL()
        ], $label );
    }
};
