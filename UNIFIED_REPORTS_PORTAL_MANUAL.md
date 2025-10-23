# Unified Reports Portal - Complete System Manual

## Table of Contents
1. [System Overview](#system-overview)
2. [System Architecture](#system-architecture)
3. [Authentication & Security](#authentication--security)
4. [Dashboard Module](#dashboard-module)
5. [SDF Reports Module](#sdf-reports-module)
6. [CRBT Reports Module](#crbt-reports-module)
7. [Emergency Credit Module](#emergency-credit-module)
8. [API Documentation](#api-documentation)
9. [Database Schema](#database-schema)
10. [Troubleshooting](#troubleshooting)
11. [Deployment Guide](#deployment-guide)

---

## System Overview

### Purpose
The Unified Reports Portal is a comprehensive business intelligence system designed to provide real-time analytics and reporting for telecommunications services. The system integrates three main modules:

- **SDF Reports**: Subscriber Data Files analysis
- **CRBT Reports**: Caller Ring Back Tone analytics
- **Emergency Credit**: Credit management and revenue analysis

### Key Features
- **Real-time Dashboard**: Unified overview of all system metrics
- **Interactive Reports**: Dynamic filtering and visualization
- **Session Management**: Secure authentication with timeout handling
- **Multi-database Support**: Integration with multiple data sources
- **Responsive Design**: Mobile-friendly interface
- **Export Capabilities**: Data export functionality

### System Requirements
- **PHP**: 8.1 or higher
- **Laravel**: 11.x
- **MySQL**: 5.7 or higher
- **Node.js**: 16.x or higher (for asset compilation)
- **Web Server**: Apache/Nginx

---

## System Architecture

### Technology Stack
- **Backend**: Laravel 11.x (PHP Framework)
- **Frontend**: Bootstrap 5, Chart.js, ApexCharts
- **Database**: MySQL (Primary), SQLite (Development)
- **Authentication**: Laravel Auth with session management
- **Caching**: Laravel Cache (File-based)

### Database Connections
```php
// Primary Database (Default)
'default' => 'sqlite' // Development
'default' => 'mysql'  // Production

// CRBT Database
'crbt' => [
    'driver' => 'mysql',
    'database' => 'crbt_core_backup',
    'host' => '127.0.0.1',
    'port' => '3306'
]

// Emergency Credit Database
'mysql2' => [
    'driver' => 'mysql',
    'database' => 'emergency_credit',
    'host' => '127.0.0.1',
    'port' => '3306'
]
```

### File Structure
```
unified-reports-portal/
├── app/
│   ├── Http/Controllers/
│   │   ├── DashboardController.php
│   │   ├── CRBTController.php
│   │   ├── EmergencyCreditController.php
│   │   ├── ServiceReportController.php
│   │   └── Admin/AuthController.php
│   └── Models/
├── resources/views/
│   ├── admin/
│   │   ├── dashboard.blade.php
│   │   ├── crbt/
│   │   ├── emergency_credit/
│   │   └── all_services.blade.php
│   └── layouts/
├── routes/
│   └── web.php
└── config/
    └── database.php
```

---

## Authentication & Security

### Login System
**URL**: `/admin/login`

**Default Credentials**:
- Username: `admin` | Password: `admin`
- Username: `admin2` | Password: `admin123`

### Session Management
- **Session Timeout**: 30 minutes of inactivity
- **Warning Period**: 25 minutes (shows warning modal)
- **Auto-logout**: Automatic redirect to login page
- **Activity Tracking**: Mouse, keyboard, scroll, and touch events

### Security Features
- **CSRF Protection**: All forms protected with CSRF tokens
- **Route Protection**: Middleware-based authentication
- **Input Validation**: Server-side validation for all inputs
- **SQL Injection Prevention**: Eloquent ORM and prepared statements

### Session Timeout Implementation
```javascript
// Session timeout configuration
const SESSION_WARNING_TIME = 25 * 60 * 1000; // 25 minutes
const SESSION_TIMEOUT = 30 * 60 * 1000; // 30 minutes

// Activity tracking events
const activityEvents = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];
```

---

## Dashboard Module

### Overview
The unified dashboard provides a comprehensive overview of all system metrics and modules.

**URL**: `/dashboard`

### Features
- **Real-time Metrics**: Live data from all modules
- **Interactive Charts**: Visual representation of data
- **Quick Access**: Direct links to all modules
- **Auto-refresh**: Data updates every 5 minutes
- **System Health**: Uptime and performance monitoring

### Dashboard Components

#### 1. Quick Stats Cards
- **SDF Total Offers**: Shows total and active offers
- **CRBT Daily Active**: Daily active users and base statistics
- **Emergency Credit Revenue**: Revenue totals and transaction counts
- **System Health**: System status and uptime percentage

#### 2. Interactive Charts
- **SDF Status Distribution**: Donut chart showing subscriber status
- **CRBT Interface Usage**: Line chart displaying usage trends
- **Revenue Trends**: Area chart with gradient showing credit vs paid

#### 3. Module Quick Access
- **SDF Reports**: Direct navigation to subscriber data
- **CRBT Reports**: Access to caller ring back tone reports
- **Emergency Credit**: Credit management reports
- **System Settings**: Configuration management

#### 4. Recent Activity
- **Activity Table**: Real-time system actions
- **Status Indicators**: Color-coded success/error status
- **Timestamps**: Precise timing of all activities

### Dashboard API Endpoints
```javascript
// SDF Data
GET /api/v1/offers
Response: { totalOffers: 45, activeOffers: 25, offers: [...] }

// CRBT Data
GET /api/crbt/daily-mis
Response: { data: [...], pagination: {...} }

// Emergency Credit Data
GET /api/v1/emergency-credit/revenue-summary/data
Response: { revenueData: [...], totalRevenue: 0, totalPaid: 0 }
```

---

## SDF Reports Module

### Overview
Subscriber Data Files (SDF) reports provide comprehensive analysis of subscriber services and offerings.

### Available Reports

#### 1. Service Overview
**URL**: `/all-services`
**Purpose**: Overview of all services and their performance metrics

**Features**:
- Total offers count
- Active offers count
- Service performance metrics
- Pagination support
- Real-time data updates

**API Endpoint**: `/api/v1/offers`
**Response Structure**:
```json
{
  "totalOffers": 45,
  "activeOffers": 25,
  "offers": [
    {
      "id": 57,
      "name": "VoiceChat",
      "status": "ACTIVE",
      "date": "2023-03-01 13:09:26",
      "validity": "2035-12-31 00:00:00",
      "app_id": 51,
      "short_code": "400",
      "message": "Ku soo dhawoow adeega Voice Chat"
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 10,
    "total": 45
  }
}
```

#### 2. New Subscribers Analytics
**URL**: `/single-service`
**Purpose**: Detailed analysis of new subscriber trends

**Features**:
- Service selection dropdown
- Date range filtering
- Status-based filtering
- Interactive charts
- Export capabilities

**Database Tables Used**:
- `subs_in_out_count`: Main subscriber data
- `offers`: Service definitions

**Key Queries**:
```sql
-- Get service statistics
SELECT 
    name,
    COUNT(*) as total_subscribers,
    SUM(CASE WHEN status = 'ACTIVE' THEN base_count ELSE 0 END) as active_count,
    SUM(CASE WHEN status = 'FAILED' THEN base_count ELSE 0 END) as failed_count
FROM subs_in_out_count 
WHERE name = ? AND date BETWEEN ? AND ?
GROUP BY name
```

#### 3. Detailed Report by Status
**URL**: `/status-wise-services`
**Purpose**: Status-wise breakdown of subscriber services

**Features**:
- Status filtering (ACTIVE, FAILED, CANCELED)
- Service-wise breakdown
- Trend analysis
- Performance metrics

**Status Types**:
- **ACTIVE**: Successfully subscribed users
- **FAILED**: Failed subscription attempts
- **CANCELED**: Canceled subscriptions

#### 4. Overall Subscription Status
**URL**: `/overall-subscriber-report`
**Purpose**: Comprehensive overview of all subscription statuses

**Features**:
- Total subscriber counts
- Status distribution
- Service performance
- Historical trends

### SDF Database Schema

#### `subs_in_out_count` Table
```sql
CREATE TABLE subs_in_out_count (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    status ENUM('ACTIVE', 'FAILED', 'CANCELED') NOT NULL,
    base_count INT NOT NULL,
    date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name_status (name, status),
    INDEX idx_date (date)
);
```

#### `offers` Table
```sql
CREATE TABLE offers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    status ENUM('ACTIVE', 'INACTIVE') NOT NULL,
    date DATETIME NOT NULL,
    validity DATETIME NOT NULL,
    app_id INT NOT NULL,
    short_code VARCHAR(10) NOT NULL,
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

---

## CRBT Reports Module

### Overview
Caller Ring Back Tone (CRBT) reports provide comprehensive analysis of ringback tone services and user interactions.

### Database Connection
**Connection Name**: `crbt`
**Database**: `crbt_core_backup`
**Host**: `127.0.0.1:3306`

### Available Reports

#### 1. Daily CRBT MIS
**URL**: `/crbt/daily-mis`
**Purpose**: Daily management information system for CRBT services

**Features**:
- Daily statistics and trends
- Base count analysis
- Revenue tracking
- Interactive charts
- Date range filtering

**API Endpoint**: `/api/crbt/daily-mis`
**Response Structure**:
```json
{
  "data": [
    {
      "date": "2025-04-25",
      "parkingBase": 0,
      "activeBase": 135485,
      "graceBase": 102963,
      "suspendBase": 678086,
      "vchurnBase": 158708,
      "invchurnBase": 212121,
      "activeNrml": 127888,
      "graceNrml": 98276,
      "suspendNrml": 651272,
      "vchurnNrml": 152452,
      "invchurnNrml": 202558,
      "SubsRev": "11.31",
      "RenewRev": "4440.29"
    }
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 10,
    "total": 1218,
    "last_page": 122
  }
}
```

**Key Metrics**:
- **activeBase**: Total active subscribers
- **graceBase**: Subscribers in grace period
- **suspendBase**: Suspended subscribers
- **vchurnBase**: Voluntary churn
- **invchurnBase**: Involuntary churn
- **SubsRev**: Subscription revenue
- **RenewRev**: Renewal revenue

#### 2. Hourly CRBT MIS
**URL**: `/crbt/hourly-mis`
**Purpose**: Hourly breakdown of CRBT activities

**Features**:
- Hourly statistics
- Peak usage analysis
- Time-based trends
- Performance metrics

**Database Table**: `HR_WISE_CRBT_MIS`
**Key Fields**:
- `date`: Date of the record
- `currentHour`: Hour of the day (0-23)
- `action`: Type of action (SubscriptionCount, etc.)
- `data`: Count or value for the action

#### 3. Interface-wise Sub/Unsub
**URL**: `/crbt/interface-sub-unsub`
**Purpose**: Analysis of subscription/unsubscription by interface

**Features**:
- Interface-wise statistics
- Subscription trends
- Unsubscription analysis
- Performance comparison

**Database Table**: `INTERFACE_WISE_SUB_UNSUB_MIS`
**Key Fields**:
- `interface`: Interface name
- `action`: SUB or UNSUB
- `data`: Count of actions
- `date`: Date of the record

#### 4. Interface-wise Tone Usage
**URL**: `/crbt/interface-tone`
**Purpose**: Tone usage analysis by interface

**Features**:
- Tone usage statistics
- Interface performance
- Usage trends
- Revenue analysis

**Database Table**: `INTERFACE_WISE_TONE_MIS`
**Key Fields**:
- `interface`: Interface name
- `data`: Usage count
- `date`: Date of the record

#### 5. Status Cycle MIS
**URL**: `/crbt/status-cycle`
**Purpose**: Status cycle analysis for CRBT services

**Features**:
- Status cycle tracking
- Performance metrics
- Trend analysis
- Pagination support

#### 6. HLR Activations
**URL**: `/crbt/hlr-activations`
**Purpose**: Home Location Register activation analysis

**Features**:
- HLR activation statistics
- Geographic analysis
- Performance metrics
- Trend tracking

#### 7. User Information
**URL**: `/crbt/user-info`
**Purpose**: Detailed user information and statistics

**Features**:
- User demographics
- Usage patterns
- Performance metrics
- Historical data

#### 8. User Tone Information
**URL**: `/crbt/user-tone-info`
**Purpose**: User-specific tone usage analysis

**Features**:
- Individual user tone usage
- Preference analysis
- Usage patterns
- Revenue tracking

#### 9. Billing & Charges
**URL**: `/crbt/billing-charges`
**Purpose**: Billing and charging analysis

**Features**:
- Billing statistics
- Charge analysis
- Revenue tracking
- Payment trends

### Corporate CRBT Reports

#### 1. Corporate Accounts
**URL**: `/crbt/corporate-info`
**Purpose**: Corporate account analysis

#### 2. Corporate Users
**URL**: `/crbt/corporate-users`
**Purpose**: Corporate user management and analysis

### Backup Reports

#### 1. CRBT Core Backup
**URL**: `/crbt/backup-reports`
**Purpose**: Backup data analysis and reporting

### CRBT Database Schema

#### `DAILY_CRBT_MIS` Table
```sql
CREATE TABLE DAILY_CRBT_MIS (
    id INT PRIMARY KEY AUTO_INCREMENT,
    date DATE NOT NULL,
    parkingBase INT DEFAULT 0,
    activeBase INT DEFAULT 0,
    graceBase INT DEFAULT 0,
    suspendBase INT DEFAULT 0,
    vchurnBase INT DEFAULT 0,
    invchurnBase INT DEFAULT 0,
    parkingBaseNrml INT DEFAULT 0,
    activeBaseNrml INT DEFAULT 0,
    graceBaseNrml INT DEFAULT 0,
    suspendBaseNrml INT DEFAULT 0,
    vchurnBaseNrml INT DEFAULT 0,
    invchurnBaseNrml INT DEFAULT 0,
    parkingBaseCorp INT DEFAULT 0,
    activeBaseCorp INT DEFAULT 0,
    graceBaseCorp INT DEFAULT 0,
    suspendBaseCorp INT DEFAULT 0,
    vchurnBaseCorp INT DEFAULT 0,
    invchurnBaseCorp INT DEFAULT 0,
    parkingNrml INT DEFAULT 0,
    activeNrml INT DEFAULT 0,
    graceNrml INT DEFAULT 0,
    suspendNrml INT DEFAULT 0,
    vchurnNrml INT DEFAULT 0,
    invchurnNrml INT DEFAULT 0,
    parkingCorp INT DEFAULT 0,
    activeCorp INT DEFAULT 0,
    graceCorp INT DEFAULT 0,
    suspendCorp INT DEFAULT 0,
    vchurnCorp INT DEFAULT 0,
    invchurnCorp INT DEFAULT 0,
    SubsRev DECIMAL(10,2) DEFAULT 0.00,
    RenewRev DECIMAL(10,2) DEFAULT 0.00,
    SubsRevNrml DECIMAL(10,2) DEFAULT 0.00,
    SubsRevCorp DECIMAL(10,2) DEFAULT 0.00,
    RenewRevNrml DECIMAL(10,2) DEFAULT 0.00,
    RenewRevCorp DECIMAL(10,2) DEFAULT 0.00,
    VsmsRev DECIMAL(10,2) DEFAULT 0.00,
    VsmsSuccess INT DEFAULT 0,
    VsmsFailed INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_date (date)
);
```

---

## Emergency Credit Module

### Overview
Emergency Credit module provides comprehensive analysis of credit transactions, revenue tracking, and financial reporting.

### Database Connection
**Connection Name**: `mysql2`
**Database**: `emergency_credit`
**Host**: `127.0.0.1:3306`

### Available Reports

#### 1. Daily Transactions
**URL**: `/emergency-credit/daily`
**Purpose**: Daily transaction analysis and statistics

**Features**:
- Daily transaction counts
- Revenue analysis
- User statistics
- Top users identification
- Date filtering

**API Endpoint**: `/emergency-credit/daily/data`
**Response Structure**:
```json
{
  "dailyStats": {
    "unique_users": 150,
    "total_transactions": 1250,
    "total_units": 50000,
    "avg_units": 40.0
  },
  "topUsers": [
    {
      "msisdn": "252612345678",
      "txn_count": 25
    }
  ]
}
```

**Key Metrics**:
- **unique_users**: Number of unique users
- **total_transactions**: Total transaction count
- **total_units**: Total units transacted
- **avg_units**: Average units per transaction

#### 2. Top Users
**URL**: `/emergency-credit/top-users`
**Purpose**: Analysis of top users by transaction volume

**Features**:
- User ranking by transactions
- Usage patterns analysis
- Revenue contribution
- Historical trends

#### 3. Weekly Trends
**URL**: `/emergency-credit/weekly`
**Purpose**: Weekly trend analysis

**Features**:
- Weekly aggregation
- Trend identification
- Performance comparison
- Seasonal analysis

#### 4. Monthly Overview
**URL**: `/emergency-credit/monthly`
**Purpose**: Monthly comprehensive analysis

**Features**:
- Monthly statistics
- Revenue trends
- User growth
- Performance metrics

#### 5. Transaction Status
**URL**: `/emergency-credit/status`
**Purpose**: Status-wise transaction analysis

**Features**:
- Status distribution
- Success/failure rates
- Performance metrics
- Trend analysis

**Status Types**:
- **CREDIT**: Successful credit transactions
- **REPAID**: Repaid transactions
- **FAILED**: Failed transactions
- **PENDING**: Pending transactions

#### 6. Credit Type Analysis
**URL**: `/emergency-credit/credit-type`
**Purpose**: Analysis by credit type

**Features**:
- Credit type distribution
- Usage patterns
- Revenue analysis
- Performance comparison

**Credit Types**:
- **DATA**: Data credit transactions
- **MINUTES**: Voice minutes credit
- **SMS**: SMS credit transactions

### Revenue Reports

#### 1. Revenue Summary (All)
**URL**: `/emergency-credit/revenue-summary`
**Purpose**: Comprehensive revenue analysis

**Features**:
- Total revenue tracking
- Payment analysis
- Repayment percentage
- Date range filtering

**API Endpoint**: `/api/v1/emergency-credit/revenue-summary/data`
**Response Structure**:
```json
{
  "revenueData": [
    {
      "date_label": "2025-01-01",
      "total_credit": 1500.50,
      "total_paid": 1200.25,
      "repayment_percentage": 80.02
    }
  ]
}
```

**Key Metrics**:
- **total_credit**: Total credit amount (in 10K units)
- **total_paid**: Total amount paid (in 10K units)
- **repayment_percentage**: Percentage of credit repaid

#### 2. Revenue Data Only
**URL**: `/emergency-credit/revenue-data-only`
**Purpose**: Data-specific revenue analysis

**Features**:
- Data credit analysis only
- Revenue tracking
- Payment analysis
- Performance metrics

#### 3. Revenue with Balance
**URL**: `/emergency-credit/revenue-with-balance`
**Purpose**: Revenue analysis including outstanding balances

**Features**:
- Revenue with balance tracking
- Outstanding amount analysis
- Payment trends
- Balance management

### Emergency Credit Database Schema

#### `transaction_credit` Table
```sql
CREATE TABLE transaction_credit (
    id INT PRIMARY KEY AUTO_INCREMENT,
    msisdn VARCHAR(20) NOT NULL,
    credit_type ENUM('DATA', 'MINUTES', 'SMS') NOT NULL,
    units_amount_to_pay DECIMAL(15,2) NOT NULL,
    status ENUM('CREDIT', 'REPAID', 'FAILED', 'PENDING') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_msisdn (msisdn),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    INDEX idx_credit_type (credit_type)
);
```

#### `transaction_repayment` Table
```sql
CREATE TABLE transaction_repayment (
    id INT PRIMARY KEY AUTO_INCREMENT,
    credit_transaction_id INT NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    status ENUM('SUCCESS', 'FAILED', 'PENDING') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (credit_transaction_id) REFERENCES transaction_credit(id),
    INDEX idx_credit_transaction_id (credit_transaction_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);
```

### Key SQL Queries

#### Revenue Summary Query
```sql
SELECT 
    DATE(c.created_at) as date_label,
    ROUND(SUM(c.units_amount_to_pay) / 10000.0, 2) as total_credit,
    ROUND(SUM(COALESCE(r.repaid_amount, 0)) / 10000.0, 2) as total_paid,
    CASE 
        WHEN SUM(c.units_amount_to_pay) = 0 THEN 0 
        ELSE ROUND((SUM(COALESCE(r.repaid_amount, 0)) / SUM(c.units_amount_to_pay)) * 100, 2) 
    END as repayment_percentage
FROM transaction_credit c
LEFT JOIN (
    SELECT 
        rc.id as credit_id, 
        SUM(r.amount) as repaid_amount 
    FROM transaction_repayment r 
    INNER JOIN transaction_credit rc ON r.credit_transaction_id = rc.id 
    WHERE r.status = 'SUCCESS' 
    GROUP BY rc.id
) r ON c.id = r.credit_id
WHERE c.created_at >= ? AND c.created_at <= ?
    AND c.status IN ('CREDIT', 'REPAID')
GROUP BY DATE(c.created_at)
ORDER BY date_label;
```

#### Data Only Revenue Query
```sql
SELECT 
    DATE(c.created_at) as date_label,
    ROUND(SUM(c.units_amount_to_pay) / 10000.0, 2) as total_credit,
    ROUND(SUM(COALESCE(r.repaid_amount, 0)) / 10000.0, 2) as total_paid,
    CASE 
        WHEN SUM(c.units_amount_to_pay) = 0 THEN 0 
        ELSE ROUND((SUM(c.units_amount_to_pay) - COALESCE((
            SELECT SUM(r.amount) 
            FROM transaction_repayment r 
            INNER JOIN transaction_credit rc ON r.credit_transaction_id = rc.id 
            WHERE DATE(rc.created_at) = DATE(c.created_at) 
                AND r.status = 'SUCCESS'
        ), 0) / SUM(c.units_amount_to_pay)) * 100, 2) 
    END as repayment_percentage
FROM transaction_credit c
LEFT JOIN (
    SELECT 
        rc.id as credit_id, 
        SUM(r.amount) as repaid_amount 
    FROM transaction_repayment r 
    INNER JOIN transaction_credit rc ON r.credit_transaction_id = rc.id 
    WHERE r.status = 'SUCCESS' 
        AND rc.credit_type = 'DATA' 
    GROUP BY rc.id
) r ON c.id = r.credit_id
WHERE c.created_at >= ? AND c.created_at <= ?
    AND c.status IN ('CREDIT', 'REPAID')
    AND c.credit_type = 'DATA'
GROUP BY DATE(c.created_at)
ORDER BY date_label;
```

---

## API Documentation

### Authentication
All API endpoints require authentication via session cookies.

### Base URL
```
http://127.0.0.1:8001
```

### SDF API Endpoints

#### Get Offers
```http
GET /api/v1/offers
```

**Response**:
```json
{
  "totalOffers": 45,
  "activeOffers": 25,
  "offers": [...],
  "pagination": {...}
}
```

#### Get Service Report
```http
GET /api/v1/service-report
Parameters:
- service_name (required)
- start_date (optional)
- end_date (optional)
- status (optional)
- page (optional, default: 1)
- per_page (optional, default: 10)
```

### CRBT API Endpoints

#### Get Daily MIS Data
```http
GET /api/crbt/daily-mis
Parameters:
- start_date (optional)
- end_date (optional)
- page (optional, default: 1)
- per_page (optional, default: 10)
```

#### Get Hourly MIS Data
```http
GET /api/crbt/hourly-mis
Parameters:
- start_date (optional)
- end_date (optional)
- page (optional, default: 1)
- per_page (optional, default: 10)
```

#### Get Interface Data
```http
GET /api/crbt/interface-data
Parameters:
- start_date (optional)
- end_date (optional)
- page (optional, default: 1)
- per_page (optional, default: 10)
```

### Emergency Credit API Endpoints

#### Get Daily Data
```http
GET /emergency-credit/daily/data
Parameters:
- date (optional, default: today)
- status (optional)
```

#### Get Revenue Summary
```http
GET /api/v1/emergency-credit/revenue-summary/data
Parameters:
- start_date (optional)
- end_date (optional)
```

#### Get Revenue Data Only
```http
GET /api/v1/emergency-credit/revenue-data-only/data
Parameters:
- start_date (optional)
- end_date (optional)
```

#### Get Revenue with Balance
```http
GET /api/v1/emergency-credit/revenue-with-balance/data
Parameters:
- start_date (optional)
- end_date (optional)
```

### Response Format
All API responses follow this format:
```json
{
  "data": [...],
  "pagination": {
    "current_page": 1,
    "per_page": 10,
    "total": 100,
    "last_page": 10
  },
  "message": "Success"
}
```

### Error Format
```json
{
  "error": "Error message",
  "code": 400,
  "details": "Additional error details"
}
```

---

## Database Schema

### Primary Database (Default)
- **Connection**: `mysql` (production) / `sqlite` (development)
- **Purpose**: Application data, user management, offers

### CRBT Database
- **Connection**: `crbt`
- **Database**: `crbt_core_backup`
- **Purpose**: CRBT service data and analytics

### Emergency Credit Database
- **Connection**: `mysql2`
- **Database**: `emergency_credit`
- **Purpose**: Emergency credit transactions and revenue

### Key Tables

#### Users Table
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

#### Cache Table
```sql
CREATE TABLE cache (
    key VARCHAR(255) PRIMARY KEY,
    value MEDIUMTEXT NOT NULL,
    expiration INT NOT NULL
);
```

#### Jobs Table
```sql
CREATE TABLE jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    queue VARCHAR(255) NOT NULL,
    payload LONGTEXT NOT NULL,
    attempts TINYINT UNSIGNED NOT NULL,
    reserved_at INT UNSIGNED NULL,
    available_at INT UNSIGNED NOT NULL,
    created_at INT UNSIGNED NOT NULL
);
```

---

## Troubleshooting

### Common Issues

#### 1. Database Connection Errors
**Error**: `SQLSTATE[HY000] [2002] Connection refused`

**Solution**:
1. Check database server status
2. Verify connection parameters in `config/database.php`
3. Ensure database credentials are correct
4. Check firewall settings

#### 2. Session Timeout Issues
**Error**: User gets logged out frequently

**Solution**:
1. Check session configuration in `config/session.php`
2. Verify session storage is working
3. Check for JavaScript errors that might prevent activity tracking

#### 3. API Endpoint Not Found
**Error**: `404 Not Found` for API endpoints

**Solution**:
1. Clear route cache: `php artisan route:clear`
2. Check route definitions in `routes/web.php`
3. Verify middleware configuration

#### 4. Data Not Loading
**Error**: Reports show "Loading..." or "No data available"

**Solution**:
1. Check database connections
2. Verify API endpoints are accessible
3. Check browser console for JavaScript errors
4. Verify CSRF tokens are included in requests

#### 5. Chart Not Rendering
**Error**: Charts not displaying

**Solution**:
1. Check if Chart.js and ApexCharts are loaded
2. Verify data format matches chart requirements
3. Check for JavaScript errors in console

### Performance Optimization

#### 1. Database Query Optimization
1. Use proper indexes on frequently queried columns
2. Implement query caching for expensive operations
3. Use pagination for large datasets
4. Optimize JOIN operations

#### 2. Caching Strategy
1. Cache API responses for 15 minutes
2. Use Redis for production caching
3. Implement cache invalidation strategies
4. Cache expensive calculations

#### 3. Frontend Optimization
1. Minify CSS and JavaScript files
2. Use CDN for external libraries
3. Implement lazy loading for large datasets
4. Optimize image assets

### Logging and Monitoring

#### Laravel Logs
Location: `storage/logs/laravel.log`

**Key Log Entries**:
- API request/response logging
- Database query logging
- Error tracking
- Performance monitoring

#### Application Monitoring
1. Monitor API response times
2. Track database query performance
3. Monitor memory usage
4. Track user session activity

---

## Deployment Guide

### Prerequisites
1. **Server Requirements**:
   - PHP 8.1 or higher
   - MySQL 5.7 or higher
   - Apache/Nginx web server
   - Composer
   - Node.js (for asset compilation)

### Installation Steps

#### 1. Clone Repository
```bash
git clone <repository-url>
cd unified-reports-portal
```

#### 2. Install Dependencies
```bash
composer install
npm install
```

#### 3. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

#### 4. Database Configuration
Update `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=unified_reports
DB_USERNAME=your_username
DB_PASSWORD=your_password

# CRBT Database
DB_SECOND_HOST=127.0.0.1
DB_SECOND_PORT=3306
DB_SECOND_DATABASE=crbt_core_backup
DB_SECOND_USERNAME=your_username
DB_SECOND_PASSWORD=your_password
```

#### 5. Database Migration
```bash
php artisan migrate
php artisan db:seed
```

#### 6. Asset Compilation
```bash
npm run build
```

#### 7. Cache Configuration
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### 8. Set Permissions
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### Production Configuration

#### 1. Web Server Configuration (Apache)
```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /path/to/unified-reports-portal/public
    
    <Directory /path/to/unified-reports-portal/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/unified-reports-error.log
    CustomLog ${APACHE_LOG_DIR}/unified-reports-access.log combined
</VirtualHost>
```

#### 2. Web Server Configuration (Nginx)
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/unified-reports-portal/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

#### 3. SSL Configuration
1. Obtain SSL certificate
2. Configure HTTPS redirect
3. Update application URL in `.env`

#### 4. Security Hardening
1. Set proper file permissions
2. Configure firewall rules
3. Enable HTTPS only
4. Set secure session configuration
5. Implement rate limiting

### Backup Strategy

#### 1. Database Backup
```bash
# Daily backup script
mysqldump -u username -p unified_reports > backup_$(date +%Y%m%d).sql
mysqldump -u username -p crbt_core_backup > crbt_backup_$(date +%Y%m%d).sql
mysqldump -u username -p emergency_credit > ec_backup_$(date +%Y%m%d).sql
```

#### 2. Application Backup
```bash
# Backup application files
tar -czf app_backup_$(date +%Y%m%d).tar.gz /path/to/unified-reports-portal
```

#### 3. Automated Backup
Create cron job for automated backups:
```bash
# Add to crontab
0 2 * * * /path/to/backup-script.sh
```

### Monitoring and Maintenance

#### 1. Health Checks
- Monitor application logs
- Check database connectivity
- Verify API endpoints
- Monitor disk space

#### 2. Performance Monitoring
- Track response times
- Monitor memory usage
- Check database query performance
- Monitor user session activity

#### 3. Regular Maintenance
- Clear old cache files
- Rotate log files
- Update dependencies
- Security patches

---

## Conclusion

This manual provides comprehensive documentation for the Unified Reports Portal system. The system is designed to provide real-time analytics and reporting for telecommunications services with three main modules: SDF Reports, CRBT Reports, and Emergency Credit.

### Key Features Summary:
- **Unified Dashboard**: Real-time overview of all system metrics
- **Interactive Reports**: Dynamic filtering and visualization
- **Session Management**: Secure authentication with timeout handling
- **Multi-database Support**: Integration with multiple data sources
- **Responsive Design**: Mobile-friendly interface
- **Comprehensive Analytics**: Detailed reporting across all modules

### Support and Maintenance:
- Regular system monitoring
- Database optimization
- Security updates
- Performance tuning
- User training and support

For additional support or questions, please refer to the system logs and contact the development team.

---

**Document Version**: 1.0  
**Last Updated**: October 2025  
**System Version**: Unified Reports Portal v1.0
