# Rwanda Explorer - deployment guide

A cinematic tourism site plus a full admin dashboard for Rwanda tours, stays and experiences.

## Stack (deliberate choice)

- **Plain PHP 8** front controller + PDO/MySQL - no framework, no Composer.
- **Vanilla JavaScript** - no build step, no Node runtime, no npm on the server.
- **Single CSS design system** (`assets/css/app.css`) + `assets/css/admin.css`.
- Works on any shared host with PHP; verified against **InfinityFree** (Apache + MariaDB).

This keeps the InfinityFree package small and FTP-friendly: copy files, import SQL, edit one config file.

Requirements: PHP >= 8.0 with `pdo_mysql`, `mbstring`, `gd` (image resizing), `curl` optional; Apache with `mod_rewrite`; MySQL 5.7+/MariaDB 10.x.

---

## 1. Local install (XAMPP)

1. Place the project in `C:\xampp\htdocs\Rwanda`.
2. Start Apache and MySQL.
3. Create the database `rwanda_explorer` (utf8mb4), then import:
   - `database/schema.sql`
   - `database/seed.sql`
4. `config/config.php` is already set for local XAMPP (`root`, empty password, `BASE_PATH => '/Rwanda'`).
5. Open <http://localhost/Rwanda>.

Seed data: 5 destinations, 8 tours, 10 hotels, 5 bookings, hero slides, gallery, blog posts.

### Demo customer accounts

| Email | Password |
|---|---|
| traveler@rwandaexplorer.rw | traveler123 |
| hotel@rwandaexplorer.rw | hotel123 |
| operator@rwandaexplorer.rw | operator123 |
| guide@rwandaexplorer.rw | guide123 |

### Admin account

| Email | Password |
|---|---|
| admin@rwandaexplorer.rw | Admin@2026 |

Change this password after the first login (Admin -> My profile). Any new
admin registration at `/admin/register` lands as **pending** and needs
approval under **Admin -> Admin requests**.

If your database was created before this change, run this migration in
phpMyAdmin first (see `database/migration_admin_requests.sql`).

---

## 2. InfinityFree deployment

Live target values:

| Setting | Value |
|---|---|
| Domain | `https://rwanda-explorer.wuaze.com` |
| htdocs | `/home/vol9_5/infinityfree.com/if0_43229793/htdocs` |
| DB host | `sql308.infinityfree.com` |
| DB port | `3306` |
| DB name | `if0_43229793_Rwanda` |
| DB user | `if0_43229793` |
| DB pass | `Rwanda2026` |

### Step by step

1. **Upload files** - FTP the project contents into `htdocs` (not the folder itself,
   so `index.php` sits at the web root). Keep `.htaccess` files - FileZilla must
   show hidden files (Server -> Force showing hidden files).
2. **Import the database** - in the InfinityFree control panel open phpMyAdmin for
   `if0_43229793_Rwanda`, then Import `database/schema.sql` followed by
   `database/seed.sql`.
3. **Create the live config** - copy `config/config.example.php` to
   `config/config.php` on the server and fill in:
   - keep `SITE_URL`, `BASE_PATH => ''`, `APP_ENV => 'production'`,
     `SESSION_SECURE => true` as provided.
   - never overwrite the local `config/config.php` with the live one.
4. **Create the upload temp folder** - see section 3. This is required for image
   uploads on InfinityFree.
5. **Permissions** - set `uploads/` to `777` (and `uploads/YYYY-MM/` is created
   automatically by the app). `tmp/` also `777`. PHP on InfinityFree runs as a
   different system user, so `755` will make uploads fail silently.
6. **Enable SSL** - in the control panel enable the free SSL certificate, then
   confirm `https://rwanda-explorer.wuaze.com` loads before relying on
   `SESSION_SECURE => true`.
7. **Admin account** - the seed creates `admin@rwandaexplorer.rw` /
   `Admin@2026`. Sign in at `/admin/login` and change the password. Later
   admin registrations need approval in **Admin -> Admin requests**.
8. **Smoke test** - home, a tour, a hotel, register/login, booking, favourites,
   admin dashboard, one image upload, 404 page.

> InfinityFree ignores some PHP directives in `.htaccess`. That is why upload
> limits live in `.user.ini` (next section) and error display is controlled by
> `APP_ENV` in `config.php`.

---

## 3. Image uploads on InfinityFree (important)

**Symptom:** "uploading my products with img not working online" - the form saves
everything except the image (or shows a generic upload error).

**Cause:** InfinityFree has no system temp directory, so PHP rejects uploads with
`UPLOAD_ERR_NO_TMP_DIR` (error code 6) before the app ever sees the file. Default
upload limits are also low.

