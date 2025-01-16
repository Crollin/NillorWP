document.addEventListener('DOMContentLoaded', function () {
    // Vérifier si l'utilisateur est connecté
    if (!document.body.classList.contains('logged-in')) {
        // Masquer les colonnes Quantity et Add to Cart
        document.querySelectorAll('.pvt-table .pvt-quantity, .pvt-table .pvt-add-to-cart').forEach(function (column) {
            column.style.display = 'none';
        });
    }
});