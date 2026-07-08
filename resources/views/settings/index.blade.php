@extends('backup-ui::layouts.backup')

@section('title', __('Settings'))

@section('content')
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">{{ __('Backup Configuration') }}</h2>
        <p class="mt-1 text-sm text-gray-500">{{ __('Configured in config/backup.php') }}</p>
    </div>

    <div class="space-y-6">
        {{-- Source --}}
        <div class="rounded-lg border border-gray-200 bg-white">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('Source') }}</h3>
            </div>
            <div class="px-6 py-4 space-y-4">
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">{{ __('Application Name') }}</h4>
                    <p class="text-sm text-gray-900">{{ $backupConfig->backup->name }}</p>
                </div>

                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">{{ __('Databases') }}</h4>
                    @forelse ($backupSource->databases as $database)
                        <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">{{ $database }}</span>
                    @empty
                        <p class="text-sm text-gray-500 italic">{{ __('None') }}</p>
                    @endforelse
                </div>

                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">{{ __('Files Included') }}</h4>
                    @forelse ($backupSource->files->include as $path)
                        <p class="text-sm text-gray-900 font-mono">{{ $path }}</p>
                    @empty
                        <p class="text-sm text-gray-500 italic">{{ __('None') }}</p>
                    @endforelse
                </div>

                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">{{ __('Files Excluded') }}</h4>
                    @forelse ($backupSource->files->exclude as $path)
                        <p class="text-sm text-gray-900 font-mono">{{ $path }}</p>
                    @empty
                        <p class="text-sm text-gray-500 italic">{{ __('None') }}</p>
                    @endforelse
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-700">{{ __('Follow Symlinks') }}</h4>
                        @include('backup-ui::settings.partials.badge', ['active' => $backupSource->files->followLinks])
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-700">{{ __('Ignore Unreadable Dirs') }}</h4>
                        @include('backup-ui::settings.partials.badge', ['active' => $backupSource->files->ignoreUnreadableDirectories])
                    </div>
                </div>
            </div>
        </div>

        {{-- Destination --}}
        <div class="rounded-lg border border-gray-200 bg-white">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('Destination') }}</h3>
            </div>
            <div class="px-6 py-4 space-y-4">
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">{{ __('Disks') }}</h4>
                    @forelse ($destination->disks as $disk)
                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">{{ $disk }}</span>
                    @empty
                        <p class="text-sm text-gray-500 italic">{{ __('None') }}</p>
                    @endforelse
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-700">{{ __('Compression Method') }}</h4>
                        <p class="text-sm text-gray-900">{{ $destination->compressionMethod }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-700">{{ __('Compression Level') }}</h4>
                        <p class="text-sm text-gray-900">{{ $destination->compressionLevel }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-700">{{ __('Filename Prefix') }}</h4>
                        <p class="text-sm text-gray-900 font-mono">{{ $destination->filenamePrefix ?: __('(none)') }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-700">{{ __('Continue on Failure') }}</h4>
                        @include('backup-ui::settings.partials.badge', ['active' => $destination->continueOnFailure])
                    </div>
                </div>
            </div>
        </div>

        {{-- Schedule --}}
        <div class="rounded-lg border border-gray-200 bg-white">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('Schedule') }}</h3>
            </div>
            <div class="px-6 py-4 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-700">{{ __('Backup') }}</h4>
                        <div class="mt-1 flex items-center gap-2">
                            @include('backup-ui::settings.partials.badge', ['active' => $schedule['backup']['enabled']])
                            @if ($schedule['backup']['enabled'])
                                <span class="text-sm text-gray-500">{{ __(':frequency at :time', ['frequency' => $schedule['backup']['frequency'], 'time' => $schedule['backup']['time']]) }}</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-700">{{ __('Cleanup') }}</h4>
                        <div class="mt-1 flex items-center gap-2">
                            @include('backup-ui::settings.partials.badge', ['active' => $schedule['clean']['enabled']])
                            @if ($schedule['clean']['enabled'])
                                <span class="text-sm text-gray-500">{{ __(':frequency at :time', ['frequency' => $schedule['clean']['frequency'], 'time' => $schedule['clean']['time']]) }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Encryption --}}
        <div class="rounded-lg border border-gray-200 bg-white">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('Encryption') }}</h3>
            </div>
            <div class="px-6 py-4 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-700">{{ __('Encryption') }}</h4>
                        @include('backup-ui::settings.partials.badge', ['active' => $backupConfig->backup->encryption->value !== 'none'])
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-700">{{ __('Algorithm') }}</h4>
                        <p class="text-sm text-gray-900">{{ $backupConfig->backup->encryption->value }}</p>
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-700">{{ __('Password Set') }}</h4>
                    @include('backup-ui::settings.partials.badge', ['active' => ! is_null($backupConfig->backup->password)])
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-700">{{ __('Verify Backup') }}</h4>
                    @include('backup-ui::settings.partials.badge', ['active' => $backupConfig->backup->verifyBackup])
                </div>
            </div>
        </div>

        {{-- Cleanup / Retention --}}
        <div class="rounded-lg border border-gray-200 bg-white">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('Retention & Cleanup') }}</h3>
            </div>
            <div class="px-6 py-4 space-y-4">
                <div>
                    <h4 class="text-sm font-medium text-gray-700">{{ __('Strategy') }}</h4>
                    <p class="text-sm text-gray-900 font-mono">{{ class_basename($cleanup->strategy) }}</p>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-700">{{ __('Keep All') }}</h4>
                        <p class="text-sm text-gray-900">{{ __(':days days', ['days' => $cleanup->defaultStrategy->keepAllBackupsForDays]) }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-700">{{ __('Keep Daily') }}</h4>
                        <p class="text-sm text-gray-900">{{ __(':days days', ['days' => $cleanup->defaultStrategy->keepDailyBackupsForDays]) }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-700">{{ __('Keep Weekly') }}</h4>
                        <p class="text-sm text-gray-900">{{ __(':weeks weeks', ['weeks' => $cleanup->defaultStrategy->keepWeeklyBackupsForWeeks]) }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-700">{{ __('Keep Monthly') }}</h4>
                        <p class="text-sm text-gray-900">{{ __(':months months', ['months' => $cleanup->defaultStrategy->keepMonthlyBackupsForMonths]) }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-700">{{ __('Keep Yearly') }}</h4>
                        <p class="text-sm text-gray-900">{{ __(':years years', ['years' => $cleanup->defaultStrategy->keepYearlyBackupsForYears]) }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-700">{{ __('Max Storage') }}</h4>
                        <p class="text-sm text-gray-900">
                            @if ($cleanup->defaultStrategy->deleteOldestBackupsWhenUsingMoreMegabytesThan)
                                {{ number_format($cleanup->defaultStrategy->deleteOldestBackupsWhenUsingMoreMegabytesThan) }} MB
                            @else
                                <span class="text-gray-500 italic">{{ __('Unlimited') }}</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Health Monitoring --}}
        <div class="rounded-lg border border-gray-200 bg-white">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('Health Monitoring') }}</h3>
            </div>
            <div class="px-6 py-4 space-y-4">
                @forelse ($monitoredBackups->monitorBackups as $monitored)
                    <div class="border border-gray-100 rounded-lg p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-medium text-gray-900">{{ $monitored['name'] }}</h4>
                            <div class="flex gap-1">
                                @foreach ($monitored['disks'] as $disk)
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800">{{ $disk }}</span>
                                @endforeach
                            </div>
                        </div>
                        @if (! empty($monitored['healthChecks']))
                            <div class="space-y-1">
                                @foreach ($monitored['healthChecks'] as $check => $value)
                                    <p class="text-xs text-gray-500">
                                        {{ class_basename($check) }}: {{ $value }}
                                        @if (str_contains(class_basename($check), 'Age'))
                                            {{ __('day(s)') }}
                                        @elseif (str_contains(class_basename($check), 'Storage'))
                                            MB
                                        @endif
                                    </p>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-gray-500 italic">{{ __('No health monitoring configured.') }}</p>
                @endforelse
            </div>
        </div>

        {{-- Notifications --}}
        <div class="rounded-lg border border-gray-200 bg-white">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('Notifications') }}</h3>
            </div>
            <div class="px-6 py-4 space-y-4">
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">{{ __('Events & Channels') }}</h4>
                    @forelse ($notifications->notifications as $notification => $channels)
                        <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                            <span class="text-sm text-gray-900">{{ class_basename($notification) }}</span>
                            <div class="flex gap-1">
                                @foreach ($channels as $channel)
                                    <span class="inline-flex items-center rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-medium text-purple-800">{{ $channel }}</span>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 italic">{{ __('No notifications configured.') }}</p>
                    @endforelse
                </div>

                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">{{ __('Mail Recipients') }}</h4>
                    @php $to = is_array($notifications->mail->to) ? $notifications->mail->to : [$notifications->mail->to]; @endphp
                    @forelse ($to as $email)
                        <p class="text-sm text-gray-900">{{ $email }}</p>
                    @empty
                        <p class="text-sm text-gray-500 italic">{{ __('None') }}</p>
                    @endforelse
                </div>

                @if ($notifications->slack->webhookUrl)
                    <div>
                        <h4 class="text-sm font-medium text-gray-700">{{ __('Slack') }}</h4>
                        <p class="text-sm text-gray-500">{{ __('Configured') }}@if ($notifications->slack->channel) ({{ $notifications->slack->channel }})@endif</p>
                    </div>
                @endif

                @if ($notifications->discord?->webhookUrl)
                    <div>
                        <h4 class="text-sm font-medium text-gray-700">{{ __('Discord') }}</h4>
                        <p class="text-sm text-gray-500">{{ __('Configured') }}</p>
                    </div>
                @endif

                @if ($notifications->webhook?->url)
                    <div>
                        <h4 class="text-sm font-medium text-gray-700">{{ __('Webhook') }}</h4>
                        <p class="text-sm text-gray-500">{{ __('Configured') }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Advanced --}}
        <div class="rounded-lg border border-gray-200 bg-white">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('Advanced') }}</h3>
            </div>
            <div class="px-6 py-4 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-700">{{ __('Temporary Directory') }}</h4>
                        <p class="text-sm text-gray-900 font-mono">{{ $backupConfig->backup->temporaryDirectory ?? __('(default)') }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-700">{{ __('Retry Tries') }}</h4>
                        <p class="text-sm text-gray-900">{{ $backupConfig->backup->tries }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-700">{{ __('Retry Delay') }}</h4>
                        <p class="text-sm text-gray-900">{{ $backupConfig->backup->retryDelay > 0 ? __(':seconds seconds', ['seconds' => $backupConfig->backup->retryDelay]) : __('None') }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-700">{{ __('Database Dump Compressor') }}</h4>
                        <p class="text-sm text-gray-900 font-mono">{{ $backupConfig->backup->databaseDumpCompressor ? class_basename($backupConfig->backup->databaseDumpCompressor) : __('(none)') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Backup UI Config --}}
        <div class="rounded-lg border border-gray-200 bg-white">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('Package Settings') }}</h3>
                <p class="text-xs text-gray-500 mt-1">{{ __('Configured in config/backup-ui.php') }}</p>
            </div>
            <div class="px-6 py-4 space-y-4">
                <div>
                    <h4 class="text-sm font-medium text-gray-700">{{ __('Authorization Gate') }}</h4>
                    <p class="text-sm text-gray-900 font-mono">{{ $backupUiConfig['gate'] ?? __('Not set (default deny)') }}</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-700">{{ __('Route Prefix') }}</h4>
                        <p class="text-sm text-gray-900">/{{ $backupUiConfig['routes']['prefix'] ?? 'backup' }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-700">{{ __('Route Middleware') }}</h4>
                        <div class="flex gap-1 flex-wrap">
                            @foreach ($backupUiConfig['routes']['middleware'] ?? ['web'] as $mw)
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800">{{ $mw }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
