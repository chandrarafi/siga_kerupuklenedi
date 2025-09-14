<?php
$uri = service('uri');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin Panel' ?> - Sistem Penggajian & Absensi</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- DataTables Bootstrap 5 -->
    <link href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Animate CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <?= $this->renderSection('styles') ?>

    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #1a3c6e;
            --primary-gradient-start: #2c5ca2;
            --primary-gradient-end: #1a3c6e;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --info-color: #17a2b8;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --light-color: #f8f9fc;
            --dark-color: #343a40;
            --border-radius: 0.5rem;
            --card-border-radius: 0.75rem;
            --box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f5f7fa;
            color: #444;
            transition: all 0.3s ease;
            overflow-x: hidden;
        }

        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #c5c5c5;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-color);
        }

        /* Glassmorphism effect */
        .glassmorphism {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        /* Sidebar styling */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary-gradient-end) 0%, var(--primary-gradient-start) 100%);
            box-shadow: var(--box-shadow);
            z-index: 1040;
            position: fixed;
            width: 280px;
            transition: all 0.3s ease-in-out;
            display: flex;
            flex-direction: column;
        }

        .sidebar-menu {
            height: calc(100vh - 6rem);
            overflow-y: auto;
            padding-bottom: 2rem;
        }

        .sidebar-menu::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-menu::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar-menu::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 10px;
        }

        .sidebar-menu::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        .sidebar-brand {
            height: 5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
        }

        .sidebar-brand h3 {
            color: white;
            font-weight: 700;
            font-size: 1.4rem;
            margin-bottom: 0;
            letter-spacing: 1px;
        }

        .sidebar-brand p {
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 0;
            letter-spacing: 1px;
        }

        .sidebar-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            margin: 0 1.5rem;
        }

        .nav-header {
            color: rgba(255, 255, 255, 0.5);
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 1rem;
            padding-left: 1.5rem;
            padding-bottom: 0.5rem;
        }

        .nav-item {
            position: relative;
            padding: 0 0.5rem;
            margin-bottom: 0.1rem;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
            padding: 0.7rem 1rem;
            border-radius: var(--border-radius);
            margin: 0.1rem 0;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background-color: white;
            transform: scaleY(0);
            transition: transform 0.3s, opacity 0.3s;
            transform-origin: top;
            opacity: 0;
            border-radius: 0 2px 2px 0;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
            transform: translateX(5px);
        }

        .nav-link:hover::before {
            transform: scaleY(1);
            opacity: 1;
        }

        .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.1);
        }

        .nav-link.active::before {
            transform: scaleY(1);
            opacity: 1;
        }

        .nav-link i {
            margin-right: 0.8rem;
            font-size: 1rem;
            width: 1.2rem;
            text-align: center;
            transition: all 0.3s;
        }

        .nav-link span {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .badge {
            margin-left: 0.5rem;
        }

        /* Main content */
        .main-content {
            margin-left: 280px;
            transition: all 0.3s ease-in-out;
            min-height: 100vh;
            background-color: #f5f7fa;
            padding: 2rem;
            position: relative;
            z-index: 1;
        }

        .main-content::before {
            content: '';
            position: fixed;
            top: 0;
            left: 280px;
            right: 0;
            height: 100vh;
            background: radial-gradient(circle at top right, rgba(44, 62, 80, 0.1) 0%, transparent 70%);
            z-index: -1;
        }

        /* Topbar */
        .topbar {
            height: 4.375rem;
            background-color: white;
            box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
            border-radius: var(--card-border-radius);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .topbar::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), var(--info-color), var(--success-color), var(--warning-color), var(--danger-color));
            background-size: 500% 500%;
            animation: gradient 10s ease infinite;
        }

        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .topbar h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 0;
        }

        .topbar-divider {
            width: 0;
            border-right: 1px solid #e3e6f0;
            height: 2rem;
            margin: auto 1rem;
        }

        .topbar-nav {
            display: flex;
            align-items: center;
            margin-left: auto;
            gap: 0.5rem;
        }

        .topbar-item {
            position: relative;
        }

        .topbar-nav .nav-link {
            color: var(--secondary-color);
            padding: 0.7rem;
            border-radius: 50%;
            height: 2.5rem;
            width: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: visible;
            background-color: #f8f9fc;
            transition: all 0.3s;
        }

        .topbar-nav .nav-link::before {
            display: none;
        }

        .topbar-nav .nav-link:hover {
            background-color: var(--primary-color);
            color: white;
            transform: translateY(-3px);
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            padding: 0.2rem 0.5rem;
            border-radius: 50%;
            font-size: 0.6rem;
            background-color: var(--danger-color);
            color: white;
            font-weight: bold;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
        }

        .user-profile {
            display: flex;
            align-items: center;
            margin-left: 1rem;
            cursor: pointer;
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            transition: all 0.3s;
        }

        .user-profile:hover {
            background-color: #f8f9fc;
        }

        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid white;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .user-info {
            margin-left: 0.8rem;
        }

        .user-info h6 {
            margin-bottom: 0;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .user-info small {
            color: var(--secondary-color);
            font-size: 0.75rem;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: var(--card-border-radius);
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.07);
            transition: all 0.3s ease-in-out;
            background-color: white;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 1rem 3rem rgba(58, 59, 69, 0.15);
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.25rem 1.5rem;
            border-top-left-radius: var(--card-border-radius) !important;
            border-top-right-radius: var(--card-border-radius) !important;
            font-weight: 600;
            color: var(--dark-color);
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Buttons */
        .btn {
            padding: 0.6rem 1.2rem;
            font-weight: 600;
            border-radius: var(--border-radius);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .btn::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.1);
            z-index: -1;
            transform: scaleY(0);
            transform-origin: bottom;
            transition: transform 0.3s;
        }

        .btn:hover::after {
            transform: scaleY(1);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-gradient-start) 0%, var(--primary-gradient-end) 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-gradient-start) 0%, var(--primary-gradient-end) 100%);
            box-shadow: 0 5px 15px rgba(26, 60, 110, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, #34c759 0%, #28a745 100%);
            border: none;
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #34c759 0%, #28a745 100%);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }

        .btn-info {
            background: linear-gradient(135deg, #20b2d2 0%, #17a2b8 100%);
            border: none;
        }

        .btn-info:hover {
            background: linear-gradient(135deg, #20b2d2 0%, #17a2b8 100%);
            box-shadow: 0 5px 15px rgba(23, 162, 184, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #e04b59 0%, #dc3545 100%);
            border: none;
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #e04b59 0%, #dc3545 100%);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        }

        /* Progress bars */
        .progress {
            border-radius: 1rem;
            height: 0.6rem;
            margin-bottom: 0.5rem;
            background-color: #eaecf4;
            overflow: hidden;
        }

        .progress-bar {
            border-radius: 1rem;
            position: relative;
            overflow: hidden;
        }

        .progress-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.3) 50%, rgba(255, 255, 255, 0) 100%);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% {
                transform: translateX(-100%);
            }

            100% {
                transform: translateX(100%);
            }
        }

        /* Tables */
        .table-responsive {
            overflow-x: auto;
            border-radius: 0.5rem;
        }

        table.dataTable {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }

        /* Footer */
        .footer {
            padding: 1rem 0;
            background-color: white;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            color: var(--secondary-color);
            font-size: 0.875rem;
        }

        .table th {
            font-weight: 600;
            background-color: #f8f9fc;
            border-bottom: 2px solid #e3e6f0;
            color: var(--dark-color);
            padding: 1rem;
            white-space: nowrap;
        }

        .table td {
            vertical-align: middle;
            padding: 1rem;
            border-bottom: 1px solid #f0f0f0;
        }

        .table tbody tr {
            transition: all 0.2s;
        }

        .table tbody tr:hover {
            background-color: #f8f9fc;
            transform: scale(1.01);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }

        /* Badge */
        .badge {
            padding: 0.5rem 0.8rem;
            font-weight: 600;
            font-size: 0.75rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Forms */
        .form-control,
        .form-select {
            padding: 0.7rem 1rem;
            font-size: 0.9rem;
            border: 1px solid #e5e7eb;
            border-radius: var(--border-radius);
            transition: all 0.3s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: rgba(52, 152, 219, 0.5);
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
        }

        .input-group {
            border-radius: var(--border-radius);
            overflow: hidden;
        }

        .input-group-text {
            background-color: #f8f9fc;
            border-color: #e5e7eb;
            color: var(--secondary-color);
        }

        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }

        /* Stat cards */
        .stat-card {
            border-left: 0.25rem solid var(--primary-color);
            border-radius: var(--card-border-radius);
            position: relative;
            overflow: hidden;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), transparent);
        }

        .stat-card.primary {
            border-left-color: var(--primary-color);
        }

        .stat-card.primary::after {
            background: linear-gradient(90deg, var(--primary-color), transparent);
        }

        .stat-card.success {
            border-left-color: var(--success-color);
        }

        .stat-card.success::after {
            background: linear-gradient(90deg, var(--success-color), transparent);
        }

        .stat-card.warning {
            border-left-color: var(--warning-color);
        }

        .stat-card.warning::after {
            background: linear-gradient(90deg, var(--warning-color), transparent);
        }

        .stat-card.danger {
            border-left-color: var(--danger-color);
        }

        .stat-card.danger::after {
            background: linear-gradient(90deg, var(--danger-color), transparent);
        }

        .stat-card .icon {
            font-size: 2rem;
            color: rgba(44, 62, 80, 0.1);
            transition: all 0.3s;
        }

        .stat-card:hover .icon {
            transform: scale(1.2);
            color: rgba(44, 62, 80, 0.2);
        }

        /* Modals */
        .modal-content {
            border: none;
            border-radius: var(--card-border-radius);
            overflow: hidden;
            box-shadow: 0 0.5rem 2rem rgba(0, 0, 0, 0.2);
        }

        .modal-backdrop {
            background-color: rgba(44, 62, 80, 0.5) !important;
            backdrop-filter: blur(4px) !important;
            -webkit-backdrop-filter: blur(4px) !important;
        }

        .modal-backdrop.show {
            opacity: 0.7 !important;
        }

        body.modal-open {
            overflow: hidden;
            padding-right: 0 !important;
        }

        .modal-open .modal {
            overflow-x: hidden;
            overflow-y: auto;
        }

        .modal {
            z-index: 1051 !important;
        }

        .modal-dialog {
            margin: 1.75rem auto;
        }

        .modal-header {
            padding: 1rem 1.5rem;
        }

        .modal-footer {
            padding: 1rem 1.5rem;
        }

        /* Custom Select Style */
        .select-wrapper {
            position: relative;
        }

        .select-wrapper:after {
            content: '⌄';
            font-size: 1.5rem;
            top: 0.7rem;
            right: 1rem;
            position: absolute;
            color: var(--secondary-color);
            pointer-events: none;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                width: 75px;
                overflow: hidden;
            }

            .sidebar-brand {
                padding: 1.5rem 0.5rem;
            }

            .sidebar .sidebar-brand h3,
            .sidebar .sidebar-brand p,
            .sidebar .nav-header,
            .sidebar .nav-link span {
                display: none;
            }

            .nav-link {
                padding: 1rem 0;
                display: flex;
                justify-content: center;
            }

            .nav-link i {
                margin: 0;
                font-size: 1.2rem;
            }

            .nav-link:hover {
                transform: none;
            }

            .main-content {
                margin-left: 75px;
            }

            .main-content::before {
                left: 75px;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 1.5rem;
            }

            .topbar {
                margin-bottom: 1.5rem;
            }

            .user-profile {
                display: none;
            }

            .sidebar {
                width: 0;
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
            }

            .main-content::before {
                left: 0;
            }

            .sidebar.show {
                width: 240px;
                transform: translateX(0);
            }

            .sidebar.show+.main-content {
                margin-left: 0;
            }

            .sidebar.show .sidebar-brand h3,
            .sidebar.show .sidebar-brand p,
            .sidebar.show .nav-header,
            .sidebar.show .nav-link span {
                display: block;
            }

            .sidebar.show .nav-link {
                padding: 1rem;
                justify-content: flex-start;
            }

            .sidebar.show .nav-link i {
                margin-right: 0.8rem;
            }

            .sidebar-toggle {
                display: none !important;
            }
        }

        /* Sidebar toggle */
        .sidebar-toggle {
            display: none;
            background: linear-gradient(135deg, var(--primary-gradient-start) 0%, var(--primary-gradient-end) 100%);
            color: white;
            border-radius: 50%;
            height: 3rem;
            width: 3rem;
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            z-index: 1050;
            text-align: center;
            line-height: 3rem;
            font-size: 1.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            cursor: pointer;
            transition: all 0.3s;
        }

        .sidebar-toggle:hover {
            transform: scale(1.1);
        }

        /* Improved navbar hamburger for sidebar */
        .navbar-toggler {
            background-color: transparent;
            border: none;
            padding: 0;
            margin-right: 1rem;
            display: none;
        }

        .navbar-toggler-icon {
            color: var(--dark-color);
            font-size: 1.5rem;
        }

        @media (max-width: 768px) {
            .navbar-toggler {
                display: block;
            }
        }

        /* List groups */
        .list-group-item {
            padding: 1.2rem 1.5rem;
            border-color: rgba(0, 0, 0, 0.05);
            transition: all 0.2s;
        }

        .list-group-item:hover {
            background-color: #f8f9fc;
            transform: translateX(5px);
        }

        /* Utilities */
        .bg-gradient-primary {
            background: linear-gradient(135deg, var(--primary-gradient-start) 0%, var(--primary-gradient-end) 100%);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
        }

        .bg-gradient-info {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
        }

        .bg-gradient-danger {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        }

        .text-primary {
            color: var(--primary-color) !important;
        }

        .text-secondary {
            color: var(--secondary-color) !important;
        }

        .text-success {
            color: var(--success-color) !important;
        }

        .text-info {
            color: var(--info-color) !important;
        }

        .text-warning {
            color: var(--warning-color) !important;
        }

        .text-danger {
            color: var(--danger-color) !important;
        }

        .bg-primary {
            background-color: var(--primary-color) !important;
        }

        .bg-secondary {
            background-color: var(--secondary-color) !important;
        }

        .bg-success {
            background-color: var(--success-color) !important;
        }

        .bg-info {
            background-color: var(--info-color) !important;
        }

        .bg-warning {
            background-color: var(--warning-color) !important;
        }

        .bg-danger {
            background-color: var(--danger-color) !important;
        }

        /* Animations */
        .animate__animated {
            animation-duration: 0.5s;
        }

        .page-content {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Tooltip styling */
        .tooltip {
            font-family: 'Nunito', sans-serif;
        }

        .tooltip-inner {
            background-color: var(--dark-color);
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .bs-tooltip-auto[data-popper-placement^=top] .tooltip-arrow::before,
        .bs-tooltip-top .tooltip-arrow::before {
            border-top-color: var(--dark-color);
        }

        /* Mobile responsive text adjustments */
        @media (max-width: 767.98px) {
            body {
                font-size: 0.9rem;
            }

            h1 {
                font-size: 1.5rem;
            }

            h4 {
                font-size: 1.2rem;
            }

            h5,
            h6 {
                font-size: 1rem;
            }

            .sidebar {
                transform: translateX(-100%);
                width: 250px;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 1rem;
            }

            .main-content::before {
                left: 0;
            }

            .sidebar-menu {
                height: calc(100vh - 5rem);
            }

            .card-header {
                padding: 1rem;
            }

            .card-body {
                padding: 1rem;
            }

            .btn {
                padding: 0.5rem 1rem;
                font-size: 0.85rem;
            }

            .btn-sm {
                padding: 0.25rem 0.75rem;
                font-size: 0.75rem;
            }

            .h3 {
                font-size: 1.5rem;
            }

            .small,
            small {
                font-size: 80%;
            }

            .topbar h1 {
                font-size: 1.25rem;
            }

            .badge {
                padding: 0.35rem 0.65rem;
                font-size: 0.7rem;
            }

            .form-label {
                font-size: 0.85rem;
            }

            .form-control,
            .form-select {
                padding: 0.5rem 0.75rem;
                font-size: 0.85rem;
            }

            .modal-dialog {
                margin: 1rem;
                max-width: calc(100% - 2rem);
            }

            .dataTables_length,
            .dataTables_filter {
                text-align: left !important;
                display: block;
                width: 100%;
            }

            .dataTables_length select {
                min-width: 60px;
                padding: 0.35rem;
                border-radius: 0.25rem;
                margin: 0 5px;
                display: inline-block;
            }

            .dataTables_filter input {
                margin-left: 0;
                width: 100%;
                margin-top: 0.25rem;
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="text-center">
                <h3>SIGA</h3>
                <p class="small">Sistem Penggajian & Absensi</p>
            </div>
        </div>
        <hr class="sidebar-divider">
        <div class="sidebar-menu">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?= $title == 'Dashboard' ? 'active' : '' ?>" href="<?= site_url('admin') ?>">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <?php if (session()->get('role') == 'pimpinan'): ?>
                    <li class="nav-header mt-3">LAPORAN</li>
                    <li class="nav-item">
                        <a class="nav-link <?= $title == 'Laporan Data Pegawai' ? 'active' : '' ?>" href="<?= site_url('admin/pegawai/report') ?>">
                            <i class="bi bi-file-earmark-bar-graph"></i>
                            <span>Laporan Pegawai</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $title == 'Laporan Data Jabatan' ? 'active' : '' ?>" href="<?= site_url('admin/jabatan/report') ?>">
                            <i class="bi bi-file-earmark-bar-graph"></i>
                            <span>Laporan Jabatan</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $title == 'Laporan Pengajuan Izin' ? 'active' : '' ?>" href="<?= site_url('admin/izin/report') ?>">
                            <i class="bi bi-file-earmark-bar-graph"></i>
                            <span>Laporan Izin</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $title == 'Laporan Data Absensi' ? 'active' : '' ?>" href="<?= site_url('admin/absensi/report') ?>">
                            <i class="bi bi-file-earmark-bar-graph"></i>
                            <span>Laporan Absensi</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $title == 'Laporan Data Lembur' ? 'active' : '' ?>" href="<?= site_url('admin/lembur/report') ?>">
                            <i class="bi bi-file-earmark-bar-graph"></i>
                            <span>Laporan Lembur</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $title == 'Laporan Gaji' ? 'active' : '' ?>" href="<?= site_url('admin/gaji/report') ?>">
                            <i class="bi bi-file-earmark-bar-graph"></i>
                            <span>Laporan Gaji</span>
                        </a>
                    </li>

                <?php elseif (session()->get('role') == 'admin'): ?>
                    <li class="nav-header mt-3">KEPEGAWAIAN</li>
                    <li class="nav-item">
                        <a class="nav-link <?= $title == 'Data Bagian' ? 'active' : '' ?>" href="<?= site_url('admin/bagian') ?>">
                            <i class="bi bi-diagram-3"></i>
                            <span>Data Bagian</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $title == 'Jabatan' ? 'active' : '' ?>" href="<?= site_url('admin/jabatan') ?>">
                            <i class="bi bi-briefcase"></i>
                            <span>Data Jabatan</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= site_url('admin/pegawai') ?>" class="nav-link <?= $uri->getSegment(2) == 'pegawai' ? 'active' : '' ?>">
                            <i class="bi bi-people"></i>
                            <span>Pegawai</span>
                        </a>
                    </li>

                    <li class="nav-header mt-3">ABSENSI</li>
                    <li class="nav-item">
                        <a href="<?= site_url('admin/absensi') ?>" class="nav-link <?= $uri->getSegment(2) == 'absensi' ? 'active' : '' ?>">
                            <i class="bi bi-clipboard-check"></i>
                            <span>Absensi</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= site_url('admin/izin') ?>" class="nav-link <?= $uri->getSegment(2) == 'izin' ? 'active' : '' ?>">
                            <i class="bi bi-calendar-check"></i>
                            <span>Pengajuan Izin</span>
                            <?php
                            $izinModel = new \App\Models\IzinModel();
                            $pendingCount = $izinModel->countPendingIzin();
                            if ($pendingCount > 0) :
                            ?>
                                <span class="badge bg-danger rounded-pill"><?= $pendingCount ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $title == 'Lembur' ? 'active' : '' ?>" href="<?= site_url('admin/lembur') ?>">
                            <i class="bi bi-clock-history"></i>
                            <span>Data Lembur</span>
                        </a>
                    </li>

                    <li class="nav-header mt-3">PENGGAJIAN</li>
                    <li class="nav-item">
                        <a class="nav-link <?= $uri->getSegment(2) == 'gaji' && !$uri->getSegment(3) ? 'active' : '' ?>" href="<?= site_url('admin/gaji') ?>">
                            <i class="bi bi-cash-stack"></i>
                            <span>Data Gaji</span>
                        </a>
                    </li>

                    <li class="nav-header mt-3">LAPORAN</li>
                    <li class="nav-item">
                        <a class="nav-link <?= $title == 'Laporan Data Pegawai' ? 'active' : '' ?>" href="<?= site_url('admin/pegawai/report') ?>">
                            <i class="bi bi-file-earmark-bar-graph"></i>
                            <span>Laporan Pegawai</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $title == 'Laporan Data Jabatan' ? 'active' : '' ?>" href="<?= site_url('admin/jabatan/report') ?>">
                            <i class="bi bi-file-earmark-bar-graph"></i>
                            <span>Laporan Jabatan</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $title == 'Laporan Pengajuan Izin' ? 'active' : '' ?>" href="<?= site_url('admin/izin/report') ?>">
                            <i class="bi bi-file-earmark-bar-graph"></i>
                            <span>Laporan Izin</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $title == 'Laporan Data Absensi' ? 'active' : '' ?>" href="<?= site_url('admin/absensi/report') ?>">
                            <i class="bi bi-file-earmark-bar-graph"></i>
                            <span>Laporan Absensi</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $title == 'Laporan Data Lembur' ? 'active' : '' ?>" href="<?= site_url('admin/lembur/report') ?>">
                            <i class="bi bi-file-earmark-bar-graph"></i>
                            <span>Laporan Lembur</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $title == 'Laporan Gaji' ? 'active' : '' ?>" href="<?= site_url('admin/gaji/report') ?>">
                            <i class="bi bi-file-earmark-bar-graph"></i>
                            <span>Laporan Gaji</span>
                        </a>
                    </li>

                    <li class="nav-header mt-3">PENGATURAN</li>
                    <li class="nav-item">
                        <a class="nav-link <?= $title == 'User Management' ? 'active' : '' ?>" href="<?= site_url('admin/users') ?>">
                            <i class="bi bi-person-gear"></i>
                            <span>Manajemen User</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $title == 'Lokasi Kantor' ? 'active' : '' ?>" href="<?= site_url('admin/settings/office-location') ?>">
                            <i class="bi bi-geo-alt"></i>
                            <span>Lokasi Kantor</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $title == 'Jam Absensi' ? 'active' : '' ?>" href="<?= site_url('admin/settings/absensi-settings') ?>">
                            <i class="bi bi-clock"></i>
                            <span>Jam Absensi</span>
                        </a>
                    </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link" id="btn-logout">
                        <i class="bi bi-box-arrow-left"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Mobile Toggle Button -->
    <div class="sidebar-toggle d-lg-none" id="sidebarToggle">
        <i class="bi bi-list"></i>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <div class="topbar glassmorphism">
            <button class="navbar-toggler d-md-none" id="navbarToggler" type="button">
                <i class="bi bi-list navbar-toggler-icon"></i>
            </button>
            <h1><?= $title ?? 'Dashboard' ?></h1>
            <div class="topbar-divider"></div>
            <div class="text-secondary small">Selamat Datang, Administrator</div>
            <div class="topbar-nav">
                <div class="topbar-item">
                    <a href="#" class="nav-link" data-bs-toggle="tooltip" title="Notifications">
                        <i class="bi bi-bell"></i>
                        <span class="notification-badge">5</span>
                    </a>
                </div>
                <div class="topbar-item">
                    <a href="#" class="nav-link" data-bs-toggle="tooltip" title="Messages">
                        <i class="bi bi-envelope"></i>
                        <span class="notification-badge">2</span>
                    </a>
                </div>
                <div class="topbar-item">
                    <a href="#" class="nav-link" data-bs-toggle="tooltip" title="Settings">
                        <i class="bi bi-gear"></i>
                    </a>
                </div>
                <div class="user-profile">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=1a3c6e&color=fff" alt="Admin">
                    <div class="user-info">
                        <h6>Admin</h6>
                        <small>Administrator</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="container-fluid page-content animate__animated animate__fadeIn">
            <?= $this->renderSection('content') ?>
        </div>

        <!-- Footer -->
        <footer class="footer mt-4">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        <p class="text-muted mb-0">&copy; <?= date('Y') ?> SIGA - Sistem Penggajian & Absensi</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p class="text-muted mb-0">Versi 1.0.0</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>
    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- ApexCharts for beautiful charts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        // Fungsi untuk membersihkan modal dan backdrop yang mungkin tertinggal
        function cleanupModals() {
            // Hapus semua backdrop yang mungkin tertinggal
            document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
                backdrop.remove();
            });

            // Hapus kelas modal-open dari body
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';

            // Pastikan semua modal tersembunyi
            document.querySelectorAll('.modal').forEach(modal => {
                modal.classList.remove('show');
                modal.style.display = 'none';
                modal.setAttribute('aria-hidden', 'true');
            });
        }

        // Jalankan pembersihan saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            cleanupModals();
        });

        // Global Modal Helper
        window.ModalHelper = {
            modals: {},

            // Initialize a modal
            initModal: function(modalId) {
                if (document.getElementById(modalId)) {
                    try {
                        // Hapus modal lama jika sudah ada
                        if (this.modals[modalId]) {
                            delete this.modals[modalId];
                        }

                        // Inisialisasi modal baru dengan Bootstrap native
                        this.modals[modalId] = new bootstrap.Modal(document.getElementById(modalId), {
                            backdrop: 'static',
                            keyboard: false,
                            focus: true
                        });

                        return this.modals[modalId];
                    } catch (error) {
                        console.error('Error initializing modal:', error);
                        return null;
                    }
                }
                return null;
            },

            // Show a modal
            showModal: function(modalId) {
                try {
                    if (!this.modals[modalId]) {
                        this.initModal(modalId);
                    }

                    if (this.modals[modalId]) {
                        this.modals[modalId].show();
                    }
                } catch (error) {
                    console.error('Error showing modal:', error);
                }
            },

            // Hide a modal
            hideModal: function(modalId) {
                try {
                    if (this.modals[modalId]) {
                        this.modals[modalId].hide();

                        // Tambahkan pembersihan setelah modal ditutup
                        setTimeout(cleanupModals, 300);
                    }
                } catch (error) {
                    console.error('Error hiding modal:', error);
                    // Fallback jika gagal menutup modal
                    cleanupModals();
                }
            },

            // Close all modals
            closeAllModals: function() {
                try {
                    document.querySelectorAll('.modal').forEach(modalEl => {
                        const modalInstance = bootstrap.Modal.getInstance(modalEl);
                        if (modalInstance) {
                            modalInstance.hide();
                        }
                    });

                    // Tambahkan pembersihan setelah semua modal ditutup
                    setTimeout(cleanupModals, 300);
                } catch (error) {
                    console.error('Error closing all modals:', error);
                    // Fallback jika gagal menutup modal
                    cleanupModals();
                }
            }
        };

        // Initialize all modals when DOM is ready
        $(document).ready(function() {
            // Close any open modals that might be stuck from previous page loads
            ModalHelper.closeAllModals();

            // Setup modal close buttons
            $(document).on('click', '[data-bs-dismiss="modal"]', function() {
                const modalId = $(this).closest('.modal').attr('id');
                if (modalId) {
                    ModalHelper.hideModal(modalId);
                }
            });
        });

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                boundary: document.body
            });
        });

        // Mobile sidebar toggle
        $(document).ready(function() {
            $('#sidebarToggle, #navbarToggler').on('click', function() {
                $('#sidebar').toggleClass('show');

                // Change icon based on sidebar state
                if ($('#sidebar').hasClass('show')) {
                    $(this).find('i').removeClass('bi-list').addClass('bi-x');
                } else {
                    $(this).find('i').removeClass('bi-x').addClass('bi-list');
                }
            });

            // Close sidebar when clicking outside on mobile
            $(document).on('click', function(e) {
                if ($(window).width() < 768) {
                    if (!$(e.target).closest('#sidebar').length &&
                        !$(e.target).closest('#sidebarToggle').length &&
                        !$(e.target).closest('#navbarToggler').length &&
                        $('#sidebar').hasClass('show')) {
                        $('#sidebar').removeClass('show');
                        $('#sidebarToggle, #navbarToggler').find('i').removeClass('bi-x').addClass('bi-list');
                    }
                }
            });

            // Handle window resize
            $(window).resize(function() {
                if ($(window).width() >= 768) {
                    $('#sidebar').removeClass('show');
                    $('#sidebarToggle, #navbarToggler').find('i').removeClass('bi-x').addClass('bi-list');
                }
            });
        });
    </script>

    <?= $this->renderSection('scripts') ?>

    <script>
        $(document).ready(function() {
            $('#btn-logout').click(function() {
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Anda akan keluar dari sistem!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Keluar!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '<?= site_url('auth/logout') ?>',
                            type: 'GET',
                            success: function(response) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Anda telah berhasil keluar',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href = '<?= site_url('auth') ?>';
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>

</body>

</html>