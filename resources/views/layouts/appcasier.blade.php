<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir Restoran Modern</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6; /* Light gray background */
        }
        /* Custom scrollbar for product list */
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1; /* Gray-300 */
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8; /* Gray-400 */
        }
        /* Hide scrollbar by default for narrow screens, show on hover/overflow */
        @media (max-width: 1023px) { /* lg breakpoint */
            .custom-scrollbar {
                overflow-x: auto; /* Allow horizontal scroll for narrow screens */
            }
            .custom-scrollbar::-webkit-scrollbar {
                height: 8px; /* Set height for horizontal scrollbar */
            }
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-3xl shadow-xl overflow-hidden w-full max-w-6xl flex flex-col lg:flex-row h-[90vh]">
        <!-- Sidebar Navigation -->
        <div class="w-16 bg-white flex flex-col items-center py-6 border-r border-gray-200">
            <div class="mb-8">
                <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16 0C7.16344 0 0 7.16344 0 16C0 24.8366 7.16344 32 16 32C24.8366 32 32 24.8366 32 16C32 7.16344 24.8366 0 16 0ZM16 3.2C23.0336 3.2 28.8 8.9664 28.8 16C28.8 23.0336 23.0336 28.8 16 28.8C8.9664 28.8 3.2 23.0336 3.2 16C3.2 8.9664 8.9664 3.2 16 3.2ZM16 6.4C10.7011 6.4 6.4 10.7011 6.4 16C6.4 21.2989 10.7011 25.6 16 25.6C21.2989 25.6 25.6 21.2989 25.6 16C25.6 10.7011 21.2989 6.4 16 6.4ZM16 9.6C18.9706 9.6 21.6 12.0294 21.6 16C21.6 18.9706 19.1706 21.6 16 21.6C13.0294 21.6 10.4 19.1706 10.4 16C10.4 13.0294 12.8294 10.4 16 10.4Z" fill="#FF5722"/>
                </svg>
            </div>
           @include('layouts.sidebar')
            <div class="mt-auto">
                <a href="#" class="p-2 text-gray-400 hover:text-orange-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9.247a8.5 8.5 0 011.666-1.53c.473-.393.882-.716 1.189-.964.307-.247.533-.42.66-.516.126-.097.204-.153.23-.174.025-.021.037-.03.037-.03-.022.012-.047.025-.075.038-.07.03-.15.064-.24.1-.18.067-.38.153-.6.257-.44.2-.95.42-1.5.658-.87.41-1.8.84-2.77 1.3-1.04.48-2.07 1.05-3.09 1.68-1.02.63-1.93 1.25-2.73 1.86-.8.61-1.46 1.18-1.98 1.69-.52.51-.92.98-1.18 1.39-.26.41-.39.78-.39 1.1A2.5 2.5 0 004 20a2.5 2.5 0 002.5 2h9a2.5 2.5 0 002.5-2 2.5 2.5 0 00-2.5-2h-9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5h9c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5h-9a2.5 2.5 0 01-2.5-2 2.5 2.5 0 012.5-2h9z"></path></svg>
                </a>
            </div>
        </div>

        <!-- Main Content Area (Menu & Products) -->
        <div class="flex-1 p-8 overflow-y-auto custom-scrollbar">
           
{{$slot}}
        
    </div>
   </div>
</body>
</html>
