# ThesisHub

System zarządzania pracami dyplomowymi dla uczelni wyższych.

## 🚀 Technologie

- **Backend:** Laravel 12
- **Frontend:** Vue 3 + Inertia.js
- **Styling:** Tailwind CSS
- **Database:** SQLite (dev) / MySQL (production)
- **Auth:** Laravel Breeze

## 📦 Instalacja

```bash
# Sklonuj repo
git clone https://github.com/michal94mk/ThesisHub.git
cd ThesisHub

# Zainstaluj zależności
composer install
npm install

# Skonfiguruj .env
cp .env.example .env
php artisan key:generate

# Uruchom migracje i seedery
php artisan migrate:fresh --seed

# Uruchom serwery
php artisan serve
npm run dev
```

## 👥 Użytkownicy testowi

Po uruchomieniu `php artisan migrate:fresh --seed` dostępni są użytkownicy:

### Administrator
- **Email:** admin@thesis.pl
- **Hasło:** password
- **Dostęp:** Panel administracyjny

### Promotorzy
- **Email:** kowalski@thesis.pl / password
- **Email:** nowak@thesis.pl / password  
- **Email:** wisniewski@thesis.pl / password
- **Dostęp:** Panel promotora

### Studenci
- **Email:** student1@thesis.pl / password
- **Email:** student2@thesis.pl / password
- **Email:** student3@thesis.pl / password
- **Email:** student4@thesis.pl / password
- **Email:** student5@thesis.pl / password
- **Dostęp:** Panel studenta

## 🔐 System ról

Aplikacja posiada 3 role:

- **admin** - pełny dostęp do systemu
- **supervisor** (promotor) - zarządzanie pracami studentów
- **student** - zgłaszanie i zarządzanie własnymi pracami

## 📝 Status projektu

✅ Ukończone:
- [x] Setup Laravel + Breeze + Inertia + Vue 3
- [x] System autoryzacji z 3 rolami
- [x] Middleware sprawdzające role
- [x] Seedery z użytkownikami testowymi

🚧 W trakcie:
- [ ] Model Thesis (prace dyplomowe)
- [ ] Upload dokumentów + wersjonowanie
- [ ] System czatu
- [ ] Kalendarz i terminy
- [ ] System powiadomień

## 📄 Licencja

MIT License

## 👨‍💻 Autor

Michał - [GitHub](https://github.com/michal94mk)
