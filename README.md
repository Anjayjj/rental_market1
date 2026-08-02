# RentalMarket

PHP MVC rental marketplace application prepared for InfinityFree deployment.

## Web root structure
Upload the contents of this directory directly into the hosting `htdocs` folder:

- `index.php` and `.htaccess` are in the root.
- `app/` contains controllers, models, views and core classes.
- `assets/` contains CSS and public upload files.

## Database
- Import `database.sql` first.
- Import `seed_data.sql` afterward if demo/seed data is required.
- Verify credentials in `app/config/config.php`.

## Important
- `BASEURL` is configured for `https://rentalmarket.ct.ws`.
- Public assets are served from `/assets/`.
- Upload directories are `assets/uploads/avatars`, `assets/uploads/items`, and `assets/uploads/payments`.
- Remove SQL dump files from the public web root after importing the database.
