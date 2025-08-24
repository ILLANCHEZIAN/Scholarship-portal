function filterScholarships() {
    // Get filter values
    let caste = document.getElementById('caste').value;
    let graduation = document.getElementById('graduation').value;
    let government = document.getElementById('government').value;
    let privateScheme = document.getElementById('private').value;
    let gender = document.getElementById('gender').value;


    let scholarships = document.querySelectorAll('.scholarship');
    
    scholarships.forEach(function(scholarship) {
        let show = true;

        // Compare data attributes with selected filter values
        if (caste && scholarship.getAttribute('data-caste') !== caste) {
            show = false;
        }
        if (graduation && scholarship.getAttribute('data-graduation') !== graduation) {
            show = false;
        }
        if (government && scholarship.getAttribute('data-government') !== government) {
            show = false;
        }
        if (privateScheme && scholarship.getAttribute('data-private') !== privateScheme) {
            show = false;
        }
        if (gender && scholarship.getAttribute('data-gender') !== gender) {
            show = false;
        }


        // Show or hide scholarship based on filter
        scholarship.style.display = show ? 'block' : 'none';
    });
}