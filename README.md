<a href="https://phanstatic.com" target="_blank"><img src="https://github.com/terdelyi/phanstatic/blob/main/art/logo.png" alt="Phanstatic"></a>

<a href="https://packagist.org/packages/terdelyi/phanstatic"><img src="https://img.shields.io/packagist/dt/terdelyi/phanstatic" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/terdelyi/phanstatic"><img src="https://img.shields.io/packagist/v/terdelyi/phanstatic" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/terdelyi/phanstatic"><img src="https://img.shields.io/packagist/l/terdelyi/phanstatic" alt="License"></a>

Phanstatic is a simple, lightweight, CLI based static site generator written in PHP. There are no frameworks or template
engines, just put your content into pure PHP templates and markdown files. During the building process, all of your content
is transformed into HTML files ready to deploy or upload to your webserver.

## Install

To create a new project just run:

```
composer create-project terdelyi/phanstatic
```

## Build

To build static files to the `dist` folder from the files placed inside the `content` directory run the following
command in your root folder:

```
php ./vendor/bin/phanstatic build
```

## Preview

To preview your site in a browser directly from the `content` folder just run:

```
php ./vendor/bin/phanstatic preview
```

You can also preview the built files from the `dist` folder:

```
php ./vendor/bin/phanstatic preview --dist
```

If necessary you can also change the default `--host` (localhost) and the `--port` (8080) settings.

## Configuration

It's optional, but you can place a configuration file under `content/config.php` which must return a `ConfigBuilder` object like this:

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
If no `config.php` file exist the builder will use the default settings. To explore settings your IDE should guide you by
offering the available properties with types.

## Content basics

Structuring the content is straightforward. The `content` folder is where your pages and collections live:

- `content/pages`: This is where you put your page templates with `.php` extension.
- `content/collections`: This is where you put your collections in subdirectories and their items as `.md` files.
- `content/assets`: Any files placed in here will be published under `dist/assets`.

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
│   │   ├── about.php
│   │   ├── index.php
│   ├── config.php
├── composer.json
├── composer.lock
```

If you create a folder under `collections` you **must add it as a collection** to your config file, unless you're going
to have `Configuration for collection 'Collection' is missing` error.