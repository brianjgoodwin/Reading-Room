# Reading Room

A self-hosted personal reading journal built with Laravel 13. Single-user, authentication-gated. Ported from a Django experiment and built as a serious Laravel learning project — proper idioms throughout.

## What it does

**Shelf** — the main view of your reading life:
- Books organized by status: To Read, Reading, Read
- Start and finish dates
- Tags (comma-separated, user-scoped)
- Journal entry count per book

**Journal** — a chronological feed of reading notes:
- Write in Markdown, rendered to HTML
- 0–5 star ratings
- Private/public toggle
- Tag filtering

**Tags** — a tag cloud with usage counts, linking to filtered book and journal views

**Stats** — a reading dashboard:
- Books by status
- Monthly pace bar chart (last 12 months)
- Top 5 authors by shelf count
- Reading streak (consecutive months with at least one book finished)
- Average star rating across all rated entries

**Find a Book** — search Open Library by title, author, or keyword:
- Cover images downloaded and stored locally
- One click to add to shelf

**Import** — Goodreads CSV export import:
- Deduplicates by ISBN, then title + author
- Maps Goodreads shelves to reading status
- Preserves start and finish dates
- Optionally imports reviews as private journal entries

## Stack

- Laravel 13
- Breeze (Blade + Tailwind CSS)
- SQLite (development) / PostgreSQL (production)
- league/commonmark (Markdown rendering)
- intervention/image-laravel (cover image processing)
- Open Library API (book search + covers — free, no API key)
- Lora serif font, custom parchment/ink color palette

## Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan storage:link
npm run build
```

Edit `.env`:
```
ALLOW_REGISTRATION=true   # set to false after creating your account
FILESYSTEM_DISK=public
```

Start the server:
```bash
php artisan serve
```

Register at `/register`, then set `ALLOW_REGISTRATION=false` in `.env`.

## Goodreads import

Export your library from Goodreads: **My Books → Import and export → Export library**. Upload the CSV at `/import`. Books already on your shelf are skipped — existing data is never overwritten.

## Development

```bash
php artisan serve        # start dev server
npm run dev              # watch and rebuild assets
php artisan migrate      # run pending migrations
php artisan cache:clear  # clear application cache
php artisan pint         # fix code style
```

## Current state

Functionally complete. All planned features are working:

- Auth (registration gated by env variable)
- Shelf with status tracking, dates, and tags
- Journal entries with Markdown, ratings, and privacy
- Tag cloud and tag-filtered views
- Reading stats dashboard
- Open Library book search with cover downloads
- Goodreads CSV import

Planned for a future session: VPS deployment with PostgreSQL, Nginx, and a deploy workflow.
