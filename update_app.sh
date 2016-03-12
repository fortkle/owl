#!/bin/bash
set -e

# ################################################################
#  function
# ################################################################
NORMAL=$(tput sgr0)
GREEN=$(tput setaf 2; tput bold)
YELLOW=$(tput setaf 3)
RED=$(tput setaf 1)

# echo red color string
function red() {
    echo "$RED$*$NORMAL"
}

# echo green color string
function green() {
    echo "$GREEN$*$NORMAL"
}

# echo yellow color string
function yellow() {
    echo "$YELLOW$*$NORMAL"
}

# echo normal string
function message() {
    echo "$1"
}

# check command exists
function command_exists() {
  command -v "$1" > /dev/null;
}

# check PHP version >= 5.4
function checkPhpVersion() {
    version=`php -v | awk 'NR == 1 {print $2}' | sed 's/\([0-9]*\)\.\([0-9]*\).\([0-9]*\)/\1 \2 \3/' | xargs printf "%02d%02d%02d" | xargs expr 1 \*`
    if [ $version -gt 50400 ]; then
        green "OK"
    else
        red "PHPが見つからないか、もしくは対応していないバージョンです。 (require PHP >= 5.4)"
        exit 1
    fi
}


# ################################################################
#  check environment
# ################################################################

message ""
message "実行環境をチェックしています..."
message ""

message "check PHP ..."
checkPhpVersion
message ""

message "check PHP module ..."
count=`php -m | grep mcrypt | wc -l`
if [ $count = 0 ]; then
    red "PHP module(php-mcrypt)が見つかりません。（require php-mcrypt）"
else
    green "mcript...OK"
fi
message ""
count=`php -m | grep openssl | wc -l`
if [ $count = 0 ]; then
    red "PHP module(openssl)が見つかりません。（require openssl）"
else
    green "openssl...OK"
fi
message ""
count=`php -m | grep mbstring | wc -l`
if [ $count = 0 ]; then
    red "PHP module(mbstring)が見つかりません。（require mbstring）"
else
    green "mbstring...OK"
fi
message ""
count=`php -m | grep pdo | wc -l`
if [ $count = 0 ]; then
    red "PHP module(pdo)が見つかりません。（require pdo）"
else
    green "pdo...OK"
fi
message ""

message "check SQlite ..."
if ! command_exists sqlite3 ; then
    red "SQLiteが見つからないか、もしくは対応していないバージョンです。（require SQLite 3）"
fi
green "OK"
message ""

message "check node.js ..."
if ! command_exists node ; then
    red "nodeが見つからないか、もしくは対応していないバージョンです。"
fi
green "OK"
message ""

message "check npm ..."
if ! command_exists npm ; then
    red "npmが見つからないか、もしくは対応していないバージョンです。"
fi
green "OK"
message ""


# ################################################################
#  main
# ################################################################

message ""
message "アップデートしています..."
message ""

# check .env file
if [ ! -e .env ]; then
    message "設定ファイル(.env)が見つかりませんでした。"
    message "必要に応じて.env.exampleをコピーしてください。"
    exit 1
fi

# update core files
git pull origin master

# download composer.phar
curl -sS https://getcomposer.org/installer | php

# install libraries
php composer.phar install

# database migration
php artisan migrate --seed --force

# run npm build
npm i
npm run build
