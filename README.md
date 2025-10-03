<a href="https://phanstatic.com" target="_blank"><img src="https://raw.githubusercontent.com/terdelyi/phanstatic/refs/heads/feature/di-container/art/logo.png" width="500" alt="Phanstatic"></a>

<a href="https://packagist.org/packages/terdelyi/phanstatic"><img src="https://img.shields.io/packagist/dt/terdelyi/phanstatic" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/terdelyi/phanstatic"><img src="https://img.shields.io/packagist/v/terdelyi/phanstatic" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/terdelyi/phanstatic"><img src="https://img.shields.io/packagist/l/terdelyi/phanstatic" alt="License"></a>

Phanstatic is a simple, lightweight, CLI based static site generator written in PHP. There are no frameworks or template
engines, just simple pages written in pure PHP code and markdown files. During the building process, all of your content
is transformed into static HTML files, ready to deploy or upload to your server.

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

To preview the `dist` folder quickly in a browser:

```
php ./vendor/bin/phanstatic preview
```

This will start PHP's built-in server at `localhost` with port `8000` and make the files from the `dist` available in a
browser.

You can override the default host (`--host`) and the port (`--port`) settings if necessary.

## Configuration

You can place a configuration file under `content/config.php` which must return a `ConfigBuilder` object like this:

```php
use Terdelyi\Phanstatic\Support\ConfigBuilder;

return (new ConfigBuilder)
    ->setBaseUrl(getenv('BASE_URL'))
    ->setTitle('My super-fast static site')
    ->addCollection(
        key: 'posts',
        title: 'Posts',
        slug: 'posts',
        pageSize: 10
    );
```
If no `config.php` file exist the builder will use the default settings. To explore settings your IDE will help you by
offering the available methods.

## Content basics

Structuring the content is simple. The `content` folder is where your files live:

- `content/pages`: This is where you put your `.php` files.
- `content/collections`: This is where you put your `.md` files under subdirectories named as your collection key.
- `content/assets`: Any of these files will be published under `dist/assets`. It can be `.js`, `.css` or any type of images.

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