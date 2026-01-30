// Weekly Journal Inventory - Main JavaScript

const API_BASE_URL = '/api';
const TOTAL_WEEKS = 8; // Total weeks for 2 months
let currentEditId = null;

// Initialize application when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    loadEntries();
    initializeFormHandlers();
});

// Load all journal entries
async function loadEntries() {
    try {
        const response = await fetch(`${API_BASE_URL}/read_entries.php`);
        if (!response.ok) throw new Error('Failed to fetch entries');
        
        const entries = await response.json();
        displayEntries(entries);
        updateStatistics(entries);
    } catch (error) {
        console.error('Error loading entries:', error);
        showError('Failed to load entries. Please try again.');
    }
}

// Display entries in the grid
function displayEntries(entries) {
    const container = document.getElementById('entriesContainer');
    const emptyState = document.getElementById('emptyState');
    
    if (entries.length === 0) {
        container.innerHTML = '';
        emptyState.style.display = 'block';
        return;
    }
    
    emptyState.style.display = 'none';
    container.innerHTML = entries.map(entry => createEntryCard(entry)).join('');
}

// Create HTML for a single entry card
function createEntryCard(entry) {
    const sanitizedImageUrl = sanitizeImageUrl(entry.image_url);
    const imageHtml = sanitizedImageUrl 
        ? `<img src="${escapeHtml(sanitizedImageUrl)}" class="card-img-top" alt="${escapeHtml(entry.title)}">`
        : '<div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 200px;"><i class="fas fa-image fa-3x text-white-50"></i></div>';
    
    return `
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                ${imageHtml}
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-primary">Week ${escapeHtml(entry.week_number)}</span>
                        <span class="badge bg-info">Month ${escapeHtml(entry.month)}</span>
                    </div>
                    <h5 class="card-title">${escapeHtml(entry.title)}</h5>
                    <p class="card-text">${escapeHtml(entry.content)}</p>
                    <div class="text-muted small">
                        <i class="far fa-clock me-1"></i>${formatDate(entry.created_at)}
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <button class="btn btn-sm btn-outline-primary" onclick="editEntry(${entry.id})">
                        <i class="fas fa-edit me-1"></i>Edit
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteEntry(${entry.id})">
                        <i class="fas fa-trash me-1"></i>Delete
                    </button>
                </div>
            </div>
        </div>
    `;
}

// Update statistics dashboard
function updateStatistics(entries) {
    const totalEntries = entries.length;
    const mostRecentEntry = entries[0];
    
    document.getElementById('totalEntries').textContent = totalEntries;
    
    if (mostRecentEntry) {
        document.getElementById('currentMonth').textContent = mostRecentEntry.month;
        document.getElementById('lastUpdated').textContent = formatDate(mostRecentEntry.created_at);
    }
    
    // Calculate progress based on total expected weeks
    const progressPercent = Math.min(Math.round((totalEntries / TOTAL_WEEKS) * 100), 100);
    document.getElementById('progressPercent').textContent = `${progressPercent}%`;
}

// Initialize form handlers
function initializeFormHandlers() {
    const form = document.getElementById('entryForm');
    const imageInput = document.getElementById('entryImage');
    const modal = document.getElementById('addEntryModal');
    
    // Form submission handler
    form.addEventListener('submit', handleFormSubmit);
    
    // Image preview handler
    imageInput.addEventListener('change', handleImagePreview);
    
    // Reset form when modal is closed
    modal.addEventListener('hidden.bs.modal', resetForm);
}

// Handle form submission
async function handleFormSubmit(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    const originalBtnText = submitBtn.innerHTML;
    
    try {
        // Disable submit button and show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
        
        // Get form data
        const weekNumber = document.getElementById('weekNumber').value;
        const monthNumber = document.getElementById('monthNumber').value;
        const title = document.getElementById('entryTitle').value;
        const description = document.getElementById('entryDescription').value;
        const imageFile = document.getElementById('entryImage').files[0];
        
        let imageUrl = null;
        
        // Upload image if selected
        if (imageFile) {
            imageUrl = await uploadImage(imageFile);
        }
        
        // Create entry data
        const entryData = {
            week_number: parseInt(weekNumber),
            month: parseInt(monthNumber),
            title: title,
            content: description,
            image_url: imageUrl
        };
        
        // Submit entry
        const response = await fetch(`${API_BASE_URL}/create_entry.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(entryData)
        });
        
        const result = await response.json();
        
        if (result.status === 'success') {
            showSuccess('Entry saved successfully!');
            bootstrap.Modal.getInstance(document.getElementById('addEntryModal')).hide();
            resetForm();
            loadEntries();
        } else {
            throw new Error(result.message || 'Failed to save entry');
        }
    } catch (error) {
        console.error('Error saving entry:', error);
        showError(error.message || 'Failed to save entry. Please try again.');
    } finally {
        // Re-enable submit button
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    }
}

// Upload image to server
async function uploadImage(file) {
    // Client-side validation (matches server-side validation in upload_image.php)
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        throw new Error('Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.');
    }
    
    // Validate file size (5MB)
    const maxSize = 5 * 1024 * 1024;
    if (file.size > maxSize) {
        throw new Error('File size exceeds 5MB limit.');
    }
    
    const formData = new FormData();
    formData.append('image', file);
    
    const response = await fetch(`${API_BASE_URL}/upload_image.php`, {
        method: 'POST',
        body: formData
    });
    
    const result = await response.json();
    
    if (result.status === 'success') {
        return result.image_url;
    } else {
        throw new Error(result.message || 'Image upload failed');
    }
}

// Handle image preview
function handleImagePreview(e) {
    const file = e.target.files[0];
    const previewDiv = document.getElementById('imagePreview');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewDiv.innerHTML = `
                <img src="${e.target.result}" class="img-thumbnail" style="max-height: 200px;">
                <button type="button" class="btn btn-sm btn-danger mt-2" onclick="clearImagePreview()">
                    <i class="fas fa-times me-1"></i>Remove
                </button>
            `;
        };
        reader.readAsDataURL(file);
    } else {
        previewDiv.innerHTML = '';
    }
}

// Clear image preview
function clearImagePreview() {
    document.getElementById('entryImage').value = '';
    document.getElementById('imagePreview').innerHTML = '';
}

// Reset form to initial state
function resetForm() {
    document.getElementById('entryForm').reset();
    document.getElementById('imagePreview').innerHTML = '';
    document.getElementById('entryId').value = '';
    document.getElementById('modalTitle').textContent = 'Add New Entry';
    currentEditId = null;
}

// Edit entry (placeholder for future implementation)
function editEntry(id) {
    showError('Edit functionality coming soon!');
}

// Delete entry (placeholder for future implementation)
function deleteEntry(id) {
    if (confirm('Are you sure you want to delete this entry?')) {
        showError('Delete functionality coming soon!');
    }
}

// Utility Functions

// Sanitize image URL to prevent XSS
function sanitizeImageUrl(url) {
    if (!url) return null;
    // Only allow URLs starting with 'uploads/' (relative paths from our upload directory)
    if (typeof url === 'string' && url.startsWith('uploads/')) {
        return url;
    }
    return null;
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    if (text === null || text === undefined) return '';
    const div = document.createElement('div');
    div.textContent = String(text);
    return div.innerHTML;
}

// Format date for display
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Show success message
function showSuccess(message) {
    // TODO: Replace with toast notification system for better UX
    alert(message);
}

// Show error message
function showError(message) {
    // TODO: Replace with toast notification system for better UX
    alert('Error: ' + message);
}