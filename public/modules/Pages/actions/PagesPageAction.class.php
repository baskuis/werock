<?php

/**
 * Page actions
 */
class PagesPageAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {
	
	/**
	 * Set main template
	 */
	public $template = "pagedefault";

    /**
     * Set decorator template
     * @var string
     */
    public $decorator = "pagedecorator";

    public $description = null;
    public $html = null;

	/**
	 * Show slider
	 */
	public $showSlider = true;

	/**
	 * Show tabs
	 */
	public $showTabs = true;
	
	/**
	 * User
	 */
    public $user = true;

    /** @var PagesService $PagesService */
    private $PagesService = null;

	/**
	 * Get menu's for this action
	 *
	 * @return array
	 */
	public function getMenus()
	{

	}

	/**
	 * Get routes for this action
	 *
	 * @return array
	 */
	public function getRoutes()
	{

		$routes = array();

		$route = new CoreControllerObject('/^\/page\/(.*)-(.*)\.html$/i', __CLASS__, null, CoreControllerObject::MATCH_TYPE_REGEX, CoreControllerObject::GROUP_PAGE);
		$route->setType(CoreControllerObject::TYPE_ACTION);
		array_push($routes, $route);

		return $routes;

	}

	/**
     * Register action
     *
     * @return mixed|void
     */
    public function register(){

    }

	/**
	 * Catch params
	 */
    public function build($params = array()){

        $this->PagesService = CoreLogic::getService('PagesService');

        if (isset($params[2])) {
            $PagesPageObject = $this->PagesService->getPage(
                CoreUriUtils::decodeNumber((string) $params[2])
            );
            $this->setTitle($PagesPageObject->title);
            $this->setDescription($PagesPageObject->description);
            $this->html = $PagesPageObject->html;
        }

	}
	
}