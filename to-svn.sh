#!/bin/bash

# This is a helper script that takes our git-controlled code here and ultimately commits it to the SVN repo for this
# plugin (defined below as the SVNDIR).
#
# USAGE
# -------------
# Basic usage:
#   bash to-svn.sh
#
# Tag and release new version
#   bash to-svn.sh 1.2.3
#
# 1. "export" the current directory to a temp directory
# 2. merge the temp directory in with the managed SVN directory
# 3. commit the SVN code


#################
# CONFIGURATION
#################
# Temp dir -- will be deleted!
# Omit trailing slash.
# Don't use spaces or weird chars in the dir names.
TMPDIR="/tmp/cctm"

# Relative to this dir
SVNDIR="../../cctm.svn"


# Resolve SVNDIR to an absolute dir
# One-liner similar to PHP's __DIR__ -- omits trailing slash
DIR="$( cd "$(dirname "$0")" ; pwd -P )"

cd $DIR;
cd $SVNDIR;
SVNDIR=`pwd`;
SVNTRUNK=`svn info ${SVNDIR} | grep 'URL:' | sed -e 's/URL: //'`
SVNTAGS=$(echo $SVNTRUNK | sed -e 's/trunk/tags/')

# Verify Data
if [[ -d $SVNDIR ]] && [[ -d ${SVNDIR}/.svn ]]; then
    echo "SVN repository directory detected at ${SVNDIR}"
else
    echo "${SVNDIR} is not an SVN working copy.";
    exit 1;
fi


# Prep the tmp dir
rm -rf $TMPDIR;
# We want rsync to use the trailing slash for referencing this directory, otherwise a sub-dir will be created
rsync -arz ${DIR}/ $TMPDIR --exclude=".git" --exclude=".gitignore" --exclude="*.sh" --exclude=".idea";

# Update the version?
if [[ -z $1 ]]; then
    # We need something in here for the code block to be valid
    echo "Version number NOT updated."

    # Move from tmp into svn
    rsync -arz ${TMPDIR}/ $SVNDIR;

    cd $SVNDIR;
    svn commit -m "Storing changes via automated script"

else
    echo "Updating the version to ${1}";
    cd $TMPDIR;
    sed -i '' "s/Version:.*/Version: ${1}/" index.php
    sed -i '' "s/Stable tag:.*/Stable tag: ${1}/" readme.txt
    sed -i '' "s/Version:.*/Version: ${1}/" readme.txt
    sed -i '' "s/.*const version = .*/    const version = '${1}'\;/" includes/CCTM.php

    # Move it all from tmp into svn
    rsync -arvz ${TMPDIR}/ $SVNDIR;

    # Tag the Git repo
    cd $DIR;
    git tag -a v${1} -m "Tagging version ${1} via automated script"
    git push origin v${1}

    # Commit
    svn commit ${SVNDIR} -m "Committing changes for version ${1} via automated script"

    # SVN copy from trunk to tag
    svn copy ${SVNTRUNK} ${SVNTAGS}/${1} -m "Tagging version ${1} via automated script"
fi


# SUCCESS
exit 0;