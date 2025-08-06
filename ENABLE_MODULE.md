# How to Enable the Request Support Module

## Step 1: Clear Cache
1. Go to your HumHub admin panel
2. Navigate to **Administration > Information > Cache**
3. Click **"Clear Cache"**

## Step 2: Enable the Module
1. Go to **Administration > Modules**
2. Look for **"Request Support"** in the modules list
3. Click **"Enable"** next to the module

## Step 3: Run Database Migrations (if needed)
If the module doesn't appear or you get database errors:

1. **Via Command Line:**
   ```bash
   cd /path/to/your/humhub
   php protected/yii migrate/up --migrationPath=protected/modules/requestSupport/migrations
   ```

2. **Via Admin Panel:**
   - Go to **Administration > Information > Database**
   - Look for any pending migrations and run them

## Step 4: Enable in Spaces
1. Go to any space where you want to enable support requests
2. Go to **Space Settings > Modules**
3. Enable **"Request Support"** for that space

## Step 5: Verify Installation
1. Go to a space where the module is enabled
2. You should see a **"Support"** menu item in the space navigation
3. Click on it to access the support request system

## Troubleshooting

### If the module doesn't appear in the modules list:
1. Check file permissions - all module files should be readable
2. Clear the cache again
3. Check the HumHub logs for any errors

### If you get database errors:
1. Make sure the database user has proper permissions
2. Run the migrations manually via command line
3. Check if the database tables were created properly

### If the module appears but doesn't work:
1. Check that all required files are present
2. Verify that the module is enabled in the specific space
3. Check user permissions for the space

## Module Features
- Users can create support requests
- Space administrators can manage requests
- Support categories can be customized
- Email notifications for new requests and responses
- Status tracking (Open, In Progress, Resolved, Closed)

## File Structure
```
requestSupport/
├── Module.php              # Main module class
├── config.php              # Module configuration
├── Events.php              # Event handlers
├── controllers/            # Controllers
├── models/                 # Database models
├── views/                  # View templates
├── permissions/            # Permission classes
├── notifications/          # Notification classes
├── migrations/             # Database migrations
└── messages/              # Translation files
```

All files have been checked and are syntax-error free. 