# AutoFeed - New Features Setup Guide

## Overview
This guide explains how to set up the new features added to AutoFeed.

## Database Setup

1. **Run the schema updates:**
   ```sql
   -- First, run the base schema if not already done
   mysql -u root -p auto_marketplace < database/schema.sql
   
   -- Then run the updates
   mysql -u root -p auto_marketplace < database/schema_updates.sql
   ```

   **Note:** If you get errors about columns already existing, you can ignore them or manually add only the missing columns.

2. **Verify tables were created:**
   - `saved_listings` - for wishlist feature
   - `comments` - for commenting on listings
   - `likes` - for liking listings
   - `user_contacts` - for contact information

## Google OAuth Setup

1. **Create Google OAuth Credentials:**
   - Go to [Google Cloud Console](https://console.cloud.google.com/)
   - Create a new project or select existing
   - Enable Google+ API
   - Go to "Credentials" → "Create Credentials" → "OAuth 2.0 Client ID"
   - Choose "Web application"
   - Add authorized redirect URI: `http://localhost/auth.php` (or your domain)
   - Copy the Client ID and Client Secret

2. **Update config.php:**
   ```php
   define('GOOGLE_CLIENT_ID', 'YOUR_CLIENT_ID_HERE');
   define('GOOGLE_CLIENT_SECRET', 'YOUR_CLIENT_SECRET_HERE');
   define('GOOGLE_REDIRECT_URI', BASE_URL . '/auth.php');
   ```

## New Features Implemented

### ✅ 1. Google Sign-In (OAuth2)
- **Files:** `login.php`, `auth.php`, `logout.php`
- **Location:** Login button in header, bottom nav
- **Features:**
  - Google OAuth2 authentication
  - Auto-creates user accounts
  - Stores Google UID, name, email, picture

### ✅ 2. Bottom Navigation Update
- **File:** `index.php`, `profile.php`
- **Layout:** Home • Search • Post • Profile (or Sign In for guests)

### ✅ 3. User Profile Page
- **File:** `profile.php`
- **Features:**
  - Profile picture, name, email
  - User type (individual/dealership)
  - Listings posted
  - Saved listings
  - Contact information

### ✅ 4. Verified Seller System
- **Database:** `users.is_verified` field
- **Display:** Verified badge (✓) appears next to verified sellers
- **Feed:** Verified sellers appear at top of feed (already implemented in `getAllCars()`)

### ✅ 5. Contact Seller Feature
- **File:** `car_detail.php`
- **Features:**
  - WhatsApp link
  - Email link
  - Phone link
  - Modal with contact options

### ✅ 6. Location System
- **Database:** `cars.country`, `cars.city` fields
- **Files:** `add_post.php` (form), `search.php` (filters), `car_detail.php` (display)
- **Features:**
  - Country and City fields in listing form
  - Location filters in search page
  - Location displayed on listings

### ✅ 7. Dealership Features
- **Database:** `users.dealership_name`, `users.dealership_logo`, `users.dealership_location`, `users.dealership_hours`
- **Display:** Dealership badge (🏢) and custom profile
- **Note:** Full dealership management UI can be added later

### ✅ 8. Save Listing (Wishlist)
- **File:** `api/save_listing.php`, `js/feed-interactions.js`
- **Features:**
  - Heart button to save/unsave listings
  - Saved listings shown in profile page
  - Only available for logged-in users

### ✅ 9. Share Listing Feature
- **File:** `js/feed-interactions.js`
- **Features:**
  - WhatsApp share
  - Facebook share
  - Twitter/X share
  - Messenger share
  - Copy link
  - Share modal with all options

### ✅ 10. Guest User Restrictions
- **Implementation:**
  - Guests cannot save listings (redirected to login)
  - Guests cannot comment (redirected to login)
  - Guests can share listings
  - Guest posts show as "Seller_XXXX"

### ✅ 11. User Name Display
- **Implementation:**
  - Logged-in users: Show their name (or dealership name)
  - Guest users: Show "Seller_XXXX" format
  - Verified badge for verified sellers
  - Dealership badge for dealerships

## Testing Checklist

- [ ] Google OAuth login works
- [ ] User profile displays correctly
- [ ] Save/unsave listing works
- [ ] Share functionality works for all platforms
- [ ] Contact seller modal works
- [ ] Location filters work in search
- [ ] Verified sellers appear at top of feed
- [ ] Guest restrictions work correctly
- [ ] Profile page shows listings and saved listings

## Notes

1. **Google OAuth:** Make sure to update the redirect URI in Google Console to match your domain
2. **Database:** Some columns may already exist if you've run migrations before - that's okay
3. **Dealership Features:** Basic structure is in place. Full dealership management can be added as needed
4. **Comments:** API is ready (`api/comments.php`) but UI integration can be enhanced
5. **Verified Sellers:** Currently needs to be set manually in database. Admin interface can be added later

## Troubleshooting

### Google OAuth not working:
- Check redirect URI matches exactly
- Verify Client ID and Secret are correct
- Check that Google+ API is enabled

### Database errors:
- Make sure all tables exist
- Check foreign key constraints
- Verify user_id references are correct

### Save/Share not working:
- Check browser console for errors
- Verify user is logged in for save feature
- Check API endpoints are accessible

