document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const fileInput = document.getElementById('foodImage');
    const submitButton = form.querySelector('button[type="submit"]');
    let imagePreview = document.getElementById('imagePreview');

    // Create image preview element if it doesn't exist
    if (!imagePreview) {
        imagePreview = document.createElement('img');
        imagePreview.id = 'imagePreview';
        fileInput.parentNode.insertBefore(imagePreview, fileInput.nextSibling);
    }

    // Handle file selection
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            imagePreview.style.display = 'none';
        }
    });

    // Handle form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        submitButton.disabled = true;
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Analyzing...';

        fetch('analyze.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayResults(data.data);
            } else {
                throw new Error(data.error || 'Failed to analyze image');
            }
        })
        .catch(error => {
            alert(error.message);
        })
        .finally(() => {
            submitButton.disabled = false;
            submitButton.innerHTML = 'Analyze Food';
        });
    });

    // Function to display analysis results
    function displayResults(data) {
        let resultsDiv = document.querySelector('.analysis-results');
        if (!resultsDiv) {
            resultsDiv = document.createElement('div');
            resultsDiv.className = 'analysis-results card mt-4';
            form.parentNode.appendChild(resultsDiv);
        }

        resultsDiv.innerHTML = `
            <div class="card-body">
                <h5 class="card-title">Analysis Results</h5>
                <div class="row">
                    <div class="col-md-6">
                        <h6>Detected Items:</h6>
                        <ul class="list-unstyled">
                            ${data.items.map(item => `<li>${item}</li>`).join('')}
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Nutritional Information:</h6>
                        <ul class="list-unstyled">
                            <li>Calories: ${data.nutritionalInfo.calories}</li>
                            <li>Protein: ${data.nutritionalInfo.protein}</li>
                            <li>Carbs: ${data.nutritionalInfo.carbs}</li>
                            <li>Fat: ${data.nutritionalInfo.fat}</li>
                        </ul>
                    </div>
                </div>
            </div>
        `;
        resultsDiv.style.display = 'block';
    }
}); 