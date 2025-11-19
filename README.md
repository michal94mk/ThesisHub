# ThesisHub

Thesis management system for universities.

## 🚀 Technologies

- **Backend:** Laravel 12
- **Frontend:** Vue 3 + Inertia.js
- **Styling:** Tailwind CSS
- **Database:** SQLite (dev) / MySQL (production)
- **Auth:** Laravel Breeze

## 📦 Installation

```bash
# Clone the repository
git clone https://github.com/michal94mk/ThesisHub.git
cd ThesisHub

# Install dependencies
composer install
npm install

# Configure .env
cp .env.example .env
php artisan key:generate

# Run migrations and seeders
php artisan migrate:fresh --seed

# Start servers
php artisan serve
npm run dev
```

## 👥 Test Users

After running `php artisan migrate:fresh --seed`, the following users are available:

### Administrator
- **Email:** admin@thesis.pl
- **Password:** password
- **Access:** Admin panel

### Supervisors
- **Email:** kowalski@thesis.pl / password
- **Email:** nowak@thesis.pl / password  
- **Email:** wisniewski@thesis.pl / password
- **Access:** Supervisor panel

### Students
- **Email:** student1@thesis.pl / password
- **Email:** student2@thesis.pl / password
- **Email:** student3@thesis.pl / password
- **Email:** student4@thesis.pl / password
- **Email:** student5@thesis.pl / password
- **Access:** Student panel

## 🔐 Role System

The application has 3 roles:

- **admin** - full system access
- **supervisor** - manage student theses
- **student** - submit and manage own theses

## 📝 Project Status

✅ Completed:
- [x] Setup Laravel + Breeze + Inertia + Vue 3
- [x] Authentication system with 3 roles
- [x] Role-checking middleware
- [x] Seeders with test users

🚧 In Progress:
- [ ] Thesis model
- [ ] Document upload + versioning
- [ ] Chat system
- [ ] Calendar and deadlines
- [ ] Notification system

## 📄 License

MIT License

## 👨‍💻 Author

Michał - [GitHub](https://github.com/michal94mk)
