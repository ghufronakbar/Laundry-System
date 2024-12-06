<!-- Navbar di atas untuk perangkat mobile -->
<div class="lg:hidden flex items-center justify-between bg-gray-800 text-white p-4 fixed top-0 w-full">
    <!-- Tombol Hamburger (Toggle) -->
    <button id="toggle-sidebar" class="text-white text-2xl">
        ☰
    </button>
    <h2 class="text-2xl font-semibold">MyLaundry</h2>
</div>

<!-- Sidebar untuk perangkat desktop dan mobile -->
<div id="sidebar" class="px-8 py-8 bg-gray-800 text-white hidden lg:block fixed inset-0 lg:relative">
    <div class="space-y-4">
        <h2 class="text-2xl font-semibold mb-8 py-2 px-4">MyLaundry</h2>

        <ul>
            <li>
                <a href="{{ route('dashboard') }}" class="block py-2 px-4 rounded hover:bg-gray-700 text-start">
                    Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('machine.index') }}" class="block py-2 px-4 rounded hover:bg-gray-700 text-start">
                    Mesin
                </a>
            </li>
            <li>
                <a href="{{ route('transactions.index') }}" class="block py-2 px-4 rounded hover:bg-gray-700 text-start">
                    Transaksi
                </a>
            </li>
            <li>
                <a href="{{ route('logout') }}" class="block py-2 px-4 rounded hover:bg-gray-700">
                    Logout
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- Sidebar untuk mobile (hidden by default) -->
<div id="mobile-sidebar" class="lg:hidden fixed inset-0 bg-gray-800 text-white p-4 hidden">
    <div class="flex justify-between items-center mb-8">
        <!-- Tombol Close untuk mobile sidebar -->
        <button id="close-sidebar" class="text-white text-2xl">
            &times;
        </button>
        <h2 class="text-2xl font-semibold">MyLaundry</h2>
    </div>
    <div class="space-y-4">
        <ul>
            <li>
                <a href="{{ route('dashboard') }}" class="block py-2 px-4 rounded hover:bg-gray-700">
                    Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('machine.index') }}" class="block py-2 px-4 rounded hover:bg-gray-700">
                    Mesin
                </a>
            </li>
            <li>
                <a href="{{ route('transactions.index') }}" class="block py-2 px-4 rounded hover:bg-gray-700">
                    Transaksi
                </a>
            </li>
            <li>
                <a href="{{ route('logout') }}" class="block py-2 px-4 rounded hover:bg-gray-700">
                    Logout
                </a>
            </li>
        </ul>
    </div>
</div>

<script>
    // Mengambil elemen-elemen yang dibutuhkan
    const toggleButton = document.getElementById('toggle-sidebar');
    const mobileSidebar = document.getElementById('mobile-sidebar');
    const closeButton = document.getElementById('close-sidebar');

    // Menambahkan event listener pada tombol toggle (untuk membuka sidebar)
    toggleButton.addEventListener('click', function() {
        mobileSidebar.classList.toggle('hidden');  // Toggle sidebar pada klik
    });

    // Menambahkan event listener pada tombol close (untuk menutup sidebar)
    closeButton.addEventListener('click', function() {
        mobileSidebar.classList.add('hidden');  // Sembunyikan sidebar saat tombol close diklik
    });
</script>
