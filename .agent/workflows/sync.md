---
description: Synchronize the local project with the GitHub repository
---

To keep your local project up to date with the latest changes from the GitHub repository, follow these steps:

// turbo
1. Pull the latest changes from the main branch:
```cmd
git pull origin main
```

// turbo
2. Update PHP dependencies:
```cmd
composer install
```

// turbo
3. Update JavaScript dependencies:
```cmd
npm install
```

// turbo
4. Run any new database migrations:
```cmd
php artisan migrate --force
```

// turbo
5. Rebuild assets (if using Vite):
```cmd
npm run build
```

> **Note:** If you have local changes that haven't been committed, `git pull` might fail. You should commit or stash your changes before syncing.
