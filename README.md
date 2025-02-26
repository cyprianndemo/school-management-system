## [Click Here to Download the Project Report](https://www.mediafire.com/file/oazbkqfb0z19888/TP+Final.pdf/file)

# School Communication Portal

## Overview

This repository contains the code and documentation for the **School Communication Portal**, a web-based application designed to facilitate communication between teachers and parents. The portal is developed using **PostgreSQL, PHP, HTML, and CSS**.

The primary objective of the School Communication Portal is to create a platform where parents can stay updated about their children's academic progress and school activities. It allows parents to:

- View a list of all teachers
- Search for specific teachers
- Send messages to teachers

Teachers, on the other hand, can:

- Receive and respond to messages from parents

The portal also includes a **secure login system**, where users can log in as an **admin, teacher, or parent**. Each user type has access to different modules, providing a personalized user experience. The login system is designed with security measures to prevent SQL injection attacks.

## Features

### Parent Features:
- View list of teachers
- Search for teachers
- Send messages to teachers

### Teacher Features:
- Receive and respond to messages from parents

### Admin Features:
- Manage teachers and parents
- Oversee communication records

## Database Design

The database for the portal is built using **PostgreSQL**, ensuring robust and scalable data storage. It includes tables for:

- **Admins** (managing the system)
- **Teachers** (handling communication with parents)
- **Parents** (tracking student progress)
- **Students** (linked to parents and teachers)
- **Messages** (storing communication records)

The relationships between these tables are carefully structured to ensure **data integrity and efficiency**.

## Technologies Used

The **School Communication Portal** project utilizes the following technologies:

- **PostgreSQL**: A powerful, open-source relational database system used for managing and storing data.
- **PHP**: A server-side scripting language used to handle authentication, data processing, and communication with the database.
- **HTML/CSS**: Used to structure and style the frontend of the portal.
- **JavaScript** (Optional Enhancements): Can be used to enhance user experience with dynamic interactions.

## Setup Instructions

To set up the project on your local machine:

1. Clone the repository:
   ```bash
   git clone https://github.com/cyprianndemo/school-management-system.git
   cd school-management-system
   ```
2. Set up your **PostgreSQL database** and import the provided SQL schema.
3. Configure database credentials in `db-connect.php`.
4. Start your local server (Apache/Nginx with PHP support).
5. Open the project in your browser and log in with the appropriate user credentials.

## Contributing

Contributions are welcome! If you'd like to contribute:
- Fork the repository
- Create a new branch (`git checkout -b feature-branch`)
- Commit your changes (`git commit -m "Feature description"`)
- Push to your branch (`git push origin feature-branch`)
- Create a Pull Request

## Contributors
This project was developed as part of my projects. Contributors include:

cyprian ndemo
albrieght jeruto

## License

This project is open-source and available under the MIT License.

