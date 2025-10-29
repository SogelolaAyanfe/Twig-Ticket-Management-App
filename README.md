# Twig Ticket Management System

A modern ticket management web application built with PHP, Twig templating, and session-based authentication. Features secure user authentication with bcrypt, full CRUD operations for tickets, and real-time dashboard statistics.

**Live Demo:** https://twig-ticket-management-web-app.up.railway.app

## Features

- **Authentication:** Secure signup/login with bcrypt password hashing and encrypted session tokens
- **Ticket CRUD:** Create, read, update, and delete tickets with status tracking (Open, In Progress, Closed)
- **Dashboard:** Real-time statistics showing total, open, in-progress, and resolved tickets
- **Responsive UI:** Modern card-based design with status tags and smooth animations

## Tech Stack

PHP 8.2+ • Twig 3.0 • Bcrypt • Sessions • Railway

## Quick Start

### Prerequisites
- PHP 8.2 or higher
- Composer

### Installation
```bash
# Clone the repository
`git clone https://github.com/SogelolaAyanfe/Twig-Ticket-Management-App/.git`
`cd stage-2-twig`

# Install dependencies
`composer install`

# Run development server
`php -S localhost:8000`

# Open browser to http://localhost:8000
```

## Project Structure
```
stage-2-twig/
├── public/css/              # Stylesheets
├── src/
│   ├── config/              # Session configuration
│   └── modules/             # Auth & ticket logic (PHP classes)
├── templates/               # Twig templates
│   ├── auth/                # Login & signup pages
│   ├── components/          # Reusable components
│   └── *.html.twig          # Dashboard & ticket manager
├── index.php                # Main entry point & router
├── composer.json            # Dependencies
└── Procfile                 # Railway deployment config
```

## Usage

1. **Sign Up** at `/signup` (password min. 10 characters)
2. **Login** at `/login` with your credentials
3. **View Dashboard** at `/dashboard` for ticket statistics
4. **Manage Tickets** at `/ticket-manager` - create, edit, or delete tickets
5. **Logout** - click logout button to end session

## Routes

| Route | Method | Description | Auth Required |
|-------|--------|-------------|---------------|
| `/home` | GET | Landing page | No |
| `/signup` | GET/POST | User registration | No |
| `/login` | GET/POST | User authentication | No |
| `/dashboard` | GET | Statistics dashboard | Yes |
| `/ticket-manager` | GET/POST | Ticket CRUD | Yes |
| `/logout` | GET | End session | Yes |

## Deployment

Deployed on Railway. To deploy your own:
1. Push code to GitHub
2. Connect repo to Railway
3. Set start command: `php -S 0.0.0.0:$PORT`

## Security

- Bcrypt password hashing with salt
- Base64 encrypted session tokens
- 24-hour session expiration
- Protected routes with authorization checks
- Input validation (email format, password length)



---


