#!/bin/bash

# build.sh

set -e

echo "Building Vue Docker image..."
docker compose build --no-cache

echo "Copying dist from Docker image to local ./dist..."

rm -rf ./dist
mkdir -p ./dist

docker create --name vue_dist_export vue_app
docker cp vue_dist_export:/usr/share/nginx/html/. ./dist/
docker rm vue_dist_export

echo "Starting Vue application..."
docker compose up -d

echo ""
echo "======================================"
echo "Vue application is running:"
echo "http://localhost:8088"
echo ""
echo "Local dist directory:"
echo "./dist"
echo "======================================"