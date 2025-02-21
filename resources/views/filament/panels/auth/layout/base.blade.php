<div id="auth-content" class="relative flex items-center justify-center min-h-screen">
    <div class="absolute w-full min-h-screen overlay backdrop-blur-sm"></div>
    <div class="w-full  bg-gray-200/50 dark:bg-gray-800/50 rounded-3xl backdrop-blur-2xl max-w-{{ $this->getMaxWidth() ?? 'lg' }} ">
        <div class="relative z-10 grid w-full max-w-5xl p-8 mx-auto overflow-hidden bg-gray-100 dark:bg-gray-900 lg:rounded-lg">
            <section class="grid auto-cols-fr gap-y-6">
                <header class="flex flex-col items-center fi-simple-header">
                    <div class="flex justify-end w-full">
                        @include('theme-switcher')
                    </div>

                    <a href="/">
                        @include('banner')
                    </a>

                    <h1 class="text-3xl font-bold tracking-tight text-center fi-simple-header-heading text-gray-950 dark:text-white">
                        @yield('heading', $this->getHeading())
                    </h1>

                    <p class="mt-2 text-sm text-center text-gray-500 fi-simple-header-subheading dark:text-gray-400">
                        @yield('subheading', $this->getSubHeading())
                    </p>
                </header>

                @yield('content')

                <footer>
                    @yield('footer')
                </footer>
            </section>
        </div>
    </div>
</div>

@push('styles')
@stack('styles')
<style>
    #auth-content {
        background-size: 100%;
        background-repeat: no-repeat;
        background-position: center;
        background-attachment: fixed;
    }

    html:not(.dark) #auth-content {
        background-image: url("{{ asset('assets/svg/layered-peak-light.svg') }}");
    }

    html.dark #auth-content {
        background-image: url("{{ asset('assets/svg/layered-peak-dark.svg') }}");
    }

    html:not(.dark) .overlay {
        background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(var(--primary-400), 0.5) 100%);
    }

    html.dark .overlay {
        background: linear-gradient(0deg, rgba(0,0,0,0) 0%, rgba(var(--primary-600), 0.5) 100%);
    }
</style>
@endpush

@push('scripts')
@stack('scripts')
@endpush
