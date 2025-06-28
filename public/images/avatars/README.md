# MechaMap Avatar Images Directory

This directory contains avatar images for different user roles in the MechaMap system.

## Directory Structure

```
/images/avatars/
├── admin1.jpg          # Admin users
├── admin2.jpg
├── moderator1.jpg      # Moderator users  
├── moderator2.jpg
├── moderator3.jpg
├── moderator4.jpg
├── senior1.jpg         # Senior Member users
├── senior2.jpg
├── senior3.jpg
├── senior4.jpg
├── senior5.jpg
├── senior6.jpg
├── member1.jpg         # Member users
├── member2.jpg
├── member3.jpg
├── member4.jpg
├── member5.jpg
├── member6.jpg
├── member7.jpg
├── member8.jpg
├── member9.jpg
├── member10.jpg
├── member11.jpg
├── member12.jpg
├── member13.jpg
├── member14.jpg
├── member15.jpg
├── guest1.jpg          # Guest users
├── guest2.jpg
├── guest3.jpg
├── supplier1.jpg       # Supplier business users
├── supplier2.jpg
├── supplier3.jpg
├── supplier4.jpg
├── supplier5.jpg
├── manufacturer1.jpg   # Manufacturer business users
├── manufacturer2.jpg
├── manufacturer3.jpg
├── manufacturer4.jpg
├── brand1.jpg          # Brand business users
├── brand2.jpg
├── brand3.jpg
└── brand4.jpg
```

## Image Requirements

- **Format**: JPG, PNG, or WebP
- **Size**: 200x200px to 400x400px (square aspect ratio)
- **File size**: Under 500KB each
- **Quality**: Professional headshots for business users, casual for community users

## Usage in Seeders

These avatar paths are referenced in:
- `database/seeders/MechaMapUserSeeder.php` - Community users
- `database/seeders/BusinessUserSeeder.php` - Business users

## Placeholder Images

If actual images are not available, you can use placeholder services:
- https://picsum.photos/300/300 (random photos)
- https://ui-avatars.com/api/?name=User+Name&size=300 (generated avatars)
- https://robohash.org/user.png?size=300x300 (robot avatars)

## Adding New Images

1. Add image files to this directory
2. Update the corresponding seeder file
3. Ensure the file path matches the avatar field in the user data
4. Run `php artisan db:seed` to apply changes

## Notes

- All paths are relative to the public directory
- Images are served directly by the web server
- Consider using Laravel's Storage system for production environments
- Optimize images for web delivery (compress, resize appropriately)
