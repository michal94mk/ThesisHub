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
- [x] Thesis model with relationships
- [x] Thesis controller with CRUD operations
- [x] Authorization policies for theses
- [x] Database migrations for theses
- [x] Sample thesis data (7 theses with various statuses)
- [x] Frontend views for thesis management (Index, Create, Edit, Show)
- [x] Role-based dashboards with statistics
- [x] Document upload and download (PDF, DOC, DOCX, TXT, ZIP, RAR)
- [x] Document management (list, upload, delete)
- [x] Real-time chat system (student ↔ supervisor communication)
- [x] Message polling with auto-refresh every 5 seconds
- [x] Chat history with read status tracking
- [x] In-app notification system with bell icon
- [x] Notification types: thesis submitted, approved, rejected, returned, new message
- [x] Unread notification badge with auto-refresh
- [x] Mark as read and mark all as read functionality

🚧 In Progress:
- [ ] Document versioning
- [ ] Calendar and deadlines
- [ ] Email notifications

## 📄 License

MIT License

## 👨‍💻 Author

Michał - [GitHub](https://github.com/michal94mk)
