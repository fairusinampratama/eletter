# Deploying Laravel Application to Railway

This guide will walk you through the process of deploying your Laravel application to Railway.

## Prerequisites

1. A Railway account (sign up at [railway.app](https://railway.app))
2. Git installed on your local machine
3. Railway CLI installed (optional but recommended)
4. Your Laravel application code

## Step 1: Prepare Your Application

1. Create a `Procfile` in your project root:

```bash
web: vendor/bin/heroku-php-apache2 public/
worker: php artisan queue:work
```

2. Create a `railway.toml` file in your project root:

```toml
[build]
builder = "nixpacks"
buildCommand = "composer install --no-dev"

[deploy]
startCommand = "php artisan migrate --force && vendor/bin/heroku-php-apache2 public/"
healthcheckPath = "/"
healthcheckTimeout = 100
restartPolicyType = "on-failure"
restartPolicyMaxRetries = 10

[env]
APP_ENV = "production"
APP_DEBUG = "false"
```

3. Update your `.gitignore` file to ensure sensitive files are not committed:

```
/node_modules
/public/hot
/public/storage
/storage/*.key
/vendor
.env
.env.backup
.phpunit.result.cache
docker-compose.override.yml
Homestead.json
Homestead.yaml
npm-debug.log
yarn-error.log
/.idea
/.vscode
```

## Step 2: Set Up Railway Project

1. Log in to your Railway dashboard
2. Click "New Project"
3. Select "Deploy from GitHub repo"
4. Connect your GitHub account and select your repository
5. Railway will automatically detect your Laravel application

## Step 3: Configure Environment Variables

In your Railway project dashboard, add the following environment variables:

```
APP_NAME=YourAppName
APP_ENV=production
APP_KEY=base64:your-app-key
APP_DEBUG=false
APP_URL=https://your-railway-app-url.up.railway.app

DB_CONNECTION=mysql
DB_HOST=your-railway-mysql-host
DB_PORT=3306
DB_DATABASE=your-database-name
DB_USERNAME=your-database-username
DB_PASSWORD=your-database-password

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-mail-username
MAIL_PASSWORD=your-mail-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-mail-address
MAIL_FROM_NAME="${APP_NAME}"

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database
```

## Step 4: Add Database Service

1. In your Railway project, click "New"
2. Select "Database" → "MySQL"
3. Railway will automatically provision a MySQL database
4. Copy the database credentials provided by Railway
5. Update your environment variables with the new database credentials

## Step 5: Deploy Your Application

1. Push your changes to GitHub:

```bash
git add .
git commit -m "Prepare for Railway deployment"
git push
```

2. Railway will automatically detect the changes and start the deployment process

## Step 6: Verify Deployment

1. Once deployment is complete, Railway will provide you with a URL
2. Visit the URL to ensure your application is running correctly
3. Check the logs in Railway dashboard for any potential issues

## Step 7: Set Up Custom Domain (Optional)

1. In your Railway project, go to "Settings"
2. Click "Domains"
3. Add your custom domain
4. Follow the DNS configuration instructions provided by Railway

## Troubleshooting

If you encounter any issues:

1. Check the Railway deployment logs
2. Verify all environment variables are set correctly
3. Ensure your database migrations are running successfully
4. Check if your application's storage directory is writable
5. Verify that all required PHP extensions are installed

## Additional Tips

1. Enable automatic deployments from your main branch
2. Set up monitoring and alerts in Railway
3. Configure backup schedules for your database
4. Use Railway's CLI for easier management:

```bash
railway login
railway link
railway up
```

## Support

If you need help:

-   Check Railway's documentation: https://docs.railway.app
-   Join Railway's Discord community
-   Contact Railway support through their dashboard
