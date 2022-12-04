<?php

declare(strict_types=1);

namespace User\Controller;

use App\Controller\AbstractAppController;
use App\Form\FormInterface;
use App\Form\FormManagerAwareInterface;
use App\Form\FormManagerAwareTrait;
use App\Log\LogEvent;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ViewModel;
use RuntimeException;
use Throwable;
use User\Form\LoginForm;
use User\Form\UserForm;
use User\Service\UserServiceInterface;

use function array_merge;
use function sprintf;

final class AccountController extends AbstractAppController implements FormManagerAwareInterface
{
    use FormManagerAwareTrait;

    /** @var string $resourceId */
    protected $resourceId = 'account';

    public function dashboardAction(): ViewModel
    {
        return $this->view;
    }

    public function loginAction(): mixed
    {
        $form = $this->formManager->get(LoginForm::class);
        if (! $this->request->isPost()) {
            $this->view->setVariable('form', $form);
            return $this->view;
        }
        $form->setData($this->request->getPost());
        if (! $form->isValid()) {
            $this->getEventManager()->trigger(LogEvent::NOTICE, 'log_login_failure');
            $this->view->setVariable('form', $form);
            return $this->view;
        } else {
            $userService = $this->identity()->getIdentity();
            $this->getEventManager()->trigger(LogEvent::NOTICE, 'log_login_success', $userService->getLogData());
            $this->flashMessenger()->addSuccessMessage(
                $this->getTranslator()->translate('login_success')
                . ' '
                . sprintf($this->getTranslator()->translate('welcome_back'), $userService->getFullName())
            );
            return $this->redirect()->toRoute('home', ['userName' => $userService->userName]);
        }
        $this->view->setVariable('form', $form);
        return $this->view;
    }

    public function editAction(): ViewModel
    {
        try {
            // get the user by userName that is to be edited
            $userName = $this->params()->fromRoute('userName');
            $user     = $this->userService->fetchByColumn('userName', $userName);
            if (! $this->isAllowed()) {
                $this->getEventManager()->trigger(LogEvent::CRITICAL, 'log_forbidden_403');
                $this->flashMessenger()->addWarningMessage(
                    $this->getTranslator()->translate('forbidden_403')
                );
                $this->redirect()->toRoute('home');
            }
            $form     = $this->formManager->build(
                UserForm::class,
                ['mode' => FormInterface::EDIT_MODE, 'userId' => $user->id]
            );
            $userName = $form->get('acct-data')->get('userName');
            $userName->setAttribute('readonly', 'readonly');
            if (! $this->request->isPost()) {
                $data = [
                    'acct-data'    => [
                        'id'       => $user->id,
                        'userName' => $user->userName,
                        'email'    => $user->email,
                    ],
                    'role-data'    => [
                        'role' => $user->role,
                    ],
                    'profile-data' => [
                        'firstName' => $user->firstName,
                        'lastName'  => $user->lastName,
                        'age'       => $user->age,
                        'birthday'  => $user->birthday,
                        'bio'       => $user->bio,
                    ],
                ];
                $form->setData($data);
            } else {
                $data = $this->request->getPost();
                $form->setData($data);
                if (! $form->isValid()) { // if the form does not validate, check the acct-data fieldset filter
                    $this->view->setVariable('form', $form);
                    return $this->view;
                } else {
                    $valid  = $form->getData();
                    $result = $this->userService->save(
                        array_merge($valid['acct-data'], $valid['role-data'], $valid['profile-data']),
                        $valid['acct-data']['id']
                    );
                }
                if ($result) {
                    // Redirect to User list
                    $this->redirect()->toRoute('user/list', ['page' => 1, 'count' => 5]);
                } else {
                    throw new RuntimeException('log_account_update_failure');
                }
            }
            $this->view->setVariable('form', $form);
        } catch (Throwable $th) {
            $this->getEventManager()->trigger(LogEvent::ERROR, $th->getMessage());
        }
        return $this->view;
    }

    public function deleteAction(): void
    {
        $user = false;
        try {
            $userName = $this->params()->fromRoute('userName');
            $user     = $this->userService->fetchByColumn('userName', $userName);
            if (! $user instanceof UserServiceInterface) {
                throw new RuntimeException('log_exception_user_not_found');
            }
            if ($this->isAllowed($user)) {
                $result = $this->userService->delete(['id' => $user->id]);
                if ($result > 0) {
                    $this->getEventManager()->trigger(
                        LogEvent::INFO,
                        $this->getTranslator()->translate('log_account_delete_success'),
                        $user->getFullName()
                    );
                    $this->redirect()->toRoute(
                        'user',
                        ['action' => 'index', 'userName' => $user->userName]
                    );
                } else {
                    throw new RuntimeException('log_account_deletion_failure');
                }
            } else {
                $this->getEventManager()->trigger(
                    LogEvent::ERROR,
                    sprintf(
                        $this->getTranslator()->translate('log_forbidden_known_action_403'),
                        $user->getFullName()
                    )
                );
                $this->flashMessenger()->addErrorMessage(
                    sprintf(
                        $this->getTranslator()->translate('forbidden_known_action_403_user'),
                        $user->getFullName()
                    )
                );
                $this->response->setStatusCode(403);
                $this->redirect()->toRoute('user/list', ['page' => 1, 'count' => 5]);
            }
        } catch (RuntimeException $e) {
            $this->getEventManager()->trigger(
                LogEvent::ERROR,
                sprintf(
                    $this->getTranslator()->translate('log_account_delete_failure'),
                    $user->getFullName()
                )
            );
        }
    }

