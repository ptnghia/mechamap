# 🔧 Development Setup

> **Complete guide for setting up MechaMap development environment**

## 📋 Setup Guides

- [Installation Guide](./installation.md) - Local development setup
- [Environment Configuration](./environment.md) - Environment variables and config
- [Docker Setup](./docker.md) - Containerized development
- [VS Code Configuration](./vscode.md) - IDE setup and extensions

## 🎯 Quick Start

1. **Prerequisites**: PHP 8.2+, MySQL 8.0+, Node.js 18+
2. **Clone**: `git clone https://github.com/mechamap/mechamap.git`
3. **Install**: `composer install && npm install`
4. **Configure**: Copy `.env.example` to `.env`
5. **Migrate**: `php artisan migrate --seed`
6. **Serve**: `php artisan serve`

## 🔗 Related Docs

- [Architecture Overview](../architecture/overview.md)
- [Testing Setup](../testing/README.md)
- [Contributing Guidelines](../contributing/code-standards.md)
