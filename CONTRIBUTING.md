# Contributing

Contributions are **welcome** and will be fully **credited**.

We accept contributions via Pull Requests on [Github](https://github.com/craftsmancoding/custom-content-type-manager/pulls).


## Pull Requests

- **[PSR-2 Coding Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)** - The easiest way to apply the conventions is to install [PHP Code Sniffer](http://pear.php.net/package/PHP_CodeSniffer).

- **Add tests!** - Your patch won't be accepted if it doesn't have tests.

- **Document any change in behaviour** - Make sure the `README.md` and any other relevant documentation are kept up-to-date.

- **Consider our release cycle** - We try to follow [SemVer v2.0.0](http://semver.org/) as it applies to WordPress.

- **Create feature branches** - Don't ask us to pull from your master branch.

- **One pull request per feature** - If you want to do more than one thing, send multiple pull requests.

- **Send coherent history** - Make sure each individual commit in your pull request is meaningful. If you had to make multiple intermediate commits while developing, please squash them before submitting.


## Example Contribution

1. Log into your Github account and fork the repo.

2. Clone your fork to your local work environment.

```bash
git clone git@github.com:myuser/custom-content-type-manager.git
```

3. Add the upstream repo to ensure that your fork can receive any changes that occur in the original repo while you are doing work.

``` bash
git add remote -f upstream git@github.com:craftsmancoding/custom-content-type-manager.git
git branch --set-upstream-to=upstream/master
```

**This is very important!** You must connect to the upstream repository to get the latest code from the starting branch (usually `master`), e.g.

```bash
git checkout master
git pull
```

Make sure you switch back to your branch after pulling in changes!  Use `git checkout name-of-branch` to switch.


4. Create a branch for the bug-fix or feature that you are working on.  The name of the branch is up to you, but the important thing is to create a unique named branch per pull request (PR).

```bash
git checkout -b pr/bugfix upstream/master
```

In this example, we're using a branch name beginning with "pr" so it's clear the branch was initiated as a pull request, but you can name your branches however you'd like.

5. Write code!  Here's where you actually make changes, fix things, add things, etc.

If you made the mistake of making changes to the code _before_ you created a dedicated branch, or you inadvertently forgot to switch to the separate branch, you can fix it.  For example, let's say you were accidentally still on the master branch when you made your changes.  Then you can run this from the master branch:

```bash
git add --all 
git stash
git checkout -b bugfix
git stash pop
```

This trick involves stashing your changes and then "popping" them out of the stash queue.


6. Commit and push your code once the bug is fixed or feature complete.

```bash
git commit -m "A description of the bug being fixed or the feature being added"
git push origin pr/bugfix
```

7. Back in the Github GUI, create a pull request (PR).  The PR should target the upstream branch you started from (e.g. master)

8. Wash-Rinse-Repeat!  Why stop now?  You can now repeat this process and add another feature or fix another bug.  Remember: we want one feature or one bug-fix per PR!  So don't be afraid to submit these frequently.  Once you've submitted a pull request, you can wash-rinse-repeat and add more features.

```bash
git checkout master
git pull
git checkout -b pr/new-bugfix upstream/master
```

Or more condensed, you could do this:

```bash
git fetch upstream && git checkout -b new-bugfix upstream/master
```


## Running Tests

We are moving to PHPUnit for testing.  Run tests as follows:

``` bash
$ phpunit
```


**Happy coding**!