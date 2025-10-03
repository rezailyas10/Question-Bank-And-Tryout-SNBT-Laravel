<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IRT Analysis - 1PL Model</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .content {
            padding: 30px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border: 1px solid #e2e8f0;
        }
        
        .stat-card h3 {
            color: #334155;
            margin-bottom: 15px;
            font-size: 1.3rem;
        }
        
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        
        .stat-item {
            background: white;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .stat-label {
            font-size: 0.9rem;
            color: #64748b;
            margin-bottom: 5px;
        }
        
        .stat-value {
            font-size: 1.4rem;
            font-weight: 700;
            color: #1e293b;
        }
        
        .section {
            margin-bottom: 40px;
        }
        
        .section-title {
            font-size: 1.8rem;
            color: #1e293b;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #4f46e5;
            display: inline-block;
        }
        
        .tabs {
            display: flex;
            background: #f1f5f9;
            border-radius: 10px;
            padding: 5px;
            margin-bottom: 20px;
        }
        
        .tab-button {
            flex: 1;
            padding: 12px 20px;
            background: transparent;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            color: #64748b;
        }
        
        .tab-button.active {
            background: white;
            color: #4f46e5;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .table-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border: 1px solid #e2e8f0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 15px 12px;
            text-align: center;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #f1f5f9;
            color: #374151;
        }
        
        tr:nth-child(even) {
            background: #f8fafc;
        }
        
        tr:hover {
            background: #e0e7ff;
            transition: background 0.2s ease;
        }
        
        .response-cell {
            font-family: 'Courier New', monospace;
            font-size: 0.85rem;
            white-space: nowrap;
        }
        
        .correct {
            color: #059669;
            font-weight: bold;
        }
        
        .incorrect {
            color: #dc2626;
        }
        
        .score-high {
            color: #059669;
            font-weight: bold;
        }
        
        .score-medium {
            color: #d97706;
            font-weight: bold;
        }
        
        .score-low {
            color: #dc2626;
            font-weight: bold;
        }
        
        .chart-container {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border: 1px solid #e2e8f0;
            margin-bottom: 20px;
        }
        
        .item-difficulty-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .difficulty-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            border: 1px solid #e2e8f0;
            text-align: center;
        }
        
        .difficulty-card h4 {
            color: #4f46e5;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }
        
        .difficulty-value {
            font-size: 1.3rem;
            font-weight: bold;
            color: #1e293b;
            margin: 5px 0;
        }
        
        .proportion-value {
            font-size: 0.9rem;
            color: #64748b;
        }
        
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .stat-grid {
                grid-template-columns: 1fr;
            }
            
            .tabs {
                flex-direction: column;
            }
            
            table {
                font-size: 0.8rem;
            }
            
            th, td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 IRT Analysis Dashboard</h1>
            <p>Item Response Theory - 1 Parameter Logistic Model</p>
        </div>
        
        <div class="content">
            <!-- Summary Statistics -->
            <div class="section">
                <h2 class="section-title">📈 Summary Statistics</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>🎯 Theta (Ability) Statistics</h3>
                        <div class="stat-grid">
                            <div class="stat-item">
                                <div class="stat-label">Mean</div>
                                <div class="stat-value">{{ $irtResults['summary_stats']['theta']['mean'] }}</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">Std Dev</div>
                                <div class="stat-value">{{ $irtResults['summary_stats']['theta']['std'] }}</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">Minimum</div>
                                <div class="stat-value">{{ $irtResults['summary_stats']['theta']['min'] }}</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">Maximum</div>
                                <div class="stat-value">{{ $irtResults['summary_stats']['theta']['max'] }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <h3>📊 CEEB Score Statistics</h3>
                        <div class="stat-grid">
                            <div class="stat-item">
                                <div class="stat-label">Mean</div>
                                <div class="stat-value">{{ $irtResults['summary_stats']['ceeb']['mean'] }}</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">Std Dev</div>
                                <div class="stat-value">{{ $irtResults['summary_stats']['ceeb']['std'] }}</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">Minimum</div>
                                <div class="stat-value">{{ $irtResults['summary_stats']['ceeb']['min'] }}</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">Maximum</div>
                                <div class="stat-value">{{ $irtResults['summary_stats']['ceeb']['max'] }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <h3>✅ Raw Score Statistics</h3>
                        <div class="stat-grid">
                            <div class="stat-item">
                                <div class="stat-label">Mean</div>
                                <div class="stat-value">{{ $irtResults['summary_stats']['raw_score']['mean'] }}</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">Std Dev</div>
                                <div class="stat-value">{{ $irtResults['summary_stats']['raw_score']['std'] }}</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">Minimum</div>
                                <div class="stat-value">{{ $irtResults['summary_stats']['raw_score']['min'] }}</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">Maximum</div>
                                <div class="stat-value">{{ $irtResults['summary_stats']['raw_score']['max'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Item Difficulties -->
            <div class="section">
                <h2 class="section-title">🔧 Item Difficulty Parameters</h2>
                <div class="item-difficulty-grid">
                    @foreach($irtResults['item_difficulties'] as $item => $difficulty)
                    <div class="difficulty-card">
                        <h4>{{ $item }}</h4>
                        <div class="difficulty-value">{{ round($difficulty['difficulty'], 3) }}</div>
                        <div class="proportion-value">P-correct: {{ round($difficulty['proportion_correct'], 3) }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Data Tables -->
            <div class="section">
                <h2 class="section-title">📋 Analysis Results</h2>
                
                <div class="tabs">
                    <button class="tab-button active" onclick="showTab('results')">🎯 IRT Results</button>
                    <button class="tab-button" onclick="showTab('responses')">📝 Response Data</button>
                </div>
                
                <div id="results" class="tab-content active">
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>User Name</th>
                                    <th>Raw Score</th>
                                    <th>Theta (θ)</th>
                                    <th>Z1 Score</th>
                                    <th>CEEB Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($irtResults['person_results'] as $result)
                                <tr>
                                    <td>{{ $result['user_id'] }}</td>
                                    <td>{{ $result['user_name'] }}</td>
                                    <td>{{ $result['raw_score'] }}/15</td>
                                    <td>{{ $result['theta'] }}</td>
                                    <td>{{ $result['z1'] }}</td>
                                    <td class="{{ $result['ceeb_score'] >= 550 ? 'score-high' : ($result['ceeb_score'] >= 450 ? 'score-medium' : 'score-low') }}">
                                        {{ $result['ceeb_score'] }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div id="responses" class="tab-content">
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>User</th>
                                    @foreach($sampleData['items'] as $item)
                                    <th>{{ $item }}</th>
                                    @endforeach
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($irtResults['person_results'] as $result)
                                <tr>
                                    <td><strong>{{ $result['user_name'] }}</strong></td>
                                    @foreach($result['responses'] as $item => $response)
                                    <td class="response-cell {{ $response == 1 ? 'correct' : 'incorrect' }}">
                                        {{ $response }}
                                    </td>
                                    @endforeach
                                    <td><strong>{{ $result['raw_score'] }}</strong></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Charts -->
            <div class="section">
                <h2 class="section-title">📈 Visualization</h2>
                <div class="chart-container">
                    <canvas id="abilityChart" width="400" height="200"></canvas>
                </div>
                <div class="chart-container">
                    <canvas id="ceebChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabId) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Remove active class from all buttons
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active');
            });
            
            // Show selected tab and activate button
            document.getElementById(tabId).classList.add('active');
            event.target.classList.add('active');
        }
        
        // Chart data
        const results = @json($irtResults['person_results']);
        const thetaValues = results.map(r => r.theta);
        const ceebScores = results.map(r => r.ceeb_score);
        const userNames = results.map(r => r.user_name);
        
        // Theta distribution chart
        const abilityCtx = document.getElementById('abilityChart').getContext('2d');
        new Chart(abilityCtx, {
            type: 'line',
            data: {
                labels: userNames,
                datasets: [{
                    label: 'Theta (Ability)',
                    data: thetaValues,
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Person Ability Distribution (Theta)',
                        font: { size: 16, weight: 'bold' }
                    }
                },
                scales: {
                    x: { display: false },
                    y: {
                        title: {
                            display: true,
                            text: 'Theta Value'
                        }
                    }
                }
            }
        });
        
        // CEEB score distribution chart
        const ceebCtx = document.getElementById('ceebChart').getContext('2d');
        new Chart(ceebCtx, {
            type: 'bar',
            data: {
                labels: userNames,
                datasets: [{
                    label: 'CEEB Score',
                    data: ceebScores,
                    backgroundColor: ceebScores.map(score => 
                        score >= 550 ? '#059669' : 
                        score >= 450 ? '#d97706' : '#dc2626'
                    ),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'CEEB Score Distribution (500 + 100 × Z)',
                        font: { size: 16, weight: 'bold' }
                    }
                },
                scales: {
                    x: { display: false },
                    y: {
                        title: {
                            display: true,
                            text: 'CEEB Score'
                        },
                        min: 200,
                        max: 800
                    }
                }
            }
        });
    </script>
</body>
</html>