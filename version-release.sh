#!/bin/bash

# Tag and release a new version of the plugin.
#
# This script takes a local git repo directory and syncs it to an SVN working directory for use as a
# new version per WordPress plugin development practices.  In this set-up, use the local git directory normally for
# development: branch, tag, and commit however often you want.  When you are ready to release a new version of the
# plugin, run this script.  It will copy the contents of this folder to an SVN folder (defined by the SVNDIR variable).
# It will handle incrementing the version number, creating a tag (in both Git and SVN), and it will add or remove files
# from SVN to match the contents of this git directory.
#
#
# USAGE
# -------------
#   bash to-svn.sh 1.2.3
#
# 1. "export" the current directory to a temp directory
# 2. merge the temp directory in with the managed SVN directory
# 3. commit the SVN code


#####################################################
# CONFIGURATION
#####################################################
# Temp dir -- will be deleted!
# Omit trailing slash.
# Don't use spaces or weird chars in the dir names.
TMPDIR="/tmp/cctm"

# Absolute path or path relative to this dir, omit trailing slash
# This dir should contain the SVN trunk.
SVNDIR="../../cctm.svn"

#####################################################
# END CONFIG
#####################################################
# Most bash variables are global, but not the arguments
# so we pass them to another variable.
VERSION=$1;

#####################################################
# SUBROUTINES
#####################################################

function verify_version {
    if [[ -z $VERSION ]]; then
        echo 'Missing required VERSION parameter.';
        exit 1;
    fi

    if [[ ! $VERSION =~ [[:digit:]]+\.[[:digit:]]+\.[[:digit:]]+ ]]; then
        echo 'Invalid version number';
        exit 2;
    fi

    if [[ $VERSION < "1.0.0" ]]; then
        echo 'New version must be greater than existing versions!';
        exit 3;
    fi
}

# Resolve SVNDIR to an absolute dir
# One-liner similar to PHP's __DIR__ -- omits trailing slash
function get_abs_pwd {
    SRCDIR="$( cd "$(dirname "$0")" ; pwd -P )"
}

# Will remove files from SVN control if they are not in the 
# Git repo
function sync_svn {
    
    # Get relative paths
    cd $SVNDIR
    SVNFILES=`find * -type f`

    while read -r LINE; do
    
        TESTFILE=${SRCDIR}/${LINE}
        if [[ ! -e $TESTFILE ]]; then
            echo "${TESTFILE} does NOT exist";
            svn delete ${TESTFILE};
        fi
    
    done <<< "$SVNFILES"
}

# Update the plugin version
function update_version {
    cd $SRCDIR;
    echo "Updating the version to ${VERSION}";
    cd $TMPDIR;
    sed -i '' "s/Version:.*/Version: ${VERSION}/" index.php
    sed -i '' "s/Stable tag:.*/Stable tag: ${VERSION}/" readme.txt
    sed -i '' "s/Version:.*/Version: ${VERSION}/" readme.txt
    sed -i '' "s/.*const version = .*/    const version = '${VERSION}'\;/" includes/CCTM.php
}

# Make sure the SVNDIR is actually a working copy.
function verify_svn_dir
{
    # Remember: the SVNDIR is relative to this file
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
}

function update_svn {
    cd $SVNDIR;
    svn update
}



# Prep the tmp dir
function prep_tmp_dir
{
    rm -rf $TMPDIR;
    # We want rsync to use the trailing slash for referencing this directory, otherwise a sub-dir will be created
    rsync -arz ${SRCDIR}/ $TMPDIR --exclude=".git" --exclude=".gitignore" --exclude="*.sh" --exclude=".idea";
}

# Tag the version
function tag_version
{

    # Move it all from tmp into svn
    # Alternatively, get all files:
    # find . -type f
    # Then do svn delete of files in the svn directory which have been removed from git directory
    echo "Syncing from ${TMPDIR}/ to $SVNDIR"
    rsync -arvz ${TMPDIR}/ $SVNDIR;

    # Tag the Git repo
    cd $SRCDIR;
    git tag -a v${1} -m "Tagging version ${1} via automated script"
    git push origin v${1}

    # Commit
    svn commit ${SVNDIR} -m "Committing changes for version ${1} via automated script"

    # SVN copy from trunk to tag
    svn copy ${SVNTRUNK} ${SVNTAGS}/${1} -m "Tagging version ${1} via automated script"
}

verify_version;


# SUCCESS
exit 0;
