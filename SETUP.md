# Asset Assessment System Setup Guide

## Overview
A comprehensive role-based asset assessment application built with CodeIgniter 4.

## Features by Role

### Super Admin
- All admin features
- Create Admin accounts
- Full system management

### Admin  
- CRUD Room Data
- CRUD Asset Data
- Define Asset Weights and Benefits
- Create Room-Asset Relationships
- Manage User Logins (Staff & Leader)
- Cannot perform assessments

### GA Staff
- Perform Assessments
- Select rooms and input asset scores (1-10)
- View own assessment history
- Cannot manage rooms/assets/users

### Leader
- View room assessment dashboard
- View asset assessment reports
- Download reports (PDF/Excel)
- Cannot input data

## Setup Instructions

### 1. Database Setup
Create a MySQL database named `asuransi_astra`:
```sql
CREATE DATABASE asuransi_astra;
```

### 2. Environment Configuration
Update `.env` file with your database credentials:
```
database.default.hostname = localhost
database.default.database = asuransi_astra  
database.default.username = your_username
database.default.password = your_password
```

### 3. Run Migrations
```bash
php spark migrate
```

### 4. Start Development Server
```bash
php spark serve
```

## Default Login Credentials
- **Email:** superadmin@example.com
- **Password:** admin123
- **Role:** Super Admin

## Key Features

### Automatic Feasibility Calculation
- Assets with feasibility score > 80% are marked as feasible
- Formula: `(score * weight + benefit_score) * 10`
- Automatically calculated during assessment save

### Role-Based Access Control
- Routes are protected by role-specific filters
- Users see different dashboards based on their role
- Unauthorized access redirects to dashboard with error

### Assessment System
- GA Staff can select rooms and assess assets
- Score validation ensures 1-10 range
- Automatic feasibility calculation
- Assessment history tracking

### Reporting System
- Leaders get comprehensive dashboards
- Filter reports by date, room, or asset  
- Export functionality (PDF/Excel ready for implementation)
- Feasibility statistics and analytics

## Architecture

### Database Tables
- `users` - User accounts with roles
- `rooms` - Room management
- `assets` - Asset data with weights/benefits  
- `room_assets` - Room-asset relationships
- `assessments` - Assessment data with feasibility scores

### Controllers
- `Auth` - Authentication system
- `Dashboard` - Role-based dashboard
- `Users` - User management (Admin/Super Admin)
- `Rooms` - Room management (Admin/Super Admin)
- `Assets` - Asset management (Admin/Super Admin)
- `RoomAssets` - Relationship management
- `Assessments` - GA Staff assessment system
- `Reports` - Leader reporting system

### Security Features
- Role-based route protection
- Password hashing
- Session management
- SQL injection prevention via models
- XSS protection via view escaping

## Next Steps
- Implement PDF/Excel export functionality
- Add data validation and error handling
- Create additional views for all CRUD operations
- Add charts and visualizations for reports
- Implement audit logging
