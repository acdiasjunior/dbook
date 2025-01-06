# **DBook Project**

DBook is a PHP-based project that uses CakePHP, MySQL, and RabbitMQ to create a simple API for managing books. This README describes the project structure, how to set up and run the project, and how to use additional tools such as Mailhog and Postman.

---

## Docker-Compose Structure

The project uses `docker-compose` to orchestrate the containers, each responsible for a specific task:

- **`app`**: The main application container, running CakePHP and PHP-FPM.
- **`db`**: The MySQL database container, storing all project data.
- **`mail`**: A lightweight SMTP server for capturing and viewing emails sent by the application. Accessible via a web interface.
- **`queue`**: The RabbitMQ container, used for queuing and managing background tasks.
- **`web`**: The Nginx container, serving the application and routing requests to the app container.
- **`worker`**: PHP Cli + Supervisor container, used for starting the service workers.

---

## Getting Started

Follow these steps to set up and run the project:

### Step 1: Clone the Repository
1. Clone the repository to your local machine:
    ```
    git clone https://github.com/your-repo/dbook.git
    cd dbook
    ```

### Step 2: Copy and Edit the Environment File
1. Copy the `.env.example` file to `.env`:
    ```
    cp .env.example .env
    ```

2. Edit the `.env` file to update the following variables as needed:
   - **Database credentials**:
     - `DB_HOST`
     - `DB_USER`
     - `DB_PASS`
   - **JWT Secret**
     - `JWT_SECRET`
   - **RabbitMQ credentials**:
     - `RABBITMQ_HOST`
     - `RABBITMQ_USER`
     - `RABBITMQ_PASS`
   - **SMTP settings**:
     - `SMTP_HOST`
     - `SMTP_PORT`

### Step 3: Start the Containers
1. Start the Docker containers using the following command:
    ```
    docker-compose up -d
    ```

---

## Mailhog

The application uses **Mailhog** to capture outgoing emails during development. 

### Access Mailhog
1. Open your browser and go to:

    http://localhost:8025

2. View and interact with the emails sent by the application through Mailhog's web interface.

---

## Postman Collection

The project includes a Postman collection for testing API endpoints. The file `DBook.postman_collection.json` is located on the project root directory.
