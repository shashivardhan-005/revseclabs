# Cybersecurity Awareness Portal (RevSecLabs)

## Overview

The **Cybersecurity Awareness Portal** is a comprehensive, enterprise-grade web application developed by **RevSecLabs**. Its primary objective is to enhance organizational security posture by educating employees and stakeholders through interactive, scenario-based learning and rigorous assessment modules. 

In an era of increasing social engineering and phishing attacks, this platform serves as a critical line of defense, ensuring that every member of the organization is well-versed in identifying and mitigating potential cyber threats.

---

## Detailed Features

### 1. Advanced Quiz Engine
- **Scenario-Based Learning**: Quizzes are not just multiple-choice; they are designed around real-world cybersecurity scenarios (e.g., Identifying a phishing email, secure password practices, physical security).
- **Time-Bound Assessments**: Each quiz has a configurable duration, simulating high-pressure environments where quick, correct decision-making is vital.
- **Dynamic Question Banking**: Support for various topics and difficulty levels, allowing admins to tailor assessments to different departments (e.g., more technical quizes for IT, general awareness for HR/Finance).

### 2. Proctored Anti-Cheat System
To ensure the integrity of the results, the portal includes several proctoring features:
- **Full-Screen Enforcement**: Quizzes can be configured to force full-screen mode, preventing users from accessing other browser tools.
- **Tab-Switch Detection**: Real-time monitoring of browser focus. Switching tabs or windows triggers a violation.
- **Auto-Submission**: If a user exceeds the allowed number of violations, their quiz is automatically submitted with a penalty.
- **Copy-Paste Restriction**: Disables right-click and keyboard shortcuts for copying question text or pasting answers from external sources.

### 3. Comprehensive Analytics & Reporting
- **Performance Dashboards**: Users can track their progress over time, seeing scores and areas for improvement.
- **Admin Insights**: High-level views of organizational awareness trends, identifying specific departments or topics that require further training.
- **Audit Logging**: Every critical action (login, quiz start, submission, violation) is logged for security and transparency.

### 4. Automated Communication
- **Personalized Results**: High-fidelity HTML emails are sent automatically upon quiz completion, providing detailed breakdowns of correct and incorrect answers.
- **Welcome & Onboarding**: Automated enrollment emails for new users.

---

## Technical Architecture

The platform is built on a modern, secure stack:
- **Django 5.0**: Leverages the "batteries-included" framework for robust security, ORM, and administrative features.
- **MariaDB/MySQL**: Optimized for handling complex relational data and high-concurrency quiz attempts.
- **Vanilla CSS3 & JS**: Custom-built, high-performance UI using modern design principles (Gradients, Glassmorphism, Responsive Grid).
- **Production-Hardened**: Pre-configured with HSTS, SSL redirection, and secure cookie management for deployment at `https://revseclabs.in`.

---

## Getting Started

### Installation Guide

1. **Clone & Setup**:
   ```bash
   git clone https://github.com/revseclabs/cybersecurity-portal.git
   cd cybersecurity-portal
   python -m venv venv
   .\venv\Scripts\activate
   pip install -r requirements.txt
   ```

2. **Database Initialization**:
   The project includes a consolidated SQL script for quick setup:
   - Create a database: `CREATE DATABASE cybersecurity_db;`
   - Import the script: `mysql -u root -p cybersecurity_db < database_setup.sql`
   - *This script sets up all tables and creates the initial admin user.*

3. **Environment Setup**:
   Create a `.env` file in the root directory:
   ```env
   DEBUG=False
   SECRET_KEY=your_secret_key_here
   BASE_URL=https://revseclabs.in
   DB_NAME=cybersecurity_db
   DB_USER=root
   DB_PASSWORD=your_password
   EMAIL_HOST_USER=your_email@gmail.com
   EMAIL_HOST_PASSWORD=your_app_password
   ```

## Production Notes

- **Domain**: This project is locked to `revseclabs.in` for production.
- **Security**: The `ALLOWED_HOSTS` and `CSRF_TRUSTED_ORIGINS` are pre-configured in `settings.py`.
- **Maintenance**: Use the `python manage.py check --deploy` command to verify the environment before going live.

---

## Contact & Support

**RevSecLabs Team**  
🌐 [www.revseclabs.in](https://revseclabs.in)  
📧 [revseclabs@gmail.com](mailto:revseclabs@gmail.com)  

*Proprietary software. All rights reserved by RevSecLabs.*
