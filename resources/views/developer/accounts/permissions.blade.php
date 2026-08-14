@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => $activeRoute])

@section('content')
<div class="w-full px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Role & Permission Mapping</h1>
        <a href="{{ route('developer.accounts.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-arrow-left mr-2"></i> Back to Accounts
        </a>
    </div>

    <p class="text-sm text-gray-500 mb-4">
        This matrix reflects the access restrictions enforced by the app's role-based route middleware.
        It is visible to Admins only.
    </p>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Module</th>
                    @foreach($roles as $role)
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">{{ ucfirst($role) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($modules as $module => $allowedRoles)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $module }}</td>
                    @foreach($roles as $role)
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if(in_array($role, $allowedRoles))
                                <i class="fas fa-check-circle text-green-600"></i>
                            @else
                                <i class="fas fa-times-circle text-gray-300"></i>
                            @endif
                        </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
