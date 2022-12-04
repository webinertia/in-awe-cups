<?php

declare(strict_types=1);

namespace User\Controller;

use App\Controller\AbstractAppController;
use Laminas\Db\Sql\Select;
use Laminas\EventManager\Exception\InvalidArgumentException;
use Laminas\Mvc\Exception\DomainException;
use Laminas\Mvc\MvcEvent;
use Laminas\Paginator\Adapter\LaminasDb\DbSelect;
use Laminas\Paginator\AdapterPluginManager;
use Laminas\Paginator\Paginator;
use Laminas\View\Model\ViewModel;
use User\Db\UserGateway;

final class WidgetController extends AbstractAppController
{
    /** @var Paginator $paginator */
    protected $paginator;
    /** @var string $group */
    protected $group = 'member';

    /**
     * @throws InvalidArgumentException
     * @throws DomainException
     */
    public function onDispatch(MvcEvent $e): mixed
    {
        $this->group = $this->getEvent()->getRouteMatch()->getParam('group', 'all');
        $sm          = $e->getApplication()->getServiceManager();
        $config      = $this->config;
        $table       = $sm->get(UserGateway::class);
        $sql         = $table->getSql();
        $select      = new Select();
        $select->from('users')->order(['users.role ASC']);
        $pluginManager = $this->getEvent()->getApplication()->getServiceManager()->get(AdapterPluginManager::class);
        $adapter       = $pluginManager->get(DbSelect::class, [$select, $sql, $table->getResultSetPrototype()]);
        $paginator     = new Paginator($adapter);
        $paginator->setDefaultItemCountPerPage($config['widgets']['member_list']['items_per_page']);
        $this->paginator = $paginator;
        $this->view->setTerminal(true);
        return parent::onDispatch($e);
    }

    public function memberListAction(): ViewModel
    {
        $this->view->setTerminal(true);
        $page = (int) $this->params('page', '1');
        $this->paginator->setCurrentPageNumber($page);
        $this->view->setVariables(
            [
                'paginator' => $this->paginator,
                'listType'  => $this->config['widgets']['admin_member_list']['display_groups'],
            ]
        );
        return $this->view;
    }

    public function listDataAction(): ViewModel
    {
        $this->view->setTerminal(true);
        $page = (int) $this->params('page', '1');
        $this->paginator->setCurrentPageNumber($page);
        $this->view->setVariable('paginator', $this->paginator);
        return $this->view;
    }

    public function adminWidgetAction()
    {
        if ($this->request->isXmlHttpRequest()) {
            $this->view->setTerminal(true);
        }
        $memberListWidget = $this->forward()->dispatch(
            WidgetController::class,
            [
                'action' => 'member-list',
                'group'  => 'admin',
            ]
        );
        $this->view->setVariables(['memberListWidget' => $memberListWidget, 'listType' => 'admin']);
        return $this->view;
    }
}
