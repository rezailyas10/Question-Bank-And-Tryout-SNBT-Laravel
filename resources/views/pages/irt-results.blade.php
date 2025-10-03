{{-- resources/views/irt/results.blade.php --}}
@extends('layouts.app')

@section('title', 'IRT Analysis Results')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-800">IRT Analysis Results</h1>
            <a href="{{ route('irt.index') }}" 
               class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Analysis
            </a>
        </div>

        {{-- Summary --}}
        <div class="grid md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <div class="text-3xl font-bold text-blue-600">{{ $sample_size }}</div>
                <div class="text-gray-600">Respondents</div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <div class="text-3xl font-bold text-green-600">{{ $num_items }}</div>
                <div class="text-gray-600">Items</div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <div class="text-3xl font-bold text-purple-600">
                    {{ number_format(array_sum(array_column($theta_scores, 'z1')) / count($theta_scores), 3) }}
                </div>
                <div class="text-gray-600">Mean θ</div>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-8">
            {{-- Theta Scores --}}
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">
                        <i class="fas fa-user-graduate mr-2"></i>
                        Theta Scores (Ability Estimates)
                    </h2>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto max-h-96">
                        <table class="min-w-full table-auto">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Respondent</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Theta (θ)</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">SE</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($theta_scores as $index => $score)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $index + 1 }}</td>
                                    <td class="px-4 py-2 text-sm font-mono text-gray-900">
                                        {{ number_format($score['z1'], 3) }}
                                    </td>
                                    <td class="px-4 py-2 text-sm font-mono text-gray-500">
                                        {{ number_format($score['se.z1'], 3) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                        <h4 class="font-semibold text-blue-800 mb-2">Theta Distribution</h4>
                        <div class="grid grid-cols-3 gap-4 text-sm text-blue-700">
                            <div>
                                <span class="font-medium">Min:</span> 
                                {{ number_format(min(array_column($theta_scores, 'z1')), 3) }}
                            </div>
                            <div>
                                <span class="font-medium">Max:</span> 
                                {{ number_format(max(array_column($theta_scores, 'z1')), 3) }}
                            </div>
                            <div>
                                <span class="font-medium">SD:</span> 
                                @php
                                    $values = array_column($theta_scores, 'z1');
                                    $mean = array_sum($values) / count($values);
                                    $variance = array_sum(array_map(function($x) use ($mean) { return pow($x - $mean, 2); }, $values)) / count($values);
                                    $sd = sqrt($variance);
                                @endphp
                                {{ number_format($sd, 3) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Person Fit Statistics --}}
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Person Fit Statistics
                    </h2>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto max-h-96">
                        <table class="min-w-full table-auto">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Respondent</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">L0</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Lz</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($person_fit as $index => $fit)
                                <tr class="hover:bg-gray-50 {{ isset($fit['Lz']) && abs($fit['Lz']) > 2 ? 'bg-red-50' : '' }}">
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $index + 1 }}</td>
                                    <td class="px-4 py-2 text-sm font-mono text-gray-900">
                                        {{ number_format($fit['L0'], 3) }}
                                    </td>
                                    <td class="px-4 py-2 text-sm font-mono text-gray-900">
                                        {{ number_format($fit['Lz'], 3) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4 p-4 bg-yellow-50 rounded-lg">
                        <h4 class="font-semibold text-yellow-800 mb-2">Person Fit Interpretation</h4>
                        <div class="text-sm text-yellow-700">
                            <p><strong>L0:</strong> Person fit statistic (lower values = better fit)</p>
                            <p><strong>Lz:</strong> Standardized person fit (|Lz| > 2 may indicate misfit)</p>
                            <p class="mt-2 text-red-600">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Red highlighted rows indicate potential person misfit (|Lz| > 2)
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Raw Data Preview --}}
        <div class="mt-8 bg-white rounded-lg shadow-md">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">
                    <i class="fas fa-table mr-2"></i>
                    Raw Response Data (First 10 Respondents)
                </h2>
                @if(isset($source))
                    <p class="text-sm text-gray-600 mt-1">Source: {{ $source }}</p>
                @endif
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Respondent</th>
                                @for($i = 1; $i <= $num_items; $i++)
                                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Item {{ $i }}</th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach(array_slice($raw_data, 0, 10) as $index => $responses)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 text-sm text-gray-900">{{ $index + 1 }}</td>
                                @foreach($responses as $response)
                                    <td class="px-4 py-2 text-center text-sm font-mono {{ $response == 1 ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50' }}">
                                        {{ $response }}
                                    </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if(count($raw_data) > 10)
                    <p class="text-sm text-gray-500 mt-4 text-center">
                        Showing first 10 of {{ count($raw_data) }} respondents
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection