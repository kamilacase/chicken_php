#!/bin/bash
php php-cs-fixer-v2.phar \
#	--dry-run \
	--diff --diff-format=udiff \
	fix . 
