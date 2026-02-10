# Cybersecurity Awareness Portal (RevSecLabs)

## Overview

The **Cybersecurity Awareness Portal** is a comprehensive, enterprise-grade web application developed by **RevSecLabs**. Its primary objective is to enhance organizational security posture by educating employees and stakeholders through interactive, scenario-based learning and rigorous assessment modules. 

In an era of increasing social engineering and phishing attacks, this platform serves as a critical line of defense, ensuring that every member of the organization is well-versed in identifying and mitigating potential cyber threats.

---

## Detailed Features

### 1. Advanced Quiz Engine
- **Scenario-Based Learning**: Quizzes are designed around real-world cybersecurity scenarios (e.g., Identifying a phishing email, secure password practices, physical security).
- **Time-Bound Assessments**: Each quiz has a configurable duration, simulating high-pressure environments where quick, correct decision-making is vital.
- **Dynamic Question Banking**: Support for various topics and difficulty levels, allowing admins to tailor assessments to different departments.

### 2. Proctored Anti-Cheat System
To ensure the integrity of the results, the portal includes several proctoring features:
- **Full-Screen Enforcement**: Quizzes force full-screen mode to prevent users from accessing other browser tools.
- **Tab-Switch Detection**: Real-time monitoring of browser focus. Switching tabs or windows triggers a violation.
- **Auto-Submission**: If a user exceeds the allowed number of violations, their quiz is automatically submitted.
- **Copy-Paste Restriction**: Disables right-click and keyboard shortcuts for copying question text or pasting answers.

### 3. Comprehensive Analytics & Reporting
- **Performance Dashboards**: Users can track their progress over time, seeing scores and areas for improvement.
- **Admin Insights**: High-level views of organizational awareness trends, identifying specific departments or topics that require further training.
- **Audit Logging**: Every critical action (login, quiz start, submission, violation) is logged for security and transparency.

### 4. Automated Communication
- **Personalized Results**: High-fidelity HTML emails are sent automatically upon quiz completion, providing detailed breakdowns.
- **Welcome & Onboarding**: Automated enrollment emails for new users.

---

## Technical Architecture

The platform is built on a modern, secure stack:
- **CodeIgniter 4.7**: A powerful PHP framework with a very small footprint, built for developers who need a simple and elegant toolkit to create full-featured web applications.
- **MariaDB/MySQL**: Optimized for handling complex relational data and high-concurrency quiz attempts.
- **Vanilla CSS3 & JS**: Custom-built, high-performance UI using modern design principles (Gradients, Glassmorphism, Responsive Grid).
- **Production-Hardened**: Pre-configured with security headers and secure session management.

---

## Getting Started

### Installation Guide

1. **Clone & Setup**:
   ```bash
   git clone https://github.com/revseclabs/cybersecurity-portal.git
   cd cybersecurity-portal
   composer install
   ```

2. **Database Initialization**:
   - Create a database: `CREATE DATABASE cybersecurity_db;`
   - Import the provided SQL script: `mysql -u root -p cybersecurity_db < database_setup.sql` (or use the `database.sql` file in the root).

3. **Environment Setup**:
   Copy `env` to `.env` and configure your settings:
   ```env
   CI_ENVIRONMENT = development
   app.baseURL = 'http://localhost/revseclabs/public/'
   database.default.hostname = localhost
   database.default.database = cybersecurity_db
   database.default.username = root
   database.default.password = 
   ```

4. **Run the Application**:
   If using XAMPP, place the folder in `htdocs` and access it via `http://localhost/revseclabs/public/`.
   Alternatively, use the built-in server:
   ```bash
   php spark serve
   ```

## Production Notes

- **Base URL**: Ensure `app.baseURL` in `.env` matches your production domain (e.g., `https://revseclabs.in`).
- **Permissions**: Ensure the `writable` directory is writable by the web server.
- **Security**: Set `CI_ENVIRONMENT = production` in your `.env` file for live deployments.

---

## Contact & Support

**RevSecLabs Team**  
🌐 [www.revseclabs.in](https://revseclabs.in)  
📧 [revseclabs@gmail.com](mailto:revseclabs@gmail.com)  

*Proprietary software. All rights reserved by RevSecLabs.*
