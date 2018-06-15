<?
use PortalManager\News;
use PortalManager\Template;
use PortalManager\Pagination;

class hirek extends Controller{
	function __construct(){
		parent::$user_opt = $user_option;
		parent::__construct();

		$this->out( 'bodyclass', 'article' );


		$url = DOMAIN.__CLASS__;
		$image = \PortalManager\Formater::sourceImg($this->view->settings['logo']);
		$title = 'Hírek';
		$description = $this->view->settings['page_title'].' friss hírek. Kövesd oldalunkat és tájékozódj az újdonságokról!';

		$news = new News( false, array( 'db' => $this->db ) );
		$temp = new Template( VIEW . __CLASS__.'/template/' );
		$this->out( 'template', $temp );

		if ( $this->view->gets[1] != '' && !is_numeric($this->view->gets[1]) ) {
			$this->out( 'news', $news->get( $this->view->gets[1] ) );
			$arg = array(
				'limit' => 4,
				'page' 	=> 1,
				'order' => array(
					'by' => 'rand()'
				),
				"except_id" => $this->view->news->getId()
			);
			$this->out( 'related', $news->getTree( $arg ) );

			$url = $this->view->news->getUrl();
			if ( $this->view->news->getImage() ) {
				$image = \PortalManager\Formater::sourceImg($this->view->news->getImage());
			}
			$title = $this->view->news->getTitle() . ' | Hírek';
			$description = substr(strip_tags($this->view->news->getDescription()), 0 , 350);

		} else {
			$arg = array(
				'limit' => 9,
				'page' => Helper::currentPageNum()
			);
			$this->out( 'list', $news->getTree( $arg ) );
			$this->out( 'navigator', (new Pagination(array(
					'class' 	=> 'pagination pagination-sm center',
					'current' 	=> $news->getCurrentPage(),
					'max' 		=> $news->getMaxPage(),
					'root' 		=> '/'.__CLASS__,
					'item_limit'=> 12
				)))->render() );
		}


		// SEO Információk
		$SEO = null;
		// Site info
		$SEO .= $this->view->addMeta('description', $description);
		$SEO .= $this->view->addMeta('keywords', $keywords);
		$SEO .= $this->view->addMeta('revisit-after','3 days');

		// FB info
		$SEO .= $this->view->addOG('title',$title.' | '.$this->view->settings['page_title']);
		$SEO .= $this->view->addOG('type','website');
		$SEO .= $this->view->addOG('url',$url);
		$SEO .= $this->view->addOG('image',$image);
		$SEO .= $this->view->addOG('site_name',$title.' | '.$this->view->settings['page_title']);

		$this->view->SEOSERVICE = $SEO;

		parent::$pageTitle = $title;
	}

	function __destruct(){
		// RENDER OUTPUT
			parent::bodyHead();					# HEADER
			$this->view->render(__CLASS__);		# CONTENT
			parent::__destruct();				# FOOTER
	}
}
?>
