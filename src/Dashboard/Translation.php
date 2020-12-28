<?php

declare(strict_types=1);

namespace AdvancedDatabaseReplacer\Dashboard;

class Translation
{
    public static function getTranslations(): array
    {
        $translations = [
            'title'       => \__('Advanced Database Replacer', 'adr'),
            'coming_soon' => \__('Coming soon', 'adr'),
            'alert'       => [
                'message' => \__('Let\'s try the Advanced Database Replacer in PRO version!', 'adr'),
                'banner'  => [
                    \__('Save replacement templates for fast back to them and repeat!', 'adr'),
                    \__('Create replacement with infinity number of conditions groups for better fit your query!', 'adr'),
                    \__('Use advanced replacement methods like increase or decrease numbers!', 'adr'),
                ],
                'button' => \__('Get PRO version', 'adr'),
            ],
            'menu' => [
                'replacer'  => \__('Replacer', 'adr'),
                'templates' => \__('Saved templates', 'adr'),
                'help'      => \__('Help', 'adr'),
                'history'   => \__('History', 'adr'),
                'pro'       => \__('Get PRO version', 'adr'),
            ],
            'form' => [
                'valid'                    => \__('This field is required!', 'adr'),
                'prev_step'                => \__('Previous step', 'adr'),
                'next_step'                => \__('Next step', 'adr'),
                'remove_group'             => \__('Remove condition group', 'adr'),
                'remove_group_confirm'     => \__('Are you sure do you want to remove this group?', 'adr'),
                'remove_group_confirm_yes' => \__('Yes, remove this group', 'adr'),
                'remove_group_confirm_no'  => \__('No, cancel', 'adr'),
                'new_group'                => \__('Add new condition group', 'adr'),
                'steps'                    => [
                    'target'     => \__('Target', 'adr'),
                    'conditions' => \__('Conditions', 'adr'),
                    'update'     => \__('Update', 'adr'),
                    'replace'    => \__('Replace', 'adr'),
                ],
                'select_placeholder'       => \__('Select value', 'adr'),
                'async_select_placeholder' => \__('Start typing to search...', 'adr'),
                'save_template'            => \__('Save as a template', 'adr'),
            ],
            'replace' => [
                'confirm' => [
                    'title'   => \__('Confirm execution', 'adr'),
                    'message' => \__('Are you sure do you want to execute the query? This action cannot be
                        undone and can make extremely dangerous changes in the database that can break
                        the website. Please, make sure that you created a database backup before the run.', 'adr'),
                    'yes' => \__('Yes, execute', 'adr'),
                    'no'  => \__('No, get me back', 'adr'),
                ],
                'message' => \__('Here you can review all provided data and valid if anything is wrong.', 'adr'),
                'run_dry' => \__('Run dry query', 'adr'),
                'run'     => \__('Run query', 'adr'),
                'toggle'  => \__('Toggle SQL query', 'adr'),
                'modal'   => [
                    'success' => \__('Your run has been executed successful!', 'adr'),
                    'error'   => \__('Your run has been not executed successful!', 'adr'),
                ],
            ],
            'faq' => [
                'title'     => \__('Frequently Asked Questions', 'adr'),
                'questions' => [
                    [
                        'question' => \__('How to use Advanced Database Replacer?', 'adr'),
                        'answer'   => \__('Advanced Database Replacer allows you to fast and easily update a lot of
                            data in the database. The plugin provides a user-friendly form that allows you to update
                            posts, taxonomies, users (and many more) data without any programming knowledge. Plugin
                            based on the provided data automatically prepares the SQL (Structured Query Language)
                            query that allows an update of many records at the same time.', 'adr'),
                    ],
                    [
                        'question' => \__('I want to use ADR but I don\'t know how to backup my database', 'adr'),
                        'answer'   => \__('If you are not an IT specialist then you should use some extra plugin to
                            create a backup, like WP Database Backup, and then start the replacement process.
                            You can use the condition group to fit your query and limit the data that can be
                            affected by the replacing process.', 'adr'),
                    ],
                    [
                        'question' => \__('What should I do when ADR broke my website?', 'adr'),
                        'answer'   => \__('In this case, the database backup that you create before replacement will be
                        indispensable. You have to have access to your database (most hosting providers allow
                        access to the database) and then upload a backup database.', 'adr'),
                    ],
                ],
            ],
        ];

        return (array) \apply_filters('adr\translations', $translations);
    }
}
