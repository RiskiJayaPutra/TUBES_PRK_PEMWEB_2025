// ============================================
// DATA MANAGEMENT
// ============================================
const serviceData = {
  1: { components: [], laborCost: 0, diagnosisDesc: '', additionalDetails: '' },
  2: { components: [], laborCost: 0, diagnosisDesc: '', additionalDetails: '' },
  3: { components: [], laborCost: 0, diagnosisDesc: '', additionalDetails: '' },
  4: { components: [], laborCost: 0, diagnosisDesc: '', additionalDetails: '' }
};

let currentServiceId = null;

const formatter = new Intl.NumberFormat('id-ID', {
  style: 'currency',
  currency: 'IDR',
  minimumFractionDigits: 0
});

// ============================================
// CALCULATION FUNCTIONS
// ============================================
function calculateTotal() {
  const laborText = document.getElementById('laborCost').value;
  const labor = parseFloat(laborText.replace(/[^0-9]/g, '')) || 0;
  let componentTotal = 0;
  
  const componentCosts = document.querySelectorAll('#componentList .component-cost');
  componentCosts.forEach(input => {
    const costText = input.value;
    const cost = parseFloat(costText.replace(/[^0-9]/g, '')) || 0;
    componentTotal += cost;
  });

  const total = componentTotal + labor;
  document.getElementById('totalDisplay').textContent = formatter.format(total);
  return total;
}

// ============================================
// COMPONENT FIELD MANAGEMENT
// ============================================
function formatCurrency(value) {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(value);
}

function handleCostInput(event) {
  const input = event.target;
  let value = input.value.replace(/[^0-9]/g, '');
  
  if (value) {
    const numValue = parseInt(value, 10);
    input.value = formatCurrency(numValue);
  }
  
  calculateTotal();
}

function generateComponentField(comp) {
  const uniqueId = Date.now().toString(36) + Math.random().toString(36).substr(2, 5);
  const displayCost = comp.cost && comp.cost > 0 ? formatCurrency(comp.cost) : '';
  
  return `
    <div class="flex gap-2 items-center component-item" id="comp-${uniqueId}">
      <input
        type="text"
        placeholder="Nama Komponen"
        class="component-name flex-1 px-4 py-2 border border-gray-300 rounded-lg outline-none text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        value="${comp.name || ''}"
      />
      <input
        type="text"
        placeholder="Harga (Rp)"
        class="component-cost w-40 px-4 py-2 border border-gray-300 rounded-lg outline-none text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        value="${displayCost}"
        oninput="handleCostInput(event)"
      />
      <button 
        onclick="removeComponentField('comp-${uniqueId}')" 
        class="text-red-600 hover:text-red-800 p-2 leading-none flex-shrink-0" 
        type="button" 
        title="Hapus Komponen"
      >
        &times;
      </button>
    </div>
  `;
}

function renderComponentList(serviceId) {
  const listContainer = document.getElementById('componentList');
  listContainer.innerHTML = '';
  const components = serviceData[serviceId].components;

  if (components.length === 0) {
    listContainer.innerHTML = generateComponentField({});
  } else {
    components.forEach(comp => {
      listContainer.innerHTML += generateComponentField(comp);
    });
  }

  calculateTotal();
}

function addComponentField() {
  // Simpan data komponen yang sudah ada
  const componentItems = document.querySelectorAll('#componentList .component-item');
  const components = [];
  componentItems.forEach(item => {
    const name = item.querySelector('.component-name').value;
    const costText = item.querySelector('.component-cost').value;
    const cost = parseFloat(costText.replace(/[^0-9]/g, '')) || 0;
    if (name.trim() !== '' || cost > 0) {
      components.push({ name: name.trim(), cost });
    }
  });

  // Tambah komponen baru ke data
  components.push({ name: '', cost: 0 });
  serviceData[currentServiceId].components = components;

  // Render ulang dengan data terbaru
  renderComponentList(currentServiceId);
}

