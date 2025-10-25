# Admin Token System Guide

## Overview

The admin token system allows you to create API tokens with full access to all resources, regardless of user ownership. These tokens have special `admin:*` abilities that bypass normal user ownership checks.

## Token Abilities System

- **Regular User Tokens**: `["user:access"]` - Can only access resources they own
- **Admin Tokens**: `["admin:*"]` - Can access ANY resource regardless of ownership

**Important**: Do NOT use `["*"]` (wildcard) for regular tokens, as Sanctum treats this as matching all abilities including admin abilities.

## Creating an Admin Token

### Basic Usage

Generate an admin token with default settings:

```bash
php artisan token:create-admin
```

This will:
- Create or use a system admin user (`admin@system.local`)
- Generate a token named "Admin Token"
- Token never expires
- Has `admin:*` ability (full access)

### Advanced Options

**Custom Token Name:**
```bash
php artisan token:create-admin --name="My Custom Admin Token"
```

**Use Existing User:**
```bash
php artisan token:create-admin --user=1
```

**Set Expiration:**
```bash
php artisan token:create-admin --expires=30
# Token expires in 30 days
```

**Combine Options:**
```bash
php artisan token:create-admin --name="Production Admin" --user=5 --expires=90
```

## Using the Admin Token

### API Request Example

```bash
# Regular user token - can only access own products
curl -H "Authorization: Bearer {user_token}" \
  https://yourapp.com/api/v1/products/123

# Admin token - can access ANY product
curl -H "Authorization: Bearer {admin_token}" \
  https://yourapp.com/api/v1/products/123
```

### How It Works

1. **Regular User Tokens**: Can only access resources where `user_id` matches their own ID
2. **Admin Tokens**: Can access ANY resource regardless of `user_id`

The admin check happens automatically in controllers using the `canAccessResource()` helper method.

## Security Considerations

⚠️ **Important Security Notes:**

1. **Store Tokens Securely**: Admin tokens have full access - treat them like root passwords
2. **Limit Distribution**: Only create admin tokens when absolutely necessary
3. **Monitor Usage**: Check the `personal_access_tokens` table for `last_used_at` timestamps
4. **Rotate Regularly**: Revoke and recreate admin tokens periodically
5. **Use Expiration**: Consider setting expiration dates for production admin tokens

## Revoking Admin Tokens

### Via Database

```sql
DELETE FROM personal_access_tokens WHERE id = {token_id};
```

### Via Tinker

```bash
php artisan tinker
>>> \Laravel\Sanctum\PersonalAccessToken::find(5)->delete();
```

### Revoke All Admin Tokens

```bash
php artisan tinker
>>> \Laravel\Sanctum\PersonalAccessToken::where('abilities', 'like', '%admin:*%')->delete();
```

## Checking Token Abilities

List all admin tokens:

```bash
php artisan tinker
>>> \Laravel\Sanctum\PersonalAccessToken::where('abilities', 'like', '%admin:*%')->get(['id', 'name', 'tokenable_id', 'created_at']);
```

## Extending Admin Functionality

### Custom Admin Abilities

You can create more granular admin abilities:

```php
// In your command or token creation
$token = $user->createToken('Limited Admin', ['admin:read', 'admin:products']);
```

Then check in controllers:

```php
if ($request->user()->tokenCan('admin:products')) {
    // Admin access for products only
}
```

### Custom Resource Access Check

The `canAccessResource()` method can be used for any resource:

```php
// Check product access
if (!auth()->user()->canAccessResource($product)) {
    abort(403);
}

// Check with custom ownership key
if (!auth()->user()->canAccessResource($order, 'owner_id')) {
    abort(403);
}
```

## Current API Endpoints Supporting Admin Tokens

- ✅ `GET /api/v1/products/{id}` - View any product
- ✅ `PUT /api/v1/products/{id}` - Update any product

Admin tokens automatically work with any endpoint using `canAccessResource()` helper.

## Troubleshooting

### Token Not Working

1. Verify token format: `{token_id}|{plaintext_token}`
2. Check token hasn't expired
3. Ensure `admin:*` ability exists in database
4. Verify Bearer token is in Authorization header

### Check Token in Database

```bash
php artisan tinker
>>> $token = \Laravel\Sanctum\PersonalAccessToken::where('token', hash('sha256', 'plaintext_part_here'))->first();
>>> $token->abilities; // Should show ["admin:*"]
```

## Example: Complete Workflow

```bash
# 1. Create admin token
php artisan token:create-admin --name="Production Admin"
# Save the output token: 5|hIhEAFE8ghXJddHBbi2s3vz2ro3z1Ub2FH6i58bOfe9ddf61

# 2. Test with regular user product
curl -H "Authorization: Bearer 5|hIhEAFE8ghXJddHBbi2s3vz2ro3z1Ub2FH6i58bOfe9ddf61" \
  https://yourapp.com/api/v1/products/1

# 3. Update any product
curl -X PUT \
  -H "Authorization: Bearer 5|hIhEAFE8ghXJddHBbi2s3vz2ro3z1Ub2FH6i58bOfe9ddf61" \
  -H "Content-Type: application/json" \
  -d '{"description_ai":"Updated by admin"}' \
  https://yourapp.com/api/v1/products/1

# 4. Revoke when done
php artisan tinker
>>> \Laravel\Sanctum\PersonalAccessToken::find(5)->delete();
```

## Summary

The admin token system provides:
- ✅ Full API access without user restrictions
- ✅ Easy token generation via Artisan command
- ✅ Built on Laravel Sanctum (no new dependencies)
- ✅ Flexible expiration and naming options
- ✅ Works with existing API endpoints
- ✅ Audit trail via `personal_access_tokens` table
