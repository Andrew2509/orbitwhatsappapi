@extends('layouts.admin')

@section('title', 'Plan Management')
@section('page-title', 'Plan Management')
@section('page-subtitle', 'Kelola paket langganan')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div></div>
        <a href="{{ route('admin.plans.create') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Plan
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($plans as $plan)
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden hover:shadow-md transition-shadow {{ $plan->is_featured ? 'ring-2 ring-emerald-500' : '' }}">
            @if($plan->is_featured)
            <div class="bg-emerald-500 text-white text-center text-sm py-1 font-medium">Most Popular</div>
            @endif
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold">{{ $plan->name }}</h3>
                    <span class="admin-badge {{ $plan->is_active ? 'admin-badge-success' : 'admin-badge-danger' }}">
                        {{ $plan->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <p class="text-2xl font-bold mb-4">{{ $plan->formatted_price }}<span class="text-sm font-normal text-slate-500">/bulan</span></p>
                <p class="text-sm text-slate-500 mb-4">{{ $plan->description }}</p>
                
                <div class="space-y-2 mb-6">
                    <div class="flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ $plan->max_devices == -1 ? 'Unlimited' : $plan->max_devices }} Device</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ $plan->max_messages_per_day == -1 ? 'Unlimited' : number_format($plan->max_messages_per_day) }} Pesan/hari</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ $plan->max_contacts == -1 ? 'Unlimited' : number_format($plan->max_contacts) }} Kontak</span>
                    </div>
                </div>

                <div class="border-t border-gray-200 dark:border-slate-700 pt-4">
                    <p class="text-sm text-slate-500 mb-2">{{ $plan->subscriptions_count }} subscribers</p>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.plans.edit', $plan) }}" class="flex-1 px-3 py-2 text-center text-sm bg-slate-100 dark:bg-slate-700 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                            Edit
                        </a>
                        @if($plan->subscriptions_count == 0)
                        <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Hapus plan ini?')" class="w-full px-3 py-2 text-center text-sm bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition">
                                Delete
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

