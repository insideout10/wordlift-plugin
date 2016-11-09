# Contributing to WordLift

Hello and welcome 🖖! We'd love for you to contribute to our source code and to make WordLift even better :tada:!

Here are the guidelines we'd like you to follow :pray::

 - [Question or Problem?](#question)
 - [Issues and Bugs](#issue)
 - [Submission Guidelines](#submit)
 - [Coding Rules](#rules)
 - [Releases and Commit Workflow](#releases)
 - [Further Info](#info)
 
## <a name="question"></a> Got a Question or Problem?

If you have questions about how to use WordLift, please direct these to [StackOverflow][stackoverflow] or to [GitHub issues][github-issues]. We are also available on [Gitter][gitter].

## <a name="issue"></a> Found an Issue?

If you find a bug in the source code or a mistake in the documentation, you can help us by
submitting an issue to our [GitHub Repository][github]. Even better you can submit a Pull Request
with a fix.

**Please see the [Submission Guidelines](#submit) below.**

### Submitting an Issue
Before you submit your issue search the archive, maybe your question was already answered.

If your issue appears to be a bug, and hasn't been reported, open a new issue. Help us to maximize
the effort we can spend fixing issues and adding new features, by not reporting duplicate issues.
Providing the following information will increase the chances of your issue being dealt with
quickly:

* **Overview of the Issue**
* **Motivation for or Use Case** - explain why this is a bug for you
* **WordLift Version(s)**
* **Browsers and Operating System** - is this a problem with all browsers or only specific ones?
* **Reproduce the Error** - provide an unambiguous set of steps to reproduce the error.
* **Related Issues** - has a similar issue been reported before?
* **Suggest a Fix**

## <a name="rules"></a> Coding Rules

To ensure consistency throughout the source code, keep these rules in mind as you are working:

* WordLift is a WordPress plugin therefore it abides by [WordPress coding standards][wp-coding-standards]:
  * [PHP coding standards][wp-coding-php-standards]
  * [JavaScript coding standards][wp-coding-javascript-standards]
  * [HTML coding standards][wp-coding-html-standards]
  * [CSS coding standards][wp-coding-css-standards]
  * [Documentation standards][wp-documentation-standards]
* All features or bug fixes **must be tested** according to the [Testing guidelines][wp-testing]:
  * a [sample test class][wl-sample-test]
  * a convenient [unit test class][wl-unit-test-case]
  * a convenient [ajax test class][wl-ajax-test-case]
* The project structure is based on the [WordPress Plugin Boilerplate][wp-plugin-boilerplate] with some legacy code still floating around (we **@deprecate** old code that we're going to remove soon)
* If you're using [PhpStorm][phpstorm] for development, consider setting the [WordPress code style][phpstorm-wp-code-style]
* We use [Code Climate][code-climate] and [Scrutinizer][scrutinizer] to monitor the quality of code and code style consistency: check there for issues and fixes (**we're aiming to greatly improve our score**)
* We believe in [DRY][dry] and [KISS][kiss] and somewhat in ["Don't Reinvent the Wheel"][dont-reinvent-the-wheel]

## <a name="releases"></a> Commit and Release Workflow

We use [Semantic Versioning][semantic-versioning] and abide by the [GitFlow workflow][gitflow]: development happens
in feature branches created from the develop branch. Once we're ready for a release, we merge the feature branches
to develop and create a release branch which transitions into QA.

Once the released is ready, we merge it to master and push it to [wordpress.org][wp-plugins-wordlift].

Fix to [bugs][wl-open-bugs] are fixed in hotfixes branches created from the master branch to which they're merged back
(and then to the develop branch).

We have very precise rules over how our git commit messages can be formatted.  This leads to **more
readable messages** that are easy to follow when looking through the **project history**.

In summary:
* new features are developed in feature branches:
  * forked off the develop branch
  * the name is **feature/xyz_title_of_the_issue**
* fixes are developed in hotfix branches
  * forked off the master branch
  * the name is **hotfix/xyz_title_of_the_issue**

The commit message rule is very simple: 'see #xyz: small description of the change', where *xyz* is the issue
number, e.g. 'see #1: updated changelog'.

Once work is finished on an issue, **don't close issues**: label it **resolved**. Only after the work is published on
wordpress.org we close the issue (typically the person that publishes the plugin to [wordpress.org][wp-plugins-wordlift]
will close the issues).

## <a name="info"></a> Further Info

For further info please head to [WordLift's web site][wordlift].

[code-climate]: https://codeclimate.com/github/insideout10/wordlift-plugin
[dont-reinvent-the-wheel]: https://blog.codinghorror.com/dont-reinvent-the-wheel-unless-you-plan-on-learning-more-about-wheels/
[dry]: https://en.wikipedia.org/wiki/Don%27t_repeat_yourself
[gitflow]: https://www.atlassian.com/git/tutorials/comparing-workflows/gitflow-workflow
[github]: https://github.com/insideout10/wordlift-plugin
[github-issues]: https://github.com/insideout10/wordlift-plugin/issues
[gitter]: https://gitter.im/wordlift/wordlift
[kiss]: https://en.wikipedia.org/wiki/KISS_principle
[phpstorm]: https://www.jetbrains.com/phpstorm/
[phpstorm-wp-code-style]: https://www.jetbrains.com/help/phpstorm/2016.2/code-style-php.html
[scrutinizer]: https://scrutinizer-ci.com/g/insideout10/wordlift-plugin/
[semantic-versioning]: http://semver.org/
[stackoverflow]: http://stackoverflow.com/questions/tagged/wordlift
[wl-sample-test]: https://github.com/insideout10/wordlift-plugin/blob/develop/tests/test-entity-service.php
[wl-ajax-test-case]: https://github.com/insideout10/wordlift-plugin/blob/develop/tests/class-wordlift-ajax-unit-test-case.php
[wl-open-bugs]: https://github.com/insideout10/wordlift-plugin/issues?q=is%3Aissue+is%3Aopen+label%3Abug
[wl-unit-test-case]: https://github.com/insideout10/wordlift-plugin/blob/develop/tests/class-wordlift-unit-test-case.php
[wordlift]: https://wordlift.io
[wp-coding-standards]: https://make.wordpress.org/core/handbook/best-practices/coding-standards/
[wp-coding-php-standards]: https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/
[wp-coding-javascript-standards]: https://make.wordpress.org/core/handbook/best-practices/coding-standards/javascript/
[wp-coding-html-standards]: https://make.wordpress.org/core/handbook/best-practices/coding-standards/html/
[wp-coding-css-standards]: https://make.wordpress.org/core/handbook/best-practices/coding-standards/css/
[wp-documentation-standards]: https://make.wordpress.org/core/handbook/best-practices/inline-documentation-standards/php/
[wp-plugin-boilerplate]: https://github.com/DevinVinson/WordPress-Plugin-Boilerplate
[wp-plugins-wordlift]: https://wordpress.org/plugins/wordlift/
[wp-testing]: https://make.wordpress.org/core/handbook/testing/automated-testing/phpunit/
