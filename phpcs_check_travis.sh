#!/usr/bin/env bash

set -eu

echo "********************"
echo "* install gems     *"
echo "********************"
gem install --no-document checkstyle_filter-git saddler saddler-reporter-github

echo "********************"
echo "* exec check       *"
echo "********************"
set +e
git diff --name-only --diff-filter=ACMR origin/master...HEAD | grep ".php" | xargs vendor/bin/phpcs --standard=./phpcs.xml --report=checkstyle --report-file=phpcs.result.xml
set -e

echo "********************"
echo "* checkstyle       *"
echo "********************"
cat phpcs.result.xml | saddler report --require saddler/reporter/github --reporter Saddler::Reporter::Github::PullRequestReviewComment