function removeComponentField(elementId) {
  const element = document.getElementById(elementId);
  if (element) {
    element.remove();
  }

  const remainingItems = document.querySelectorAll('#componentList .component-item').length;
  if (remainingItems === 0) {
    document.getElementById('componentList').innerHTML = generateComponentField({});
  }

  calculateTotal();
}

// ============================================
// MODAL FUNCTIONS
// ============================================
function openDiagnosaModal(serviceId, customerName, itemName) {
  const modal = document.getElementById('diagnosaModal');
  currentServiceId = serviceId;
  
  // Simpan nama customer dan item untuk struk
  window.currentCustomerName = customerName;
  window.currentItemName = itemName;
  
  document.getElementById('diagnosaCustomerName').textContent = customerName;
  document.getElementById('diagnosaItemName').textContent = itemName;

  const data = serviceData[serviceId];
  document.getElementById('laborCost').value = data.laborCost > 0 ? formatCurrency(data.laborCost) : '';
  document.getElementById('diagnosisDesc').value = data.diagnosisDesc || '';
  document.getElementById('additionalDetails').value = data.additionalDetails || '';

  renderComponentList(serviceId);
  calculateTotal();
  
  modal.classList.remove('hidden');
}

function closeDiagnosaModal() {
  document.getElementById('diagnosaModal').classList.add('hidden');
}

function closeStrukModal() {
  document.getElementById('strukModal').classList.add('hidden');
}

// ============================================
// SAVE FUNCTION
// ============================================
function saveDetails(showAlert = true) {
  if (!currentServiceId) return;

  const laborCostText = document.getElementById('laborCost').value;
  const laborCost = parseFloat(laborCostText.replace(/[^0-9]/g, '')) || 0;
  const diagnosisDesc = document.getElementById('diagnosisDesc').value;
  const additionalDetails = document.getElementById('additionalDetails').value;
  
  const newComponents = [];
  const componentItems = document.querySelectorAll('#componentList .component-item');
  componentItems.forEach(item => {
    const name = item.querySelector('.component-name').value;
    const costText = item.querySelector('.component-cost').value;
    const cost = parseFloat(costText.replace(/[^0-9]/g, '')) || 0;
    if (name.trim() !== '' || cost > 0) { 
      newComponents.push({ name: name.trim(), cost });
    }
  });

  serviceData[currentServiceId] = {
    components: newComponents,
    laborCost,
    diagnosisDesc,
    additionalDetails
  };

  const total = calculateTotal();
  updateTableTotal(currentServiceId, total);
  
  if (showAlert) {
    alert('Data berhasil disimpan!');
  }
}

function updateTableTotal(serviceId, total) {
  const row = document.getElementById(`row-${serviceId}`);
  if (row) {
    const totalCell = row.querySelector('.total-cost');
    if (totalCell) {
      totalCell.textContent = formatter.format(total);
    }
  }
}