**Fix:**

1. Create a folder `tmp/` inside `htdocs` and set permissions to `777`.
2. Create `htdocs/.user.ini`:

```ini
upload_tmp_dir = /home/vol9_5/infinityfree.com/if0_43229793/htdocs/tmp
upload_max_filesize = 4M
post_max_size = 8M
max_file_uploads = 10
```

3. Set `uploads/` to `777` as well.
4. Wait a few minutes (PHP reads `.user.ini` periodically), then retry the upload.

The app already:

- checks `$_FILES[...]['error']` explicitly and returns a readable message for
  each code (including "no temp folder" and "file too large");
- accepts JPG/PNG/WebP/GIF, caps size at `MAX_UPLOAD_MB` (4 MB);
- stores files under `uploads/YYYY-MM/` with random names;
- downscales images larger than 1920px with GD when available;
- blocks PHP execution inside `uploads/` via `uploads/.htaccess`.

If uploads still fail: confirm the `tmp/` path matches your account exactly, check
`uploads/` is `777`, and confirm the file is under 4 MB.

---

## 4. Routes

Public: `/`, `/destinations`, `/destinations/{slug}`, `/tours`, `/tours/{slug}`,
`/hotels`, `/hotels/{slug}`, `/experiences`, `/gallery`, `/about`, `/contact`,
`/search`, `/guide`, `/blog`, `/blog/{slug}`.

Auth: `/login`, `/register`, `/forgot-password`, `/reset-password`, `/logout`;
`/admin/login`, `/admin/register`, `/admin/logout`.

Account: `/account?tab=overview|bookings|favorites|profile` (+ POST profile/password),
`/booking/create`, `/booking/{id}/cancel`.

Admin: `/admin`, `/admin/analytics`, `/admin/notifications`, `/admin/settings`,
`/admin/profile`, and CRUD for destinations, tours, hotels, rooms, bookings,
customers, payments, reviews, gallery, hero-slides, blog, testimonials, guides,
coupons, permits and tour-dates (`/admin/{resource}/new`, `/{id}/edit`, `/{id}/delete`).

API (JSON, auth-gated): `/api/search`, `/api/favorites/toggle`,
`/api/notifications`, `/api/account/bookings`, `/api/admin/stats`,
`/api/admin/bookings`, `/api/admin/{resource}`,
`/api/admin/{resource}/{id}/delete`.

---

## 5. Security measures

- `password_hash` / `password_verify` (bcrypt), automatic rehash on login.
- Session id regenerated on login; `HttpOnly`, `SameSite=Lax`, `Secure` on live.
- CSRF token on every POST form and JSON mutation (`csrf_verify()`,
  `csrf_header_check()`); JSON APIs also require the `X-CSRF-Token` header.
- Login rate limiting: 5 failed attempts per email / 20 per IP in 15 minutes.
- All SQL uses prepared statements; all output escaped with `e()`.
- Role separation: customer login rejects admin accounts and vice versa;
  `require_admin()` guards every admin page and admin API (HTML redirect vs JSON
  401/403).
- `config/`, `database/`, `logs/`, `app/`, `views/` blocked by `.htaccess`;
  `uploads/` cannot execute PHP.
- Errors go to `logs/error.log` in production - never to the browser.

---

## 6. Known limitations

- **Promo video**: the "Watch film" modal expects `assets/video/promo.mp4`, which
  is not included. The UI degrades gracefully with a "film coming soon" fallback
  instead of breaking. Add a real MP4 at that path to enable playback.
- **Email**: InfinityFree blocks outbound SMTP, so password reset links are written
  to the error log instead of being emailed (`error_log`), and `SHOW_RESET_LINK`
  can display the link on screen for testing. Configure SMTP on a host that allows
  it for production email.
   Admin -> Admin requests: every admin page and API rejects them server-side.
- Admin charts are drawn with the Canvas API (no chart library, no external CDN).
- All imagery is local (`assets/img/`); no external CDNs or Google Fonts requests
  at runtime beyond the font CSS if enabled by the host.

---

## 7. Post-deploy checklist

- [ ] `config/config.php` exists on the server only; `config.example.php` untouched.
- [ ] Database imported and `/admin` loads after creating an admin.
- [ ] Second admin registration lands in Admin -> Admin requests as pending.
- [ ] `tmp/.user.ini` + `uploads/` permissions set; test one image upload.
- [ ] HTTPS certificate active; `SESSION_SECURE` true; HTTP redirects to HTTPS.
- [ ] Password reset tested (link appears in `logs/error.log`).
- [ ] Booking create + cancel tested with a real customer account.
- [ ] `logs/error.log` has no new entries after the smoke test.
