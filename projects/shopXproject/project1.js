// header + panel

function toggleDropdown(id) {
    const dropdowns = document.querySelectorAll('.dropdown, .cart-dropdown');
    dropdowns.forEach(d => {
      if (d.id !== id) d.style.display = 'none';
    });

    const dropdown = document.getElementById(id);
    dropdown.style.display = (dropdown.style.display === 'block') ? "none" : 'block';
}

function toggleCategoryDropdown() {
    const menu = document.getElementById("dropdownMenu");
    menu.style.display = (menu.style.display === "block") ? "none" : "block";
}

document.addEventListener("click", function (e) {
    const isDropdown = e.target.closest('.nav-item, .cart, .panel-all');
    if (!isDropdown) {
      document.querySelectorAll('.dropdown, .cart-dropdown, .dropdown-content').forEach(d => d.style.display = 'none');
    }
});

// product-container slider


