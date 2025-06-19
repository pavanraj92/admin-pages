# Admin Page (CMS) Manager

This package provides an Admin Page (CMS) Manager for managing content pages within your application.

## Features

- Create, edit, and delete CMS pages
- Organize pages with categories or hierarchies
- WYSIWYG editor support
- SEO-friendly URLs and metadata management
- User permissions and access control

## Need to update `composer.json` file

Add the following to your `composer.json` to use the package from a local path:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/pavanraj92/admin-pages.git"
    }
]
```

## Installation

```bash
composer require admin/pages --dev
```

## Usage

1. Publish the configuration and migration files:
    ```bash
    php artisan vendor:publish --tag=page
    php artisan migrate
    ```
2. Access the CMS manager from your admin dashboard.

## Example

```php
// Creating a new page
$page = new Page();
$page->title = 'About Us';
$page->slug = 'about-us';
$page->content = '<p>Welcome to our website!</p>';
$page->save();
```

## Customization

You can customize views, routes, and permissions by editing the configuration file.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).write code in the readme.md file regarding to the admin/page(CMS) manager
