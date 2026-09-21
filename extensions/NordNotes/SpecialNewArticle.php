<?php
use MediaWiki\Html\Html;
use MediaWiki\SpecialPage\SpecialPage;
use MediaWiki\Title\Title;

class SpecialNewArticle extends SpecialPage {
    public function __construct() { parent::__construct( 'NewArticle' ); }
    public function getDescription() { return $this->msg( 'nordnotes-newarticle-title' ); }
    protected function getGroupName() { return 'editing'; }
    public function execute( $par ) {
        $this->setHeaders();
        $out = $this->getOutput();
        $request = $this->getRequest();
        $query = $request->getText( 'newtitle' );
        if ( $request->getCheck( 'create' ) ) {
            $title = Title::newFromText( $query );
            if ( !$title || $title->getNamespace() !== NS_MAIN || str_contains( $title->getText(), '/' ) ) {
                $out->addHTML( Html::element( 'p', [ 'class' => 'error' ], 'Enter an article title without a namespace or language suffix. Create related translations from the source article using Translate.' ) );
            } else {
                $out->redirect( $title->getLocalURL( $title->exists() ? [] : [
                    'action' => 'edit',
                    'nordmode' => $request->getVal( 'nordmode' ) === 'related' ? 'related' : 'original',
                    'nordcollection' => $request->getText( 'nordcollection' )
                ] ) );
                return;
            }
        }
        $out->addModules( 'ext.nordNotes.discovery' );
        $out->addHTML( '<div class="nord-create"><p>Check for an existing article or alias before starting a new page.</p>' .
            Html::openElement( 'form', [ 'method' => 'get', 'action' => $this->getPageTitle()->getLocalURL(), 'id' => 'nord-create-form' ] ) .
            '<label for="nord-new-title">Article title</label>' .
            Html::input( 'newtitle', $query, 'text', [ 'id' => 'nord-new-title', 'required' => true, 'autocomplete' => 'off', 'placeholder' => 'Search titles and aliases…' ] ) .
            '<div id="nord-title-matches" aria-live="polite"></div>' .
            '<div class="nord-create-options"><label>Article type<select name="nordmode"><option value="original">Regular article</option><option value="related">Source article for linked translations</option></select></label>' .
            '<label>Collection<select name="nordcollection"><option value="">Choose later</option><option>Electronics</option><option>Astronomy</option><option>Library</option></select></label></div>' .
            '<button type="submit" name="create" value="1" id="nord-create-button">Open editor</button></form>' .
            '<p>The editor opens with a language bar, article sections, See also and References. Nothing is published until you save. New source articles start in English; change the page content language before marking a different source language for translation.</p>' .
            '<p>To translate an existing article, use its language bar or <a href="' . SpecialPage::getTitleFor( 'Translate' )->getLocalURL() . '">Translate</a>. Do not create a /zh-hans page by hand.</p>' .
            '</div>' );
    }
}
