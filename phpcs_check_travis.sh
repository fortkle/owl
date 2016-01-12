#!/usr/bin/env bash

set -eu

echo "********************"
echo "* exec check       *"
echo "********************"
set +e
vendor/bin/phpcs ./app --report=checkstyle --report-file=phpcs.result.xml --standard=./phpcs.xml
set -e

echo "********************"
echo "* checkstyle       *"
echo "********************"
cat phpcs.result.xml | checkstyle_filter-git diff origin/master | saddler report --require saddler/reporter/github --reporter Saddler::Reporter::Github::PullRequestReviewComment
cat phpcs.result.xml | checkstyle_filter-git diff origin/master | saddler report --require saddler/reporter/github --reporter Saddler::Reporter::Github::CommitReviewComment

