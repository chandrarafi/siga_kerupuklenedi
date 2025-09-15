<?php
$uri = service('uri');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard Pegawai' ?> - SIGA</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    animation: {
                        'bounce-slow': 'bounce 3s infinite',
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'float': 'float 3s ease-in-out infinite',
                        'slide-in': 'slideIn 0.5s ease-out forwards',
                        'fade-in': 'fadeIn 0.5s ease-out forwards',
                        'slide-down': 'slideDown 0.3s ease-out forwards',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': {
                                transform: 'translateY(0)'
                            },
                            '50%': {
                                transform: 'translateY(-10px)'
                            },
                        },
                        slideIn: {
                            '0%': {
                                transform: 'translateX(-100%)'
                            },
                            '100%': {
                                transform: 'translateX(0)'
                            },
                        },
                        fadeIn: {
                            '0%': {
                                opacity: '0'
                            },
                            '100%': {
                                opacity: '1'
                            },
                        },
                        slideDown: {
                            '0%': {
                                transform: 'translateY(-10px)',
                                opacity: '0'
                            },
                            '100%': {
                                transform: 'translateY(0)',
                                opacity: '1'
                            },
                        },
                    }
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer utilities {
            .active-nav-link {
                @apply relative bg-gradient-to-r from-white/20 to-transparent text-white font-medium pl-10;
            }
            .active-nav-link::before {
                content: '';
                @apply absolute left-0 top-0 bottom-0 w-1 bg-white rounded-r-full;
            }
            .active-nav-link i {
                @apply absolute left-4 text-white;
            }
            .nav-link {
                @apply relative flex items-center gap-3 px-4 py-3.5 rounded-lg hover:bg-white/10 text-white/80 transition-all duration-300 pl-10;
            }
            .nav-link i {
                @apply absolute left-4 transition-transform duration-300;
            }
            .nav-link:hover i {
                @apply transform scale-110 text-white;
            }
            .nav-link:hover {
                @apply text-white;
            }
            .animation-delay-150 {
                animation-delay: 150ms;
            }
            .animation-delay-300 {
                animation-delay: 300ms;
            }
            .animation-delay-500 {
                animation-delay: 500ms;
            }
            .animation-delay-700 {
                animation-delay: 700ms;
            }
            .menu-item {
                @apply opacity-0;
            }
            .menu-item-active {
                animation: fadeSlideIn 0.4s ease forwards;
            }
            .sidebar-logo {
                animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
            }
            @keyframes fadeSlideIn {
                from {
                    opacity: 0;
                    transform: translateX(-20px);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }
            .hover-scale {
                transition: transform 0.3s ease;
            }
            .hover-scale:hover {
                transform: scale(1.05);
            }
        }
        
        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 5px;
        }
        
        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }
    </style>
    <?= $this->renderSection('style') ?>
</head>

