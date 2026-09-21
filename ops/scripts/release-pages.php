<?php
/** Publish a current-content snapshot using normal revisions; never import local history. */
require dirname(__DIR__, 2) . '/public/w/maintenance/Maintenance.php';
class PublishReleasePages extends MediaWiki\Maintenance\Maintenance {
 public function __construct(){parent::__construct();$this->addOption('manifest','Current-content JSON',true,true);$this->addOption('user','Existing administrator',true,true);}
 public function execute(){
  $services=$this->getServiceContainer();$user=$services->getUserFactory()->newFromName($this->getOption('user'));
  if(!$user||!$user->isRegistered())throw new RuntimeException('An existing user is required');
  $pages=json_decode(file_get_contents($this->getOption('manifest')),true,512,JSON_THROW_ON_ERROR);
  foreach($pages as $item){$title=MediaWiki\Title\Title::newFromText($item['title']);$page=$services->getWikiPageFactory()->newFromTitle($title);$updater=$page->newPageUpdater($user);$updater->setContent(MediaWiki\Revision\SlotRecord::MAIN,MediaWiki\Content\ContentHandler::makeContent($item['text'],$title));$updater->saveRevision(MediaWiki\CommentStore\CommentStoreComment::newUnsavedComment('Publish FuuraikoWiki 1.0.0'),0);if(!$updater->getStatus()->isOK())throw new RuntimeException('Failed to save '.$item['title']);$this->output('Saved '.$item['title']."\n");}
 }
}
$maintClass=PublishReleasePages::class;require RUN_MAINTENANCE_IF_MAIN;
