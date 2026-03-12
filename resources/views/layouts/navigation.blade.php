<nav x-data="{ open: false }" style="background-color:#bf4646;" class="border-b border-gray-700">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">

            <!-- LEFT : Logo / Brand -->
            <div class="flex items-center space-x-6">
                <h1 class="text-white text-xl font-bold" style="background:#bf4646;padding:6px 16px;border-radius:4px;">
                    MotoPart
                </h1>

                <!-- Menu -->
                <a href="{{ route('dashboard') }}" class="text-white font-medium border-b-2 border-blue-500 pb-1">
                    Dashboard
                </a>
            </div>

            <!-- RIGHT : User -->
            <div class="flex items-center">

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">

                        <button class="flex items-center text-white text-sm font-medium focus:outline-none">
                            <div>{{ Auth::user()->name }}</div>

                            <svg class="ms-2 h-4 w-4 fill-current" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4
                                    4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>

                    </x-slot>

                    <x-slot name="content">

                        <x-dropdown-link :href="route('profile.edit')">
                            Profile
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                this.closest('form').submit();">
                                Log Out
                            </x-dropdown-link>
                        </form>

                    </x-slot>
                </x-dropdown>

            </div>

        </div>
    </div>

</nav>
