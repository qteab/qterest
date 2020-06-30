#!/bin/sh
echo "Copying files..."
rm -rf ./build
mkdir ./build
mkdir ./build/qterest
rsync -av --exclude=".git/" --exclude=".github/" --exclude=".idea/" --exclude="vendor/" --exclude="build/" --exclude="test/" --exclude="prepros-6.config" --exclude=".gitignore"  --exclude="build.sh" . build/qterest > /dev/null

echo "Installing dependencies..."
composer install --no-dev --working-dir="./build/qterest/" --quiet

echo "Creating archive..."
pushd ./build > /dev/null
zip -r -qq qterest.zip qterest
popd > /dev/null

echo "Cleaning up.."
rm -rf ./build/qterest


