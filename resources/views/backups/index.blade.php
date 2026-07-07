@extends('backup-ui::layouts.backup')

@section('title', __('Backups'))

@section('content')
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-2xl font-bold text-gray-900">{{ __('Backups') }}</h2>
        <div class="flex gap-3">
            <form action="{{ route('backup.run') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="type" value="full">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    {{ __('Run Full Backup') }}
                </button>
            </form>
            <form action="{{ route('backup.clean') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    {{ __('Clean Old Backups') }}
                </button>
            </form>
        </div>
    </div>

    @forelse ($statuses as $status)
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-4">
                <h3 class="text-lg font-semibold text-gray-900">{{ $status['disk'] }} / {{ $status['name'] }}</h3>
                @if ($status['reachable'])
                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">{{ __('Reachable') }}</span>
                @else
                    <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">{{ __('Unreachable') }}</span>
                @endif
                <span class="text-sm text-gray-500">{{ __(':size used', ['size' => number_format($status['used_storage'] / 1024 / 1024, 2) . ' MB']) }}</span>
            </div>

            @if ($status['backups']->isEmpty())
                <div class="rounded-lg border border-dashed border-gray-300 p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7M4 7a2 2 0 012-2h1m9 2h1a2 2 0 012 2M9 11h6m-6 4h3"/>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">{{ __('No backups yet.') }}</p>
                    <p class="text-xs text-gray-400">{{ __('Run your first backup to see it here.') }}</p>
                </div>
            @else
                <div class="overflow-hidden rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('File') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Date') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Size') }}</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach ($status['backups'] as $backup)
                                <tr class="hover:bg-gray-50">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                        {{ $backup['filename'] }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        {{ $backup['date']->format('M j, Y g:i A') }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        {{ number_format($backup['size'] / 1024 / 1024, 2) }} MB
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('backup.download', $backup['filename']) }}"
                                               class="rounded bg-white px-3 py-1.5 text-sm font-medium text-blue-600 hover:text-blue-800 border border-blue-200 hover:border-blue-300">
                                                {{ __('Download') }}
                                            </a>
                                            <form action="{{ route('backup.destroy', $backup['filename']) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('{{ __('Delete this backup?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="rounded bg-white px-3 py-1.5 text-sm font-medium text-red-600 hover:text-red-800 border border-red-200 hover:border-red-300">
                                                    {{ __('Delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @empty
        <div class="rounded-lg border border-dashed border-gray-300 p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <p class="mt-2 text-sm text-gray-500">{{ __('No backup destinations configured.') }}</p>
            <p class="text-xs text-gray-400">{{ __('Publish and configure config/backup.php to get started.') }}</p>
        </div>
    @endforelse
@endsection
