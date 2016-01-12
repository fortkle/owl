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
echo "* save outputs     *"
echo "********************"

cat phpcs.result.xml
echo "ほげ"

echo "********************"
echo "* select reporter  *"
echo "********************"

if [ -z "${TRAVIS_PULL_REQUEST}" ]; then
    # when not pull request
    REPORTER=Saddler::Reporter::Github::CommitReviewComment
else
    REPORTER=Saddler::Reporter::Github::PullRequestReviewComment
fi

echo "********************"
echo "* checkstyle       *"
echo "********************"
cat phpcs.result.xml \
    | checkstyle_filter-git diff origin/master \
    | saddler report --require saddler/reporter/github --reporter $REPORTER

echo "********************"
echo "* PMD              *"
echo "********************"
cat phpmd.result.xml \
    | pmd_translate_checkstyle_format translate \
    | checkstyle_filter-git diff origin/master \
    | saddler report --require saddler/reporter/github --reporter $REPORTER
