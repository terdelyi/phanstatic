Phanstatic 👷‍
==========

Phanstatic is a simple, lightweight, CLI based static site generator written in PHP. There are no frameworks or template
engines, just simple pages written in pure PHP code and markdown files. During the building process, all of your content
is transformed into static HTML files, ready to deploy.

## Install

To create a new project just run:

```
composer create-project terdelyi/phanstatic:dev-develop
```

## Build

To build static files from your `content` directory to the `dist` folder run the following command in your root folder:

```
php ./vendor/bin/phanstatic build
```

## Preview

To preview your build in a browser:

```
php ./vendor/bin/phanstatic preview
```

This will start PHP's built-in server at `localhost` with port `8000` and makes the files from the `dist` available in a
browser.

You can override the default host (`--host`) and the port (`--port`) settings if necessary.

## Configuration

For starter all you need is a `content/pages` directory in the root of your project which contains `.php` files or a
`content/collections/{collectionName}` with `.md` files.

Optionally, you can place a configuration file under `content/config.php` which must return a `Config` object:

```php
use Terdelyi\Phanstatic\Config\ConfigBuilder;

return (new ConfigBuilder)
    ->setBaseUrl(getenv('BASE_URL'))
    ->setTitle('My super-fast static site')
    ->addCollection(
        key: 'posts',
        title: 'Posts',
        slug: 'posts',
        pageSize: 10
    )
    ->build();
```

## Example content structure

None of these files are mandatory. Phanstatic and its builders will look for the existing folders and configuration
and use them if they're available.

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

The contents of the assets folder will be copied to the `dist/assets` folder on build as it is and will be available
from the url `/assets`.

## Contributing

Bugfixes are highly welcome, but this package is in its early stages, and I'm planning to add new features carefully.