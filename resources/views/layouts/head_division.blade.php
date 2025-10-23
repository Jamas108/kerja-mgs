<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Kepala Divisi') - Sistem Manajemen Kinerja</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #00a0eb;
            --primary-hover: #0084c2;
            --primary-light: #e6f7ff;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #0ea5e9;
            --dark-color: #1e293b;
            --light-color: #f8fafc;
            --border-color: #e2e8f0;
            --body-bg: #f1f5f9;
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 80px;
            --header-height: 70px;
            --card-border-radius: 16px;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --transition-speed: 0.3s;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--body-bg);
            color: var(--dark-color);
            overflow-x: hidden;
        }

        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Layout */
        .wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: white;
            box-shadow: var(--shadow-md);
            transition: all var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
            overflow-y: auto;
            border-right: 1px solid var(--border-color);
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        .sidebar-header {
            display: flex;
            align-items: left;
            padding: 24px 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .sidebar-brand img,
        .sidebar-brand svg {
            width: 38px;
            height: 45px;
            transition: transform var(--transition-speed) ease;
        }

        .sidebar.collapsed .sidebar-brand img,
        .sidebar.collapsed .sidebar-brand svg {
            transform: scale(1.2);
        }

        .sidebar-brand-text {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary-color);
            white-space: nowrap;
            opacity: 1;
            transition: opacity var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar.collapsed .sidebar-brand-text {
            opacity: 0;
            width: 0;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .sidebar-menu-header {
            padding: 10px 24px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: var(--secondary-color);
            font-weight: 600;
            opacity: 0.8;
            white-space: nowrap;
            transition: opacity var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar.collapsed .sidebar-menu-header {
            opacity: 0;
        }

        .sidebar-menu-items {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu-item {
            margin: 6px 12px;
            border-radius: 12px;
            transition: all var(--transition-speed) ease;
            position: relative;
        }

        .sidebar-menu-link {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            text-decoration: none;
            color: var(--secondary-color);
            border-radius: 12px;
            transition: all var(--transition-speed) ease;
            position: relative;
            z-index: 1;
        }

        .sidebar-menu-link:hover {
            color: var(--primary-color);
        }

        .sidebar-menu-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: var(--primary-light);
            border-radius: 12px;
            opacity: 0;
            transition: opacity var(--transition-speed) ease;
            z-index: -1;
        }

        .sidebar-menu-link:hover::before {
            opacity: 1;
        }

        .sidebar-menu-link.active {
            color: white;
        }

        .sidebar-menu-link.active::before {
            background-color: var(--primary-color);
            opacity: 1;
        }

        .sidebar-menu-icon {
            min-width: 24px;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            transition: margin var(--transition-speed) ease;
        }

        .sidebar.collapsed .sidebar-menu-icon {
            margin-right: 0;
        }

        .sidebar-menu-text {
            white-space: nowrap;
            opacity: 1;
            transition: opacity var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar.collapsed .sidebar-menu-text {
            opacity: 0;
            width: 0;
        }

        .sidebar-divider {
            height: 1px;
            background-color: var(--border-color);
            margin: 16px 24px;
            opacity: 1;
            transition: margin var(--transition-speed) ease;
        }

        .sidebar.collapsed .sidebar-divider {
            margin: 16px 12px;
        }

        .sidebar-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--border-color);
            margin-top: auto;
            transition: padding var(--transition-speed) ease;
        }

        .sidebar.collapsed .sidebar-footer {
            padding: 16px 12px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            min-width: 40px;
            height: 40px;
            border-radius: 12px;
            background-color: var(--primary-light);
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 16px;
            transition: border-radius var(--transition-speed) ease;
        }

        .sidebar.collapsed .user-avatar {
            border-radius: 50%;
        }

        .user-details {
            opacity: 1;
            transition: opacity var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar.collapsed .user-details {
            opacity: 0;
            width: 0;
        }

        .user-name {
            font-weight: 600;
            font-size: 14px;
            color: var(--dark-color);
            margin-bottom: 2px;
        }

        .user-role {
            font-size: 12px;
            color: var(--secondary-color);
        }

        /* Tooltip for collapsed sidebar */
        .sidebar.collapsed .sidebar-menu-item:hover::after {
            content: attr(data-title);
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            background-color: var(--dark-color);
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            white-space: nowrap;
            z-index: 10;
            margin-left: 10px;
            font-size: 12px;
            box-shadow: var(--shadow-md);
            opacity: 1;
        }

        .sidebar.collapsed .sidebar-menu-item:hover::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 100%;
            transform: translateY(-50%);
            border: 6px solid transparent;
            border-right-color: var(--dark-color);
            margin-left: -2px;
            z-index: 10;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            transition: margin-left var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar.collapsed~.main-content {
            margin-left: var(--sidebar-collapsed-width);
        }

        /* Header */
        .header {
            background-color: white;
            height: var(--header-height);
            padding: 0 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .sidebar-toggle {
            background: transparent;
            border: none;
            color: var(--secondary-color);
            cursor: pointer;
            font-size: 18px;
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all var(--transition-speed) ease;
            background-color: var(--light-color);
        }

        .sidebar-toggle:hover {
            color: var(--primary-color);
            background-color: var(--primary-light);
        }

        .page-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--dark-color);
            margin: 0;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .notification-badge {
            position: relative;
        }

        .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background-color: var(--light-color);
            color: var(--secondary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            cursor: pointer;
            transition: all var(--transition-speed) ease;
        }

        .notification-icon:hover {
            background-color: var(--primary-light);
            color: var(--primary-color);
        }

        .notification-count {
            position: absolute;
            top: -6px;
            right: -6px;
            background-color: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
            font-weight: 600;
        }

        .search-container {
            position: relative;
            width: 250px;
        }

        .search-input {
            width: 100%;
            padding: 10px 16px 10px 42px;
            border: none;
            background-color: var(--light-color);
            border-radius: 12px;
            font-size: 14px;
            transition: all var(--transition-speed) ease;
        }

        .search-input:focus {
            outline: none;
            box-shadow: 0 0 0 2px var(--primary-light);
            background-color: white;
        }

        .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary-color);
            font-size: 14px;
        }

        .user-dropdown {
            position: relative;
        }

        .user-dropdown-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            padding: 6px;
            border-radius: 12px;
            transition: all var(--transition-speed) ease;
        }

        .user-dropdown-toggle:hover {
            background-color: var(--light-color);
        }

        .user-dropdown-avatar {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background-color: var(--primary-light);
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 16px;
        }

        .user-dropdown-info {
            display: flex;
            flex-direction: column;
        }

        .user-dropdown-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--dark-color);
        }

        .user-dropdown-role {
            font-size: 12px;
            color: var(--secondary-color);
        }

        .user-dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background-color: white;
            border-radius: 16px;
            box-shadow: var(--shadow-xl);
            width: 240px;
            padding: 12px;
            margin-top: 12px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
            border: 1px solid var(--border-color);
        }

        .user-dropdown-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .user-dropdown-menu::before {
            content: '';
            position: absolute;
            top: -6px;
            right: 24px;
            width: 12px;
            height: 12px;
            background-color: white;
            transform: rotate(45deg);
            border-left: 1px solid var(--border-color);
            border-top: 1px solid var(--border-color);
        }

        .user-dropdown-header {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 8px;
        }

        .user-dropdown-header-avatar {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background-color: var(--primary-light);
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 18px;
        }

        .user-dropdown-header-info {
            display: flex;
            flex-direction: column;
        }

        .user-dropdown-header-name {
            font-size: 16px;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 2px;
        }

        .user-dropdown-header-email {
            font-size: 13px;
            color: var(--secondary-color);
        }

        .user-dropdown-divider {
            height: 1px;
            background-color: var(--border-color);
            margin: 8px 0;
        }

        .user-dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            color: var(--secondary-color);
            text-decoration: none;
            border-radius: 12px;
            transition: all var(--transition-speed) ease;
        }

        .user-dropdown-item:hover {
            background-color: var(--primary-light);
            color: var(--primary-color);
        }

        .user-dropdown-item.danger:hover {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
        }

        .user-dropdown-icon {
            font-size: 16px;
            width: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .theme-switch {
            width: 44px;
            height: 22px;
            background-color: var(--secondary-color);
            border-radius: 11px;
            position: relative;
            cursor: pointer;
            transition: all var(--transition-speed) ease;
        }

        .theme-switch.active {
            background-color: var(--primary-color);
        }

        .theme-switch-toggle {
            position: absolute;
            top: 2px;
            left: 2px;
            width: 18px;
            height: 18px;
            background-color: white;
            border-radius: 50%;
            transition: all var(--transition-speed) ease;
        }

        .theme-switch.active .theme-switch-toggle {
            left: calc(100% - 20px);
        }

        /* Content Area */
        .content {
            padding: 30px;
        }

        /* Cards */
        .card {
            background-color: white;
            border-radius: var(--card-border-radius);
            box-shadow: var(--shadow-sm);
            border: none;
            transition: all var(--transition-speed) ease;
            height: 100%;
        }

        .card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .card-stat {
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 20px;
            position: relative;
            overflow: hidden;
        }

        .card-stat-background {
            position: absolute;
            top: 0;
            right: 0;
            width: 120px;
            height: 120px;
            background-color: var(--primary-light);
            border-radius: 100% 0 0 0;
            opacity: 0.3;
            transform: translate(40px, -40px);
            z-index: 0;
        }

        .card-stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            position: relative;
            z-index: 1;
        }

        .card-stat-icon.primary {
            background-color: var(--primary-light);
            color: var(--primary-color);
        }

        .card-stat-icon.success {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }

        .card-stat-icon.danger {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
        }

        .card-stat-icon.info {
            background-color: rgba(14, 165, 233, 0.1);
            color: var(--info-color);
        }

        .card-stat-content {
            flex: 1;
            position: relative;
            z-index: 1;
        }

        .card-stat-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--secondary-color);
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .card-stat-value {
            font-size: 32px;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 0;
            display: flex;
            align-items: flex-end;
            gap: 6px;
        }

        .card-stat-value-trend {
            font-size: 14px;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 6px;
            margin-bottom: 4px;
        }

        .card-stat-value-trend.up {
            color: var(--success-color);
            background-color: rgba(16, 185, 129, 0.1);
        }

        .card-stat-value-trend.down {
            color: var(--danger-color);
            background-color: rgba(239, 68, 68, 0.1);
        }

        .card-stat-description {
            font-size: 13px;
            color: var(--secondary-color);
            margin-top: 4px;
        }

        .card-header {
            background-color: white;
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-radius: var(--card-border-radius) var(--card-border-radius) 0 0;
        }

        .card-header-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--dark-color);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-header-actions {
            display: flex;
            gap: 8px;
        }

        .card-body {
            padding: 24px;
        }

        /* Tables */
        .table {
            margin-bottom: 0;
        }

        .table th {
            border-top: none;
            font-weight: 600;
            color: var(--secondary-color);
            padding: 12px 16px;
            background-color: var(--light-color);
            white-space: nowrap;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table td {
            padding: 16px;
            vertical-align: middle;
            border-bottom-width: 1px;
            font-size: 14px;
        }

        .table tr:last-child td {
            border-bottom: none;
        }

        .table-responsive {
            /* border-radius: 12px; */
            overflow: hidden;
            /* border: 1px solid var(--border-color); */
        }

        /* Badges */
        .badge {
            padding: 6px 10px;
            font-weight: 500;
            font-size: 12px;
            border-radius: 8px;
        }

        .badge-primary {
            background-color: var(--primary-light);
            color: var(--primary-color);
        }

        .badge-success {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }

        .badge-danger {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
        }

        .badge-warning {
            background-color: rgba(245, 158, 11, 0.1);
            color: var(--warning-color);
        }

        .badge-info {
            background-color: rgba(14, 165, 233, 0.1);
            color: var(--info-color);
        }

        .badge-pill {
            border-radius: 50rem;
        }

        /* Buttons */
        .btn {
            padding: 10px 16px;
            font-weight: 500;
            border-radius: 12px;
            transition: all var(--transition-speed) ease;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 14px;
            border-radius: 8px;
        }

        .btn-lg {
            padding: 12px 20px;
            font-size: 16px;
        }

        .btn-icon {
            width: 40px;
            height: 40px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
        }

        .btn-icon.btn-sm {
            width: 32px;
            height: 32px;
            border-radius: 8px;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .btn-light {
            background-color: var(--light-color);
            border-color: var(--light-color);
            color: var(--secondary-color);
        }

        .btn-light:hover {
            background-color: var(--primary-light);
            border-color: var(--primary-light);
            color: var(--primary-color);
        }

        .border-left-primary {
            border-left: 4px solid var(--primary-color);
        }

        .border-left-success {
            border-left: 4px solid var(--success-color);
        }

        .border-left-danger {
            border-left: 4px solid var(--danger-color);
        }

        .border-left-info {
            border-left: 4px solid var(--info-color);
        }

        /* Utilities */
        .text-primary {
            color: var(--primary-color) !important;
        }

        .text-success {
            color: var(--success-color) !important;
        }

        .text-danger {
            color: var(--danger-color) !important;
        }

        .text-info {
            color: var(--info-color) !important;
        }

        .text-warning {
            color: var(--warning-color) !important;
        }

        .bg-primary-light {
            background-color: var(--primary-light) !important;
        }

        .avatar-stack {
            display: flex;
            align-items: center;
        }

        .avatar-stack-item {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: var(--primary-light);
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 12px;
            border: 2px solid white;
            margin-left: -10px;
        }

        .avatar-stack-item:first-child {
            margin-left: 0;
        }

        .avatar-stack-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .avatar-stack-count {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: var(--light-color);
            color: var(--secondary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 12px;
            border: 2px solid white;
            margin-left: -10px;
        }

        /* Progress */
        .progress {
            height: 8px;
            background-color: var(--light-color);
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-bar {
            background-color: var(--primary-color);
            border-radius: 4px;
        }

        .progress-bar-success {
            background-color: var(--success-color);
        }

        .progress-bar-danger {
            background-color: var(--danger-color);
        }

        .progress-bar-warning {
            background-color: var(--warning-color);
        }

        .progress-bar-info {
            background-color: var(--info-color);
        }

        /* Alerts */
        .alert {
            border-radius: 12px;
            border: none;
            padding: 16px;
        }

        .alert-primary {
            background-color: var(--primary-light);
            color: var(--primary-color);
        }

        .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }

        .alert-danger {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
        }

        .alert-warning {
            background-color: rgba(245, 158, 11, 0.1);
            color: var(--warning-color);
        }

        .alert-info {
            background-color: rgba(14, 165, 233, 0.1);
            color: var(--info-color);
        }

        /* Tooltips */
        .tooltip {
            border-radius: 6px;
            font-size: 12px;
            padding: 8px 12px;
        }

        /* Mobile menu */
        .mobile-menu-toggle {
            display: none;
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background-color: var(--light-color);
            color: var(--secondary-color);
            align-items: center;
            justify-content: center;
            font-size: 18px;
            cursor: pointer;
            transition: all var(--transition-speed) ease;
            border: none;
            margin-right: 10px;
        }

        .mobile-menu-toggle:hover {
            background-color: var(--primary-light);
            color: var(--primary-color);
        }

        .mobile-menu-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all var(--transition-speed) ease;
        }

        .mobile-menu-backdrop.show {
            opacity: 1;
            visibility: visible;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
                box-shadow: var(--shadow-xl);
            }

            .sidebar.show {
                transform: translateX(0);
                width: var(--sidebar-width);
            }

            .sidebar.show .sidebar-brand-text,
            .sidebar.show .sidebar-menu-text,
            .sidebar.show .user-details,
            .sidebar.show .sidebar-menu-header {
                opacity: 1;
                width: auto;
            }

            .main-content {
                margin-left: 0 !important;
            }

            .mobile-menu-toggle {
                display: flex;
            }

            .search-container {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .header {
                padding: 0 20px;
            }

            .user-dropdown-info {
                display: none;
            }

            .content {
                padding: 20px;
            }

            .card-stat {
                padding: 20px;
            }

            .card-header {
                padding: 16px 20px;
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .card-header-actions {
                width: 100%;
                justify-content: flex-end;
            }

            .card-body {
                padding: 20px;
            }

            .table-responsive {
                border-radius: 8px;
            }
        }

        @media (max-width: 576px) {
            .header-right {
                gap: 10px;
            }

            .card-stat {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .card-stat-icon {
                width: 50px;
                height: 50px;
                font-size: 20px;
            }

            .card-stat-background {
                width: 80px;
                height: 80px;
            }
        }

        /* Animation Keyframes */
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(0, 160, 235, 0.4);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(0, 160, 235, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(0, 160, 235, 0);
            }
        }

        .pulse {
            animation: pulse 2s infinite;
        }
    </style>

    @stack('styles')
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('head_division.dashboard') }}" class="sidebar-brand">
                    <img src="{{ Storage::url('images/migaslogo.png') }}" />
                    <span class="sidebar-brand-text">SIKERJA</span>
                </a>
            </div>

            <div class="sidebar-menu">
                <p class="sidebar-menu-header">Menu Utama</p>
                <ul class="sidebar-menu-items">
                    <li class="sidebar-menu-item" data-title="Dashboard">
                        <a href="{{ route('head_division.dashboard') }}"
                            class="sidebar-menu-link {{ request()->routeIs('head_division.dashboard') ? 'active' : '' }}">
                            <span class="sidebar-menu-icon">
                                <i class="fas fa-th-large"></i>
                            </span>
                            <span class="sidebar-menu-text">Dashboard</span>
                        </a>
                    </li>

                    <li class="sidebar-menu-item">
                        <a href="{{ route('head_division.team_members.index') }}"
                            class="sidebar-menu-link {{ request()->routeIs('head_division.team_members.*') ? 'active' : '' }}">
                            <span class="sidebar-menu-icon">
                                <i class="fas fa-users"></i>
                            </span>
                            <span class="sidebar-menu-text">Anggota Tim</span>
                        </a>
                    </li>
                </ul>

                <hr class="sidebar-divider">

                <p class="sidebar-menu-header">Manajemen Tugas</p>
                <ul class="sidebar-menu-items">
                    <li class="sidebar-menu-item" data-title="Tugas">
                        <a href="{{ route('head_division.job_desks.index') }}"
                            class="sidebar-menu-link {{ request()->routeIs('head_division.job_desks.*') ? 'active' : '' }}">
                            <span class="sidebar-menu-icon">
                                <i class="fas fa-clipboard-list"></i>
                            </span>
                            <span class="sidebar-menu-text">Tugas</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item" data-title="Tinjauan">
                        <a href="{{ route('head_division.reviews.index') }}"
                            class="sidebar-menu-link {{ request()->routeIs('head_division.reviews.*') ? 'active' : '' }}">
                            <span class="sidebar-menu-icon">
                                <i class="fas fa-clipboard-check"></i>
                            </span>
                            <span class="sidebar-menu-text">Tinjauan</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a class="sidebar-menu-link" href="{{ route('head_division.performances.index') }}">
                            <span class="sidebar-menu-icon">
                                <i class="fas fa-chart-line"></i>
                            </span>
                            <span class="sidebar-menu-text">Kinerja Karyawan</span>
                        </a>
                    </li>
                </ul>

                <hr class="sidebar-divider">

                <p class="sidebar-menu-header">Pengaturan</p>
                <ul class="sidebar-menu-items">
                    <li class="sidebar-menu-item" data-title="Profil">
                        <a href="#" class="sidebar-menu-link">
                            <span class="sidebar-menu-icon">
                                <i class="fas fa-user"></i>
                            </span>
                            <span class="sidebar-menu-text">Profil</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item" data-title="Keluar">
                        <a href="{{ route('logout') }}" class="sidebar-menu-link"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <span class="sidebar-menu-icon">
                                <i class="fas fa-sign-out-alt"></i>
                            </span>
                            <span class="sidebar-menu-text">Keluar</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="user-details">
                        <div class="user-name">{{ auth()->user()->name }}</div>
                        <div class="user-role">{{ auth()->user()->division->name ?? 'Tanpa Divisi' }}</div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="header">
                <div class="header-left">
                    <!-- Moved the sidebar toggle button here -->
                    <button class="sidebar-toggle" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="page-title">@yield('title', 'Dashboard')</h1>
                </div>
                <div class="header-right">
                    <div class="user-dropdown" id="userDropdown">
                        <div class="user-dropdown-toggle">
                            <div class="user-dropdown-avatar">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <div class="user-dropdown-info">
                                <div class="user-dropdown-name">{{ auth()->user()->name }}</div>
                                <div class="user-dropdown-role">{{ auth()->user()->division->name ?? 'Tanpa Divisi' }}
                                </div>
                            </div>
                            <i class="fas fa-chevron-down ms-2"></i>
                        </div>
                        <div class="user-dropdown-menu" id="userDropdownMenu">
                            <div class="user-dropdown-header">
                                <div class="user-dropdown-header-avatar">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                                <div class="user-dropdown-header-info">
                                    <div class="user-dropdown-header-name">{{ auth()->user()->name }}</div>
                                    <div class="user-dropdown-header-email">{{ auth()->user()->email }}</div>
                                </div>
                            </div>
                            <a href="#" class="user-dropdown-item">
                                <i class="fas fa-user user-dropdown-icon"></i>
                                Profil Saya
                            </a>
                            <a href="#" class="user-dropdown-item">
                                <i class="fas fa-cog user-dropdown-icon"></i>
                                Pengaturan Akun
                            </a>
                            <div class="user-dropdown-divider"></div>
                            <a href="{{ route('logout') }}" class="user-dropdown-item danger"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt user-dropdown-icon"></i>
                                Keluar
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <div class="mobile-menu-backdrop" id="mobileMenuBackdrop"></div>

            <div class="content">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                            aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                            aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar Toggle
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const mobileMenuBackdrop = document.getElementById('mobileMenuBackdrop');

            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');

                // For mobile devices
                if (window.innerWidth < 992) {
                    sidebar.classList.toggle('show');
                    mobileMenuBackdrop.classList.toggle('show');
                    document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
                }
            });

            mobileMenuBackdrop.addEventListener('click', function() {
                sidebar.classList.remove('show');
                mobileMenuBackdrop.classList.remove('show');
                document.body.style.overflow = '';
            });

            // User Dropdown Toggle
            const userDropdown = document.getElementById('userDropdown');
            const userDropdownMenu = document.getElementById('userDropdownMenu');

            userDropdown.addEventListener('click', function(event) {
                event.stopPropagation();
                userDropdownMenu.classList.toggle('show');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                if (userDropdownMenu.classList.contains('show') && !userDropdownMenu.contains(event
                        .target)) {
                    userDropdownMenu.classList.remove('show');
                }
            });

            // Responsive Sidebar
            const handleResize = () => {
                if (window.innerWidth < 992) {
                    sidebar.classList.add('collapsed');
                    sidebar.classList.remove('show');
                    mobileMenuBackdrop.classList.remove('show');
                    document.body.style.overflow = '';
                } else {
                    // Return to default state on larger screens
                    sidebar.classList.remove('collapsed');
                }
            };

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Initial call and event listener
            handleResize();
            window.addEventListener('resize', handleResize);
        });
    </script>

    @stack('scripts')
</body>

</html>
