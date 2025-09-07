document.querySelectorAll('.menu-item').forEach(item => {
    item.addEventListener('click', function(e) {
        e.preventDefault();

        // Remove active class from all menu items
        document.querySelectorAll('.menu-item').forEach(link => link.classList.remove('active'));

        // Add active class to clicked item
        this.classList.add('active');

    });
});