#!/usr/bin/env bash

# Move to plug-in root
if [[ `pwd` == */bin ]]
then
	pushd ../ > /dev/null
	LPWOOTRK_RESTORE_DIR=true
else
	LPWOOTRK_RESTORE_DIR=false
fi

if [ ! -z "${WP_I18N_LIB+xxx}" ] || [ ! -d "$WP_I18N_LIB" ]; then
	WP_I18N_LIB="/usr/lib/wpi18n"
fi

if [ $# -lt 1 ]; then
	LPWOOTRK_PLUGIN_DIR=`pwd`
else
	LPWOOTRK_PLUGIN_DIR="$1"
fi

if [ -z "$2" ]; then
	LPWOOTRK_TEXT_DOMAIN=""
else
	LPWOOTRK_TEXT_DOMAIN=$2
fi

if [[ ! $LPWOOTRK_TEXT_DOMAIN ]]
then
	LPWOOTRK_TEXT_DOMAIN="livepayments-wootracker"
fi

wp i18n make-pot "$LPWOOTRK_PLUGIN_DIR" "$LPWOOTRK_PLUGIN_DIR/lang/$LPWOOTRK_TEXT_DOMAIN.pot" --slug="livepayments-wootracker" --domain=$LPWOOTRK_TEXT_DOMAIN --exclude="build,bin,assets,data,.github,.vscode,help"

if [ "$LPWOOTRK_RESTORE_DIR" = true ]
then
	popd > /dev/null
fi