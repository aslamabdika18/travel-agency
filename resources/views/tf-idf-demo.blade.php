<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo TF-IDF Content-Based Filtering - Travel Agency</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .toast {
            transform: translateX(100%);
            transition: transform 0.3s ease-in-out;
        }
        .toast.show {
            transform: translateX(0);
        }
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .loading {
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <!-- Header -->
    <header class="gradient-bg text-white py-16">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold mb-4">
                    <i class="fas fa-brain mr-3"></i>
                    Demo TF-IDF
                </h1>
                <p class="text-xl md:text-2xl opacity-90 mb-8">
                    Content-Based Filtering untuk Rekomendasi Paket Perjalanan
                </p>
                <div class="bg-white/20 backdrop-blur-sm rounded-lg p-6 max-w-2xl mx-auto">
                    <p class="text-lg">
                        Algoritma TF-IDF (Term Frequency-Inverse Document Frequency) digunakan untuk menganalisis kesamaan konten dan memberikan rekomendasi paket perjalanan yang relevan.
                    </p>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-12">
        <!-- Package Selection -->
        <section class="mb-12">
            <div class="bg-white rounded-xl shadow-lg p-8 card-hover">
                <h2 class="text-3xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-map-marked-alt text-blue-600 mr-3"></i>
                    Pilih Paket Perjalanan
                </h2>
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Paket Referensi untuk Analisis
                        </label>
                        <select id="packageSelect" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Pilih paket perjalanan...</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button id="analyzeBtn" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 px-6 rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-search mr-2"></i>
                            Analisis TF-IDF
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Loading State -->
        <div id="loadingState" class="hidden">
            <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                <div class="loading w-16 h-16 border-4 border-blue-600 border-t-transparent rounded-full mx-auto mb-4"></div>
                <p class="text-lg text-gray-600">Menganalisis data dengan algoritma TF-IDF...</p>
            </div>
        </div>

        <!-- Results Section -->
        <div id="resultsSection" class="hidden space-y-8">
            <!-- Selected Package Info -->
            <section class="bg-white rounded-xl shadow-lg p-8 card-hover fade-in">
                <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-info-circle text-green-600 mr-3"></i>
                    Paket Terpilih
                </h3>
                <div id="selectedPackageInfo" class="grid md:grid-cols-2 gap-6">
                    <!-- Will be populated by JavaScript -->
                </div>
            </section>

            <!-- TF-IDF Analysis -->
            <section class="bg-white rounded-xl shadow-lg p-8 card-hover fade-in">
                <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-chart-bar text-purple-600 mr-3"></i>
                    Analisis TF-IDF
                </h3>
                
                <!-- TF-IDF Table -->
                <div class="mb-8">
                    <h4 class="text-lg font-semibold mb-4">Tabel Perhitungan TF, IDF, dan TF-IDF</h4>
                    <div class="overflow-x-auto">
                        <table id="tfidfTable" class="w-full border-collapse border border-gray-300">
                            <!-- Will be populated by JavaScript -->
                        </table>
                    </div>
                </div>
                
                <div class="grid lg:grid-cols-2 gap-8">
                    <div>
                        <h4 class="text-lg font-semibold mb-4">Term Frequency (TF)</h4>
                        <canvas id="tfChart" width="400" height="300"></canvas>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold mb-4">TF-IDF Scores</h4>
                        <canvas id="tfidfChart" width="400" height="300"></canvas>
                    </div>
                </div>
            </section>

            <!-- Recommendations -->
            <section class="bg-white rounded-xl shadow-lg p-8 card-hover fade-in">
                <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-star text-yellow-500 mr-3"></i>
                    Rekomendasi Paket Serupa
                </h3>
                <div id="recommendationsGrid" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Will be populated by JavaScript -->
                </div>
            </section>

            <!-- Similarity Matrix -->
            <section class="bg-white rounded-xl shadow-lg p-8 card-hover fade-in">
                <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-table text-indigo-600 mr-3"></i>
                    Matriks Kesamaan
                </h3>
                <div class="overflow-x-auto">
                    <table id="similarityMatrix" class="w-full text-sm">
                        <!-- Will be populated by JavaScript -->
                    </table>
                </div>
            </section>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-16">
        <div class="container mx-auto px-4 text-center">
            <p class="text-lg">
                <i class="fas fa-code mr-2"></i>
                Demo TF-IDF Content-Based Filtering - Travel Agency
            </p>
            <p class="text-gray-400 mt-2">
                Menggunakan algoritma machine learning untuk rekomendasi yang lebih akurat
            </p>
        </div>
    </footer>

    <script>
        // Global variables
        let packages = [];
        let selectedPackage = null;
        let tfChart = null;
        let tfidfChart = null;

        // Toast notification system
        function showToast(message, type = 'info') {
            const toastContainer = document.getElementById('toast-container');
            const toast = document.createElement('div');
            
            const bgColor = {
                'success': 'bg-green-500',
                'error': 'bg-red-500',
                'warning': 'bg-yellow-500',
                'info': 'bg-blue-500'
            }[type] || 'bg-blue-500';
            
            const icon = {
                'success': 'fas fa-check-circle',
                'error': 'fas fa-exclamation-circle',
                'warning': 'fas fa-exclamation-triangle',
                'info': 'fas fa-info-circle'
            }[type] || 'fas fa-info-circle';
            
            toast.className = `toast ${bgColor} text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3 min-w-80`;
            toast.innerHTML = `
                <i class="${icon}"></i>
                <span class="flex-1">${message}</span>
                <button onclick="this.parentElement.remove()" class="text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            toastContainer.appendChild(toast);
            
            // Show toast
            setTimeout(() => toast.classList.add('show'), 100);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }

        // Load packages data
        async function loadPackages() {
            try {
                const response = await fetch('/api/tf-idf-demo/packages');
                if (!response.ok) throw new Error('Failed to load packages');
                
                const data = await response.json();
                packages = data.packages;
                
                populatePackageSelect();
                showToast('Data paket berhasil dimuat!', 'success');
            } catch (error) {
                console.error('Error loading packages:', error);
                showToast('Gagal memuat data paket', 'error');
            }
        }

        // Populate package select dropdown
        function populatePackageSelect() {
            const select = document.getElementById('packageSelect');
            select.innerHTML = '<option value="">Pilih paket perjalanan...</option>';
            
            packages.forEach(pkg => {
                const option = document.createElement('option');
                option.value = pkg.id;
                option.textContent = `${pkg.name} (${pkg.category}) - Rp ${pkg.price.toLocaleString()}`;
                select.appendChild(option);
            });
        }

        // Analyze TF-IDF
        async function analyzeTfIdf() {
            const packageId = document.getElementById('packageSelect').value;
            if (!packageId) {
                showToast('Silakan pilih paket terlebih dahulu', 'warning');
                return;
            }

            selectedPackage = packages.find(p => p.id == packageId);
            
            // Show loading
            document.getElementById('loadingState').classList.remove('hidden');
            document.getElementById('resultsSection').classList.add('hidden');
            
            try {
                const response = await fetch(`/api/tf-idf-demo/analyze/${packageId}`);
                if (!response.ok) throw new Error('Analysis failed');
                
                const data = await response.json();
                
                // Hide loading
                document.getElementById('loadingState').classList.add('hidden');
                
                // Show results
                displayResults(data);
                document.getElementById('resultsSection').classList.remove('hidden');
                
                showToast('Analisis TF-IDF berhasil!', 'success');
                
                // Smooth scroll to results
                document.getElementById('resultsSection').scrollIntoView({ 
                    behavior: 'smooth' 
                });
                
            } catch (error) {
                console.error('Error analyzing:', error);
                document.getElementById('loadingState').classList.add('hidden');
                showToast('Gagal melakukan analisis', 'error');
            }
        }

        // Display analysis results
        function displayResults(data) {
            displaySelectedPackageInfo();
            displayTfIdfTable(data.tfidf_analysis);
            displayTfIdfCharts(data.tfidf_analysis);
            displayRecommendations(data.recommendations);
            displaySimilarityMatrix(data.similarity_matrix);
        }

        // Display selected package info
        function displaySelectedPackageInfo() {
            const container = document.getElementById('selectedPackageInfo');
            container.innerHTML = `
                <div class="bg-gradient-to-r from-blue-50 to-purple-50 p-6 rounded-lg">
                    <h4 class="font-semibold text-lg mb-2">${selectedPackage.name}</h4>
                    <p class="text-gray-600 mb-2"><i class="fas fa-tag mr-2"></i>Kategori: ${selectedPackage.category}</p>
                    <p class="text-gray-600 mb-2"><i class="fas fa-money-bill-wave mr-2"></i>Harga: Rp ${selectedPackage.price.toLocaleString()}</p>
                    <p class="text-gray-600"><i class="fas fa-clock mr-2"></i>Durasi: ${selectedPackage.duration} hari</p>
                </div>
                <div class="bg-gradient-to-r from-green-50 to-blue-50 p-6 rounded-lg">
                    <h4 class="font-semibold text-lg mb-2">Informasi Analisis</h4>
                    <p class="text-gray-600 mb-2"><i class="fas fa-search mr-2"></i>Algoritma: TF-IDF</p>
                    <p class="text-gray-600 mb-2"><i class="fas fa-database mr-2"></i>Dataset: ${packages.length} paket</p>
                    <p class="text-gray-600"><i class="fas fa-star mr-2"></i>Rekomendasi: Top 5</p>
                </div>
            `;
        }

        // Display TF-IDF table
        function displayTfIdfTable(analysis) {
            const table = document.getElementById('tfidfTable');
            
            if (!analysis || !analysis.tfidfTableData || !analysis.packageNames) {
                table.innerHTML = '<tr><td class="px-4 py-2 text-center text-gray-500">Data tidak tersedia</td></tr>';
                return;
            }

            const packageNames = analysis.packageNames;
            const tableData = analysis.tfidfTableData;

            // Build header row
            let html = '<thead class="bg-gray-100"><tr>';
            html += '<th class="border border-gray-300 px-4 py-2 font-semibold text-left">Kata Kunci</th>';
            
            // TF columns for each package
            packageNames.forEach((name, index) => {
                html += `<th class="border border-gray-300 px-4 py-2 font-semibold text-center">TF<br>Paket ${String.fromCharCode(65 + index)}</th>`;
            });
            
            html += '<th class="border border-gray-300 px-4 py-2 font-semibold text-center">IDF</th>';
            
            // TF-IDF columns for each package
            packageNames.forEach((name, index) => {
                html += `<th class="border border-gray-300 px-4 py-2 font-semibold text-center">TF-IDF ${String.fromCharCode(65 + index)}</th>`;
            });
            
            html += '</tr></thead><tbody>';

            // Build data rows
            tableData.forEach(row => {
                html += '<tr class="hover:bg-gray-50">';
                html += `<td class="border border-gray-300 px-4 py-2 font-medium">${row.term}</td>`;
                
                // TF values for each package
                packageNames.forEach((name, index) => {
                    const tfValue = row['tf_package_' + String.fromCharCode(65 + index)] || 0;
                    html += `<td class="border border-gray-300 px-4 py-2 text-center">${tfValue}</td>`;
                });
                
                // IDF value
                html += `<td class="border border-gray-300 px-4 py-2 text-center font-semibold text-blue-600">${row.idf}</td>`;
                
                // TF-IDF values for each package
                packageNames.forEach((name, index) => {
                    const tfidfValue = row['tfidf_package_' + String.fromCharCode(65 + index)] || 0;
                    html += `<td class="border border-gray-300 px-4 py-2 text-center font-semibold text-purple-600">${tfidfValue}</td>`;
                });
                
                html += '</tr>';
            });
            
            html += '</tbody>';
            table.innerHTML = html;
        }

        // Display TF-IDF charts
        function displayTfIdfCharts(analysis) {
            // Destroy existing charts
            if (tfChart) tfChart.destroy();
            if (tfidfChart) tfidfChart.destroy();

            // Sample data for demonstration
            const terms = ['bali', 'pantai', 'gunung', 'budaya', 'adventure'];
            const tfScores = [0.8, 0.6, 0.4, 0.7, 0.5];
            const tfidfScores = [0.65, 0.45, 0.32, 0.58, 0.41];

            // TF Chart
            const tfCtx = document.getElementById('tfChart').getContext('2d');
            tfChart = new Chart(tfCtx, {
                type: 'bar',
                data: {
                    labels: terms,
                    datasets: [{
                        label: 'Term Frequency',
                        data: tfScores,
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 1
                        }
                    }
                }
            });

            // TF-IDF Chart
            const tfidfCtx = document.getElementById('tfidfChart').getContext('2d');
            tfidfChart = new Chart(tfidfCtx, {
                type: 'bar',
                data: {
                    labels: terms,
                    datasets: [{
                        label: 'TF-IDF Score',
                        data: tfidfScores,
                        backgroundColor: 'rgba(147, 51, 234, 0.8)',
                        borderColor: 'rgba(147, 51, 234, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 1
                        }
                    }
                }
            });
        }

        // Display recommendations
        function displayRecommendations(recommendations) {
            const container = document.getElementById('recommendationsGrid');
            container.innerHTML = '';

            recommendations.forEach((rec, index) => {
                const card = document.createElement('div');
                card.className = 'bg-gradient-to-br from-white to-gray-50 p-6 rounded-lg border border-gray-200 card-hover';
                card.innerHTML = `
                    <div class="flex items-center justify-between mb-4">
                        <span class="bg-gradient-to-r from-blue-500 to-purple-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                            #${index + 1}
                        </span>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-green-600">${(rec.similarity * 100).toFixed(1)}%</div>
                            <div class="text-xs text-gray-500">Similarity</div>
                        </div>
                    </div>
                    <h4 class="font-semibold text-lg mb-2">${rec.package.name}</h4>
                    <p class="text-gray-600 mb-2"><i class="fas fa-tag mr-2"></i>${rec.package.category}</p>
                    <p class="text-gray-600 mb-2"><i class="fas fa-money-bill-wave mr-2"></i>Rp ${rec.package.price.toLocaleString()}</p>
                    <p class="text-gray-600 mb-4"><i class="fas fa-clock mr-2"></i>${rec.package.duration} hari</p>
                    <div class="bg-gray-100 rounded p-3">
                        <div class="text-sm text-gray-600 mb-1">Faktor Kesamaan:</div>
                        <div class="text-xs space-y-1">
                            <div class="flex justify-between">
                                <span>Konten:</span>
                                <span class="font-semibold">${(rec.text_similarity * 100).toFixed(1)}%</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Kategori:</span>
                                <span class="font-semibold">${(rec.category_similarity * 100).toFixed(1)}%</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Harga:</span>
                                <span class="font-semibold">${(rec.price_similarity * 100).toFixed(1)}%</span>
                            </div>
                        </div>
                    </div>
                `;
                container.appendChild(card);
            });
        }

        // Display similarity matrix
        function displaySimilarityMatrix(matrix) {
            const table = document.getElementById('similarityMatrix');
            
            if (!matrix || !matrix.labels || !matrix.matrix) {
                table.innerHTML = '<tr><td class="px-4 py-2 text-center text-gray-500">Data matrix tidak tersedia</td></tr>';
                return;
            }

            const labels = matrix.labels;
            const matrixData = matrix.matrix;

            // Build header row
            let html = '<thead><tr>';
            html += '<th class="px-4 py-2 bg-gray-100 font-semibold text-left">Paket</th>';
            labels.forEach(label => {
                html += `<th class="px-4 py-2 bg-gray-100 font-semibold text-center">${label}</th>`;
            });
            html += '</tr></thead><tbody>';

            // Build data rows
            labels.forEach((rowLabel, i) => {
                html += '<tr class="border-t">';
                html += `<td class="px-4 py-2 font-semibold bg-gray-50">${rowLabel}</td>`;
                
                matrixData[i].forEach((cell, j) => {
                    const similarity = parseFloat(cell);
                    const isHighSimilarity = similarity > 0.7;
                    const isDiagonal = i === j;
                    
                    let cellClass = 'px-4 py-2 text-center';
                    if (isDiagonal) {
                        cellClass += ' bg-blue-100 text-blue-800 font-semibold';
                    } else if (isHighSimilarity) {
                        cellClass += ' bg-green-100 text-green-800 font-semibold';
                    }
                    
                    html += `<td class="${cellClass}">${similarity.toFixed(2)}</td>`;
                });
                html += '</tr>';
            });
            html += '</tbody>';
            
            table.innerHTML = html;
        }

        // Event listeners
        document.getElementById('analyzeBtn').addEventListener('click', analyzeTfIdf);

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadPackages();
            showToast('Selamat datang di Demo TF-IDF!', 'info');
        });
    </script>
</body>
</html>