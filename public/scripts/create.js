// Current step tracking
let currentStep = 1;
let selectedType = '';

// Theme management
function toggleTheme() {
  const currentTheme = document.documentElement.getAttribute('data-theme');
  const newTheme = currentTheme === 'light' ? 'dark' : 'light';
  document.documentElement.setAttribute('data-theme', newTheme);
  
  // Update icon
  const icon = document.querySelector('.theme-toggle i');
  icon.className = newTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
  
  // Save preference
  localStorage.setItem('theme', newTheme);
}

// Check for saved theme preference
if (localStorage.getItem('theme') === 'dark') {
  document.documentElement.setAttribute('data-theme', 'dark');
  document.querySelector('.theme-toggle i').className = 'fas fa-sun';
}

// Equipment type selection
function selectType(type) {
  selectedType = type;
  document.getElementById('selected-type').textContent = type;
  document.getElementById('equipment_type').value = type;
  document.getElementById('form-type').textContent = type;
  document.getElementById('import-type').textContent = type;
  
  // Update UI
  document.getElementById('step1').classList.add('hidden');
  document.getElementById('step2').classList.remove('hidden');
  document.getElementById('step2').classList.add('fade-in');
  
  // Update progress steps
  document.getElementById('step1-indicator').classList.remove('active');
  document.getElementById('step1-indicator').classList.add('completed');
  document.getElementById('step2-indicator').classList.add('active');
  
  currentStep = 2;
}

// Show import section
function showImport() {
  document.getElementById('step2').classList.add('hidden');
  document.getElementById('import-section').classList.remove('hidden');
  document.getElementById('import-section').classList.add('fade-in');
}

// Show form
function showForm() {
  document.getElementById('step2').classList.add('hidden');
  document.getElementById('step3').classList.remove('hidden');
  document.getElementById('step3').classList.add('fade-in');
  
  // Update progress steps
  document.getElementById('step2-indicator').classList.remove('active');
  document.getElementById('step2-indicator').classList.add('completed');
  document.getElementById('step3-indicator').classList.add('active');
  
  currentStep = 3;
}

// Back to step 1
function backToStep1() {
  document.getElementById('step2').classList.add('hidden');
  document.getElementById('step1').classList.remove('hidden');
  document.getElementById('step1').classList.add('fade-in');
  
  // Update progress steps
  document.getElementById('step1-indicator').classList.add('active');
  document.getElementById('step1-indicator').classList.remove('completed');
  document.getElementById('step2-indicator').classList.remove('active');
  
  currentStep = 1;
}

// Back to step 2
function backToStep2() {
  if (document.getElementById('import-section').classList.contains('hidden')) {
    document.getElementById('step3').classList.add('hidden');
  } else {
    document.getElementById('import-section').classList.add('hidden');
  }
  
  document.getElementById('step2').classList.remove('hidden');
  document.getElementById('step2').classList.add('fade-in');
  
  // Update progress steps
  document.getElementById('step2-indicator').classList.add('active');
  document.getElementById('step3-indicator').classList.remove('active');
  
  currentStep = 2;
}

// Form submission
document.getElementById('inventory-form').addEventListener('submit', function(e) {
  e.preventDefault();
  
  // Here you would normally send the form data to the server
  // For demo purposes, we'll just show a success message
  alert('Inventory item saved successfully!');
  
  // Reset form
  this.reset();
  backToStep1();
});

// Import form submission
document.getElementById('import-form').addEventListener('submit', function(e) {
  e.preventDefault();
  const fileInput = document.getElementById('csv-file');
  
  if (fileInput.files.length > 0) {
    // Here you would process the CSV file
    alert('CSV file imported successfully!');
    backToStep1();
  }
});
