# Skill Swap Platform

A peer-to-peer learning platform where people teach what they know and learn what they want through 1-on-1 skill exchanges.

## Overview

Skill Swap Platform is a portfolio-ready Laravel application that helps users discover compatible learning partners, create skill-based swap requests, chat with each other, schedule sessions, and leave reviews after completing a swap.

The product is built around a simple idea: one user can offer a skill in exchange for learning a different skill from someone else.

## Features

- User registration and login
- Guided onboarding for building a matchable profile
- Profile photo upload with live preview
- Teach and learn skill fields
- Browse and search users by skill, level, availability, timezone, and format
- Suggested matches on the dashboard
- Swap request flow with accept and decline actions
- Active swap workspace for each exchange
- Basic messaging and shared notes/resources
- Session scheduling with status tracking
- Ratings and reviews after completed swaps
- Saved profiles and user settings

## User Flow

Landing page -> Sign up -> Complete onboarding -> Add teach/learn skills -> Browse matches -> Send swap request -> Accept request -> Chat and schedule -> Complete swap -> Leave review

## Tech Stack

- Laravel 13
- PHP 8.3
- MySQL
- Blade templates
- Vite
- Custom CSS

## Local Setup

1. Clone the repository.
2. Install PHP dependencies:

```bash
composer install
```

3. Install frontend dependencies:

```bash
npm install
```

4. Create your environment file and configure your database:

```bash
copy .env.example .env
```

5. Generate the app key:

```bash
php artisan key:generate
```

6. Run migrations and seed demo data:

```bash
php artisan migrate:fresh --seed
```

7. Create the public storage symlink for uploaded profile images:

```bash
php artisan storage:link
```

8. Build frontend assets:

```bash
npm run build
```

9. Start the development server:

```bash
php artisan serve
```

## Demo Account

- Email: `test@example.com`
- Password: `password`

## Testing

Run the test suite with:

```bash
php artisan test
```

## Project Goals

This project was built as a strong portfolio-style MVP with a complete product flow, not just static pages. It focuses on:

- practical Laravel CRUD and relationship design
- user-centered onboarding and profile creation
- matching and exchange-based product logic
- polished UI and navigation flow
- media uploads and account management

## Future Improvements

- Google OAuth
- Real-time chat
- Match percentage scoring improvements
- Notifications and reminders
- Calendar integration
- Video call support
- Admin moderation tools

## License

This project is open-sourced under the [MIT license](https://opensource.org/licenses/MIT).
