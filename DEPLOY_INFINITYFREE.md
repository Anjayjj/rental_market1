# RentalMarket - InfinityFree Deployment

## Struktur web root
Upload the contents of this folder directly into the InfinityFree `htdocs` directory.

- `index.php` and `.htaccess` are in the web root.
- `app/` contains MVC application code.
- `assets/` contains CSS and public uploads.

## Database
1. Create/import the database using `database.sql`.
2. If initial demo data is required, import `seed_data.sql` after the schema.
3. Verify database credentials in `app/config/config.php`.

## Important
- The application base URL is configured as `https://rentalmarket.ct.ws`.
- Upload directories are under `assets/uploads/`.
- Do not leave SQL dumps publicly accessible after importing them.
- If the domain changes, update `BASEURL` in `app/config/config.php`.
