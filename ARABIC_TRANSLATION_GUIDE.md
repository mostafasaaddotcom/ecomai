# Arabic Translation Implementation Guide

## Overview
This guide documents the complete Arabic translation implementation for your e-commerce platform. The website now supports both Arabic and English languages with full RTL (Right-to-Left) support.

## Implementation Summary

### ✅ What Was Implemented

1. **Translation Files** - Complete Arabic and English translation files for all UI elements
2. **Language Switcher** - User-friendly language switcher in the header/sidebar
3. **RTL Support** - Full right-to-left layout support for Arabic
4. **User Preferences** - Language preference saved per user in the database
5. **Middleware** - Automatic locale detection and setting
6. **Default Language** - Arabic set as the default language

---

## File Structure

### Translation Files Created

```
resources/lang/
├── ar/                          # Arabic translations
│   ├── auth.php                 # Authentication strings (~50 keys)
│   ├── messages.php             # General UI strings (~80 keys)
│   ├── products.php             # Product management (~130 keys)
│   ├── settings.php             # Settings & account (~80 keys)
│   ├── validation.php           # Laravel validation messages
│   └── passwords.php            # Password reset messages
│
└── en/                          # English translations
    ├── messages.php             # General UI strings
    ├── products.php             # Product management
    └── settings.php             # Settings & account
```

### Core Files Modified/Created

1. **Middleware**
   - `/app/Http/Middleware/SetLocale.php` - Language detection and setting

2. **Livewire Component**
   - `/app/Livewire/LanguageSwitcher.php` - Language switcher logic
   - `/resources/views/livewire/language-switcher.blade.php` - Language switcher UI

3. **Database**
   - Migration: `2025_10_20_164010_add_locale_to_users_table.php`
   - User Model: Added `locale` to fillable attributes

4. **Layouts (RTL Support Added)**
   - `/resources/views/components/layouts/app/sidebar.blade.php`
   - `/resources/views/components/layouts/app/header.blade.php`
   - `/resources/views/components/layouts/auth/simple.blade.php`
   - `/resources/views/components/layouts/auth/split.blade.php`
   - `/resources/views/components/layouts/auth/card.blade.php`

5. **Configuration**
   - `/bootstrap/app.php` - Middleware registration
   - `.env` - Default locale set to Arabic

---

## How It Works

### 1. Language Detection Flow

```
Request → SetLocale Middleware → Check:
  1. Authenticated user's locale preference (database)
  2. Session locale
  3. Default from config (Arabic)
→ Set application locale → Render page
```

### 2. Language Switching

When a user clicks the language switcher:
1. **Livewire component** receives the language selection
2. **User preference** is updated in the database (if authenticated)
3. **Session** is updated with the new locale
4. **Page refreshes** to apply RTL/LTR changes
5. **All text** is displayed in the selected language

### 3. RTL Support

The `dir` attribute is dynamically set on the `<html>` tag:
```blade
<html dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
```

Tailwind CSS automatically handles RTL layouts using utility classes like:
- `me-2` → margin-end (right in LTR, left in RTL)
- `ms-2` → margin-start (left in LTR, right in RTL)
- `rtl:space-x-reverse` → reverse spacing direction in RTL

---

## Translation Keys Reference

### Common Usage Patterns

#### In Blade Templates
```blade
{{ __('Welcome back') }}
{{ __('auth.login') }}
{{ __('products.create_product') }}
```

#### In PHP/Livewire Components
```php
session()->flash('message', __('products.created_successfully'));
return redirect()->with('success', __('settings.profile_updated'));
```

#### With Parameters
```blade
{{ __('auth.throttle', ['seconds' => $seconds, 'minutes' => $minutes]) }}
```

### Key Translation Files

#### 1. Authentication (`auth.php`)
- Login/logout
- Registration
- Password reset
- Email verification
- Two-factor authentication

#### 2. General Messages (`messages.php`)
- Common actions (save, cancel, delete, edit)
- Navigation items
- Dashboard elements
- Date/time labels
- Pagination
- Themes

#### 3. Products (`products.php`)
- Product CRUD operations
- Analysis features
- Copywriting features
- Image generation
- Product types and statuses

#### 4. Settings (`settings.php`)
- Profile management
- Password changes
- Two-factor authentication
- API tokens
- Appearance settings
- Account deletion

---

## Usage Guide

### For Users

#### Switching Languages

1. **Desktop**: Click the language dropdown in the sidebar
2. **Mobile**: Click the language dropdown in the header
3. Select your preferred language (العربية or English)
4. The page will refresh with the new language

#### Language Persistence

- **Authenticated users**: Language preference is saved to your account
- **Guest users**: Language preference is saved in session (until browser closes)

### For Developers

#### Adding New Translation Strings

1. **Add to Arabic file** (`resources/lang/ar/[category].php`):
```php
'new_key' => 'النص بالعربية',
```

2. **Add to English file** (`resources/lang/en/[category].php`):
```php
'new_key' => 'English text',
```

3. **Use in Blade template**:
```blade
{{ __('category.new_key') }}
```

#### Creating New Translation Categories

1. Create new file: `resources/lang/ar/category.php`
2. Create English version: `resources/lang/en/category.php`
3. Use with: `__('category.key_name')`

#### Translating Dynamic Content (Database)

For product names, descriptions, etc., you would need to:
1. Add language columns to the database (e.g., `name_ar`, `name_en`)
2. Create a helper function to retrieve the correct translation
3. Update forms to accept multilingual input

