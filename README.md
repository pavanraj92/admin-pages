# Admin Page (CMS) Manager

This package provides an Admin Page (CMS) Manager for managing content pages within your application.

---

## Features

- Create, edit, and delete CMS pages
- Organize pages with categories or hierarchies
- CKeditor support
- SEO-friendly URLs and metadata management
- User permissions and access control

---

## Requirements

- PHP >=8.2
- Laravel Framework >= 12.x

---

## Installation

### 1. Add Git Repository to `composer.json`

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/pavanraj92/admin-pages.git"
    }
]
```
### 2. Require the package via Composer
    ```bash
    composer require admin/pages:@dev
    ```

### 3. Publish assets
    ```bash
    php artisan pages:publish --force
    ```
---

## Usage

## Admin Panel Routes

| Method | Endpoint         | Description              |
|--------|------------------|--------------------------|
| GET    | `/pages`         | List all pages           |
| POST   | `/pages`         | Create a new page        |
| GET    | `/pages/{id}`    | Get page details         |
| PUT    | `/pages/{id}`    | Update a page            |
| DELETE | `/pages/{id}`    | Delete a page            |

---

## Protecting Admin Routes

Protect your routes using the provided middleware:

```php
Route::middleware(['web','admin.auth'])->group(function () {
    // Admin pages routes here
});
```
---

## Database Tables

- `pages` - Stores pages information

---

## License

This package is open-sourced software licensed under the MIT license.
