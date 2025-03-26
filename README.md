# Laravel Backend for Multi-Platform Posting

## Overview
This project is a Laravel-based backend that allows users to manage and schedule posts across multiple social media platforms, including Twitter and LinkedIn. It integrates with Large Language Models (LLMs) to assist users in generating content.

## Features
- **User Account Linking**: Connect user accounts to Twitter, LinkedIn, and other platforms.
- **Post Creation & Scheduling**: Create posts and schedule them for automatic publishing using Laravel's scheduling, command, and queue system.
- **Post History & Status Tracking**: View post history, check their status, and track interactions (likes, comments, shares).
- **AI-Powered Content Generation**: Utilize Gemini 2 LLM with Chain-of-Thought and Meta Prompting techniques to generate high-quality post content.

## Tech Stack
- **Framework**: Laravel
- **Queue System**: Laravel Queues & Workers
- **Scheduler**: Laravel Task Scheduling
- **AI Integration**: Google Gemini 2 API
- **Database**: MySQL
- **Authentication**: OAuth for social media account linking

## Installation & Setup
### Prerequisites
- PHP 8.1
- Composer
- MySQL
- Laravel 10.x

### Steps
1. **Clone the Repository**
   ```sh
   git clone https://github.com/dtvn-training/2025-DN-PHP-Backend.git
   cd 2025-DN-PHP-Backend
   git checkout final
   ```

2. **Install Dependencies**
   ```sh
   composer install
   ```

3. **Set Up Environment**
   Copy the `.env.example` file and configure your environment variables:
   ```sh
   cp .env.example .env
   ```
   Update database credentials and API keys.

4. **Generate App Key**
   ```sh
   php artisan key:generate
   ```

5. **Run Migrations**
   ```sh
   php artisan migrate
   ```

6. **Start Queue Worker & Scheduler**
   ```sh
   php artisan queue:work --verbose --tries=1
   php artisan schedule:work
   ```

7. **Run the Application**
   ```sh
   php artisan serve
   ```

## API Endpoints

### User
| Method | Endpoint | Description |
|--------|---------|-------------|
| GET    | `/api/users` | Get all users |
| POST   | `/api/users` | Create a new user |
| GET    | `/api/users/{id}` | Get user by ID |
| PUT    | `/api/users/{id}` | Update user by ID |
| DELETE | `/api/users/{id}` | Delete user by ID |
| PUT    | `/api/deleted-users/{deleted_user_id}` | Restore deleted user |
| GET    | `/api/deleted-users` | Get deleted users |

### Posts
| Method | Endpoint | Description |
|--------|---------|-------------|
| POST   | `/api/posts` | Create a post |
| GET    | `/api/posts/{id}` | Get post by ID |
| PUT    | `/api/posts/{id}` | Update post by ID |
| DELETE | `/api/posts/{id}` | Delete post by ID |
| PUT    | `/api/deleted-posts/{deleted_post_id}` | Restore deleted post |
| GET    | `/api/deleted-posts` | Get deleted posts |

### Tweets
| Method | Endpoint | Description |
|--------|---------|-------------|
| POST   | `/api/tweets` | Create a tweet |
| DELETE | `/api/tweets/{id}` | Delete a tweet |
| GET    | `/api/tweets/{id}` | Get tweet by ID |
| GET    | `/api/tweets/{id}/interactions` | Get interactions of a tweet |

### Profile
| Method | Endpoint | Description |
|--------|---------|-------------|
| GET    | `/api/me` | Get authenticated user profile |
| GET    | `/api/me/tweets` | Get my tweets |
| GET    | `/api/me/posts` | Get my posts |
| GET    | `/api/me/social-accounts` | Get my linked social accounts |

### Social Account
| Method | Endpoint | Description |
|--------|---------|-------------|
| GET    | `/api/social-accounts` | Get social account by user and platform |

### Interaction
| Method | Endpoint | Description |
|--------|---------|-------------|
| GET    | `/api/interactions/platform/{platform_id}` | Get interactions by post platform ID |
| GET    | `/api/interactions/post/{post_id}` | Get interactions by post ID |

### Content
| Method | Endpoint | Description |
|--------|---------|-------------|
| POST   | `/api/contents` | Enhance content using AI |

### Authentication
| Method | Endpoint | Description |
|--------|---------|-------------|
| POST   | `/api/login` | User login |
| POST   | `/api/register` | User registration |


## License
This project is licensed under the [MIT License](LICENSE).