function showStruk() {
  saveDetails(false);
  
  const serviceId = currentServiceId;
  const data = serviceData[serviceId];
  const customerName = window.currentCustomerName || '';
  const itemName = window.currentItemName || '';

  let strukHtml = `
    <div class="space-y-4">
      <div class="border-b-2 border-gray-400 pb-4 text-center">
        <h2 class="text-2xl font-bold text-gray-800">STRUK LAYANAN PERBAIKAN</h2>
        <p class="text-sm text-gray-600 mt-2">Tanggal: ${new Date().toLocaleDateString('id-ID')}</p>
      </div>

      <div class="space-y-2 text-sm">
        <div class="flex justify-between">
          <span class="font-semibold">Pelanggan:</span>
          <span>${customerName}</span>
        </div>
        <div class="flex justify-between">
          <span class="font-semibold">Barang:</span>
          <span>${itemName}</span>
        </div>
      </div>

      <div class="border-t-2 border-b-2 border-gray-400 py-3">
        <h3 class="font-semibold text-sm mb-2">Deskripsi Diagnosa:</h3>
        <p class="text-sm text-gray-700">${data.diagnosisDesc || 'Tidak ada deskripsi'}</p>
        ${data.additionalDetails ? `<p class="text-sm text-gray-700 mt-2"><strong>Catatan Tambahan:</strong> ${data.additionalDetails}</p>` : ''}
      </div>

      <div class="border-b-2 border-gray-400 pb-3">
        <h3 class="font-semibold text-sm mb-2">Komponen Rusak & Biaya:</h3>
        ${data.components.length > 0 ? `
          <table class="w-full text-sm">
            <thead>
              <tr class="border-b border-gray-300">
                <th class="text-left py-1">Komponen</th>
                <th class="text-right py-1">Harga</th>
              </tr>
            </thead>
            <tbody>
              ${data.components.map(comp => `
                <tr class="border-b border-gray-300">
                  <td class="py-1">${comp.name || '-'}</td>
                  <td class="text-right py-1">${formatter.format(comp.cost)}</td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        ` : '<p class="text-sm text-gray-600">Tidak ada komponen</p>'}
      </div>

      <div class="space-y-2 text-sm">
        <div class="flex justify-between">
          <span>Total Komponen:</span>
          <span>${formatter.format(data.components.reduce((sum, comp) => sum + comp.cost, 0))}</span>
        </div>
        <div class="flex justify-between">
          <span>Biaya Jasa:</span>
          <span>${formatter.format(data.laborCost)}</span>
        </div>
        <div class="flex justify-between font-bold text-lg border-t-2 border-gray-400 pt-2 mt-2">
          <span>TOTAL KESELURUHAN:</span>
          <span>${formatter.format(data.components.reduce((sum, comp) => sum + comp.cost, 0) + data.laborCost)}</span>
        </div>
      </div>

      <div class="text-center text-xs text-gray-500 mt-4">
        <p>Terima kasih telah menggunakan layanan kami</p>
      </div>
    </div>
  `;

  document.getElementById('strukContent').innerHTML = strukHtml;
  document.getElementById('strukModal').classList.remove('hidden');
}

function printStruk() {
  window.print();
}

// ============================================
// STATUS UPDATE FUNCTION
// ============================================
function updateStatus(serviceId, statusValue) {
  fetch('update_status.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: `serviceId=${serviceId}&status=${statusValue}`
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      console.log('Status berhasil diperbarui untuk service ID: ' + serviceId);
    } else {
      console.error('Gagal memperbarui status');
    }
  })
  .catch(error => console.error('Error:', error));
}

// ============================================
// FILTER FUNCTION
// ============================================
function filterData() {
  const searchInput = document.getElementById('searchInput').value.toLowerCase().trim();
  const statusFilter = document.getElementById('statusFilter').value;
  
  const rows = document.querySelectorAll('#serviceTableBody tr');
  let visibleCount = 0;
  
  rows.forEach(row => {
    const cells = row.querySelectorAll('td');
    if (cells.length < 4) return;
    
    const customerName = cells[1].textContent.toLowerCase().trim();
    const statusSelect = row.querySelector('.status-select');
    const currentStatus = statusSelect ? statusSelect.value : '';
    
    let matches = true;
    
    // Filter by search input
    if (searchInput && !customerName.includes(searchInput)) {
      matches = false;
    }
    
    // Filter by status
    if (statusFilter && currentStatus !== statusFilter) {
      matches = false;
    }
    
    if (matches) {
      row.style.display = '';
      visibleCount++;
    } else {
      row.style.display = 'none';
    }
  });
  
  // Tampilkan empty state jika tidak ada hasil
  const emptyState = document.getElementById('emptyState');
  if (visibleCount === 0) {
    emptyState.classList.remove('hidden');
  } else {
    emptyState.classList.add('hidden');
  }
  
  // Alert jika pencarian tidak menemukan hasil
  if (searchInput && visibleCount === 0) {
    alert('Data pelanggan "' + searchInput + '" tidak ditemukan!');
  }
}

// Reset filter saat halaman dimuat
window.addEventListener('DOMContentLoaded', function() {
  document.getElementById('searchInput').value = '';
  document.getElementById('statusFilter').value = '';
});
