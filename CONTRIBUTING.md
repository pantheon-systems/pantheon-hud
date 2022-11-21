# Contributing

This plugin is under active development on GitHub:

[https://github.com/pantheon-systems/pantheon-hud](https://github.com/pantheon-systems/pantheon-hud)

Please feel free to file issues there. Pull requests are also welcome!

## Workflow

The `develop` branch is the development branch which means it contains the next version to be released. `master` contains the corresponding stable development version. Always work on the `develop` branch and open up PRs against `develop`.

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

1. Starting from `develop`, cut a release branch named `release_X.Y.Z` containing your changes.
1. Update plugin version in `package.json`, `README.md`, `readme.txt`, and `pantheon-hud.php`.
1. Update the Changelog with the latest changes.
1. Create a PR against the `master` branch.
1. After all tests pass and you have received approval from a CODEOWNER (including resolving any merge conflicts), merge the PR into `master`.
1. Pull `master` locally, create a new tag, and push up.
1. Confirm that the necessary assets are present in the newly created tag, and test on a WP install if desired.
1. Create a [new release](https://github.com/pantheon-systems/pantheon-hud/releases/new), naming the release with the new version number, and targeting the tag created in the previous step. Paste the release changelog from `CHANGELOG.md` into the body of the release and include a link to the closed issues if applicable.
1. Wait for the [_Release pantheon-hud plugin to wp.org_ action](https://github.com/pantheon-systems/pantheon-hud/actions/workflows/wordpress-plugin-deploy.yml) to finish deploying to the WordPress.org repository. If all goes well, users with SVN commit access for that plugin will receive an emailed diff of changes.
1. Check WordPress.org: Ensure that the changes are live on https://wordpress.org/plugins/pantheon-hud/. This may take a few minutes.