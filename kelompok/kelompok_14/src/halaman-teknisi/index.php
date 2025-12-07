<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Teknisi - FixTrack</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      background: #f8f9fa;
      color: #1e293b;
    }
    .card-shadow {
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
  </style>
</head>

<body>

<!-- Header -->
<header class="sticky top-0 z-40 w-full bg-white shadow-sm border-b border-gray-200 px-6 py-4">
  <div class="flex justify-between items-center">
    <div class="flex items-center gap-3">
      <div class="bg-blue-600 text-white p-2 rounded-lg">
        <i class="fas fa-tools"></i>
      </div>
      <div>
        <h1 class="text-xl font-bold text-gray-800">FixTrack <span class="text-blue-600 font-normal">Teknisi</span></h1>
      </div>
    </div>

    <div class="flex items-center gap-4">
      <span class="text-gray-600 text-sm">Halo, Teknisi</span>

      <!-- tombol profil -->
      <a href="../profile/profile.php" class="text-blue-600 hover:text-blue-700 flex items-center gap-1 text-sm font-medium">
        <i class="fas fa-user"></i> Profil
      </a>

      <!-- tombol logout -->
      <a href="../login.php" class="text-red-600 hover:text-red-700 flex items-center gap-1 text-sm font-medium">
        <i class="fas fa-sign-out-alt"></i> Logout
      </a>
    </div>
  </div>
</header>


  <!-- Main Content -->
  <main class="p-6 space-y-6">
    <!-- Page Title -->
    <div class="flex justify-between items-center">
      <div>
        <h2 class="text-3xl font-bold text-gray-800">Dashboard</h2>
        <p class="text-gray-500 text-sm mt-1">Ringkasan aktivitas bengkel hari ini</p>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <!-- Card 1 -->
      <div class="bg-white rounded-xl p-5 card-shadow border border-gray-100">
        <div class="flex items-start justify-between">
          <div>
            <p class="text-gray-500 text-sm mb-2">Antrian Baru</p>
            <p class="text-3xl font-bold text-gray-800">0</p>
            <p class="text-xs text-gray-400 mt-1">Perlu Diproses</p>
          </div>
          <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
          </div>
        </div>
      </div>
      
      <!-- Card 2 -->
      <div class="bg-white rounded-xl p-5 card-shadow border border-gray-100">
        <div class="flex items-start justify-between">
          <div>
            <p class="text-gray-500 text-sm mb-2">Proses</p>
            <p class="text-3xl font-bold text-gray-800">0</p>
            <p class="text-xs text-gray-400 mt-1">Sedang Dikerjakan</p>
          </div>
          <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Card 3 -->
      <div class="bg-white rounded-xl p-5 card-shadow border border-gray-100">
        <div class="flex items-start justify-between">
          <div>
            <p class="text-gray-500 text-sm mb-2">Selesai</p>
            <p class="text-3xl font-bold text-gray-800">0</p>
            <p class="text-xs text-gray-400 mt-1">Siap Diambil</p>
          </div>
          <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Card 4 -->
      <div class="bg-white rounded-xl p-5 card-shadow border border-gray-100">
        <div class="flex items-start justify-between">
          <div>
            <p class="text-gray-500 text-sm mb-2">Omset</p>
            <p class="text-3xl font-bold text-gray-800">Rp 0</p>
            <p class="text-xs text-gray-400 mt-1">Bulan Ini</p>
          </div>
          <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Servis Terbaru Section -->
    <div class="bg-white rounded-xl card-shadow border border-gray-100">
      <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-xl font-bold text-gray-800">Servis Terbaru</h3>
        <a href="data_service.html" class="text-blue-600 hover:text-blue-700 text-sm font-medium">Lihat Semua</a>
      </div>

      <!-- Search & Filter -->
      <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex flex-wrap gap-3">
          <input 
            type="text" 
            id="searchInput"
            placeholder="Cari nama pelanggan..."
            class="px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm"
          />
          <select id="statusFilter" class="px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm">
            <option value="">Semua Status</option>
            <option value="1">Diterima admin</option>
            <option value="2">Dikerjakan oleh teknisi</option>
            <option value="3">Selesai dikerjakan</option>
            <option value="4">Barang sudah dapat diambil</option>
          </select>
          <button onclick="filterData()" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
            Cari
          </button>
        </div>
      </div>

      <!-- Service Table -->
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">NO. RESI</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Pelanggan</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Barang</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200" id="serviceTableBody">
            <tr id="row-1">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#001</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">Rudi</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">TV LED</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <select class="px-3 py-1 border border-gray-300 rounded-lg text-sm appearance-none bg-white status-select" data-id="1" onchange="updateStatus(1, this.value)">
                  <option value="1" selected>Diterima admin</option>
                  <option value="2">Dikerjakan oleh teknisi</option>
                  <option value="3">Selesai dikerjakan</option>
                  <option value="4">Barang sudah dapat diambil</option>
                </select>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 total-cost">Rp 0</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                <button type="button" onclick="openDiagnosaModal(1, 'Rudi', 'TV LED')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded-lg text-sm font-medium transition">
                  Diagnosa
                </button>
              </td>
            </tr>
            <tr id="row-2">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#002</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">Budi</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">Kulkas</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <select class="px-3 py-1 border border-gray-300 rounded-lg text-sm appearance-none bg-white status-select" data-id="2" onchange="updateStatus(2, this.value)">
                  <option value="1">Diterima admin</option>
                  <option value="2" selected>Dikerjakan oleh teknisi</option>
                  <option value="3">Selesai dikerjakan</option>
                  <option value="4">Barang sudah dapat diambil</option>
                </select>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 total-cost">Rp 0</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                <button type="button" onclick="openDiagnosaModal(2, 'Budi', 'Kulkas')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded-lg text-sm font-medium transition">
                  Diagnosa
                </button>
              </td>
            </tr>
            <tr id="row-3">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#003</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">Agus</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">HP Samsung</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <select class="px-3 py-1 border border-gray-300 rounded-lg text-sm appearance-none bg-white status-select" data-id="3" onchange="updateStatus(3, this.value)">
                  <option value="1">Diterima admin</option>
                  <option value="2">Dikerjakan oleh teknisi</option>
                  <option value="3" selected>Selesai dikerjakan</option>
                  <option value="4">Barang sudah dapat diambil</option>
                </select>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 total-cost">Rp 0</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                <button type="button" onclick="openDiagnosaModal(3, 'Agus', 'HP Samsung')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded-lg text-sm font-medium transition">
                  Diagnosa
                </button>
              </td>
            </tr>
            <tr id="row-4">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#004</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">Siti</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">Mesin Cuci</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <select class="px-3 py-1 border border-gray-300 rounded-lg text-sm appearance-none bg-white status-select" data-id="4" onchange="updateStatus(4, this.value)">
                  <option value="1">Diterima admin</option>
                  <option value="2">Dikerjakan oleh teknisi</option>
                  <option value="3">Selesai dikerjakan</option>
                  <option value="4" selected>Barang sudah dapat diambil</option>
                </select>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 total-cost">Rp 0</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                <button type="button" onclick="openDiagnosaModal(4, 'Siti', 'Mesin Cuci')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded-lg text-sm font-medium transition">
                  Diagnosa
                </button>
              </td>
            </tr>
          </tbody>
        </table>
        <div id="emptyState" class="hidden px-6 py-12 text-center">
          <p class="text-gray-400 text-sm">Belum ada data servis.</p>
        </div>
      </div>
    </div>

    <!-- Modal Diagnosa -->
    <div id="diagnosaModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl max-h-screen overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center sticky top-0 bg-white rounded-t-xl">
          <h2 class="text-xl font-bold text-gray-800">
            Diagnosa Service: <span id="diagnosaCustomerName"></span> (<span id="diagnosaItemName"></span>)
          </h2>
          <button type="button" onclick="closeDiagnosaModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>

        <div class="p-6 space-y-6">
          <!-- Deskripsi Diagnosa -->
          <div class="space-y-4">
            <h3 class="text-lg font-bold text-gray-800">Deskripsi Diagnosa</h3>
            <div>
              <label for="diagnosisDesc" class="block text-sm font-medium text-gray-700 mb-2">
                Penjelasan Diagnosa Akhir
              </label>
              <textarea 
                id="diagnosisDesc" 
                placeholder="Masukkan penjelasan diagnosa..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none h-24 resize-none text-sm"
              ></textarea>
            </div>
            <div>
              <label for="additionalDetails" class="block text-sm font-medium text-gray-700 mb-2">
                Detail Tambahan / Catatan Teknisi
              </label>
              <textarea 
                id="additionalDetails" 
                placeholder="Detail tambahan atau catatan penting lainnya..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none h-20 resize-none text-sm"
              ></textarea>
            </div>
          </div>

          <!-- Komponen & Biaya Jasa -->
          <div class="space-y-4">
            <h3 class="text-lg font-bold text-gray-800">Komponen & Biaya Jasa</h3>
            <div id="componentList" class="space-y-3"></div>
            <button type="button" onclick="addComponentField()" class="flex items-center text-sm font-medium text-blue-600 hover:text-blue-700 transition">
              <span class="text-xl mr-1">+</span> Tambah Komponen
            </button>
            <div>
              <label for="laborCost" class="block text-sm font-medium text-gray-700 mb-2">Harga Jasa (Rp)</label>
              <input 
                type="text" 
                id="laborCost" 
                placeholder="Masukkan harga jasa..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm"
                oninput="handleCostInput(event); calculateTotal()"
              />
            </div>
          </div>

          <!-- Total Biaya -->
          <div class="bg-blue-600 p-4 rounded-lg text-white flex justify-between items-center">
            <div>
              <label class="block text-sm font-medium mb-1">Total Biaya Keseluruhan</label>
              <p class="text-3xl font-bold" id="totalDisplay">Rp 0</p>
            </div>
            <button type="button" onclick="showStruk()" class="bg-white text-blue-600 px-4 py-2 rounded-lg hover:bg-gray-100 transition font-medium text-sm">
              Lihat Struk
            </button>
          </div>

          <!-- Action Buttons -->
          <div class="flex gap-3 pt-4">
            <button 
              type="button"
              onclick="saveDetails()" 
              class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-lg transition font-medium"
            >
              Simpan & Perbarui
            </button>
            <button 
              type="button"
              onclick="closeDiagnosaModal()" 
              class="flex-1 bg-gray-400 hover:bg-gray-500 text-white px-4 py-2.5 rounded-lg transition font-medium"
            >
              Tutup
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Struk -->
    <div id="strukModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl shadow-2xl w-full max-w-md max-h-screen overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center sticky top-0 bg-white rounded-t-xl">
          <h2 class="text-xl font-bold text-gray-800">Struk Biaya Service</h2>
          <button type="button" onclick="closeStrukModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        <div class="p-6 space-y-4" id="strukContent"></div>
        <div class="px-6 py-4 border-t border-gray-200">
          <button type="button" onclick="printStruk()" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg transition font-medium">
            Cetak Struk
          </button>
        </div>
      </div>
    </div>
  </main>

  <script src="js/app.js"></script>
</body>
</html>
