#!/usr/bin/env bash

# Move to plug-in root
if [[ `pwd` == */bin ]]
then
	pushd ../ > /dev/null
	LPWOOTRK_RESTORE_DIR=true
else
	LPWOOTRK_RESTORE_DIR=false
fi

# Store some stuff for later use
LPWOOTRK_CDIR=$(pwd)

LPWOOTRK_BUILD_ROOTDIR="$LPWOOTRK_CDIR/build"
LPWOOTRK_BUILD_OUTDIR="$LPWOOTRK_BUILD_ROOTDIR/output"
LPWOOTRK_BUILD_COMPATDIR="$LPWOOTRK_BUILD_ROOTDIR/compat-info"
LPWOOTRK_BUILD_TMPDIR="$LPWOOTRK_BUILD_ROOTDIR/tmp"

LPWOOTRK_VERSION=$(awk '{IGNORECASE=1}/Version:/{print $NF}' ./lpwootrk-plugin-main.php | awk '{gsub(/\s+/,""); print $0}')
LPWOOTRK_BUILD_NAME="lpwootrk-plugin-main.$LPWOOTRK_VERSION.zip"

# Ensure all output directories exist
ensure_out_dirs() {
	echo "Ensuring output directory structure..."

	if [ ! -d $LPWOOTRK_BUILD_ROOTDIR ]
	then
		mkdir $LPWOOTRK_BUILD_ROOTDIR
	fi

	if [ ! -d $LPWOOTRK_BUILD_OUTDIR ] 
	then
		mkdir $LPWOOTRK_BUILD_OUTDIR
	fi

	if [ ! -d $LPWOOTRK_BUILD_COMPATDIR ] 
	then
		mkdir $LPWOOTRK_BUILD_COMPATDIR
	fi

	if [ ! -d $LPWOOTRK_BUILD_TMPDIR ] 
	then
		mkdir $LPWOOTRK_BUILD_TMPDIR
	fi
}

# Regenerate compatibility info
make_compat_info() {
	echo "Building compatibility information files..."
	./bin/detect-compat-info.sh
}

# Ensure help contents is up to date
regenerate_help() {
	echo "No help contents to generate..."
}

clean_tmp_dir() {
	echo "Cleaning up temporary directory..."
	rm -rf $LPWOOTRK_BUILD_TMPDIR/*
	rm -rf $LPWOOTRK_BUILD_TMPDIR/.htaccess
}

# Clean output directories
clean_out_dirs() {
	echo "Ensuring output directories are clean..."
	rm -rf $LPWOOTRK_BUILD_OUTDIR/* > /dev/null
	rm -rf $LPWOOTRK_BUILD_TMPDIR/* > /dev/null
	rm -rf $LPWOOTRK_BUILD_TMPDIR/.htaccess > /dev/null
}

# Copy over all files
copy_source_files() {
	echo "Copying all files..."
	cp ./LICENSE "$LPWOOTRK_BUILD_TMPDIR/license.txt"
	cp ./README.txt "$LPWOOTRK_BUILD_TMPDIR/readme.txt"
	cp ./index.php "$LPWOOTRK_BUILD_TMPDIR"
	cp ./lpwootrk-plugin-*.php "$LPWOOTRK_BUILD_TMPDIR"
	cp ./.htaccess "$LPWOOTRK_BUILD_TMPDIR"

	mkdir "$LPWOOTRK_BUILD_TMPDIR/media" && cp -r ./media/* "$LPWOOTRK_BUILD_TMPDIR/media"
	mkdir "$LPWOOTRK_BUILD_TMPDIR/views" && cp -r ./views/* "$LPWOOTRK_BUILD_TMPDIR/views"
	mkdir "$LPWOOTRK_BUILD_TMPDIR/lib" && cp -r ./lib/* "$LPWOOTRK_BUILD_TMPDIR/lib"
	mkdir "$LPWOOTRK_BUILD_TMPDIR/lang" && cp -r ./lang/* "$LPWOOTRK_BUILD_TMPDIR/lang"

	mkdir "$LPWOOTRK_BUILD_TMPDIR/data"
	mkdir "$LPWOOTRK_BUILD_TMPDIR/data/cache"
	mkdir "$LPWOOTRK_BUILD_TMPDIR/data/help"
	mkdir "$LPWOOTRK_BUILD_TMPDIR/data/setup"

	cp -r ./data/help/* "$LPWOOTRK_BUILD_TMPDIR/data/help" > /dev/null
	cp -r ./data/setup/* "$LPWOOTRK_BUILD_TMPDIR/data/setup" > /dev/null
}

generate_package() {
	echo "Generating archive..."
	pushd $LPWOOTRK_BUILD_TMPDIR > /dev/null
	zip -rT $LPWOOTRK_BUILD_OUTDIR/$LPWOOTRK_BUILD_NAME ./ > /dev/null
	popd > /dev/null
}

echo "Using version: ${LPWOOTRK_VERSION}"

ensure_out_dirs
clean_out_dirs
regenerate_help
make_compat_info
copy_source_files
generate_package
clean_tmp_dir

echo "DONE!"

if [ "$LPWOOTRK_RESTORE_DIR" = true ]
then
	popd > /dev/null
fi