// Custom Alert Function
function showCustomAlert(message, type = 'info') {
  // Remove existing alerts
  const existingAlert = document.querySelector('.custom-alert');
  if (existingAlert) {
    existingAlert.remove();
  }

  // Create alert element
  const alertDiv = document.createElement('div');
  alertDiv.className = `custom-alert custom-alert-${type}`;
  alertDiv.innerHTML = `
    <div class="custom-alert-content">
      <span class="custom-alert-message">${message}</span>
      <button class="custom-alert-close" onclick="this.parentElement.parentElement.remove()">×</button>
    </div>
  `;

  // Add to body
  document.body.appendChild(alertDiv);

  // Auto remove after 5 seconds
  setTimeout(() => {
    if (alertDiv.parentElement) {
      alertDiv.remove();
    }
  }, 5000);
}

// Confirm Delete Function
function confirmDelete(animalId) {
  showCustomConfirm('Are you sure you want to delete this animal?', function() {
    // Create form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.style.display = 'none';
    
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'delete_id';
    input.value = animalId;
    
    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
  });
}

// Custom Confirm Dialog
function showCustomConfirm(message, onConfirm) {
  // Remove existing confirm dialogs
  const existingConfirm = document.querySelector('.custom-confirm');
  if (existingConfirm) {
    existingConfirm.remove();
  }

  // Create confirm dialog
  const confirmDiv = document.createElement('div');
  confirmDiv.className = 'custom-confirm';
  confirmDiv.innerHTML = `
    <div class="custom-confirm-overlay"></div>
    <div class="custom-confirm-content">
      <div class="custom-confirm-message">${message}</div>
      <div class="custom-confirm-buttons">
        <button class="custom-confirm-btn custom-confirm-cancel" onclick="this.closest('.custom-confirm').remove()">Cancel</button>
        <button class="custom-confirm-btn custom-confirm-ok">Confirm</button>
      </div>
    </div>
  `;

  // Add event listener for confirm button
  confirmDiv.querySelector('.custom-confirm-ok').addEventListener('click', function() {
    confirmDiv.remove();
    if (onConfirm) onConfirm();
  });

  // Add event listener for overlay click
  confirmDiv.querySelector('.custom-confirm-overlay').addEventListener('click', function() {
    confirmDiv.remove();
  });

  // Add to body
  document.body.appendChild(confirmDiv);
}

// Toggle Details Function (for animals page)
function toggleDetails(button) {
  const card = button.closest('.animal-card');
  const details = card.querySelector('.details');
  details.classList.toggle('open');
}

// Toggle Adoption Function (for animals page)
function toggleAdoption(button) {
  const card = button.closest('.animal-card');
  const adopt = card.querySelector('.adopt');
  adopt.classList.toggle('open');
}

// Image Zoom Function
function zoomImage(imageSrc) {
  // Remove existing zoom modal
  const existingModal = document.querySelector('.image-zoom-modal');
  if (existingModal) {
    existingModal.remove();
  }

  // Create zoom modal
  const modal = document.createElement('div');
  modal.className = 'image-zoom-modal';
  modal.innerHTML = `
    <div class="image-zoom-content">
      <button class="image-zoom-close" onclick="this.closest('.image-zoom-modal').remove()">×</button>
      <img src="${imageSrc}" alt="Zoomed Image">
    </div>
  `;

  // Close on overlay click
  modal.addEventListener('click', function(e) {
    if (e.target === modal) {
      modal.remove();
    }
  });

  // Close on Escape key
  document.addEventListener('keydown', function escapeHandler(e) {
    if (e.key === 'Escape') {
      modal.remove();
      document.removeEventListener('keydown', escapeHandler);
    }
  });

  // Add to body
  document.body.appendChild(modal);
}

// Image Preview Function
function previewImage(input) {
  const preview = document.getElementById('imagePreview');
  const placeholder = document.getElementById('imagePlaceholder');
  
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    
    reader.onload = function(e) {
      preview.src = e.target.result;
      preview.style.display = 'block';
      if (placeholder) {
        placeholder.style.display = 'none';
      }
    };
    
    reader.readAsDataURL(input.files[0]);
  } else {
    // If no file selected, hide preview and show placeholder
    preview.style.display = 'none';
    if (placeholder) {
      placeholder.style.display = 'flex';
    }
  }
}

// Replace all default alerts with custom alerts when page loads
document.addEventListener('DOMContentLoaded', function() {
  // Override window.alert
  window.alert = function(message) {
    showCustomAlert(message, 'info');
  };
});