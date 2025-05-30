<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For detailed instructions you can look the title section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'title' => 'Autokeys',
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    |
    | Here you can activate the favicon.
    |
    | For detailed instructions you can look the favicon section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_ico_only' => false,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Google Fonts
    |--------------------------------------------------------------------------
    |
    | Here you can allow or not the use of external google fonts. Disabling the
    | google fonts may be useful if your admin panel internet access is
    | restricted somehow.
    |
    | For detailed instructions you can look the google fonts section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'google_fonts' => [
        'allowed' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For detailed instructions you can look the logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'logo' => '<b>Auto</b>keys',
    'logo_img' => '/images/AutoFondo.jpeg',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => '',

    /*
    |--------------------------------------------------------------------------
    | Authentication Logo
    |--------------------------------------------------------------------------
    |
    | Here you can setup an alternative logo to use on your login and register
    | screens. When disabled, the admin panel logo will be used instead.
    |
    | For detailed instructions you can look the auth logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'auth_logo' => [
        'enabled' => false,
        'img' => [
            'path' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
            'alt' => 'Auth Logo',
            'class' => '',
            'width' => 50,
            'height' => 50,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Preloader Animation
    |--------------------------------------------------------------------------
    |
    | Here you can change the preloader animation configuration. Currently, two
    | modes are supported: 'fullscreen' for a fullscreen preloader animation
    | and 'cwrapper' to attach the preloader animation into the content-wrapper
    | element and avoid overlapping it with the sidebars and the top navbar.
    |
    | For detailed instructions you can look the preloader section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'preloader' => [
        'enabled' => true,
        'mode' => 'cwrapper',
        'img' => [
            'path' => '/images/AutoFondo.jpeg',
            'alt' => 'AdminLTE Preloader Image',
            'effect' => 'animation__shake',
            'width' => 60,
            'height' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    |
    | Here you can activate and change the user menu.
    |
    | For detailed instructions you can look the user menu section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'usermenu_enabled' => true,
    'usermenu_header' => false,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => false,
    'usermenu_desc' => false,
    'usermenu_profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For detailed instructions you can look the layout section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => null,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => null,

    /*
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the authentication views.
    |
    | For detailed instructions you can look the auth classes section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For detailed instructions you can look the admin panel classes here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-light-navy elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-navy navbar-dark',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For detailed instructions you can look the sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For detailed instructions you can look the right sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For detailed instructions you can look the urls section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_route_url' => false,
    'dashboard_url' => 'dashboard',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,
    'disable_darkmode_routes' => false,

    /*
    |--------------------------------------------------------------------------
    | Laravel Asset Bundling
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Asset Bundling option for the admin panel.
    | Currently, the next modes are supported: 'mix', 'vite' and 'vite_js_only'.
    | When using 'vite_js_only', it's expected that your CSS is imported using
    | JavaScript. Typically, in your application's 'resources/js/app.js' file.
    | If you are not using any of these, leave it as 'false'.
    |
    | For detailed instructions you can look the asset bundling section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'laravel_asset_bundling' => false,
    'laravel_css_path' => 'css/app.css',
    'laravel_js_path' => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

   'menu' => [
    // ========================================
    // ITEMS DE NAVBAR (PARTE SUPERIOR DERECHA)
    // ========================================
    [
        'type' => 'navbar-search',
        'text' => 'Buscar',
        'topnav_right' => true,
    ],
    [
        'type' => 'fullscreen-widget',
        'topnav_right' => true,
    ],

    // ========================================
    // ITEMS DE SIDEBAR
    // ========================================
    [
        'text' => 'Dashboard',
        'url' => 'dashboard',
        'icon' => 'fas fa-fw fa-tachometer-alt',
        'can' => ['admin', 'limited_user'], // Accesible para ambos roles
    ],

    // ========================================
    // SECCIÓN INVENTARIO
    // ========================================
    [
        'header' => 'INVENTARIO',
        'can' => ['admin', 'inventario_limited'],
    ],
    [
        'text' => 'Almacenes',
        'url' => 'almacenes',
        'icon' => 'fas fa-fw fa-warehouse',
        'can' => 'admin', // Solo admin
    ],
    [
        'text' => 'Productos',
        'url' => 'productos',
        'icon' => 'fas fa-fw fa-box-open',
        'can' => 'admin', // Solo admin
    ],
    [
        'text' => 'Inventario',
        'icon' => 'fas fa-fw fa-boxes',
        'can' => ['admin', 'inventario_limited'],
        'submenu' => [
            [
                'text' => 'Inventario',
                'url' => 'inventarios',
                'icon' => 'fas fa-fw fa-clipboard-check',
                'can' => ['admin', 'inventario_limited'],
            ],
            [
                'text' => 'Transferencias',
                'url' => 'transferencias',
                'icon' => 'fas fa-fw fa-exchange-alt',
                'can' => ['admin', 'inventario_limited'],
            ],
            
    [
        'text' => 'Carga/Descarga',
        'url' => 'cargas',
        'icon' => 'fas fa-fw fa-angle-double-down',
        'can' => ['admin', 'inventario_limited'],
    ],

        ],
    ],


    // ========================================
    // SECCIÓN VENTAS (para usuarios limitados)
    // ========================================
    [
        'header' => 'VENTAS',
        'can' => ['admin', 'presupuestos_limited', 'ordenes_limited', 'ventas_limited'],
    ],
    [
        'text' => 'Clientes',
        'url' => 'clientes',
        'icon' => 'fas fa-fw fa-user-friends',
        'can' => ['admin', 'ventas_limited'],
    ],
    [
        'text' => 'Trabajos',
        'url' => 'trabajos',
        'icon' => 'fas fa-fw fa-tasks',
        'can' => ['admin', 'ventas_limited'],
    ],
    [
        'text' => 'Presupuestos',
        'url' => 'presupuestos',
        'icon' => 'fas fa-fw fa-file-invoice',
        'can' => ['admin', 'presupuestos_limited'],
    ],
    [
        'text' => 'Órdenes de Trabajo',
        'url' => 'ordens',
        'icon' => 'fas fa-fw fa-tasks',
        'can' => ['admin', 'ordenes_limited'],
    ],
    [
        'text' => 'Registro de Ventas',
        'url' => 'registro-vs',
        'icon' => 'fas fa-fw fa-shopping-cart',
        'can' => ['admin', 'ventas_limited'],
    ],
    [
        'text' => 'Cuentas por Cobrar',
        'url' => 'cxc',
        'icon' => 'fas fa-fw fa-file-invoice-dollar',
        'can' => [  'admin','ventas_limited' ]
    ],

    // ========================================
    // SECCIÓN NÓMINA (solo admin)
    // ========================================
    [
        'header' => 'NÓMINA',
        'can' => 'admin',
    ],
    [
        'text' => 'Empleados',
        'url' => 'empleados',
        'icon' => 'fas fa-fw fa-id-badge',
        'can' => 'admin',
    ],
    [
        'text' => 'Movimientos',
        'icon' => 'fas fa-fw fa-random',
        'can' => 'admin',
        'submenu' => [
            [
                'text' => 'Préstamos',
                'url' => 'prestamos',
                'can' => 'admin',
            ],
            [
                'text' => 'Descuentos',
                'url' => 'descuentos',
                'can' => 'admin',
            ],
            [
                'text' => 'Abonos',
                'url' => 'abonos',
                'can' => 'admin',
            ],
        ],
    ],
    [
        'text' => 'Procesos de Nómina',
        'icon' => 'fas fa-fw fa-calculator',
        'can' => 'admin',
        'submenu' => [
            [
                'text' => 'Generar Pagos',
                'url' => 'nempleados',
                'can' => 'admin',
            ],
            [
                'text' => 'Reportes',
                'url' => 'nempleados/generar-reporte',
                'can' => 'admin',
            ],
        ],
    ],

    // ========================================
    // SECCIÓN CONTABILIDAD
    // ========================================
    [
        'header' => 'CONTABILIDAD',
        'can' => ['admin'],
    ],
    [
        'text' => 'Registro de Costos',
        'url' => 'costos',
        'icon' => 'fas fa-fw fa-money-bill-wave',
        'can' => ['admin'],
    ],
    [
        'text' => 'Registro de Gastos',
        'url' => 'gastos',
        'icon' => 'fas fa-fw fa-receipt',
        'can' => ['admin'],
    ],
    [
        'text' => 'Tipos de Pago',
        'url' => 'tipos-de-pagos',
        'icon' => 'fas fa-fw fa-credit-card',
        'can' => 'admin',
    ],
    [
        'text' => 'Subcategorías',
        'url' => 'categorias',
        'icon' => 'fas fa-fw fa-tags',
        'can' => 'admin',
    ],
    [  
        'header' => 'REPORTES',
        'can' => 'admin',
    ],
    [
        'text' => 'Reportes',
        'icon' => 'fas fa-fw fa-chart-area',
        'can' => 'admin',
        'submenu' => [
            [
                'text' => 'Estado de Resultados',
                'url' => 'estadisticas-ventas',
                'icon' => 'fas fa-fw fa-chart-line',
                'can' => 'admin',
            ],
            [
                'text' => 'Reporte de Ventas Vanes',
                'url' => 'estadisticas-vanes',
                'icon' => 'fas fa-fw fa-chart-bar',
                'can' => 'admin',
            ],
            [
                'text' => 'Cierre Semanal',
                'url' => 'cierre-ventas-semanal',
                'icon' => 'fas fa-fw fa-chart-line',
                'can' => 'admin',
            ],
            [
                'text' => 'Reporte de CXC',
                'url' => 'reportes/cxc',
                'icon' => 'fas fa-fw fa-chart-line',
                'can' => 'admin',
            ],
        ],
    ],
    // ========================================
    // SECCIÓN ADMINISTRACIÓN (solo admin)
    // ========================================
    [
        'header' => 'ADMINISTRACIÓN',
        'can' => 'admin',
    ],
    [
        'text' => 'Perfil',
        'url' => 'profile',
        'icon' => 'fas fa-fw fa-users-cog',
        'can' => 'admin',
    ],

],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For detailed instructions you can look the plugins section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Plugins-Configuration
    |
    */

    'plugins' => [
        'Toastr' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css',
                ],
            ],
        ],
        'SweetAlert2' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@11',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css',
                ],
            ],
        ],
        'jQuery' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//code.jquery.com/jquery-3.6.0.min.js',
                ],
            ],
        ],
        'Select2' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
                ],
            ],
        ],
        'DataTables' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/responsive/2.4.0/css/responsive.dataTables.min.css',
                ],
            ],
        ],
        'MiPlugin' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true, 
                    'location' => '/custom/script.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => '/custom/style.css',
                ],
            ],
        ],
        'Select2' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IFrame
    |--------------------------------------------------------------------------
    |
    | Here we change the IFrame mode configuration. Note these changes will
    | only apply to the view that extends and enable the IFrame mode.
    |
    | For detailed instructions you can look the iframe mode section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/IFrame-Mode-Configuration
    |
    */

    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Livewire support.
    |
    | For detailed instructions you can look the livewire here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'livewire' => false,

    'custom_js' => [
        'js/script.js',
    ],
    'custom_css' => [
        'css/global.css',
    ],
];