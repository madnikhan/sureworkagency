# IONOS launch checklist

The live site was not deployed from this workspace. `surework.co.uk` was not registered on 16 October 2026, and this environment has no IONOS account, mailbox, or SSH access. Do not describe the release as live until the checks below have been done on the host.

## Before upload

- Buy `surework.co.uk`. Do not point the site at `surework.com`.
- Run the word Surework through the official UK IPO trade mark search. The earlier screen was not a clearance.
- Confirm the company name before changing `legal_name` in `config/brand.php`.
- Create the MySQL database in the IONOS panel. Put the credentials only in the server `.env`.
- Create the mailbox that will send mail, and set `MAIL_MAILER=smtp` with that mailbox.

## Server

- Select PHP 8.3 or newer for the site, preferably 8.4, and use the matching `php8.4-cli`.
- Set the document root to Laravel's `public/` directory. If the host forces `public_html`, place the application above that folder and use `deploy/public_html.htaccess` so requests are rewritten into `public/`. `.env`, `storage/`, and the rest of the application must not be browsable.
- `storage/` and `bootstrap/cache/` must be writable by PHP.
- Build assets locally with `npm run build` and upload `public/build`. Production does not run Node.
- On the server: `composer install --no-dev --optimize-autoloader`, then `php artisan migrate --force` and `php artisan config:cache`.
- Do not run `db:seed` on production. The local owner and any demonstration customer must not be created there. The service catalogue can be seeded once, with `php artisan db:seed --class=Database\\Seeders\\ServiceCatalogueSeeder --force`, because it contains no customer records and no prices.
- Set `APP_ENV=production`, `APP_DEBUG=false`, and `APP_URL` to the live `https://` domain. Set `SESSION_SECURE_COOKIE=true`.
- Add a cron job every minute: `php8.4-cli /path/to/artisan schedule:run`.
- Turn on IONOS backups and take a MySQL dump. Restore that dump once before announcing the site.

## Still open

- Domain registration
- Formal UK IPO search
- Company incorporation and VAT status
- Published prices, opening hours, and claimed social profiles
- Real portfolio and testimonials
- IONOS hosting package, SSH, and mailbox
