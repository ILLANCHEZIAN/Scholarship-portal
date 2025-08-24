<?php
header('Content-Type: application/json'); // Critical!
// In submit_contact.php or similar API endpoints
header('Content-Type: application/json');

try {
    // Your existing processing code
    
    echo json_encode([
        'success' => true,
        'message' => 'Your message was sent successfully'
    ]);
    exit();
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit();
}


document.querySelector("#contactForm").addEventListener("submit", async function(e) {
    e.preventDefault();
    
    const form = e.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
    submitBtn.disabled = true;
    
    try {
        const formData = new FormData(form);
        
        const response = await fetch('submit_contact.php', {
            method: 'POST',
            body: formData
        });
        
        // First check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            throw new Error(`Server returned: ${text.substring(0, 100)}...`);
        }
        
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.message || 'Request failed');
        }
        
        // Success case
        alert(`Thank you! ${data.message}`);
        form.reset();
        
    } catch (error) {
        console.error('Error:', error);
        alert(`Failed to send: ${error.message}`);
    } finally {
        // Reset button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
});