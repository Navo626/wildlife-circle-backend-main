# Wildlife Circle Backend

Wildlife-Circle is a dynamic web application built with React.
It serves as a comprehensive platform for wildlife enthusiasts and an administrative tool
for managing various aspects of the application.
The application provides a range of features including user authentication,
product and order management, blog viewing, a gallery, and a unique 3D forest view.
It fetches data from an external API and presents it in a user-friendly format,
offering detailed information about each product.
With features like dynamic listing, detailed information, dark mode support, and responsive design,
Wildlife-Circle aims to enhance the user's browsing experience
and provide administrators with the tools they need to manage content effectively.

This project is the backend part of the Wildlife Circle System.
The frontend part of the project is available [here](https://github.com/nureka-rodrigo/wildlife-circle-frontend).

## Features

- **User Authentication**: The application supports user registration, login, and password reset functionalities.
- **Admin Panel**: An admin panel is available for managing users, gallery, products, orders, and content.
- **Product Management**: Users can view a list of products, view individual product details, and make purchases.
- **Order Management**: Admins can view and manage orders from users.
- **Blog Management**: Users can view a list of blogs and individual blog details.
- **Gallery**: A gallery page is available for users to view images.
- **3D Forest View**: An interactive 3D forest view page is available for users.
- **Dark Mode Support**: The application has a dark mode toggle functionality for a comfortable viewing experience in different lighting conditions.
- **Responsive Design**: Works well on a variety of devices and window or screen sizes.

## Environment Variables

You should create a `.env` file in the root directory of the project to store the following environment variables:

- `DB_CONNECTION`: The database connection type (mysql, pgsql, etc.)
- `DB_HOST`: The database host
- `DB_PORT`: The database port
- `DB_DATABASE`: The database name
- `DB_USERNAME`: The database username
- `DB_PASSWORD`: The database password
- `APP_URL`: The base URL of the application
- `APP_KEY`: The application key
- `COOKIE_PATH`: The path for the cookie
- `COOKIE_DOMAIN`: The domain for the cookie
- `COOKIE_SAMESITE`: The SameSite attribute for the cookie (Strict, Lax, or None)
- `COOKIE_SECURE`: Whether the cookie should only be sent over secure connections (true or false)
- `COOKIE_HTTP_ONLY`: Whether the cookie should only be accessible over HTTP, and not by scripting languages like JavaScript (true or false)
- `PAYHERE_MERCHANT_ID`: The merchant ID for the PayHere payment gateway
- `PAYHERE_MERCHANT_SECRET`: The merchant secret for the PayHere payment gateway
- `PAYHERE_CURRENCY`: The currency for the PayHere payment gateway

Please note that you should never commit your `.env` file to the version control system.
It contains sensitive information that should not be publicly accessible.
Instead, you can provide a `.env.example` file with dummy values to help other developers set up their environment.

## Installation

This project is a Laravel-based backend for the Wildlife Circle System. To set up and run this project locally, you'll need to have [PHP](https://www.php.net/), [Composer](https://getcomposer.org/), and a database system (like [MySQL](https://www.mysql.com/)) installed. Follow these steps:

1. Clone the repository: `git clone https://github.com/nureka-rodrigo/wildlife-circle-backend.git`
2. Navigate into the project directory: `cd wildlife-circle-backend`
3. Install the dependencies: `composer install`
4. Copy the `.env.example` file to create your own `.env` file.
5. Update the `.env` file with your database and other configuration settings.
6. Generate an application key: `php artisan key:generate`
7. Run the database migrations: `php artisan migrate`
8. Create a symbolic link for storage: `php artisan storage:link`
9. Start the application: `php artisan serve`

The application will start running on `http://localhost:8000`.

Please note that this assumes you have a PHP environment set up on your machine.
If you don't,
you can consider
using a tool like [Laravel Homestead](https://laravel.com/docs/homestead) or [Laravel Valet](https://laravel.com/docs/valet).

## Contributing

Thank you for your interest in contributing to our project. However, please note that this is a private contracted project, and we are not accepting external contributions at this time.

If you have any questions or concerns, please feel free to contact the repository contributors directly.
## License

This project is licensed under the terms of
the [Apache-2.0 License](https://github.com/nureka-rodrigo/wildlfe-circle-backend/blob/main/LICENSE).
