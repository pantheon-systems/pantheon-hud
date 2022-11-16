# Contributing

This plugin is under active development on GitHub:

[https://github.com/pantheon-systems/pantheon-hud](https://github.com/pantheon-systems/pantheon-hud)

Please feel free to file issues there. Pull requests are also welcome!

## Testing

You may notice there are three sets of tests running:

* [PHPCS](https://github.com/squizlabs/PHP_CodeSniffer) to detect violations of `wp-coding-standards/wpcs` coding standards.
* [PHPUnit](https://phpunit.de/) test suite.
* [Behat](http://behat.org/) test suite against a Pantheon site, to ensure the plugin's compatibility with the Pantheon platform.

These test suites can be run locally, with a varying amount of setup.

PHPUnit requires the [WordPress PHPUnit test suite](https://make.wordpress.org/core/handbook/testing/automated-testing/phpunit/), and access to a database with name `wordpress_test`. If you haven't already configured the test suite locally, you can run `bash bin/install-wp-tests.sh wordpress_test root '' localhost`.

Behat requires a Pantheon site. Once you've created the site, you'll need [install Terminus](https://github.com/pantheon-systems/terminus#installation), and set the `TERMINUS_TOKEN`, `TERMINUS_SITE`, and `TERMINUS_ENV` environment variables. Then, you can run `./bin/behat-prepare.sh` to prepare the site for the test suite.

Note that dependencies are installed via Composer and the `vendor` directory is not committed to the repository. You will need to run `composer install` locally for the plugin to function. You can read more about Composer [here](https://getcomposer.org)

## Release Process

1. Update plugin version in `package.json`, `readme.txt`, and `pantheon-hud.php`.
2. Run `npm install && npm run readme` to install npm dependencies and update the README.md file.
3. Create a PR against the `master` branch.
4. After all tests pass and you have received approval from a CODEOWNER (including resolving any merge conflicts), merge the PR into `master`.
5. Pull `master` locally, create a new tag, and push up.
6. Confirm that the necessary assets are present in the newly created tag, and test on a WP install if desired.
7. Publish a new release using the latest tag. Publishing a release will kick off `wordpress-plugin-deploy.yml` and release the plugin to wp.org. If you do not want a tag to be publised to wp.org, do not publish a release from it.