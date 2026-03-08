<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ \App\Helpers\SettingsHelper::get('app_name', config('app.name', 'Antrian Project')) }}</title>

    @if(\App\Helpers\SettingsHelper::get('app_favicon'))
        <link rel="icon" href="{{ asset('storage/' . \App\Helpers\SettingsHelper::get('app_favicon')) }}" type="image/x-icon">
    @endif

    <!-- Google Font: Inter -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        :root {
            /* Desired Palette Distribution: White 35%, Blue 30%, Orange 20%, Green 15% */
            --theme-white: #ffffff;
            --theme-bg: #f8fafc;
            --theme-blue: #2563eb;
            --theme-blue-hover: #1d4ed8;
            --theme-orange: #f97316;
            --theme-orange-hover: #ea580c;
            --theme-green: #10b981;
            --theme-green-hover: #059669;
            --theme-dark: #1f2d3d;
            --theme-gray: #64748b;
        }

        html, body {
            max-width: 100%;
        }

        body {
            background: var(--theme-bg);
            font-family: 'Inter', sans-serif;
            color: var(--theme-dark);
        }

        /* Enforce AdminLTE/Bootstrap Overrides for custom theme */
        .text-primary { color: var(--theme-blue) !important; }
        .bg-primary { background-color: var(--theme-blue) !important; color: var(--theme-white) !important; }
        .btn-primary { background-color: var(--theme-blue); border-color: var(--theme-blue); color: var(--theme-white); }
        .btn-primary:hover, .btn-primary:active, .btn-primary:focus { background-color: var(--theme-blue-hover) !important; border-color: var(--theme-blue-hover) !important; color: var(--theme-white) !important; }

        .text-warning { color: var(--theme-orange) !important; }
        .bg-warning { background-color: var(--theme-orange) !important; color: var(--theme-white) !important; }
        .btn-warning { background-color: var(--theme-orange); border-color: var(--theme-orange); color: var(--theme-white); }
        .btn-warning:hover, .btn-warning:active, .btn-warning:focus { background-color: var(--theme-orange-hover) !important; border-color: var(--theme-orange-hover) !important; color: var(--theme-white) !important; }

        .text-success { color: var(--theme-green) !important; }
        .bg-success { background-color: var(--theme-green) !important; color: var(--theme-white) !important; }
        .btn-success { background-color: var(--theme-green); border-color: var(--theme-green); color: var(--theme-white); }
        .btn-success:hover, .btn-success:active, .btn-success:focus { background-color: var(--theme-green-hover) !important; border-color: var(--theme-green-hover) !important; color: var(--theme-white) !important; }

        .nav-pills .nav-link.active, .nav-pills .show > .nav-link {
            background-color: var(--theme-blue);
            color: var(--theme-white);
        }

        a { color: var(--theme-blue); transition: color 0.2s ease; }
        a:hover { color: var(--theme-blue-hover); }

        .wrapper {
            background: var(--theme-bg);
            overflow-x: hidden;
        }

        .main-header.navbar {
            border-bottom: 2px solid var(--theme-blue);
            background: var(--theme-white);
            min-height: 62px;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1030;
        }

        .content-wrapper {
            background: var(--theme-bg);
            margin-top: 0;
            padding-top: 106px !important;
        }

        /* Override AdminLTE fixed-navbar offset so content starts right below custom navbar + top menu */
        .layout-navbar-fixed .wrapper .content-wrapper {
            margin-top: 0 !important;
        }

        .layout-navbar-fixed .wrapper .main-header {
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        .content-header {
            padding-bottom: 0 !important;
            padding-top: 0 !important;
        }

        .content-header .row {
            margin-bottom: 0 !important;
        }

        .breadcrumb {
            margin-bottom: 0 !important;
            padding-bottom: 0 !important;
        }

        .content {
            padding-top: 0 !important;
        }

        .content > .container-fluid {
            padding-top: 0 !important;
        }

        .content > .container-fluid > :not(.alert):first-child {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }

        .page-title-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
        }

        .top-menu-wrap {
            background: var(--theme-white);
            border-bottom: 1px solid var(--theme-bg);
            position: fixed;
            width: 100%;
            top: 62px;
            z-index: 1020;
        }

        .top-menu-wrap .container-fluid {
            display: flex;
            align-items: flex-end;
            min-height: 44px;
        }

        .top-menu-scroll {
            position: relative;
        }

        .top-menu-scroll::before,
        .top-menu-scroll::after {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            width: 22px;
            pointer-events: none;
            opacity: 0;
            transition: opacity .2s ease;
            z-index: 2;
        }

        .top-menu-scroll::before {
            left: 0;
            background: linear-gradient(to right, #fff, rgba(255,255,255,0));
        }

        .top-menu-scroll::after {
            right: 0;
            background: linear-gradient(to left, #fff, rgba(255,255,255,0));
        }

        .top-menu-scroll.has-left::before {
            opacity: 1;
        }

        .top-menu-scroll.has-right::after {
            opacity: 1;
        }

        .top-menu-list {
            display: flex;
            align-items: center;
            gap: 1.4rem;
            overflow-x: auto;
            white-space: nowrap;
            padding: .15rem 0 0;
            margin: 0;
            list-style: none;
            scrollbar-width: thin;
            scroll-snap-type: x proximity;
        }

        .top-menu-list > li {
            display: inline-flex;
            align-items: center;
        }

        .top-menu-link {
            display: inline-flex;
            align-items: center;
            padding: .62rem .55rem;
            border-bottom: 2.5px solid transparent;
            color: #64748b;
            font-weight: 600;
            font-size: .88rem;
            text-decoration: none;
            scroll-snap-align: start;
            line-height: 1.1;
            border-radius: .5rem .5rem .2rem .2rem;
            transition: color .2s ease, background-color .2s ease, border-color .2s ease;
        }

        .top-menu-link:hover {
            color: #1f2d3d;
            text-decoration: none;
        }

        .top-menu-link.active {
            color: var(--theme-blue);
            background: rgba(37, 99, 235, 0.12);
            border-bottom-color: var(--theme-blue);
            box-shadow: inset 0 0 0 1px rgba(37, 99, 235, 0.18);
        }

        .brand-head {
            display: inline-flex;
            align-items: center;
            gap: .55rem;
            font-weight: 800;
            color: #1f2d3d;
            text-decoration: none;
        }

        .brand-head:hover {
            color: #1f2d3d;
            text-decoration: none;
        }

        .brand-head .brand-icon {
            width: 30px;
            height: 30px;
            border-radius: 7px;
            background: var(--theme-orange);
            color: var(--theme-white);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .9rem;
        }

        .brand-head.has-logo .brand-icon {
            width: auto;
            height: auto;
            border-radius: 0;
            background: transparent;
            color: inherit;
            display: inline-flex;
        }

        .brand-head.has-logo .brand-icon img {
            display: block;
        }

        .brand-head span:last-child {
            letter-spacing: .1px;
            font-size: 1.03rem;
        }

        .user-meta {
            display: inline-flex;
            flex-direction: column;
            align-items: flex-end;
            line-height: 1.05;
            margin-right: .5rem;
        }

        .user-meta-name {
            font-size: .84rem;
            font-weight: 700;
            color: #1f2d3d;
        }

        .user-meta-role {
            font-size: .72rem;
            color: #94a3b8;
            font-weight: 600;
        }

        .user-avatar-pill {
            width: 30px;
            height: 30px;
            border: 2px solid #f1b28a;
            object-fit: cover;
            border-radius: 10px;
        }

        .support-shell-card {
            border-radius: 1.25rem;
            border: 1px solid #e2e8f0;
            background: #fff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .content-wrapper .card {
            border-radius: 1.25rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            margin-bottom: 1.5rem;
        }

        .content-wrapper .small-box {
            border-radius: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 6px 20px rgba(31, 45, 61, .06);
        }

        .content-wrapper .small-box .icon {
            top: .75rem;
        }

        .support-stat-card {
            border-radius: 1.25rem;
            border: 1px solid #e2e8f0;
            padding: 1.25rem;
            background: #fff;
            height: 100%;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            transition: all 0.3s ease;
        }
        
        .support-stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
        }

        .support-stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: .15rem;
            color: #1f2d3d;
        }

        .support-stat-label {
            font-size: .875rem;
            color: #64748b;
            font-weight: 500;
        }

        .support-search {
            width: 420px;
        }

        .support-search .input-group-text,
        .support-search .form-control {
            background: #f1f5f9;
            border-color: #f1f5f9;
        }

        .support-search .form-control:focus {
            box-shadow: none;
            border-color: #dbe5f3;
            background: #fff;
        }

        .top-icon-btn {
            color: #64748b;
            padding: .45rem .55rem;
            font-size: 1rem;
        }

        .top-icon-btn:hover {
            color: #1f2d3d;
        }

        .top-divider {
            width: 1px;
            height: 24px;
            background: #e5eaf3;
            margin: 0 .5rem;
        }

        /* Notification dropdown layout */
        #notification-dropdown.notification-dropdown {
            width: min(420px, calc(100vw - 24px));
            max-height: min(70vh, 560px);
            padding: 0;
            overflow: hidden;
        }

        #notification-list {
            max-height: min(58vh, 420px);
            overflow-y: auto;
            overscroll-behavior: contain;
        }

        #notification-list .notification-item {
            white-space: normal;
            padding: 0.75rem 0.9rem;
            line-height: 1.25;
        }

        .notification-item-top {
            display: flex;
            align-items: flex-start;
            gap: 0.55rem;
        }

        .notification-item-title {
            display: flex;
            align-items: flex-start;
            gap: 0.45rem;
            min-width: 0;
            flex: 1;
            font-weight: 600;
            color: #1f2d3d;
        }

        .notification-item-title i {
            flex-shrink: 0;
            margin-top: 0.12rem;
        }

        .notification-item-title-text {
            min-width: 0;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        .notification-item-time {
            flex-shrink: 0;
            color: #94a3b8;
            font-size: 0.72rem;
            white-space: nowrap;
            margin-top: 0.05rem;
        }

        .notification-item-message {
            margin: 0.35rem 0 0 1.35rem;
            color: #64748b;
            font-size: 0.82rem;
            overflow-wrap: anywhere;
            word-break: break-word;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        @media (max-width: 576px) {
            #notification-dropdown.notification-dropdown {
                width: calc(100vw - 16px);
            }

            #notification-list .notification-item {
                padding: 0.7rem 0.75rem;
            }

            .notification-item-message {
                margin-left: 1.2rem;
            }
        }

        @media (max-width: 992px) {
            .support-search {
                width: 260px;
            }
        }

        @media (max-width: 768px) {
            .content-wrapper {
                padding-top: .35rem;
            }

            .content-wrapper .container-fluid {
                padding-left: .7rem;
                padding-right: .7rem;
            }

            .content-wrapper .card {
                border-radius: .85rem;
            }

            .content .card-header,
            .content .card-body,
            .content .card-footer {
                padding-left: .8rem;
                padding-right: .8rem;
            }

            .content .card-footer .btn,
            .content .card-header .btn {
                margin-bottom: .35rem;
            }

            .content .card-footer .btn:last-child,
            .content .card-header .btn:last-child {
                margin-bottom: 0;
            }

            .support-search {
                display: none;
            }

            .dropdown-menu {
                border: 0;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                border-radius: 1rem;
                padding: 0.5rem 0;
            }
            
            .dropdown-item {
                border-radius: 0.5rem;
                margin: 0 0.5rem;
                width: auto;
                padding: 0.5rem 1rem;
                transition: background-color 0.2s ease;
            }

            .top-menu-list {
                gap: .85rem;
                padding-left: .15rem;
                -webkit-overflow-scrolling: touch;
            }

            .top-divider,
            .user-meta {
                display: none !important;
            }

            .top-menu-scroll::before,
            .top-menu-scroll::after {
                width: 16px;
            }

            .table-responsive .table {
                min-width: 620px;
            }

            .content .badge {
                white-space: normal;
            }
        }

        .table td, .table th {
            vertical-align: middle;
            border-top: 1px solid #f1f5f9;
        }

        .table thead th {
            border-bottom: 2px solid #f1f5f9;
            color: #64748b;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .badge {
            font-weight: 600;
            padding: 0.4em 0.6em;
            border-radius: 0.375rem;
        }

        .badge-primary { background-color: rgba(37, 99, 235, 0.1) !important; color: var(--theme-blue) !important; border: 1px solid rgba(37, 99, 235, 0.2); }
        .badge-success { background-color: rgba(16, 185, 129, 0.1) !important; color: var(--theme-green) !important; border: 1px solid rgba(16, 185, 129, 0.2); }
        .badge-warning { background-color: rgba(249, 115, 22, 0.1) !important; color: var(--theme-orange) !important; border: 1px solid rgba(249, 115, 22, 0.2); }
        .badge-danger { background-color: #fee2e2 !important; color: #b91c1c !important; }
        .badge-info { background-color: rgba(37, 99, 235, 0.05) !important; color: var(--theme-blue) !important; }
        .badge-secondary { background-color: #f1f5f9 !important; color: var(--theme-gray) !important; }

        .progress-xs {
            height: 6px;
            border-radius: 3px;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table-responsive .table {
            min-width: 720px;
        }

        .table.table-borderless {
            min-width: 0 !important;
        }

        .detail-table th,
        .detail-table td {
            padding: .55rem .35rem;
            vertical-align: top;
        }

        .detail-table th {
            white-space: nowrap;
            color: #6b7280;
            font-weight: 600;
        }

        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_length {
            margin-bottom: .4rem;
        }

        .dataTables_wrapper .dataTables_paginate .pagination {
            margin-bottom: 0;
        }

        .dataTables_wrapper .dataTables_info {
            padding-top: .35rem;
        }

        @media (max-width: 768px) {
            .dataTables_wrapper .row {
                margin-left: 0;
                margin-right: 0;
            }

            .dataTables_wrapper .row > div {
                padding-left: 0;
                padding-right: 0;
            }

            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter,
            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate {
                text-align: left !important;
            }

            .dataTables_wrapper .dataTables_filter input {
                margin-left: 0 !important;
                margin-top: .35rem;
                width: 100%;
            }

            .dataTables_wrapper .dataTables_paginate .pagination {
                justify-content: flex-start;
                flex-wrap: wrap;
                gap: .25rem;
            }

            .content .card-header {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: .45rem;
            }

            .content .card-header .card-tools {
                margin-left: 0;
                width: 100%;
                display: flex;
                flex-wrap: wrap;
                gap: .35rem;
            }

            .content .card-header .card-tools .btn {
                width: 100%;
            }

            .detail-table,
            .detail-table tbody,
            .detail-table tr,
            .detail-table th,
            .detail-table td {
                display: block;
                width: 100%;
            }

            .detail-table tr {
                padding: .35rem 0;
                border-bottom: 1px solid #eef2f7;
            }

            .detail-table tr:last-child {
                border-bottom: 0;
            }

            .detail-table th {
                padding: .1rem 0;
                font-size: .78rem;
                color: #94a3b8;
            }

            .detail-table td {
                padding: .1rem 0 .3rem;
            }
        }

        .breadcrumb {
            margin-bottom: 0;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            color: #b6c1d2;
        }

        .quick-link {
            border-radius: .65rem;
        }
        /* Dark Mode Overrides */
        body.dark-mode {
            --theme-white: #1e1e1e;
            --theme-bg: #121212;
            --theme-dark: #f8f9fa;
            --theme-gray: #a1aab2;
        }

        body.dark-mode .support-shell-card,
        body.dark-mode .content-wrapper .card,
        body.dark-mode .support-stat-card {
            background: var(--theme-white);
            border-color: #2d2d2d;
            color: var(--theme-dark);
        }

        body.dark-mode .table,
        body.dark-mode .table-hover tbody tr:hover {
            color: var(--theme-dark);
        }

        body.dark-mode .table thead th,
        body.dark-mode .table thead.bg-light th,
        body.dark-mode .bg-light {
            background-color: #2d2d2d !important;
            color: var(--theme-dark) !important;
            border-color: #3d3d3d;
        }

        body.dark-mode .table td,
        body.dark-mode .table th {
            border-color: #2d2d2d;
        }

        body.dark-mode .badge-light {
            background-color: #2d2d2d;
            color: var(--theme-dark);
            border: 1px solid #3d3d3d;
        }

        body.dark-mode .main-header.navbar,
        body.dark-mode .top-menu-wrap {
            background: var(--theme-white);
        }

        body.dark-mode .top-icon-btn {
            color: var(--theme-gray) !important;
        }
        
        body.dark-mode .top-icon-btn:hover {
            color: var(--theme-dark) !important;
        }

        body.dark-mode .support-search .input-group-text,
        body.dark-mode .support-search .form-control {
            background: #1a1a1a;
            border-color: #1a1a1a;
            color: var(--theme-dark);
        }
        
        body.dark-mode .support-search .form-control:focus {
            background: #2d2d2d;
            border-color: #3d3d3d;
            color: var(--theme-dark);
        }

        body.dark-mode .top-menu-link {
            color: var(--theme-gray);
        }

        body.dark-mode .top-menu-link:hover,
        body.dark-mode .brand-head,
        body.dark-mode .user-meta-name,
        body.dark-mode .support-stat-value {
            color: var(--theme-dark);
        }

        body.dark-mode .top-menu-link.active {
            color: #93c5fd;
            background: rgba(37, 99, 235, 0.2);
            border-bottom-color: #60a5fa;
            box-shadow: inset 0 0 0 1px rgba(147, 197, 253, 0.28);
        }
        
        body.dark-mode .breadcrumb-item a {
            color: var(--theme-blue);
        }

        body.dark-mode .breadcrumb-item.active {
            color: var(--theme-gray);
        }

        body.dark-mode .page-title-wrap h1,
        body.dark-mode .text-dark {
            color: var(--theme-dark) !important;
        }
        
        body.dark-mode .text-muted {
            color: var(--theme-gray) !important;
        }

        body.dark-mode .border,
        body.dark-mode .border-bottom,
        body.dark-mode .border-top {
            border-color: #2d2d2d !important;
        }

        body.dark-mode .modal-content {
            background-color: var(--theme-white);
            color: var(--theme-dark);
            border-color: #2d2d2d;
        }

        body.dark-mode .modal-header,
        body.dark-mode .modal-body,
        body.dark-mode .modal-footer {
            border-color: #2d2d2d;
            color: var(--theme-dark);
        }

        /* Form Controls */
        body.dark-mode .form-control,
        body.dark-mode .custom-select {
            background-color: var(--theme-white);
            border-color: #2d2d2d;
            color: var(--theme-dark);
        }
        body.dark-mode .form-control:focus,
        body.dark-mode .custom-select:focus {
            background-color: #2d2d2d;
            color: var(--theme-dark);
        }

        /* Dropdowns */
        body.dark-mode .dropdown-menu {
            background-color: var(--theme-white);
            border-color: #2d2d2d;
        }
        body.dark-mode .dropdown-item {
            color: var(--theme-dark);
        }
        body.dark-mode .dropdown-item:hover,
        body.dark-mode .dropdown-item:focus {
            background-color: #2d2d2d;
            color: var(--theme-dark);
        }
        body.dark-mode .dropdown-item.dropdown-header {
            color: var(--theme-gray) !important;
        }

        body.dark-mode .notification-item-title {
            color: #e5e7eb;
        }

        body.dark-mode .notification-item-time {
            color: #9ca3af;
        }

        body.dark-mode .notification-item-message {
            color: #d1d5db;
        }

        /* Pagination */
        body.dark-mode .page-link {
            background-color: var(--theme-white);
            border-color: #2d2d2d;
            color: var(--theme-gray);
        }
        body.dark-mode .page-link:hover {
            background-color: #2d2d2d;
            color: var(--theme-dark);
        }
        body.dark-mode .page-item.active .page-link {
            background-color: var(--theme-blue);
            border-color: var(--theme-blue);
            color: #fff;
        }
        body.dark-mode .page-item.disabled .page-link {
            background-color: var(--theme-bg);
            border-color: #2d2d2d;
            color: #5c6c7c;
        }

        /* Nav Pills / Tabs */
        body.dark-mode .nav-pills .nav-link {
            color: var(--theme-gray);
        }
        body.dark-mode .nav-pills .nav-link:hover {
            color: var(--theme-dark);
        }
        
        /* SweetAlert */
        body.dark-mode .swal2-popup {
            background: var(--theme-white);
            color: var(--theme-dark);
        }
        body.dark-mode .swal2-title, body.dark-mode .swal2-html-container {
            color: var(--theme-dark);
        }

        /* List Groups */
        body.dark-mode .list-group-item {
            background-color: var(--theme-white);
            border-color: #2d2d2d;
            color: var(--theme-dark);
        }

        /* Specific Background Helpers */
        body.dark-mode .bg-white {
            background-color: var(--theme-white) !important;
            color: var(--theme-dark) !important;
        }
        
        body.dark-mode .nav-treeview > .nav-item > .nav-link.active {
            background-color: #2d2d2d;
            color: var(--theme-dark);
        }

        /* Support Stat Cards (Charts/Progress) */
        body.dark-mode .progress {
            background-color: #2d2d2d;
        }

        /* Dropdowns */
        body.dark-mode .dropdown-menu {
            background-color: var(--theme-white);
            border-color: #2d2d2d;
            color: var(--theme-dark);
        }

        body.dark-mode .dropdown-item {
            color: var(--theme-dark);
        }

        body.dark-mode .dropdown-item:hover,
        body.dark-mode .dropdown-item:focus {
            background-color: #2d2d2d;
            color: #fff;
        }

        body.dark-mode .dropdown-header,
        body.dark-mode .dropdown-footer {
            color: var(--theme-dark);
        }

        body.dark-mode .dropdown-divider {
            border-top-color: #2d2d2d;
        }

        /* Direct Chat */
        body.dark-mode .direct-chat-messages {
            background-color: var(--theme-white);
        }
        
        body.dark-mode .direct-chat-msg .direct-chat-text {
            background-color: #2d2d2d;
            border-color: #2d2d2d;
            color: var(--theme-dark);
        }

        body.dark-mode .direct-chat-msg .direct-chat-text::after,
        body.dark-mode .direct-chat-msg .direct-chat-text::before {
            border-right-color: #2d2d2d;
        }

        body.dark-mode .direct-chat-msg.right .direct-chat-text {
            background-color: var(--theme-blue);
            border-color: var(--theme-blue);
            color: #fff;
        }

        body.dark-mode .direct-chat-msg.right .direct-chat-text::after,
        body.dark-mode .direct-chat-msg.right .direct-chat-text::before {
            border-left-color: var(--theme-blue);
        }

        body.dark-mode .direct-chat-name {
            color: var(--theme-dark);
        }

        body.dark-mode .direct-chat-timestamp {
            color: var(--theme-gray);
        }
        
        body.dark-mode .direct-chat-primary .right > .direct-chat-text {
            background-color: var(--theme-blue);
            border-color: var(--theme-blue);
            color: #fff;
        }

        /* Direct Chat Inputs */
        body.dark-mode .direct-chat-primary .card-footer {
            background-color: var(--theme-white) !important;
            border-color: #2d2d2d;
        }

        body.dark-mode .direct-chat-primary .card-footer .form-control {
            background-color: #1a1a1a;
            border-color: #2d2d2d;
            color: var(--theme-dark);
        }

        body.dark-mode .direct-chat-primary .card-footer .form-control:focus {
            background-color: #2d2d2d;
            border-color: #3d3d3d;
        }

        body.dark-mode .direct-chat-primary .card-footer .btn-secondary,
        body.dark-mode .direct-chat-primary .card-footer .custom-file-label {
            background-color: #2d2d2d;
            border-color: #3d3d3d;
            color: var(--theme-dark);
        }
    </style>
    
    @stack('styles')
    
<body class="hold-transition layout-top-nav layout-navbar-fixed">
    <!-- Prevent FOUC for Dark Mode -->
    <script>
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-mode');
        }
    </script>
    <div class="wrapper">
        <!-- Navbar -->
        @include('layouts.partials.navbar')

        <!-- Top Menu -->
        @include('layouts.partials.top-menu')

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            @if(trim($__env->yieldContent('breadcrumb')) !== '')
                <div class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-12">
                                <ol class="breadcrumb float-sm-right">
                                    @yield('breadcrumb')
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <i class="icon fas fa-check"></i> {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <i class="icon fas fa-ban"></i> {{ session('error') }}
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="alert alert-warning alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <i class="icon fas fa-exclamation-triangle"></i> {{ session('warning') }}
                        </div>
                    @endif

                    @yield('content')
                </div>
            </section>
        </div>

        <!-- Footer -->
        @include('layouts.partials.footer')
        
        <!-- Chat Widget -->
        @include('layouts.partials.chat-widget')
    </div>

    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Initialize DataTables
        $(document).ready(function() {
            const isMobile = window.matchMedia('(max-width: 768px)').matches;

            // Auto-wrap regular tables so they stay usable on mobile
            document.querySelectorAll('.content-wrapper table').forEach(function(table) {
                if (table.classList.contains('table-borderless')) {
                    return;
                }

                if (table.closest('.table-responsive') || table.closest('.dataTables_wrapper')) {
                    return;
                }

                const wrapper = document.createElement('div');
                wrapper.className = 'table-responsive';
                table.parentNode.insertBefore(wrapper, table);
                wrapper.appendChild(table);
            });

            $('.data-table').each(function() {
                if ($.fn.DataTable.isDataTable(this)) {
                    return;
                }

                const $table = $(this);
                const totalColumns = $table.find('thead th').length;

                $table.find('tbody tr').each(function() {
                    const $row = $(this);
                    const $cells = $row.children('td, th');

                    if ($cells.length === 1 && $cells.first().attr('colspan')) {
                        $row.remove();
                        return;
                    }

                    if ($cells.length > 0 && $cells.length !== totalColumns) {
                        $row.remove();
                    }
                });

                $table.DataTable({
                    "responsive": false,
                    "scrollX": true,
                    "lengthChange": !isMobile,
                    "autoWidth": false,
                    "pagingType": isMobile ? 'simple' : 'simple_numbers',
                    "pageLength": isMobile ? 8 : 10,
                    "language": {
                        "emptyTable": "Tidak ada data yang tersedia",
                        "zeroRecords": "Data tidak ditemukan",
                        "search": "Cari:",
                        "lengthMenu": "Tampilkan _MENU_ data",
                        "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        "infoEmpty": "Menampilkan 0 data",
                        "paginate": {
                            "first": "Awal",
                            "last": "Akhir",
                            "next": "Berikutnya",
                            "previous": "Sebelumnya"
                        }
                    }
                });
            });
        });

        // Delete confirmation
        function confirmDelete(formId) {
            Swal.fire({
                title: 'Anda yakin?',
                text: 'Data yang dihapus tidak dapat dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }
    </script>

    <!-- Dark Mode Logic -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleBtn = document.getElementById('dark-mode-toggle');
            const icon = document.getElementById('dark-mode-icon');
            const body = document.body;

            // Check local storage (FOUC prevention handles initial class setting)
            if (localStorage.getItem('theme') === 'dark') {
                body.classList.add('dark-mode');
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            }

            // Toggle function
            if (toggleBtn) {
                toggleBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (body.classList.contains('dark-mode')) {
                        body.classList.remove('dark-mode');
                        icon.classList.remove('fa-sun');
                        icon.classList.add('fa-moon');
                        localStorage.setItem('theme', 'light');
                    } else {
                        body.classList.add('dark-mode');
                        icon.classList.remove('fa-moon');
                        icon.classList.add('fa-sun');
                        localStorage.setItem('theme', 'dark');
                    }
                });
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const scrollWrap = document.querySelector('.top-menu-scroll');
            const menuList = document.querySelector('.top-menu-list');

            if (!scrollWrap || !menuList) {
                return;
            }

            function updateTopMenuFade() {
                const maxScrollLeft = menuList.scrollWidth - menuList.clientWidth;
                const hasOverflow = maxScrollLeft > 1;

                if (!hasOverflow) {
                    scrollWrap.classList.remove('has-left', 'has-right');
                    return;
                }

                scrollWrap.classList.toggle('has-left', menuList.scrollLeft > 4);
                scrollWrap.classList.toggle('has-right', menuList.scrollLeft < (maxScrollLeft - 4));
            }

            menuList.addEventListener('scroll', updateTopMenuFade, { passive: true });
            window.addEventListener('resize', updateTopMenuFade);
            updateTopMenuFade();
        });
    </script>

    <!-- Notification System -->
    <script>
        $(document).ready(function() {
            let notificationInterval;

            function escapeHtml(value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;');
            }

            function safeHref(url) {
                if (typeof url !== 'string' || !url.trim()) {
                    return '#';
                }

                return url;
            }

            function updateNotifications() {
                $.ajax({
                    url: '{{ route("notifications.counts") }}',
                    method: 'GET',
                    success: function(data) {
                        console.log('Notification counts:', data);
                        
                        // Update badge
                        if (data.total > 0) {
                            $('#notification-count').text(data.total).show();
                            if (data.unread_messages > 0) {
                                $('#chat-notification-badge').text(data.unread_messages).show();
                            }
                        } else {
                            $('#notification-count').hide();
                            $('#chat-notification-badge').hide();
                        }

                        // Update header
                        $('#notification-header').text(data.total + ' Notifikasi');

                        // Update title badge
                        if (data.total > 0) {
                            document.title = '(' + data.total + ') {{ config("app.name") }}';
                        } else {
                            document.title = '{{ config("app.name") }}';
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Notification error:', error);
                    }
                });
            }

            function loadNotifications() {
                $.ajax({
                    url: '{{ route("notifications.list") }}',
                    method: 'GET',
                    success: function(notifications) {
                        console.log('Notifications:', notifications);
                        
                        const $list = $('#notification-list');
                        $list.empty();

                        if (notifications.length === 0) {
                            $list.html('<div class="text-center p-3 text-muted">Tidak ada notifikasi baru</div>');
                            return;
                        }

                        notifications.forEach(function(notif, index) {
                            const iconClass = escapeHtml(notif.icon || 'fas fa-bell');
                            const color = escapeHtml(notif.color || 'primary');
                            const title = escapeHtml(notif.title || 'Notifikasi');
                            const message = escapeHtml(notif.message || '');
                            const time = escapeHtml(notif.time || '');

                            const $item = $(`
                                <a href="${safeHref(notif.url)}" class="dropdown-item notif-link notification-item" data-type="${escapeHtml(notif.type || '')}" data-id="${parseInt(notif.id || 0, 10) || 0}">
                                    <div class="notification-item-top">
                                        <span class="notification-item-title">
                                            <i class="${iconClass} text-${color}"></i>
                                            <span class="notification-item-title-text">${title}</span>
                                        </span>
                                        <span class="notification-item-time">${time}</span>
                                    </div>
                                    <p class="notification-item-message mb-0">${message}</p>
                                </a>
                            `);

                            $list.append($item);

                            if (index < notifications.length - 1) {
                                $list.append('<div class="dropdown-divider"></div>');
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Load notifications error:', error);
                    }
                });
            }

            // Load notifications when dropdown is opened
            $(document).on('click', '#notification-bell', function(e) {
                e.preventDefault();
                loadNotifications();
            });

            // Handle notification click
            $(document).on('click', '.notif-link', function(e) {
                e.preventDefault();
                const $link = $(this);
                const type = $link.data('type');
                const id = $link.data('id');
                const targetUrl = $link.attr('href');

                if (!type || !id) {
                    window.location.href = targetUrl;
                    return;
                }

                $.ajax({
                    url: '{{ route("notifications.mark-read-get") }}',
                    method: 'GET',
                    data: {
                        type: type,
                        id: id || 0
                    },
                    complete: function() {
                        // Always continue to target page even if mark-read fails.
                        // This prevents users being blocked by session/CSRF edge cases.
                        if (type === 'message') {
                            let currentTotal = parseInt($('#notification-count').text()) || 0;
                            if (currentTotal > 0) {
                                let newTotal = currentTotal - 1;
                                $('#notification-count').text(newTotal);
                                if (newTotal === 0) {
                                    $('#notification-count').hide();
                                    document.title = '{{ config("app.name") }}';
                                } else {
                                    document.title = '(' + newTotal + ') {{ config("app.name") }}';
                                }
                            }
                        }

                        window.location.href = targetUrl;
                    }
                });
            });

            // Initial load and auto-update every 30 seconds
            updateNotifications();
            notificationInterval = setInterval(updateNotifications, 30000);

            // Clear interval on page unload
            $(window).on('beforeunload', function() {
                clearInterval(notificationInterval);
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
