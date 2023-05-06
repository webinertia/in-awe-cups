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
            'db_time_format' => \DateTimeInterface::RFC3339,
            'display_time_format' => 'm-j-Y g:i:s', // normal US display format
            'time_format' => 'm-j-Y g:i:s',
            'time_zone' => 'America/Chicago',
            'log_errors' => '1',
            'enable_translation' => '1',
        ],
        'seo' => [
            'key_words' => 'Aurora CMS, Webinertia.net, Laminas, Php, MySQL',
            'description' => 'Application development, Mobile &amp; API development',
        ],
        'view' => [
            'site_name' => 'Webinertia LLC',
            'copyright_link' => 'https://webinertia.net',
            'copyright_text' => 'Webinertia LLC',
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
