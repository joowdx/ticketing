@use(App\Enums\ActionStatus)
@use(App\Filament\Helpers\ColorToHex)

@php($chat ??= false)
@php($progress ??= true)

<section wire:poll class="space-y-3">
    <div class="pl-[0.75rem] space-y-3">
        <ol class="relative border-gray-200 border-s dark:border-gray-700">
            @if(!$chat && $progress && in_array($request->action->status, [ActionStatus::STARTED, ActionStatus::RESPONDED], true))
                <li class="mb-4 ms-6">
                    <span
                        class='absolute flex items-center justify-center w-6 h-6 rounded-full -start-3 ring-8 ring-white dark:ring-gray-900 bg-white dark:bg-gray-900'
                        @style(['color:'.ColorToHex::convert('success')])
                    >
                        <x-filament::icon class="h-6 w-6" :icon="ActionStatus::IN_PROGRESS->getIcon()"/>
                    </span>

                    <div class="flex justify-between">
                        <h3 class="flex items-center mb-1 text-base italic uppercase">
                            {{ ActionStatus::IN_PROGRESS->getLabel() }}
                        </h3>
                    </div>

                    <time class="block mb-2 text-sm font-light leading-none text-neutral-500">
                        Please be patient as the assigned

                        @switch(true)
                            @case($request->assignees()->count() > 2)
                                agents are
                                @break
                            @default
                                agent is
                        @endswitch

                        currently working on the request.
                    </time>
                </li>
            @endif

            @foreach ($request->actions->when($chat, fn ($actions) => $actions->filter(fn ($action) => $action->status === ActionStatus::RESPONDED)) as $action)
                <li class="mb-4 ms-6">
                    <span
                        class='absolute flex items-center justify-center w-6 h-6 rounded-full -start-3 ring-8 ring-white dark:ring-gray-900 bg-white dark:bg-gray-900'
                        @style(['color:'.ColorToHex::convert($action->status->getColor())])
                    >
                        <x-filament::icon class="h-6 w-6" icon="{{ $action->status->getIcon() }}"/>
                    </span>

                    <div class="flex justify-between">
                        <h3 class="flex items-center mb-1 text-base">
                            {{ $action->user?->name ?? '' }}
                        </h3>

                        <time class="text-sm font-light leading-none text-neutral-500">
                            {{ $action->created_at->diffForHumans() }}
                        </time>
                    </div>

                    <time class="block mb-2 text-sm font-light leading-none text-neutral-500">
                        <span @class(["font-bold", "italic" => is_null($action->user)])>
                            @switch($action->status)
                                @case(ActionStatus::SUBMITTED)
                                @case(ActionStatus::ASSIGNED)
                                @case(ActionStatus::QUEUED)
                                    {{
                                        $request->actions->reverse()->first(fn ($act) => $act->status === $action->status)->is($action)
                                            ? $action->status->getLabel()
                                            : "Re{$action->status->value}"
                                    }}
                                    @break
                                @default
                                    {{ $action->status->getLabel() }}
                            @endswitch
                        </span>

                        on {{ $action->created_at->format('jS \of F Y \a\t H:i') }}
                    </time>

                    @if ($action->remarks)
                        <div class="p-3 text-base bg-gray-100 rounded-md dark:bg-gray-800">
                            <svg class="w-5 h-5 mb-2 text-gray-400 dark:text-gray-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 14">
                                <path d="M6 0H2a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h4v1a3 3 0 0 1-3 3H2a1 1 0 0 0 0 2h1a5.006 5.006 0 0 0 5-5V2a2 2 0 0 0-2-2Zm10 0h-4a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h4v1a3 3 0 0 1-3 3h-1a1 1 0 0 0 0 2h1a5.006 5.006 0 0 0 5-5V2a2 2 0 0 0-2-2Z"/>
                            </svg>
                            <div class="prose max-w-none dark:prose-invert [&>*:first-child]:mt-0 [&>*:last-child]:mb-0 prose-sm text-sm leading-6 text-gray-950 dark:text-white  ">
                                {{ str($action->remarks)->markdown()->toHtmlString() }}
                            </div>
                        </div>
                    @endif
                </li>
            @endforeach
        </ol>
    </div>
</section>
