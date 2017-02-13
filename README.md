# terms-archive
This plugins creates archive pages for terms in public taxonomies.

## Requirements
WordPress 4.7 or later, PHP 5.4 or later and Composer.

## Installation
```
$ composer require ssnepenthe/terms-archive
```

OR

```
$ cd /path/to/project/wp-content/plugins
$ git clone git@github.com:ssnepenthe/terms-archive.git
$ cd terms-archive
$ composer install
```

## Usage
To start, you need to add theme support for any taxonomy you wish to add an archive page for. Any of the following are valid ways to do so:

```
add_theme_support( 'ta-terms-archive' ); // Adds support for all public taxonomies.
add_theme_support( 'ta-terms-archive', 'category' ); // Adds support for the category taxonomy.
add_theme_support( 'ta-terms-archive', [ 'category', 'post_tag' ] ); // Adds support for categories and tags.
```

Additionally, you need to create at least one of the following template files:

* ta-terms-archive-{$taxonomy}.php
* ta-terms-archive.php

Where $taxonomy is the string given as the first param to `register_taxonomy()`.

A number of functions are available to aid in theme development and can be found in `inc/functions.php`. They should feel very familiar if you have any experience developing WordPress themes.

Check `examples/tf-child/` for an example implementation in the form of a `twentyfifteen` child theme.

Once you have completed the above and activated the plugin, you will be able to view lists of all terms which have associated posts and are in a public taxonomy from the frontend.

You can find these archive pages at `{home url}/{wp rewrite front}/{tax rewrite slug}`.

For example, if your site lives at `https://example.com` and your permalink structure is set to `/blog/%postname%/`, you will be able to access categories at `https://example.com/blog/category/`.

If you want to disable archive pages for specific taxonomies, you can do so by visiting `Settings > Terms Archive` and selecting the taxonomies you wish to disable.
