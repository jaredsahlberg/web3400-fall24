document.addEventListener('DOMContentLoaded', function () {
    // Add event listener to all elements with the 'delete' class
    document.querySelectorAll('.delete').forEach(function (button) {
        button.addEventListener('click', function () {
            const notification = this.parentElement; // Parent of the close button
            if (notification) {
                notification.remove(); // Remove the notification element
            }
        });
    });
});
