#!/bin/bash

TMPDIR=/tmp/atomik

mkdir -p $TMPDIR

(
cd $TMPDIR
composer create-project atomik/skeleton .
)

cd $(dirname $TMPDIR) && zip -r atomik.zip $(basename $TMPDIR)
