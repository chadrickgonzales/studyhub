# StudyHub

A collaborative learning platform where students can create and join study rooms, share resources, and engage in group study sessions.

## Features

- Create and join study rooms
- Real-time chat and messaging
- Share study resources and materials
- Schedule and manage study sessions
- Collaborative note-taking
- Task management and to-do lists
- Room categories and organization
- User profiles and reputation system

## Requirements

- PHP >= 8.1
- Composer
- Node.js & NPM
- MySQL
- XAMPP (or similar local development environment)

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/studyhub.git
cd studyhub
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install NPM dependencies:
```bash
npm install
```

4. Create environment file:
```bash
cp .env.example .env
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Configure your database in `.env` file:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=studyhub
DB_USERNAME=root
DB_PASSWORD=
```

7. Run migrations:
```bash
php artisan migrate
```

8. Start the development server:
```bash
php artisan serve
npm run dev
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
