<?php
use MediaWiki\Html\Html;
use MediaWiki\Title\Title;
use MediaWiki\Extension\Translate\PageTranslation\TranslatablePage;
use MediaWiki\Extension\Translate\Statistics\MessageGroupStats;
use MediaWiki\Extension\Translate\Services;

class NordNotesHooks {
    public static function onParserFirstCallInit( $parser ) {
        $parser->setHook( 'nordlanguages', [ self::class, 'renderLanguages' ] );
    }
    public static function onBeforePageDisplay( $out, $skin ) {
        if ( $skin->getSkinName() === 'nord' ) {
            $out->addModuleStyles( 'ext.nordNotes' );
            if ( $out->getTitle()->isMainPage() ) { $out->addModules( 'ext.nordNotes.dashboard' ); }
            if ( $out->getTitle()->isSpecial( 'Search' ) ) { $out->addModules( 'ext.nordNotes.discovery' ); }
            if ( $out->getTitle()->getNamespace() === NS_MAIN && TranslatablePage::getTranslationPageFromTitle( $out->getTitle() ) ) {
                $out->addBodyClasses( 'nord-translation-page' );
            }
        }
    }
    public static function onEditFormPreloadText( &$text, $title ) {
        if ( $text !== '' || $title->getNamespace() !== NS_MAIN || $title->exists() || str_contains( $title->getText(), '/' ) ) { return; }
        $request = \RequestContext::getMain()->getRequest();
        $starter = Title::newFromText( $request->getVal( 'nordmode' ) === 'related' ? 'Template:Translate article starter' : 'Template:Article starter' );
        $revision = \MediaWiki\MediaWikiServices::getInstance()->getRevisionLookup()->getRevisionByTitle( $starter );
        if ( $revision ) {
            $text = $revision->getContent( 'main' )->getText();
            $collection = $request->getText( 'nordcollection' );
            if ( in_array( $collection, [ 'Electronics', 'Astronomy', 'Library' ], true ) ) { $text .= "\n[[Category:$collection]]\n"; }
        }
    }
    public static function onPageSaveComplete( $wikiPage, $user, $summary, $flags, $revision, $editResult ) {
        if ( $wikiPage->getTitle()->getNamespace() !== NS_TRANSLATIONS || $editResult->isNullEdit() ) { return; }
        $handle = new \MediaWiki\Extension\Translate\MessageLoading\MessageHandle( $wikiPage->getTitle() );
        if ( !$handle->isValid() ) { return; }
        $group = $handle->getGroup();
        $store = Services::getInstance()->getMessageGroupReviewStore();
        if ( $store->getState( $group, $handle->getCode() ) === 'reviewed' ) {
            $editor = \MediaWiki\MediaWikiServices::getInstance()->getUserFactory()->newFromUserIdentity( $user );
            $store->changeState( $group, $handle->getCode(), 'proofreading', $editor );
        }
    }
    private static function link( string $page, string $label, array $attributes = [] ): string {
        $title = Title::newFromText( $page );
        return $title ? Html::element( 'a', [ 'href' => $title->getLocalURL() ] + $attributes, $label ) : Html::element( 'span', [], $label );
    }
    /** Semicolon-separated language:value pairs; values may contain namespace colons. */
    private static function languageMap( string $value ): array {
        $map = [];
        foreach ( array_filter( explode( ';', $value ) ) as $entry ) {
            $pair = explode( ':', $entry, 2 );
            if ( count( $pair ) !== 2 ) {
                throw new \InvalidArgumentException( 'Use language:value pairs separated by semicolons.' );
            }
            $map[trim( $pair[0] )] = trim( $pair[1] );
        }
        return $map;
    }
    public static function renderLanguages( $input, array $args, $parser, $frame ) {
        $mode = $args['mode'] ?? 'original';
        if ( $mode === 'translate' ) { $mode = 'related'; } // Existing article templates.
        if ( !in_array( $mode, [ 'original', 'independent', 'related' ], true ) ) {
            return Html::element( 'span', [ 'class' => 'error' ], 'Article languages: mode must be original, related or independent.' );
        }
        $current = $parser->getTitle();
        $source = $args['source'] ?? 'zh-hans';
        $root = $current;
        $page = null;
        $group = null;
        if ( $mode === 'related' ) {
            $parser->getOutput()->updateCacheExpiry( 0 );
            $root = Title::newFromText( $args['page'] ?? $current->getPrefixedText() );
            if ( !$root ) { return Html::element( 'span', [ 'class' => 'error' ], 'Article languages: invalid source page.' ); }
            if ( $root->exists() ) {
                $page = TranslatablePage::newFromTitle( $root );
                $group = $page->getMessageGroup();
                $source = $page->getSourceLanguageCode();
            }
        }
        $links = [];
        $states = [];
        if ( $mode === 'independent' ) {
            $root = Title::newFromText( $args['page'] ?? $current->getPrefixedText() );
            if ( !$root ) { return Html::element( 'span', [ 'class' => 'error' ], 'Article languages: invalid source page.' ); }
            try {
                $links = self::languageMap( $args['links'] ?? '' );
                $states = self::languageMap( $args['states'] ?? '' );
            } catch ( \InvalidArgumentException $e ) {
                return Html::element( 'span', [ 'class' => 'error' ], $e->getMessage() );
            }
            if ( !empty( $args['other'] ) ) { $links[$args['other-language'] ?? 'en'] = $args['other']; }
        }
        // Article language, independent of the visitor's interface language.
        $displayLanguage = $mode === 'related' ? $parser->getTargetLanguage()->getCode() : $source;
        if ( $mode === 'independent' ) {
            foreach ( $links as $language => $target ) {
                $targetTitle = Title::newFromText( $target );
                if ( $targetTitle && $targetTitle->equals( $current ) ) { $displayLanguage = $language; }
            }
        }
        $msg = static fn( $key ) => wfMessage( 'nordnotes-language-' . $key )->inLanguage( $displayLanguage )->text();
        $names = [ 'zh-hans' => '简体中文', 'en' => 'English', 'ja' => '日本語' ];
        $languageNames = \MediaWiki\MediaWikiServices::getInstance()->getLanguageNameUtils();
        $name = static fn( $code ) => $names[$code] ?? $languageNames->getLanguageName( $code );
        $chip = static function ( string $target, string $language, string $state = '', string $detail = '' ) use ( $current, $name, $msg, $mode, $source, $displayLanguage ) {
            $title = Title::newFromText( $target );
            $active = ( $title && $title->equals( $current ) ) || ( $mode === 'related' && $language === $source && $displayLanguage === $source );
            $attributes = [ 'hreflang' => $language, 'lang' => $language ];
            if ( $active ) { $attributes['aria-current'] = 'page'; }
            $html = self::link( $target, $name( $language ), $attributes );
            if ( $state !== '' ) {
                $html .= Html::element( 'span', [ 'class' => 'nord-language-state', 'title' => $detail ], ' · ' . $msg( $state ) );
            }
            return Html::rawElement( 'span', [ 'class' => 'nord-language-status' . ( $active ? ' is-current' : '' ), 'data-status' => $state ], $html );
        };
        $parts = [ Html::rawElement( 'span', [ 'class' => 'nord-language-origin' ],
            Html::element( 'span', [ 'class' => 'nord-language-kind' ], $msg( 'original' ) ) . $chip( $root->getPrefixedText(), $source ) ) ];
        if ( $mode === 'original' ) {
            $parts[] = Html::element( 'span', [ 'class' => 'nord-language-kind' ], $msg( 'none' ) );
        } elseif ( $mode === 'independent' ) {
            $parts[] = Html::element( 'span', [ 'class' => 'nord-language-kind', 'title' => $msg( 'independent-help' ) ], $msg( 'independent' ) );
            foreach ( $links as $language => $target ) {
                if ( $language === $source ) { continue; }
                $state = $states[$language] ?? 'unassessed';
                if ( !in_array( $state, [ 'translating', 'translated', 'proofreading', 'reviewed', 'unassessed' ], true ) ) {
                    return Html::element( 'span', [ 'class' => 'error' ], 'Article languages: invalid independent status.' );
                }
                $parts[] = $chip( $target, $language, $state, $msg( 'manual-status' ) );
            }
        } else {
            $parts[] = Html::element( 'span', [ 'class' => 'nord-language-kind', 'title' => $msg( 'related-help' ) ], $msg( 'related' ) );
            if ( !$group ) {
                $parts[] = Html::element( 'span', [], $msg( 'unmarked' ) );
            } else {
                $stats = MessageGroupStats::forGroup( $group->getId() );
                $targets = array_filter( array_map( 'trim', explode( ',', $args['targets'] ?? '' ) ) );
                // Discover every existing translated language, including ones added after the template was written.
                foreach ( $page->getTranslationPages() as $translationTitle ) {
                    $targets[] = substr( $translationTitle->getText(), strrpos( $translationTitle->getText(), '/' ) + 1 );
                }
                $targets = array_values( array_diff( array_unique( $targets ), [ $source ] ) );
                foreach ( $targets as $language ) {
                    $s = $stats[$language] ?? [];
                    $total = $s[MessageGroupStats::TOTAL] ?? 0;
                    $done = $s[MessageGroupStats::TRANSLATED] ?? 0;
                    $fuzzy = $s[MessageGroupStats::FUZZY] ?? 0;
                    $review = Services::getInstance()->getMessageGroupReviewStore()->getState( $group, $language );
                    $state = $total && $done >= $total ? 'translated' : 'translating';
                    if ( $state === 'translated' && in_array( $review, [ 'proofreading', 'reviewed' ], true ) ) { $state = $review; }
                    if ( $fuzzy ) { $state = 'outdated'; }
                    $parts[] = $chip( $root->getPrefixedText() . '/' . $language, $language, $state, "$done/$total" );
                }
                $parts[] = Html::element( 'a', [ 'class' => 'nord-language-manage', 'href' => Title::newFromText( 'Special:Translate' )->getLocalURL( [
                    'group' => $group->getId(), 'language' => $displayLanguage !== $source ? $displayLanguage : ( $targets[0] ?? 'zh-hans' ), 'filter' => ''
                ] ) ], $msg( 'manage' ) );
            }
        }
        return Html::rawElement( 'div', [ 'class' => 'nord-language-bar', 'lang' => $displayLanguage, 'dir' => $parser->getTargetLanguage()->getDir(), 'aria-label' => $msg( 'label' ), 'data-mode' => $mode ], implode( '', $parts ) );
    }
}
