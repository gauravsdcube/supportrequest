# RequestSupport Module for HumHub

A comprehensive support request management system for HumHub spaces. This module allows space members to submit support requests to Space Administrators and Moderators, with full category management, status tracking, and response system.

## Features

- **Support Request Management**: Create, view, edit, and manage support requests
- **Category Management**: Admin interface for managing support categories per space
- **Response System**: Add responses to requests with proper permissions
- **Status Management**: Change request status (Open, In Progress, Resolved, Closed)
- **Permission System**: Role-based access control for different user types
- **Notification System**: Email notifications for new requests and responses
- **Auto-Category Creation**: Default categories created automatically for new spaces
- **Closed Request Protection**: Prevents responses on closed requests (except admins/moderators)
- **Space-Specific Categories**: Categories are managed per space

## Requirements

- HumHub 1.17.3 or higher
- PHP 8.0 or higher
- MySQL 5.7 or higher / MariaDB 10.2 or higher

## Installation

### Method 1: Manual Installation

1. **Download the module**
   ```bash
   # Navigate to your HumHub installation
   cd /path/to/humhub
   
   # Create the module directory
   mkdir -p protected/modules/requestSupport
   ```

2. **Copy the module files**
   ```bash
   # Copy all files from this repository
   cp -r * /path/to/humhub/protected/modules/requestSupport/
   ```

3. **Set proper permissions**
   ```bash
   chmod -R 755 protected/modules/requestSupport/
   chown -R www-data:www-data protected/modules/requestSupport/
   ```

4. **Clear cache**
   ```bash
   rm -rf protected/runtime/cache/*
   ```

5. **Enable the module**
   - Log in to your HumHub admin panel
   - Go to **Administration** → **Modules**
   - Find "Request Support" and click **Enable**

6. **Configure spaces**
   - Go to any space you want to enable support requests for
   - Go to **Space Settings** → **Modules**
   - Enable "Request Support" for the space

### Method 2: Git Clone

```bash
cd /path/to/humhub/protected/modules/
git clone https://github.com/gauravsdcube/supportrequest.git requestSupport
```

## Configuration

### Default Categories

The module automatically creates default categories for new spaces:
- Technical Issue
- Account Problem
- Content Issue
- General Question
- Bug Report
- Feature Request

### Permissions

The module includes three permission levels:

1. **CreateSupportRequest**: Allows users to create support requests
2. **ManageSupportRequests**: Allows users to view and manage all requests in the space
3. **ManageCategories**: Allows users to manage support categories

### User Roles

- **Space Members**: Can create requests and view their own requests
- **Space Moderators**: Can manage all requests, change status, and manage categories
- **Space Administrators**: Full access to all features
- **System Administrators**: Full access across all spaces

## Usage

### For Space Members

1. **Create a Support Request**
   - Navigate to a space with the module enabled
   - Click "Support" in the space navigation
   - Click "Create Support Request"
   - Fill in the form and submit

2. **View Your Requests**
   - Go to the Support section
   - View your submitted requests and responses

### For Space Moderators/Administrators

1. **Manage Requests**
   - View all requests in the space
   - Change request status using the dropdown
   - Add responses to any request
   - Edit request details

2. **Manage Categories**
   - Click "Manage Categories" in the Support section
   - Add, edit, or delete categories
   - Reorder categories using sort order

### Status Management

- **Open**: Initial status for new requests
- **In Progress**: Request is being worked on
- **Resolved**: Request has been resolved
- **Closed**: Request is closed (no new responses from regular users)

## File Structure

```
requestSupport/
├── controllers/
│   ├── DefaultController.php      # Main listing controller
│   ├── RequestController.php      # Individual request actions
│   └── CategoryController.php     # Category management
├── models/
│   ├── SupportRequest.php         # Main request model
│   ├── SupportCategory.php        # Category model
│   └── SupportResponse.php        # Response model
├── views/
│   ├── default/
│   │   └── index.php             # Main listing view
│   ├── request/
│   │   ├── create.php            # Create form
│   │   ├── view.php              # View details
│   │   └── update.php            # Edit form
│   └── category/
│       ├── index.php             # Category list
│       ├── create.php            # Create category
│       └── update.php            # Edit category
├── permissions/
│   ├── CreateSupportRequest.php   # Create permission
│   ├── ManageSupportRequests.php # Manage permission
│   └── ManageCategories.php      # Category permission
├── notifications/
│   ├── NewSupportRequest.php     # New request notification
│   ├── SupportRequestResponse.php # Response notification
│   └── SupportRequestNotificationCategory.php # Notification category
├── migrations/
│   └── m000001_000000_initial.php # Database tables
├── messages/
│   └── en/
│       └── base.php              # English translations
├── config.php                    # Module configuration
├── Events.php                    # Event handlers
├── Module.php                    # Main module class
├── module.json                   # Module metadata
├── DEPLOYMENT.md                 # Deployment guide
├── ENABLE_MODULE.md              # Quick enable guide
└── README.md                     # This file
```

## Troubleshooting

### Common Issues

1. **Module not appearing in space navigation**
   - Ensure the module is enabled for the specific space
   - Clear the application cache
   - Check file permissions

2. **Database errors**
   - Ensure the migration has run properly
   - Check database connection
   - Verify table structure

3. **Permission errors**
   - Check user roles and permissions
   - Verify space membership
   - Clear user cache

### Debug Mode

Enable debug mode in your HumHub configuration to see detailed error messages:

```php
// In protected/config/common.php
'components' => [
    'log' => [
        'traceLevel' => YII_DEBUG ? 3 : 0,
        'targets' => [
            [
                'class' => 'yii\log\FileTarget',
                'levels' => ['error', 'warning'],
            ],
        ],
    ],
],
```

## Development

### Adding New Features

1. **New Controllers**: Extend `ContentContainerController`
2. **New Models**: Extend `ContentActiveRecord` for content-related models
3. **New Permissions**: Extend `BasePermission`
4. **New Views**: Follow existing naming conventions

### Testing

1. **Unit Tests**: Create tests in `tests/` directory
2. **Integration Tests**: Test with different user roles
3. **Manual Testing**: Test all features in a development environment

## Support

For issues and feature requests:
- Create an issue on this GitHub repository
- Contact D Cube Consulting at info@dcubeconsulting.co.uk
- Check the HumHub community forums

## Changelog

### Version 1.0.4
- Fixed notification rendering issues
- Improved error handling
- Enhanced documentation

### Version 1.0.3
- Added notification system
- Fixed permission issues
- Improved UI/UX

### Version 1.0.2
- Production-ready fixes
- Enhanced security
- Performance improvements

### Version 1.0.1
- Bug fixes and improvements
- Enhanced category management

### Version 1.0.0
- Initial release
- Support request management
- Category management
- Response system
- Status management
- Permission system
- Notification system

## Credits

Developed by [D Cube Consulting](https://dcubeconsulting.co.uk/). 