    public function logoutAction(): object
    {
        if ($this->identity()->hasIdentity()) {
            $ident = $this->identity()->getIdentity()->getLogData();
            $this->identity()->clearIdentity();
            $this->getEventManager()->trigger(
                LogEvent::INFO,
                sprintf(
                    $this->getTranslator()->translate('log_logout_success'),
                    $ident['firstName'] . ' ' . $ident['lastName']
                ),
                $ident
            );
            $this->flashMessenger()->addInfoMessage($this->getTranslator()->translate('logout_success'));
            return $this->redirect()->toRoute('home');
        } else {
            $this->flashMessenger()->addErrorMessage(
                $this->getTranslator()->translate('logout_failure')
            );
            // Serious issue, possibly searching for attack vectors
            $this->getEventManager()->trigger(LogEvent::ALERT, 'log_logout_non_loggedin_attempt');
            return $this->redirect()->toRoute('home');
        }
    }

    public function staffActivateAction(): ?ViewModel
    {
        try {
            $user = false;
            if ($this->isAllowed()) {
                if ($this->request->isXmlHttpRequest()) {
                    $jsonModel = new JsonModel();
                }
                $userName = $this->params()->fromRoute('userName');
                $user     = $this->userService->fetchByColumn('userName', $userName);
                if (! $user instanceof UserServiceInterface) {
                    throw new RuntimeException('The user could not be found');
                }
                $user->active = 1;
                $result       = $this->userService->save($user->toArray(), $user->id);
                if ($result) {
                    $this->getEventManager()->trigger(
                        LogEvent::INFO,
                        sprintf(
                            $this->getTranslator()->translate('log_account_staff_activation_success'),
                            $user->userName
                        )
                    );
                } else {
                    throw new RuntimeException(
                        sprintf(
                            $this->getTranslator()->translate('log_account_staff_activation_failure'),
                            $user->userName
                        )
                    );
                }
            } else {
                $this->getEventManager()->trigger(LogEvent::ALERT, 'log_forbidden_403');
                $this->flashMessenger()->addErrorMessage(
                    $this->getTranslator()->translate('forbidden_403')
                );
                $this->response->setStatusCode(403);
            }
        } catch (RuntimeException $e) {
            $this->getEventManager()->trigger(LogEvent::ERROR, $e->getMessage());
        }
        $this->view->setVariables(['user' => $this->identity()->getIdentity(), 'activatedUser' => $user]);
        $this->redirect()->toRoute(
            'user/list',
            [
                'page'  => 1,
                'count' => 5,
            ]
        );
        return $this->view;
    }

    public function staffDeactivateAction(): ?ViewModel
    {
        try {
            $user = false;
            if ($this->acl()->isAllowed($this->identity()->getIdentity(), $this, 'create')) {
                if ($this->request->isXmlHttpRequest()) {
                    $this->view->setTerminal(true);
                }
                $userName = $this->params()->fromRoute('userName');
                $user     = $this->userService->fetchByColumn('userName', $userName);
                if (! $user instanceof UserServiceInterface) {
                    throw new RuntimeException(
                        sprintf(
                            $this->getTranslator()->translate('log_known_user_not_found'),
                            $userName
                        )
                    );
                }
                $user->active = 0;
                $result       = $this->userService->save($user->toArray(), $user->id);
                if ($result) {
                    $this->getEventManager()->trigger(
                        LogEvent::NOTICE,
                        sprintf(
                            $this->getTranslator()->translate('log_account_staff_deactivation_success'),
                            $user->userName
                        )
                    );
                } else {
                    throw new RuntimeException(
                        sprintf(
                            $this->getTranslator()->translate('log_account_staff_deactivation_failure'),
                            $user->userName
                        )
                    );
                }
            } else {
                $this->flashMessenger()->addErrorMessage(
                    $this->getTranslator()->translate('forbidden_403')
                );
                $this->response->setStatusCode(403);
            }
        } catch (RuntimeException $e) {
            $this->getEventManager()->trigger(LogEvent::ALERT, $e->getMessage());
        }
        $this->view->setVariables(['user' => $this->identity()->getIdentity(), 'deactivatedUser' => $user]);
        $this->redirect()->toRoute(
            'user/list',
            [
                'page'  => $this->params()->fromRoute('page'),
                'count' => 5,
            ]
        );
        return $this->view;
    }
}
