<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kertas Kerja')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .card-disabled {
            opacity: 0.5;
            pointer-events: none;
        }

        /* ══ GLOBAL RESPONSIVE ══ */
        @media (max-width: 768px) {

        .max-w-7xl { padding-left: 1rem !important; padding-right: 1rem !important; }
        .px-8, .px-10 { padding-left: 1rem !important; padding-right: 1rem !important; }
        .py-10 { padding-top: 1.5rem !important; padding-bottom: 1.5rem !important; }

        .relative.flex.items-center.justify-between { flex-direction: column !important; align-items: flex-start !important; gap: 1rem !important; }

        .flex.items-center.space-x-4 { flex-wrap: wrap !important; gap: 0.5rem !important; justify-content: flex-start !important; width: 100% !important; }
        .flex.items-center.space-x-4 > a,
        .flex.items-center.space-x-4 > form > button {
            padding-left: 0.75rem !important;
            padding-right: 0.75rem !important;
            font-size: 10px !important;
        }

        .grid-cols-5, .grid-cols-6, .grid-cols-4, .grid-cols-3 {
            grid-template-columns: 1fr 1fr !important;
        }

        .grid-cols-4 { grid-template-columns: 1fr 1fr !important; }
        .md\:grid-cols-4 { grid-template-columns: 1fr 1fr !important; }

        .overflow-x-auto { overflow-x: auto !important; -webkit-overflow-scrolling: touch; }
        table { min-width: 600px; }

        .lg\:grid-cols-3, .md\:grid-cols-2 { grid-template-columns: 1fr !important; }

        .space-x-6 { gap: 0.75rem !important; }

        .text-2xl { font-size: 1.2rem !important; }

        .tracking-wider { letter-spacing: 0.02em !important; }

        .min-w-full.border-collapse { font-size: 10px !important; }

    }

    @media (max-width: 480px) {
        .grid-cols-5, .grid-cols-6, .grid-cols-4, .grid-cols-3, .grid-cols-2 {
            grid-template-columns: 1fr !important;
        }
        .md\:grid-cols-4, .md\:grid-cols-2 { grid-template-columns: 1fr !important; }
    }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    {{-- Session Messages --}}
    @if(session('success'))
    <div class="fixed top-4 right-4 bg-green-50 border border-green-200 text-green-800 px-6 py-3 rounded-lg shadow-md z-50 max-w-sm">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="fixed top-4 right-4 bg-red-50 border border-red-200 text-red-800 px-6 py-3 rounded-lg shadow-md z-50 max-w-sm">
        {{ session('error') }}
    </div>
    @endif

    @yield('content')
    <script>
        window.addEventListener('beforeunload', function() {
            sessionStorage.setItem('scrollPos_' + window.location.pathname, window.scrollY);
        });

        window.addEventListener('DOMContentLoaded', function() {
            var savedPos = sessionStorage.getItem('scrollPos_' + window.location.pathname);
            if (savedPos) {
                window.scrollTo(0, parseInt(savedPos));
                sessionStorage.removeItem('scrollPos_' + window.location.pathname);
            }
        });
    </script>
</body>
</html>
