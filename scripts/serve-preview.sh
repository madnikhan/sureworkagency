#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/.."
docker run --rm --name surework-preview \
  -p 127.0.0.1:8123:8000 \
  -u "$(id -u):$(id -g)" \
  -v "$PWD":/app \
  -w /app \
  --entrypoint php \
  composer:2 artisan serve --host=0.0.0.0 --port=8000
