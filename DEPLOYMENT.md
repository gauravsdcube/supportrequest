# RequestSupport Module - Deployment Guide

This guide provides step-by-step instructions for deploying the RequestSupport module to production environments.

## Pre-Deployment Checklist

### System Requirements
- [ ] HumHub 1.17.3 or higher installed
- [ ] PHP 8.0+ with required extensions
- [ ] MySQL 5.7+ or MariaDB 10.2+
- [ ] Sufficient disk space (minimum 50MB)
- [ ] Proper file permissions configured

### Backup Requirements
- [ ] Database backup created
- [ ] File system backup created
- [ ] Current HumHub configuration backed up

## Deployment Steps

### Step 1: Prepare the Environment

```bash
# Navigate to HumHub installation
cd /path/to/humhub

# Create backup
cp -r protected/modules/ protected/modules_backup/
mysqldump -u username -p database_name > backup_$(date +%Y%m%d_%H%M%S).sql
```

### Step 2: Install the Module

```bash
# Create module directory
mkdir -p protected/modules/requestSupport

# Extract module files (if using zip)
unzip requestSupport-module.zip -d protected/modules/

# Or copy files manually
cp -r requestSupport/* protected/modules/requestSupport/
```

### Step 3: Set Permissions

```bash
# Set proper ownership (adjust www-data to your web server user)
chown -R www-data:www-data protected/modules/requestSupport/

# Set proper permissions
chmod -R 755 protected/modules/requestSupport/
chmod -R 644 protected/modules/requestSupport/*.php
chmod -R 644 protected/modules/requestSupport/**/*.php
```

### Step 4: Clear Cache

```bash
# Clear application cache
rm -rf protected/runtime/cache/*
rm -rf protected/runtime/HTML/*
```

### Step 5: Database Migration

```bash
# Run database migrations
php yii migrate/up --migrationPath=@humhub/modules/requestSupport/migrations

# Verify tables were created
php yii migrate/history --migrationPath=@humhub/modules/requestSupport/migrations
```

### Step 6: Enable the Module

1. **Via Admin Panel:**
   - Log in as administrator
   - Go to **Administration** → **Modules**
   - Find "Request Support" and click **Enable**

2. **Via Command Line:**
   ```bash
   php yii module/enable requestSupport
   ```

### Step 7: Configure Spaces

For each space where you want to enable support requests:

1. Navigate to the space
2. Go to **Space Settings** → **Modules**
3. Enable "Request Support"
4. Configure permissions as needed

### Step 8: Test the Installation

1. **Create a test request:**
   - Log in as a regular user
   - Navigate to a space with the module enabled
   - Create a support request

2. **Test admin functionality:**
   - Log in as a space administrator
   - View the created request
   - Add a response
   - Change the status

3. **Test category management:**
   - Access the category management interface
   - Create a new category
   - Edit an existing category

## Production Configuration

### Performance Optimization

1. **Enable OPcache:**
   ```php
   // In php.ini
   opcache.enable=1
   opcache.memory_consumption=128
   opcache.max_accelerated_files=4000
   ```

2. **Configure Redis/Memcached:**
   ```php
   // In protected/config/common.php
   'components' => [
       'cache' => [
           'class' => 'yii\caching\RedisCache',
           'redis' => [
               'hostname' => 'localhost',
               'port' => 6379,
           ],
       ],
   ],
   ```

### Security Configuration

1. **File Permissions:**
   ```bash
   # Restrict access to sensitive files
   chmod 644 protected/modules/requestSupport/config.php
   chmod 644 protected/modules/requestSupport/Module.php
   ```

2. **Database Security:**
   ```sql
   -- Create dedicated database user
   CREATE USER 'requestsupport'@'localhost' IDENTIFIED BY 'strong_password';
   GRANT SELECT, INSERT, UPDATE, DELETE ON humhub.requestsupport_* TO 'requestsupport'@'localhost';
   FLUSH PRIVILEGES;
   ```

### Monitoring Setup

