<?php

declare(strict_types=1);

namespace User\Acl;

use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Assertion\OwnershipAssertion as Owner;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use User\Model\Roles;

final class AclFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Acl
    {
        $acl   = new Acl();
        $roles = $container->get(Roles::class);
        $roles = $roles->getRoles();
        foreach ($roles as $role) {
            if ($role['inheritsFrom'] !== null) {
                $acl->addRole($role['role'], $role['inheritsFrom']);
            } else {
                $acl->addRole($role['role']);
            }
        }
        $acl->addResource('admin');
        $acl->addResource('settings', 'admin');
        $acl->addResource('features', 'settings');
        $acl->addResource('roles', 'admin');
        $acl->addResource('server', 'settings');
        $acl->addResource('seo', 'settings');
        $acl->addResource('view', 'settings');
        $acl->addResource('themes', 'settings');
        $acl->addResource('logs', 'admin');

        $acl->addResource('users');
        $acl->addResource('account', 'users');
        $acl->addResource('store', 'account');
        $acl->addResource('profile', 'account');
        $acl->addResource('member-list', 'users');

        $acl->addResource('messages');
        $acl->addResource('contact-us', 'messages');
        $acl->addResource('site-message', 'messages');
        $acl->addResource('personal-message', 'messages');

        $acl->addResource('content');
        $acl->addResource('page', 'content');
        $acl->addResource('widget', 'content');
        $acl->addResource('imageslider', 'widget');

        $acl->allow('Guest', 'content', 'view'); // should allow reading of pages
        $acl->allow('Guest', 'account', ['register', 'login']); // should allow showing the register, login tabs
        $acl->allow('Guest', 'contact-us', ['view', 'send']); // view, send contact us form
        $acl->allow('Guest', ['messages', 'site-message'], ['message-Staff']);
        $acl->allow('Guest', ['widget'], 'view');
        $acl->deny('Guest', 'account', 'logout'); // should prevent guest from seeing the logout

        $acl->allow('Member', null, ['view', 'edit', 'delete'], new Owner()); // view, edit, delete own account
        $acl->deny('Member', 'account', ['register', 'login']);
        $acl->deny('Member', 'member-list', 'view'); // should prevent user from seeing the user list
        $acl->allow('Member', 'account', 'logout');
        $acl->allow('Member', 'profile', 'view'); // allow any logged in user to view their profile
        $acl->allow('Member', 'profile', ['edit', 'delete'], new Owner());
        // allow sending and replying to messages
        $acl->allow('Member', ['site-message', 'personal-message'], ['send', 'reply']);
        // allow deleting and viewing of own messages
        $acl->allow('Member', ['site-message', 'personal-message'], ['delete', 'view'], new Owner());
        $acl->deny(['Guest', 'Member'], 'admin', 'view'); // should prevent guests and users from seeing the admin page

        $acl->allow(
            'Staff',
            ['account', 'profile', 'member-list', 'content', 'store'], // resources
            [// privileges
                'view',
                'create',
                'edit',
                'delete',
                'upload-images',
                'staffActivate',
                'create-category',
                'create-product',
                'edit-category',
                'edit-product',
                'edit-order',
                'create-shipping',
                'edit-shipping',
                'use-store-toolbar',
            ]
        );
        $acl->allow('Staff', 'admin', 'view'); // should allow staff to view the admin page
        $acl->deny('Staff', ['settings']);
        $acl->allow(
            'Administrator',
            null,
            ['create', 'view', 'edit', 'delete', 'admin.access', 'manage']
        ); // should allow admin to view, edit, and delete everything
        $acl->allow(
            'Administrator',
            'settings',
            ['manage-settings', 'manage-themes']
        ); // should allow admin to manage settings
        $acl->allow('Administrator', 'roles', 'assign'); // should allow admin to assign roles to users
        $acl->allow('Super Administrator');

        return $acl;
    }
}
