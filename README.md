# Owl [![Build Status](https://travis-ci.org/owl/owl.svg?branch=master)](https://travis-ci.org/owl/owl)

Owl is a simple team collaboration tool.
This software is released under the MIT License, see LICENSE.txt.

![screenshot](https://raw.githubusercontent.com/wiki/fortkle/owl/images/owl_screenshot.png)

## Feature

- Markdown
- Syntax Highlight
- Like button
- Comment
- Favorite
- Tags
- Full Text Search
- Item Publishing Settings (Public, Limited, Only Me)
- Mail Notification
- Export data as .md files (YAML front matter)

## Requirements

- PHP 5.4
- SQLite 3
- npm

# How to setup
1.Clone the project

```
$ git clone https://github.com/owl/owl.git
$ cd owl
```

2.execute setup shell

```
$ sh ./setup_app.sh
```

3.Access the page

```
php artisan serve --host 0.0.0.0 --port 3000
```

access http://localhost:3000

You can sign in as an owner with id:admin pw:password

## Test
Behat acceptance test
(after execute `setup_app.sh`)

```
$ vendor/bin/behat --colors
```
