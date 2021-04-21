=== Advanced Database Replacer ===
Contributors: dam6pl
Tags: replace, database, woocommerce, cpt, taxonomy, users
Requires at least: 5.0
Tested up to: 5.7.1
Requires PHP: 7.2.5
Stable tag: 1.1.0
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

The most advanced WordPress database replacer plugin. A user-friendly and powerful tool to fast modify WordPress posts, taxonomies, and meta.

== Description ==
Advanced Database Replacer allows you to fast and easily update a lot of data in the database. The plugin provides a user-friendly form that allows you to update posts, taxonomies, users (and many more) data without any programming knowledge. Plugin based on the provided data automatically prepares the SQL (Structured Query Language)  query that allows an update of many records at the same time.

### Plugin features and possibilities
Advanced Database Replacer plugin is prepared for non-technical people who want to fast and save update many elements in the WordPress database. By default, you can edit all Custom Post Types, Taxonomies, Users data, and all connected with the custom meta. More features:

* replace, uppercase and lowercase fields from Custom Post Types, Terms and Users, and all connected meta,
* update CPTs title, content, and exception, Terms names and descriptions, Users nice name, email, display name, website URL, biography content,
* add extra conditions that allow fit the query and update only that content do you really want to, eg. filter specific terms or find posts by the author,
* dry run for check how many records will be updated and confirm that query has been build property,
* history of replacement to take more control of changes made by the plugin,
* **[PRO ONLY]** add infinity number of conditions and define the relationships between them,
* **[PRO ONLY]** use CPTs, Terms, and Users custom meta in the condition fields, eg. find WooCommerce products with stock higher than 3,
* **[PRO ONLY]** allows increase/decrease, and at the beginning, add at the end during the update, eg. add `Awesome` prefix to the title for all posts from `Awesome` category,
* **[COMING SOON]** save reusable templates, that allows fast repeat replace, eg. increase stock value for WooCommerce products.

== Frequently Asked Questions ==
= How to use Advanced Database Replacer? =
Advanced Database Replacer allows you to fast and easily update a lot of data in the database. The plugin provides a user-friendly form that allows you to update posts, taxonomies, users (and many more) data without any programming knowledge. Plugin based on the provided data automatically prepares the SQL (Structured Query Language) query that allows an update of many records at the same time.

= I want to use ADR but I don't know how to back up my database =
If you are not an IT specialist then you should use some extra plugin to create a backup, like WP Database Backup, and then start the replacement process. You can use the condition group to fit your query and limit the data that can be affected by the replacing process.

= What should I do when ADR broke my website? =
In this case, the database backup that you create before replacement will be indispensable. You have to have access to your database (most hosting providers allow access to the database) and then upload a backup database.

== Screenshots ==
1. The view of conditions section
2. Preview the form data before run query
3. View of query execution message

== Changelog ==
= 1.1.0 =
* Tested plugin with WordPress 5.7 version,
* Fixed issue with fatal error when call Dry run,
* Changed minimal required PHP version to 7.2.5,
* Updated external dependencies.

= 1.0.1 =
* Update texts and translations,
* Small refactor of Builder class.

= 1.0.0 =
* First release of the plugin, all options are ready for you!