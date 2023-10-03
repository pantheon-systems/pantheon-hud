# Contributing

This plugin is under active development on GitHub:

[https://github.com/pantheon-systems/pantheon-hud](https://github.com/pantheon-systems/pantheon-hud)

Please feel free to file issues there. Pull requests are also welcome!

## Workflow

The `develop` branch is the development branch which means it contains the next version to be released. `main` contains the corresponding stable development version. Always work on the `develop` branch and open up PRs against `develop`.

We prefer to squash commits (i.e. avoid merge PRs) from a feature branch into `develop` when merging, and to include the PR # in the commit message. PRs to `develop` should also include any relevent updates to the changelog in readme.txt. For example, if a feature constitutes a minor or major version bump, that version update should be discussed and made as part of approving and merging the feature into `develop`.

`develop` should be stable and usable, though possibly a few commits ahead of the public release on wp.org.

The `master` branch matches the latest stable release deployed to [wp.org](wp.org).

## Testing

You may notice there are three sets of tests running:

* [PHPCS](https://github.com/squizlabs/PHP_CodeSniffer) to detect violations of [Pantheon WP Coding Standards](https://github.com/pantheon-systems/Pantheon-WP-Coding-Standards).
* [PHPUnit](https://phpunit.de/) test suite.
* [Behat](http://behat.org/) test suite against a Pantheon site, to ensure the plugin's compatibility with the Pantheon platform.

These test suites can be run locally, with a varying amount of setup.

PHPUnit requires the [WordPress PHPUnit test suite](https://make.wordpress.org/core/handbook/testing/automated-testing/phpunit/), and access to a database with name `wordpress_test`. If you haven't already configured the test suite locally, you can run `bash bin/install-wp-tests.sh wordpress_test root '' localhost`.

Behat requires a Pantheon site. Once you've created the site, you'll need [install Terminus](https://github.com/pantheon-systems/terminus#installation), and set the `TERMINUS_TOKEN`, `TERMINUS_SITE`, and `TERMINUS_ENV` environment variables. Then, you can run `./bin/behat-prepare.sh` to prepare the site for the test suite.

Note that dependencies are installed via Composer and the `vendor` directory is not committed to the repository. You will need to run `composer install` locally for the plugin to function. You can read more about Composer [here](https://getcomposer.org)

## Release Process

1. Make a release commit:
1. From `develop`, checkout a new branch `release_X.Y.Z`.
    * Drop the `-dev` from the version number in `package.json`, `README.md`, `readme.txt`, and `pantheon-hud.php`.
    * Update the "Latest" heading in the changelog to the new version number with the date
    * Commit these changes with the message `Release X.Y.Z`
    * Push the release branch up.
1. Open a Pull Request to merge `release_X.Y.Z` into `main`. Your PR should consist of all commits to `develop` since the last release, and one commit to update the version number. The PR name should also be `Release X.Y.Z`.
1. After all tests pass and you have received approval from a CODEOWNER (including resolving any merge conflicts), merge the PR into `main`.
1. Pull `main` locally, create a new tag, and push up.
1. Confirm that the necessary assets are present in the newly created tag, and test on a WP install if desired.
1. Create a [new release](https://github.com/pantheon-systems/pantheon-hud/releases/new) using the tag created in the previous steps, naming the release with the new version number, and targeting the tag created in the previous step. Paste the release changelog from the `Changelog` section of [the readme](readme.txt) into the body of the release, including the links to the closed issues if applicable.
1. Wait for the [_Release pantheon-hud plugin to wp.org_ action](https://github.com/pantheon-systems/pantheon-hud/actions/workflows/wordpress-plugin-deploy.yml) to finish deploying to the WordPress.org plugin repository. If all goes well, users with SVN commit access for that plugin will receive an emailed diff of changes.
1. Check WordPress.org: Ensure that the changes are live on [the plugin repository](https://wordpress.org/plugins/pantheon-hud/). This may take a few minutes.
1. Following the release, prepare the next dev version with the following steps:
    * `git checkout develop`
    * `git rebase master`
    * Update the version number in all locations, incrementing the version by one patch version, and add the `-dev` flag (e.g. after releasing `1.2.3`, the new verison will be `1.2.4-dev`)
    * Add a new `** Latest **` heading to the changelog
    * `git add -A .`
    * `git commit -m "Prepare X.Y.X-dev"`
    * `git push origin develop`
