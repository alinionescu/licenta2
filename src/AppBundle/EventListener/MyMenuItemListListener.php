<?php
/**
 * Created by PhpStorm.
 * User: alin
 * Date: 13.03.2016
 * Time: 19:55
 */

namespace AppBundle\EventListener;

use AppBundle\Model\MenuItemModel;
use Avanzu\AdminThemeBundle\Event\SidebarMenuEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class MyMenuItemListListener
{
    /** @var TokenStorage */
    private $tokenStorage;

    public function __construct(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param SidebarMenuEvent $event
     */
    public function onSetupMenu(SidebarMenuEvent $event) {

        $request = $event->getRequest();

        foreach ($this->getMenu($request) as $item) {
            $event->addItem($item);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    protected function getMenu(Request $request) {
        // Build your menu here by constructing a MenuItemModel array

        $menuItems = $this->getMeniuItemByRole();

        return $this->activateByRoute($request->get('_route'), $menuItems);
    }

    /**
     * @param $route
     * @param $items
     * @return mixed
     */
    protected function activateByRoute($route, $items) {

        foreach($items as $item) {
            if($item->hasChildren()) {
                $this->activateByRoute($route, $item->getChildren());
            }
            else {
                if($item->getRoute() == $route) {
                    $item->setIsActive(true);
                }
            }
        }

        return $items;
    }

    /**
     * @return array
     */
    protected function getMeniuItemByRole()
    {
        $menuItems = [
            $blog = new MenuItemModel('ItemId', 'ItemDisplayName', 'fos_user_security_login', array(/* options */), 'iconclasses fa fa-plane'),
            $action = new MenuItemModel('actionId', 'Action', 'fos_user_security_login', array(), 'fa fa-rss-square')
        ];

        $user = $this->tokenStorage->getToken()->getUser();

        if ($user == 'anon.') {
            $login = new MenuItemModel('loginId', 'Login', 'fos_user_security_login', array(/* options */), 'iconclasses fa fa-plane');
            array_push($menuItems, $login);
        }
        // Add some children

        // A child with default circle icon
        $blog->addChild(new MenuItemModel('ChildTwoItemId', 'ChildTwoDisplayName', 'fos_user_security_login'));

        return $menuItems;
    }
}