1. **Error Logging:**
   ```php
   // In protected/config/common.php
   'components' => [
       'log' => [
           'targets' => [
               [
                   'class' => 'yii\log\FileTarget',
                   'levels' => ['error', 'warning'],
                   'logVars' => [],
                   'categories' => ['requestSupport*'],
               ],
           ],
       ],
   ],
   ```

2. **Health Checks:**
   ```bash
   # Create health check script
   cat > health_check.php << 'EOF'
   <?php
   require_once 'protected/config/common.php';
   
   try {
       $db = Yii::$app->db;
       $result = $db->createCommand('SELECT COUNT(*) FROM requestsupport_request')->queryScalar();
       echo "OK: Database connection successful, $result requests found\n";
   } catch (Exception $e) {
       echo "ERROR: " . $e->getMessage() . "\n";
       exit(1);
   }
   EOF
   ```

## Troubleshooting

### Common Issues

1. **Module not appearing:**
   ```bash
   # Check file permissions
   ls -la protected/modules/requestSupport/
   
   # Check module registration
   php yii module/list | grep requestSupport
   
   # Clear cache again
   rm -rf protected/runtime/cache/*
   ```

2. **Database errors:**
   ```bash
   # Check migration status
   php yii migrate/status --migrationPath=@humhub/modules/requestSupport/migrations
   
   # Check table structure
   mysql -u username -p database_name -e "DESCRIBE requestsupport_request;"
   ```

3. **Permission errors:**
   ```bash
   # Check web server user
   ps aux | grep apache
   ps aux | grep nginx
   
   # Fix ownership
   chown -R www-data:www-data protected/modules/requestSupport/
   ```

### Debug Mode

Enable debug mode temporarily for troubleshooting:

```php
// In protected/config/common.php
'components' => [
    'log' => [
        'traceLevel' => 3,
        'targets' => [
            [
                'class' => 'yii\log\FileTarget',
                'levels' => ['error', 'warning', 'info'],
                'logVars' => ['_GET', '_POST'],
            ],
        ],
    ],
],
```

## Rollback Plan

### If Issues Occur

1. **Disable the module:**
   ```bash
   php yii module/disable requestSupport
   ```

2. **Restore from backup:**
   ```bash
   # Restore files
   rm -rf protected/modules/requestSupport/
   cp -r protected/modules_backup/ protected/modules/
   
   # Restore database
   mysql -u username -p database_name < backup_file.sql
   ```

3. **Clear cache:**
   ```bash
   rm -rf protected/runtime/cache/*
   ```

## Maintenance

### Regular Tasks

1. **Monitor logs:**
   ```bash
   tail -f protected/runtime/logs/app.log | grep requestSupport
   ```

2. **Check database size:**
   ```sql
   SELECT 
       table_name,
       ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)'
   FROM information_schema.tables 
   WHERE table_schema = 'your_database' 
   AND table_name LIKE 'requestsupport_%';
   ```

3. **Clean old requests:**
   ```sql
   -- Archive requests older than 1 year
   INSERT INTO requestsupport_archive 
   SELECT * FROM requestsupport_request 
   WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 YEAR);
   
   DELETE FROM requestsupport_request 
   WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 YEAR);
   ```

### Updates

1. **Backup before update:**
   ```bash
   cp -r protected/modules/requestSupport/ protected/modules/requestSupport_backup/
   ```

2. **Update files:**
   ```bash
   # Extract new version
   unzip requestSupport-update.zip -d protected/modules/
   ```

3. **Run migrations:**
   ```bash
   php yii migrate/up --migrationPath=@humhub/modules/requestSupport/migrations
   ```

4. **Clear cache:**
   ```bash
   rm -rf protected/runtime/cache/*
   ```

## Support

For deployment issues:
- Check the HumHub community forums
- Review the module's GitHub issues
- Contact D Cube Consulting at info@dcubeconsulting.co.uk

## Security Considerations

1. **Regular updates:** Keep the module updated
2. **Access control:** Monitor who has admin access
3. **Data backup:** Regular backups of support data
4. **Log monitoring:** Monitor for suspicious activity
5. **Permission review:** Regularly review user permissions 