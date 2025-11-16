<a href="https://phanstatic.com" target="_blank"><img src="https://github.com/terdelyi/phanstatic/blob/main/art/logo.png" alt="Phanstatic"></a>

<a href="https://packagist.org/packages/terdelyi/phanstatic"><img src="https://img.shields.io/packagist/dt/terdelyi/phanstatic" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/terdelyi/phanstatic"><img src="https://img.shields.io/packagist/v/terdelyi/phanstatic" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/terdelyi/phanstatic"><img src="https://img.shields.io/packagist/l/terdelyi/phanstatic" alt="License"></a>

Phanstatic is a simple, lightweight, CLI-based static site generator written in PHP, without additional frameworks or template engines.

You don't need to learn any new APIs or syntax: it's just basic PHP pages and Markdown files placed in a `content` directory, which will then be compiled into fast, portable HTML files, ready to deploy on any web server with minimal effort.

## Install

To create a new project, run:

```
composer create-project terdelyi/phanstatic
```

If you already have a project with a `content` folder, install Phanstatic with:

```
composer require terdelyi/phanstatic
```

## Build

To generate static files from the content in your `content` directory, run the following command from your project
root:

```
php ./vendor/bin/phanstatic build
```

The generated files will be placed in the `dist` folder.

## Preview

To preview your site directly from the `content` folder in a browser, run:

```
php ./vendor/bin/phanstatic preview
```

In this mode, you can edit your PHP files, and changes will appear in the browser after refreshing the page.

To preview the built files from the `dist` folder, use:

```
php ./vendor/bin/phanstatic preview --dist
```

You can also customise the host and port with the `--host` and `--port` options. The default values are _localhost_
and _8080_.

## Configuration

Configuration is optional. You can create a configuration file at `content/config.php`, which must return a
`ConfigBuilder` object, for example:

```php
use Terdelyi\Phanstatic\Models\Config;
use Terdelyi\Phanstatic\Models\CollectionConfig;

return new Config(
    baseUrl: (string) getenv('BASE_URL'),
    title: 'My super-fast static site',
    collections: [
        'posts' => new CollectionConfig(
            title: 'Posts',
            slug: 'posts',
            pageSize: 10
        ),
    ],
);
```
If no `config.php` file exists, Phanstatic will use the default settings. Your IDE can help you explore the available
configuration options, showing the properties and their types.

## Content basics

Organizing your content is simple. The content folder contains your pages, collections, and assets:

- `content/pages`: Place your page templates here using the `.php` extension.
- `content/collections`: Store collections in subdirectories, with individual items as `.md` files.
- `content/assets`: Any files here will be published to `dist/assets`.

You can also use partials: any folder or file starting with an underscore `_` will be ignored during the build.
Include these partials in your pages using PHP's `include()` function.

### Example project structure

```
├── content
│   ├── assets
│   │   ├── images
│   │   ├── css
│   │   ├── js
│   ├── collections
│   │   ├── posts
│   │   │   ├── my-first-blog-post.md
│   ├── pages
│   │   ├── _partials
│   │   │   ├── header.php
│   │   │   ├── footer.php
│   │   ├── about.php
│   │   ├── index.php
│   ├── config.php
├── composer.json
├── composer.lock
```

If you create a folder under collections, **you must register it in your configuration file**. Otherwise, you will
get a "Configuration for collection 'Collection' is missing" error.

## Frontend

Phanstatic does not include frontend or theme support. You can add your CSS and other assets freely under the assets/ folder, and reference them in your pages using:

```php
<link rel="stylesheet" href="<?php echo asset('css/site.css'); ?>">
```


## Available helpers

| Function       | Description                                                                                    |
|:---------------|:-----------------------------------------------------------------------------------------------|
| `assets()`     | Link files from the `assets` folder. The base URL from the config is added automatically.      |
| `url()`        | Generate links to any page on the site. . The base URL from the config is added automatically. |
| `dd()`         | Dump variables and stop execution during runtime or build.                                     |
| `source_dir()` | Link files from the source directory (default: _content_).                                     |
| `build_dir()`  | Link files from the distribution directory (default: _dist_).                                  |


## Contributions

The main goal of Phanstatic is to keep simple and fast for common use cases, such as simple sites and blogs.

If you
find a bug, have a feature request, or want to suggest improvements, please
[contact me directly](https://github.com/terdelyi) or
[start a discussion on GitHub](https://github.com/terdelyi/phanstatic/discussions). Contributions in the form of
code,  documentation, or examples are very welcome and  greatly appreciated.