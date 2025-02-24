@use(App\Enums\ActionStatus)
@use(App\Filament\Helpers\ColorToHex)

@php($chat ??= false)
@php($re ??= false)

<div>
    @if (isset($action) && ! isset($content))
        <span
            class='absolute flex items-center justify-center w-6 h-6 bg-white rounded-full -start-3 ring-8 ring-white dark:ring-gray-900 dark:bg-gray-900'
            @style(['color:'.ColorToHex::convert($action->status->getColor())])
        >
            <x-filament::icon class="w-6 h-6" icon="{{ $action->status->getIcon() }}"/>
        </span>

        <div class="flex justify-between">
            <h3 class="flex items-center mb-1 text-base">
                {{ $action->user?->name ?? '' }}

                @if ($chat)
                    {{ $action->user->id === Auth::id() ? '(You)' : '' }}
                @endif
            </h3>

            <time class="text-sm font-light leading-none text-neutral-500">
                {{ $action->created_at->diffForHumans() }}
            </time>
        </div>

        <time class="block mb-2 text-sm font-light leading-none text-neutral-500">
            @if (!$chat)
                <span @class(["font-bold", "italic" => is_null($action->user)])>
                    {{ $re ? "Re{$action->status->value}" : $action->status->getLabel() }}
                </span>
                on
            @endif

            {{ $action->created_at->format('jS \of F Y \a\t H:i') }}
        </time>
    @endif

    @if ($content ?? $action->remarks)
        @if ($chat)
            <div class="prose max-w-none dark:prose-invert [&>*:first-child]:mt-0 [&>*:last-child]:mb-0 prose-sm text-sm leading-6 text-gray-950 dark:text-white  ">
                {{ str($content ?? $action->remarks)->markdown()->toHtmlString() }}
            </div>
        @else
            <div class="p-3 text-base bg-gray-100 rounded-md dark:bg-gray-800">
                <svg class="w-5 h-5 mb-2 text-gray-400 dark:text-gray-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 14">
                    <path d="M6 0H2a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h4v1a3 3 0 0 1-3 3H2a1 1 0 0 0 0 2h1a5.006 5.006 0 0 0 5-5V2a2 2 0 0 0-2-2Zm10 0h-4a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h4v1a3 3 0 0 1-3 3h-1a1 1 0 0 0 0 2h1a5.006 5.006 0 0 0 5-5V2a2 2 0 0 0-2-2Z"/>
                </svg>
                <div class="prose max-w-none dark:prose-invert [&>*:first-child]:mt-0 [&>*:last-child]:mb-0 prose-sm text-sm leading-6 text-gray-950 dark:text-white  ">
                    {{ str($content ?? $action->remarks)->markdown()->toHtmlString() }}
                </div>
            </div>
        @endif
    @endif
</div>
