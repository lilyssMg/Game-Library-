# Admin Guide

## Database access

```bash
mysql -u game_user -p game_library
```

## Backup

```bash
mysqldump -u game_user -p game_library > backup_$(date +%Y%m%d).sql
```

## Restore

```bash
mysql -u game_user -p game_library < backup_YYYYMMDD.sql
```

## Adding a member

Insert directly into the `members` table:

```sql
INSERT INTO members (name, student_id, email, bio)
VALUES ('Name', '41385XXXX', 'email@example.com', 'Short bio.');
```

## Uploaded images

Images are stored in `uploads/`. To free disk space:

```bash
ls uploads/
rm uploads/<filename>
```

Removing a file does not remove the DB record. The game will show "no cover."

## Resetting the database

**Destructive — drops all data.**

```bash
mysql -u game_user -p game_library -e "DROP TABLE IF EXISTS games, members;"
mysql -u game_user -p game_library < database/schema.sql
```

## Service management (DietPi / systemd)

```bash
sudo systemctl status apache2
sudo systemctl restart apache2
sudo systemctl status mariadb
sudo systemctl restart mariadb
```

## Logs

```bash
sudo tail -f /var/log/apache2/error.log
sudo tail -f /var/log/apache2/access.log
```
