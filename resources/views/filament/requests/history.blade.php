<section class="space-y-3">
    <ol class="relative border-gray-200 border-s dark:border-gray-700">
        @foreach ($request->actions as $action)
            <li class="ms-6">
                <span class="absolute flex items-center justify-center w-6 h-6 p-0.5 rounded-full bg-custom-500 -start-3"
                    @style(["--c-500:var(--{$action->status->getColor()}-500)"])
                >
                    <x-filament::icon :icon="$action->status->getIcon()" />
                </span>

                <h3 class="mb-1 text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $action->status->getLabel() }} by {{ $action->user->name }}
                </h3>
                <time class="block mb-2 text-sm font-normal leading-none text-neutral-500">
                    {{ $action->created_at->format('F jS, Y') }} at {{ $action->created_at->format('H:i') }} ({{ $action->created_at->diffForHumans() }})
                </time>
                @if ($action->remarks)
                    <div class="prose max-w-none dark:prose-invert [&>*:first-child]:mt-0 [&>*:last-child]:mb-0 prose-sm text-sm leading-6 text-gray-950 dark:text-white">
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 14">
                            <path d="M6 0H2a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h4v1a3 3 0 0 1-3 3H2a1 1 0 0 0 0 2h1a5.006 5.006 0 0 0 5-5V2a2 2 0 0 0-2-2Zm10 0h-4a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h4v1a3 3 0 0 1-3 3h-1a1 1 0 0 0 0 2h1a5.006 5.006 0 0 0 5-5V2a2 2 0 0 0-2-2Z"/>
                        </svg>
                        {{ str($action->remarks)->markdown()->toHtmlString() }}
                    </div>
                @endif
            </li>
        @endforeach
    </ol>
</section>
