# Contributing

This plugin is under active development on GitHub:

[https://github.com/pantheon-systems/pantheon-hud](https://github.com/pantheon-systems/pantheon-hud)

Please feel free to file issues there. Pull requests are also welcome!

## Workflow

The `main` branch is the development branch which means it contains the next version to be released. `release` contains the corresponding stable development version. Always work on the `main` branch and open up PRs against `main`.

We prefer to squash commits (i.e. avoid merge PRs) from a feature branch into `main` when merging, and to include the PR # in the commit message. PRs to `main` should also include any relevent updates to the changelog in readme.txt. For example, if a feature constitutes a minor or major version bump, that version update should be discussed and made as part of approving and merging the feature into `main`.

`main` should be stable and usable, though possibly a few commits ahead of the public release on wp.org.

The `release` branch matches the latest stable release deployed to [wp.org](wp.org).

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

1. Merge your feature branch into `main` with a PR. This PR should include any necessary updates to the changelog in readme.txt and README.md. Features should be squash merged. 
1. From main, checkout a new branch `release_X.Y.Z`.
1. Make a release commit: 
    * In `package.json`, `README.md`, `readme.txt`, and `pantheon-hud.php`, remove the `-dev`  from the version number. 
    * For the README files, the version number must be updated both at the top of the document as well as the changelog. 
    * Add the date to the  `** X.Y.Z **` heading in the changelogs in `README.md`, `readme.txt`, and any other appropriate location. 
    * Commit these changes with the message `Release X.Y.Z`
    * Push the release branch up.
1. Open a pull request to merge `release_X.Y.Z` into `release`. Your PR should consist of all commits to `main` since the last release, and one commit to update the version number. The PR name should also be `Release X.Y.Z`.
1. After all tests pass and you have received approval from a CODEOWNER (including resolving any merge conflicts), merge the PR into `release`. Use a "merge" commit, do no not rebase or squash.
1. After merging to the `release` branch, a draft Release will be automatically created by the build-tag-release workflow. This draft release will be automatically pre-filled with release notes. 
1. Confirm that the necessary assets are present in the newly created tag, and test on a WP install if desired. 
1. Review the release notes, making any necessary changes, and publish the release. 
1. Wait for the Release pantheon-hud plugin to wp.org action to finish deploying to the WordPress.org plugin repository. 1. If all goes well, users with SVN commit access for that plugin will receive an email with a diff of the changes. 
1. Check WordPress.org: Ensure that the changes are live on the plugin repository. This may take a few minutes.
1. Following the release, prepare the next dev version with the following steps:
    * `git checkout release`
    * `git pull origin release`
    * `git checkout main`
    * `git rebase release`
    * Update the version number in all locations, incrementing the version by one patch version, and add the `-dev` flag (e.g. after releasing `1.2.3`, the new verison will be `1.2.4-dev`)
    * Add a new `** X.Y.Z-dev **` heading to the changelog
    * `git add -A .`
    * `git commit -m "Prepare X.Y.Z-dev"`
    * `git checkout -b release-XYZ-dev` (we need to test this commit but we will _not_ PR it into main)
    * _Wait for all required status checks to pass in CI._
    * `git push origin main`