**Note**: Currently, only UI elements are translated, not database content.

---

## Configuration

### Environment Variables (`.env`)

```env
APP_LOCALE=ar              # Default language (ar = Arabic)
APP_FALLBACK_LOCALE=en     # Fallback if translation is missing
APP_FAKER_LOCALE=ar_SA     # For generating Arabic test data
```

### Supported Locales

- `ar` - Arabic (Right-to-Left)
- `en` - English (Left-to-Right)

### Adding More Languages

To add a new language (e.g., French):

1. Create translation directory:
```bash
mkdir resources/lang/fr
```

2. Copy and translate files:
```bash
cp resources/lang/en/*.php resources/lang/fr/
# Then translate all strings in the fr/ files
```

3. Update `SetLocale` middleware:
```php
$supportedLocales = ['ar', 'en', 'fr'];
```

4. Update `LanguageSwitcher` component:
```php
if (!in_array($locale, ['ar', 'en', 'fr'])) {
    return;
}
```

5. Add option to language switcher view

---

## Testing

### Manual Testing Checklist

- [ ] Login page displays in Arabic by default
- [ ] Language switcher appears in sidebar/header
- [ ] Switching to English updates all text
- [ ] Switching back to Arabic updates all text + RTL layout
- [ ] Language preference persists after logout/login
- [ ] RTL layout displays correctly (menus, forms, buttons)
- [ ] Validation messages appear in correct language
- [ ] Flash messages appear in correct language
- [ ] All pages load without translation key errors

### Testing Commands

```bash
# Clear caches after translation changes
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Verify migrations
php artisan migrate:status
```

---

## Troubleshooting

### Issue: Language not changing
**Solution**: Clear application cache
```bash
php artisan cache:clear
php artisan config:clear
```

### Issue: Translation keys showing instead of text
**Cause**: Missing translation key
**Solution**: Add the key to the appropriate language file

### Issue: RTL layout not working
**Cause**: Missing `dir` attribute or Tailwind RTL classes
**Solution**: Verify `<html>` tag has `dir` attribute and use RTL-compatible Tailwind classes

### Issue: User preference not saving
**Cause**: Migration not run or database issue
**Solution**: Run migrations
```bash
php artisan migrate
```

### Issue: Page not refreshing after language change
**Cause**: Livewire component issue
**Solution**: Check browser console for JavaScript errors

---

## Best Practices

### 1. Translation Keys Organization
- Use dot notation: `products.create_product`
- Group related keys in same file
- Use descriptive key names

### 2. RTL-Compatible CSS
- Use logical properties: `me-*`, `ms-*`, `start-*`, `end-*`
- Avoid hardcoded `left` or `right`
- Use `rtl:` variant for RTL-specific styles

### 3. Translation File Structure
```php
return [
    // Group related translations
    'actions' => [
        'save' => 'حفظ',
        'cancel' => 'إلغاء',
    ],

    // Use descriptive keys
    'user_profile_updated' => 'تم تحديث الملف الشخصي',

    // Support parameters
    'welcome_user' => 'مرحباً :name',
];
```

---

## API Integration

If you need to provide translations via API:

### Example API Response
```json
{
    "message": "Product created successfully.",
    "message_ar": "تم إنشاء المنتج بنجاح.",
    "locale": "ar"
}
```

### Laravel API Resource Example
```php
public function toArray($request)
{
    return [
        'id' => $this->id,
        'name' => $this->name,
        'message' => __('products.created_successfully'),
        'locale' => app()->getLocale(),
    ];
}
```

---

## Statistics

### Translation Coverage
- **Total translation keys**: ~350-400
- **Arabic translations**: 100% complete
- **English translations**: 100% complete (for UI keys)
- **Files updated**: 46 Blade templates (already using translation keys)
- **Livewire components**: 21 components

### Features Translated
- ✅ Authentication & Authorization
- ✅ Dashboard & Navigation
- ✅ Product Management (CRUD)
- ✅ Product Analysis
- ✅ Copywriting Features
- ✅ Image Generation
- ✅ User Settings & Profile
- ✅ API Token Management
- ✅ Two-Factor Authentication
- ✅ Validation Messages
- ✅ Error Messages

---

## Future Enhancements

### Recommended Improvements

1. **Database Content Translation**
   - Add multilingual support for product names/descriptions
   - Use Laravel's translatable package

2. **SEO Optimization**
   - Add language-specific meta tags
   - Implement hreflang tags
   - Create language-specific URLs

3. **Date & Number Formatting**
   - Localize date formats (Hijri calendar for Arabic)
   - Format numbers according to locale

4. **Email Translations**
   - Translate notification emails
   - Add language preference to email templates

5. **Admin Panel**
   - Create interface to manage translations
   - Add missing translation detection

---

## Support & Resources

### Laravel Documentation
- [Localization](https://laravel.com/docs/11.x/localization)
- [Middleware](https://laravel.com/docs/11.x/middleware)
- [Blade Templates](https://laravel.com/docs/11.x/blade)

### Tailwind CSS RTL
- [RTL Support](https://tailwindcss.com/docs/hover-focus-and-other-states#rtl-support)

### Carbon (Date Handling)
- [Localization](https://carbon.nesbot.com/docs/#api-localization)

---

## Credits

Implementation completed: October 20, 2025
Framework: Laravel 11 + Livewire 2 + Tailwind CSS 4 + Flux UI
Languages: Arabic (العربية) + English

---

## License

This translation implementation follows the same license as your main application.
