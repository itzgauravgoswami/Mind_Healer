document.getElementById('depressionTest').addEventListener('submit', function(e) {
    const inputs = document.querySelectorAll('input[type="radio"]:checked');
    if (inputs.length < 20) {
        e.preventDefault();
        alert('Please answer all questions before submitting.');
    }
});