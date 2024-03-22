Phanstatic 👷‍
==========

Phanstatic is a simple, lightweight, CLI based static site generator written in PHP. There are no frameworks or template engines,
just simple pages written in pure PHP code and markdown files. During the building process
all of your content will be transformed into static HTML files ready to deploy.

## Install

To create a new project just run

```
composer create-project terdelyi/phanstatic:dev-develop
```

## Build

To build static files from your `content` directory run the following command in your root folder:

```
php ./vendor/bin/phanstatic build
```

## Preview

To preview your build in a browser:

```
php ./vendor/bin/phanstatic preview
```

This will start PHP's built-in server at `localhost` with port `8000` and makes the files from the `dist` available in a browser.

You can override the default host (`--host`) and the port (`--port`) settings if necessary.