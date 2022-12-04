<?php
/**
 * This file is for application settings that can be modified.
 * db_time_format ISO8601/RFC3339 \DateTimeInterface::RFC3339 dojo compatible, provides correct sorting of data strings
 * display_time_format 'm-j-Y g:i:s' display in normal US output
 * phpcs:ignoreFile
 */
return [
    'app_settings' => [
        'server' => [
            'app_path' => 'C:/htdocs/aurora',
            'upload_basepath' => 'C:/htdocs/aurora/public/module',
            'request_scheme' => 'http',
            'host' => 'aurora',
            'db_time_format' => \DateTimeInterface::RFC3339,
            'display_time_format' => 'm-j-Y g:i:s', // normal US display format
            'time_format' => 'm-j-Y g:i:s',
            'time_zone' => 'America/Chicago',
            'log_errors' => '1',
            'enable_translation' => '1',
            'captcha_path' => '/modules/app/captcha/',
        ],
        'log' => [
            'time_format' => 'm-d-Y H:i:s',
        ],
        'seo' => [
            'key_words' => 'Aurora CMS, Webinertia.net, Laminas, Php, MySQL',
            'description' => 'In Awe Cups &amp; More',
        ],
        'view' => [
            'site_name' => 'In Awe Cups &amp; More',
            'copyright_link' => 'https://inawecups.com',
            'copyright_text' => 'In Awe Cups &amp; More',
            'footer_text' => 'Developed by Webinertia',
            'show_breadcrumbs' => '0',
        ],
        'email' => [
            'contact_form_email' => 'jsmith@webinertia.net',
            'smtp_sender_address' => 'jsmith@webinertia.net',
            'smtp_sender_password' => '',
            'enable_contact_form' => '1',
        ],
        'security' => [
            'enable_login' => '1',
            'enable_registration' => '1',
            'enable_captcha' => '0',
        ],
    ],
];