<body class="bg-gray-50 font-sans">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar untuk desktop -->
        <div id="sidebar" class="hidden md:flex md:flex-col w-64 bg-gradient-to-br from-primary-800 via-primary-700 to-primary-900 text-white transition-all duration-300 shadow-xl relative z-50 overflow-y-auto">
            <div class="flex flex-col min-h-full">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center py-6 animate-fade-in">
                        <div class="flex items-center gap-2 hover-scale">
                            <div class="w-10 h-10 rounded-lg bg-white bg-opacity-20 backdrop-blur-sm flex items-center justify-center shadow-lg sidebar-logo">
                                <i class="fas fa-fingerprint text-xl text-white"></i>
                            </div>
                            <h1 class="text-2xl font-bold">SIGA</h1>
                        </div>
                    </div>
                    <div class="text-center mb-8 animate-fade-in animation-delay-150">
                        <p class="text-sm text-primary-200">Sistem Informasi Gaji & Absensi</p>
                    </div>
                    <!-- User Info -->
                    <div class="flex flex-col items-center mb-10 px-4 py-6 bg-white bg-opacity-10 rounded-2xl backdrop-blur-sm animate-fade-in animation-delay-300 hover-scale">
                        <div class="w-20 h-20 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center mb-4 shadow-lg ring-4 ring-white ring-opacity-20 animate-pulse-slow">
                            <span class="text-2xl font-bold"><?= substr($pegawai['namapegawai'] ?? 'User', 0, 1) ?></span>
                        </div>
                        <h3 class="font-semibold"><?= $pegawai['namapegawai'] ?? 'User' ?></h3>
                        <p class="text-xs text-primary-300 mt-1"><?= $pegawai['idpegawai'] ?? '' ?></p>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="flex-grow overflow-y-auto">
                    <div class="text-xs uppercase text-gray-400 font-semibold px-4 mb-2 animate-fade-in animation-delay-500">Menu Utama</div>
                    <nav class="flex-1 space-y-1 px-2 pb-4">
                        <a href="<?= site_url('pegawai/dashboard') ?>" class="menu-item <?= $uri->getSegment(2) == 'dashboard' && !$uri->getSegment(3) ? 'active-nav-link' : 'nav-link' ?>">
                            <i class="fas fa-home"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="<?= site_url('pegawai/dashboard/riwayat') ?>" class="menu-item <?= $uri->getSegment(3) == 'riwayat' ? 'active-nav-link' : 'nav-link' ?>">
                            <i class="fas fa-history"></i>
                            <span>Riwayat Absensi</span>
                        </a>
                        <a href="<?= site_url('pegawai/izin') ?>" class="menu-item <?= $uri->getSegment(2) == 'izin' ? 'active-nav-link' : 'nav-link' ?>">
                            <i class="fas fa-calendar-check"></i>
                            <span>Pengajuan Izin</span>
                        </a>
                        <a href="<?= site_url('pegawai/dashboard/lembur') ?>" class="menu-item <?= $uri->getSegment(2) == 'dashboard' && $uri->getSegment(3) == 'lembur' ? 'active-nav-link' : 'nav-link' ?>">
                            <i class="fas fa-clock"></i>
                            <span>Riwayat Lembur</span>
                        </a>
                        <a href="<?= site_url('pegawai/dashboard/gaji') ?>" class="menu-item <?= $uri->getSegment(2) == 'dashboard' && $uri->getSegment(3) == 'gaji' ? 'active-nav-link' : 'nav-link' ?>">
                            <i class="fas fa-file-invoice-dollar"></i>
                            <span>Slip Gaji</span>
                        </a>
                        <div class="text-xs uppercase text-gray-400 font-semibold px-2 mt-6 mb-2 animate-fade-in animation-delay-700">Pengaturan</div>
                        <a href="#" class="menu-item nav-link">
                            <i class="fas fa-user-cog"></i>
                            <span>Profil</span>
                        </a>
                    </nav>
                </div>

                <!-- Logout -->
                <div class="flex-shrink-0 pt-4 animate-fade-in animation-delay-700">
                    <a href="<?= site_url('logout') ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-red-500 bg-opacity-20 hover:bg-opacity-40 text-white transition-all hover:scale-105 transform duration-300">
                        <i class="fas fa-sign-out-alt w-5 text-center"></i>
                        <span>Logout</span>
                    </a>
                    <div class="text-xs text-center mt-4 text-gray-400">
                        <p>SIGA v1.0</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation -->
            <header class="bg-white shadow-sm">
                <div class="flex items-center justify-between p-4">
                    <!-- Mobile menu button -->
                    <button id="mobile-menu-button" class="md:hidden text-gray-600 focus:outline-none hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-lg font-semibold text-gray-800 md:text-xl"><?= $title ?? 'Dashboard' ?></h1>
                    <div class="flex items-center space-x-4">
                        <div class="hidden md:block text-right">
                            <p class="text-sm font-medium text-gray-900"><?= $pegawai['namapegawai'] ?? 'User' ?></p>
                            <p class="text-xs text-gray-500">Pegawai</p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-white shadow-md hover:scale-110 transition-transform duration-300">
                            <span class="font-bold"><?= substr($pegawai['namapegawai'] ?? 'User', 0, 1) ?></span>
                        </div>
                    </div>
                </div>
            </header>
            <!-- Mobile Sidebar Overlay -->
            <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden hidden backdrop-blur-sm" onclick="toggleMobileSidebar()"></div>
            <!-- Mobile Sidebar -->
            <div id="mobile-sidebar" class="fixed inset-y-0 left-0 w-72 bg-gradient-to-br from-primary-800 via-primary-700 to-primary-900 text-white p-4 transform -translate-x-full transition-transform duration-300 ease-in-out z-50 md:hidden overflow-y-auto">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-2 hover-scale">
                        <div class="w-10 h-10 rounded-lg bg-white bg-opacity-20 backdrop-blur-sm flex items-center justify-center shadow-lg sidebar-logo">
                            <i class="fas fa-fingerprint text-xl text-white"></i>
                        </div>
                        <h1 class="text-2xl font-bold">SIGA</h1>
                    </div>
                    <button onclick="toggleMobileSidebar()" class="w-8 h-8 rounded-full bg-white bg-opacity-10 flex items-center justify-center text-white focus:outline-none hover:bg-opacity-20 transition-all duration-300 hover:rotate-90 transform">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <!-- User Info -->
                <div class="flex flex-col items-center mb-8 px-4 py-6 bg-white bg-opacity-10 rounded-2xl backdrop-blur-sm hover-scale">
                    <div class="w-24 h-24 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center mb-4 shadow-lg ring-4 ring-white ring-opacity-20 animate-pulse-slow">
                        <span class="text-3xl font-bold"><?= substr($pegawai['namapegawai'] ?? 'User', 0, 1) ?></span>
                    </div>
                    <h3 class="font-semibold text-lg"><?= $pegawai['namapegawai'] ?? 'User' ?></h3>
                    <p class="text-xs text-primary-300 mt-1"><?= $pegawai['idpegawai'] ?? '' ?></p>
                </div>
                <!-- Navigation -->
                <div class="text-xs uppercase text-gray-400 font-semibold px-4 mb-2">Menu Utama</div>
                <nav class="flex-1 space-y-1 px-2 pb-4" id="mobile-nav">
                    <a href="<?= site_url('pegawai/dashboard') ?>" class="mobile-menu-item <?= $uri->getSegment(2) == 'dashboard' && !$uri->getSegment(3) ? 'active-nav-link' : 'nav-link' ?>">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="<?= site_url('pegawai/dashboard/riwayat') ?>" class="mobile-menu-item <?= $uri->getSegment(3) == 'riwayat' ? 'active-nav-link' : 'nav-link' ?>">
                        <i class="fas fa-history"></i>
                        <span>Riwayat Absensi</span>
                    </a>
                    <a href="<?= site_url('pegawai/izin') ?>" class="mobile-menu-item <?= $uri->getSegment(2) == 'izin' ? 'active-nav-link' : 'nav-link' ?>">
                        <i class="fas fa-calendar-check"></i>
                        <span>Pengajuan Izin</span>
                    </a>
                    <a href="<?= site_url('pegawai/dashboard/lembur') ?>" class="mobile-menu-item <?= $uri->getSegment(2) == 'dashboard' && $uri->getSegment(3) == 'lembur' ? 'active-nav-link' : 'nav-link' ?>">
                        <i class="fas fa-clock"></i>
                        <span>Riwayat Lembur</span>
                    </a>
                    <a href="<?= site_url('pegawai/dashboard/gaji') ?>" class="mobile-menu-item <?= $uri->getSegment(2) == 'dashboard' && $uri->getSegment(3) == 'gaji' ? 'active-nav-link' : 'nav-link' ?>">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <span>Slip Gaji</span>
                    </a>
                    <div class="text-xs uppercase text-gray-400 font-semibold px-2 mt-6 mb-2">Pengaturan</div>
                    <a href="#" class="mobile-menu-item nav-link">
                        <i class="fas fa-user-cog"></i>
                        <span>Profil</span>
                    </a>
                </nav>
                <!-- Logout -->
                <div class="mt-6 pt-4">
                    <a href="<?= site_url('logout') ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-red-500 bg-opacity-20 hover:bg-opacity-40 text-white transition-all hover:scale-105 transform duration-300">
                        <i class="fas fa-sign-out-alt w-5 text-center"></i>
                        <span>Logout</span>
                    </a>
                    <div class="text-xs text-center mt-4 text-gray-400">
                        <p>SIGA v1.0</p>
                    </div>
                </div>
            </div>
            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto p-4 md:p-6">
                <?php if (session()->getFlashdata('success')) : ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-lg flex items-center animate-slide-down" role="alert">
                        <i class="fas fa-check-circle text-green-500 mr-3 text-lg"></i>
                        <p><?= session()->getFlashdata('success') ?></p>
                    </div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-lg flex items-center animate-slide-down" role="alert">
                        <i class="fas fa-exclamation-circle text-red-500 mr-3 text-lg"></i>
                        <p><?= session()->getFlashdata('error') ?></p>
                    </div>
                <?php endif; ?>
                <?= $this->renderSection('content') ?>
            </main>
            <!-- Footer -->
            <footer class="bg-white p-4 shadow-inner text-center text-gray-500 text-sm">
                <p>&copy; <?= date('Y') ?> SIGA - Sistem Informasi Gaji & Absensi</p>
            </footer>
        </div>
    </div>
    <script>
        // Mobile sidebar toggle
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('mobile-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
            if (!sidebar.classList.contains('-translate-x-full')) {
                animateMobileMenuItems();
            }
        }
        document.getElementById('mobile-menu-button').addEventListener('click', toggleMobileSidebar);
        // Animate menu items on load
        document.addEventListener('DOMContentLoaded', function() {
            animateMenuItems();
        });

        function animateMenuItems() {
            const menuItems = document.querySelectorAll('.menu-item');
            menuItems.forEach((item, index) => {
                setTimeout(() => {
                    item.classList.add('menu-item-active');
                }, 100 + (index * 100));
            });
        }

        function animateMobileMenuItems() {
            const menuItems = document.querySelectorAll('.mobile-menu-item');
            menuItems.forEach((item, index) => {
                item.style.opacity = '0';
                item.style.transform = 'translateX(-20px)';
                setTimeout(() => {
                    item.style.transition = 'all 0.3s ease';
                    item.style.opacity = '1';
                    item.style.transform = 'translateX(0)';
                }, 100 + (index * 100));
            });
        }
    </script>
    <!-- Render scripts section -->
    <?= $this->renderSection('scripts') ?>
</body>

</html>