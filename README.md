runcommand/assign-featured-images
=================================

Assign featured images to posts that support thumbnails.

[![runcommand open source](https://runcommand.io/wp-content/themes/runcommand-theme/bin/shields/runcommand-open-source.svg)](https://runcommand.io/pricing/) [![Build Status](https://travis-ci.org/runcommand/assign-featured-images.svg?branch=master)](https://travis-ci.org/runcommand/assign-featured-images)

Quick links: [Using](#using) | [Installing](#installing) | [Support](#support)

## Using

~~~
wp assign-featured-images [--attachment=<attachment>] [--only-missing] [--dry-run]
~~~

**WARNING**: This command will irrevocably change your database. Please
make sure to `wp db export --tables=wp_postmeta` before running.

Default behavior is to randomly assign attachments as featured images
to all posts of post types that support thumbnails.

WXR import get mangled and only some of the posts in your dev environment
have featured images? Use `--only-missing` to only replace featured
images on posts where the existing value is missing or invalid.

**OPTIONS**

	[--attachment=<attachment>]
		Assign a specified attachment. Defaults to randomly-selected attachments.

	[--only-missing]
		Only replace featured images where existing value is missing or invalid.

	[--dry-run]
		Test the operation without performing database alterations.

## Installing

Installing this package requires WP-CLI v0.23.0 or greater. Update to the latest stable release with `wp cli update`.

Once you've done so, you can install this package with `wp package install runcommand/assign-featured-images`.

## Support

This WP-CLI package is free for anyone to use. Support, including usage questions and feature requests, is available to [paying runcommand customers](https://runcommand.io/pricing/).

Think you’ve found a bug? Before you create a new issue, you should [search existing issues](https://github.com/runcommand/sparks/issues?q=label%3Abug%20) to see if there’s an existing resolution to it, or if it’s already been fixed in a newer version. Once you’ve done a bit of searching and discovered there isn’t an open or fixed issue for your bug, please [create a new issue](https://github.com/runcommand/sparks/issues/new) with description of what you were doing, what you saw, and what you expected to see.

Want to contribute a new feature? Please first [open a new issue](https://github.com/runcommand/sparks/issues/new) to discuss whether the feature is a good fit for the project. Once you've decided to work on a pull request, please include [functional tests](https://wp-cli.org/docs/pull-requests/#functional-tests) and follow the [WordPress Coding Standards](http://make.wordpress.org/core/handbook/coding-standards/).

runcommand customers can also email [support@runcommand.io](mailto:support@runcommand.io) for private support.


